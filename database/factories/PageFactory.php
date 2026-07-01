<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true);

        return [
            'title'            => ucfirst($title),
            'slug'             => Str::slug($title),
            'nav_label'        => null,
            'show_in_nav'      => false,
            'nav_order'        => 0,
            'parent_id'        => null,
            'is_home'          => false,
            'show_footer'      => true,
            'is_published'     => false,
            'meta_title'       => null,
            'meta_description' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(['is_published' => true]);
    }

    public function home(): static
    {
        return $this->state(['is_home' => true, 'is_published' => true]);
    }

    public function inNav(): static
    {
        return $this->state(['show_in_nav' => true, 'is_published' => true]);
    }
}
