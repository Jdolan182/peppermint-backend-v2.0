<?php

use App\Models\Consumer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('authenticated admin can get their user data', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')
        ->getJson('/api/admin/user')
        ->assertOk()
        ->assertJsonStructure(['data' => ['id', 'name', 'email']])
        ->assertJsonPath('data.id', $user->id);
});

test('unauthenticated request to admin user endpoint returns 401', function () {
    $this->getJson('/api/admin/user')
        ->assertUnauthorized();
});

test('authenticated consumer can get their user data', function () {
    $consumer = Consumer::factory()->create();

    $this->actingAs($consumer, 'consumer')
        ->getJson('/api/consumer/user')
        ->assertOk()
        ->assertJsonStructure(['data' => ['id', 'name', 'email']])
        ->assertJsonPath('data.id', $consumer->id);
});

test('unauthenticated request to consumer user endpoint returns 401', function () {
    $this->getJson('/api/consumer/user')
        ->assertUnauthorized();
});

// updateAdmin
test('authenticated admin can update their name and email', function () {
    $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);

    $this->actingAs($user, 'web')
        ->putJson('/api/admin/user', ['name' => 'New Name', 'email' => 'new@example.com'])
        ->assertOk()
        ->assertJsonPath('data.name', 'New Name')
        ->assertJsonPath('data.email', 'new@example.com');
});

test('updateAdmin fails without name', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')
        ->putJson('/api/admin/user', ['email' => 'x@example.com'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('updateAdmin fails without email', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')
        ->putJson('/api/admin/user', ['name' => 'Jordan'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('updateAdmin fails when email is taken by another user', function () {
    $user  = User::factory()->create();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($user, 'web')
        ->putJson('/api/admin/user', ['name' => 'Jordan', 'email' => 'taken@example.com'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('authenticated admin can change their password', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')
        ->putJson('/api/admin/user', [
            'name'                  => $user->name,
            'email'                 => $user->email,
            'password'              => 'newpassword',
            'password_confirmation' => 'newpassword',
        ])
        ->assertOk();

    expect(Hash::check('newpassword', $user->fresh()->password))->toBeTrue();
});

test('updateAdmin fails with password shorter than 8 characters', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'web')
        ->putJson('/api/admin/user', [
            'name'                  => $user->name,
            'email'                 => $user->email,
            'password'              => 'short',
            'password_confirmation' => 'short',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('unauthenticated request to update admin user returns 401', function () {
    $this->putJson('/api/admin/user', ['name' => 'X', 'email' => 'x@x.com'])->assertUnauthorized();
});

// updateConsumer
test('authenticated consumer can update their name and email', function () {
    $consumer = Consumer::factory()->create(['name' => 'Old Name', 'email' => 'old2@example.com']);

    $this->actingAs($consumer, 'consumer')
        ->putJson('/api/consumer/user', ['name' => 'New Name', 'email' => 'new2@example.com'])
        ->assertOk()
        ->assertJsonPath('data.name', 'New Name')
        ->assertJsonPath('data.email', 'new2@example.com');
});

test('updateConsumer fails without name', function () {
    $consumer = Consumer::factory()->create();

    $this->actingAs($consumer, 'consumer')
        ->putJson('/api/consumer/user', ['email' => 'x@example.com'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('updateConsumer fails without email', function () {
    $consumer = Consumer::factory()->create();

    $this->actingAs($consumer, 'consumer')
        ->putJson('/api/consumer/user', ['name' => 'Jordan'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('updateConsumer fails when email is taken by another consumer', function () {
    $consumer = Consumer::factory()->create();
    Consumer::factory()->create(['email' => 'taken2@example.com']);

    $this->actingAs($consumer, 'consumer')
        ->putJson('/api/consumer/user', ['name' => 'Jordan', 'email' => 'taken2@example.com'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('authenticated consumer can change their password', function () {
    $consumer = Consumer::factory()->create();

    $this->actingAs($consumer, 'consumer')
        ->putJson('/api/consumer/user', [
            'name'                  => $consumer->name,
            'email'                 => $consumer->email,
            'password'              => 'newpassword',
            'password_confirmation' => 'newpassword',
        ])
        ->assertOk();

    expect(Hash::check('newpassword', $consumer->fresh()->password))->toBeTrue();
});

test('updateConsumer fails with password shorter than 8 characters', function () {
    $consumer = Consumer::factory()->create();

    $this->actingAs($consumer, 'consumer')
        ->putJson('/api/consumer/user', [
            'name'                  => $consumer->name,
            'email'                 => $consumer->email,
            'password'              => 'short',
            'password_confirmation' => 'short',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('unauthenticated request to update consumer user returns 401', function () {
    $this->putJson('/api/consumer/user', ['name' => 'X', 'email' => 'x@x.com'])->assertUnauthorized();
});
