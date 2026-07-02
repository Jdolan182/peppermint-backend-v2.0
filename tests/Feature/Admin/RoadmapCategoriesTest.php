<?php

use App\Models\RoadmapCategory;
use App\Models\RoadmapItem;
use App\Models\User;

// index
test('authenticated admin can list roadmap categories', function () {
    $admin = User::factory()->create();
    RoadmapCategory::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/roadmap-categories')
        ->assertOk()
        ->assertJsonCount(3);
});

test('roadmap categories are returned sorted by sort_order then name', function () {
    $admin  = User::factory()->create();
    $second = RoadmapCategory::factory()->create(['name' => 'Beta',  'sort_order' => 2]);
    $first  = RoadmapCategory::factory()->create(['name' => 'Alpha', 'sort_order' => 1]);
    $third  = RoadmapCategory::factory()->create(['name' => 'Gamma', 'sort_order' => 2]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/roadmap-categories')
        ->assertOk();

    expect($response->json('0.id'))->toBe($first->id)
        ->and($response->json('1.name'))->toBe('Beta')
        ->and($response->json('2.name'))->toBe('Gamma');
});

test('unauthenticated request to list roadmap categories returns 401', function () {
    $this->getJson('/api/admin/roadmap-categories')->assertUnauthorized();
});

// store
test('authenticated admin can create a roadmap category', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/roadmap-categories', [
            'name'       => 'Billing',
            'color'      => '#10b981',
            'sort_order' => 1,
        ])
        ->assertCreated()
        ->assertJsonPath('name', 'Billing')
        ->assertJsonPath('color', '#10b981');
});

test('store roadmap category fails without name', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/roadmap-categories', ['color' => '#ff0000'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('store roadmap category fails without color', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/roadmap-categories', ['name' => 'UI'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['color']);
});

test('unauthenticated request to create roadmap category returns 401', function () {
    $this->postJson('/api/admin/roadmap-categories', ['name' => 'X', 'color' => '#fff'])->assertUnauthorized();
});

// update
test('authenticated admin can update a roadmap category', function () {
    $admin    = User::factory()->create();
    $category = RoadmapCategory::factory()->create(['name' => 'Old Name', 'color' => '#aaaaaa']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/roadmap-categories/{$category->id}", [
            'name'  => 'New Name',
            'color' => '#bbbbbb',
        ])
        ->assertOk()
        ->assertJsonPath('name', 'New Name')
        ->assertJsonPath('color', '#bbbbbb');
});

test('update roadmap category fails without name', function () {
    $admin    = User::factory()->create();
    $category = RoadmapCategory::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/roadmap-categories/{$category->id}", ['color' => '#ff0000'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('update roadmap category fails without color', function () {
    $admin    = User::factory()->create();
    $category = RoadmapCategory::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/roadmap-categories/{$category->id}", ['name' => 'UI'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['color']);
});

test('unauthenticated request to update roadmap category returns 401', function () {
    $category = RoadmapCategory::factory()->create();

    $this->putJson("/api/admin/roadmap-categories/{$category->id}", ['name' => 'X', 'color' => '#fff'])
        ->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a roadmap category with no items', function () {
    $admin    = User::factory()->create();
    $category = RoadmapCategory::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/roadmap-categories/{$category->id}")
        ->assertNoContent();

    $this->assertDatabaseMissing('roadmap_categories', ['id' => $category->id]);
});

test('cannot delete a roadmap category that has roadmap items', function () {
    $admin    = User::factory()->create();
    $category = RoadmapCategory::factory()->create();
    RoadmapItem::factory()->create(['category_id' => $category->id]);

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/roadmap-categories/{$category->id}")
        ->assertUnprocessable()
        ->assertJson(['message' => 'Cannot delete a category that has roadmap items.']);
});

test('unauthenticated request to delete roadmap category returns 401', function () {
    $category = RoadmapCategory::factory()->create();

    $this->deleteJson("/api/admin/roadmap-categories/{$category->id}")->assertUnauthorized();
});
