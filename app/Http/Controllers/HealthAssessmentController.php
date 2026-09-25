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
        $validated = $request->validate([
            'assessment_date' => ['required', 'date', 'before_or_equal:today'],
            'weight_kg' => ['nullable', 'numeric', 'min:0.5', 'max:300'],
            'height_cm' => ['nullable', 'numeric', 'min:30', 'max:250'],
            'systolic_bp' => ['nullable', 'integer', 'min:50', 'max:260'],
            'diastolic_bp' => ['nullable', 'integer', 'min:30', 'max:160'],
            'pulse_rate' => ['nullable', 'integer', 'min:30', 'max:220'],
            'respiratory_rate' => ['nullable', 'integer', 'min:10', 'max:80'],
            'temperature_celsius' => ['nullable', 'numeric', 'min:30', 'max:45'],
            'nutritional_status' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

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
