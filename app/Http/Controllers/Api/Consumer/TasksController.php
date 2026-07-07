<?php

namespace App\Http\Controllers\Api\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::with(['type', 'status', 'assignedAdmin', 'roadmapItem'])
            ->where('consumer_id', Auth::guard('consumer')->id())
            ->when($request->status_id, fn($q) => $q->where('status_id', $request->status_id))
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($tasks);
    }

    public function show(Task $task)
    {
        abort_unless($task->consumer_id === Auth::guard('consumer')->id(), 403);

        return response()->json($task->load(['type', 'status', 'assignedAdmin', 'roadmapItem']));
    }
}
