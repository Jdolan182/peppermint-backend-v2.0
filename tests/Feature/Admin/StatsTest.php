<?php

use App\Models\RoadmapItem;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;

test('unauthenticated request to stats returns 401', function () {
    $this->getJson('/api/admin/stats')->assertUnauthorized();
});

test('stats returns five labelled entries', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/stats')
        ->assertOk();

    expect($response->json())->toHaveCount(5)
        ->and(collect($response->json())->pluck('label')->all())->toEqual([
            'Open tasks', 'Assigned to me', 'Overdue', 'Completed', 'Roadmap in flight',
        ]);
});

test('open tasks count excludes tasks in a closed status', function () {
    $admin  = User::factory()->create();
    $open   = TaskStatus::factory()->create(['is_closed' => false]);
    $closed = TaskStatus::factory()->closed()->create();
    Task::factory(2)->create(['status_id' => $open->id]);
    Task::factory()->create(['status_id' => $closed->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/stats')
        ->assertOk();

    $stat = collect($response->json())->firstWhere('label', 'Open tasks');
    expect($stat['value'])->toBe(2);
});

test('assigned to me counts open tasks assigned to the authenticated admin', function () {
    $admin = User::factory()->create();
    $other = User::factory()->create();
    $open  = TaskStatus::factory()->create(['is_closed' => false]);
    Task::factory(2)->create(['status_id' => $open->id, 'assigned_admin_id' => $admin->id]);
    Task::factory()->create(['status_id' => $open->id,  'assigned_admin_id' => $other->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/stats')
        ->assertOk();

    $stat = collect($response->json())->firstWhere('label', 'Assigned to me');
    expect($stat['value'])->toBe(2);
});

test('overdue counts open tasks with a past due date', function () {
    $admin  = User::factory()->create();
    $open   = TaskStatus::factory()->create(['is_closed' => false]);
    $closed = TaskStatus::factory()->closed()->create();
    Task::factory()->create(['status_id' => $open->id,   'due_date' => now()->subDay()]);
    Task::factory()->create(['status_id' => $open->id,   'due_date' => now()->addDay()]);
    Task::factory()->create(['status_id' => $closed->id, 'due_date' => now()->subDay()]);
    Task::factory()->create(['status_id' => $open->id,   'due_date' => null]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/stats')
        ->assertOk();

    $stat = collect($response->json())->firstWhere('label', 'Overdue');
    expect($stat['value'])->toBe(1);
});

test('completed counts tasks in a closed status', function () {
    $admin  = User::factory()->create();
    $open   = TaskStatus::factory()->create(['is_closed' => false]);
    $closed = TaskStatus::factory()->closed()->create();
    Task::factory(3)->create(['status_id' => $closed->id]);
    Task::factory()->create(['status_id' => $open->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/stats')
        ->assertOk();

    $stat = collect($response->json())->firstWhere('label', 'Completed');
    expect($stat['value'])->toBe(3);
});

test('roadmap in flight counts roadmap items with in-progress status', function () {
    $admin = User::factory()->create();
    RoadmapItem::factory()->inProgress()->create();
    RoadmapItem::factory()->inProgress()->create();
    RoadmapItem::factory()->create(['status' => 'planned']);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/stats')
        ->assertOk();

    $stat = collect($response->json())->firstWhere('label', 'Roadmap in flight');
    expect($stat['value'])->toBe(2);
});
