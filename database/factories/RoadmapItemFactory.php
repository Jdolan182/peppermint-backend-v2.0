<?php

namespace Database\Factories;

use App\Models\RoadmapItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoadmapItemFactory extends Factory
{
    protected $model = RoadmapItem::class;

    public function definition(): array
    {
        return [
            'title'      => $this->faker->sentence(4),
            'description'=> null,
            'status'     => 'planned',
            'start_date'  => null,
            'date'        => null,
            'category_id' => null,
            'sort_order'  => 0,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(['status' => 'in-progress']);
    }

    public function shipped(): static
    {
        return $this->state(['status' => 'shipped']);
    }

    public function cancelled(): static
    {
        return $this->state(['status' => 'cancelled']);
    }
}
