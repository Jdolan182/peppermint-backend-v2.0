<?php

namespace Database\Factories;

use App\Models\TaskStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskStatusFactory extends Factory
{
    protected $model = TaskStatus::class;

    public function definition(): array
    {
        return [
            'name'       => $this->faker->unique()->words(2, true),
            'color'      => '#6b7280',
            'sort_order' => 0,
            'is_default' => false,
            'is_closed'  => false,
        ];
    }

    public function default(): static
    {
        return $this->state(['is_default' => true]);
    }

    public function closed(): static
    {
        return $this->state(['is_closed' => true]);
    }
}
