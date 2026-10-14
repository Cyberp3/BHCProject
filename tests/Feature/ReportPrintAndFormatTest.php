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

test('inventory report provides specification buttons for critical, near-expiry, and overall inventory views', function () {
    $response = $this->actingAs($this->admin)->get(route('reports.inventory'));
    $response->assertStatus(200);
    $response->assertSee('Report Specification:');
    $response->assertSee('All Sections');
    $response->assertSee('Critical Stock');
    $response->assertSee('Near-Expiry');
    $response->assertSee('Stock Movement Tracker');
    $response->assertSee('Overall Inventory');
});

test('inventory report accepts section parameter for direct URL filtering', function () {
    $responseCritical = $this->actingAs($this->admin)->get(route('reports.inventory', ['section' => 'critical']));
    $responseCritical->assertStatus(200);
    $responseCritical->assertSee("section: 'critical'", false);
    $responseCritical->assertSee('Critical / Low Stock Items Only');

    $responseNear = $this->actingAs($this->admin)->get(route('reports.inventory', ['section' => 'near_expiry']));
    $responseNear->assertStatus(200);
    $responseNear->assertSee("section: 'near_expiry'", false);
    $responseNear->assertSee('Near-Expiry Batches Only');

    $responseMovements = $this->actingAs($this->admin)->get(route('reports.inventory', ['section' => 'movements']));
    $responseMovements->assertStatus(200);
    $responseMovements->assertSee("section: 'movements'", false);
    $responseMovements->assertSee('Stock Movement &amp; Balance Tracker', false);
    $responseMovements->assertSee('Beginning Balance');
    $responseMovements->assertSee('Stock In (+)');
    $responseMovements->assertSee('Stock Out (&minus;)', false);
    $responseMovements->assertSee('Remaining Balance (=)');

    $responseOverall = $this->actingAs($this->admin)->get(route('reports.inventory', ['section' => 'overall']));
    $responseOverall->assertStatus(200);
    $responseOverall->assertSee("section: 'overall'", false);
    $responseOverall->assertSee('Overall Inventory Summary Only');
});

test('inventory report stock movement tracker supports custom date range filtering', function () {
    $response = $this->actingAs($this->admin)->get(route('reports.inventory', [
        'section' => 'movements',
        'start_date' => '2026-10-06',
        'end_date' => '2026-10-07',
    ]));

    $response->assertStatus(200);
    $response->assertSee('value="2026-10-06"', false);
    $response->assertSee('value="2026-10-07"', false);
    $response->assertSee('Stock Movement &amp; Balance Tracker', false);
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

    // Default 10 per page -> page 1 should have pagination controls
    $response = $this->actingAs($this->admin)->get(route('reports.patients'));
    $response->assertOk();
    $response->assertSee('Showing 1 to 10 of');
    $response->assertSee('page=2');

    // Page 2 should render next set of patients
    $page2Response = $this->actingAs($this->admin)->get(route('reports.patients', ['page' => 2]));
    $page2Response->assertOk();
    $page2Response->assertSee('Showing 11 to 20 of');

    // Show All query param should show all records on a single page
    $allResponse = $this->actingAs($this->admin)->get(route('reports.patients', ['per_page' => 'all']));
    $allResponse->assertOk();
    $allResponse->assertDontSee('page=2');
});
