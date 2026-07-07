<?php

use App\Models\Page;
use App\Models\PageSection;
use App\Models\User;

// index
test('authenticated admin can list pages', function () {
    $admin = User::factory()->create();
    Page::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/pages')
        ->assertOk()
        ->assertJsonStructure(['pages' => [['id', 'title', 'slug', 'children']], 'page_limit']);
});

test('index only returns root pages', function () {
    $admin = User::factory()->create();
    $parent = Page::factory()->create();
    Page::factory()->create(['parent_id' => $parent->id]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/pages')
        ->assertOk();

    expect($response->json('pages'))->toHaveCount(1)
        ->and($response->json('pages.0.id'))->toBe($parent->id);
});

test('index includes page_limit from config', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/pages')
        ->assertOk();

    expect(array_key_exists('page_limit', $response->json()))->toBeTrue();
});

test('store returns 422 when page limit is reached', function () {
    $admin = User::factory()->create();
    config(['peppermint.max_pages' => 1]);
    Page::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/pages', ['title' => 'One Too Many'])
        ->assertUnprocessable()
        ->assertJson(['message' => 'Page limit of 1 reached.']);
});

test('unauthenticated request to list pages returns 401', function () {
    $this->getJson('/api/admin/pages')->assertUnauthorized();
});

// show
test('authenticated admin can view a page with sections', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create(['title' => 'About Us']);
    PageSection::factory()->create(['page_id' => $page->id, 'type' => 'text']);

    $response = $this->actingAs($admin, 'web')
        ->getJson("/api/admin/pages/{$page->id}")
        ->assertOk();

    expect($response->json('title'))->toBe('About Us')
        ->and($response->json('sections'))->toHaveCount(1);
});

test('show returns 404 for non-existent page', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/pages/999999')
        ->assertNotFound();
});

test('unauthenticated request to show page returns 401', function () {
    $page = Page::factory()->create();

    $this->getJson("/api/admin/pages/{$page->id}")->assertUnauthorized();
});

// store
test('authenticated admin can create a page', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/pages', ['title' => 'Contact Us'])
        ->assertCreated()
        ->assertJsonPath('title', 'Contact Us')
        ->assertJsonPath('slug', 'contact-us');
});

test('store auto-generates unique slug when slug is already taken', function () {
    $admin = User::factory()->create();
    Page::factory()->create(['title' => 'Home', 'slug' => 'home']);

    $response = $this->actingAs($admin, 'web')
        ->postJson('/api/admin/pages', ['title' => 'Home'])
        ->assertCreated();

    expect($response->json('slug'))->toBe('home-1');
});

test('store uses provided slug if given', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->postJson('/api/admin/pages', ['title' => 'My Page', 'slug' => 'custom-slug'])
        ->assertCreated();

    expect($response->json('slug'))->toBe('custom-slug');
});

test('store fails with missing title', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/pages', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

test('store rejects a reserved slug', function () {
    $admin = User::factory()->create();

    foreach (['api', 'blogs', 'login', 'register', 'logout', 'preview'] as $reserved) {
        $this->actingAs($admin, 'web')
            ->postJson('/api/admin/pages', ['title' => 'Test Page', 'slug' => $reserved])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }
});

test('store auto-generates a safe slug when title would produce a reserved slug', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->postJson('/api/admin/pages', ['title' => 'Login'])
        ->assertCreated();

    expect($response->json('slug'))->toBe('page');
});

test('unauthenticated request to create page returns 401', function () {
    $this->postJson('/api/admin/pages', ['title' => 'Test'])->assertUnauthorized();
});

// update
test('authenticated admin can update a page', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create(['title' => 'Old Title']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/pages/{$page->id}", ['title' => 'New Title'])
        ->assertOk()
        ->assertJsonPath('title', 'New Title');
});

test('update can toggle published state', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create(['is_published' => false]);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/pages/{$page->id}", ['is_published' => true])
        ->assertOk()
        ->assertJsonPath('is_published', true);
});

test('update rejects a reserved slug', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/pages/{$page->id}", ['slug' => 'register'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['slug']);
});

