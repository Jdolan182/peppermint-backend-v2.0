<?php

use App\Models\Consumer;
use App\Models\User;

test('consumer can login with valid credentials', function () {
    $consumer = Consumer::factory()->create();

    $this->postJson('/api/consumer/auth/login', [
        'email' => $consumer->email,
        'password' => 'password',
    ])
        ->assertOk()
        ->assertJsonStructure(['user' => ['id', 'name', 'email']]);
});

test('consumer login fails with wrong password', function () {
    $consumer = Consumer::factory()->create();

    $this->postJson('/api/consumer/auth/login', [
        'email' => $consumer->email,
        'password' => 'wrong-password',
    ])
        ->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
});

test('consumer login fails with missing email', function () {
    $this->postJson('/api/consumer/auth/login', [
        'password' => 'password',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('consumer login fails with missing password', function () {
    $this->postJson('/api/consumer/auth/login', [
        'email' => 'test@example.com',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('authenticated consumer can logout', function () {
    $consumer = Consumer::factory()->create();

    $this->actingAs($consumer, 'consumer')
        ->postJson('/api/consumer/auth/logout')
        ->assertOk()
        ->assertJson(['message' => 'Logged out']);
});

test('unauthenticated consumer logout returns 401', function () {
    $this->postJson('/api/consumer/auth/logout')
        ->assertUnauthorized();
});

test('admin credentials cannot be used for consumer login', function () {
    $user = User::factory()->create();

    $this->postJson('/api/consumer/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
});

test('consumer login fails when consumer is inactive', function () {
    $consumer = Consumer::factory()->create(['is_active' => false]);

    $this->postJson('/api/consumer/auth/login', [
        'email'    => $consumer->email,
        'password' => 'password',
    ])
        ->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
});
