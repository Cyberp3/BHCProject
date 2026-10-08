<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\HealthServiceRecord;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class HealthServiceController extends Controller
{
    //
    public function index(Request $request): View
    {
        $query = HealthServiceRecord::with(['patient.purok', 'user'])->latest('service_date');

        if ($request->filled('service_type')) {
            $query->serviceType($request->input('service_type'));
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->whereHas('patient', function ($q) use ($term) {
                $q->search($term);
            });
        }

        $records = $query->paginate(15)->withQueryString();
        $services = HealthServiceRecord::SERVICES;

        $programCounts = HealthServiceRecord::select('service_type', DB::raw('count(*) as count'))
            ->groupBy('service_type')
            ->pluck('count', 'service_type')
            ->toArray();

        return view('services.index', compact('records', 'services', 'programCounts'));
    }

    public function create(Request $request): View
    {
        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::findOrFail($request->input('patient_id'));
        }

        $selectedType = $request->input('type', 'family_planning');
        $services = HealthServiceRecord::SERVICES;
        $today = Carbon::today()->format('Y-m-d');
        $patients = Patient::with('purok')->orderBy('last_name')->get();

        return view('services.create', compact('patient', 'patients', 'selectedType', 'services', 'today'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'service_type' => ['required', Rule::in(array_keys(HealthServiceRecord::SERVICES))],
            'service_date' => ['required', 'date', 'before_or_equal:today'],
            'complaint_or_reason' => ['nullable', 'string'],
            'findings_and_notes' => ['nullable', 'string'],
            'service_specific_data' => ['nullable', 'array'],
            'next_follow_up_date' => ['nullable', 'date', 'after:today'],
        ]);

        $validated['user_id'] = Auth::id();

        $record = HealthServiceRecord::create($validated);
        $patient = Patient::findOrFail($validated['patient_id']);

        AuditLog::log(
            'RECORD_SERVICE',
            'Services',
            "Recorded {$record->service_title} for patient {$patient->full_name}",
            $record->id
        );

        return redirect()->route('patients.show', $patient)
            ->with('success', "{$record->service_title} record saved successfully.");
    }

    public function show(HealthServiceRecord $serviceRecord): View
    {
        $serviceRecord->load(['patient.purok', 'user']);

        return view('services.show', compact('serviceRecord'));
    }
}
