<?php

namespace Database\Seeders;

use App\Models\FooterSection;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        PageSection::query()->delete();
        FooterSection::query()->delete();
        Page::query()->delete();

        $this->seedPages();
        $this->seedFooter();
    }

    // -------------------------------------------------------------------------
    // Pages
    // -------------------------------------------------------------------------

    private function seedPages(): void
    {
        $home     = $this->makePage('Home',     '/',        is_home: true,  show_in_nav: false);
        $features = $this->makePage('Features', 'features', show_in_nav: true, nav_order: 1);
        $pricing  = $this->makePage('Pricing',  'pricing',  show_in_nav: true, nav_order: 2);
        $about    = $this->makePage('About',    'about',    show_in_nav: true, nav_order: 3);
        $contact  = $this->makePage('Contact',  'contact',  show_in_nav: true, nav_order: 4);

        $aboutMe  = $this->makePage('About me', 'about-me', show_in_nav: true, nav_order: 1, parent_id: $about->id);

        $this->seedHomeSections($home);
        $this->seedFeaturesSections($features);
        $this->seedPricingSections($pricing);
        $this->seedAboutSections($about);
        $this->seedAboutMeSections($aboutMe);
        $this->seedContactSections($contact);
    }

    private function makePage(
        string $title,
        string $slug,
        bool   $is_home     = false,
        bool   $show_in_nav = false,
        int    $nav_order   = 0,
        ?int   $parent_id   = null,
    ): Page {
        return Page::create([
            'title'        => $title,
            'slug'         => $slug,
            'is_home'      => $is_home,
            'is_published' => true,
            'show_in_nav'  => $show_in_nav,
            'nav_order'    => $nav_order,
            'show_footer'  => true,
            'parent_id'    => $parent_id,
        ]);
    }

    private function addSection(Page $page, string $type, array $data, int $order): void
    {
        PageSection::create([
            'page_id' => $page->id,
            'type'    => $type,
            'order'   => $order,
            'data'    => $data,
        ]);
    }

    // -------------------------------------------------------------------------
    // Home
    // -------------------------------------------------------------------------

    private function seedHomeSections(Page $page): void
    {
        $this->addSection($page, 'hero', [
            'heading'             => 'Everything your business needs online — built and managed for you',
            'subheading'          => 'I build bespoke websites for local businesses that include a blog, customer management, and more. You focus on your business, I handle the tech.',
            'body'                => '',
            'image'               => 'https://picsum.photos/seed/hero1/800/600',
            'cta_primary_label'   => 'See what\'s included',
            'cta_primary_url'     => '/features',
            'cta_secondary_label' => 'Get in touch',
            'cta_secondary_url'   => '/contact',
        ], 1);

        $this->addSection($page, 'stats', [
            'items' => [
                ['value' => '2 weeks', 'label' => 'Typical build time',   'description' => 'From brief to live'],
                ['value' => '1 login', 'label' => 'Manage everything',    'description' => 'Pages, blog, customers — one place'],
                ['value' => '£0',      'label' => 'Ongoing dev costs',    'description' => 'Monthly fee covers everything'],
                ['value' => 'Always',  'label' => 'Someone to call',      'description' => 'Not a support ticket queue'],
            ],
        ], 2);

        $this->addSection($page, 'features', [
            'heading'    => 'More than just a website',
            'subheading' => 'Every build comes with a full set of tools to run your online presence — no extra subscriptions needed.',
            'items'      => [
                ['icon' => '📝', 'title' => 'Your website',        'description' => 'Beautiful, fast pages built to your spec. Update content yourself whenever you need to.'],
                ['icon' => '✍️', 'title' => 'Blog',                'description' => 'Write news, updates, or guides. Great for SEO and keeping customers coming back.'],
                ['icon' => '👥', 'title' => 'Customer portal',     'description' => 'Give customers a login to view quotes, track jobs, or raise requests — all in one place.'],
                ['icon' => '✅', 'title' => 'Job & task tracking', 'description' => 'Keep on top of what needs doing with a simple task board. Assign to staff or link to customers.'],
                ['icon' => '🗺️', 'title' => 'Service roadmap',     'description' => 'Show customers what\'s coming — new services, upcoming availability, seasonal changes.'],
                ['icon' => '📅', 'title' => 'Calendar view',       'description' => 'See tasks and upcoming work together in a shared calendar. Book jobs and plan ahead.'],
            ],
        ], 3);

        $this->addSection($page, 'testimonials', [
            'heading'    => 'What your clients will say',
            'subheading' => 'Your site will feature real reviews from your own customers — here\'s what that looks like.',
            'items'      => [
                [
                    'quote'  => 'I used to have three different tools for my website, my bookings, and keeping track of jobs. Now it\'s all in one place and I actually use it.',
                    'author' => 'Claire M.',
                    'role'   => 'Owner, Meridian Beauty Studio',
                    'image'  => 'https://picsum.photos/seed/avatar1/80/80',
                ],
                [
                    'quote'  => 'Was up and running in under two weeks. The admin panel is straightforward enough that even I can update it without breaking anything.',
                    'author' => 'Tom R.',
                    'role'   => 'Director, Riverside Heating',
                    'image'  => 'https://picsum.photos/seed/avatar2/80/80',
                ],
                [
                    'quote'  => 'Having a customer portal was something I\'d wanted for years but thought it would cost a fortune. This was genuinely affordable.',
                    'author' => 'Jen K.',
                    'role'   => 'Founder, Keller Landscaping',
                    'image'  => 'https://picsum.photos/seed/avatar3/80/80',
                ],
            ],
        ], 4);

        $this->addSection($page, 'cta', [
            'heading'   => 'Ready to get started?',
            'body'      => '<p>Tell me about your business and what you need. I\'ll come back with a spec and a price — no commitment required.</p>',
            'cta_label' => 'Get in touch',
            'cta_url'   => '/contact',
            'style'     => 'dark',
        ], 5);
    }

    // -------------------------------------------------------------------------
    // Features
    // -------------------------------------------------------------------------

    private function seedFeaturesSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'What\'s included',
            'subheading' => 'Every build comes with the full platform. You only pay for what you actually use.',
        ], 1);

        $this->addSection($page, 'features', [
            'heading'    => 'A complete system, not just a website',
            'subheading' => 'Most website builders give you a site. This gives you the tools to run your business online.',
            'items'      => [
                ['icon' => '📝', 'title' => 'Page builder',        'description' => 'Build and update your own pages without touching code. Add text, images, and sections whenever you need to.'],
                ['icon' => '✍️', 'title' => 'Blog',                'description' => 'Publish news, guides, and updates. Organised by category, great for search rankings.'],
                ['icon' => '👥', 'title' => 'Customer portal',     'description' => 'Customers get their own login to view jobs, raise requests, and track progress. Reduces back-and-forth.'],
                ['icon' => '✅', 'title' => 'Tasks & job board',   'description' => 'A simple kanban board to manage your workload. Assign jobs to staff, set priorities and due dates.'],
                ['icon' => '🗺️', 'title' => 'Public roadmap',      'description' => 'Let customers see what\'s coming — new services, seasonal changes, or future availability.'],
                ['icon' => '📅', 'title' => 'Calendar',            'description' => 'All your tasks and upcoming jobs in one calendar view. See the week at a glance.'],
                ['icon' => '🌙', 'title' => 'Dark mode',           'description' => 'Looks great in light and dark. Your site automatically matches the visitor\'s system preference.'],
                ['icon' => '🔧', 'title' => 'Fully managed',       'description' => 'Hosting, updates, backups, and security are all handled. You don\'t need to think about any of it.'],
            ],
        ], 2);

        $this->addSection($page, 'bento', [
            'heading' => 'Built around how you actually work',
            'items'   => [
                ['title' => 'Update it yourself',    'description' => 'The admin panel is simple enough to use without any training. Change your pages, post a blog update, or close a job in minutes.',                 'image' => 'https://picsum.photos/seed/feat1/600/400', 'size' => 'wide'],
                ['title' => 'Everything connected',  'description' => 'Tasks link to customers. Customers link to jobs. It all talks to each other.',                                                                      'image' => 'https://picsum.photos/seed/feat2/400/400', 'size' => 'normal'],
                ['title' => 'Live in two weeks',     'description' => 'Brief on Monday, live site by the end of week two — typically.',                                                                                   'image' => 'https://picsum.photos/seed/feat3/400/400', 'size' => 'normal'],
                ['title' => 'Mobile ready',          'description' => 'Your site looks and works perfectly on phones. So does the admin panel.',                                                                           'image' => 'https://picsum.photos/seed/feat4/400/400', 'size' => 'normal'],
                ['title' => 'No vendor lock-in',     'description' => 'Your content is yours. If you ever leave, you take it with you.',                                                                                   'image' => 'https://picsum.photos/seed/feat5/400/400', 'size' => 'normal'],
            ],
        ], 3);

        $this->addSection($page, 'cta', [
            'heading'   => 'Want to see it in action?',
            'body'      => '<p>Get in touch and I\'ll show you a live demo tailored to your type of business.</p>',
            'cta_label' => 'Book a demo',
            'cta_url'   => '/contact',
            'style'     => 'dark',
        ], 4);
    }

    // -------------------------------------------------------------------------
    // Pricing
    // -------------------------------------------------------------------------

    private function seedPricingSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'Pricing',
            'subheading' => 'A one-time build fee. Hosting, support, and maintenance are optional — keep it running with me, or take it elsewhere.',
        ], 1);

        $this->addSection($page, 'pricing', [
            'heading' => 'Pick the right fit',
            'plans'   => [
                [
                    'name'     => 'Starter',
                    'price'    => '£599',
                    'period'   => 'one-time build fee',
                    'features' => "Up to 5 pages\nBlog\nContact form\nAdmin panel\nSSL certificate\nOptional: hosting & support from £35/mo",
                    'cta'      => 'Get in touch',
                    'cta_url'  => '/contact',
                ],
                [
                    'name'     => 'Business',
                    'price'    => '£999',
                    'period'   => 'one-time build fee',
                    'features' => "Unlimited pages\nBlog\nCustomer portal\nTask & job board\nPublic roadmap\nCalendar view\nOptional: hosting & support from £35/mo",
                    'cta'      => 'Get in touch',
                    'cta_url'  => '/contact',
                ],
                [
                    'name'     => 'Custom',
                    'price'    => 'From £1,500',
                    'period'   => 'one-time build fee',
                    'features' => "Everything in Business\nBespoke features\nThird-party integrations\nCustom design work\nDedicated support\nOptional: hosting & support from £55/mo",
                    'cta'      => 'Let\'s talk',
                    'cta_url'  => '/contact',
                ],
            ],
        ], 2);

        $this->addSection($page, 'faq', [
            'heading' => 'Common questions',
            'items'   => [
                ['question' => 'Who owns the website?',                       'answer' => 'You do. The content, the domain, all of it. I build and maintain it, but it\'s yours.'],
                ['question' => 'Can I update the site myself?',               'answer' => 'Yes — the admin panel lets you edit pages, write blog posts, and manage customers without touching code.'],
                ['question' => 'How long does a build take?',                 'answer' => 'Usually two weeks from brief to launch. More complex builds may take three to four weeks.'],
                ['question' => 'Is the monthly fee required?',                'answer' => 'No. The build fee is a one-off — the monthly is completely optional. It covers managed hosting, SSL, backups, and ongoing support. If you\'d rather self-host or use your own provider, I\'ll hand everything over.'],
                ['question' => 'What if I want to cancel or move later?',     'answer' => 'No problem. I\'ll help you migrate everything to wherever you want to go. Nothing is locked in.'],
                ['question' => 'Do I need to know anything about websites?',  'answer' => 'Nothing at all. That\'s the point. You tell me what you need, I handle the rest.'],
            ],
        ], 3);

        $this->addSection($page, 'cta', [
            'heading'   => 'Not sure which plan fits?',
            'body'      => '<p>Tell me about your business and I\'ll recommend the right fit. Most people know within a ten-minute chat.</p>',
            'cta_label' => 'Get in touch',
            'cta_url'   => '/contact',
            'style'     => 'light',
        ], 4);
    }

    // -------------------------------------------------------------------------
    // About
    // -------------------------------------------------------------------------

    private function seedAboutSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'About',
            'subheading' => 'A bit about who I am and how I work.',
        ], 1);

        $this->addSection($page, 'content', [
            'body' => '<p>I\'m a freelance web developer who builds bespoke websites and management tools for small and local businesses. I started because I kept seeing the same problem: business owners paying for three or four different subscriptions to do things that could all live in one place.</p><p>So I built the platform myself. Every site I deliver is built on top of it — which means you get a full set of tools from day one, without the complexity or cost of stitching together separate products.</p><p>I work with a small number of clients at a time so I can actually give them proper attention. If you get in touch, you\'ll hear back from me directly — not an account manager, not a support bot.</p>',
        ], 2);

        $this->addSection($page, 'stats', [
            'items' => [
                ['value' => '2024',   'label' => 'Started',          'description' => 'Building tools for real businesses'],
                ['value' => 'Solo',   'label' => 'How I work',       'description' => 'One developer, full accountability'],
                ['value' => '2 wks',  'label' => 'Typical turnaround','description' => 'Brief to live site'],
                ['value' => 'Fixed',  'label' => 'Pricing',          'description' => 'No hourly surprises'],
            ],
        ], 3);

        $this->addSection($page, 'bento', [
            'heading' => 'How I work',
            'items'   => [
                ['title' => 'I spec it first',       'description' => 'Before any build starts I write up exactly what you\'re getting — pages, features, timeline, price. No surprises.',              'image' => 'https://picsum.photos/seed/bento1/600/400', 'size' => 'wide'],
                ['title' => 'Small client list',     'description' => 'I keep the number of active clients small so each one gets proper attention.',                                                   'image' => 'https://picsum.photos/seed/bento2/400/400', 'size' => 'normal'],
                ['title' => 'You talk to me',        'description' => 'No support tickets. No offshore helpdesk. You have my number.',                                                                 'image' => 'https://picsum.photos/seed/bento3/400/400', 'size' => 'normal'],
                ['title' => 'Built to hand over',    'description' => 'The admin panel is simple enough that you can run it yourself from day one. Training is included.',                             'image' => 'https://picsum.photos/seed/bento4/600/400', 'size' => 'wide'],
            ],
        ], 4);
    }

    // -------------------------------------------------------------------------
    // About me
    // -------------------------------------------------------------------------

    private function seedAboutMeSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'About me',
            'subheading' => 'The person behind the builds.',
        ], 1);

        $this->addSection($page, 'team', [
            'heading'    => '',
            'subheading' => '',
            'members'    => [
                [
                    'name'  => 'Jordan Dolan',
                    'role'  => 'Developer & founder',
                    'bio'   => 'I\'ve been building web applications for years and decided to focus on solving a specific problem: local businesses spending too much on too many tools. I build, I maintain, and I\'m your point of contact for everything.',
                    'image' => '/images/jordan.jpg',
                ],
            ],
        ], 2);
    }

    // -------------------------------------------------------------------------
    // Contact
    // -------------------------------------------------------------------------

    private function seedContactSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'Get in touch',
            'subheading' => 'Tell me about your business and what you\'re looking for. I\'ll get back to you within one working day.',
        ], 1);

        $this->addSection($page, 'contact', [
            'heading'    => 'Send me a message',
            'subheading' => 'Not sure what you need yet? That\'s fine — just tell me a bit about your business and we\'ll go from there.',
            'email'      => 'hello@example.com',
        ], 2);
    }

    // -------------------------------------------------------------------------
    // Footer
    // -------------------------------------------------------------------------

    private function seedFooter(): void
    {
        FooterSection::create([
            'type'  => 'footer-brand',
            'order' => 1,
            'data'  => [
                'logo_image' => null,
                'tagline'    => 'Bespoke websites and management tools for local businesses.',
                'copyright'  => '© ' . date('Y') . ' All rights reserved.',
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-links',
            'order' => 2,
            'data'  => [
                'heading' => 'Site',
                'links'   => [
                    ['label' => 'Features', 'url' => '/features'],
                    ['label' => 'Pricing',  'url' => '/pricing'],
                    ['label' => 'About',    'url' => '/about'],
                    ['label' => 'Blog',     'url' => '/blogs'],
                ],
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-links',
            'order' => 3,
            'data'  => [
                'heading' => 'Get started',
                'links'   => [
                    ['label' => 'Contact',  'url' => '/contact'],
                    ['label' => 'Pricing',  'url' => '/pricing'],
                    ['label' => 'About me', 'url' => '/about-me'],
                ],
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-text',
            'order' => 5,
            'data'  => [
                'body' => '<p>Built with care. Your data stays private and is never shared or sold.</p>',
            ],
        ]);
    }
}
