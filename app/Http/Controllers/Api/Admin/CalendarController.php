<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoadmapItem;
use App\Models\Task;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'from' => ['required', 'date'],
            'to'   => ['required', 'date'],
        ]);

        $from = $request->from;
        $to   = $request->to;

        $tasks = Task::with(['type', 'status', 'assignedAdmin', 'consumer'])
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$from, $to])
            ->get()
            ->map(fn($t) => array_merge($t->toArray(), ['_type' => 'task']));

        $roadmapItems = RoadmapItem::with('assignedAdmin')
            ->whereNotNull('date')
            ->whereBetween('date', [$from, $to])
            ->get()
            ->map(fn($r) => array_merge($r->toArray(), ['_type' => 'roadmap']));

        return response()->json([
            'tasks'         => $tasks,
            'roadmap_items' => $roadmapItems,
        ]);
    }
}
