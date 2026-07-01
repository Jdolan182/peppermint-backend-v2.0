<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PagesController extends Controller
{
    public function nav()
    {
        $pages = Page::where('is_published', true)
            ->where('show_in_nav', true)
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_published', true)->where('show_in_nav', true)->orderBy('nav_order')])
            ->orderBy('nav_order')
            ->get(['id', 'title', 'slug', 'nav_label', 'nav_order', 'parent_id']);

        return response()->json($pages);
    }

    public function home()
    {
        $page = Page::where('is_home', true)->where('is_published', true)
            ->with(['sections' => fn ($q) => $q->orderBy('order')])
            ->first();

        if (!$page) {
            return response()->json(null);
        }

        return response()->json($page);
    }

    public function show(string $slug)
    {
        $page = Page::where('slug', $slug)->where('is_published', true)
            ->with(['sections' => fn ($q) => $q->orderBy('order')])
            ->firstOrFail();

        return response()->json($page);
    }
}
