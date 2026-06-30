<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);

        return [
            'title'        => $title,
            'slug'         => Str::slug($title),
            'content'      => $this->faker->paragraphs(3, true),
            'excerpt'      => $this->faker->optional()->sentence(),
            'published_at' => null,
            'user_id'      => User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(['published_at' => now()->subMinute()]);
    }
}
