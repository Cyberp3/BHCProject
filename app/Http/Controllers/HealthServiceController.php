<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\HealthServiceRecord;
use App\Models\Patient;
use App\Models\User;
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
            $query->where(function ($q) use ($term) {
                $q->whereHas('patient', function ($pq) use ($term) {
                    $pq->withTrashed()->search($term);
                })
                    ->orWhere('complaint_or_reason', 'like', "%{$term}%")
                    ->orWhere('findings_and_notes', 'like', "%{$term}%");
            });
        }

        $records = $query->paginate(10)->withQueryString();
        $services = HealthServiceRecord::SERVICES;

        $programCounts = HealthServiceRecord::select('service_type', DB::raw('count(*) as count'))
            ->groupBy('service_type')
            ->pluck('count', 'service_type')
            ->toArray();

        return view('services.index', compact('records', 'services', 'programCounts'));
    }

    public function create(Request $request): View
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->isAdmin(), 403, 'Unauthorized: Health program consultations and clinical care must be conducted and recorded by authorized healthcare personnel.');

        $allowedServices = $user->allowedHealthServices();
        abort_if(empty($allowedServices), 403, 'Unauthorized: You are not authorized to record health service consultations.');

        $patient = null;
        if ($request->filled('patient_id')) {
            $patient = Patient::findOrFail($request->input('patient_id'));
        }

        $selectedType = $request->input('type');
        if (! array_key_exists((string) $selectedType, $allowedServices)) {
            $selectedType = array_key_first($allowedServices);
        }

        $services = $allowedServices;
        $today = Carbon::today()->format('Y-m-d');
        $patients = Patient::with('purok')->orderBy('last_name')->get();

        return view('services.create', compact('patient', 'patients', 'selectedType', 'services', 'today'));
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        abort_if($user->isAdmin(), 403, 'Unauthorized: Health program consultations and clinical care must be conducted and recorded by authorized healthcare personnel.');

        $allowedServices = $user->allowedHealthServices();
        $requestedType = (string) $request->input('service_type');

        if (! array_key_exists($requestedType, $allowedServices)) {
            if ($user->isBhw()) {
                abort(403, 'Unauthorized: Barangay Health Workers (BHW) are only authorized to record Purok Kalusugan (Community Outreach) activities. Clinical programs require a Registered Nurse or Midwife.');
            }
            abort(403, 'Unauthorized: You are not authorized to record consultations for this health program.');
        }

        $validated = $request->validate([
            'patient_id' => ['required', Rule::exists('patients', 'id')->whereNull('deleted_at')],
            'service_type' => ['required', Rule::in(array_keys($allowedServices))],
            'service_date' => ['required', 'date', 'before_or_equal:today', 'after:2000-01-01'],
            'complaint_or_reason' => ['nullable', 'string', 'max:1000'],
            'findings_and_notes' => ['nullable', 'string'],
            'service_specific_data' => ['nullable', 'array'],
            'next_follow_up_date' => ['nullable', 'date', 'after:today'],
        ], [
            'patient_id.exists' => 'The selected patient record does not exist or has been archived.',
            'service_date.after' => 'Service date cannot be earlier than January 1, 2000.',
            'service_date.before_or_equal' => 'Service date cannot be a future date.',
        ]);

        $allowedKeys = match ($validated['service_type']) {
            'family_planning' => ['method', 'client_type', 'source', 'drop_out_reason', 'method_accepted'],
            'prenatal_care' => ['gravida', 'para', 'lmp', 'edc', 'trimester', 'fundic_height_cm', 'fetal_heart_tone', 'tetanus_toxoid_status'],
            'immunization' => ['vaccine_name', 'dose_sequence', 'admin_site', 'batch_lot_number', 'adverse_events', 'vaccine_administered', 'site_of_injection'],
            'cvd_screening' => ['cvd_risk', 'organ_damage', 'systolic_bp', 'diastolic_bp', 'smoker_status', 'cvd_risk_level', 'has_hypertension_history', 'has_diabetes_history'],
            'philpen' => ['tobacco_use', 'alcohol_use', 'fbs', 'total_cholesterol', 'risk_stratification', 'alcohol_consumption', 'fasting_blood_sugar'],
            'ntp' => ['tb_presumptive', 'cough_duration', 'sputum_genexpert_result', 'chest_xray_findings', 'treatment_regimen', 'tb_testing'],
            'purok_kalusugan' => ['activity_type', 'household_members_screened', 'sanitation_status', 'health_education_topic', 'outreach_activity'],
            'bns_program' => ['target_group', 'bns_intervention', 'bns_category', 'weight_for_age', 'height_for_age', 'weight_for_length', 'vitamin_a_given', 'deworming_given'],
            default => [],
        };

        if (! empty($validated['service_specific_data']) && is_array($validated['service_specific_data'])) {
            $filtered = [];
            foreach ($allowedKeys as $key) {
                if (isset($validated['service_specific_data'][$key]) && $validated['service_specific_data'][$key] !== '') {
                    $filtered[$key] = $validated['service_specific_data'][$key];
                }
            }
            $validated['service_specific_data'] = ! empty($filtered) ? $filtered : null;
        } else {
            $validated['service_specific_data'] = null;
        }

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
