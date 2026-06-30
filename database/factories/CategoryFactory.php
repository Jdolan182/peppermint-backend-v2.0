<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = ucfirst($this->faker->unique()->word());
        $colors = ['#6366f1', '#ec4899', '#f97316', '#10b981', '#3b82f6', '#8b5cf6', '#f59e0b', '#ef4444'];

        return [
            'name'  => $name,
            'slug'  => Str::slug($name),
            'color' => $this->faker->randomElement($colors),
        ];
    }
}
