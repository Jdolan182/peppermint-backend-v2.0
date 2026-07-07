<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function unlock(Request $request)
    {
        $request->validate(['password' => ['required', 'string']]);

        $expected = config('peppermint.maintenance_bypass_password');

        if (!$expected || $request->password !== $expected) {
            return response()->json(['message' => 'Incorrect password.'], 403);
        }

        return response()->json([
            'token' => hash_hmac('sha256', 'maintenance_bypass', config('app.key')),
        ]);
    }
}
