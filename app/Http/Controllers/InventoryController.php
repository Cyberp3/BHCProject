<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    //
    public function index(Request $request): View
    {
        $category = $request->input('category', 'all');

        $query = InventoryItem::with(['batches' => function ($q) {
            $q->where('status', 'active')->orderBy('expiration_date', 'asc');
        }]);

        if ($category === 'medicine') {
            $query->medicines();
        } elseif ($category === 'vaccine') {
            $query->vaccines();
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('generic_name', 'like', "%{$term}%")
                    ->orWhere('item_code', 'like', "%{$term}%");
            });
        }

        $items = $query->orderBy('name')->paginate(15)->withQueryString();

        // Count summary metrics
        $lowStockCount = InventoryItem::with('batches')->get()->filter(function ($item) {
            return $item->total_stock <= $item->minimum_stock_alert;
        })->count();

        $nearExpiryBatches = InventoryBatch::with('item')
            ->nearExpiry(90)
            ->get();

        $recentTransactions = InventoryTransaction::with(['item', 'batch', 'user', 'patient'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(8)
            ->get();

        return view('inventory.index', compact(
            'items',
            'category',
            'lowStockCount',
            'nearExpiryBatches',
            'recentTransactions'
        ));
    }

    public function createItem(): View
    {
        return view('inventory.items.create');
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_code' => ['required', 'string', 'max:50', 'unique:inventory_items,item_code'],
            'name' => ['required', 'string', 'max:150'],
            'generic_name' => ['nullable', 'string', 'max:150'],
            'category' => ['required', 'in:medicine,vaccine'],
            'dosage_form' => ['nullable', 'string', 'max:100'],
            'unit_of_measure' => ['required', 'string', 'max:50'],
            'minimum_stock_alert' => ['required', 'integer', 'min:1'],
            'is_cold_chain' => ['nullable', 'boolean'],
            'storage_temperature_note' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_cold_chain'] = $request->boolean('is_cold_chain');

        $item = InventoryItem::create($validated);

        AuditLog::log(
            'CREATE_INVENTORY_ITEM',
            'Inventory',
            "Added {$item->name} ({$item->item_code}) to inventory catalog",
            $item->id
        );

        return redirect()->route('inventory.index')
            ->with('success', "Item {$item->name} added to catalog successfully.");
    }

    public function editItem(InventoryItem $item): View
    {
        return view('inventory.items.edit', compact('item'));
    }

    public function updateItem(Request $request, InventoryItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'item_code' => ['required', 'string', 'max:50', 'unique:inventory_items,item_code,'.$item->id],
            'name' => ['required', 'string', 'max:150'],
            'generic_name' => ['nullable', 'string', 'max:150'],
            'category' => ['required', 'in:medicine,vaccine'],
            'dosage_form' => ['nullable', 'string', 'max:100'],
            'unit_of_measure' => ['required', 'string', 'max:50'],
            'minimum_stock_alert' => ['required', 'integer', 'min:1'],
            'is_cold_chain' => ['nullable', 'boolean'],
            'storage_temperature_note' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_cold_chain'] = $request->boolean('is_cold_chain');

        $item->update($validated);

        AuditLog::log(
            'UPDATE_INVENTORY_ITEM',
            'Inventory',
            "Updated catalog details for {$item->name}",
            $item->id
        );

        return redirect()->route('inventory.index')
            ->with('success', "Item {$item->name} updated successfully.");
    }

    public function receiveBatchForm(Request $request): View
    {
        $items = InventoryItem::orderBy('name')->get();
        $selectedItemId = $request->input('item_id');
        $today = Carbon::today()->format('Y-m-d');

        return view('inventory.batches.receive', compact('items', 'selectedItemId', 'today'));
    }

    public function storeBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'batch_number' => ['required', 'string', 'max:100'],
            'date_received' => ['required', 'date', 'before_or_equal:today'],
            'expiration_date' => ['required', 'date', 'after:date_received'],
            'quantity_received' => ['required', 'integer', 'min:1'],
            'supplier_or_source' => ['nullable', 'string', 'max:150'],
            'remarks' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated) {
            $batch = InventoryBatch::create([
                'inventory_item_id' => $validated['inventory_item_id'],
                'batch_number' => $validated['batch_number'],
                'date_received' => $validated['date_received'],
                'expiration_date' => $validated['expiration_date'],
                'quantity_received' => $validated['quantity_received'],
                'current_quantity' => $validated['quantity_received'],
                'supplier_or_source' => $validated['supplier_or_source'],
                'status' => 'active',
                'remarks' => $validated['remarks'],
            ]);

            InventoryTransaction::create([
                'inventory_item_id' => $validated['inventory_item_id'],
                'inventory_batch_id' => $batch->id,
                'user_id' => Auth::id(),
                'transaction_type' => 'received',
                'quantity' => $validated['quantity_received'],
                'transaction_date' => $validated['date_received'],
                'remarks' => 'Stock received from '.($validated['supplier_or_source'] ?? 'Supplier'),
            ]);

            $item = InventoryItem::find($validated['inventory_item_id']);
            AuditLog::log(
                'RECEIVE_BATCH',
                'Inventory',
                "Received batch {$batch->batch_number} ({$batch->quantity_received} {$item->unit_of_measure}) for {$item->name}",
                $batch->id
            );
        });

        return redirect()->route('inventory.index')
            ->with('success', 'Stock batch received and recorded successfully.');
    }

    public function dispenseForm(Request $request): View
    {
        $items = InventoryItem::with(['batches' => function ($q) {
            $q->where('status', 'active')->where('current_quantity', '>', 0)->orderBy('expiration_date', 'asc');
        }])->orderBy('name')->get();

        $patients = Patient::orderBy('last_name')->get();
        $patients = Patient::with('purok')->orderBy('last_name')->get();
        $selectedItemId = $request->input('item_id');
        $selectedPatientId = $request->input('patient_id');
        $today = Carbon::today()->format('Y-m-d');

        return view('inventory.dispense', compact('items', 'patients', 'selectedItemId', 'selectedPatientId', 'today'));
    }

    public function processDispense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'patient_id' => ['nullable', 'exists:patients,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $item = InventoryItem::with(['activeBatches'])->findOrFail($validated['inventory_item_id']);
        $quantityNeeded = $validated['quantity'];
        $availableStock = $item->total_stock;

        if ($quantityNeeded > $availableStock) {
            return back()->withInput()->withErrors([
                'quantity' => "Insufficient stock. Only {$availableStock} {$item->unit_of_measure} available in active inventory.",
            ]);
        }

        DB::transaction(function () use ($item, $quantityNeeded, $validated) {
            $remaining = $quantityNeeded;
            $today = Carbon::today();
            $patient = ! empty($validated['patient_id']) ? Patient::find($validated['patient_id']) : null;

            // FIFO: iterate active batches ordered by expiration_date ascending
            foreach ($item->activeBatches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $deduct = min($remaining, $batch->current_quantity);
                $batch->current_quantity -= $deduct;

                if ($batch->current_quantity === 0) {
                    $batch->status = 'depleted';
                }
                $batch->save();

                InventoryTransaction::create([
                    'inventory_item_id' => $item->id,
                    'inventory_batch_id' => $batch->id,
                    'user_id' => Auth::id(),
                    'patient_id' => $patient ? $patient->id : null,
                    'transaction_type' => 'dispensed',
                    'quantity' => $deduct,
                    'transaction_date' => $today,
                    'remarks' => $validated['remarks'] ?? ($patient ? "Dispensed to {$patient->full_name}" : 'Dispensed for health service'),
                ]);

                $remaining -= $deduct;
            }

            $patientDesc = $patient ? " to {$patient->full_name}" : '';
            AuditLog::log(
                'DISPENSE_INVENTORY',
                'Inventory',
                "Dispensed {$quantityNeeded} {$item->unit_of_measure} of {$item->name}{$patientDesc} (FIFO applied)",
                $item->id
            );
        });

        return redirect()->route('inventory.index')
            ->with('success', "{$quantityNeeded} {$item->unit_of_measure} of {$item->name} dispensed successfully.");
    }
}
