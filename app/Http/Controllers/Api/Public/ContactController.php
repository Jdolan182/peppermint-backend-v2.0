<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Models\User;
use App\Notifications\ContactSubmissionReceived;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255'],
            'message'   => ['required', 'string', 'max:5000'],
            'page_slug' => ['nullable', 'string', 'max:255'],
        ]);

        $submission = ContactSubmission::create($data);

        User::where('notify_contact', true)
            ->where('is_active', true)
            ->each(fn(User $admin) => $admin->notify(new ContactSubmissionReceived($submission)));

        return response()->json(['message' => 'Submission received.'], 201);
    }
}
