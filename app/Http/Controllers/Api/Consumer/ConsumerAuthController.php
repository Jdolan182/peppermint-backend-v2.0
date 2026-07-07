<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class ConsumerAuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!$this->authService->attempt(array_merge($credentials, ['is_active' => true]), 'consumer')) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'user' => $this->authService->user('consumer'),
        ]);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request, 'consumer');

        return response()->json(['message' => 'Logged out']);
    }
}
