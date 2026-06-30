<?php

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('get returns the value for an existing key', function () {
    Setting::create(['key' => 'blog_title', 'value' => 'My Blog']);

    expect(Setting::get('blog_title'))->toBe('My Blog');
});

test('get returns null by default when key does not exist', function () {
    expect(Setting::get('nonexistent'))->toBeNull();
});

test('get returns the provided default when key does not exist', function () {
    expect(Setting::get('nonexistent', 'fallback'))->toBe('fallback');
});

test('set creates a new setting', function () {
    Setting::set('blog_title', 'New Blog');

    $this->assertDatabaseHas('settings', ['key' => 'blog_title', 'value' => 'New Blog']);
});

test('set updates an existing setting without creating a duplicate', function () {
    Setting::create(['key' => 'blog_title', 'value' => 'Old Title']);

    Setting::set('blog_title', 'New Title');

    expect(Setting::where('key', 'blog_title')->count())->toBe(1);
    $this->assertDatabaseHas('settings', ['key' => 'blog_title', 'value' => 'New Title']);
});
