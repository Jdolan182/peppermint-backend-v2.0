<?php

use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;

// store
test('authenticated admin can add a section to a page', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson("/api/admin/pages/{$page->id}/sections", [
            'type' => 'hero',
            'data' => ['heading' => 'Welcome'],
        ])
        ->assertCreated()
        ->assertJsonPath('type', 'hero')
        ->assertJsonPath('page_id', $page->id);
});

test('store assigns auto-incremented order to section', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create();
    PageSection::factory()->create(['page_id' => $page->id, 'order' => 1]);

    $response = $this->actingAs($admin, 'web')
        ->postJson("/api/admin/pages/{$page->id}/sections", ['type' => 'text'])
        ->assertCreated();

    expect($response->json('order'))->toBe(2);
});

test('store first section on a page gets order 1', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->postJson("/api/admin/pages/{$page->id}/sections", ['type' => 'text'])
        ->assertCreated();

    expect($response->json('order'))->toBe(1);
});

test('store section fails when type is missing', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson("/api/admin/pages/{$page->id}/sections", [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

test('unauthenticated request to store section returns 401', function () {
    $page = Page::factory()->create();

    $this->postJson("/api/admin/pages/{$page->id}/sections", ['type' => 'text'])->assertUnauthorized();
});

// update
test('authenticated admin can update a section', function () {
    $admin = User::factory()->create();
    $section = PageSection::factory()->create(['data' => ['content' => 'Old content']]);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/sections/{$section->id}", [
            'data' => ['content' => 'New content'],
        ])
        ->assertOk()
        ->assertJsonPath('data.content', 'New content');
});

test('update section fails when data is missing', function () {
    $admin = User::factory()->create();
    $section = PageSection::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/sections/{$section->id}", [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['data']);
});

test('unauthenticated request to update section returns 401', function () {
    $section = PageSection::factory()->create();

    $this->putJson("/api/admin/sections/{$section->id}", ['data' => []])->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a section', function () {
    $admin = User::factory()->create();
    $section = PageSection::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/sections/{$section->id}")
        ->assertOk()
        ->assertJson(['message' => 'Section deleted']);

    $this->assertDatabaseMissing('page_sections', ['id' => $section->id]);
});

test('unauthenticated request to delete section returns 401', function () {
    $section = PageSection::factory()->create();

    $this->deleteJson("/api/admin/sections/{$section->id}")->assertUnauthorized();
});

// saveOrder
test('authenticated admin can save section order', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create();
    $s1 = PageSection::factory()->create(['page_id' => $page->id, 'order' => 1]);
    $s2 = PageSection::factory()->create(['page_id' => $page->id, 'order' => 2]);

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/sections/order', [
            'items' => [
                ['id' => $s1->id, 'order' => 2],
                ['id' => $s2->id, 'order' => 1],
            ],
        ])
        ->assertOk()
        ->assertJson(['message' => 'Order saved']);

    $this->assertDatabaseHas('page_sections', ['id' => $s1->id, 'order' => 2]);
    $this->assertDatabaseHas('page_sections', ['id' => $s2->id, 'order' => 1]);
});

test('saveOrder fails when items is missing', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/sections/order', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['items']);
});

test('unauthenticated request to saveOrder returns 401', function () {
    $this->putJson('/api/admin/sections/order', ['items' => []])->assertUnauthorized();
});
