<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\HealthServiceRecord;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    //
    public function index(Request $request): View
    {
        $query = Appointment::with(['patient.purok', 'scheduledBy']);

        $filter = $request->input('filter', 'upcoming');

        if ($filter === 'today') {
            $query->whereDate('appointment_date', Carbon::today());
        } elseif ($filter === 'upcoming') {
            $query->whereDate('appointment_date', '>=', Carbon::today());
        } elseif ($filter === 'past') {
            $query->whereDate('appointment_date', '<', Carbon::today());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->whereHas('patient', function ($q) use ($term) {
                $q->search($term);
            });
        }

        $appointments = $query->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(15)
            ->withQueryString();

        $services = HealthServiceRecord::SERVICES;

        return view('appointments.index', compact('appointments', 'services', 'filter'));
    }

    public function create(Request $request): View
    {
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::findOrFail($request->input('patient_id'));
        }

        $patients = Patient::orderBy('last_name')->get();
        $patients = Patient::with('purok')->orderBy('last_name')->get();
        $services = HealthServiceRecord::SERVICES;
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        return view('appointments.create', compact('patient', 'patients', 'services', 'tomorrow'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'service_type' => ['required', 'string', 'max:50'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['nullable', 'date_format:H:i'],
            'appointment_time' => ['nullable', 'date_format:H:i,H:i:s'],
            'purpose' => ['required', 'string', 'max:255'],
            'status_notes' => ['nullable', 'string'],
        ]);

        $validated['scheduled_by'] = Auth::id();
        $validated['status'] = 'scheduled';

        $appointment = Appointment::create($validated);
        $patient = Patient::findOrFail($validated['patient_id']);

        AuditLog::log(
            'SCHEDULE_APPOINTMENT',
            'Appointments',
            "Scheduled appointment for {$patient->full_name} on {$appointment->appointment_date->format('M d, Y')}",
            $appointment->id
        );

        return redirect()->route('appointments.index')
            ->with('success', 'Follow-up appointment scheduled successfully.');
    }

    public function edit(Appointment $appointment): View
    {
        $appointment->load('patient');
        $appointment->load('patient.purok');
        $services = HealthServiceRecord::SERVICES;

        return view('appointments.edit', compact('appointment', 'services'));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'service_type' => ['required', 'string', 'max:50'],
            'appointment_date' => ['required', 'date'],
            'appointment_time' => ['nullable', 'date_format:H:i'],
            'appointment_time' => ['nullable', 'date_format:H:i,H:i:s'],
            'purpose' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,attended,cancelled,missed'],
            'status_notes' => ['nullable', 'string'],
        ]);

        $appointment->update($validated);

        AuditLog::log(
            'UPDATE_APPOINTMENT',
            'Appointments',
            "Updated appointment for {$appointment->patient->full_name} (Status: {$appointment->status})",
            $appointment->id
        );

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment updated successfully.');
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:scheduled,attended,cancelled,missed'],
        ]);

        $appointment->update($validated);

        AuditLog::log(
            'UPDATE_APPOINTMENT_STATUS',
            'Appointments',
            "Changed appointment status to '{$appointment->status}' for {$appointment->patient->full_name}",
            $appointment->id
        );

        return back()->with('success', "Appointment status updated to {$appointment->status}.");
    }
}
