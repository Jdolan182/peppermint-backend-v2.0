<?php

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        $filename = $this->faker->uuid() . '.jpg';

        return [
            'filename'  => $filename,
            'path'      => 'media/' . $filename,
            'disk'      => 'public',
            'mime_type' => 'image/jpeg',
            'size'      => $this->faker->numberBetween(10000, 500000),
            'alt'       => null,
        ];
    }
}
