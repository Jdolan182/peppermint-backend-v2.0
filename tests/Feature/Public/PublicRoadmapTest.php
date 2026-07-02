<?php

use App\Models\RoadmapItem;

test('public roadmap returns planned items', function () {
    RoadmapItem::factory()->create(['title' => 'Planned Feature', 'status' => 'planned']);

    $response = $this->getJson('/api/public/roadmap')->assertOk();

    expect(collect($response->json())->pluck('title'))->toContain('Planned Feature');
});

test('public roadmap returns in-progress items', function () {
    RoadmapItem::factory()->inProgress()->create(['title' => 'In Progress Feature']);

    $response = $this->getJson('/api/public/roadmap')->assertOk();

    expect(collect($response->json())->pluck('title'))->toContain('In Progress Feature');
});

test('public roadmap returns shipped items', function () {
    RoadmapItem::factory()->shipped()->create(['title' => 'Shipped Feature']);

    $response = $this->getJson('/api/public/roadmap')->assertOk();

    expect(collect($response->json())->pluck('title'))->toContain('Shipped Feature');
});

test('public roadmap excludes cancelled items', function () {
    RoadmapItem::factory()->cancelled()->create(['title' => 'Cancelled Feature']);
    RoadmapItem::factory()->create(['title' => 'Planned Feature']);

    $response = $this->getJson('/api/public/roadmap')->assertOk();

    $titles = collect($response->json())->pluck('title');
    expect($titles)->not->toContain('Cancelled Feature')
        ->and($titles)->toContain('Planned Feature');
});

test('public roadmap does not expose sensitive fields', function () {
    RoadmapItem::factory()->create();

    $response = $this->getJson('/api/public/roadmap')->assertOk();

    $item = $response->json('0');
    expect($item)->toHaveKeys(['id', 'title', 'description', 'status', 'start_date', 'date', 'category_id', 'sort_order', 'category'])
        ->and(array_key_exists('assigned_admin_id', $item))->toBeFalse();
});

test('public roadmap items are ordered by sort_order then date', function () {
    RoadmapItem::factory()->create(['title' => 'Second', 'sort_order' => 2, 'date' => null]);
    RoadmapItem::factory()->create(['title' => 'First',  'sort_order' => 1, 'date' => null]);

    $response = $this->getJson('/api/public/roadmap')->assertOk();

    expect($response->json('0.title'))->toBe('First')
        ->and($response->json('1.title'))->toBe('Second');
});
