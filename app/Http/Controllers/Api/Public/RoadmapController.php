<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\RoadmapItem;

class RoadmapController extends Controller
{
    public function index()
    {
        $items = RoadmapItem::with('category')
            ->whereIn('status', ['planned', 'in-progress', 'shipped'])
            ->orderBy('sort_order')
            ->orderBy('date')
            ->get(['id', 'title', 'description', 'status', 'start_date', 'date', 'category_id', 'sort_order']);

        return response()->json($items);
    }
}
