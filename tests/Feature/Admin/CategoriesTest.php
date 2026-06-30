<?php

use App\Models\Category;
use App\Models\User;

// index
test('authenticated admin can list categories', function () {
    $admin = User::factory()->create();
    Category::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/categories')
        ->assertOk()
        ->assertJsonStructure(['data' => [['id', 'name', 'slug']], 'meta', 'links']);
});

test('unauthenticated request to list categories returns 401', function () {
    $this->getJson('/api/admin/categories')->assertUnauthorized();
});

// store
test('authenticated admin can create a category', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/categories', ['name' => 'Technology'])
        ->assertCreated()
        ->assertJsonPath('data.name', 'Technology')
        ->assertJsonPath('data.slug', 'technology');

    $this->assertDatabaseHas('categories', ['name' => 'Technology', 'slug' => 'technology']);
});

test('store fails with missing name', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/categories', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('store fails with duplicate category name', function () {
    $admin = User::factory()->create();
    Category::factory()->create(['name' => 'Technology']);

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/categories', ['name' => 'Technology'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('unauthenticated request to create category returns 401', function () {
    $this->postJson('/api/admin/categories', ['name' => 'Technology'])
        ->assertUnauthorized();
});

// update
test('authenticated admin can update a category', function () {
    $admin = User::factory()->create();
    $category = Category::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/categories/{$category->id}", ['name' => 'Updated Name'])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.slug', 'updated-name');
});

test('update allows keeping the same name', function () {
    $admin = User::factory()->create();
    $category = Category::factory()->create(['name' => 'Technology', 'slug' => 'technology']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/categories/{$category->id}", ['name' => 'Technology'])
        ->assertOk();
});

test('update fails with name taken by another category', function () {
    $admin = User::factory()->create();
    $category = Category::factory()->create();
    Category::factory()->create(['name' => 'Taken Name']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/categories/{$category->id}", ['name' => 'Taken Name'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('unauthenticated request to update category returns 401', function () {
    $category = Category::factory()->create();

    $this->putJson("/api/admin/categories/{$category->id}", ['name' => 'New Name'])
        ->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a category', function () {
    $admin = User::factory()->create();
    $category = Category::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/categories/{$category->id}")
        ->assertOk()
        ->assertJson(['message' => 'Category deleted']);

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('unauthenticated request to delete category returns 401', function () {
    $category = Category::factory()->create();

    $this->deleteJson("/api/admin/categories/{$category->id}")->assertUnauthorized();
});
