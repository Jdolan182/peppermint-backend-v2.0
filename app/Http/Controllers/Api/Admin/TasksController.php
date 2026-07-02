<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['type', 'status', 'assignedAdmin', 'consumer', 'roadmapItem'])
            ->when($request->status_id, fn($q) => $q->where('status_id', $request->status_id))
            ->when($request->type_id,   fn($q) => $q->where('type_id', $request->type_id))
            ->when($request->priority,  fn($q) => $q->where('priority', $request->priority))
            ->when($request->assigned_admin_id, fn($q) => $q->where('assigned_admin_id', $request->assigned_admin_id))
            ->when($request->consumer_id, fn($q) => $q->where('consumer_id', $request->consumer_id))
            ->when($request->roadmap_item_id, fn($q) => $q->where('roadmap_item_id', $request->roadmap_item_id))
            ->when($request->search, fn($q) => $q->where('title', 'like', '%' . $request->search . '%'))
            ->when($request->mine, fn($q) => $q->where('assigned_admin_id', Auth::id()))
            ->orderBy('due_date')
            ->orderByDesc('created_at');

        if ($request->paginate) {
            return response()->json($query->paginate($request->integer('per_page', 50)));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'                  => ['required', 'string', 'max:255'],
            'description'            => ['sometimes', 'nullable', 'string'],
            'type_id'                => ['required', 'exists:task_types,id'],
            'status_id'              => ['sometimes', 'exists:task_statuses,id'],
            'priority'               => ['sometimes', 'in:low,medium,high,critical'],
            'due_date'               => ['sometimes', 'nullable', 'date'],
            'assigned_admin_id'      => ['sometimes', 'nullable', 'exists:users,id'],
            'consumer_id'            => ['sometimes', 'nullable', 'exists:consumers,id'],
            'roadmap_item_id'        => ['sometimes', 'nullable', 'exists:roadmap_items,id'],
            'notes'                  => ['sometimes', 'nullable', 'string'],
        ]);

        if (empty($data['status_id'])) {
            $default = TaskStatus::where('is_default', true)->first();
            $data['status_id'] = $default?->id ?? TaskStatus::orderBy('sort_order')->value('id');
        }

        $data['created_by_admin_id'] = Auth::id();

        $task = Task::create($data);

        return response()->json($task->load(['type', 'status', 'assignedAdmin', 'consumer', 'roadmapItem']), 201);
    }

    public function show(Task $task)
    {
        return response()->json($task->load(['type', 'status', 'assignedAdmin', 'consumer', 'roadmapItem', 'createdByAdmin', 'createdByConsumer']));
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'title'             => ['sometimes', 'string', 'max:255'],
            'description'       => ['sometimes', 'nullable', 'string'],
            'type_id'           => ['sometimes', 'exists:task_types,id'],
            'status_id'         => ['sometimes', 'exists:task_statuses,id'],
            'priority'          => ['sometimes', 'in:low,medium,high,critical'],
            'due_date'          => ['sometimes', 'nullable', 'date'],
            'assigned_admin_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'consumer_id'       => ['sometimes', 'nullable', 'exists:consumers,id'],
            'roadmap_item_id'   => ['sometimes', 'nullable', 'exists:roadmap_items,id'],
            'notes'             => ['sometimes', 'nullable', 'string'],
        ]);

        $task->update($data);

        return response()->json($task->fresh()->load(['type', 'status', 'assignedAdmin', 'consumer', 'roadmapItem']));
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
