<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    //
    public function index(Request $request): View
    {
        $today = Carbon::today();

        // Automatically quarantine batches that have passed expiration date
        InventoryBatch::where('status', 'active')
            ->whereDate('expiration_date', '<', $today)
            ->update(['status' => 'expired']);

        $category = $request->input('category', 'all');

        $query = InventoryItem::with(['batches' => function ($q) {
            $q->where('status', 'active')->where('current_quantity', '>', 0)->orderBy('expiration_date', 'asc');
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

        // Count summary metrics based on valid dispensable stock
        $lowStockCount = InventoryItem::with(['batches' => function ($q) {
            $q->where('status', 'active')->where('current_quantity', '>', 0);
        }])->get()->filter(function ($item) {
            return $item->dispensable_stock <= $item->minimum_stock_alert;
        })->count();

        $nearExpiryBatches = InventoryBatch::with('item')
            ->nearExpiry(90)
            ->get();

        $expiredBatchesCount = InventoryBatch::expired()->count();

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
            'expiredBatchesCount',
            'recentTransactions'
        ));
    }

    public function createItem(): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->isBhw(), 403, 'Unauthorized: Catalog item management must be conducted by registered clinical personnel or administrators.');

        return view('inventory.items.create');
    }

    public function storeItem(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->isBhw(), 403, 'Unauthorized: Catalog item management must be conducted by registered clinical personnel or administrators.');
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
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->isBhw(), 403, 'Unauthorized: Catalog item management must be conducted by registered clinical personnel or administrators.');

        return view('inventory.items.edit', compact('item'));
    }

    public function updateItem(Request $request, InventoryItem $item): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->isBhw(), 403, 'Unauthorized: Catalog item management must be conducted by registered clinical personnel or administrators.');
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

    public function showItem(InventoryItem $item): View
    {
        $today = Carbon::today();

        // Auto-quarantine expired batches for this item
        InventoryBatch::where('inventory_item_id', $item->id)
            ->where('status', 'active')
            ->whereDate('expiration_date', '<', $today)
            ->update(['status' => 'expired']);

        $item->load([
            'batches' => function ($q) {
                $q->orderBy('expiration_date', 'asc');
            },
            'transactions' => function ($q) {
                $q->with(['batch', 'user', 'patient'])->latest('transaction_date')->latest('id');
            },
        ]);

        $activeBatches = $item->batches->filter(function ($b) use ($today) {
            return $b->status === 'active' && $b->current_quantity > 0 && $b->expiration_date >= $today;
        });

        $expiredBatches = $item->batches->filter(function ($b) use ($today) {
            return ($b->status === 'expired' || $b->expiration_date < $today) && $b->current_quantity > 0;
        });

        $depletedBatches = $item->batches->filter(function ($b) {
            return $b->current_quantity <= 0;
        });

        return view('inventory.items.show', compact('item', 'activeBatches', 'expiredBatches', 'depletedBatches'));
    }

    public function receiveBatchForm(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->isBhw(), 403, 'Unauthorized: Stock batch receiving must be conducted by registered clinical personnel or administrators.');

        $items = InventoryItem::orderBy('name')->get();
        $selectedItemId = $request->input('item_id');
        $today = Carbon::today()->format('Y-m-d');

        return view('inventory.batches.receive', compact('items', 'selectedItemId', 'today'));
    }

    public function storeBatch(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->isBhw(), 403, 'Unauthorized: Stock batch receiving must be conducted by registered clinical personnel or administrators.');
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'batch_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('inventory_batches')->where(function ($query) use ($request) {
                    return $query->where('inventory_item_id', $request->input('inventory_item_id'))
                        ->where('status', 'active');
                }),
            ],
            'date_received' => ['required', 'date', 'before_or_equal:today'],
            'expiration_date' => ['required', 'date', 'after:date_received'],
            'quantity_received' => ['required', 'integer', 'min:1'],
            'supplier_or_source' => ['nullable', 'string', 'max:150'],
            'remarks' => ['nullable', 'string'],
        ], [
            'batch_number.unique' => 'This batch number is already active for this item with an existing expiration date. Each new delivery must have a distinct batch/lot number.',
        ]);

        DB::transaction(function () use ($validated) {
            $batch = InventoryBatch::create([
                'inventory_item_id' => $validated['inventory_item_id'],
                'batch_number' => $validated['batch_number'],
                'date_received' => $validated['date_received'],
                'expiration_date' => $validated['expiration_date'],
                'quantity_received' => $validated['quantity_received'],
                'current_quantity' => $validated['quantity_received'],
                'supplier_or_source' => $validated['supplier_or_source'] ?? null,
                'status' => 'active',
                'remarks' => $validated['remarks'] ?? null,
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
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->canDispenseMedicine(), 403, 'Unauthorized: Only registered clinical personnel (Nurses and Midwives) are authorized to dispense medications and vaccines.');

        $today = Carbon::today();

        // Auto-quarantine expired active batches across inventory
        InventoryBatch::where('status', 'active')
            ->whereDate('expiration_date', '<', $today)
            ->update(['status' => 'expired']);

        $items = InventoryItem::with(['dispensableBatches', 'batches'])->orderBy('name')->get();
        $patients = Patient::with('purok')->orderBy('last_name')->get();
        $selectedItemId = $request->input('item_id');
        $selectedPatientId = $request->input('patient_id');

        return view('inventory.dispense', [
            'items' => $items,
            'patients' => $patients,
            'selectedItemId' => $selectedItemId,
            'selectedPatientId' => $selectedPatientId,
            'today' => $today->format('Y-m-d'),
        ]);
    }

    public function processDispense(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_unless($user->canDispenseMedicine(), 403, 'Unauthorized: Only registered clinical personnel (Nurses and Midwives) are authorized to dispense medications and vaccines.');
        $validated = $request->validate([
            'inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'patient_id' => ['nullable', Rule::exists('patients', 'id')->whereNull('deleted_at')],
            'quantity' => ['required', 'integer', 'min:1'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ], [
            'patient_id.exists' => 'The selected patient record does not exist or has been archived.',
        ]);

        $today = Carbon::today();

        // Quarantine any expired batches for this item that are still marked 'active'
        InventoryBatch::where('inventory_item_id', $validated['inventory_item_id'])
            ->where('status', 'active')
            ->whereDate('expiration_date', '<', $today)
            ->update(['status' => 'expired']);

        $item = InventoryItem::with(['dispensableBatches'])->findOrFail($validated['inventory_item_id']);
        $quantityNeeded = (int) $validated['quantity'];
        $dispensableStock = $item->dispensable_stock;

        if ($quantityNeeded > $dispensableStock) {
            $message = $dispensableStock <= 0
                ? "Cannot dispense {$item->name}. All available stock is expired or quarantined. Please restock with a valid batch."
                : "Insufficient valid stock. Only {$dispensableStock} unexpired {$item->unit_of_measure} available for dispensation. Expired batches cannot be dispensed.";

            return back()->withInput()->withErrors([
                'quantity' => $message,
            ]);
        }

        DB::transaction(function () use ($item, $quantityNeeded, $validated, $today) {
            $remaining = $quantityNeeded;
            $patient = ! empty($validated['patient_id']) ? Patient::find($validated['patient_id']) : null;

            // FEFO: strictly iterate unexpired active batches ordered by expiration_date ascending
            $batches = $item->dispensableBatches()->lockForUpdate()->get();

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                // Strict safety guard: never dispense expired batch
                if ($batch->expiration_date < $today || $batch->status === 'expired') {
                    $batch->status = 'expired';
                    $batch->save();

                    continue;
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

            if ($remaining > 0) {
                throw new \RuntimeException('Failed to allocate full requested quantity from unexpired batches.');
            }

            $patientDesc = $patient ? " to {$patient->full_name}" : '';
            AuditLog::log(
                'DISPENSE_INVENTORY',
                'Inventory',
                "Dispensed {$quantityNeeded} {$item->unit_of_measure} of {$item->name}{$patientDesc} (FEFO applied, expired batches blocked)",
                $item->id
            );
        });

        return redirect()->route('inventory.index')
            ->with('success', "{$quantityNeeded} {$item->unit_of_measure} of {$item->name} dispensed successfully.");
    }
}
