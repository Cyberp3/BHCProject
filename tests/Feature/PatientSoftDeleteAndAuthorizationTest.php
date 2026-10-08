<?php

use App\Models\Appointment;
use App\Models\AuditLog;
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

    $response = $this->actingAs($this->admin)->post(route('services.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'immunization',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'next_follow_up_date' => $yesterday,
    ]);

    $response->assertSessionHasErrors(['next_follow_up_date']);
});
