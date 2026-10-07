<?php

use App\Models\HealthServiceRecord;
use App\Models\Patient;
use App\Models\Purok;
use App\Models\User;
use Carbon\Carbon;

beforeEach(function () {
    $this->purok1 = Purok::firstOrCreate(['name' => 'Purok 1']);
    $this->purok2 = Purok::firstOrCreate(['name' => 'Purok 2']);
    $this->admin = User::firstOrCreate(
        ['email' => 'admin@tacunan.gov.ph'],
        [
            'name' => 'Admin Staff',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]
    );
});

test('address and contact number are required when registering a patient', function () {
    $response = $this->actingAs($this->admin)->post(route('patients.store'), [
        'patient_control_number' => 'TAC-VAL-0001',
        'first_name' => 'Maria',
        'last_name' => 'Clara',
        'sex' => 'Female',
        'date_of_birth' => '1998-01-01',
        'purok_id' => $this->purok1->id,
        'street_address' => '',
        'contact_number' => '',
    ]);

    $response->assertSessionHasErrors(['street_address', 'contact_number']);
});

test('philhealth number must follow official XX-XXXXXXXXX-X format', function () {
    // Invalid format: missing hyphens or bad length
    $responseInvalid = $this->actingAs($this->admin)->post(route('patients.store'), [
        'patient_control_number' => 'TAC-VAL-0002',
        'first_name' => 'Crisostomo',
        'last_name' => 'Ibarra',
        'sex' => 'Male',
        'date_of_birth' => '1990-03-10',
        'purok_id' => $this->purok1->id,
        'street_address' => 'Poblacion Tacunan',
        'contact_number' => '09123456789',
        'philhealth_number' => '123456789012',
    ]);

    $responseInvalid->assertSessionHasErrors(['philhealth_number']);

    // Valid format: 12-345678901-2
    $responseValid = $this->actingAs($this->admin)->post(route('patients.store'), [
        'patient_control_number' => 'TAC-VAL-0003',
        'first_name' => 'Crisostomo',
        'last_name' => 'Ibarra',
        'sex' => 'Male',
        'date_of_birth' => '1990-03-10',
        'purok_id' => $this->purok1->id,
        'street_address' => 'Poblacion Tacunan',
        'contact_number' => '09123456789',
        'philhealth_number' => '12-345678901-2',
    ]);

    $responseValid->assertSessionHasNoErrors();
    $this->assertDatabaseHas('patients', ['patient_control_number' => 'TAC-VAL-0003']);
});

test('patients can be filtered by purok, sex, age bracket, and health service', function () {
    // Create an Infant patient (under 1 year old)
    $infant = Patient::create([
        'patient_control_number' => 'TAC-FILTER-001',
        'first_name' => 'Baby',
        'last_name' => 'Boy',
        'sex' => 'Male',
        'date_of_birth' => Carbon::today()->subMonths(6)->toDateString(),
        'purok_id' => $this->purok1->id,
        'street_address' => 'Purok 1 St',
        'contact_number' => '09111111111',
        'created_by' => $this->admin->id,
    ]);

    // Create a Senior patient (65 years old) in Purok 2
    $senior = Patient::create([
        'patient_control_number' => 'TAC-FILTER-002',
        'first_name' => 'Lola',
        'last_name' => 'Santos',
        'sex' => 'Female',
        'date_of_birth' => Carbon::today()->subYears(65)->toDateString(),
        'purok_id' => $this->purok2->id,
        'street_address' => 'Purok 2 St',
        'contact_number' => '09222222222',
        'created_by' => $this->admin->id,
    ]);

    // Assign Immunization record to infant
    HealthServiceRecord::create([
        'patient_id' => $infant->id,
        'user_id' => $this->admin->id,
        'service_type' => 'immunization',
        'service_date' => Carbon::today()->toDateString(),
        'complaint_or_reason' => 'Routine pentavalent vaccination',
    ]);

    // Filter by Sex: Female
    $resSex = $this->actingAs($this->admin)->get(route('patients.index', ['sex' => 'Female']));
    $resSex->assertStatus(200);
    $resSex->assertSee('Lola');
    $resSex->assertDontSee('Baby Boy');

    // Filter by Age Bracket: Infant
    $resAge = $this->actingAs($this->admin)->get(route('patients.index', ['age_bracket' => 'infant']));
    $resAge->assertStatus(200);
    $resAge->assertSee('Baby');
    $resAge->assertDontSee('Lola');

    // Filter by Service Type: Immunization
    $resService = $this->actingAs($this->admin)->get(route('patients.index', ['service_type' => 'immunization']));
    $resService->assertStatus(200);
    $resService->assertSee('Baby');
    $resService->assertDontSee('Lola');
});
