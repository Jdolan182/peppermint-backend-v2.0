<?php

use App\Services\AuthService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

test('attempt returns true when credentials are valid', function () {
    $guard = Mockery::mock();
    $guard->shouldReceive('attempt')
        ->with(['email' => 'test@example.com', 'password' => 'password'])
        ->andReturn(true);

    Auth::shouldReceive('guard')->with('web')->andReturn($guard);

    expect((new AuthService())->attempt(['email' => 'test@example.com', 'password' => 'password'], 'web'))
        ->toBeTrue();
});

test('attempt returns false when credentials are invalid', function () {
    $guard = Mockery::mock();
    $guard->shouldReceive('attempt')
        ->with(['email' => 'test@example.com', 'password' => 'wrong'])
        ->andReturn(false);

    Auth::shouldReceive('guard')->with('web')->andReturn($guard);

    expect((new AuthService())->attempt(['email' => 'test@example.com', 'password' => 'wrong'], 'web'))
        ->toBeFalse();
});

test('logout calls guard logout and invalidates session', function () {
    $guard = Mockery::mock();
    $guard->shouldReceive('logout')->once();

    Auth::shouldReceive('guard')->with('web')->andReturn($guard);

    $session = Mockery::mock();
    $session->shouldReceive('invalidate')->once();
    $session->shouldReceive('regenerateToken')->once();

    $request = Mockery::mock(Request::class);
    $request->shouldReceive('session')->andReturn($session);

    (new AuthService())->logout($request, 'web');
});

test('user returns the authenticated user from the guard', function () {
    $authenticatable = Mockery::mock(Authenticatable::class);

    $guard = Mockery::mock();
    $guard->shouldReceive('user')->andReturn($authenticatable);

    Auth::shouldReceive('guard')->with('web')->andReturn($guard);

    expect((new AuthService())->user('web'))->toBe($authenticatable);
});

test('user returns null when no user is authenticated', function () {
    $guard = Mockery::mock();
    $guard->shouldReceive('user')->andReturn(null);

    Auth::shouldReceive('guard')->with('consumer')->andReturn($guard);

    expect((new AuthService())->user('consumer'))->toBeNull();
});
