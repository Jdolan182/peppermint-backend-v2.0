<?php

use App\Models\User;

test('admin can login with valid credentials', function () {
    $user = User::factory()->create();

    $this->postJson('/api/admin/auth/login', [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertOk()
        ->assertJsonStructure(['user' => ['id', 'name', 'email']]);
});

test('admin login fails with wrong password', function () {
    $user = User::factory()->create();

    $this->postJson('/api/admin/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])
        ->assertStatus(401)
        ->assertJson(['message' => 'Invalid credentials']);
});

test('admin login fails with missing email', function () {
    $this->postJson('/api/admin/auth/login', [
        'password' => 'password',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('admin login fails with invalid email format', function () {
    $this->postJson('/api/admin/auth/login', [
        'email' => 'not-an-email',
        'password' => 'password',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('admin login fails with missing password', function () {
    $this->postJson('/api/admin/auth/login', [
        'email' => 'test@example.com',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('authenticated admin can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')
        ->postJson('/api/admin/auth/logout')
        ->assertOk()
        ->assertJson(['message' => 'Logged out']);
});

test('unauthenticated admin logout returns 401', function () {
    $this->postJson('/api/admin/auth/logout')
        ->assertUnauthorized();
});
