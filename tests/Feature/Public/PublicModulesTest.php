<?php

use App\Models\Setting;

test('modules endpoint returns 200 with all expected keys', function () {
    $this->getJson('/api/public/modules')
        ->assertOk()
        ->assertJsonStructure([
            'blogs', 'public', 'consumers', 'public_login', 'team', 'settings',
            'maintenance', 'maintenance_message', 'site_name',
        ]);
});

test('module is true when env is enabled and no db override', function () {
    // MODULE_BLOGS_ENABLED=true is set in phpunit.xml, no DB override
    $response = $this->getJson('/api/public/modules')->assertOk();

    expect($response->json('blogs'))->toBeTrue();
});

test('module is false when db setting is set to false', function () {
    Setting::set('module_blogs', 'false');

    $response = $this->getJson('/api/public/modules')->assertOk();

    expect($response->json('blogs'))->toBeFalse();
});

test('maintenance is false when neither env nor db setting is active', function () {
    $response = $this->getJson('/api/public/modules')->assertOk();

    expect($response->json('maintenance'))->toBeFalse();
});

test('maintenance is true when db maintenance_enabled is true', function () {
    Setting::set('maintenance_enabled', 'true');

    $response = $this->getJson('/api/public/modules')->assertOk();

    expect($response->json('maintenance'))->toBeTrue();
});

test('maintenance_message defaults to fallback when not set', function () {
    $response = $this->getJson('/api/public/modules')->assertOk();

    expect($response->json('maintenance_message'))->toBe("We'll be back soon.");
});

test('maintenance_message returns db value when set', function () {
    Setting::set('maintenance_message', 'Down for upgrades.');

    $response = $this->getJson('/api/public/modules')->assertOk();

    expect($response->json('maintenance_message'))->toBe('Down for upgrades.');
});

test('site_name defaults to Peppermint when not set', function () {
    $response = $this->getJson('/api/public/modules')->assertOk();

    expect($response->json('site_name'))->toBe('Peppermint');
});

test('site_name returns db value when set', function () {
    Setting::set('site_name', 'My Awesome Site');

    $response = $this->getJson('/api/public/modules')->assertOk();

    expect($response->json('site_name'))->toBe('My Awesome Site');
});

test('modules endpoint is always reachable even when maintenance is on', function () {
    Setting::set('maintenance_enabled', 'true');

    // /public/modules has no maintenance middleware — frontend must always reach it
    $this->getJson('/api/public/modules')->assertOk();
});
