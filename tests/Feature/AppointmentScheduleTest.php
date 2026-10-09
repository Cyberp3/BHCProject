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
    $nextMonday = Carbon::now()->next(Carbon::MONDAY);
    $nextWednesday = Carbon::now()->next(Carbon::WEDNESDAY);

    // Immunization is only on Wednesday, so Monday should fail
    $invalidError = Appointment::validateClinicSchedule('Immunization', $nextMonday, '09:00');
    expect($invalidError)->not->toBeNull();
    expect($invalidError)->toContain('Wednesday');

    // Immunization on next Wednesday should succeed
    $validError = Appointment::validateClinicSchedule('Immunization', $nextWednesday, '09:00');
    expect($validError)->toBeNull();
});

test('ntp is strictly morning on monday or tuesday, afternoon or other days fail', function () {
    $nextMonday = Carbon::now()->next(Carbon::MONDAY);
    $nextWednesday = Carbon::now()->next(Carbon::WEDNESDAY);

    // Monday morning (09:00) should succeed
    expect(Appointment::validateClinicSchedule('ntp', $nextMonday, '09:00'))->toBeNull();

    // Monday afternoon (14:00) should fail with session error
    $invalidTimeError = Appointment::validateClinicSchedule('ntp', $nextMonday, '14:00');
    expect($invalidTimeError)->not->toBeNull();
    expect($invalidTimeError)->toContain('morning session');

    // Wednesday should fail (Immunization day, not NTP)
    $invalidDayError = Appointment::validateClinicSchedule('ntp', $nextWednesday, '09:00');
    expect($invalidDayError)->not->toBeNull();
    expect($invalidDayError)->toContain('Monday & Tuesday morning');
});

test('family planning is strictly afternoon on monday or tuesday', function () {
    $nextTuesday = Carbon::now()->next(Carbon::TUESDAY);

    // Tuesday afternoon (14:00) should succeed
    expect(Appointment::validateClinicSchedule('family_planning', $nextTuesday, '14:00'))->toBeNull();

    // Tuesday morning (10:00) should fail
    $invalidTimeError = Appointment::validateClinicSchedule('family_planning', $nextTuesday, '10:00');
    expect($invalidTimeError)->not->toBeNull();
    expect($invalidTimeError)->toContain('afternoon session');
});

test('thursday is purok kalusugan and friday is cvd screening', function () {
    $nextThursday = Carbon::now()->next(Carbon::THURSDAY);
    $nextFriday = Carbon::now()->next(Carbon::FRIDAY);

    // Purok Kalusugan on Thursday succeeds
    expect(Appointment::validateClinicSchedule('purok_kalusugan', $nextThursday, '09:00'))->toBeNull();

    // Purok Kalusugan on Friday fails
    $thuFail = Appointment::validateClinicSchedule('purok_kalusugan', $nextFriday, '09:00');
    expect($thuFail)->not->toBeNull();

    // CVD on Friday succeeds
    expect(Appointment::validateClinicSchedule('cvd_screening', $nextFriday, '09:00'))->toBeNull();
});

test('weekend appointments are strictly rejected', function () {
    $nextSaturday = Carbon::now()->next(Carbon::SATURDAY);

    $satError = Appointment::validateClinicSchedule('General Consultation', $nextSaturday, '09:00');
    expect($satError)->not->toBeNull();
    expect($satError)->toContain('weekends');
});