test('unauthenticated request to update page returns 401', function () {
    $page = Page::factory()->create();

    $this->putJson("/api/admin/pages/{$page->id}", ['title' => 'Updated'])->assertUnauthorized();
});

// preview
test('authenticated admin can preview a page with ordered sections', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create(['title' => 'Preview Me', 'is_published' => false]);
    PageSection::factory()->create(['page_id' => $page->id, 'type' => 'hero', 'order' => 2]);
    PageSection::factory()->create(['page_id' => $page->id, 'type' => 'text', 'order' => 1]);

    $response = $this->actingAs($admin, 'web')
        ->getJson("/api/admin/pages/{$page->id}/preview")
        ->assertOk();

    expect($response->json('title'))->toBe('Preview Me')
        ->and($response->json('sections.0.type'))->toBe('text')
        ->and($response->json('sections.1.type'))->toBe('hero');
});

test('preview works for unpublished pages', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create(['is_published' => false]);

    $this->actingAs($admin, 'web')
        ->getJson("/api/admin/pages/{$page->id}/preview")
        ->assertOk();
});

test('unauthenticated request to preview page returns 401', function () {
    $page = Page::factory()->create();

    $this->getJson("/api/admin/pages/{$page->id}/preview")->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a page', function () {
    $admin = User::factory()->create();
    $page = Page::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/pages/{$page->id}")
        ->assertOk()
        ->assertJson(['message' => 'Page deleted']);

    $this->assertDatabaseMissing('pages', ['id' => $page->id]);
});

test('unauthenticated request to delete page returns 401', function () {
    $page = Page::factory()->create();

    $this->deleteJson("/api/admin/pages/{$page->id}")->assertUnauthorized();
});

// setHome
test('setHome marks the page as home and clears others', function () {
    $admin = User::factory()->create();
    $oldHome = Page::factory()->create(['is_home' => true]);
    $newHome = Page::factory()->create(['is_home' => false]);

    $this->actingAs($admin, 'web')
        ->postJson("/api/admin/pages/{$newHome->id}/home")
        ->assertOk()
        ->assertJsonPath('is_home', true);

    $this->assertDatabaseHas('pages', ['id' => $newHome->id, 'is_home' => true]);
    $this->assertDatabaseHas('pages', ['id' => $oldHome->id, 'is_home' => false]);
});

test('unauthenticated request to setHome returns 401', function () {
    $page = Page::factory()->create();

    $this->postJson("/api/admin/pages/{$page->id}/home")->assertUnauthorized();
});

// saveNavOrder
test('authenticated admin can save nav order', function () {
    $admin = User::factory()->create();
    $pageA = Page::factory()->create(['nav_order' => 1]);
    $pageB = Page::factory()->create(['nav_order' => 2]);

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/pages/nav-order', [
            'items' => [
                ['id' => $pageA->id, 'nav_order' => 2, 'parent_id' => null],
                ['id' => $pageB->id, 'nav_order' => 1, 'parent_id' => null],
            ],
        ])
        ->assertOk()
        ->assertJson(['message' => 'Nav order saved']);

    $this->assertDatabaseHas('pages', ['id' => $pageA->id, 'nav_order' => 2]);
    $this->assertDatabaseHas('pages', ['id' => $pageB->id, 'nav_order' => 1]);
});

test('saveNavOrder can assign a parent', function () {
    $admin = User::factory()->create();
    $parent = Page::factory()->create(['nav_order' => 1]);
    $child = Page::factory()->create(['nav_order' => 1, 'parent_id' => null]);

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/pages/nav-order', [
            'items' => [
                ['id' => $parent->id, 'nav_order' => 1, 'parent_id' => null],
                ['id' => $child->id, 'nav_order' => 1, 'parent_id' => $parent->id],
            ],
        ])
        ->assertOk();

    $this->assertDatabaseHas('pages', ['id' => $child->id, 'parent_id' => $parent->id]);
});

test('saveNavOrder fails when items is missing', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/pages/nav-order', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['items']);
});

test('unauthenticated request to saveNavOrder returns 401', function () {
    $this->putJson('/api/admin/pages/nav-order', ['items' => []])->assertUnauthorized();
});
