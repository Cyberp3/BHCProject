<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\HealthServiceRecord;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Patient;
use App\Models\Purok;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected function authorizeReportsAccess(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_if(! $user || ! $user->canAccessReports(), 403, 'Unauthorized: Barangay Health Workers (BHW) are not authorized to view or print health center administrative reports.');
    }

    public function index(): View
    {
        $this->authorizeReportsAccess();

        return view('reports.index');
    }

    public function patients(Request $request): View
    {
        $this->authorizeReportsAccess();

        $totalPatients = Patient::count();
        $maleCount = Patient::where('sex', 'Male')->count();
        $femaleCount = Patient::where('sex', 'Female')->count();

        $purokStats = Purok::withCount('patients')
            ->orderBy('id')
            ->get();

        $selectedPurokId = $request->input('purok_id');
        $patientsQuery = Patient::with('purok');

        if ($selectedPurokId) {
            $patientsQuery->where('purok_id', $selectedPurokId);
        }

        $totalPatientsCount = (clone $patientsQuery)->count();
        $perPage = $request->input('per_page', 25);
        $patientList = ($perPage === 'all' || $request->boolean('all'))
            ? $patientsQuery->orderBy('last_name')->paginate($totalPatientsCount ?: 25)->withQueryString()
            : $patientsQuery->orderBy('last_name')->paginate((int) $perPage)->withQueryString();

        return view('reports.patients', compact(
            'totalPatients',
            'maleCount',
            'femaleCount',
            'purokStats',
            'selectedPurokId',
            'patientList'
        ));
    }

    public function services(Request $request): View
    {
        $this->authorizeReportsAccess();

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $countsByService = HealthServiceRecord::whereBetween('service_date', [$startDate, $endDate])
            ->select('service_type', DB::raw('count(*) as count'))
            ->groupBy('service_type')
            ->pluck('count', 'service_type')
            ->toArray();

        $recordsQuery = HealthServiceRecord::with(['patient.purok', 'user'])
            ->whereBetween('service_date', [$startDate, $endDate])
            ->orderBy('service_date', 'desc');

        $totalRecordsCount = (clone $recordsQuery)->count();
        $perPage = $request->input('per_page', 25);
        $records = ($perPage === 'all' || $request->boolean('all'))
            ? $recordsQuery->paginate($totalRecordsCount ?: 25)->withQueryString()
            : $recordsQuery->paginate((int) $perPage)->withQueryString();

        $allServices = HealthServiceRecord::SERVICES;

        return view('reports.services', compact(
            'countsByService',
            'records',
            'allServices',
            'startDate',
            'endDate'
        ));
    }

    public function appointments(Request $request): View
    {
        $this->authorizeReportsAccess();

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->addMonth()->format('Y-m-d'));

        $statusCounts = Appointment::whereBetween('appointment_date', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $appointments = Appointment::with(['patient.purok', 'scheduledBy'])
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->orderBy('appointment_date', 'asc')
            ->get();

        return view('reports.appointments', compact(
            'statusCounts',
            'appointments',
            'startDate',
            'endDate'
        ));
    }

    public function inventory(Request $request): View
    {
        $this->authorizeReportsAccess();

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $itemsQuery = InventoryItem::with(['batches' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('name');

        $totalItemsCount = (clone $itemsQuery)->count();
        $perPage = $request->input('per_page', 25);
        $items = ($perPage === 'all' || $request->boolean('all'))
            ? $itemsQuery->paginate($totalItemsCount ?: 25)->withQueryString()
            : $itemsQuery->paginate((int) $perPage)->withQueryString();

        $lowStockItems = (clone $itemsQuery)->get()->filter(function ($item) {
            return $item->total_stock <= $item->minimum_stock_alert;
        });

        $nearExpiryBatches = InventoryBatch::with('item')
            ->nearExpiry(90)
            ->get();

        $recentTransactions = InventoryTransaction::with(['item', 'batch', 'user', 'patient'])
            ->latest('transaction_date')
            ->take(50)
            ->get();

        // Calculate Stock Movement Tracker (Beginning Balance + Stock In - Stock Out = Remaining Balance)
        $allItems = (clone $itemsQuery)->get();
        $transactions = InventoryTransaction::whereDate('transaction_date', '<=', $endDate)->get();

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon = Carbon::parse($endDate)->endOfDay();

        $priorTxsByItem = $transactions->where('transaction_date', '<', $startCarbon)->groupBy('inventory_item_id');
        $periodTxsByItem = $transactions->whereBetween('transaction_date', [$startCarbon, $endCarbon])->groupBy('inventory_item_id');

        $stockMovements = $allItems->map(function ($item) use ($priorTxsByItem, $periodTxsByItem) {
            $priorTxs = $priorTxsByItem->get($item->id, collect());
            $priorIn = $priorTxs->where('transaction_type', 'received')->sum('quantity');
            $priorOut = $priorTxs->where('transaction_type', 'dispensed')->sum('quantity');
            $beginningBalance = max(0, $priorIn - $priorOut);

            $periodTxs = $periodTxsByItem->get($item->id, collect());
            $stockIn = $periodTxs->where('transaction_type', 'received')->sum('quantity');
            $stockOut = $periodTxs->where('transaction_type', 'dispensed')->sum('quantity');

            if ($priorTxs->isEmpty() && $periodTxs->isEmpty()) {
                $beginningBalance = (int) $item->total_stock;
                $remainingBalance = $beginningBalance;
            } else {
                $remainingBalance = max(0, $beginningBalance + $stockIn - $stockOut);
            }

            return (object) [
                'item' => $item,
                'beginning_balance' => $beginningBalance,
                'stock_in' => $stockIn,
                'stock_out' => $stockOut,
                'remaining_balance' => $remainingBalance,
                'net_change' => $stockIn - $stockOut,
            ];
        });

        $movementTotals = [
            'beginning' => $stockMovements->sum('beginning_balance'),
            'stock_in' => $stockMovements->sum('stock_in'),
            'stock_out' => $stockMovements->sum('stock_out'),
            'remaining' => $stockMovements->sum('remaining_balance'),
        ];

        return view('reports.inventory', compact(
            'items',
            'lowStockItems',
            'nearExpiryBatches',
            'recentTransactions',
            'stockMovements',
            'movementTotals',
            'startDate',
            'endDate'
        ));
    }
}
