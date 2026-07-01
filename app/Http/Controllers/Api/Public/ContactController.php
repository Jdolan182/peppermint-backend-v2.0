<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
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

        ContactSubmission::create($data);

        return response()->json(['message' => 'Submission received.'], 201);
    }
}
