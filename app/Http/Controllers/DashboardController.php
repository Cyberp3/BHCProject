<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\Patient;
use App\Models\Purok;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    //
    public function index(): View
    {
        $today = Carbon::today();

        $totalPatients = Patient::count();

        $todayAppointmentsCount = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'scheduled')
            ->count();

        // Calculate low stock items
        $items = InventoryItem::with(['batches' => function ($query) {
            $query->where('status', 'active');
        }])->get();

        $lowStockCount = $items->filter(function ($item) {
            return $item->batches->sum('current_quantity') <= $item->minimum_stock_alert;
        })->count();

        $nearExpiryCount = InventoryBatch::nearExpiry(90)->count();

        $todayAppointments = Appointment::with('patient')
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time', 'asc')
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

        $recentActivities = AuditLog::with('user')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard.index', compact(
            'totalPatients',
            'todayAppointmentsCount',
            'lowStockCount',
            'nearExpiryCount',
            'todayAppointments',
            'recentPatients',
            'purokBreakdown',
            'recentActivities'
        ));
    }
}
