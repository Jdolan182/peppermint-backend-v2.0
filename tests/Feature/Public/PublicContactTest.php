<?php

use App\Models\ContactSubmission;

test('visitor can submit a contact form', function () {
    $this->postJson('/api/public/contact', [
        'name'    => 'Jane Doe',
        'email'   => 'jane@example.com',
        'message' => 'Hello, I have a question.',
    ])
        ->assertCreated()
        ->assertJson(['message' => 'Submission received.']);

    $this->assertDatabaseHas('contact_submissions', [
        'name'  => 'Jane Doe',
        'email' => 'jane@example.com',
    ]);
});

test('submission is stored as unread by default', function () {
    $this->postJson('/api/public/contact', [
        'name'    => 'John Smith',
        'email'   => 'john@example.com',
        'message' => 'A message.',
    ])->assertCreated();

    $this->assertDatabaseHas('contact_submissions', [
        'email'   => 'john@example.com',
        'read_at' => null,
    ]);
});

test('submission accepts an optional page slug', function () {
    $this->postJson('/api/public/contact', [
        'name'      => 'Alice',
        'email'     => 'alice@example.com',
        'message'   => 'Contacting from the about page.',
        'page_slug' => 'about',
    ])
        ->assertCreated();

    $this->assertDatabaseHas('contact_submissions', [
        'email'     => 'alice@example.com',
        'page_slug' => 'about',
    ]);
});

test('submission fails when name is missing', function () {
    $this->postJson('/api/public/contact', [
        'email'   => 'test@example.com',
        'message' => 'Hello.',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('submission fails when email is missing', function () {
    $this->postJson('/api/public/contact', [
        'name'    => 'Test User',
        'message' => 'Hello.',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('submission fails when email is invalid', function () {
    $this->postJson('/api/public/contact', [
        'name'    => 'Test User',
        'email'   => 'not-an-email',
        'message' => 'Hello.',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('submission fails when message is missing', function () {
    $this->postJson('/api/public/contact', [
        'name'  => 'Test User',
        'email' => 'test@example.com',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['message']);
});
