<?php

use App\Models\User;

// index
test('authenticated admin can list users', function () {
    $admin = User::factory()->create();
    User::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/users')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name', 'email', 'created_at']], 'meta', 'links']);
});

test('unauthenticated request to list users returns 401', function () {
    $this->getJson('/api/admin/users')->assertUnauthorized();
});

// store
test('authenticated admin can create a user', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/users', [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => 'password123',
        ])
        ->assertCreated()
        ->assertJsonPath('data.name', 'John Doe')
        ->assertJsonPath('data.email', 'john@example.com');

    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
});

test('store fails with missing name', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/users', ['email' => 'john@example.com', 'password' => 'password123'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('store fails with duplicate email', function () {
    $admin = User::factory()->create();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/users', [
            'name'     => 'John Doe',
            'email'    => 'taken@example.com',
            'password' => 'password123',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('store fails with password shorter than 8 characters', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/users', [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => 'short',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('unauthenticated request to create user returns 401', function () {
    $this->postJson('/api/admin/users', [
        'name'     => 'John Doe',
        'email'    => 'john@example.com',
        'password' => 'password123',
    ])->assertUnauthorized();
});

// update
test('authenticated admin can update a user', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/users/{$user->id}", [
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.email', 'updated@example.com');
});

test('update does not change password when omitted', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $originalHash = $user->password;

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/users/{$user->id}", [
            'name'  => 'Updated Name',
            'email' => $user->email,
        ])
        ->assertOk();

    expect($user->fresh()->password)->toBe($originalHash);
});

test('update allows keeping the same email', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create(['email' => 'same@example.com']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/users/{$user->id}", [
            'name'  => 'New Name',
            'email' => 'same@example.com',
        ])
        ->assertOk();
});

test('update fails with email taken by another user', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/users/{$user->id}", [
            'name'  => 'Updated Name',
            'email' => 'taken@example.com',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('unauthenticated request to update user returns 401', function () {
    $user = User::factory()->create();

    $this->putJson("/api/admin/users/{$user->id}", [
        'name'  => 'Updated Name',
        'email' => 'updated@example.com',
    ])->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a user', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/users/{$user->id}")
        ->assertOk()
        ->assertJson(['message' => 'User deleted']);

    $this->assertSoftDeleted('users', ['id' => $user->id]);
});

test('deleting a non-existent user returns 404', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson('/api/admin/users/999999')
        ->assertNotFound();
});

test('unauthenticated request to delete user returns 401', function () {
    $user = User::factory()->create();

    $this->deleteJson("/api/admin/users/{$user->id}")->assertUnauthorized();
});
