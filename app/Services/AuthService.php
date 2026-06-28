<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function attempt(array $credentials, string $guard): bool
    {
        return Auth::guard($guard)->attempt($credentials);
    }

    public function logout(Request $request, string $guard): void
    {
        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function user(string $guard): ?Authenticatable
    {
        return Auth::guard($guard)->user();
    }
}
