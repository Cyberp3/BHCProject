<?php

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\HealthServiceRecord;
use App\Models\Patient;
use App\Models\Purok;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->purok = Purok::firstOrCreate(['name' => 'Purok 1']);

    $this->admin = User::firstOrCreate(
        ['email' => 'admin@tacunan.gov.ph'],
        [
            'name' => 'Admin Staff',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]
    );

    $this->nurse = User::firstOrCreate(
        ['email' => 'nurse.archive@tacunan.gov.ph'],
        [
            'name' => 'Clinical Nurse',
            'password' => bcrypt('password123'),
            'role' => 'nurse',
            'is_active' => true,
        ]
    );

    $this->patient = Patient::create([
        'patient_control_number' => 'TAC-ARCHIVE-'.uniqid(),
        'first_name' => 'Roberto',
        'last_name' => 'Gomez',
        'sex' => 'Male',
        'date_of_birth' => '1980-01-15',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1 Interior',
        'contact_number' => '09170003344',
        'created_by' => $this->admin->id,
    ]);
});

test('administrator can archive a patient record using soft deletes', function () {
    $response = $this->actingAs($this->admin)->delete(route('patients.destroy', $this->patient));

    $response->assertRedirect(route('patients.index'));
    $response->assertSessionHas('success', 'Patient record archived successfully.');

    // The patient should not be returned by standard queries
    expect(Patient::find($this->patient->id))->toBeNull();

    // The record should still exist in database with a deleted_at timestamp
    $archived = Patient::withTrashed()->find($this->patient->id);
    expect($archived)->not->toBeNull();
    expect($archived->deleted_at)->not->toBeNull();

    // Audit log should record the archive action
    $log = AuditLog::where('module', 'Patients')
        ->where('action', 'DELETE_PATIENT')
        ->where('record_id', $this->patient->id)
        ->first();

    expect($log)->not->toBeNull();
    expect($log->description)->toContain('Soft Deleted');
});

test('non-admin personnel cannot archive patient records and receive 403', function () {
    $response = $this->actingAs($this->nurse)->delete(route('patients.destroy', $this->patient));

    $response->assertForbidden();

    // The patient should remain active and un-deleted
    expect(Patient::find($this->patient->id))->not->toBeNull();
    expect($this->patient->fresh()->deleted_at)->toBeNull();
});

test('historical clinical records remain intact after patient is soft deleted', function () {
    // Create an appointment for this patient
    $nextWednesday = Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d');
    $appointment = Appointment::create([
        'patient_id' => $this->patient->id,
        'service_type' => 'immunization',
        'appointment_date' => $nextWednesday,
        'appointment_time' => '09:00',
        'purpose' => 'Routine Booster',
        'scheduled_by' => $this->admin->id,
        'status' => 'scheduled',
    ]);

    // Admin archives patient
    $this->actingAs($this->admin)->delete(route('patients.destroy', $this->patient));

    // The appointment record is preserved in the database
    $preservedAppt = Appointment::find($appointment->id);
    expect($preservedAppt)->not->toBeNull();
    expect($preservedAppt->patient_id)->toBe($this->patient->id);

    // The soft-deleted patient can still be accessed via withTrashed relationship
    expect(Patient::withTrashed()->find($preservedAppt->patient_id)->full_name)->toBe('Gomez, Roberto');
});

test('suggested control number accounts for soft-deleted patients and never collides', function () {
    $year = Carbon::now()->year;
    $firstNumber = sprintf('TAC-%d-0001', $year);

    $patient1 = Patient::create([
        'patient_control_number' => $firstNumber,
        'first_name' => 'First',
        'last_name' => 'Patient',
        'sex' => 'Male',
        'date_of_birth' => '1990-01-01',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1',
        'contact_number' => '09171112233',
        'created_by' => $this->admin->id,
    ]);

    // Archive (soft delete) patient #1
    $patient1->delete();

    // Now visit the create form
    $response = $this->actingAs($this->admin)->get(route('patients.create'));

    $response->assertStatus(200);
    // Should suggest 0002, NOT 0001 which already exists in soft-deleted state
    $expectedSecond = sprintf('TAC-%d-0002', $year);
    $response->assertSee($expectedSecond);
});

test('health service follow-up advisory date must be a future date', function () {
    $yesterday = Carbon::yesterday()->format('Y-m-d');

    $response = $this->actingAs($this->nurse)->post(route('services.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'immunization',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'next_follow_up_date' => $yesterday,
    ]);

    $response->assertSessionHasErrors(['next_follow_up_date']);
});

test('health service creation rejects archived soft-deleted patients', function () {
    $this->patient->delete();

    $response = $this->actingAs($this->nurse)->post(route('services.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'immunization',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'complaint_or_reason' => 'Routine vaccine',
    ]);

    $response->assertSessionHasErrors(['patient_id']);
});

