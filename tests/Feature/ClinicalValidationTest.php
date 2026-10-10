<?php

use App\Models\HealthAssessment;
use App\Models\Patient;
use App\Models\Purok;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\PurokSeeder;

beforeEach(function () {
    $this->seed(PurokSeeder::class);

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
        ['email' => 'nurse@tacunan.gov.ph'],
        [
            'name' => 'Nurse Joy',
            'password' => bcrypt('password123'),
            'role' => 'nurse',
            'is_active' => true,
        ]
    );

    $this->purok = Purok::first();
});

test('patient registration requires valid name characters without numbers or special symbols', function () {
    $response = $this->actingAs($this->admin)->post(route('patients.store'), [
        'patient_control_number' => 'TAC-VAL-001',
        'first_name' => 'Juan123',
        'last_name' => 'Dela Cruz@!',
        'sex' => 'Male',
        'date_of_birth' => '1990-01-01',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1, Tacunan',
        'contact_number' => '09123456789',
    ]);

    $response->assertSessionHasErrors(['first_name', 'last_name']);
});

test('patient contact number must adhere to Philippine mobile number format', function () {
    // Invalid format: less digits or invalid prefix
    $invalidResponse = $this->actingAs($this->admin)->post(route('patients.store'), [
        'patient_control_number' => 'TAC-VAL-002',
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'sex' => 'Female',
        'date_of_birth' => '1992-05-10',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1, Tacunan',
        'contact_number' => '0812345678', // Invalid prefix & too short
    ]);

    $invalidResponse->assertSessionHasErrors(['contact_number']);

    // Valid format: 09XXXXXXXXX
    $validResponse = $this->actingAs($this->admin)->post(route('patients.store'), [
        'patient_control_number' => 'TAC-VAL-003',
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'sex' => 'Female',
        'date_of_birth' => '1992-05-10',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1, Tacunan',
        'contact_number' => '09171234567',
    ]);

    $validResponse->assertRedirect();
    $this->assertDatabaseHas('patients', [
        'patient_control_number' => 'TAC-VAL-003',
        'contact_number' => '09171234567',
    ]);
});

test('date of birth cannot be in the future or prior to 1900', function () {
    $futureResponse = $this->actingAs($this->admin)->post(route('patients.store'), [
        'patient_control_number' => 'TAC-VAL-004',
        'first_name' => 'Baby',
        'last_name' => 'Future',
        'sex' => 'Female',
        'date_of_birth' => Carbon::tomorrow()->format('Y-m-d'),
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1, Tacunan',
        'contact_number' => '09191234567',
    ]);

    $futureResponse->assertSessionHasErrors(['date_of_birth']);

    $ancientResponse = $this->actingAs($this->admin)->post(route('patients.store'), [
        'patient_control_number' => 'TAC-VAL-005',
        'first_name' => 'Old',
        'last_name' => 'Person',
        'sex' => 'Male',
        'date_of_birth' => '1899-12-31',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1, Tacunan',
        'contact_number' => '09191234567',
    ]);

    $ancientResponse->assertSessionHasErrors(['date_of_birth']);
});

test('blood type and civil status must adhere to valid whitelists', function () {
    $response = $this->actingAs($this->admin)->post(route('patients.store'), [
        'patient_control_number' => 'TAC-VAL-006',
        'first_name' => 'Pedro',
        'last_name' => 'Penduko',
        'sex' => 'Male',
        'date_of_birth' => '1985-04-12',
        'civil_status' => 'UnknownStatus',
        'blood_type' => 'Z+',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1, Tacunan',
        'contact_number' => '09201234567',
    ]);

    $response->assertSessionHasErrors(['civil_status', 'blood_type']);
});

test('health assessment requires assessment date to not be in the future', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-ASSESS-001',
        'first_name' => 'Clara',
        'last_name' => 'Soriano',
        'sex' => 'Female',
        'date_of_birth' => '1994-06-21',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 2, Tacunan',
        'contact_number' => '09221234567',
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->nurse)->post(route('assessments.store', $patient), [
        'assessment_date' => Carbon::tomorrow()->format('Y-m-d'),
        'systolic_bp' => 120,
        'diastolic_bp' => 80,
    ]);

    $response->assertSessionHasErrors(['assessment_date']);
});

