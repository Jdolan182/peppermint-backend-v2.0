<?php

namespace Database\Factories;

use App\Models\FooterSection;
use Illuminate\Database\Eloquent\Factories\Factory;

class FooterSectionFactory extends Factory
{
    protected $model = FooterSection::class;

    public function definition(): array
    {
        return [
            'type'  => 'links',
            'order' => 0,
            'data'  => ['title' => $this->faker->word()],
        ];
    }
}
