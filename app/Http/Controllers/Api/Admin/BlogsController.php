<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\StoreBlogRequest;
use App\Http\Requests\Admin\Blog\UpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogsController extends Controller
{
    public function index(Request $request)
    {
        $blogs = Blog::with(['author', 'categories'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return BlogResource::collection($blogs);
    }

    public function show(Blog $blog)
    {
        return new BlogResource($blog->load(['author', 'categories']));
    }

    public function store(StoreBlogRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = $request->user()->id;
        $validated['slug'] = $this->uniqueSlug($validated['title']);

        $categoryIds = $validated['category_ids'] ?? [];
        unset($validated['category_ids']);

        $blog = Blog::create($validated);
        $blog->categories()->sync($categoryIds);

        return new BlogResource($blog->load(['author', 'categories']));
    }

    public function update(UpdateBlogRequest $request, Blog $blog)
    {
        $validated = $request->validated();

        if ($blog->title !== $validated['title']) {
            $validated['slug'] = $this->uniqueSlug($validated['title'], $blog->id);
        }

        $categoryIds = $validated['category_ids'] ?? [];
        unset($validated['category_ids']);

        $blog->update($validated);
        $blog->categories()->sync($categoryIds);

        return new BlogResource($blog->load(['author', 'categories']));
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();

        return response()->json(['message' => 'Blog deleted']);
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $count = 1;

        while (Blog::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}
