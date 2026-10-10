<?php

use App\Models\AuditLog;
use App\Models\HealthAssessment;
use App\Models\HealthServiceRecord;
use App\Models\InventoryItem;
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
    // Create a clinical health service record for this patient
    $record = HealthServiceRecord::create([
        'patient_id' => $this->patient->id,
        'user_id' => $this->admin->id,
        'service_type' => 'immunization',
        'service_title' => 'Routine Booster Dose',
        'service_date' => Carbon::now()->format('Y-m-d'),
        'clinical_findings' => 'Booster dose administered normally.',
    ]);

    // Admin archives patient
    $this->actingAs($this->admin)->delete(route('patients.destroy', $this->patient));

    // The clinical record is preserved in the database
    $preservedRecord = HealthServiceRecord::find($record->id);
    expect($preservedRecord)->not->toBeNull();
    expect($preservedRecord->patient_id)->toBe($this->patient->id);

    // The soft-deleted patient can still be accessed via withTrashed relationship
    expect(Patient::withTrashed()->find($preservedRecord->patient_id)->full_name)->toBe('Gomez, Roberto');
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

test('patient model accessors correctly mask sensitive personally identifiable information', function () {
    $this->patient->update([
        'contact_number' => '09171234567',
        'philhealth_number' => '12-345678901-2',
        'emergency_contact_number' => '09189876543',
    ]);

    expect($this->patient->masked_contact_number)->toBe('0917••••567')
        ->and($this->patient->masked_philhealth_number)->toBe('12-•••••••01-2')
        ->and($this->patient->masked_emergency_contact_number)->toBe('0918••••543');
});

test('patient profile screen renders medical confidentiality banner and hides edit actions for admin', function () {
    $adminView = $this->actingAs($this->admin)->get(route('patients.show', $this->patient));
    $adminView->assertStatus(200);
    $adminView->assertSee('Administrative Read-Only View');
    $adminView->assertSee('Medical Confidentiality Enforced');
    $adminView->assertSee('Privacy Shield');
    $adminView->assertDontSee('Edit Profile');

    $nurseView = $this->actingAs($this->nurse)->get(route('patients.show', $this->patient));
    $nurseView->assertStatus(200);
    $nurseView->assertSee('Authorized Healthcare Personnel Access');
    $nurseView->assertSee('Edit Profile');
});

test('patient directory screen renders privacy safeguards and hides edit buttons for admin', function () {
    $adminIndex = $this->actingAs($this->admin)->get(route('patients.index'));
    $adminIndex->assertStatus(200);
    $adminIndex->assertDontSee('RA 10173 Protected');
    $adminIndex->assertSee('Privacy Shield');
    $adminIndex->assertSee('Read-Only');
    $adminIndex->assertDontSee('Edit Patient');

    $nurseIndex = $this->actingAs($this->nurse)->get(route('patients.index'));
    $nurseIndex->assertStatus(200);
    $nurseIndex->assertSee('Edit Patient');
    $nurseIndex->assertDontSee('Read-Only');
});

test('consultation detail screen renders protected health information confidentiality safeguards', function () {
    $record = HealthServiceRecord::create([
        'patient_id' => $this->patient->id,
        'user_id' => $this->nurse->id,
        'service_type' => 'immunization',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'complaint_or_reason' => 'Routine childhood vaccine',
    ]);

    $adminView = $this->actingAs($this->admin)->get(route('services.show', $record));
    $adminView->assertStatus(200);
    $adminView->assertSee('Protected Health Information (PHI)');
    $adminView->assertSee('Administrative Oversight (Read-Only)');

    $nurseView = $this->actingAs($this->nurse)->get(route('services.show', $record));
    $nurseView->assertStatus(200);
    $nurseView->assertSee('Protected Health Information (PHI)');
    $nurseView->assertSee('Attending Healthcare Personnel View');
});

test('administrator can restore an archived patient and action is recorded in audit log', function () {
    $this->patient->delete();
    expect($this->patient->fresh()->trashed())->toBeTrue();

    $response = $this->actingAs($this->admin)->patch(route('patients.restore', $this->patient));

    $response->assertRedirect(route('patients.show', $this->patient));
    $response->assertSessionHas('success', 'Patient record restored successfully.');

    expect($this->patient->fresh()->trashed())->toBeFalse();

    $log = AuditLog::where('module', 'Patients')
        ->where('action', 'RESTORE_PATIENT')
        ->where('record_id', $this->patient->id)
        ->first();

    expect($log)->not->toBeNull();
    expect($log->description)->toContain('Restored archived patient record');
});

test('non-admin personnel cannot restore an archived patient and receive 403', function () {
    $this->patient->delete();

    $response = $this->actingAs($this->nurse)->patch(route('patients.restore', $this->patient));
    $response->assertForbidden();

    expect($this->patient->fresh()->trashed())->toBeTrue();
});

