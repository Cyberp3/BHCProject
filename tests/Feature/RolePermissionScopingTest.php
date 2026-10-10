<?php

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

    $this->nurse = User::firstOrCreate(
        ['email' => 'nurse@tacunan.gov.ph'],
        [
            'name' => 'Nurse Maria Santos',
            'password' => bcrypt('password123'),
            'role' => 'nurse',
            'is_active' => true,
        ]
    );

    $this->midwife = User::firstOrCreate(
        ['email' => 'midwife@tacunan.gov.ph'],
        [
            'name' => 'Midwife Elena Reyes',
            'password' => bcrypt('password123'),
            'role' => 'midwife',
            'is_active' => true,
        ]
    );

    $this->bhw = User::firstOrCreate(
        ['email' => 'bhw@tacunan.gov.ph'],
        [
            'name' => 'BHW Rosa Flores',
            'password' => bcrypt('password123'),
            'role' => 'bhw',
            'is_active' => true,
        ]
    );

    $this->patient = Patient::firstOrCreate(
        ['patient_control_number' => 'TAC-ROLE-001'],
        [
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'sex' => 'Male',
            'date_of_birth' => '1990-01-01',
            'civil_status' => 'Single',
            'purok_id' => $this->purok->id,
            'street_address' => 'Purok 1 Main',
            'contact_number' => '09171112233',
        ]
    );
});

test('nurse and midwife can access and record all health service programs', function () {
    foreach ([$this->nurse, $this->midwife] as $clinicalStaff) {
        // Can open create form for prenatal
        $response = $this->actingAs($clinicalStaff)->get(route('services.create', ['type' => 'prenatal_care']));
        $response->assertOk();
        $response->assertSee('Prenatal Care');

        // Can record prenatal consultation
        $postResponse = $this->actingAs($clinicalStaff)->post(route('services.store'), [
            'patient_id' => $this->patient->id,
            'service_type' => 'prenatal_care',
            'service_date' => Carbon::today()->format('Y-m-d'),
            'complaint_or_reason' => 'Routine prenatal check',
            'service_specific_data' => [
                'trimester' => '1st Trimester',
            ],
        ]);
        $postResponse->assertRedirect(route('patients.show', $this->patient));
    }
});

test('bhw can record purok kalusugan community outreach', function () {
    $response = $this->actingAs($this->bhw)->get(route('services.create', ['type' => 'purok_kalusugan']));
    $response->assertOk();
    $response->assertSee('Purok Kalusugan');
    $response->assertSee('BHW Community Outreach');

    $postResponse = $this->actingAs($this->bhw)->post(route('services.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'purok_kalusugan',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'complaint_or_reason' => 'Community household health visit',
        'service_specific_data' => [
            'outreach_activity' => 'Health promotion and sanitation check',
        ],
    ]);
    $postResponse->assertRedirect(route('patients.show', $this->patient));

    $record = HealthServiceRecord::where('patient_id', $this->patient->id)
        ->where('service_type', 'purok_kalusugan')
        ->first();
    expect($record)->not->toBeNull();
    expect($record->user_id)->toBe($this->bhw->id);
});

test('bhw is strictly blocked from recording clinical health programs', function () {
    $clinicalTypes = [
        'family_planning',
        'prenatal_care',
        'immunization',
        'cvd_screening',
        'philpen',
        'ntp',
        'bns_program',
    ];

    foreach ($clinicalTypes as $serviceType) {
        $response = $this->actingAs($this->bhw)->post(route('services.store'), [
            'patient_id' => $this->patient->id,
            'service_type' => $serviceType,
            'service_date' => Carbon::today()->format('Y-m-d'),
            'complaint_or_reason' => 'Attempting clinical submission',
        ]);

        $response->assertForbidden();
    }
});

test('nurse and midwife can dispense medicine in inventory', function () {
    $item = InventoryItem::firstOrCreate(
        ['item_code' => 'MED-ROLE-DISP'],
        [
            'name' => 'Paracetamol 500mg',
            'category' => 'medicine',
            'unit_of_measure' => 'Tablet',
            'minimum_stock_alert' => 10,
        ]
    );

    InventoryBatch::firstOrCreate(
        ['batch_number' => 'LOT-ROLE-DISP-001'],
        [
            'inventory_item_id' => $item->id,
            'date_received' => Carbon::yesterday()->format('Y-m-d'),
            'expiration_date' => Carbon::now()->addYear()->format('Y-m-d'),
            'quantity_received' => 100,
            'current_quantity' => 100,
            'status' => 'active',
        ]
    );

    foreach ([$this->nurse, $this->midwife] as $staff) {
        $formResponse = $this->actingAs($staff)->get(route('inventory.dispense', ['item_id' => $item->id]));
        $formResponse->assertOk();

        $dispenseResponse = $this->actingAs($staff)->post(route('inventory.dispense.process'), [
            'inventory_item_id' => $item->id,
            'patient_id' => $this->patient->id,
            'quantity' => 2,
            'remarks' => 'Dispensed for fever',
        ]);
        $dispenseResponse->assertRedirect(route('inventory.index'));
    }
});

