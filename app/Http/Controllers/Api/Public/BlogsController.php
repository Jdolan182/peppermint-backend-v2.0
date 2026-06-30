<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Setting;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;

class BlogsController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with(['author', 'categories'])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at');

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $request->category));
        }

        $blogs = $query->paginate($request->integer('per_page', 12));
        $categories = Category::orderBy('name')->get();

        return response()->json([
            'data'       => BlogResource::collection($blogs)->resolve(),
            'meta'       => [
                'current_page' => $blogs->currentPage(),
                'last_page'    => $blogs->lastPage(),
                'from'         => $blogs->firstItem(),
                'to'           => $blogs->lastItem(),
                'total'        => $blogs->total(),
            ],
            'categories' => CategoryResource::collection($categories),
            'settings'   => [
                'blog_title'       => Setting::get('blog_title', 'Our Blog'),
                'blog_description' => Setting::get('blog_description', ''),
            ],
        ]);
    }

    public function show(string $slug)
    {
        $blog = Blog::with(['author', 'categories'])
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('slug', $slug)
            ->firstOrFail();

        return new BlogResource($blog);
    }
}
