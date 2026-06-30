<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $settings = Setting::all()->pluck('value', 'key');
        return response()->json($settings);
    }

    public function update(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'site_name'            => ['sometimes', 'nullable', 'string', 'max:100'],
            'maintenance_enabled'  => ['sometimes', 'boolean'],
            'maintenance_message'  => ['sometimes', 'nullable', 'string', 'max:500'],
            'blog_title'           => ['sometimes', 'nullable', 'string', 'max:200'],
            'blog_description'     => ['sometimes', 'nullable', 'string', 'max:500'],
            'module_blogs'         => ['sometimes', 'boolean'],
            'module_consumers'     => ['sometimes', 'boolean'],
            'module_public_login'  => ['sometimes', 'boolean'],
            'module_team'          => ['sometimes', 'boolean'],
            'module_public'        => ['sometimes', 'boolean'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        $settings = Setting::all()->pluck('value', 'key');
        return response()->json($settings);
    }
}