test('health assessment blood pressure fields must be paired', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-ASSESS-002',
        'first_name' => 'Danilo',
        'last_name' => 'Alcantara',
        'sex' => 'Male',
        'date_of_birth' => '1988-11-05',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 3, Tacunan',
        'contact_number' => '09231234567',
        'created_by' => $this->admin->id,
    ]);

    // Systolic provided without diastolic
    $response1 = $this->actingAs($this->nurse)->post(route('assessments.store', $patient), [
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'systolic_bp' => 120,
    ]);
    $response1->assertSessionHasErrors(['diastolic_bp']);

    // Diastolic provided without systolic
    $response2 = $this->actingAs($this->nurse)->post(route('assessments.store', $patient), [
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'diastolic_bp' => 80,
    ]);
    $response2->assertSessionHasErrors(['systolic_bp']);
});

test('health assessment enforces physiological rule that systolic BP must be higher than diastolic BP', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-ASSESS-003',
        'first_name' => 'Eduardo',
        'last_name' => 'Manalo',
        'sex' => 'Male',
        'date_of_birth' => '1975-08-14',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 4, Tacunan',
        'contact_number' => '09241234567',
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->nurse)->post(route('assessments.store', $patient), [
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'systolic_bp' => 70,
        'diastolic_bp' => 110, // Inverted BP
    ]);

    $response->assertSessionHasErrors([
        'systolic_bp' => 'Systolic blood pressure must be higher than diastolic blood pressure.',
    ]);
});

test('health assessment cannot be submitted completely blank without any clinical measurements', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-ASSESS-004',
        'first_name' => 'Grace',
        'last_name' => 'Torres',
        'sex' => 'Female',
        'date_of_birth' => '2000-02-18',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 5, Tacunan',
        'contact_number' => '09251234567',
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->nurse)->post(route('assessments.store', $patient), [
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'weight_kg' => null,
        'height_cm' => null,
        'systolic_bp' => null,
        'diastolic_bp' => null,
        'pulse_rate' => null,
        'respiratory_rate' => null,
        'temperature_celsius' => null,
        'nutritional_status' => null,
        'notes' => null,
    ]);

    $response->assertSessionHasErrors([
        'weight_kg' => 'At least one vital sign or anthropometric measurement must be recorded.',
    ]);
});

test('patient update enforces the same demographic validation rules', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-VAL-UPDATE-01',
        'first_name' => 'Roberto',
        'last_name' => 'Dalisay',
        'sex' => 'Male',
        'date_of_birth' => '1980-01-01',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1, Tacunan',
        'contact_number' => '09181234567',
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->nurse)->put(route('patients.update', $patient), [
        'patient_control_number' => 'TAC-VAL-UPDATE-01',
        'first_name' => 'Roberto99', // Invalid name
        'last_name' => 'Dalisay',
        'sex' => 'Male',
        'date_of_birth' => '1980-01-01',
        'civil_status' => 'InvalidStatus', // Invalid civil status
        'blood_type' => 'X-', // Invalid blood type
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 1, Tacunan',
        'contact_number' => '12345', // Invalid phone
    ]);

    $response->assertSessionHasErrors(['first_name', 'civil_status', 'blood_type', 'contact_number']);
});

