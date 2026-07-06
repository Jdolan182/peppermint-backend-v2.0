<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\RoadmapItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $openTasks = Task::whereHas('status', fn($q) => $q->where('is_closed', false))->count();

        $overdue = Task::whereHas('status', fn($q) => $q->where('is_closed', false))
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->count();

        $myOpen = Task::where('assigned_admin_id', Auth::id())
            ->whereHas('status', fn($q) => $q->where('is_closed', false))
            ->count();

        $inProgress = RoadmapItem::where('status', 'in-progress')->count();

        $completed = Task::whereHas('status', fn($q) => $q->where('is_closed', true))->count();

        return response()->json([
            ['label' => 'Open tasks',       'value' => $openTasks],
            ['label' => 'Assigned to me',   'value' => $myOpen],
            ['label' => 'Overdue',          'value' => $overdue],
            ['label' => 'Completed',        'value' => $completed],
            ['label' => 'Roadmap in flight','value' => $inProgress],
        ]);
    }
}
