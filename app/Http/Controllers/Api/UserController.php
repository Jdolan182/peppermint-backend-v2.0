<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    public function getUser(Request $request)
    {
        return response()->json([
            'data' => $this->authService->user('web'),
        ]);
    }

    public function getConsumer(Request $request)
    {
        return response()->json([
            'data' => $this->authService->user('consumer'),
        ]);
    }
}
