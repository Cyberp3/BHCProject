<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\HealthServiceRecord;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Models\Purok;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index(): View
    {
        $today = Carbon::today();

        $totalPatients = Patient::count();

        $totalServicesCount = HealthServiceRecord::count();

        // Auto-quarantine expired active batches
        InventoryBatch::where('status', 'active')
            ->whereDate('expiration_date', '<', $today)
            ->update(['status' => 'expired']);

        // Calculate low stock items based on valid unexpired stock
        $items = InventoryItem::with(['batches' => function ($query) {
            $query->where('status', 'active');
        }])->get();

        $lowStockCount = $items->filter(function ($item) {
            return $item->dispensable_stock <= $item->minimum_stock_alert;
        })->count();

        $nearExpiryCount = InventoryBatch::nearExpiry(90)->count();

        $recentServices = HealthServiceRecord::with(['patient', 'user'])
            ->latest('service_date')
            ->take(5)
            ->get();

        $recentPatients = Patient::with('purok')
            ->latest()
            ->take(5)
            ->get();

        $purokBreakdown = Purok::withCount('patients')
            ->orderBy('patients_count', 'desc')
            ->take(6)
            ->get();

        $recentActivities = Auth::user()->isAdmin()
            ? AuditLog::with('user')
                ->latest()
                ->take(6)
                ->get()
            : collect();

        return view('dashboard.index', compact(
            'totalPatients',
            'totalServicesCount',
            'lowStockCount',
            'nearExpiryCount',
            'recentServices',
            'recentPatients',
            'purokBreakdown',
            'recentActivities'
        ));
    }
}
