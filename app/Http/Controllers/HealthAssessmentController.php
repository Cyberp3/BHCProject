<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\HealthAssessment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HealthAssessmentController extends Controller
{
    //
    public function create(Patient $patient): View
    {
        $today = Carbon::today()->format('Y-m-d');

        return view('assessments.create', compact('patient', 'today'));
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'assessment_date' => ['required', 'date', 'before_or_equal:today', 'after:1900-01-01'],
            'weight_kg' => ['nullable', 'numeric', 'min:0.5', 'max:300'],
            'height_cm' => ['nullable', 'numeric', 'min:30', 'max:250'],
            'systolic_bp' => ['nullable', 'integer', 'min:50', 'max:260', 'required_with:diastolic_bp'],
            'diastolic_bp' => ['nullable', 'integer', 'min:30', 'max:160', 'required_with:systolic_bp'],
            'pulse_rate' => ['nullable', 'integer', 'min:30', 'max:220'],
            'respiratory_rate' => ['nullable', 'integer', 'min:10', 'max:80'],
            'temperature_celsius' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'nutritional_status' => ['nullable', 'string', Rule::in(['Normal', 'Underweight', 'Overweight', 'Obese', 'Wasted', 'Stunted'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'assessment_date.before_or_equal' => 'The assessment date cannot be a future date.',
            'assessment_date.after' => 'The assessment date must be after January 1, 1900.',
            'systolic_bp.required_with' => 'Systolic blood pressure is required when diastolic pressure is provided.',
            'diastolic_bp.required_with' => 'Diastolic blood pressure is required when systolic pressure is provided.',
            'nutritional_status.in' => 'Please select a valid nutritional status assessment.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $systolic = $request->input('systolic_bp');
            $diastolic = $request->input('diastolic_bp');

            if ($systolic !== null && $diastolic !== null && is_numeric($systolic) && is_numeric($diastolic)) {
                if ((int) $systolic <= (int) $diastolic) {
                    $validator->errors()->add('systolic_bp', 'Systolic blood pressure must be higher than diastolic blood pressure.');
                }
            }

            $measurements = [
                'weight_kg',
                'height_cm',
                'systolic_bp',
                'diastolic_bp',
                'pulse_rate',
                'respiratory_rate',
                'temperature_celsius',
                'nutritional_status',
                'notes',
            ];

            $hasAnyValue = collect($measurements)->some(fn ($field) => filled($request->input($field)));

            if (! $hasAnyValue) {
                $validator->errors()->add('weight_kg', 'At least one vital sign or anthropometric measurement must be recorded.');
            }
        });

        $validated = $validator->validate();

        $validated['patient_id'] = $patient->id;
        $validated['user_id'] = Auth::id();

        $assessment = HealthAssessment::create($validated);

        AuditLog::log(
            'RECORD_ASSESSMENT',
            'Assessments',
            "Recorded health/vitals assessment for {$patient->full_name}",
            $assessment->id
        );

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Health assessment and vitals recorded successfully.');
    }
}
