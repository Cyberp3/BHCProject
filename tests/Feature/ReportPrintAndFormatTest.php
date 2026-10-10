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

test('reports support pagination and customizable page sizes', function () {
    // Generate extra patients to create multiple pages
    for ($i = 2; $i <= 30; $i++) {
        Patient::create([
            'patient_control_number' => "TAC-PAG-{$i}",
            'first_name' => "FirstName{$i}",
            'last_name' => "LastName{$i}",
            'sex' => $i % 2 === 0 ? 'Female' : 'Male',
            'date_of_birth' => '1995-01-01',
            'purok_id' => $this->purok->id,
            'street_address' => 'Sample Street',
            'contact_number' => '091700000'.str_pad($i, 2, '0', STR_PAD_LEFT),
            'created_by' => $this->admin->id,
        ]);
    }

    // Default 25 per page -> page 1 should have pagination controls
    $response = $this->actingAs($this->admin)->get(route('reports.patients'));
    $response->assertOk();
    $response->assertSee('Showing 1 to 25 of');
    $response->assertSee('page=2');

    // Page 2 should render next set of patients
    $page2Response = $this->actingAs($this->admin)->get(route('reports.patients', ['page' => 2]));
    $page2Response->assertOk();
    $page2Response->assertSee('Showing 26 to 30 of');

    // Show All query param should show all records on a single page
    $allResponse = $this->actingAs($this->admin)->get(route('reports.patients', ['per_page' => 'all']));
    $allResponse->assertOk();
    $allResponse->assertDontSee('page=2');
});
