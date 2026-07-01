<?php

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// index
test('authenticated admin can list media', function () {
    $admin = User::factory()->create();
    Media::factory(3)->create();

    $this->actingAs($admin, 'web')
        ->getJson('/api/admin/media')
        ->assertOk()
        ->assertJsonCount(3);
});

test('unauthenticated request to list media returns 401', function () {
    $this->getJson('/api/admin/media')->assertUnauthorized();
});

// store
test('authenticated admin can upload a jpeg', function () {
    Storage::fake('public');
    $admin = User::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->postJson('/api/admin/media', ['file' => UploadedFile::fake()->image('photo.jpg')])
        ->assertCreated()
        ->assertJsonPath('filename', 'photo.jpg')
        ->assertJsonPath('disk', 'public');

    Storage::disk('public')->assertExists($response->json('path'));
});

test('authenticated admin can upload a png', function () {
    Storage::fake('public');
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/media', ['file' => UploadedFile::fake()->image('banner.png')])
        ->assertCreated()
        ->assertJsonPath('filename', 'banner.png');
});

test('authenticated admin can upload a gif', function () {
    Storage::fake('public');
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/media', ['file' => UploadedFile::fake()->image('anim.gif')])
        ->assertCreated();
});

test('authenticated admin can upload a webp', function () {
    Storage::fake('public');
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/media', ['file' => UploadedFile::fake()->image('thumb.webp')])
        ->assertCreated();
});

test('store records the correct mime type and size', function () {
    Storage::fake('public');
    $admin = User::factory()->create();

    $response = $this->actingAs($admin, 'web')
        ->postJson('/api/admin/media', ['file' => UploadedFile::fake()->image('photo.jpg', 100, 100)])
        ->assertCreated();

    expect($response->json('mime_type'))->not->toBeEmpty()
        ->and($response->json('size'))->toBeGreaterThan(0);
});

test('store rejects pdf files', function () {
    Storage::fake('public');
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/media', ['file' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf')])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['file']);
});

test('store rejects files over 10mb', function () {
    Storage::fake('public');
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/media', ['file' => UploadedFile::fake()->image('huge.jpg')->size(11000)])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['file']);
});

test('store fails when no file is provided', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin, 'web')
        ->postJson('/api/admin/media', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['file']);
});

test('unauthenticated request to upload media returns 401', function () {
    Storage::fake('public');

    $this->postJson('/api/admin/media', ['file' => UploadedFile::fake()->image('photo.jpg')])
        ->assertUnauthorized();
});

// update
test('authenticated admin can update media alt text', function () {
    $admin = User::factory()->create();
    $media = Media::factory()->create(['alt' => null]);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/media/{$media->id}", ['alt' => 'A scenic view'])
        ->assertOk()
        ->assertJsonPath('alt', 'A scenic view');
});

test('update can clear alt text', function () {
    $admin = User::factory()->create();
    $media = Media::factory()->create(['alt' => 'Old alt']);

    $this->actingAs($admin, 'web')
        ->putJson("/api/admin/media/{$media->id}", ['alt' => null])
        ->assertOk()
        ->assertJsonPath('alt', null);
});

test('unauthenticated request to update media returns 401', function () {
    $media = Media::factory()->create();

    $this->putJson("/api/admin/media/{$media->id}", ['alt' => 'Alt text'])->assertUnauthorized();
});

// destroy
test('authenticated admin can delete media and its file', function () {
    Storage::fake('public');
    $admin = User::factory()->create();
    $file = UploadedFile::fake()->image('to-delete.jpg');
    $path = $file->store('media', 'public');

    $media = Media::factory()->create(['path' => $path, 'disk' => 'public']);

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/media/{$media->id}")
        ->assertOk()
        ->assertJson(['message' => 'Media deleted']);

    $this->assertDatabaseMissing('media', ['id' => $media->id]);
    Storage::disk('public')->assertMissing($path);
});

test('unauthenticated request to delete media returns 401', function () {
    $media = Media::factory()->create();

    $this->deleteJson("/api/admin/media/{$media->id}")->assertUnauthorized();
});
