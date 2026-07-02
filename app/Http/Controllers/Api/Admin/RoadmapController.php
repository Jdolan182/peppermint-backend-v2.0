<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoadmapItem;
use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    public function index(Request $request)
    {
        $items = RoadmapItem::with(['category', 'assignedAdmin', 'tasks.status'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->category_id, fn($q) => $q->where('category_id', $request->category_id))
            ->orderBy('sort_order')
            ->orderBy('date')
            ->get();

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'description'       => ['sometimes', 'nullable', 'string'],
            'status'            => ['sometimes', 'in:planned,in-progress,shipped,cancelled'],
            'start_date'        => ['sometimes', 'nullable', 'date'],
            'date'              => ['sometimes', 'nullable', 'date'],
            'category_id'       => ['sometimes', 'nullable', 'exists:roadmap_categories,id'],
            'assigned_admin_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'sort_order'        => ['sometimes', 'integer'],
        ]);

        $item = RoadmapItem::create($data);

        return response()->json($item->load(['category', 'assignedAdmin']), 201);
    }

    public function show(RoadmapItem $roadmapItem)
    {
        return response()->json($roadmapItem->load(['category', 'assignedAdmin', 'tasks.status', 'tasks.type']));
    }

    public function update(Request $request, RoadmapItem $roadmapItem)
    {
        $data = $request->validate([
            'title'             => ['sometimes', 'string', 'max:255'],
            'description'       => ['sometimes', 'nullable', 'string'],
            'status'            => ['sometimes', 'in:planned,in-progress,shipped,cancelled'],
            'start_date'        => ['sometimes', 'nullable', 'date'],
            'date'              => ['sometimes', 'nullable', 'date'],
            'category_id'       => ['sometimes', 'nullable', 'exists:roadmap_categories,id'],
            'assigned_admin_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'sort_order'        => ['sometimes', 'integer'],
        ]);

        $roadmapItem->update($data);

        return response()->json($roadmapItem->fresh()->load(['category', 'assignedAdmin']));
    }

    public function destroy(RoadmapItem $roadmapItem)
    {
        $roadmapItem->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function saveOrder(Request $request)
    {
        $request->validate([
            'items'              => ['required', 'array'],
            'items.*.id'         => ['required', 'exists:roadmap_items,id'],
            'items.*.sort_order' => ['required', 'integer'],
        ]);

        foreach ($request->items as $item) {
            RoadmapItem::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => 'Order saved']);
    }
}
