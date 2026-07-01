<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;

class ContactController extends Controller
{
    public function index()
    {
        return response()->json(ContactSubmission::latest()->paginate(25));
    }

    public function unreadCount()
    {
        return response()->json(['count' => ContactSubmission::whereNull('read_at')->count()]);
    }

    public function markRead(ContactSubmission $submission)
    {
        $submission->update(['read_at' => now()]);

        return response()->json($submission);
    }

    public function destroy(ContactSubmission $submission)
    {
        $submission->delete();

        return response()->json(null, 204);
    }
}
