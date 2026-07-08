<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Consumer\StoreConsumerRequest;
use App\Http\Requests\Admin\Consumer\UpdateConsumerRequest;
use App\Http\Resources\ConsumerResource;
use App\Models\Consumer;
use Illuminate\Http\Request;

class ConsumersController extends Controller
{
    public function index(Request $request)
    {
        $consumers = Consumer::latest()->paginate($request->integer('per_page', 15));

        return ConsumerResource::collection($consumers);
    }

    public function store(StoreConsumerRequest $request)
    {
        $consumer = Consumer::create($request->validated());

        return new ConsumerResource($consumer);
    }

    public function update(UpdateConsumerRequest $request, Consumer $consumer)
    {
        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $consumer->update($validated);

        return new ConsumerResource($consumer);
    }

    public function destroy(Consumer $consumer)
    {
        abort_if($consumer->is_default, 422, 'The demo account cannot be deleted.');

        $consumer->delete();

        return response()->json(['message' => 'Consumer deleted']);
    }
}
