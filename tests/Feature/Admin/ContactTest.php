<?php

use App\Models\ContactSubmission;
use App\Models\User;

// index
test('authenticated admin can list contact submissions', function () {
    $admin = User::factory()->create();
    ContactSubmission::factory(3)->create();

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/contact')
        ->assertOk();

    expect($response->json('data'))->toHaveCount(3)
        ->and($response->json('total'))->toBe(3);
});

test('index returns newest submissions first', function () {
    $admin = User::factory()->create();
    $older = ContactSubmission::factory()->create(['created_at' => now()->subDay()]);
    $newer = ContactSubmission::factory()->create(['created_at' => now()]);

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/contact')
        ->assertOk();

    expect($response->json('data.0.id'))->toBe($newer->id);
});

test('unauthenticated request to list contact submissions returns 401', function () {
    $this->getJson('/api/admin/contact')->assertUnauthorized();
});

// unreadCount
test('authenticated admin can get unread submission count', function () {
    $admin = User::factory()->create();
    ContactSubmission::factory(2)->create();
    ContactSubmission::factory()->read()->create();

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/contact/unread-count')
        ->assertOk();

    expect($response->json('count'))->toBe(2);
});

test('unread count is zero when all submissions are read', function () {
    $admin = User::factory()->create();
    ContactSubmission::factory(3)->read()->create();

    $response = $this->actingAs($admin, 'web')
        ->getJson('/api/admin/contact/unread-count')
        ->assertOk();

    expect($response->json('count'))->toBe(0);
});

test('unauthenticated request to unread count returns 401', function () {
    $this->getJson('/api/admin/contact/unread-count')->assertUnauthorized();
});

// markRead
test('authenticated admin can mark a submission as read', function () {
    $admin = User::factory()->create();
    $submission = ContactSubmission::factory()->create(['read_at' => null]);

    $response = $this->actingAs($admin, 'web')
        ->postJson("/api/admin/contact/{$submission->id}/read")
        ->assertOk();

    expect($response->json('read_at'))->not->toBeNull();
    $this->assertDatabaseHas('contact_submissions', [
        'id' => $submission->id,
    ]);
    expect(ContactSubmission::find($submission->id)->read_at)->not->toBeNull();
});

test('markRead is idempotent when already read', function () {
    $admin = User::factory()->create();
    $submission = ContactSubmission::factory()->read()->create();

    $this->actingAs($admin, 'web')
        ->postJson("/api/admin/contact/{$submission->id}/read")
        ->assertOk();
});

test('unauthenticated request to markRead returns 401', function () {
    $submission = ContactSubmission::factory()->create();

    $this->postJson("/api/admin/contact/{$submission->id}/read")->assertUnauthorized();
});

// destroy
test('authenticated admin can delete a contact submission', function () {
    $admin = User::factory()->create();
    $submission = ContactSubmission::factory()->create();

    $this->actingAs($admin, 'web')
        ->deleteJson("/api/admin/contact/{$submission->id}")
        ->assertNoContent();

    $this->assertSoftDeleted('contact_submissions', ['id' => $submission->id]);
});

test('unauthenticated request to delete submission returns 401', function () {
    $submission = ContactSubmission::factory()->create();

    $this->deleteJson("/api/admin/contact/{$submission->id}")->assertUnauthorized();
});
