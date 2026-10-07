<?php

use App\Models\User;

test('public registration screen is disabled and redirects to login with notice', function () {
    $response = $this->get('/register');

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
});

test('public registration submission is rejected and does not authenticate user', function () {
    $response = $this->post('/register', [
        'name' => 'Unauthorized User',
        'email' => 'unauthorized@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('login'));
    $this->assertGuest();
    expect(User::where('email', 'unauthorized@example.com')->exists())->toBeFalse();
});

test('administrator can provision new authorized staff accounts', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin@tacunan.gov.ph'],
        [
            'name' => 'Admin Staff',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Staff Nurse Elena',
        'email' => 'nurse.elena@tacunan.gov.ph',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
        'role' => 'nurse',
        'contact_number' => '09170001122',
        'is_active' => 1,
    ]);

    $response->assertRedirect(route('users.index'));
    expect(User::where('email', 'nurse.elena@tacunan.gov.ph')->where('role', 'nurse')->exists())->toBeTrue();
});

test('non-admin staff cannot provision new accounts', function () {
    $nurse = User::firstOrCreate(
        ['email' => 'nurse.test@tacunan.gov.ph'],
        [
            'name' => 'Test Nurse',
            'password' => bcrypt('password123'),
            'role' => 'nurse',
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($nurse)->post(route('users.store'), [
        'name' => 'Illegal Staff',
        'email' => 'illegal@tacunan.gov.ph',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'admin',
    ]);

    $response->assertForbidden();
});
