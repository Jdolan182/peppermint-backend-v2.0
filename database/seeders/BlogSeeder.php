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
            ['name' => 'Tips & Advice', 'color' => '#6366f1'],
            ['name' => 'Case Studies',  'color' => '#ec4899'],
            ['name' => 'How It Works',  'color' => '#f97316'],
            ['name' => 'Updates',       'color' => '#10b981'],
        ];

        $cats = collect($categories)->map(function ($data) {
            return Category::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                ['name' => $data['name'], 'color' => $data['color']]
            );
        });

        $posts = [
            [
                'title'   => 'Why your business website is costing you more than it should',
                'excerpt' => 'Most small businesses are paying for three or four tools that could all live in one place. Here\'s what to look for.',
                'cats'    => ['Tips & Advice'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'What a customer portal actually does for a service business',
                'excerpt' => 'Giving customers a login to track their jobs reduces back-and-forth and makes you look more professional.',
                'cats'    => ['How It Works'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Case study: how a heating company cut admin time in half',
                'excerpt' => 'Moving from spreadsheets and emails to a single dashboard changed how the team managed daily work.',
                'cats'    => ['Case Studies'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'The difference between a website and an online presence',
                'excerpt' => 'A page with your phone number is a website. An online presence is something customers can actually use.',
                'cats'    => ['Tips & Advice'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Why I stopped using page builders and built my own',
                'excerpt' => 'Drag-and-drop tools promise simplicity but usually deliver constraints. Here\'s what I built instead.',
                'cats'    => ['How It Works'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'How a local beauty salon used a public roadmap to keep clients excited',
                'excerpt' => 'Publishing upcoming services and seasonal changes turns curious visitors into repeat customers.',
                'cats'    => ['Case Studies'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'What to put on a local business website (and what to leave out)',
                'excerpt' => 'Most small business sites have too much noise and not enough signal. A simple checklist.',
                'cats'    => ['Tips & Advice'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Blog posts for businesses that aren\'t writers',
                'excerpt' => 'You don\'t need to be a writer to publish useful content. You just need to answer the questions you get asked every week.',
                'cats'    => ['Tips & Advice'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'New: calendar view for tasks and upcoming jobs',
                'excerpt' => 'You can now see all your tasks and scheduled work in a shared month view. Here\'s how to use it.',
                'cats'    => ['Updates'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'From brief to live site: how a build actually works',
                'excerpt' => 'A walk-through of what happens between getting in touch and having a finished site.',
                'cats'    => ['How It Works'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Case study: a landscaping business that replaced three tools with one',
                'excerpt' => 'Quoting, job tracking, and customer updates were spread across email, WhatsApp, and a spreadsheet. Not any more.',
                'cats'    => ['Case Studies'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'What the monthly hosting fee actually covers',
                'excerpt' => 'Hosting, SSL, backups, security updates, and someone to call if anything breaks. Here\'s what\'s included.',
                'cats'    => ['How It Works'],
                'content' => $this->richContent(),
            ],
            [
                'title'   => 'Five signs your current website is losing you business',
                'excerpt' => 'Slow load times, no mobile layout, a phone number buried in the footer. Small problems that add up.',
                'cats'    => ['Tips & Advice'],
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
        return '<p>Every project starts with a problem worth solving. The hardest part is not the solution, it\'s staying close enough to the original problem that the solution actually fits.</p>'
            . '<h2>The first principle</h2>'
            . '<p>Good software is software that solves the problem it set out to solve, no more and no less. Scope creep is the enemy of clarity, and clarity is the enemy of mediocrity.</p>'
            . '<p>There are a few things that consistently separate products people love from products people tolerate: speed, honesty, and a refusal to waste the user\'s time.</p>'
            . '<h2>What this means in practice</h2>'
            . '<p>Start by writing down the one thing your feature needs to do. Not three things. One. Then build the smallest version of that. Ship it. Watch what happens.</p>'
            . '<ul><li>Observe how real users interact with it</li><li>Measure the thing that actually matters</li><li>Iterate based on what you see, not what you assumed</li></ul>'
            . '<p>Most of what ends up in a roadmap was invented in a meeting room by people who hadn\'t talked to a user in weeks. That\'s not a criticism, it\'s just how it goes when you move fast. The antidote is to slow down on the question of <em>what to build</em>, so you can move faster on <em>how to build it</em>.</p>'
            . '<blockquote>The goal is not to build more. The goal is to build the right things, well.</blockquote>'
            . '<p>That\'s it, really. Everything else is detail.</p>';
    }
}
