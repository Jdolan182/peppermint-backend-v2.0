<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    // Only expose keys that are safe to be public
    private const PUBLIC_KEYS = [
        'blog_title',
        'blog_description',
    ];

    public function index(): \Illuminate\Http\JsonResponse
    {
        $settings = Setting::whereIn('key', self::PUBLIC_KEYS)
            ->pluck('value', 'key');

        return response()->json($settings);
    }
}
