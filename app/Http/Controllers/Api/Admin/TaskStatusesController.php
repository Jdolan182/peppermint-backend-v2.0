<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskStatus;
use Illuminate\Http\Request;

class TaskStatusesController extends Controller
{
    public function index()
    {
        return response()->json(TaskStatus::orderBy('sort_order')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'color'      => ['sometimes', 'string', 'max:7'],
            'sort_order' => ['sometimes', 'integer'],
            'is_default' => ['sometimes', 'boolean'],
            'is_closed'  => ['sometimes', 'boolean'],
        ]);

        if (!empty($data['is_default'])) {
            TaskStatus::where('is_default', true)->update(['is_default' => false]);
        }

        $status = TaskStatus::create($data);

        return response()->json($status, 201);
    }

    public function update(Request $request, TaskStatus $taskStatus)
    {
        $data = $request->validate([
            'name'       => ['sometimes', 'string', 'max:100'],
            'color'      => ['sometimes', 'string', 'max:7'],
            'sort_order' => ['sometimes', 'integer'],
            'is_default' => ['sometimes', 'boolean'],
            'is_closed'  => ['sometimes', 'boolean'],
        ]);

        if (!empty($data['is_default'])) {
            TaskStatus::where('id', '!=', $taskStatus->id)->update(['is_default' => false]);
        }

        $taskStatus->update($data);

        return response()->json($taskStatus);
    }

    public function destroy(TaskStatus $taskStatus)
    {
        if ($taskStatus->tasks()->exists()) {
            return response()->json(['message' => 'Cannot delete a status that has tasks.'], 422);
        }

        $taskStatus->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
