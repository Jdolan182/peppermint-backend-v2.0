<?php

use App\Models\Setting;

// DB-based maintenance – blocks public/consumer scope only
test('public route returns 503 when db maintenance_enabled is true', function () {
    Setting::set('maintenance_enabled', 'true');

    $this->getJson('/api/public/blogs')
        ->assertStatus(503)
        ->assertJson(['maintenance' => true]);
});

test('public route passes through when maintenance is off', function () {
    $this->getJson('/api/public/blogs')->assertOk();
});

test('admin scope is not blocked by db maintenance_enabled', function () {
    Setting::set('maintenance_enabled', 'true');

    // Admin routes use maintenance:admin scope — DB-based maintenance skips them
    $this->postJson('/api/admin/auth/login', ['email' => 'test@example.com', 'password' => 'password'])
        ->assertStatus(401); // passes maintenance, fails on bad credentials
});

test('consumer routes return 503 when maintenance is enabled', function () {
    Setting::set('maintenance_enabled', 'true');

    $this->postJson('/api/consumer/auth/login', ['email' => 'test@example.com', 'password' => 'password'])
        ->assertStatus(503);
});

// Env-based maintenance – blocks ALL scopes including admin
test('env-based maintenance_mode blocks all routes including admin scope', function () {
    config(['peppermint.maintenance_mode' => true]);

    try {
        $this->postJson('/api/admin/auth/login', ['email' => 'test@example.com', 'password' => 'password'])
            ->assertStatus(503)
            ->assertJson(['maintenance' => true]);
    } finally {
        config(['peppermint.maintenance_mode' => false]);
    }
});
