<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Route;

// DB-based maintenance (scope = public)
test('public route returns 503 when db maintenance_enabled is true', function () {
    Setting::set('maintenance_enabled', 'true');

    Route::get('/test-maintenance-public', fn () => response()->json(['ok' => true]))
        ->middleware('maintenance');

    $this->getJson('/test-maintenance-public')
        ->assertStatus(503)
        ->assertJson(['maintenance' => true]);
});

test('public route passes through when maintenance is off', function () {
    Route::get('/test-maintenance-public', fn () => response()->json(['ok' => true]))
        ->middleware('maintenance');

    $this->getJson('/test-maintenance-public')->assertOk();
});

// DB-based maintenance does NOT block admin scope
test('admin scope is not blocked by db maintenance_enabled', function () {
    Setting::set('maintenance_enabled', 'true');

    Route::get('/test-maintenance-admin', fn () => response()->json(['ok' => true]))
        ->middleware('maintenance:admin');

    $this->getJson('/test-maintenance-admin')->assertOk();
});

// Real routes
test('public blogs returns 503 when maintenance is enabled', function () {
    Setting::set('maintenance_enabled', 'true');

    $this->getJson('/api/public/blogs')
        ->assertStatus(503)
        ->assertJson(['maintenance' => true]);
});

test('admin login is not blocked by db maintenance mode', function () {
    Setting::set('maintenance_enabled', 'true');

    // Admin routes use maintenance:admin scope — DB maintenance does not block them
    $this->postJson('/api/admin/auth/login', [
        'email'    => 'test@example.com',
        'password' => 'password',
    ])
        ->assertStatus(401); // gets past maintenance, fails on credentials
});

test('consumer routes return 503 when maintenance is enabled', function () {
    Setting::set('maintenance_enabled', 'true');

    $this->postJson('/api/consumer/auth/login', [
        'email'    => 'test@example.com',
        'password' => 'password',
    ])
        ->assertStatus(503);
});
