<?php

use App\Models\Patient;
use App\Models\Purok;
use App\Models\User;

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

    Patient::firstOrCreate(
        ['patient_control_number' => 'TAC-RPT-001'],
        [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'sex' => 'Male',
            'date_of_birth' => '1985-05-20',
            'purok_id' => $this->purok->id,
            'street_address' => 'Sitio Central',
            'contact_number' => '09170000001',
            'created_by' => $this->admin->id,
        ]
    );
});

test('reports hub page renders report generation cards', function () {
    $response = $this->actingAs($this->admin)->get(route('reports.index'));
    $response->assertStatus(200);
    $response->assertSee('Health Center Reports');
    $response->assertSee('Patient Profiling');
    $response->assertSee('Health Services Monthly Summary');
    $response->assertDontSee('Appointments & Follow-up Summary');
    $response->assertSee('Inventory');
});

test('patient demographics report renders official header and certification footer', function () {
    $response = $this->actingAs($this->admin)->get(route('reports.patients'));
    $response->assertStatus(200);
    $response->assertSee('Barangay Tacunan Health Center');
    $response->assertSee('Patient Profiling');
    $response->assertSee('Purok Distribution Report');
    $response->assertSee('Juan');
    $response->assertSee('Dela Cruz');
    $response->assertSee('Certified Correct By:');
    $response->assertSee('Approved By:');
});

test('health services report renders official header and service utilization', function () {
    $response = $this->actingAs($this->admin)->get(route('reports.services'));
    $response->assertStatus(200);
    $response->assertSee('Barangay Tacunan Health Center');
    $response->assertSee('Health Services');
    $response->assertSee('Programs Utilization Report');
    $response->assertSee('Service Utilization by Program');
    $response->assertSee('Certified Correct By:');
});

test('inventory report renders stock balance, near-expiry table, and official certification footer', function () {
    $response = $this->actingAs($this->admin)->get(route('reports.inventory'));
    $response->assertStatus(200);
    $response->assertSee('Barangay Tacunan Health Center');
    $response->assertSee('Vaccine Inventory Status Report');
    $response->assertSee('Overall Inventory Summary');
    $response->assertSee('Certified Correct By:');
    $response->assertSee('Approved By:');
});
