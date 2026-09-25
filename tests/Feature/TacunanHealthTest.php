<?php

use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\HealthAssessment;
use App\Models\HealthServiceRecord;
use App\Models\InventoryBatch;
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
});

test('admin can access dashboard and view core operational metrics', function () {
    $response = $this->actingAs($this->admin)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Health Center Dashboard');
    $response->assertSee('Total Patients');
    $response->assertSee('Low Stock Items');
});

test('can register a new patient and view patient profile', function () {
    $patientData = [
        'patient_control_number' => 'TAC-TEST-9999',
        'first_name' => 'Juan',
        'middle_name' => 'Santos',
        'last_name' => 'Dela Cruz',
        'suffix' => 'Jr.',
        'sex' => 'Male',
        'date_of_birth' => '1995-05-15',
        'civil_status' => 'Married',
        'purok_id' => $this->purok->id,
        'street_address' => 'Phase 2, Tacunan',
        'contact_number' => '09171112233',
        'philhealth_number' => '12-345678901-2',
        'blood_type' => 'O+',
    ];

    $response = $this->actingAs($this->admin)->post(route('patients.store'), $patientData);

    $patient = Patient::where('patient_control_number', 'TAC-TEST-9999')->first();
    expect($patient)->not->toBeNull();
    expect($patient->full_name)->toBe('Dela Cruz, Juan S. Jr.');

    $response->assertRedirect(route('patients.show', $patient));

    $showResponse = $this->actingAs($this->admin)->get(route('patients.show', $patient));
    $showResponse->assertStatus(200);
    $showResponse->assertSee('TAC-TEST-9999');
    $showResponse->assertSee('Dela Cruz, Juan S. Jr.');
});

test('can record vital signs and BMI is auto-calculated', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-VITALS-001',
        'first_name' => 'Pedro',
        'last_name' => 'Penduko',
        'sex' => 'Male',
        'date_of_birth' => '2000-01-01',
        'purok_id' => $this->purok->id,
        'created_by' => $this->admin->id,
    ]);

    $vitalsData = [
        'assessment_date' => Carbon::today()->format('Y-m-d'),
        'weight_kg' => 70.0,
        'height_cm' => 175.0, // Height 1.75m -> BMI = 70 / (1.75*1.75) = 22.9 (Normal)
        'systolic_bp' => 120,
        'diastolic_bp' => 80,
        'pulse_rate' => 72,
        'temperature_celsius' => 36.5,
        'notes' => 'Patient in good condition',
    ];

    $response = $this->actingAs($this->admin)->post(route('assessments.store', $patient), $vitalsData);

    $response->assertRedirect(route('patients.show', $patient));

    $assessment = HealthAssessment::where('patient_id', $patient->id)->first();
    expect($assessment)->not->toBeNull();
    expect((float) $assessment->bmi)->toBe(22.9);
    expect($assessment->nutritional_status)->toBe('Normal');
    expect($assessment->blood_pressure)->toBe('120/80 mmHg');
});

test('can log health service consultation and auto-schedule follow-up', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-SRV-001',
        'first_name' => 'Ana',
        'last_name' => 'Reyes',
        'sex' => 'Female',
        'date_of_birth' => '1998-03-20',
        'purok_id' => $this->purok->id,
        'created_by' => $this->admin->id,
    ]);

    $followUpDate = Carbon::tomorrow()->addWeeks(2)->format('Y-m-d');

    $serviceData = [
        'patient_id' => $patient->id,
        'service_type' => 'immunization',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'complaint_or_reason' => 'Routine childhood vaccine',
        'findings_and_notes' => 'Administered Pentavalent dose 2 on left thigh',
        'service_specific_data' => [
            'vaccine_name' => 'Pentavalent (DTP-HepB-Hib)',
            'dose_sequence' => 'Dose 2',
            'admin_site' => 'Left Thigh',
        ],
        'next_follow_up_date' => $followUpDate,
        'schedule_follow_up' => '1',
    ];

    $response = $this->actingAs($this->admin)->post(route('services.store'), $serviceData);

    $response->assertRedirect(route('patients.show', $patient));

    $record = HealthServiceRecord::where('patient_id', $patient->id)->first();
    expect($record)->not->toBeNull();
    expect($record->service_title)->toBe('Immunization');

    // Verify appointment was automatically scheduled
    $appointment = Appointment::where('patient_id', $patient->id)->first();
    expect($appointment)->not->toBeNull();
    expect($appointment->appointment_date->format('Y-m-d'))->toBe($followUpDate);
});

