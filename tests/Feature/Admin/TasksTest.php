<?php

use App\Models\Consumer;
use App\Models\RoadmapItem;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;

// index
test('authenticated admin can list tasks', function () {
    $admin  = User::factory()->create();
    $type   = TaskType::factory()->create();
    $status = TaskStatus::factory()->create();
    Task::factory(3)->create(['type_id' => $type->id, 'status_id' => $status->id]);

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/tasks')
        ->assertOk()
        ->assertJsonCount(3);
});

test('index filters tasks by status_id', function () {
    $admin   = User::factory()->create();
    $type    = TaskType::factory()->create();
    $open    = TaskStatus::factory()->create();
    $closed  = TaskStatus::factory()->create();
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $open->id]);
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $closed->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson("/api/admin/tasks?status_id={$open->id}")
        ->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.status.id'))->toBe($open->id);
});

test('index filters tasks by priority', function () {
    $admin  = User::factory()->create();
    $type   = TaskType::factory()->create();
    $status = TaskStatus::factory()->create();
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $status->id, 'priority' => 'high']);
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $status->id, 'priority' => 'low']);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/tasks?priority=high')
        ->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.priority'))->toBe('high');
});

test('index filters tasks by title search', function () {
    $admin  = User::factory()->create();
    $type   = TaskType::factory()->create();
    $status = TaskStatus::factory()->create();
    Task::factory()->create(['title' => 'Fix login bug', 'type_id' => $type->id, 'status_id' => $status->id]);
    Task::factory()->create(['title' => 'Add dark mode', 'type_id' => $type->id, 'status_id' => $status->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/tasks?search=login')
        ->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.title'))->toBe('Fix login bug');
});

test('index with mine=1 returns only tasks assigned to the authenticated admin', function () {
    $admin  = User::factory()->create();
    $other  = User::factory()->create();
    $type   = TaskType::factory()->create();
    $status = TaskStatus::factory()->create();
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $status->id, 'assigned_admin_id' => $admin->id]);
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $status->id, 'assigned_admin_id' => $other->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/tasks?mine=1')
        ->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.assigned_admin.id'))->toBe($admin->id);
});

test('unauthenticated request to list tasks returns 401', function () {
    $this->getJson('/api/admin/tasks')->assertUnauthorized();
});

// store
test('authenticated admin can create a task', function () {
    $admin  = User::factory()->create();
    $type   = TaskType::factory()->create();
    $status = TaskStatus::factory()->default()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/tasks', [
            'title'   => 'Fix the bug',
            'type_id' => $type->id,
        ])
        ->assertCreated()
        ->assertJsonPath('title', 'Fix the bug')
        ->assertJsonPath('type.id', $type->id);
});

test('store auto-assigns the default status when status_id is omitted', function () {
    $admin   = User::factory()->create();
    $type    = TaskType::factory()->create();
    $default = TaskStatus::factory()->default()->create();

    $response = $this->actingAs($admin, 'web')
        ->postJson('/api/admin/tasks', ['title' => 'New task', 'type_id' => $type->id])
        ->assertCreated();

    expect($response->json('status.id'))->toBe($default->id);
});

test('store records created_by_admin_id as the authenticated admin', function () {
    $admin  = User::factory()->create();
    $type   = TaskType::factory()->create();
    $status = TaskStatus::factory()->default()->create();

    $response = $this->actingAs($admin, 'web')
        ->postJson('/api/admin/tasks', ['title' => 'Task', 'type_id' => $type->id])
        ->assertCreated();

    $this->assertDatabaseHas('tasks', [
        'id'                  => $response->json('id'),
        'created_by_admin_id' => $admin->id,
    ]);
});

test('store task fails without title', function () {
    $admin  = User::factory()->create();
    $type   = TaskType::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/tasks', ['type_id' => $type->id])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

test('store task fails without type_id', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/tasks', ['title' => 'Task'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type_id']);
});

test('store task fails with invalid priority', function () {
    $admin  = User::factory()->create();
    $type   = TaskType::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/tasks', ['title' => 'Task', 'type_id' => $type->id, 'priority' => 'urgent'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['priority']);
});

