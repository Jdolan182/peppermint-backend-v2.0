<?php

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;

// index
test('authenticated admin can list task types', function () {
    $admin = User::factory()->create();
    TaskType::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/task-types')
        ->assertOk()
        ->assertJsonCount(3);
});

test('task types are returned sorted by sort_order', function () {
    $admin = User::factory()->create();
    $second = TaskType::factory()->create(['name' => 'Bug', 'sort_order' => 2]);
    $first  = TaskType::factory()->create(['name' => 'Feature', 'sort_order' => 1]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/task-types')
        ->assertOk();

    expect($response->json('0.id'))->toBe($first->id)
        ->and($response->json('1.id'))->toBe($second->id);
});

test('unauthenticated request to list task types returns 401', function () {
    $this->getJson('/api/admin/task-types')->assertUnauthorized();
});

// store
test('authenticated admin can create a task type', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/task-types', [
            'name'  => 'Bug',
            'color' => '#ef4444',
        ])
        ->assertCreated()
        ->assertJsonPath('name', 'Bug')
        ->assertJsonPath('color', '#ef4444');
});

test('store task type fails without name', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/task-types', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('unauthenticated request to create task type returns 401', function () {
    $this->postJson('/api/admin/task-types', ['name' => 'Bug'])->assertUnauthorized();
});

// update
test('authenticated admin can update a task type', function () {
    $admin = User::factory()->create();
    $type = TaskType::factory()->create(['name' => 'Old Name']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/task-types/{$type->id}", ['name' => 'New Name'])
        ->assertOk()
        ->assertJsonPath('name', 'New Name');
});

test('unauthenticated request to update task type returns 401', function () {
    $type = TaskType::factory()->create();

    $this->putJson("/api/admin/task-types/{$type->id}", ['name' => 'X'])->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a task type with no tasks', function () {
    $admin = User::factory()->create();
    $type = TaskType::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/task-types/{$type->id}")
        ->assertOk()
        ->assertJson(['message' => 'Deleted']);

    $this->assertDatabaseMissing('task_types', ['id' => $type->id]);
});

test('cannot delete a task type that has tasks', function () {
    $admin  = User::factory()->create();
    $type   = TaskType::factory()->create();
    $status = TaskStatus::factory()->create();
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $status->id]);

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/task-types/{$type->id}")
        ->assertUnprocessable()
        ->assertJson(['message' => 'Cannot delete a type that has tasks.']);
});

test('unauthenticated request to delete task type returns 401', function () {
    $type = TaskType::factory()->create();

    $this->deleteJson("/api/admin/task-types/{$type->id}")->assertUnauthorized();
});
