<?php

use App\Models\Appointment;
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

    $this->patient = Patient::firstOrCreate(
        ['patient_control_number' => 'TAC-SCHED-001'],
        [
            'first_name' => 'Ana',
            'last_name' => 'Reyes',
            'sex' => 'Female',
            'date_of_birth' => '1998-04-10',
            'civil_status' => 'Single',
            'purok_id' => $this->purok->id,
            'street_address' => 'Purok 1 Main Road',
            'contact_number' => '09181234567',
        ]
    );
});

test('appointment service schedules are strictly validated by clinic day', function () {
    // Next Monday is allowed for National Tuberculosis Control Program (NTP)
    $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

    // Immunization is only on Wednesday, so Monday should fail
    $invalidResponse = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'Immunization',
        'appointment_date' => $nextMonday,
        'appointment_time' => '09:00',
        'purpose' => 'Routine Infant Vaccine',
    ]);

    $invalidResponse->assertSessionHasErrors(['appointment_date']);

    // Immunization on next Wednesday should succeed
    $nextWednesday = Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d');
    $validResponse = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'Immunization',
        'appointment_date' => $nextWednesday,
        'appointment_time' => '09:00',
        'purpose' => 'Routine Infant Vaccine',
    ]);

    $validResponse->assertRedirect(route('appointments.index'));
    expect(Appointment::where('patient_id', $this->patient->id)->where('service_type', 'Immunization')->exists())->toBeTrue();
});

test('ntp is strictly morning on monday or tuesday, afternoon or other days fail', function () {
    $nextMonday = Carbon::now()->next(Carbon::MONDAY)->format('Y-m-d');

    // Monday morning (09:00) should succeed
    $validResponse = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'ntp',
        'appointment_date' => $nextMonday,
        'appointment_time' => '09:00',
        'purpose' => 'TB-DOTS Follow-up',
    ]);
    $validResponse->assertSessionHasNoErrors();

    // Monday afternoon (14:00) should fail with session error
    $invalidTime = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'ntp',
        'appointment_date' => $nextMonday,
        'appointment_time' => '14:00',
        'purpose' => 'TB-DOTS Follow-up',
    ]);
    $invalidTime->assertSessionHasErrors(['appointment_date']);

    // Wednesday should fail (Immunization day, not NTP)
    $nextWednesday = Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d');
    $invalidDay = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'ntp',
        'appointment_date' => $nextWednesday,
        'appointment_time' => '09:00',
        'purpose' => 'TB-DOTS Follow-up',
    ]);
    $invalidDay->assertSessionHasErrors(['appointment_date']);
});

test('family planning is strictly afternoon on monday or tuesday', function () {
    $nextTuesday = Carbon::now()->next(Carbon::TUESDAY)->format('Y-m-d');

    // Tuesday afternoon (14:00) should succeed
    $validResponse = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'family_planning',
        'appointment_date' => $nextTuesday,
        'appointment_time' => '14:00',
        'purpose' => 'Depo-Provera Injectable',
    ]);
    $validResponse->assertSessionHasNoErrors();

    // Tuesday morning (10:00) should fail
    $invalidTime = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'family_planning',
        'appointment_date' => $nextTuesday,
        'appointment_time' => '10:00',
        'purpose' => 'Depo-Provera Injectable',
    ]);
    $invalidTime->assertSessionHasErrors(['appointment_date']);
});

test('thursday is purok kalusugan and friday is cvd screening', function () {
    $nextThursday = Carbon::now()->next(Carbon::THURSDAY)->format('Y-m-d');
    $nextFriday = Carbon::now()->next(Carbon::FRIDAY)->format('Y-m-d');

    // Purok Kalusugan on Thursday succeeds
    $resThu = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'purok_kalusugan',
        'appointment_date' => $nextThursday,
        'appointment_time' => '09:00',
        'purpose' => 'Sitio Health Assessment',
    ]);
    $resThu->assertSessionHasNoErrors();

    // Purok Kalusugan on Friday fails
    $resThuFail = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'purok_kalusugan',
        'appointment_date' => $nextFriday,
        'appointment_time' => '09:00',
        'purpose' => 'Sitio Health Assessment',
    ]);
    $resThuFail->assertSessionHasErrors(['appointment_date']);

    // CVD on Friday succeeds
    $resFri = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'cvd_screening',
        'appointment_date' => $nextFriday,
        'appointment_time' => '09:00',
        'purpose' => 'Hypertension Checkup',
    ]);
    $resFri->assertSessionHasNoErrors();
});

test('weekend appointments are strictly rejected', function () {
    $nextSaturday = Carbon::now()->next(Carbon::SATURDAY)->format('Y-m-d');

    $resSat = $this->actingAs($this->admin)->post(route('appointments.store'), [
        'patient_id' => $this->patient->id,
        'service_type' => 'General Consultation',
        'appointment_date' => $nextSaturday,
        'appointment_time' => '09:00',
        'purpose' => 'Weekend Checkup',
    ]);
    $resSat->assertSessionHasErrors(['appointment_date']);
});

test('updating appointment validates clinic schedule rules', function () {
    $nextWednesday = Carbon::now()->next(Carbon::WEDNESDAY)->format('Y-m-d');
    $nextThursday = Carbon::now()->next(Carbon::THURSDAY)->format('Y-m-d');

    $appointment = Appointment::create([
        'patient_id' => $this->patient->id,
        'service_type' => 'immunization',
        'appointment_date' => $nextWednesday,
        'appointment_time' => '09:00',
        'purpose' => 'Vaccine Dose 1',
        'scheduled_by' => $this->admin->id,
        'status' => 'scheduled',
    ]);

    // Updating to an invalid day (Thursday) for Immunization fails
    $resInvalid = $this->actingAs($this->admin)->put(route('appointments.update', $appointment), [
        'service_type' => 'immunization',
        'appointment_date' => $nextThursday,
        'appointment_time' => '09:00',
        'purpose' => 'Rescheduled Vaccine Dose 1',
        'status' => 'scheduled',
    ]);
    $resInvalid->assertSessionHasErrors(['appointment_date']);
});
