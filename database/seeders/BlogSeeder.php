<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $categories = [
            ['name' => 'Technology', 'color' => '#6366f1'],
            ['name' => 'Design',     'color' => '#ec4899'],
            ['name' => 'Business',   'color' => '#f97316'],
            ['name' => 'Culture',    'color' => '#10b981'],
            ['name' => 'News',       'color' => '#3b82f6'],
        ];

        $cats = collect($categories)->map(function ($data) {
            return Category::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                ['name' => $data['name'], 'color' => $data['color']]
            );
        });

        $posts = [
            [
                'title'   => 'Getting started with Vue 3 and the Composition API',
                'excerpt' => 'The Composition API changes how we structure logic in Vue — here\'s a practical intro.',
                'cats'    => ['Technology'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Why design systems save more time than they cost',
                'excerpt' => 'Building a design system feels slow at first, but the compounding returns are real.',
                'cats'    => ['Design', 'Business'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'The quiet power of well-named variables',
                'excerpt' => 'Code is read far more than it is written. Names are the first place to invest.',
                'cats'    => ['Technology'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Building in public: six months in',
                'excerpt' => 'What sharing our progress openly has done for our thinking, our users, and our team.',
                'cats'    => ['Business', 'Culture'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Tailwind CSS v4: what actually changed',
                'excerpt' => 'The new CSS-first config, updated dark mode, and what to watch out for when upgrading.',
                'cats'    => ['Technology', 'Design'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Small teams, big products',
                'excerpt' => 'The constraints of a small team often produce the clearest product thinking.',
                'cats'    => ['Business'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Colour in interfaces: more than aesthetics',
                'excerpt' => 'How we use colour to carry meaning, guide attention, and reduce cognitive load.',
                'cats'    => ['Design'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Laravel queues without the headaches',
                'excerpt' => 'A practical guide to jobs, workers, and failure handling in production.',
                'cats'    => ['Technology'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'The case for fewer features',
                'excerpt' => 'Every feature has a maintenance cost. Most products would be better with less.',
                'cats'    => ['Business', 'Culture'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'What we shipped in Q2',
                'excerpt' => 'A rundown of everything that went out the door over the last three months.',
                'cats'    => ['News'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Accessibility is not optional',
                'excerpt' => 'Building for everyone isn\'t just the right thing to do — it makes your product better.',
                'cats'    => ['Design', 'Culture'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'How we handle database migrations at scale',
                'excerpt' => 'Zero-downtime deploys require careful thinking about schema changes and backwards compatibility.',
                'cats'    => ['Technology'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Writing product copy that actually works',
                'excerpt' => 'Clear, specific, and honest — what separates forgettable copy from copy that converts.',
                'cats'    => ['Business', 'Design'],
                'content' => $this->richContent(),
            ],
        ];

        foreach ($posts as $i => $data) {
            $slug = Str::slug($data['title']);
            $blog = Blog::firstOrCreate(['slug' => $slug], [
                'title'        => $data['title'],
                'slug'         => $slug,
                'excerpt'      => $data['excerpt'],
                'content'      => $data['content'],
                'published_at' => now()->subDays(count($posts) - $i),
                'user_id'      => $user->id,
            ]);

            $catIds = $cats->filter(fn($c) => in_array($c->name, $data['cats']))->pluck('id');
            $blog->categories()->syncWithoutDetaching($catIds);
        }
    }

    private function richContent(): string
    {
        return '<p>Every project starts with a problem worth solving. The hardest part is not the solution — it\'s staying close enough to the original problem that the solution actually fits.</p>'
            . '<h2>The first principle</h2>'
            . '<p>Good software is software that solves the problem it set out to solve, no more and no less. Scope creep is the enemy of clarity, and clarity is the enemy of mediocrity.</p>'
            . '<p>There are a few things that consistently separate products people love from products people tolerate: speed, honesty, and a refusal to waste the user\'s time.</p>'
            . '<h2>What this means in practice</h2>'
            . '<p>Start by writing down the one thing your feature needs to do. Not three things. One. Then build the smallest version of that. Ship it. Watch what happens.</p>'
            . '<ul><li>Observe how real users interact with it</li><li>Measure the thing that actually matters</li><li>Iterate based on what you see, not what you assumed</li></ul>'
            . '<p>Most of what ends up in a roadmap was invented in a meeting room by people who hadn\'t talked to a user in weeks. That\'s not a criticism — it\'s just how it goes when you move fast. The antidote is to slow down on the question of <em>what to build</em>, so you can move faster on <em>how to build it</em>.</p>'
            . '<blockquote>The goal is not to build more. The goal is to build the right things, well.</blockquote>'
            . '<p>That\'s it, really. Everything else is detail.</p>';
    }
}
