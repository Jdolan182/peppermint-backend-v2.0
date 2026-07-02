<?php

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;

// index
test('authenticated admin can list task statuses', function () {
    $admin = User::factory()->create();
    TaskStatus::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/task-statuses')
        ->assertOk()
        ->assertJsonCount(3);
});

test('task statuses are returned sorted by sort_order', function () {
    $admin  = User::factory()->create();
    $second = TaskStatus::factory()->create(['sort_order' => 2]);
    $first  = TaskStatus::factory()->create(['sort_order' => 1]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/task-statuses')
        ->assertOk();

    expect($response->json('0.id'))->toBe($first->id)
        ->and($response->json('1.id'))->toBe($second->id);
});

test('unauthenticated request to list task statuses returns 401', function () {
    $this->getJson('/api/admin/task-statuses')->assertUnauthorized();
});

// store
test('authenticated admin can create a task status', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/task-statuses', ['name' => 'Open', 'color' => '#10b981'])
        ->assertCreated()
        ->assertJsonPath('name', 'Open')
        ->assertJsonPath('color', '#10b981');
});

test('store task status fails without name', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/task-statuses', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('creating a default status clears the previous default', function () {
    $admin   = User::factory()->create();
    $oldDefault = TaskStatus::factory()->default()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/task-statuses', ['name' => 'New Default', 'is_default' => true])
        ->assertCreated();

    $this->assertDatabaseHas('task_statuses', ['id' => $oldDefault->id, 'is_default' => false]);
});

test('unauthenticated request to create task status returns 401', function () {
    $this->postJson('/api/admin/task-statuses', ['name' => 'Open'])->assertUnauthorized();
});

// update
test('authenticated admin can update a task status', function () {
    $admin  = User::factory()->create();
    $status = TaskStatus::factory()->create(['name' => 'Old']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/task-statuses/{$status->id}", ['name' => 'New'])
        ->assertOk()
        ->assertJsonPath('name', 'New');
});

test('setting is_default on update clears other defaults', function () {
    $admin      = User::factory()->create();
    $oldDefault = TaskStatus::factory()->default()->create();
    $newDefault = TaskStatus::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/task-statuses/{$newDefault->id}", ['is_default' => true])
        ->assertOk();

    $this->assertDatabaseHas('task_statuses', ['id' => $oldDefault->id, 'is_default' => false]);
    $this->assertDatabaseHas('task_statuses', ['id' => $newDefault->id, 'is_default' => true]);
});

test('unauthenticated request to update task status returns 401', function () {
    $status = TaskStatus::factory()->create();

    $this->putJson("/api/admin/task-statuses/{$status->id}", ['name' => 'X'])->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a task status with no tasks', function () {
    $admin  = User::factory()->create();
    $status = TaskStatus::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/task-statuses/{$status->id}")
        ->assertOk()
        ->assertJson(['message' => 'Deleted']);

    $this->assertDatabaseMissing('task_statuses', ['id' => $status->id]);
});

test('cannot delete a task status that has tasks', function () {
    $admin  = User::factory()->create();
    $type   = TaskType::factory()->create();
    $status = TaskStatus::factory()->create();
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $status->id]);

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/task-statuses/{$status->id}")
        ->assertUnprocessable()
        ->assertJson(['message' => 'Cannot delete a status that has tasks.']);
});

test('unauthenticated request to delete task status returns 401', function () {
    $status = TaskStatus::factory()->create();

    $this->deleteJson("/api/admin/task-statuses/{$status->id}")->assertUnauthorized();
});
