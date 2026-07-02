<?php

namespace Database\Seeders;

use App\Models\Consumer;
use App\Models\RoadmapCategory;
use App\Models\RoadmapItem;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskTestSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::first();
        $consumer = Consumer::first();

        $types    = TaskType::orderBy('sort_order')->get()->keyBy('name');
        $statuses = TaskStatus::orderBy('sort_order')->get()->keyBy('name');

        // --- Roadmap categories ---
        $categoryData = [
            ['name' => 'Design',   'color' => '#8b5cf6', 'sort_order' => 0],
            ['name' => 'Backend',  'color' => '#3b82f6', 'sort_order' => 1],
            ['name' => 'Features', 'color' => '#10b981', 'sort_order' => 2],
        ];

        $cats = [];
        foreach ($categoryData as $c) {
            $cats[$c['name']] = RoadmapCategory::firstOrCreate(['name' => $c['name']], $c);
        }

        // --- Roadmap items ---
        $roadmapItems = [
            [
                'title'       => 'Consumer portal redesign',
                'description' => 'Full visual refresh of the consumer-facing portal. New layout, improved navigation, dark mode first.',
                'status'      => 'in-progress',
                'start_date'  => now()->subWeeks(2)->toDateString(),
                'date'        => now()->addMonths(2)->toDateString(),
                'category_id' => $cats['Design']->id,
                'assigned_admin_id' => $admin?->id,
                'sort_order'  => 0,
            ],
            [
                'title'       => 'API rate limiting',
                'description' => 'Add per-consumer rate limiting to all public API endpoints to prevent abuse.',
                'status'      => 'planned',
                'start_date'  => now()->addWeeks(2)->toDateString(),
                'date'        => now()->addMonths(3)->toDateString(),
                'category_id' => $cats['Backend']->id,
                'assigned_admin_id' => $admin?->id,
                'sort_order'  => 1,
            ],
            [
                'title'       => 'Email notifications',
                'description' => 'Transactional emails for task assignments, status changes, and appointment reminders.',
                'status'      => 'planned',
                'start_date'  => now()->addMonths(2)->addWeeks(1)->toDateString(),
                'date'        => now()->addMonths(4)->toDateString(),
                'category_id' => $cats['Backend']->id,
                'assigned_admin_id' => null,
                'sort_order'  => 2,
            ],
            [
                'title'       => 'Mobile-responsive admin',
                'description' => 'Make the admin panel fully usable on tablet and mobile screens.',
                'status'      => 'planned',
                'start_date'  => now()->addMonths(3)->addWeeks(2)->toDateString(),
                'date'        => now()->addMonths(5)->toDateString(),
                'category_id' => $cats['Design']->id,
                'assigned_admin_id' => null,
                'sort_order'  => 3,
            ],
            [
                'title'       => 'Dark mode for public site',
                'description' => 'System-preference-aware dark mode across all public pages.',
                'status'      => 'shipped',
                'start_date'  => now()->subMonths(2)->toDateString(),
                'date'        => now()->subMonth()->toDateString(),
                'category_id' => $cats['Design']->id,
                'assigned_admin_id' => $admin?->id,
                'sort_order'  => 4,
            ],
            [
                'title'       => 'Page builder v1',
                'description' => 'Drag-and-drop page builder with hero, text, CTA, newsletter, and contact blocks.',
                'status'      => 'shipped',
                'start_date'  => now()->subMonths(2)->addWeeks(1)->toDateString(),
                'date'        => now()->subWeeks(2)->toDateString(),
                'category_id' => $cats['Features']->id,
                'assigned_admin_id' => $admin?->id,
                'sort_order'  => 5,
            ],
        ];

        $created = [];
        foreach ($roadmapItems as $data) {
            $created[] = RoadmapItem::create($data);
        }

        [$redesign, $rateLimit, $emails, $mobile, , $pageBuilder] = $created;

        // --- Tasks ---
        $tasks = [
            // In Progress
            [
                'title'            => 'Design new dashboard layout',
                'description'      => 'Create Figma mockups for the redesigned consumer dashboard. Focus on clarity and reducing clicks to key actions.',
                'type_id'          => $types['Feature']->id,
                'status_id'        => $statuses['In Progress']->id,
                'priority'         => 'high',
                'due_date'         => now()->addDays(5)->toDateString(),
                'assigned_admin_id'=> $admin?->id,
                'consumer_id'      => null,
                'roadmap_item_id'  => $redesign->id,
                'created_by_admin_id' => $admin?->id,
                'notes'            => 'Check with Jordy on colour palette before finalising.',
            ],
            [
                'title'            => 'Implement token bucket algorithm',
                'description'      => 'Backend rate limiter using Redis token bucket. 60 req/min per consumer, 200 req/min per IP.',
                'type_id'          => $types['Feature']->id,
                'status_id'        => $statuses['In Progress']->id,
                'priority'         => 'high',
                'due_date'         => now()->addDays(10)->toDateString(),
                'assigned_admin_id'=> $admin?->id,
                'consumer_id'      => null,
                'roadmap_item_id'  => $rateLimit->id,
                'created_by_admin_id' => $admin?->id,
                'notes'            => null,
            ],
            // To Do
            [
                'title'            => 'Write Mailables for task assignment',
                'description'      => 'Laravel Mailable for when a task is assigned to a consumer. Include task title, due date, and a link to the consumer portal.',
                'type_id'          => $types['Feature']->id,
                'status_id'        => $statuses['To Do']->id,
                'priority'         => 'medium',
                'due_date'         => now()->addDays(14)->toDateString(),
                'assigned_admin_id'=> $admin?->id,
                'consumer_id'      => null,
                'roadmap_item_id'  => $emails->id,
                'created_by_admin_id' => $admin?->id,
                'notes'            => null,
            ],
            [
                'title'            => 'Fix nav overflow on small screens',
                'description'      => 'Sidebar collapses incorrectly below 768px. Nav items clip outside the viewport.',
                'type_id'          => $types['Bug']->id,
                'status_id'        => $statuses['To Do']->id,
                'priority'         => 'critical',
                'due_date'         => now()->addDays(2)->toDateString(),
                'assigned_admin_id'=> $admin?->id,
                'consumer_id'      => null,
                'roadmap_item_id'  => $mobile->id,
                'created_by_admin_id' => $admin?->id,
                'notes'            => 'Reproduced on iPhone 13 and Galaxy S22.',
            ],
            [
                'title'            => 'Add loading skeleton to consumer tasks list',
                'description'      => 'Replace the plain "Loading…" text with a proper skeleton UI.',
                'type_id'          => $types['Improvement']->id,
                'status_id'        => $statuses['To Do']->id,
                'priority'         => 'low',
                'due_date'         => now()->addDays(21)->toDateString(),
                'assigned_admin_id'=> null,
                'consumer_id'      => $consumer?->id,
                'roadmap_item_id'  => null,
                'created_by_admin_id' => $admin?->id,
                'notes'            => null,
            ],
            // Backlog
            [
                'title'            => 'Improve colour contrast in dark mode CTA block',
                'description'      => 'The CTA text on dark backgrounds is below WCAG AA contrast ratio in some theme configs.',
                'type_id'          => $types['Bug']->id,
                'status_id'        => $statuses['Backlog']->id,
                'priority'         => 'medium',
                'due_date'         => null,
                'assigned_admin_id'=> null,
                'consumer_id'      => null,
                'roadmap_item_id'  => null,
                'created_by_admin_id' => $admin?->id,
                'notes'            => null,
            ],
            [
                'title'            => 'Add CSV export to contact submissions',
                'description'      => 'Let admins download all contact submissions as a CSV from the Contact page.',
                'type_id'          => $types['Feature']->id,
                'status_id'        => $statuses['Backlog']->id,
                'priority'         => 'low',
                'due_date'         => null,
                'assigned_admin_id'=> null,
                'consumer_id'      => null,
                'roadmap_item_id'  => null,
                'created_by_admin_id' => $admin?->id,
                'notes'            => null,
            ],
            // In Review
            [
                'title'            => 'Audit page builder block accessibility',
                'description'      => 'Run axe on every block type and document any issues. Fix critical ones before v1 launch.',
                'type_id'          => $types['Improvement']->id,
                'status_id'        => $statuses['In Review']->id,
                'priority'         => 'high',
                'due_date'         => now()->addDays(3)->toDateString(),
                'assigned_admin_id'=> $admin?->id,
                'consumer_id'      => null,
                'roadmap_item_id'  => $pageBuilder->id,
                'created_by_admin_id' => $admin?->id,
                'notes'            => 'Hero block passes. Text block has a label issue.',
            ],
            // Appointment
            [
                'title'            => 'Onboarding call',
                'description'      => '30-minute onboarding call to walk through the platform.',
                'type_id'          => $types['Appointment']->id,
                'status_id'        => $statuses['To Do']->id,
                'priority'         => 'medium',
                'due_date'         => now()->addDays(7)->toDateString(),
                'assigned_admin_id'=> $admin?->id,
                'consumer_id'      => $consumer?->id,
                'roadmap_item_id'  => null,
                'created_by_admin_id' => $admin?->id,
                'notes'            => 'Consumer requested morning slot.',
            ],
            // Done
            [
                'title'            => 'Implement drag-to-reorder page sections',
                'description'      => 'Replace up/down buttons with vuedraggable in the page editor.',
                'type_id'          => $types['Feature']->id,
                'status_id'        => $statuses['Done']->id,
                'priority'         => 'medium',
                'due_date'         => now()->subDays(3)->toDateString(),
                'assigned_admin_id'=> $admin?->id,
                'consumer_id'      => null,
                'roadmap_item_id'  => $pageBuilder->id,
                'created_by_admin_id' => $admin?->id,
                'notes'            => null,
            ],
            [
                'title'            => 'Fix contact form mark-as-read bug',
                'description'      => 'read_at was missing from $fillable so update() silently failed.',
                'type_id'          => $types['Bug']->id,
                'status_id'        => $statuses['Done']->id,
                'priority'         => 'high',
                'due_date'         => now()->subDays(5)->toDateString(),
                'assigned_admin_id'=> $admin?->id,
                'consumer_id'      => null,
                'roadmap_item_id'  => null,
                'created_by_admin_id' => $admin?->id,
                'notes'            => null,
            ],
        ];

        foreach ($tasks as $data) {
            Task::create($data);
        }
    }
}
