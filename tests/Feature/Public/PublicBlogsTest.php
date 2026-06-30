<?php

use App\Models\Blog;
use App\Models\Category;
use App\Models\User;

// index
test('index returns only published blogs', function () {
    Blog::factory()->published()->create(['title' => 'Published Blog']);
    Blog::factory()->create(['title' => 'Draft Blog']);

    $response = $this->getJson('/api/public/blogs')->assertOk();

    $titles = collect($response->json('data'))->pluck('title');
    expect($titles)->toContain('Published Blog')
        ->and($titles)->not->toContain('Draft Blog');
});

test('index excludes blogs with a future published_at', function () {
    Blog::factory()->published()->create(['title' => 'Live Blog']);
    Blog::factory()->create(['title' => 'Future Blog', 'published_at' => now()->addDay()]);

    $response = $this->getJson('/api/public/blogs')->assertOk();

    $titles = collect($response->json('data'))->pluck('title');
    expect($titles)->toContain('Live Blog')
        ->and($titles)->not->toContain('Future Blog');
});

test('index filters blogs by category slug', function () {
    $category = Category::factory()->create(['slug' => 'tech']);
    $inCategory = Blog::factory()->published()->create();
    Blog::factory()->published()->create();

    $inCategory->categories()->attach($category);

    $response = $this->getJson('/api/public/blogs?category=tech')->assertOk();

    expect($response->json('data'))->toHaveCount(1)
        ->and($response->json('data.0.id'))->toBe($inCategory->id);
});

test('index response includes a list of all categories', function () {
    Category::factory(3)->create();

    $response = $this->getJson('/api/public/blogs')->assertOk();

    expect($response->json('categories'))->toHaveCount(3);
});

test('index response contains correct meta structure', function () {
    $response = $this->getJson('/api/public/blogs')->assertOk();

    expect($response->json('meta'))->toHaveKeys(['current_page', 'last_page', 'total', 'from', 'to']);
});

// show
test('show returns a published blog by slug', function () {
    Blog::factory()->published()->create(['slug' => 'my-published-blog']);

    $this->getJson('/api/public/blogs/my-published-blog')
        ->assertOk()
        ->assertJsonPath('data.slug', 'my-published-blog');
});

test('show returns 404 for an unpublished blog', function () {
    Blog::factory()->create(['slug' => 'my-draft-blog']);

    $this->getJson('/api/public/blogs/my-draft-blog')->assertNotFound();
});

test('show returns 404 for a future-dated blog', function () {
    Blog::factory()->create([
        'slug'         => 'future-blog',
        'published_at' => now()->addDay(),
    ]);

    $this->getJson('/api/public/blogs/future-blog')->assertNotFound();
});

test('show returns 404 for a non-existent slug', function () {
    $this->getJson('/api/public/blogs/does-not-exist')->assertNotFound();
});
