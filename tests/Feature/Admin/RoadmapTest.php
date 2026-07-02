<?php

use App\Models\RoadmapCategory;
use App\Models\RoadmapItem;
use App\Models\User;

// index
test('authenticated admin can list roadmap items', function () {
    $admin = User::factory()->create();
    RoadmapItem::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/roadmap')
        ->assertOk()
        ->assertJsonCount(3);
});

test('index filters roadmap items by status', function () {
    $admin = User::factory()->create();
    RoadmapItem::factory()->create(['status' => 'planned']);
    RoadmapItem::factory()->shipped()->create();

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/roadmap?status=planned')
        ->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.status'))->toBe('planned');
});

test('index filters roadmap items by category_id', function () {
    $admin   = User::factory()->create();
    $billing = RoadmapCategory::factory()->create(['name' => 'Billing']);
    $ui      = RoadmapCategory::factory()->create(['name' => 'UI']);
    RoadmapItem::factory()->create(['category_id' => $billing->id]);
    RoadmapItem::factory()->create(['category_id' => $ui->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson("/api/admin/roadmap?category_id={$billing->id}")
        ->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.category.id'))->toBe($billing->id);
});

test('unauthenticated request to list roadmap items returns 401', function () {
    $this->getJson('/api/admin/roadmap')->assertUnauthorized();
});

// store
test('authenticated admin can create a roadmap item', function () {
    $admin    = User::factory()->create();
    $category = RoadmapCategory::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/roadmap', [
            'title'       => 'Dark Mode',
            'status'      => 'planned',
            'category_id' => $category->id,
            'start_date'  => '2026-09-01',
        ])
        ->assertCreated()
        ->assertJsonPath('title', 'Dark Mode')
        ->assertJsonPath('status', 'planned')
        ->assertJsonPath('category.id', $category->id);
});

test('store roadmap item fails without title', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/roadmap', ['status' => 'planned'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

test('store roadmap item fails with invalid status', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/roadmap', ['title' => 'Feature', 'status' => 'deleted'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('unauthenticated request to create roadmap item returns 401', function () {
    $this->postJson('/api/admin/roadmap', ['title' => 'X'])->assertUnauthorized();
});

// show
test('authenticated admin can view a roadmap item with tasks', function () {
    $admin = User::factory()->create();
    $item  = RoadmapItem::factory()->create(['title' => 'API v2']);

    $response = $this->actingAs($admin, 'web')
        ->getJson("/api/admin/roadmap/{$item->id}")
        ->assertOk();

    expect($response->json('title'))->toBe('API v2')
        ->and($response->json('tasks'))->toBeArray();
});

test('unauthenticated request to show roadmap item returns 401', function () {
    $item = RoadmapItem::factory()->create();

    $this->getJson("/api/admin/roadmap/{$item->id}")->assertUnauthorized();
});

// update
test('authenticated admin can update a roadmap item', function () {
    $admin = User::factory()->create();
    $item  = RoadmapItem::factory()->create(['title' => 'Old Title', 'status' => 'planned']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/roadmap/{$item->id}", ['title' => 'New Title', 'status' => 'in-progress'])
        ->assertOk()
        ->assertJsonPath('title', 'New Title')
        ->assertJsonPath('status', 'in-progress');
});

test('unauthenticated request to update roadmap item returns 401', function () {
    $item = RoadmapItem::factory()->create();

    $this->putJson("/api/admin/roadmap/{$item->id}", ['title' => 'X'])->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a roadmap item', function () {
    $admin = User::factory()->create();
    $item  = RoadmapItem::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/roadmap/{$item->id}")
        ->assertOk()
        ->assertJson(['message' => 'Deleted']);

    $this->assertDatabaseMissing('roadmap_items', ['id' => $item->id]);
});

test('unauthenticated request to delete roadmap item returns 401', function () {
    $item = RoadmapItem::factory()->create();

    $this->deleteJson("/api/admin/roadmap/{$item->id}")->assertUnauthorized();
});

// saveOrder
test('authenticated admin can save roadmap order', function () {
    $admin = User::factory()->create();
    $first  = RoadmapItem::factory()->create(['sort_order' => 1]);
    $second = RoadmapItem::factory()->create(['sort_order' => 2]);

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/roadmap-order', [
            'items' => [
                ['id' => $first->id,  'sort_order' => 2],
                ['id' => $second->id, 'sort_order' => 1],
            ],
        ])
        ->assertOk()
        ->assertJson(['message' => 'Order saved']);

    $this->assertDatabaseHas('roadmap_items', ['id' => $first->id,  'sort_order' => 2]);
    $this->assertDatabaseHas('roadmap_items', ['id' => $second->id, 'sort_order' => 1]);
});

test('saveOrder fails when items is missing', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/roadmap-order', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['items']);
});

test('unauthenticated request to save roadmap order returns 401', function () {
    $this->putJson('/api/admin/roadmap-order', ['items' => []])->assertUnauthorized();
});
