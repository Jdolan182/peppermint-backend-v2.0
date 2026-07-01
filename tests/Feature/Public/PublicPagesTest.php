<?php

use App\Models\Page;
use App\Models\PageSection;

// nav
test('nav returns published pages that are set to show in nav', function () {
    Page::factory()->inNav()->create(['title' => 'Visible Page']);
    Page::factory()->create(['title' => 'Hidden Page', 'show_in_nav' => false, 'is_published' => true]);

    $response = $this->getJson('/api/public/pages/nav')->assertOk();

    $titles = collect($response->json())->pluck('title');
    expect($titles)->toContain('Visible Page')
        ->and($titles)->not->toContain('Hidden Page');
});

test('nav excludes unpublished pages', function () {
    Page::factory()->create(['title' => 'Draft Page', 'show_in_nav' => true, 'is_published' => false]);

    $response = $this->getJson('/api/public/pages/nav')->assertOk();

    expect($response->json())->toBeEmpty();
});

test('nav only returns root pages', function () {
    $parent = Page::factory()->inNav()->create(['nav_order' => 1]);
    Page::factory()->inNav()->create(['parent_id' => $parent->id, 'nav_order' => 1]);

    $response = $this->getJson('/api/public/pages/nav')->assertOk();

    expect($response->json())->toHaveCount(1)
        ->and($response->json('0.id'))->toBe($parent->id);
});

test('nav includes published children nested under their parent', function () {
    $parent = Page::factory()->inNav()->create();
    $child = Page::factory()->inNav()->create(['parent_id' => $parent->id]);

    $response = $this->getJson('/api/public/pages/nav')->assertOk();

    expect($response->json('0.children'))->toHaveCount(1)
        ->and($response->json('0.children.0.id'))->toBe($child->id);
});

test('nav children exclude unpublished pages', function () {
    $parent = Page::factory()->inNav()->create();
    Page::factory()->create(['parent_id' => $parent->id, 'show_in_nav' => true, 'is_published' => false]);

    $response = $this->getJson('/api/public/pages/nav')->assertOk();

    expect($response->json('0.children'))->toBeEmpty();
});

// home
test('home returns the page marked as home with sections', function () {
    $page = Page::factory()->home()->create(['title' => 'Home']);
    PageSection::factory()->create(['page_id' => $page->id, 'type' => 'hero']);

    $response = $this->getJson('/api/public/pages/home')->assertOk();

    expect($response->json('title'))->toBe('Home')
        ->and($response->json('sections'))->toHaveCount(1);
});

test('home returns null when no home page is set', function () {
    Page::factory()->published()->create(['is_home' => false]);

    $this->getJson('/api/public/pages/home')
        ->assertOk()
        ->assertExactJson([]);
});

test('home returns null when home page exists but is not published', function () {
    Page::factory()->create(['is_home' => true, 'is_published' => false]);

    $this->getJson('/api/public/pages/home')
        ->assertOk()
        ->assertExactJson([]);
});

// show
test('show returns a published page by slug with sections', function () {
    $page = Page::factory()->published()->create(['title' => 'About', 'slug' => 'about']);
    PageSection::factory()->create(['page_id' => $page->id]);

    $response = $this->getJson('/api/public/pages/about')->assertOk();

    expect($response->json('title'))->toBe('About')
        ->and($response->json('sections'))->toHaveCount(1);
});

test('show returns 404 for a slug that does not exist', function () {
    $this->getJson('/api/public/pages/does-not-exist')->assertNotFound();
});

test('show returns 404 for an unpublished page', function () {
    Page::factory()->create(['slug' => 'secret', 'is_published' => false]);

    $this->getJson('/api/public/pages/secret')->assertNotFound();
});
