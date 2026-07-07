<?php

use App\Models\User;
use App\Notifications\AdminResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

// forgot
test('forgot password returns generic success message regardless of email existence', function () {
    Notification::fake();

    $this->postJson('/api/admin/auth/forgot-password', ['email' => 'nobody@example.com'])
        ->assertOk()
        ->assertJson(['message' => 'If that email is registered, a reset link has been sent.']);

    Notification::assertNothingSent();
});

test('forgot password sends reset notification when email is registered', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->postJson('/api/admin/auth/forgot-password', ['email' => $user->email])
        ->assertOk()
        ->assertJson(['message' => 'If that email is registered, a reset link has been sent.']);

    Notification::assertSentTo($user, AdminResetPassword::class);
});

test('forgot password fails without email', function () {
    $this->postJson('/api/admin/auth/forgot-password', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('forgot password fails with invalid email format', function () {
    $this->postJson('/api/admin/auth/forgot-password', ['email' => 'not-an-email'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

// reset
test('reset password with a valid token updates the password', function () {
    $user  = User::factory()->create();
    $token = Password::broker('users')->createToken($user);

    $this->postJson('/api/admin/auth/reset-password', [
        'token'                 => $token,
        'email'                 => $user->email,
        'password'              => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])
        ->assertOk()
        ->assertJson(['message' => 'Password reset successfully.']);

    expect(Hash::check('newpassword1', $user->fresh()->password))->toBeTrue();
});

test('reset password with an invalid token returns 422', function () {
    $user = User::factory()->create();

    $this->postJson('/api/admin/auth/reset-password', [
        'token'                 => 'invalid-token',
        'email'                 => $user->email,
        'password'              => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])
        ->assertUnprocessable()
        ->assertJson(['message' => 'This reset link is invalid or has expired.']);
});

test('reset password fails without token', function () {
    $this->postJson('/api/admin/auth/reset-password', [
        'email'                 => 'admin@example.com',
        'password'              => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['token']);
});

test('reset password fails without email', function () {
    $this->postJson('/api/admin/auth/reset-password', [
        'token'                 => 'some-token',
        'password'              => 'newpassword1',
        'password_confirmation' => 'newpassword1',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

test('reset password fails with password shorter than 8 characters', function () {
    $this->postJson('/api/admin/auth/reset-password', [
        'token'                 => 'some-token',
        'email'                 => 'admin@example.com',
        'password'              => 'short',
        'password_confirmation' => 'short',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});

test('reset password fails when passwords do not match', function () {
    $this->postJson('/api/admin/auth/reset-password', [
        'token'                 => 'some-token',
        'email'                 => 'admin@example.com',
        'password'              => 'newpassword1',
        'password_confirmation' => 'different',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);
});
