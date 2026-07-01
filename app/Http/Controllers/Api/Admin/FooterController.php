<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterSection;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    public function index()
    {
        return response()->json(FooterSection::orderBy('order')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'max:50'],
            'data' => ['nullable', 'array'],
        ]);

        $order = FooterSection::max('order') + 1;

        $section = FooterSection::create([
            'type'  => $data['type'],
            'order' => $order,
            'data'  => $data['data'] ?? [],
        ]);

        return response()->json($section, 201);
    }

    public function update(Request $request, FooterSection $footerSection)
    {
        $data = $request->validate([
            'data' => ['required', 'array'],
        ]);

        $footerSection->update(['data' => $data['data']]);

        return response()->json($footerSection->fresh());
    }

    public function destroy(FooterSection $footerSection)
    {
        $footerSection->delete();

        return response()->json(['message' => 'Footer section deleted']);
    }

    public function saveOrder(Request $request)
    {
        $items = $request->validate([
            'items'         => ['required', 'array'],
            'items.*.id'    => ['required', 'integer'],
            'items.*.order' => ['required', 'integer'],
        ])['items'];

        foreach ($items as $item) {
            FooterSection::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json(['message' => 'Order saved']);
    }
}
