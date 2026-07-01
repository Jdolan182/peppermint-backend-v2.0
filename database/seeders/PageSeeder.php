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
        // Clear existing data
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
        $home     = $this->makePage('Home',     '/',         is_home: true,  show_in_nav: false);
        $about    = $this->makePage('About',    'about',     show_in_nav: true,  nav_order: 1);
        $features = $this->makePage('Features', 'features',  show_in_nav: true,  nav_order: 2);
        $pricing  = $this->makePage('Pricing',  'pricing',   show_in_nav: true,  nav_order: 3);
        $contact  = $this->makePage('Contact',  'contact',   show_in_nav: true,  nav_order: 4);

        // Sub-pages under About
        $team    = $this->makePage('Our Team',     'team',    show_in_nav: true, nav_order: 1, parent_id: $about->id);
        $careers = $this->makePage('Careers',      'careers', show_in_nav: true, nav_order: 2, parent_id: $about->id);

        $this->seedHomeSections($home);
        $this->seedAboutSections($about);
        $this->seedFeaturesSections($features);
        $this->seedPricingSections($pricing);
        $this->seedContactSections($contact);
        $this->seedTeamSections($team);
        $this->seedCareersSections($careers);
    }

    private function makePage(
        string $title,
        string $slug,
        bool   $is_home    = false,
        bool   $show_in_nav = false,
        int    $nav_order  = 0,
        ?int   $parent_id  = null,
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
    // Home — Hero + Stats + Features + CTA + Newsletter
    // -------------------------------------------------------------------------

    private function seedHomeSections(Page $page): void
    {
        $this->addSection($page, 'hero', [
            'heading'             => 'Build something people love',
            'subheading'          => 'The all-in-one platform for modern teams. Manage your content, engage your customers, and grow your business — all in one place.',
            'body'                => '',
            'image'               => 'https://picsum.photos/seed/hero1/800/600',
            'cta_primary_label'   => 'Get started free',
            'cta_primary_url'     => '/pricing',
            'cta_secondary_label' => 'See features',
            'cta_secondary_url'   => '/features',
        ], 1);

        $this->addSection($page, 'logo-cloud', [
            'heading' => 'Trusted by teams at',
            'logos'   => [
                ['name' => 'Acme Corp',   'image' => 'https://picsum.photos/seed/logo1/120/40'],
                ['name' => 'Globex',      'image' => 'https://picsum.photos/seed/logo2/120/40'],
                ['name' => 'Initech',     'image' => 'https://picsum.photos/seed/logo3/120/40'],
                ['name' => 'Umbrella',    'image' => 'https://picsum.photos/seed/logo4/120/40'],
                ['name' => 'Hooli',       'image' => 'https://picsum.photos/seed/logo5/120/40'],
            ],
        ], 2);

        $this->addSection($page, 'stats', [
            'items' => [
                ['value' => '10k+',  'label' => 'Customers',      'description' => 'Businesses using our platform'],
                ['value' => '99.9%', 'label' => 'Uptime',         'description' => 'Guaranteed SLA'],
                ['value' => '4.9',   'label' => 'Average rating', 'description' => 'Across all reviews'],
                ['value' => '24/7',  'label' => 'Support',        'description' => 'Always here when you need us'],
            ],
        ], 3);

        $this->addSection($page, 'features', [
            'heading'    => 'Everything you need',
            'subheading' => 'A complete toolkit so you can focus on building, not plumbing.',
            'items'      => [
                ['icon' => '⚡', 'title' => 'Lightning fast',    'description' => 'Pages load in under 100ms. Your users will notice.'],
                ['icon' => '🔒', 'title' => 'Secure by default', 'description' => 'SOC2 Type II certified. GDPR compliant out of the box.'],
                ['icon' => '🧩', 'title' => 'Modular',           'description' => 'Turn features on and off. Pay for what you use.'],
                ['icon' => '📈', 'title' => 'Built to scale',    'description' => 'From a solo founder to a 500-person team.'],
                ['icon' => '🎨', 'title' => 'Fully customisable','description' => 'Your brand, your colours, your domain.'],
                ['icon' => '🤝', 'title' => 'Great support',     'description' => 'Real humans who actually know the product.'],
            ],
        ], 4);

        $this->addSection($page, 'testimonials', [
            'heading' => 'Loved by teams everywhere',
            'items'   => [
                [
                    'quote'  => 'Switching to this platform was the best decision we made this year. Our whole team was onboarded in an afternoon.',
                    'author' => 'Sarah Chen',
                    'role'   => 'CTO at Loopline',
                    'image'  => 'https://picsum.photos/seed/avatar1/80/80',
                ],
                [
                    'quote'  => 'The page builder alone saved us weeks of dev time. We shipped our landing page the same day.',
                    'author' => 'Marcus Webb',
                    'role'   => 'Founder at Driftwood',
                    'image'  => 'https://picsum.photos/seed/avatar2/80/80',
                ],
                [
                    'quote'  => 'I was sceptical of yet another CMS but this one actually gets out of your way and lets you work.',
                    'author' => 'Priya Nair',
                    'role'   => 'Head of Marketing at Folio',
                    'image'  => 'https://picsum.photos/seed/avatar3/80/80',
                ],
            ],
        ], 5);

        $this->addSection($page, 'cta', [
            'heading'   => 'Ready to get started?',
            'body'      => '<p>Join thousands of teams already using our platform. No credit card required.</p>',
            'cta_label' => 'Start for free',
            'cta_url'   => '/pricing',
            'style'     => 'dark',
        ], 6);

        $this->addSection($page, 'newsletter', [
            'heading'      => 'Stay in the loop',
            'subheading'   => 'Product updates, tips, and the occasional deep dive. No spam.',
            'placeholder'  => 'Your email address',
            'button_label' => 'Subscribe',
        ], 7);
    }

    // -------------------------------------------------------------------------
    // About — Page header + Content + Stats + Bento
    // -------------------------------------------------------------------------

    private function seedAboutSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'About us',
            'subheading' => 'We\'re a small team building tools we wish existed.',
        ], 1);

        $this->addSection($page, 'content', [
            'body' => '<p>We started in a spare bedroom in 2021 with a single idea: content management should be simple, fast, and actually enjoyable to use. Three years later we\'re a team of twelve shipping features every week.</p><p>We\'re fully remote, self-funded, and obsessed with the craft of building great software. We believe the best products come from teams that care deeply about the problem they\'re solving.</p>',
        ], 2);

        $this->addSection($page, 'stats', [
            'items' => [
                ['value' => '2021',  'label' => 'Founded',    'description' => 'Started in a spare bedroom'],
                ['value' => '12',    'label' => 'Team members','description' => 'Across 6 countries'],
                ['value' => '100%',  'label' => 'Remote',     'description' => 'Fully distributed team'],
                ['value' => '$0',    'label' => 'VC funding',  'description' => 'Proudly bootstrapped'],
            ],
        ], 3);

        $this->addSection($page, 'bento', [
            'heading' => 'How we work',
            'items'   => [
                ['title' => 'Ship fast',          'description' => 'We deploy multiple times a day. Small PRs, continuous delivery.',                           'image' => 'https://picsum.photos/seed/bento1/600/400', 'size' => 'wide'],
                ['title' => 'Write things down',  'description' => 'Decisions, context, and reasoning are documented, not just discussed.',                     'image' => 'https://picsum.photos/seed/bento2/400/400', 'size' => 'normal'],
                ['title' => 'Customer obsessed',  'description' => 'Every feature starts with a real customer problem.',                                        'image' => 'https://picsum.photos/seed/bento3/400/400', 'size' => 'normal'],
                ['title' => 'Default to open',    'description' => 'We share our roadmap, our metrics, and our thinking with the community.',                   'image' => 'https://picsum.photos/seed/bento4/600/400', 'size' => 'wide'],
            ],
        ], 4);
    }

    // -------------------------------------------------------------------------
    // Features — Page header + Features + Bento + LogoCloud + CTA
    // -------------------------------------------------------------------------

    private function seedFeaturesSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'Features',
            'subheading' => 'Everything you need to run your business online.',
        ], 1);

        $this->addSection($page, 'features', [
            'heading'    => 'Built for speed and simplicity',
            'subheading' => 'No bloat. No unnecessary complexity. Just the tools you need.',
            'items'      => [
                ['icon' => '📝', 'title' => 'Page builder',    'description' => 'Drag-and-drop blocks to build beautiful pages without writing a line of code.'],
                ['icon' => '✍️', 'title' => 'Blog',            'description' => 'Write and publish posts with a clean rich-text editor. Schedule for later.'],
                ['icon' => '👥', 'title' => 'Consumer accounts','description' => 'Let your users sign up, log in, and manage their own profiles.'],
                ['icon' => '⚙️', 'title' => 'Module system',   'description' => 'Turn features on and off per installation. Only pay for what you use.'],
                ['icon' => '🌙', 'title' => 'Dark mode',       'description' => 'Every page looks great in both light and dark. Automatically.'],
                ['icon' => '📊', 'title' => 'Analytics ready', 'description' => 'Drop in your analytics script and start tracking from day one.'],
            ],
        ], 2);

        $this->addSection($page, 'bento', [
            'heading' => 'See it in action',
            'items'   => [
                ['title' => 'Live preview',      'description' => 'See your changes before you publish.',                     'image' => 'https://picsum.photos/seed/feat1/600/400', 'size' => 'wide'],
                ['title' => 'Mobile first',      'description' => 'Every layout is responsive out of the box.',               'image' => 'https://picsum.photos/seed/feat2/400/400', 'size' => 'normal'],
                ['title' => 'Media library',     'description' => 'Upload once, reuse everywhere.',                           'image' => 'https://picsum.photos/seed/feat3/400/400', 'size' => 'normal'],
                ['title' => 'Fast by default',   'description' => 'Images are lazy-loaded. Assets are cached. Pages are fast.','image' => 'https://picsum.photos/seed/feat4/400/400', 'size' => 'normal'],
                ['title' => 'SEO built in',      'description' => 'Meta titles, descriptions, and structured data. No plugin needed.','image' => 'https://picsum.photos/seed/feat5/400/400', 'size' => 'normal'],
            ],
        ], 3);

        $this->addSection($page, 'cta', [
            'heading'   => 'Sounds good?',
            'body'      => '<p>See all of this in your own instance. Free to try, no card needed.</p>',
            'cta_label' => 'Start building',
            'cta_url'   => '/pricing',
            'style'     => 'dark',
        ], 4);
    }

    // -------------------------------------------------------------------------
    // Pricing — Page header + Pricing + FAQ + CTA
    // -------------------------------------------------------------------------

    private function seedPricingSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'Pricing',
            'subheading' => 'Simple, transparent pricing. No surprises.',
        ], 1);

        $this->addSection($page, 'pricing', [
            'heading' => 'Choose your plan',
            'plans'   => [
                [
                    'name'     => 'Starter',
                    'price'    => '$0',
                    'period'   => '/ month',
                    'features' => "Up to 5 pages\nBlog module\n1 admin user\nCommunity support",
                    'cta'      => 'Get started free',
                    'cta_url'  => '/contact',
                ],
                [
                    'name'     => 'Pro',
                    'price'    => '$49',
                    'period'   => '/ month',
                    'features' => "Unlimited pages\nAll modules\nUnlimited admins\nConsumer accounts\nPriority support\nCustom domain",
                    'cta'      => 'Start free trial',
                    'cta_url'  => '/contact',
                ],
                [
                    'name'     => 'Enterprise',
                    'price'    => 'Custom',
                    'period'   => '',
                    'features' => "Everything in Pro\nSLAs\nDedicated support\nCustom integrations\nSingle sign-on\nOn-premise option",
                    'cta'      => 'Talk to sales',
                    'cta_url'  => '/contact',
                ],
            ],
        ], 2);

        $this->addSection($page, 'faq', [
            'heading' => 'Frequently asked questions',
            'items'   => [
                ['question' => 'Do I need a credit card to start?',           'answer' => 'No. The Starter plan is free forever with no card required. You only need to add billing when you upgrade.'],
                ['question' => 'Can I change plans later?',                   'answer' => 'Yes, you can upgrade or downgrade at any time. Changes take effect immediately and we prorate any billing.'],
                ['question' => 'What happens when my trial ends?',            'answer' => 'You\'ll be moved to the Starter plan automatically. Your data is safe and nothing will be deleted.'],
                ['question' => 'Do you offer discounts for nonprofits?',      'answer' => 'Yes. Reach out via the contact page and we\'ll sort something out.'],
                ['question' => 'Can I self-host?',                            'answer' => 'Enterprise plans include an on-premise option. We handle the deployment and you own the infrastructure.'],
                ['question' => 'Is there a limit on page views?',             'answer' => 'No page view limits on any plan. We believe in transparent, usage-independent pricing.'],
            ],
        ], 3);

        $this->addSection($page, 'cta', [
            'heading'   => 'Still not sure?',
            'body'      => '<p>Book a 20-minute demo and we\'ll walk you through exactly how it fits your use case.</p>',
            'cta_label' => 'Book a demo',
            'cta_url'   => '/contact',
            'style'     => 'light',
        ], 4);
    }

    // -------------------------------------------------------------------------
    // Contact — Page header + Contact
    // -------------------------------------------------------------------------

    private function seedContactSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'Get in touch',
            'subheading' => 'We\'d love to hear from you.',
        ], 1);

        $this->addSection($page, 'contact', [
            'heading'    => 'Send us a message',
            'subheading' => 'Fill in the form and we\'ll get back to you within one business day.',
            'email'      => 'hello@example.com',
        ], 2);
    }

    // -------------------------------------------------------------------------
    // Team — Page header + Team + Testimonials
    // -------------------------------------------------------------------------

    private function seedTeamSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'Meet the team',
            'subheading' => 'The people behind the product.',
        ], 1);

        $this->addSection($page, 'team', [
            'heading'    => '',
            'subheading' => '',
            'members'    => [
                ['name' => 'Jordan Ellis',   'role' => 'Co-founder & CEO',     'bio' => 'Previously led product at two YC companies. Obsessed with simplicity.', 'image' => 'https://picsum.photos/seed/team1/400/400'],
                ['name' => 'Mia Kowalski',   'role' => 'Co-founder & CTO',     'bio' => 'Full-stack engineer. Built the first version in a weekend.', 'image' => 'https://picsum.photos/seed/team2/400/400'],
                ['name' => 'Theo Park',      'role' => 'Head of Design',       'bio' => 'Former lead designer at Figma. Believes great design is invisible.', 'image' => 'https://picsum.photos/seed/team3/400/400'],
                ['name' => 'Isabelle Morin', 'role' => 'Head of Engineering',  'bio' => 'Distributed systems by day, jazz piano by night.', 'image' => 'https://picsum.photos/seed/team4/400/400'],
                ['name' => 'Ravi Sharma',    'role' => 'Customer Success',     'bio' => 'Our customers\' biggest champion. Knows every account by name.', 'image' => 'https://picsum.photos/seed/team5/400/400'],
                ['name' => 'Chloe Baptiste', 'role' => 'Marketing',            'bio' => 'Writes most of what you read. Also the best at naming things.', 'image' => 'https://picsum.photos/seed/team6/400/400'],
            ],
        ], 2);

        $this->addSection($page, 'testimonials', [
            'heading' => 'What people say about working here',
            'items'   => [
                ['quote' => 'Best team I\'ve ever worked on. We ship fast, we care about quality, and we actually like each other.', 'author' => 'Mia Kowalski', 'role' => 'CTO', 'image' => 'https://picsum.photos/seed/team2/80/80'],
                ['quote' => 'Fully remote and it works because we have the right culture, not just the right tools.', 'author' => 'Ravi Sharma', 'role' => 'Customer Success', 'image' => 'https://picsum.photos/seed/team5/80/80'],
            ],
        ], 3);
    }

    // -------------------------------------------------------------------------
    // Careers — Page header + Content + Stats + CTA
    // -------------------------------------------------------------------------

    private function seedCareersSections(Page $page): void
    {
        $this->addSection($page, 'page-header', [
            'heading'    => 'Work with us',
            'subheading' => 'We\'re always looking for great people.',
        ], 1);

        $this->addSection($page, 'content', [
            'body' => '<h2>How we hire</h2><p>We don\'t do whiteboard interviews or trick questions. We want to see how you actually work: a short paid project, a conversation about your experience, and an honest discussion about what matters to you.</p><h2>Open roles</h2><p>We don\'t always have open roles listed, but we\'re always interested in hearing from great engineers, designers, and writers. Send us a note at <a href="mailto:jobs@example.com">jobs@example.com</a>.</p>',
        ], 2);

        $this->addSection($page, 'stats', [
            'items' => [
                ['value' => '4 weeks', 'label' => 'Paid onboarding',    'description' => 'No sink or swim'],
                ['value' => '$3k',     'label' => 'Home office budget',  'description' => 'Set up your space properly'],
                ['value' => '4 day',   'label' => 'Summer Fridays',      'description' => 'June through August'],
                ['value' => 'Async',   'label' => 'Work style',          'description' => 'Meetings by exception, not default'],
            ],
        ], 3);

        $this->addSection($page, 'cta', [
            'heading'   => 'Sound like your kind of place?',
            'body'      => '<p>We\'d love to hear from you even if there isn\'t an open role right now.</p>',
            'cta_label' => 'Say hello',
            'cta_url'   => '/contact',
            'style'     => 'light',
        ], 4);
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
                'tagline'    => 'The all-in-one platform for modern teams.',
                'copyright'  => '© ' . date('Y') . ' Peppermint. All rights reserved.',
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-links',
            'order' => 2,
            'data'  => [
                'heading' => 'Product',
                'links'   => [
                    ['label' => 'Features', 'url' => '/features'],
                    ['label' => 'Pricing',  'url' => '/pricing'],
                    ['label' => 'Blog',     'url' => '/blogs'],
                    ['label' => 'Changelog','url' => '/changelog'],
                ],
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-links',
            'order' => 3,
            'data'  => [
                'heading' => 'Company',
                'links'   => [
                    ['label' => 'About',   'url' => '/about'],
                    ['label' => 'Team',    'url' => '/team'],
                    ['label' => 'Careers', 'url' => '/careers'],
                    ['label' => 'Contact', 'url' => '/contact'],
                ],
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-social',
            'order' => 4,
            'data'  => [
                'items' => [
                    ['platform' => 'Twitter',  'url' => 'https://twitter.com'],
                    ['platform' => 'GitHub',   'url' => 'https://github.com'],
                    ['platform' => 'LinkedIn', 'url' => 'https://linkedin.com'],
                ],
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-newsletter',
            'order' => 5,
            'data'  => [
                'heading'      => 'Stay in the loop',
                'placeholder'  => 'Your email address',
                'button_label' => 'Subscribe',
            ],
        ]);

        FooterSection::create([
            'type'  => 'footer-text',
            'order' => 6,
            'data'  => [
                'body' => '<p>Built with care. We respect your privacy and will never share your data.</p>',
            ],
        ]);
    }
}
