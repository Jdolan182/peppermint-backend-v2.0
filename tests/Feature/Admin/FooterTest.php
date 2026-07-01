<?php

use App\Models\FooterSection;
use App\Models\User;

// index
test('authenticated admin can list footer sections', function () {
    $admin = User::factory()->create();
    FooterSection::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/footer')
        ->assertOk()
        ->assertJsonCount(3);
});

test('footer index returns sections ordered by order', function () {
    $admin = User::factory()->create();
    $second = FooterSection::factory()->create(['order' => 2]);
    $first = FooterSection::factory()->create(['order' => 1]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/footer')
        ->assertOk();

    expect($response->json('0.id'))->toBe($first->id)
        ->and($response->json('1.id'))->toBe($second->id);
});

test('unauthenticated request to list footer sections returns 401', function () {
    $this->getJson('/api/admin/footer')->assertUnauthorized();
});

// store
test('authenticated admin can create a footer section', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/footer/sections', [
            'type' => 'links',
            'data' => ['title' => 'Quick Links'],
        ])
        ->assertCreated()
        ->assertJsonPath('type', 'links')
        ->assertJsonPath('data.title', 'Quick Links');
});

test('store assigns auto-incremented order to footer section', function () {
    $admin = User::factory()->create();
    FooterSection::factory()->create(['order' => 3]);

    $response = $this->actingAs($admin, 'web')
        ->postJson('/api/admin/footer/sections', ['type' => 'text'])
        ->assertCreated();

    expect($response->json('order'))->toBe(4);
});

test('store footer section fails when type is missing', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/footer/sections', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

test('unauthenticated request to create footer section returns 401', function () {
    $this->postJson('/api/admin/footer/sections', ['type' => 'links'])->assertUnauthorized();
});

// update
test('authenticated admin can update a footer section', function () {
    $admin = User::factory()->create();
    $section = FooterSection::factory()->create(['data' => ['title' => 'Old Title']]);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/footer/sections/{$section->id}", [
            'data' => ['title' => 'New Title'],
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'New Title');
});

test('update footer section fails when data is missing', function () {
    $admin = User::factory()->create();
    $section = FooterSection::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/footer/sections/{$section->id}", [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['data']);
});

test('unauthenticated request to update footer section returns 401', function () {
    $section = FooterSection::factory()->create();

    $this->putJson("/api/admin/footer/sections/{$section->id}", ['data' => []])->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a footer section', function () {
    $admin = User::factory()->create();
    $section = FooterSection::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/footer/sections/{$section->id}")
        ->assertOk()
        ->assertJson(['message' => 'Footer section deleted']);

    $this->assertDatabaseMissing('footer_sections', ['id' => $section->id]);
});

test('unauthenticated request to delete footer section returns 401', function () {
    $section = FooterSection::factory()->create();

    $this->deleteJson("/api/admin/footer/sections/{$section->id}")->assertUnauthorized();
});

// saveOrder
test('authenticated admin can save footer section order', function () {
    $admin = User::factory()->create();
    $s1 = FooterSection::factory()->create(['order' => 1]);
    $s2 = FooterSection::factory()->create(['order' => 2]);

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/footer/sections/order', [
            'items' => [
                ['id' => $s1->id, 'order' => 2],
                ['id' => $s2->id, 'order' => 1],
            ],
        ])
        ->assertOk()
        ->assertJson(['message' => 'Order saved']);

    $this->assertDatabaseHas('footer_sections', ['id' => $s1->id, 'order' => 2]);
    $this->assertDatabaseHas('footer_sections', ['id' => $s2->id, 'order' => 1]);
});

test('saveOrder footer fails when items is missing', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/footer/sections/order', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['items']);
});

test('unauthenticated request to saveOrder footer returns 401', function () {
    $this->putJson('/api/admin/footer/sections/order', ['items' => []])->assertUnauthorized();
});
