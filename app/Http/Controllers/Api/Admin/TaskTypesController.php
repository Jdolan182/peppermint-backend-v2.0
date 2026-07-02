<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaskType;
use Illuminate\Http\Request;

class TaskTypesController extends Controller
{
    public function index()
    {
        return response()->json(TaskType::orderBy('sort_order')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:100'],
            'color'          => ['sometimes', 'string', 'max:7'],
            'icon'           => ['sometimes', 'nullable', 'string', 'max:100'],
            'is_appointment' => ['sometimes', 'boolean'],
            'sort_order'     => ['sometimes', 'integer'],
        ]);

        $type = TaskType::create($data);

        return response()->json($type, 201);
    }

    public function update(Request $request, TaskType $taskType)
    {
        $data = $request->validate([
            'name'           => ['sometimes', 'string', 'max:100'],
            'color'          => ['sometimes', 'string', 'max:7'],
            'icon'           => ['sometimes', 'nullable', 'string', 'max:100'],
            'is_appointment' => ['sometimes', 'boolean'],
            'sort_order'     => ['sometimes', 'integer'],
        ]);

        $taskType->update($data);

        return response()->json($taskType);
    }

    public function destroy(TaskType $taskType)
    {
        if ($taskType->tasks()->exists()) {
            return response()->json(['message' => 'Cannot delete a type that has tasks.'], 422);
        }

        $taskType->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
