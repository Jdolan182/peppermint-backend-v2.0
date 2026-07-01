<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\Request;

class SectionsController extends Controller
{
    public function store(Request $request, Page $page)
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'max:50'],
            'data' => ['nullable', 'array'],
        ]);

        $order = $page->sections()->max('order') + 1;

        $section = $page->sections()->create([
            'type'  => $data['type'],
            'order' => $order,
            'data'  => $data['data'] ?? [],
        ]);

        return response()->json($section, 201);
    }

    public function update(Request $request, PageSection $section)
    {
        $data = $request->validate([
            'data' => ['required', 'array'],
        ]);

        $section->update(['data' => $data['data']]);

        return response()->json($section->fresh());
    }

    public function destroy(PageSection $section)
    {
        $section->delete();

        return response()->json(['message' => 'Section deleted']);
    }

    public function saveOrder(Request $request)
    {
        $items = $request->validate([
            'items'         => ['required', 'array'],
            'items.*.id'    => ['required', 'integer'],
            'items.*.order' => ['required', 'integer'],
        ])['items'];

        foreach ($items as $item) {
            PageSection::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['message' => 'Order saved']);
    }
}
