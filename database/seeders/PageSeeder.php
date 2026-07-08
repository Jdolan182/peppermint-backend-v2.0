<?php

namespace Database\Seeders;

use App\Models\FooterSection;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Database\Seeder;

/**
 * Generic starter site for new customer installs.
 *
 * A neutral local-service-business template: Home / Services / About /
 * Contact. Replace the copy per customer in the admin page builder;
 * the structure is the deliverable here, not the words.
 */
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
        $home = $this->makePage('Home', '/', is_home: true, show_in_nav: false,
            meta_title: 'Your Business Name | Quality service, locally trusted',
            meta_description: 'Friendly, reliable service from a local business you can count on. Get in touch today for a free, no-obligation quote.',
        );

        $services = $this->makePage('Services', 'services', show_in_nav: true, nav_order: 1,
            meta_title: 'Our Services | Your Business Name',
            meta_description: 'See the full range of services we offer, how we work, and answers to the questions we hear most often.',
        );

        $about = $this->makePage('About', 'about', show_in_nav: true, nav_order: 2,
            meta_title: 'About Us | Your Business Name',
            meta_description: 'Who we are, how we work, and why local customers have trusted us for years.',
        );

        $contact = $this->makePage('Contact', 'contact', show_in_nav: true, nav_order: 3,
            meta_title: 'Contact Us | Your Business Name',
            meta_description: 'Get in touch for a free quote or a friendly chat about what you need. We reply within one working day.',
        );

        $this->seedHomeSections($home);
        $this->seedServicesSections($services);
        $this->seedAboutSections($about);
        $this->seedContactSections($contact);
    }

    private function makePage(
        string  $title,
        string  $slug,
        bool    $is_home          = false,
        bool    $show_in_nav      = false,
        int     $nav_order        = 0,
        ?int    $parent_id        = null,
        ?string $meta_title       = null,
        ?string $meta_description = null,
    ): Page {
        return Page::create([
            'title'            => $title,
            'slug'             => $slug,
            'is_home'          => $is_home,
            'is_published'     => true,
            'show_in_nav'      => $show_in_nav,
            'nav_order'        => $nav_order,
            'show_footer'      => true,
            'parent_id'        => $parent_id,
            'meta_title'       => $meta_title,
            'meta_description' => $meta_description,
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
            'heading'             => 'Quality service you can rely on',
            'subheading'          => 'We\'re a local business that takes pride in doing the job properly: on time, on budget, and with a friendly face. See what we can do for you.',
            'body'                => '',
            'image'               => 'https://picsum.photos/seed/starter-hero/800/600',
            'cta_primary_label'   => 'Our services',
            'cta_primary_url'     => '/services',
            'cta_secondary_label' => 'Get a free quote',
            'cta_secondary_url'   => '/contact',
        ], 1);

        $this->addSection($page, 'features', [
            'heading'    => 'What we do',
            'subheading' => 'From small jobs to big projects, we cover it all, and we\'re happy to talk through anything you\'re not sure about.',
            'items'      => [
                ['icon' => '🔧', 'title' => 'Your first service',  'description' => 'A short description of your most popular service. What it includes, who it\'s for, and why customers choose you for it.'],
                ['icon' => '🏠', 'title' => 'Your second service', 'description' => 'Another key service. Keep these descriptions short and benefit-led. What does the customer get out of it?'],
                ['icon' => '⭐', 'title' => 'Your third service',  'description' => 'A third service or specialty. Three is plenty for the home page. The full list lives on the Services page.'],
            ],
        ], 2);

        $this->addSection($page, 'stats', [
            'items' => [
                ['value' => '10+',   'label' => 'Years experience',   'description' => 'Serving the local area'],
                ['value' => '500+',  'label' => 'Happy customers',    'description' => 'And counting'],
                ['value' => 'Free',  'label' => 'Quotes',             'description' => 'No obligation, no pressure'],
                ['value' => '5★',    'label' => 'Rated by customers', 'description' => 'On Google and Facebook'],
            ],
        ], 3);

        $this->addSection($page, 'testimonials', [
            'heading'    => 'What our customers say',
            'subheading' => 'Don\'t take our word for it. Here\'s what people in the area think of our work.',
            'items'      => [
                [
                    'quote'  => 'Turned up when they said they would, did a brilliant job, and left everything spotless. Couldn\'t ask for more.',
                    'author' => 'Sarah W.',
                    'role'   => 'Local customer',
                    'image'  => 'https://picsum.photos/seed/starter-avatar1/80/80',
                ],
                [
                    'quote'  => 'Really easy to deal with from the first phone call. Fair price, great work, and they explained everything clearly.',
                    'author' => 'David H.',
                    'role'   => 'Local customer',
                    'image'  => 'https://picsum.photos/seed/starter-avatar2/80/80',
                ],
                [
                    'quote'  => 'I\'ve recommended them to friends and family. Trustworthy, tidy, and the quality speaks for itself.',
                    'author' => 'Margaret L.',
                    'role'   => 'Local customer',
                    'image'  => 'https://picsum.photos/seed/starter-avatar3/80/80',
                ],
            ],
        ], 4);

        $this->addSection($page, 'cta', [
            'heading'   => 'Ready to get started?',
            'body'      => '<p>Get in touch for a free, no-obligation quote. We\'ll get back to you within one working day.</p>',
            'cta_label' => 'Contact us',
            'cta_url'   => '/contact',
            'style'     => 'dark',
        ], 5);
    }

    // -------------------------------------------------------------------------
    // Services
    // -------------------------------------------------------------------------

    private function seedServicesSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'Our services',
            'subheading' => 'Everything we offer, explained simply. Not sure what you need? Get in touch and we\'ll point you in the right direction.',
        ], 1);

        $this->addSection($page, 'features', [
            'heading'    => 'What we offer',
            'subheading' => 'Replace these with your real services. Aim for one clear sentence about what it is and one about why it matters.',
            'items'      => [
                ['icon' => '🔧', 'title' => 'Service one',   'description' => 'What this service is and what\'s included. Mention anything that sets you apart: guarantees, materials, turnaround.'],
                ['icon' => '🏠', 'title' => 'Service two',   'description' => 'Keep each description to two sentences. Customers scan, they don\'t read.'],
                ['icon' => '⚡', 'title' => 'Service three', 'description' => 'If a service has its own pricing or process, say so here and expand in the FAQ below.'],
                ['icon' => '🛠️', 'title' => 'Service four',  'description' => 'It\'s fine to list fewer services and describe them well, rather than listing everything you\'ve ever done.'],
                ['icon' => '📋', 'title' => 'Service five',  'description' => 'Seasonal or occasional services can go here too. Customers often don\'t know you offer them.'],
                ['icon' => '⭐', 'title' => 'Service six',   'description' => 'End with your specialty, the thing you want to be known for locally.'],
            ],
        ], 2);

        $this->addSection($page, 'faq', [
            'heading' => 'Common questions',
            'items'   => [
                ['question' => 'Do you offer free quotes?',            'answer' => 'Yes, every quote is free and there\'s no obligation. Tell us what you need and we\'ll give you a clear price before any work starts.'],
                ['question' => 'Which areas do you cover?',            'answer' => 'We cover [your town] and the surrounding area. If you\'re not sure whether we reach you, just ask. We\'re flexible for larger jobs.'],
                ['question' => 'Are you insured?',                     'answer' => 'Fully insured, and happy to show proof of insurance and any relevant certifications on request.'],
                ['question' => 'How do payments work?',                'answer' => 'For most jobs we take payment on completion. Larger projects may be split into stages. We\'ll agree everything up front so there are no surprises.'],
                ['question' => 'How quickly can you start?',           'answer' => 'It depends on the season and the size of the job. Get in touch and we\'ll give you an honest lead time straight away.'],
            ],
        ], 3);

        $this->addSection($page, 'cta', [
            'heading'   => 'Don\'t see what you\'re looking for?',
            'body'      => '<p>We take on all sorts of work. If it\'s not listed, just ask. The worst we can say is that we know someone better suited.</p>',
            'cta_label' => 'Ask us anything',
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
            'heading'    => 'About us',
            'subheading' => 'Who we are and how we work.',
        ], 1);

        $this->addSection($page, 'content', [
            'body' => '<p>Write your story here: how the business started, how long you\'ve been serving the area, and what you care about. Two or three short paragraphs is plenty.</p><p>Customers reading this page are deciding whether to trust you. The things that convince them: how long you\'ve been around, that you\'re local, that real people answer the phone, and that you stand behind your work.</p><p>End with something personal. Businesses are chosen by people, and people buy from people.</p>',
        ], 2);

        $this->addSection($page, 'team', [
            'heading'    => 'Meet the team',
            'subheading' => 'The people who\'ll actually turn up.',
            'members'    => [
                [
                    'name'  => 'Alex Smith',
                    'role'  => 'Owner',
                    'bio'   => 'Founded the business and still does the work. Replace this with a couple of sentences about the owner: experience, qualifications, and what they enjoy about the job.',
                    'image' => 'https://picsum.photos/seed/starter-team1/400/400',
                ],
                [
                    'name'  => 'Jamie Brown',
                    'role'  => 'Team member',
                    'bio'   => 'Add each team member customers might meet. A friendly face and a name goes a long way before someone lets you through their front door.',
                    'image' => 'https://picsum.photos/seed/starter-team2/400/400',
                ],
            ],
        ], 3);

        $this->addSection($page, 'cta', [
            'heading'   => 'Like the sound of us?',
            'body'      => '<p>Get in touch and see for yourself. A quick chat costs nothing.</p>',
            'cta_label' => 'Get in touch',
            'cta_url'   => '/contact',
            'style'     => 'dark',
        ], 4);
    }

    // -------------------------------------------------------------------------
    // Contact
    // -------------------------------------------------------------------------

    private function seedContactSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'Get in touch',
            'subheading' => 'Tell us what you need and we\'ll get back to you within one working day, usually sooner.',
        ], 1);

        $this->addSection($page, 'contact', [
            'heading'    => 'Send us a message',
            'subheading' => 'A rough idea of what you\'re after is all we need to get started. Photos help too if it\'s a physical job.',
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
                'tagline'    => 'Quality service, locally trusted.',
                'copyright'  => '© ' . date('Y') . ' Your Business Name. All rights reserved.',
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-links',
            'order' => 2,
            'data'  => [
                'heading' => 'Explore',
                'links'   => [
                    ['label' => 'Services', 'url' => '/services'],
                    ['label' => 'About',    'url' => '/about'],
                    ['label' => 'Blog',     'url' => '/blogs'],
                    ['label' => 'Contact',  'url' => '/contact'],
                ],
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-links',
            'order' => 3,
            'data'  => [
                'heading' => 'Get in touch',
                'links'   => [
                    ['label' => 'Free quotes',   'url' => '/contact'],
                    ['label' => 'Our services',  'url' => '/services'],
                    ['label' => 'Customer login', 'url' => '/login'],
                ],
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-text',
            'order' => 4,
            'data'  => [
                'body' => '<p>Fully insured. Free quotes with no obligation. Serving [your town] and the surrounding area.</p>',
            ],
        ]);
    }
}
