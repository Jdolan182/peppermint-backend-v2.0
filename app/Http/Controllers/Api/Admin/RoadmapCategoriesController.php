<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoadmapCategory;
use Illuminate\Http\Request;

class RoadmapCategoriesController extends Controller
{
    public function index()
    {
        return response()->json(RoadmapCategory::orderBy('sort_order')->orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'color'      => 'required|string|max:20',
            'sort_order' => 'integer',
        ]);

        return response()->json(RoadmapCategory::create($data), 201);
    }

    public function update(Request $request, RoadmapCategory $roadmapCategory)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'color'      => 'required|string|max:20',
            'sort_order' => 'integer',
        ]);

        $roadmapCategory->update($data);

        return response()->json($roadmapCategory);
    }

    public function destroy(RoadmapCategory $roadmapCategory)
    {
        if ($roadmapCategory->items()->exists()) {
            return response()->json(['message' => 'Cannot delete a category that has roadmap items.'], 422);
        }

        $roadmapCategory->delete();

        return response()->json(null, 204);
    }
}
