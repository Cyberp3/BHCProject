<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\HealthServiceRecord;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Patient;
use App\Models\Purok;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    //
    public function index(): View
    {
        return view('reports.index');
    }

    public function patients(Request $request): View
    {
        $totalPatients = Patient::count();
        $maleCount = Patient::where('sex', 'Male')->count();
        $femaleCount = Patient::where('sex', 'Female')->count();

        $purokStats = Purok::withCount('patients')
            ->orderBy('name')
            ->get();

        $selectedPurokId = $request->input('purok_id');
        $patientsQuery = Patient::with('purok');

        if ($selectedPurokId) {
            $patientsQuery->where('purok_id', $selectedPurokId);
        }

        $patientList = $patientsQuery->orderBy('last_name')->get();

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
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $countsByService = HealthServiceRecord::whereBetween('service_date', [$startDate, $endDate])
            ->select('service_type', DB::raw('count(*) as count'))
            ->groupBy('service_type')
            ->pluck('count', 'service_type')
            ->toArray();

        $records = HealthServiceRecord::with(['patient.purok', 'user'])
            ->whereBetween('service_date', [$startDate, $endDate])
            ->orderBy('service_date', 'desc')
            ->get();

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
        $items = InventoryItem::with(['batches' => function ($q) {
            $q->where('status', 'active');
        }])->orderBy('name')->get();

        $lowStockItems = $items->filter(function ($item) {
            return $item->total_stock <= $item->minimum_stock_alert;
        });

        $nearExpiryBatches = InventoryBatch::with('item')
            ->nearExpiry(90)
            ->get();

        $recentTransactions = InventoryTransaction::with(['item', 'batch', 'user', 'patient'])
            ->latest('transaction_date')
            ->take(50)
            ->get();

        return view('reports.inventory', compact(
            'items',
            'lowStockItems',
            'nearExpiryBatches',
            'recentTransactions'
        ));
    }
}
