<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!$this->authService->attempt($credentials, 'web')) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'user' => $this->authService->user('web'),
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request, 'web');

        return response()->json(['message' => 'Logged out']);
    }
}
