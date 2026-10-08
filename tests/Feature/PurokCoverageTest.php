<?php

use App\Models\Patient;
use App\Models\Purok;
use App\Models\User;
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
});

test('health center handles all 22 puroks in tacunan', function () {
    expect(Purok::count())->toBeGreaterThanOrEqual(22);

    for ($i = 1; $i <= 22; $i++) {
        expect(Purok::where('name', "Purok {$i}")->exists())->toBeTrue();
    }
});

test('patient registration form renders all 22 puroks in sequential order', function () {
    $response = $this->actingAs($this->admin)->get(route('patients.create'));

    $response->assertStatus(200);
    $response->assertSee('Purok 1');
    $response->assertSee('Purok 12');
    $response->assertSee('Purok 13');
    $response->assertSee('Purok 22');
});

test('patient can be registered and filtered in newly added purok 22', function () {
    $purok22 = Purok::where('name', 'Purok 22')->firstOrFail();

    $response = $this->actingAs($this->admin)->post(route('patients.store'), [
        'patient_control_number' => 'TAC-PUROK22-001',
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'sex' => 'Male',
        'date_of_birth' => '1995-05-15',
        'civil_status' => 'Married',
        'purok_id' => $purok22->id,
        'street_address' => 'KM 12, Purok 22 Tacunan',
        'contact_number' => '09191234567',
    ]);

    $response->assertSessionHasNoErrors();
    $patient = Patient::where('patient_control_number', 'TAC-PUROK22-001')->firstOrFail();
    expect($patient->purok_id)->toBe($purok22->id);
    expect($patient->purok->name)->toBe('Purok 22');

    // Filter patient index by Purok 22
    $indexResponse = $this->actingAs($this->admin)->get(route('patients.index', ['purok_id' => $purok22->id]));
    $indexResponse->assertStatus(200);
    $indexResponse->assertSee('TAC-PUROK22-001');
    $indexResponse->assertSee('Juan');
});
