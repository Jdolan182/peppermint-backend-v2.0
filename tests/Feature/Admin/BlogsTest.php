<?php

use App\Models\Blog;
use App\Models\Category;
use App\Models\User;

// index
test('authenticated admin can list blogs', function () {
    $admin = User::factory()->create();
    Blog::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/blogs')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'title', 'slug', 'author', 'categories']], 'meta', 'links']);
});

test('unauthenticated request to list blogs returns 401', function () {
    $this->getJson('/api/admin/blogs')->assertUnauthorized();
});

// show
test('authenticated admin can view any blog including drafts', function () {
    $admin = User::factory()->create();
    $blog = Blog::factory()->create(['title' => 'Draft Blog']);

    $this->actingAs($admin, 'web')
        ->getJson("/api/admin/blogs/{$blog->id}")
        ->assertOk()
        ->assertJsonPath('data.title', 'Draft Blog');
});

test('show returns 404 for non-existent blog', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/blogs/999999')
        ->assertNotFound();
});

test('unauthenticated request to show blog returns 401', function () {
    $blog = Blog::factory()->create();

    $this->getJson("/api/admin/blogs/{$blog->id}")->assertUnauthorized();
});

// store
test('authenticated admin can create a blog', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/blogs', [
            'title'   => 'My Test Blog',
            'content' => 'Some content here.',
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'My Test Blog')
        ->assertJsonPath('data.slug', 'my-test-blog')
        ->assertJsonPath('data.author.id', $admin->id);
});

test('store auto-generates a unique slug when title is already taken', function () {
    $admin = User::factory()->create();
    Blog::factory()->create(['title' => 'My Blog Title', 'slug' => 'my-blog-title']);

    $response = $this->actingAs($admin, 'web')
        ->postJson('/api/admin/blogs', [
            'title'   => 'My Blog Title',
            'content' => 'Some content.',
        ])
        ->assertCreated();

    expect($response->json('data.slug'))->toBe('my-blog-title-1');
});

test('store associates blog with categories', function () {
    $admin = User::factory()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->postJson('/api/admin/blogs', [
            'title'        => 'Categorised Blog',
            'content'      => 'Content.',
            'category_ids' => [$category->id],
        ])
        ->assertCreated();

    expect($response->json('data.categories'))->toHaveCount(1)
        ->and($response->json('data.categories.0.id'))->toBe($category->id);
});

test('store fails with missing title', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/blogs', ['content' => 'Some content.'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

test('store fails with missing content', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/blogs', ['title' => 'My Blog'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['content']);
});

test('store fails with non-existent category id', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/blogs', [
            'title'        => 'My Blog',
            'content'      => 'Content.',
            'category_ids' => [999999],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['category_ids.0']);
});

test('unauthenticated request to create blog returns 401', function () {
    $this->postJson('/api/admin/blogs', [
        'title'   => 'My Blog',
        'content' => 'Content.',
    ])->assertUnauthorized();
});

// update
test('authenticated admin can update a blog', function () {
    $admin = User::factory()->create();
    $blog = Blog::factory()->create(['user_id' => $admin->id]);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/blogs/{$blog->id}", [
            'title'   => 'Updated Title',
            'content' => 'Updated content.',
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Updated Title');
});

test('update regenerates slug when title changes', function () {
    $admin = User::factory()->create();
    $blog = Blog::factory()->create([
        'title' => 'Original Title',
        'slug'  => 'original-title',
        'user_id' => $admin->id,
    ]);

    $response = $this->actingAs($admin, 'web')
        ->putJson("/api/admin/blogs/{$blog->id}", [
            'title'   => 'Brand New Title',
            'content' => 'Content.',
        ])
        ->assertOk();

    expect($response->json('data.slug'))->toBe('brand-new-title');
});

test('update keeps existing slug when title is unchanged', function () {
    $admin = User::factory()->create();
    $blog = Blog::factory()->create([
        'title' => 'Same Title',
        'slug'  => 'same-title',
        'user_id' => $admin->id,
    ]);

    $response = $this->actingAs($admin, 'web')
        ->putJson("/api/admin/blogs/{$blog->id}", [
            'title'   => 'Same Title',
            'content' => 'Updated content only.',
        ])
        ->assertOk();

    expect($response->json('data.slug'))->toBe('same-title');
});

test('update syncs categories', function () {
    $admin = User::factory()->create();
    $blog = Blog::factory()->create(['user_id' => $admin->id]);
    $cat1 = Category::factory()->create();
    $cat2 = Category::factory()->create();
    $blog->categories()->attach($cat1);

    $response = $this->actingAs($admin, 'web')
        ->putJson("/api/admin/blogs/{$blog->id}", [
            'title'        => $blog->title,
            'content'      => $blog->content,
            'category_ids' => [$cat2->id],
        ])
        ->assertOk();

    expect($response->json('data.categories'))->toHaveCount(1)
        ->and($response->json('data.categories.0.id'))->toBe($cat2->id);
});

test('unauthenticated request to update blog returns 401', function () {
    $blog = Blog::factory()->create();

    $this->putJson("/api/admin/blogs/{$blog->id}", [
        'title'   => 'Updated',
        'content' => 'Content.',
    ])->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a blog', function () {
    $admin = User::factory()->create();
    $blog = Blog::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/blogs/{$blog->id}")
        ->assertOk()
        ->assertJson(['message' => 'Blog deleted']);

    $this->assertSoftDeleted('blogs', ['id' => $blog->id]);
});

test('unauthenticated request to delete blog returns 401', function () {
    $blog = Blog::factory()->create();

    $this->deleteJson("/api/admin/blogs/{$blog->id}")->assertUnauthorized();
});
