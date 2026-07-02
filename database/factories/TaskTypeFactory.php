<?php

namespace Database\Factories;

use App\Models\TaskType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskTypeFactory extends Factory
{
    protected $model = TaskType::class;

    public function definition(): array
    {
        return [
            'name'           => $this->faker->unique()->words(2, true),
            'color'          => '#6366f1',
            'icon'           => null,
            'is_appointment' => false,
            'sort_order'     => 0,
        ];
    }
}
