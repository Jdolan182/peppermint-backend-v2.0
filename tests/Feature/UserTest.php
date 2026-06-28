<?php

use App\Models\Consumer;
use App\Models\User;

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
