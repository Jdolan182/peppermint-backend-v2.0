<?php

use App\Models\Consumer;
use App\Notifications\ConsumerResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

// forgot
test('consumer forgot password returns generic success message regardless of email existence', function () {
    Notification::fake();

    $this->postJson('/api/consumer/auth/forgot-password', ['email' => 'nobody@example.com'])
        ->assertOk()
        ->assertJson(['message' => 'If that email is registered, a reset link has been sent.']);

    Notification::assertNothingSent();
});

test('consumer forgot password sends reset notification when email is registered', function () {
    Notification::fake();
    $consumer = Consumer::factory()->create();

    $this->postJson('/api/consumer/auth/forgot-password', ['email' => $consumer->email])
        ->assertOk()
        ->assertJson(['message' => 'If that email is registered, a reset link has been sent.']);

    Notification::assertSentTo($consumer, ConsumerResetPassword::class);
});

test('consumer forgot password fails without email', function () {
    $this->postJson('/api/consumer/auth/forgot-password', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('consumer forgot password fails with invalid email format', function () {
    $this->postJson('/api/consumer/auth/forgot-password', ['email' => 'not-an-email'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

// reset
test('consumer reset password with a valid token updates the password', function () {
    $consumer = Consumer::factory()->create();
    $token    = Password::broker('consumers')->createToken($consumer);

    $this->postJson('/api/consumer/auth/reset-password', [
        'token'                 => $token,
        'email'                 => $consumer->email,
        'password'              => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])
        ->assertOk()
        ->assertJson(['message' => 'Password reset successfully.']);

    expect(Hash::check('newpassword1', $consumer->fresh()->password))->toBeTrue();
});

test('consumer reset password with an invalid token returns 422', function () {
    $consumer = Consumer::factory()->create();

    $this->postJson('/api/consumer/auth/reset-password', [
        'token'                 => 'invalid-token',
        'email'                 => $consumer->email,
        'password'              => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])
        ->assertUnprocessable()
        ->assertJson(['message' => 'This reset link is invalid or has expired.']);
});

test('consumer reset password fails without token', function () {
    $this->postJson('/api/consumer/auth/reset-password', [
        'email'                 => 'consumer@example.com',
        'password'              => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['token']);
});

test('consumer reset password fails without email', function () {
    $this->postJson('/api/consumer/auth/reset-password', [
        'token'                 => 'some-token',
        'password'              => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('consumer reset password fails with password shorter than 8 characters', function () {
    $this->postJson('/api/consumer/auth/reset-password', [
        'token'                 => 'some-token',
        'email'                 => 'consumer@example.com',
        'password'              => 'short',
        'password_confirmation' => 'short',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('consumer reset password fails when passwords do not match', function () {
    $this->postJson('/api/consumer/auth/reset-password', [
        'token'                 => 'some-token',
        'email'                 => 'consumer@example.com',
        'password'              => 'newpassword1',
        'password_confirmation' => 'different',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});
