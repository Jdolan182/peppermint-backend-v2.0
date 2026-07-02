<?php

use App\Models\Consumer;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;

test('authenticated consumer can list their own tasks', function () {
    $consumer = Consumer::factory()->create();
    $type     = TaskType::factory()->create();
    $status   = TaskStatus::factory()->create();
    Task::factory(2)->create(['consumer_id' => $consumer->id, 'type_id' => $type->id, 'status_id' => $status->id]);

    $response = $this->actingAs($consumer, 'consumer')
        ->getJson('/api/consumer/tasks')
        ->assertOk();

    expect($response->json())->toHaveCount(2);
});

test('consumer index does not return other consumers tasks', function () {
    $consumer = Consumer::factory()->create();
    $other    = Consumer::factory()->create();
    $type     = TaskType::factory()->create();
    $status   = TaskStatus::factory()->create();
    Task::factory()->create(['consumer_id' => $consumer->id, 'type_id' => $type->id, 'status_id' => $status->id]);
    Task::factory()->create(['consumer_id' => $other->id,    'type_id' => $type->id, 'status_id' => $status->id]);

    $response = $this->actingAs($consumer, 'consumer')
        ->getJson('/api/consumer/tasks')
        ->assertOk();

    expect($response->json())->toHaveCount(1);
});

test('consumer index filters by status_id', function () {
    $consumer = Consumer::factory()->create();
    $type     = TaskType::factory()->create();
    $open     = TaskStatus::factory()->create();
    $closed   = TaskStatus::factory()->create();
    Task::factory()->create(['consumer_id' => $consumer->id, 'type_id' => $type->id, 'status_id' => $open->id]);
    Task::factory()->create(['consumer_id' => $consumer->id, 'type_id' => $type->id, 'status_id' => $closed->id]);

    $response = $this->actingAs($consumer, 'consumer')
        ->getJson("/api/consumer/tasks?status_id={$open->id}")
        ->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.status.id'))->toBe($open->id);
});

test('unauthenticated request to consumer tasks returns 401', function () {
    $this->getJson('/api/consumer/tasks')->assertUnauthorized();
});

test('authenticated consumer can view their own task', function () {
    $consumer = Consumer::factory()->create();
    $task     = Task::factory()->create(['consumer_id' => $consumer->id]);

    $response = $this->actingAs($consumer, 'consumer')
        ->getJson("/api/consumer/tasks/{$task->id}")
        ->assertOk();

    expect($response->json('id'))->toBe($task->id)
        ->and($response->json('type'))->not->toBeNull()
        ->and($response->json('status'))->not->toBeNull();
});

test('consumer cannot view another consumers task', function () {
    $consumer = Consumer::factory()->create();
    $other    = Consumer::factory()->create();
    $task     = Task::factory()->create(['consumer_id' => $other->id]);

    $this->actingAs($consumer, 'consumer')
        ->getJson("/api/consumer/tasks/{$task->id}")
        ->assertForbidden();
});

test('unauthenticated request to show consumer task returns 401', function () {
    $task = Task::factory()->create();

    $this->getJson("/api/consumer/tasks/{$task->id}")->assertUnauthorized();
});
