<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Admin\StoreUserRequest;
use App\Http\Requests\Admin\Admin\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        // The default admin (the developer's support account) is only visible
        // to itself — customer admins don't see it in the team list.
        $users = User::latest()
            ->when(!$request->user()->is_default, fn ($q) => $q->where('is_default', false))
            ->paginate($request->integer('per_page', 15));

        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // Only the default admin may modify its own account — other admins
        // can't change its email/password (or lock the support account out).
        abort_if($user->is_default && $user->id !== Auth::id(), 403, 'This account cannot be modified.');

        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        if (isset($validated['is_active']) && !$validated['is_active'] && $user->id === Auth::id()) {
            abort(422, 'You cannot deactivate your own account.');
        }

        unset($validated['is_default']);

        $user->update($validated);

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        abort_if($user->id === Auth::id(), 422, 'You cannot delete your own account.');
        abort_if($user->is_default, 422, 'The default admin cannot be deleted.');

        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
