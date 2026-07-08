<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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

    public function updateAdmin(Request $request)
    {
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password'         => 'nullable|string|min:8|confirmed',
            'current_password' => 'nullable|string',
            'notify_contact'   => 'sometimes|boolean',
        ]);

        $this->requireCurrentPassword($user, $validated);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        if (array_key_exists('notify_contact', $validated)) {
            $user->notify_contact = $validated['notify_contact'];
        }
        $user->save();

        return response()->json(['data' => $user]);
    }

    public function updateConsumer(Request $request)
    {
        $consumer = Auth::guard('consumer')->user();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => ['required', 'email', Rule::unique('consumers', 'email')->ignore($consumer->id)],
            'password'         => 'nullable|string|min:8|confirmed',
            'current_password' => 'nullable|string',
        ]);

        $this->requireCurrentPassword($consumer, $validated);

        $consumer->name  = $validated['name'];
        $consumer->email = $validated['email'];
        if (!empty($validated['password'])) {
            $consumer->password = Hash::make($validated['password']);
        }
        $consumer->save();

        return response()->json(['data' => $consumer]);
    }

    /**
     * Changing email or password requires re-authenticating with the current
     * password, so a hijacked session can't take over the account.
     */
    protected function requireCurrentPassword($user, array $validated): void
    {
        $changingEmail    = $validated['email'] !== $user->email;
        $changingPassword = !empty($validated['password']);

        if (!$changingEmail && !$changingPassword) {
            return;
        }

        if (!Hash::check($validated['current_password'] ?? '', $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Your current password is incorrect.'],
            ]);
        }
    }
}
