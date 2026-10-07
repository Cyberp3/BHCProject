<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\HealthServiceRecord;
use App\Models\Patient;
use App\Models\Purok;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    //
    public function index(Request $request): View
    {
        $query = Patient::with('purok')->latest();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('purok_id')) {
            $query->filterPurok($request->input('purok_id'));
        }

        if ($request->filled('sex')) {
            $query->filterSex($request->input('sex'));
        }

        if ($request->filled('age_bracket')) {
            $query->filterAgeBracket($request->input('age_bracket'));
        }

        if ($request->filled('service_type')) {
            $query->filterService($request->input('service_type'));
        }

        $patients = $query->paginate(15)->withQueryString();
        $puroks = Purok::orderBy('name')->get();
        $services = HealthServiceRecord::SERVICES;

        return view('patients.index', compact('patients', 'puroks', 'services'));
    }

    public function create(): View
    {
        $year = Carbon::now()->year;
        $countThisYear = Patient::whereYear('created_at', $year)->count() + 1;
        $suggestedControlNumber = sprintf('TAC-%d-%04d', $year, $countThisYear);

        $puroks = Purok::orderBy('name')->get();

        return view('patients.create', compact('suggestedControlNumber', 'puroks'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_control_number' => ['required', 'string', 'max:50', 'unique:patients,patient_control_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'sex' => ['required', 'in:Male,Female'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'civil_status' => ['nullable', 'string', 'max:30'],
            'purok_id' => ['required', 'exists:puroks,id'],
            'street_address' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:30'],
            'philhealth_number' => ['nullable', 'string', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:30'],
        ], [
            'street_address.required' => 'The street address is required so the patient can be located.',
            'contact_number.required' => 'The contact number is required to proceed with registration.',
            'philhealth_number.regex' => 'The PhilHealth number must follow the official format: XX-XXXXXXXXX-X (e.g. 12-345678901-2).',
        ]);

        $validated['created_by'] = Auth::id();

        $patient = Patient::create($validated);

        AuditLog::log(
            'CREATE_PATIENT',
            'Patients',
            "Registered new patient {$patient->full_name} ({$patient->patient_control_number})",
            $patient->id
        );

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient record registered successfully.');
    }

    public function show(Patient $patient): View
    {
        $patient->load([
            'purok',
            'creator',
            'healthAssessments.user',
            'healthServiceRecords.user',
            'appointments.scheduledBy',
            'inventoryTransactions.item',
        ]);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        $puroks = Purok::orderBy('name')->get();

        return view('patients.edit', compact('patient', 'puroks'));
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $validated = $request->validate([
            'patient_control_number' => ['required', 'string', 'max:50', 'unique:patients,patient_control_number,'.$patient->id],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'sex' => ['required', 'in:Male,Female'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:today'],
            'civil_status' => ['nullable', 'string', 'max:30'],
            'purok_id' => ['required', 'exists:puroks,id'],
            'street_address' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:30'],
            'philhealth_number' => ['nullable', 'string', 'regex:/^\d{2}-\d{9}-\d{1}$/'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'emergency_contact_name' => ['nullable', 'string', 'max:150'],
            'emergency_contact_number' => ['nullable', 'string', 'max:30'],
        ], [
            'street_address.required' => 'The street address is required so the patient can be located.',
            'contact_number.required' => 'The contact number is required to proceed with saving changes.',
            'philhealth_number.regex' => 'The PhilHealth number must follow the official format: XX-XXXXXXXXX-X (e.g. 12-345678901-2).',
        ]);

        $patient->update($validated);

        AuditLog::log(
            'UPDATE_PATIENT',
            'Patients',
            "Updated demographics for patient {$patient->full_name} ({$patient->patient_control_number})",
            $patient->id
        );

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient record updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        abort_unless(Auth::user()->isAdmin(), 403, 'Unauthorized: Only health center administrators are authorized to archive patient records.');

        $name = $patient->full_name;
        $id = $patient->id;

        $patient->delete();

        AuditLog::log(
            'DELETE_PATIENT',
            'Patients',
            "Archived patient record: {$name} (Soft Deleted)",
            $id
        );

        return redirect()->route('patients.index')
            ->with('success', 'Patient record archived successfully.');
    }
}