test('health service consultation index and show pages render cleanly for archived patients', function () {
    $record = HealthServiceRecord::create([
        'patient_id' => $this->patient->id,
        'user_id' => $this->nurse->id,
        'service_type' => 'cvd_screening',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'complaint_or_reason' => 'Hypertension checkup',
        'findings_and_notes' => 'Blood pressure controlled at 120/80',
        'service_specific_data' => [
            'cvd_risk' => 'Low (<10%)',
        ],
    ]);

    // Admin archives patient
    $this->patient->delete();

    // Index page should render without errors and display Archived badge
    $indexResponse = $this->actingAs($this->admin)->get(route('services.index'));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('Archived');
    $indexResponse->assertSee('Gomez, Roberto');

    // Show page should render without crashing on null patient
    $showResponse = $this->actingAs($this->admin)->get(route('services.show', $record));
    $showResponse->assertStatus(200);
    $showResponse->assertSee('Gomez, Roberto');
    $showResponse->assertSee('Archived');
});

test('health service data storage strictly sanitizes and whitelists program parameters', function () {
    $response = $this->actingAs($this->nurse)->post(route('services.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'immunization',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'complaint_or_reason' => 'Measles vaccine',
        'service_specific_data' => [
            // Valid immunization parameters
            'vaccine_name' => 'MMR (Measles-Mumps-Rubella)',
            'dose_sequence' => 'Dose 1',
            'admin_site' => 'Right Deltoid',
            // Leaked fields from other programs (should be stripped!)
            'method' => 'Pills (COC/POP)',
            'trimester' => '1st Trimester (Visit 1)',
            'cvd_risk' => 'Low (<10%)',
            'tobacco_use' => 'Non-smoker',
            'empty_field' => '',
        ],
    ]);

    $response->assertRedirect(route('patients.show', $this->patient));

    $record = HealthServiceRecord::where('patient_id', $this->patient->id)->latest()->first();
    expect($record->service_specific_data)->toHaveKey('vaccine_name');
    expect($record->service_specific_data)->toHaveKey('dose_sequence');
    expect($record->service_specific_data)->toHaveKey('admin_site');
    // Ensure cross-program keys were stripped
    expect($record->service_specific_data)->not->toHaveKey('method');
    expect($record->service_specific_data)->not->toHaveKey('trimester');
    expect($record->service_specific_data)->not->toHaveKey('cvd_risk');
    expect($record->service_specific_data)->not->toHaveKey('tobacco_use');
});

test('services index search finds consultations by clinical findings and complaints', function () {
    HealthServiceRecord::create([
        'patient_id' => $this->patient->id,
        'user_id' => $this->nurse->id,
        'service_type' => 'cvd_screening',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'complaint_or_reason' => 'Dizziness and blurred vision',
        'findings_and_notes' => 'Prescribed Amlodipine 5mg OD for elevated BP',
    ]);

    // Search by diagnosis / medication
    $searchResponse = $this->actingAs($this->admin)->get(route('services.index', ['search' => 'Amlodipine']));
    $searchResponse->assertStatus(200);
    $searchResponse->assertSee('Dizziness and blurred vision');

    // Search by symptom / complaint
    $searchResponse2 = $this->actingAs($this->admin)->get(route('services.index', ['search' => 'blurred vision']));
    $searchResponse2->assertStatus(200);
    $searchResponse2->assertSee('Amlodipine');
});

test('admin is forbidden from editing patient records and receives 403', function () {
    $editPage = $this->actingAs($this->admin)->get(route('patients.edit', $this->patient));
    $editPage->assertForbidden();

    $updateReq = $this->actingAs($this->admin)->put(route('patients.update', $this->patient), [
        'patient_control_number' => $this->patient->patient_control_number,
        'first_name' => 'Roberto',
        'last_name' => 'Gomez',
        'sex' => 'Male',
        'date_of_birth' => '1980-01-15',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1 Interior',
        'contact_number' => '09170003344',
    ]);
    $updateReq->assertForbidden();
});

test('admin is forbidden from creating clinical health assessments and receives 403', function () {
    $createPage = $this->actingAs($this->admin)->get(route('assessments.create', $this->patient));
    $createPage->assertForbidden();

    $storeReq = $this->actingAs($this->admin)->post(route('assessments.store', $this->patient), [
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'weight_kg' => 65.0,
        'height_cm' => 170.0,
        'systolic_bp' => 120,
        'diastolic_bp' => 80,
    ]);
    $storeReq->assertForbidden();
});

test('admin is forbidden from recording health service consultations and receives 403', function () {
    $createPage = $this->actingAs($this->admin)->get(route('services.create'));
    $createPage->assertForbidden();

    $storeReq = $this->actingAs($this->admin)->post(route('services.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'immunization',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'complaint_or_reason' => 'Routine vaccine',
    ]);
    $storeReq->assertForbidden();
});

test('clinical health personnel can edit patient records and record consultations', function () {
    $editPage = $this->actingAs($this->nurse)->get(route('patients.edit', $this->patient));
    $editPage->assertStatus(200);

    $servicePage = $this->actingAs($this->nurse)->get(route('services.create', ['patient_id' => $this->patient->id]));
    $servicePage->assertStatus(200);

    $assessmentPage = $this->actingAs($this->nurse)->get(route('assessments.create', $this->patient));
    $assessmentPage->assertStatus(200);
});
