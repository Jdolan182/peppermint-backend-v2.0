<?php

namespace Database\Factories;

use App\Models\RoadmapCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoadmapCategoryFactory extends Factory
{
    protected $model = RoadmapCategory::class;

    public function definition(): array
    {
        return [
            'name'       => $this->faker->words(2, true),
            'color'      => $this->faker->hexColor(),
            'sort_order' => 0,
        ];
    }
}
