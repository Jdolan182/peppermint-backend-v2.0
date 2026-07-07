<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ConsumerPasswordController extends Controller
{
    public function forgot(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        Password::broker('consumers')->sendResetLink(['email' => $request->email]);

        return response()->json(['message' => 'If that email is registered, a reset link has been sent.']);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $status = Password::broker('consumers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully.']);
        }

        return response()->json(['message' => 'This reset link is invalid or has expired.'], 422);
    }
}