test('inventory dispensing adheres strictly to FIFO allocation', function () {
    // Create an item with 2 batches with different expiration dates
    $item = InventoryItem::create([
        'item_code' => 'MED-FIFO-TEST',
        'name' => 'Amoxicillin 250mg Susp',
        'generic_name' => 'Amoxicillin',
        'category' => 'medicine',
        'dosage_form' => 'Suspension',
        'unit_of_measure' => 'Bottle',
        'minimum_stock_alert' => 10,
    ]);

    // Batch 1: Expiring earlier (30 days from now) - Qty: 20
    $batch1 = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'EARLIER-EXP',
        'date_received' => Carbon::now()->subMonth(),
        'expiration_date' => Carbon::now()->addDays(30),
        'quantity_received' => 20,
        'current_quantity' => 20,
        'status' => 'active',
    ]);

    // Batch 2: Expiring later (180 days from now) - Qty: 30
    $batch2 = InventoryBatch::create([
        'inventory_item_id' => $item->id,
        'batch_number' => 'LATER-EXP',
        'date_received' => Carbon::now()->subWeek(),
        'expiration_date' => Carbon::now()->addDays(180),
        'quantity_received' => 30,
        'current_quantity' => 30,
        'status' => 'active',
    ]);

    // Dispense 25 bottles:
    // Should completely deplete Batch 1 (20 bottles) and deduct 5 bottles from Batch 2!
    $dispenseData = [
        'inventory_item_id' => $item->id,
        'quantity' => 25,
        'remarks' => 'Dispensed for outreach clinic',
    ];

    $response = $this->actingAs($this->admin)->post(route('inventory.dispense.process'), $dispenseData);

    $response->assertRedirect(route('inventory.index'));

    $batch1->refresh();
    $batch2->refresh();

    // FIFO Verification
    expect($batch1->current_quantity)->toBe(0);
    expect($batch1->status)->toBe('depleted');

    expect($batch2->current_quantity)->toBe(25); // 30 - 5 = 25
    expect($batch2->status)->toBe('active');
});

test('operational reports endpoints render valid data', function () {
    $reports = [
        route('reports.index'),
        route('reports.patients'),
        route('reports.services'),
        route('reports.appointments'),
        route('reports.inventory'),
    ];

    foreach ($reports as $url) {
        $response = $this->actingAs($this->admin)->get($url);
        $response->assertStatus(200);
    }
});

test('critical user actions generate audit logs', function () {
    $auditLogsCountBefore = AuditLog::count();

    // Perform an action: create a new patient
    $patientData = [
        'patient_control_number' => 'TAC-AUDIT-001',
        'first_name' => 'Maria',
        'last_name' => 'Clara',
        'sex' => 'Female',
        'date_of_birth' => '1999-07-10',
        'purok_id' => $this->purok->id,
    ];

    $this->actingAs($this->admin)->post(route('patients.store'), $patientData);

    expect(AuditLog::count())->toBeGreaterThan($auditLogsCountBefore);

    $lastLog = AuditLog::latest('id')->first();
    expect($lastLog->action)->toBe('CREATE_PATIENT');
    expect($lastLog->module)->toBe('Patients');
});

test('all application view screens render successfully without errors', function () {
    $patient = Patient::create([
        'patient_control_number' => 'TAC-PAGE-001',
        'first_name' => 'Maria',
        'last_name' => 'Santos',
        'sex' => 'Female',
        'date_of_birth' => '1990-01-01',
        'purok_id' => $this->purok->id,
        'created_by' => $this->admin->id,
    ]);

    $item = InventoryItem::first() ?? InventoryItem::create([
        'item_code' => 'MED-TEST-PAGE',
        'name' => 'Test Item',
        'category' => 'medicine',
        'unit_of_measure' => 'Tablet',
        'minimum_stock_alert' => 10,
    ]);

    $appointment = Appointment::create([
        'patient_id' => $patient->id,
        'scheduled_by' => $this->admin->id,
        'appointment_date' => Carbon::tomorrow(),
        'service_type' => 'immunization',
        'purpose' => 'Checkup',
        'status' => 'scheduled',
    ]);

    $urls = [
        route('dashboard'),
        route('patients.index'),
        route('patients.create'),
        route('patients.show', $patient),
        route('patients.edit', $patient),
        route('assessments.create', $patient),
        route('services.index'),
        route('services.create'),
        route('services.create', ['patient_id' => $patient->id]),
        route('appointments.index'),
        route('appointments.create'),
        route('appointments.create', ['patient_id' => $patient->id]),
        route('appointments.edit', $appointment),
        route('inventory.index'),
        route('inventory.items.create'),
        route('inventory.items.edit', $item),
        route('inventory.batches.receive'),
        route('inventory.batches.receive', ['item_id' => $item->id]),
        route('inventory.dispense'),
        route('inventory.dispense', ['item_id' => $item->id]),
        route('inventory.dispense', ['patient_id' => $patient->id]),
        route('reports.index'),
        route('reports.patients'),
        route('reports.services'),
        route('reports.appointments'),
        route('reports.inventory'),
        route('users.index'),
        route('users.create'),
        route('users.edit', $this->admin),
        route('audit-logs.index'),
    ];

    foreach ($urls as $url) {
        $response = $this->actingAs($this->admin)->get($url);
        $response->assertStatus(200);
    }
});

test('non-admin staff are forbidden from user management and audit logs', function () {
    $nurse = User::firstOrCreate(
        ['email' => 'nurse@tacunan.gov.ph'],
        [
            'name' => 'Nurse Maria',
            'password' => bcrypt('password123'),
            'role' => 'nurse',
            'is_active' => true,
        ]
    );

    // Nurse should access dashboard and patients
    $this->actingAs($nurse)->get(route('dashboard'))->assertStatus(200);
    $this->actingAs($nurse)->get(route('patients.index'))->assertStatus(200);

    // Nurse should be forbidden from admin panel
    $this->actingAs($nurse)->get(route('users.index'))->assertStatus(403);
    $this->actingAs($nurse)->get(route('audit-logs.index'))->assertStatus(403);
});