test('authorized clinical staff can update health assessment and vital signs with auto BMI recalculation and audit trail', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-VAL-UPDATE-02',
        'first_name' => 'Clarissa',
        'last_name' => 'Ocampo',
        'sex' => 'Female',
        'date_of_birth' => '1995-05-12',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 2, Tacunan',
        'contact_number' => '09191234567',
        'created_by' => $this->nurse->id,
    ]);

    $assessment = HealthAssessment::create([
        'patient_id' => $patient->id,
        'user_id' => $this->nurse->id,
        'assessment_date' => Carbon::yesterday()->format('Y-m-d'),
        'weight_kg' => 60.0,
        'height_cm' => 160.0,
        'systolic_bp' => 120,
        'diastolic_bp' => 80,
        'pulse_rate' => 72,
        'respiratory_rate' => 16,
        'temperature_celsius' => 36.5,
        'notes' => 'Original baseline checkup',
    ]);

    expect((float) $assessment->bmi)->toEqual(23.4);

    $response = $this->actingAs($this->nurse)->put(route('assessments.update', $assessment), [
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'weight_kg' => 70.0,
        'height_cm' => 160.0,
        'systolic_bp' => 130,
        'diastolic_bp' => 85,
        'pulse_rate' => 78,
        'respiratory_rate' => 18,
        'temperature_celsius' => 36.8,
        'notes' => 'Updated after re-calibration',
    ]);

    $response->assertRedirect(route('patients.show', $patient));
    $response->assertSessionHas('success');

    $assessment->refresh();
    expect((float) $assessment->weight_kg)->toEqual(70.0);
    expect($assessment->systolic_bp)->toEqual(130);
    expect((float) $assessment->bmi)->toEqual(27.3);
    expect($assessment->notes)->toEqual('Updated after re-calibration');

    $this->assertDatabaseHas('audit_logs', [
        'action' => 'EDIT_ASSESSMENT',
        'module' => 'Assessments',
        'user_id' => $this->nurse->id,
    ]);
});

test('admin cannot update clinical health assessment and receives 403', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-VAL-UPDATE-03',
        'first_name' => 'Felipe',
        'last_name' => 'Cruz',
        'sex' => 'Male',
        'date_of_birth' => '1990-07-20',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 3, Tacunan',
        'contact_number' => '09201234567',
        'created_by' => $this->admin->id,
    ]);

    $assessment = HealthAssessment::create([
        'patient_id' => $patient->id,
        'user_id' => $this->nurse->id,
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'weight_kg' => 65.0,
        'height_cm' => 170.0,
        'systolic_bp' => 120,
        'diastolic_bp' => 80,
    ]);

    $response = $this->actingAs($this->admin)->put(route('assessments.update', $assessment), [
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'weight_kg' => 75.0,
    ]);

    $response->assertStatus(403);
});

test('cannot update health assessment for an archived patient record', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-VAL-UPDATE-04',
        'first_name' => 'Carmela',
        'last_name' => 'Reyes',
        'sex' => 'Female',
        'date_of_birth' => '1992-03-15',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 4, Tacunan',
        'contact_number' => '09211234567',
        'created_by' => $this->nurse->id,
    ]);

    $assessment = HealthAssessment::create([
        'patient_id' => $patient->id,
        'user_id' => $this->nurse->id,
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'weight_kg' => 52.0,
    ]);

    $patient->delete();

    $response = $this->actingAs($this->nurse)->put(route('assessments.update', $assessment), [
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'weight_kg' => 54.0,
    ]);

    $response->assertStatus(422);
});

test('patient show profile renders view and edit modal triggers for recorded vitals', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-VAL-UPDATE-05',
        'first_name' => 'Tristan',
        'last_name' => 'Aquino',
        'sex' => 'Male',
        'date_of_birth' => '2001-09-09',
        'purok_id' => $this->purok->id,
        'street_address' => 'Purok 5, Tacunan',
        'contact_number' => '09221234567',
        'created_by' => $this->nurse->id,
    ]);

    HealthAssessment::create([
        'patient_id' => $patient->id,
        'user_id' => $this->nurse->id,
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'weight_kg' => 68.5,
        'height_cm' => 172.0,
        'systolic_bp' => 118,
        'diastolic_bp' => 76,
        'pulse_rate' => 70,
        'respiratory_rate' => 16,
        'temperature_celsius' => 36.6,
        'notes' => 'Patient in optimal health condition',
    ]);

    $response = $this->actingAs($this->nurse)->get(route('patients.show', $patient));

    $response->assertOk();
    $response->assertSee('Actions');
    $response->assertSee('openViewModal');
    $response->assertSee('openEditModal');
    $response->assertSee('Vital Signs & BNS Assessment Details', false);
    $response->assertSee('Edit Vital Signs & Assessment', false);
});
