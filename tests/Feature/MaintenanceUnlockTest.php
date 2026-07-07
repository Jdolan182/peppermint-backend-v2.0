<?php

test('unlock fails without password field', function () {
    $this->postJson('/api/maintenance/unlock', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('unlock returns 403 when no bypass password is configured', function () {
    config(['peppermint.maintenance_bypass_password' => null]);

    $this->postJson('/api/maintenance/unlock', ['password' => 'anything'])
        ->assertForbidden()
        ->assertJson(['message' => 'Incorrect password.']);
});

test('unlock returns 403 when password is wrong', function () {
    config(['peppermint.maintenance_bypass_password' => 'correct-secret']);

    $this->postJson('/api/maintenance/unlock', ['password' => 'wrong-secret'])
        ->assertForbidden()
        ->assertJson(['message' => 'Incorrect password.']);
});

test('unlock returns a token when password is correct', function () {
    config(['peppermint.maintenance_bypass_password' => 'correct-secret']);

    $response = $this->postJson('/api/maintenance/unlock', ['password' => 'correct-secret'])
        ->assertOk();

    $expected = hash_hmac('sha256', 'maintenance_bypass', config('app.key'));
    expect($response->json('token'))->toBe($expected);
});