test('bhw is strictly blocked from dispensing medicine and receiving stock batches', function () {
    $item = InventoryItem::firstOrCreate(
        ['item_code' => 'MED-ROLE-BHW'],
        [
            'name' => 'Amoxicillin 500mg',
            'category' => 'medicine',
            'unit_of_measure' => 'Capsule',
            'minimum_stock_alert' => 10,
        ]
    );

    // BHW cannot open dispense form
    $this->actingAs($this->bhw)->get(route('inventory.dispense'))->assertForbidden();

    // BHW cannot process dispensation
    $this->actingAs($this->bhw)->post(route('inventory.dispense.process'), [
        'inventory_item_id' => $item->id,
        'patient_id' => $this->patient->id,
        'quantity' => 5,
    ])->assertForbidden();

    // BHW cannot open receive batch form
    $this->actingAs($this->bhw)->get(route('inventory.batches.receive'))->assertForbidden();

    // BHW cannot submit new batch
    $this->actingAs($this->bhw)->post(route('inventory.batches.store'), [
        'inventory_item_id' => $item->id,
        'batch_number' => 'BHW-BLOCKED-01',
        'date_received' => Carbon::today()->format('Y-m-d'),
        'expiration_date' => Carbon::now()->addYear()->format('Y-m-d'),
        'quantity_received' => 50,
    ])->assertForbidden();

    // BHW cannot add catalog item
    $this->actingAs($this->bhw)->get(route('inventory.items.create'))->assertForbidden();
});

test('admin is blocked from clinical consultations and medicine dispensing', function () {
    // Admin cannot record health services
    $this->actingAs($this->admin)->get(route('services.create'))->assertForbidden();
    $this->actingAs($this->admin)->post(route('services.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'purok_kalusugan',
        'service_date' => Carbon::today()->format('Y-m-d'),
    ])->assertForbidden();

    // Admin cannot dispense medicine
    $this->actingAs($this->admin)->get(route('inventory.dispense'))->assertForbidden();
    $this->actingAs($this->admin)->post(route('inventory.dispense.process'), [
        'inventory_item_id' => 1,
        'quantity' => 1,
    ])->assertForbidden();
});

test('health services record all program-specific parameters and show permanent clinical record notice', function () {
    $prenatalData = [
        'patient_id' => $this->patient->id,
        'service_type' => 'prenatal_care',
        'service_date' => Carbon::today()->format('Y-m-d'),
        'complaint_or_reason' => '2nd trimester evaluation',
        'service_specific_data' => [
            'gravida' => '2',
            'para' => '1',
            'lmp' => '2026-04-01',
            'edc' => '2027-01-06',
            'trimester' => '2nd Trimester (Visit 2)',
            'fundic_height_cm' => '24',
            'fetal_heart_tone' => '144 bpm',
            'tetanus_toxoid_status' => 'Td 2 Given',
        ],
    ];

    $response = $this->actingAs($this->midwife)->post(route('services.store'), $prenatalData);
    $response->assertRedirect(route('patients.show', $this->patient));

    $record = HealthServiceRecord::where('patient_id', $this->patient->id)
        ->where('service_type', 'prenatal_care')
        ->latest('id')
        ->first();

    expect($record)->not->toBeNull();
    expect($record->service_specific_data['gravida'])->toBe('2');
    expect($record->service_specific_data['fundic_height_cm'])->toBe('24');
    expect($record->service_specific_data['fetal_heart_tone'])->toBe('144 bpm');

    // Show view should display parameters and permanent record notice
    $showResponse = $this->actingAs($this->midwife)->get(route('services.show', $record));
    $showResponse->assertOk();
    $showResponse->assertSee('Permanent Clinical Record');
    $showResponse->assertSee('Program-Specific Parameters');
    $showResponse->assertSee('144 bpm');
    $showResponse->assertSee('fundic height cm');

    // Consultations remain immutable with no edit route
    expect(Route::has('services.edit'))->toBeFalse();
    expect(Route::has('services.update'))->toBeFalse();
});

test('bhw users are strictly blocked from reports endpoints and reports links are hidden in UI', function () {
    $reportRoutes = [
        route('reports.index'),
        route('reports.patients'),
        route('reports.services'),
        route('reports.inventory'),
    ];

    // BHW is blocked with 403 Forbidden on all report endpoints
    foreach ($reportRoutes as $url) {
        $response = $this->actingAs($this->bhw)->get($url);
        $response->assertForbidden();
    }

    // BHW dashboard view does not render Reports navigation or reports shortcuts
    $dashboardResponse = $this->actingAs($this->bhw)->get(route('dashboard'));
    $dashboardResponse->assertOk();
    $dashboardResponse->assertDontSee('href="'.route('reports.index').'"', false);
    $dashboardResponse->assertDontSee('href="'.route('reports.patients').'"', false);
    $dashboardResponse->assertDontSee('href="'.route('reports.inventory').'"', false);
    $dashboardResponse->assertDontSee('Full Purok Report');

    // BHW inventory view does not render Full Inventory Report link
    $inventoryResponse = $this->actingAs($this->bhw)->get(route('inventory.index'));
    $inventoryResponse->assertOk();
    $inventoryResponse->assertDontSee('Full Inventory Report');
});

test('authorized clinical staff and administrators can access reports and view navigation links', function () {
    $reportRoutes = [
        route('reports.index'),
        route('reports.patients'),
        route('reports.services'),
        route('reports.inventory'),
    ];

    foreach ([$this->admin, $this->nurse, $this->midwife] as $user) {
        foreach ($reportRoutes as $url) {
            $response = $this->actingAs($user)->get($url);
            $response->assertOk();
        }

        // Navigation renders reports link
        $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertSee('href="'.route('reports.index').'"', false);
    }
});
