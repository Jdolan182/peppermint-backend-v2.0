<?php

use App\Models\Consumer;
use App\Models\User;

// index
test('authenticated admin can list consumers', function () {
    $admin = User::factory()->create();
    Consumer::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/consumers')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name', 'email', 'created_at']], 'meta', 'links']);
});

test('unauthenticated request to list consumers returns 401', function () {
    $this->getJson('/api/admin/consumers')->assertUnauthorized();
});

// store
test('authenticated admin can create a consumer', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/consumers', [
            'name'                  => 'Jane Doe',
            'email'                 => 'jane@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Jane Doe')
        ->assertJsonPath('data.email', 'jane@example.com');

    $this->assertDatabaseHas('consumers', ['email' => 'jane@example.com']);
});

test('store fails with missing name', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/consumers', ['email' => 'jane@example.com', 'password' => 'password123'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('store fails with duplicate consumer email', function () {
    $admin = User::factory()->create();
    Consumer::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/consumers', [
            'name'     => 'Jane Doe',
            'email'    => 'taken@example.com',
            'password' => 'password123',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('store fails with password shorter than 8 characters', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/consumers', [
            'name'     => 'Jane Doe',
            'email'    => 'jane@example.com',
            'password' => 'short',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('unauthenticated request to create consumer returns 401', function () {
    $this->postJson('/api/admin/consumers', [
        'name'     => 'Jane Doe',
        'email'    => 'jane@example.com',
        'password' => 'password123',
    ])->assertUnauthorized();
});

// update
test('authenticated admin can update a consumer', function () {
    $admin = User::factory()->create();
    $consumer = Consumer::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/consumers/{$consumer->id}", [
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.email', 'updated@example.com');
});

test('update does not change password when omitted', function () {
    $admin = User::factory()->create();
    $consumer = Consumer::factory()->create();
    $originalHash = $consumer->password;

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/consumers/{$consumer->id}", [
            'name'  => 'Updated Name',
            'email' => $consumer->email,
        ])
        ->assertOk();

    expect($consumer->fresh()->password)->toBe($originalHash);
});

test('update allows keeping the same email', function () {
    $admin = User::factory()->create();
    $consumer = Consumer::factory()->create(['email' => 'same@example.com']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/consumers/{$consumer->id}", [
            'name'  => 'New Name',
            'email' => 'same@example.com',
        ])
        ->assertOk();
});

test('update fails with email taken by another consumer', function () {
    $admin = User::factory()->create();
    $consumer = Consumer::factory()->create();
    Consumer::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/consumers/{$consumer->id}", [
            'name'  => 'Updated Name',
            'email' => 'taken@example.com',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('unauthenticated request to update consumer returns 401', function () {
    $consumer = Consumer::factory()->create();

    $this->putJson("/api/admin/consumers/{$consumer->id}", [
        'name'  => 'Updated Name',
        'email' => 'updated@example.com',
    ])->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a consumer', function () {
    $admin = User::factory()->create();
    $consumer = Consumer::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/consumers/{$consumer->id}")
        ->assertOk()
        ->assertJson(['message' => 'Consumer deleted']);

    $this->assertSoftDeleted('consumers', ['id' => $consumer->id]);
});

test('deleting a non-existent consumer returns 404', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson('/api/admin/consumers/999999')
        ->assertNotFound();
});

test('unauthenticated request to delete consumer returns 401', function () {
    $consumer = Consumer::factory()->create();

    $this->deleteJson("/api/admin/consumers/{$consumer->id}")->assertUnauthorized();
});

test('update can activate and deactivate a consumer', function () {
    $admin    = User::factory()->create();
    $consumer = Consumer::factory()->create(['is_active' => true]);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/consumers/{$consumer->id}", [
            'name'      => $consumer->name,
            'email'     => $consumer->email,
            'is_active' => false,
        ])
        ->assertOk()
        ->assertJsonPath('data.is_active', false);
});
