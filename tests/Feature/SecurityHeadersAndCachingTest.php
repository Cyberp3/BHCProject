<?php

use App\Models\User;

test('response includes strict anti-caching and security headers on public login page', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);

    // Verify Anti-Caching Headers (F12 Network check)
    $cacheControl = $response->headers->get('Cache-Control');
    expect($cacheControl)->toContain('no-cache');
    expect($cacheControl)->toContain('no-store');
    expect($cacheControl)->toContain('must-revalidate');

    expect($response->headers->get('Pragma'))->toBe('no-cache');
    expect($response->headers->get('Expires'))->toBe('Sun, 02 Jan 1990 00:00:00 GMT');

    // Verify Security Headers
    expect($response->headers->get('X-Frame-Options'))->toBe('SAMEORIGIN');
    expect($response->headers->get('X-Content-Type-Options'))->toBe('nosniff');
    expect($response->headers->get('X-XSS-Protection'))->toBe('1; mode=block');
    expect($response->headers->get('Referrer-Policy'))->toBe('strict-origin-when-cross-origin');
});

test('response includes strict anti-caching headers on authenticated dashboard and clinical routes', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin@tacunan.gov.ph'],
        [
            'name' => 'Admin Staff',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]
    );

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertStatus(200);

    $cacheControl = $response->headers->get('Cache-Control');
    expect($cacheControl)->toContain('no-cache');
    expect($cacheControl)->toContain('no-store');
    expect($cacheControl)->toContain('must-revalidate');
    expect($response->headers->get('Pragma'))->toBe('no-cache');
});

test('login authenticates with client-side sha256 pre-hashed password from browser network submission', function () {
    $rawPassword = 'password123';
    $sha256Hash = hash('sha256', $rawPassword);

    $user = User::firstOrCreate(
        ['email' => 'admin@tacunan.gov.ph'],
        [
            'name' => 'Admin Staff',
            'password' => bcrypt($rawPassword),
            'role' => 'admin',
            'is_active' => true,
        ]
    );

    // Simulate browser sending SHA-256 hash (as seen in F12 Network tab payload)
    $response = $this->post('/login', [
        'email' => 'admin@tacunan.gov.ph',
        'password' => $sha256Hash,
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('login continues to authenticate with direct password for testing compatibility', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('login rejects invalid password and invalid sha256 hash', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $invalidHash = hash('sha256', 'wrong-password-input');

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => $invalidHash,
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});