test('unauthenticated request to create task returns 401', function () {
    $this->postJson('/api/admin/tasks', ['title' => 'Task'])->assertUnauthorized();
});

// show
test('authenticated admin can view a task with all relations', function () {
    $admin  = User::factory()->create();
    $task   = Task::factory()->create(['title' => 'Detail Task']);

    $response = $this->actingAs($admin, 'web')
        ->getJson("/api/admin/tasks/{$task->id}")
        ->assertOk();

    expect($response->json('title'))->toBe('Detail Task')
        ->and($response->json('type'))->not->toBeNull()
        ->and($response->json('status'))->not->toBeNull();
});

test('unauthenticated request to show task returns 401', function () {
    $task = Task::factory()->create();

    $this->getJson("/api/admin/tasks/{$task->id}")->assertUnauthorized();
});

// update
test('authenticated admin can update a task', function () {
    $admin  = User::factory()->create();
    $task   = Task::factory()->create(['title' => 'Old Title', 'priority' => 'low']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/tasks/{$task->id}", ['title' => 'New Title', 'priority' => 'high'])
        ->assertOk()
        ->assertJsonPath('title', 'New Title')
        ->assertJsonPath('priority', 'high');
});

test('unauthenticated request to update task returns 401', function () {
    $task = Task::factory()->create();

    $this->putJson("/api/admin/tasks/{$task->id}", ['title' => 'X'])->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a task', function () {
    $admin = User::factory()->create();
    $task  = Task::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/tasks/{$task->id}")
        ->assertOk()
        ->assertJson(['message' => 'Deleted']);

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('unauthenticated request to delete task returns 401', function () {
    $task = Task::factory()->create();

    $this->deleteJson("/api/admin/tasks/{$task->id}")->assertUnauthorized();
});

test('index filters tasks by type_id', function () {
    $admin  = User::factory()->create();
    $typeA  = TaskType::factory()->create();
    $typeB  = TaskType::factory()->create();
    $status = TaskStatus::factory()->create();
    Task::factory()->create(['type_id' => $typeA->id, 'status_id' => $status->id]);
    Task::factory()->create(['type_id' => $typeB->id, 'status_id' => $status->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson("/api/admin/tasks?type_id={$typeA->id}")
        ->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.type.id'))->toBe($typeA->id);
});

test('index filters tasks by consumer_id', function () {
    $admin    = User::factory()->create();
    $consumer = Consumer::factory()->create();
    $type     = TaskType::factory()->create();
    $status   = TaskStatus::factory()->create();
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $status->id, 'consumer_id' => $consumer->id]);
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $status->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson("/api/admin/tasks?consumer_id={$consumer->id}")
        ->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.consumer.id'))->toBe($consumer->id);
});

test('index filters tasks by roadmap_item_id', function () {
    $admin  = User::factory()->create();
    $item   = RoadmapItem::factory()->create();
    $type   = TaskType::factory()->create();
    $status = TaskStatus::factory()->create();
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $status->id, 'roadmap_item_id' => $item->id]);
    Task::factory()->create(['type_id' => $type->id, 'status_id' => $status->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson("/api/admin/tasks?roadmap_item_id={$item->id}")
        ->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.roadmap_item.id'))->toBe($item->id);
});

test('index with paginate returns a paginated response', function () {
    $admin  = User::factory()->create();
    $type   = TaskType::factory()->create();
    $status = TaskStatus::factory()->create();
    Task::factory(5)->create(['type_id' => $type->id, 'status_id' => $status->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/tasks?paginate=1&per_page=2')
        ->assertOk();

    expect($response->json('total'))->toBe(5)
        ->and($response->json('data'))->toHaveCount(2);
});

test('task update via PATCH works the same as PUT', function () {
    $admin = User::factory()->create();
    $task  = Task::factory()->create(['title' => 'Original']);

    $this->actingAs($admin, 'web')
        ->patchJson("/api/admin/tasks/{$task->id}", ['title' => 'Patched'])
        ->assertOk()
        ->assertJsonPath('title', 'Patched');
});
