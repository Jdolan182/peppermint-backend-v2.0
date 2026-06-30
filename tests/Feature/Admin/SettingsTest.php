<?php

use App\Models\Setting;
use App\Models\User;

// index
test('authenticated admin can get all settings', function () {
    $admin = User::factory()->create();
    Setting::set('blog_title', 'My Blog');
    Setting::set('blog_description', 'A nice blog');

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/settings')
        ->assertOk()
        ->assertJson([
            'blog_title'       => 'My Blog',
            'blog_description' => 'A nice blog',
        ]);
});

test('index returns empty object when no settings exist', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/settings')
        ->assertOk()
        ->assertExactJson([]);
});

test('unauthenticated request to get settings returns 401', function () {
    $this->getJson('/api/admin/settings')->assertUnauthorized();
});

// update
test('authenticated admin can update blog_title', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->putJson('/api/admin/settings', ['blog_title' => 'Updated Title'])
        ->assertOk();

    expect($response->json('blog_title'))->toBe('Updated Title');
    expect(Setting::get('blog_title'))->toBe('Updated Title');
});

test('authenticated admin can update blog_description', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->putJson('/api/admin/settings', ['blog_description' => 'A great description'])
        ->assertOk();

    expect($response->json('blog_description'))->toBe('A great description');
});

test('update can set multiple settings at once', function () {
    $admin = User::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->putJson('/api/admin/settings', [
            'blog_title'       => 'My Blog',
            'blog_description' => 'A description',
        ])
        ->assertOk();

    expect($response->json('blog_title'))->toBe('My Blog')
        ->and($response->json('blog_description'))->toBe('A description');
});

test('update overwrites an existing setting', function () {
    $admin = User::factory()->create();
    Setting::set('blog_title', 'Old Title');

    $response = $this->actingAs($admin, 'web')
        ->putJson('/api/admin/settings', ['blog_title' => 'New Title'])
        ->assertOk();

    expect($response->json('blog_title'))->toBe('New Title');
});

test('update fails when blog_title exceeds 200 characters', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/settings', ['blog_title' => str_repeat('a', 201)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['blog_title']);
});

test('update fails when blog_description exceeds 500 characters', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->putJson('/api/admin/settings', ['blog_description' => str_repeat('a', 501)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['blog_description']);
});

test('unauthenticated request to update settings returns 401', function () {
    $this->putJson('/api/admin/settings', ['blog_title' => 'Title'])->assertUnauthorized();
});