test('archived patient can be viewed on show route and displays archived notice and restore option for admin', function () {
    $this->patient->delete();

    $response = $this->actingAs($this->admin)->get(route('patients.show', $this->patient));
    $response->assertStatus(200);
    $response->assertSee('Archived Patient Record');
    $response->assertSee('Restore Patient');
    $response->assertSee('Archived Record (New entries suspended)');
});

test('healthcare staff cannot edit or update an archived patient record', function () {
    $this->patient->delete();

    $editResponse = $this->actingAs($this->nurse)->get(route('patients.edit', $this->patient));
    $editResponse->assertNotFound();

    $updateResponse = $this->actingAs($this->nurse)->put(route('patients.update', $this->patient), [
        'patient_control_number' => $this->patient->patient_control_number,
        'first_name' => 'Roberto',
        'last_name' => 'Gomez',
        'sex' => 'Male',
        'date_of_birth' => '1980-01-15',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1 Interior',
        'contact_number' => '09170003344',
    ]);
    $updateResponse->assertNotFound();
});

test('multi-word patient search matches full name and comma-separated name', function () {
    $responseFullName = $this->actingAs($this->admin)->get(route('patients.index', ['search' => 'Roberto Gomez']));
    $responseFullName->assertStatus(200);
    $responseFullName->assertSee($this->patient->patient_control_number);

    $responseReverse = $this->actingAs($this->admin)->get(route('patients.index', ['search' => 'Gomez, Roberto']));
    $responseReverse->assertStatus(200);
    $responseReverse->assertSee($this->patient->patient_control_number);
});

test('patients directory status filter isolates active and archived records', function () {
    $archivedPatient = Patient::create([
        'patient_control_number' => 'TAC-STATUS-ARC-'.uniqid(),
        'first_name' => 'Archived',
        'last_name' => 'Person',
        'sex' => 'Female',
        'date_of_birth' => '1995-05-05',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1 Interior',
        'contact_number' => '09170009988',
        'created_by' => $this->admin->id,
    ]);
    $archivedPatient->delete();

    // Default or active status: only active patient appears
    $responseActive = $this->actingAs($this->admin)->get(route('patients.index', ['status' => 'active']));
    $responseActive->assertStatus(200);
    $responseActive->assertSee($this->patient->patient_control_number);
    $responseActive->assertDontSee($archivedPatient->patient_control_number);

    // Archived status: only archived patient appears
    $responseArchived = $this->actingAs($this->admin)->get(route('patients.index', ['status' => 'archived']));
    $responseArchived->assertStatus(200);
    $responseArchived->assertDontSee($this->patient->patient_control_number);
    $responseArchived->assertSee($archivedPatient->patient_control_number);

    // All status: both patients appear
    $responseAll = $this->actingAs($this->admin)->get(route('patients.index', ['status' => 'all']));
    $responseAll->assertStatus(200);
    $responseAll->assertSee($this->patient->patient_control_number);
    $responseAll->assertSee($archivedPatient->patient_control_number);
});

test('inventory dispense rejects archived patients', function () {
    $this->patient->delete();

    $item = InventoryItem::create([
        'item_code' => 'TEST-MED-'.uniqid(),
        'name' => 'Paracetamol 500mg',
        'category' => 'medicine',
        'unit_of_measure' => 'tablet',
        'minimum_stock_alert' => 50,
    ]);

    $dispenseResponse = $this->actingAs($this->nurse)->post(route('inventory.dispense.process'), [
        'inventory_item_id' => $item->id,
        'patient_id' => $this->patient->id,
        'quantity' => 10,
    ]);
    $dispenseResponse->assertSessionHasErrors(['patient_id']);
});

test('patient profile paginates clinical assessments and service records across pages', function () {
    // Create 15 health assessments
    for ($i = 1; $i <= 15; $i++) {
        HealthAssessment::create([
            'patient_id' => $this->patient->id,
            'user_id' => $this->nurse->id,
            'assessment_date' => Carbon::today()->subDays($i)->format('Y-m-d'),
            'weight_kg' => 60.0 + $i,
            'height_cm' => 165.0,
            'notes' => "Assessment visit #{$i}",
        ]);
    }

    $response = $this->actingAs($this->nurse)->get(route('patients.show', $this->patient));
    $response->assertOk();
    $response->assertSee('Vitals & BNS Assessments (15)', false);
    $response->assertSee('Showing 1 to 10 of 15 assessments');
    $response->assertSee('assessments_page=2');

    // Page 2 shows remaining 5 assessments
    $page2Response = $this->actingAs($this->nurse)->get(route('patients.show', [
        'patient' => $this->patient,
        'assessments_page' => 2,
    ]));
    $page2Response->assertOk();
    $page2Response->assertSee('Showing 11 to 15 of 15 assessments');
});
