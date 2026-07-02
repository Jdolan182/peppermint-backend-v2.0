<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title'     => $this->faker->sentence(4),
            'type_id'   => TaskType::factory(),
            'status_id' => TaskStatus::factory(),
            'priority'  => 'medium',
        ];
    }
}
