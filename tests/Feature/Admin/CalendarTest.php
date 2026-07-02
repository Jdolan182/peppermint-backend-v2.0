<?php

use App\Models\RoadmapItem;
use App\Models\Task;
use App\Models\User;

test('authenticated admin can view calendar events within a date range', function () {
    $admin  = User::factory()->create();
    Task::factory()->create(['due_date' => '2026-08-15']);
    RoadmapItem::factory()->create(['date' => '2026-08-20']);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/calendar?from=2026-08-01&to=2026-08-31')
        ->assertOk();

    expect($response->json('tasks'))->toHaveCount(1)
        ->and($response->json('roadmap_items'))->toHaveCount(1);
});

test('calendar excludes tasks outside the date range', function () {
    $admin = User::factory()->create();
    Task::factory()->create(['due_date' => '2026-07-10']);
    Task::factory()->create(['due_date' => '2026-08-15']);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/calendar?from=2026-08-01&to=2026-08-31')
        ->assertOk();

    expect($response->json('tasks'))->toHaveCount(1)
        ->and($response->json('tasks.0.due_date'))->toStartWith('2026-08-15');
});

test('calendar excludes tasks with no due date', function () {
    $admin = User::factory()->create();
    Task::factory()->create(['due_date' => null]);
    Task::factory()->create(['due_date' => '2026-08-10']);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/calendar?from=2026-08-01&to=2026-08-31')
        ->assertOk();

    expect($response->json('tasks'))->toHaveCount(1);
});

test('calendar excludes roadmap items outside the date range', function () {
    $admin = User::factory()->create();
    RoadmapItem::factory()->create(['date' => '2026-06-01']);
    RoadmapItem::factory()->create(['date' => '2026-08-05']);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/calendar?from=2026-08-01&to=2026-08-31')
        ->assertOk();

    expect($response->json('roadmap_items'))->toHaveCount(1);
});

test('calendar response has a _type field to distinguish entries', function () {
    $admin = User::factory()->create();
    Task::factory()->create(['due_date' => '2026-08-10']);
    RoadmapItem::factory()->create(['date' => '2026-08-10']);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/calendar?from=2026-08-01&to=2026-08-31')
        ->assertOk();

    expect($response->json('tasks.0._type'))->toBe('task')
        ->and($response->json('roadmap_items.0._type'))->toBe('roadmap');
});

test('calendar requires from date', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/calendar?to=2026-08-31')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['from']);
});

test('calendar requires to date', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/calendar?from=2026-08-01')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['to']);
});

test('unauthenticated request to calendar returns 401', function () {
    $this->getJson('/api/admin/calendar?from=2026-08-01&to=2026-08-31')->assertUnauthorized();
});
