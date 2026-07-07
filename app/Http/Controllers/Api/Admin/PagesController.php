<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PagesController extends Controller
{
    private const RESERVED_SLUGS = ['api', 'blogs', 'login', 'register', 'logout', 'preview'];

    public function index()
    {
        $pages = Page::whereNull('parent_id')
            ->with('children')
            ->orderBy('nav_order')
            ->get();

        return response()->json([
            'pages'      => $pages,
            'page_limit' => $this->pageLimit(),
        ]);
    }

    public function store(Request $request)
    {
        $limit = $this->pageLimit();
        if ($limit !== null && Page::count() >= $limit) {
            return response()->json(['message' => "Page limit of {$limit} reached."], 422);
        }

        $data = $request->validate([
            'title'            => ['required', 'string', 'max:200'],
            'slug'             => ['nullable', 'string', 'max:200', Rule::notIn(self::RESERVED_SLUGS)],
            'nav_label'        => ['nullable', 'string', 'max:200'],
            'show_in_nav'      => ['boolean'],
            'is_published'     => ['boolean'],
            'show_footer'      => ['boolean'],
            'meta_title'       => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title']);

        return response()->json(Page::create($data), 201);
    }

    public function show(Page $page)
    {
        return response()->json($page->load('sections'));
    }

    public function update(Request $request, Page $page)
    {
        $data = $request->validate([
            'title'            => ['sometimes', 'required', 'string', 'max:200'],
            'slug'             => ['sometimes', 'nullable', 'string', 'max:200', Rule::notIn(self::RESERVED_SLUGS)],
            'nav_label'        => ['sometimes', 'nullable', 'string', 'max:200'],
            'show_in_nav'      => ['sometimes', 'boolean'],
            'nav_order'        => ['sometimes', 'integer'],
            'parent_id'        => ['sometimes', 'nullable', 'exists:pages,id'],
            'is_home'          => ['sometimes', 'boolean'],
            'is_published'     => ['sometimes', 'boolean'],
            'show_footer'      => ['sometimes', 'boolean'],
            'meta_title'       => ['sometimes', 'nullable', 'string', 'max:200'],
            'meta_description' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        if (isset($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $page->id);
        }

        $page->update($data);

        return response()->json($page->fresh());
    }

    public function preview(Page $page)
    {
        return response()->json($page->load(['sections' => fn ($q) => $q->orderBy('order')]));
    }

    public function destroy(Page $page)
    {
        $page->delete();

        return response()->json(['message' => 'Page deleted']);
    }

    public function setHome(Page $page)
    {
        Page::where('is_home', true)->update(['is_home' => false]);
        $page->update(['is_home' => true]);

        return response()->json($page->fresh());
    }

    public function saveNavOrder(Request $request)
    {
        $items = $request->validate([
            'items'              => ['required', 'array'],
            'items.*.id'         => ['required', 'integer'],
            'items.*.nav_order'  => ['required', 'integer'],
            'items.*.parent_id'  => ['nullable', 'integer'],
        ])['items'];

        foreach ($items as $item) {
            Page::where('id', $item['id'])->update([
                'nav_order' => $item['nav_order'],
                'parent_id' => $item['parent_id'] ?? null,
            ]);
        }

        return response()->json(['message' => 'Nav order saved']);
    }

    private function pageLimit(): ?int
    {
        return config('peppermint.max_pages');
    }

    private function uniqueSlug(string $raw, ?int $ignoreId = null): string
    {
        $base = Str::slug($raw);
        if (!$base || in_array($base, self::RESERVED_SLUGS)) {
            $base = 'page';
        }
        $slug = $base;
        $count = 1;

        while (
            Page::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}
