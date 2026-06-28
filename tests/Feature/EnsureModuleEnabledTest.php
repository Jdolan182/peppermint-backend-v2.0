<?php

use Illuminate\Support\Facades\Route;

test('middleware passes request through when module is enabled', function () {
    // MODULE_ADMIN_ENABLED=true is set in phpunit.xml
    Route::get('/test-module-enabled', fn () => response()->json(['ok' => true]))
        ->middleware('module:admin');

    $this->getJson('/test-module-enabled')->assertOk();
});

test('middleware returns 404 when module env var is not set', function () {
    // 'nonexistent' maps to MODULE_NONEXISTENT_ENABLED which has no env entry → defaults to false
    Route::get('/test-module-disabled', fn () => response()->json(['ok' => true]))
        ->middleware('module:nonexistent');

    $this->getJson('/test-module-disabled')
        ->assertStatus(404)
        ->assertJson(['message' => 'Module not available']);
});
