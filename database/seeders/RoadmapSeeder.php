<?php

namespace Database\Seeders;

use App\Models\RoadmapCategory;
use App\Models\RoadmapItem;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use App\Models\Consumer;
use Illuminate\Database\Seeder;

class RoadmapSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::first();
        $consumer = Consumer::first();

        // ---- Categories -------------------------------------------------------

        $design = RoadmapCategory::create(['name' => 'Design',      'color' => '#8b5cf6', 'sort_order' => 0]);
        $dev    = RoadmapCategory::create(['name' => 'Development',  'color' => '#3b82f6', 'sort_order' => 1]);
        $growth = RoadmapCategory::create(['name' => 'Growth',       'color' => '#10b981', 'sort_order' => 2]);

        // ---- Roadmap items ----------------------------------------------------

        $items = [
            // Design — shipped
            [
                'title'            => 'New onboarding flow',
                'description'      => 'Redesign the first-run experience so new users reach value in under five minutes.',
                'status'           => 'shipped',
                'start_date'       => '2026-04-01',
                'date'             => '2026-04-28',
                'category_id'      => $design->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 0,
            ],
            // Design — in-progress
            [
                'title'            => 'Mobile-responsive admin',
                'description'      => 'Make the admin panel fully usable on phones and tablets.',
                'status'           => 'in-progress',
                'start_date'       => '2026-06-01',
                'date'             => '2026-07-31',
                'category_id'      => $design->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 1,
            ],
            // Design — planned
            [
                'title'            => 'Theming system',
                'description'      => 'Allow customers to set a primary colour and font family from settings.',
                'status'           => 'planned',
                'start_date'       => '2026-08-01',
                'date'             => '2026-09-30',
                'category_id'      => $design->id,
                'assigned_admin_id'=> null,
                'sort_order'       => 2,
            ],

            // Development — shipped
            [
                'title'            => 'Page builder',
                'description'      => 'Drag-and-drop block editor for building public pages without code.',
                'status'           => 'shipped',
                'start_date'       => '2026-01-15',
                'date'             => '2026-02-28',
                'category_id'      => $dev->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 3,
            ],
            [
                'title'            => 'Tasks & Kanban module',
                'description'      => 'Full task management with types, statuses, priorities, and a kanban board.',
                'status'           => 'shipped',
                'start_date'       => '2026-03-01',
                'date'             => '2026-04-15',
                'category_id'      => $dev->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 4,
            ],
            [
                'title'            => 'Roadmap module',
                'description'      => 'Plan and communicate what\'s coming with a public and private roadmap.',
                'status'           => 'shipped',
                'start_date'       => '2026-04-16',
                'date'             => '2026-05-15',
                'category_id'      => $dev->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 5,
            ],
            [
                'title'            => 'Calendar view',
                'description'      => 'Unified calendar showing tasks and roadmap items across a shared timeline.',
                'status'           => 'shipped',
                'start_date'       => '2026-05-16',
                'date'             => '2026-06-10',
                'category_id'      => $dev->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 6,
            ],
            // Development — in-progress
            [
                'title'            => 'REST API & webhooks',
                'description'      => 'Public API with webhook support so customers can integrate with their own tools.',
                'status'           => 'in-progress',
                'start_date'       => '2026-06-15',
                'date'             => '2026-08-15',
                'category_id'      => $dev->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 7,
            ],
            // Development — planned
            [
                'title'            => 'Global search',
                'description'      => 'Cmd-K search across pages, tasks, blog posts, and roadmap items.',
                'status'           => 'planned',
                'start_date'       => '2026-09-01',
                'date'             => '2026-10-15',
                'category_id'      => $dev->id,
                'assigned_admin_id'=> null,
                'sort_order'       => 8,
            ],

            // Growth — shipped
            [
                'title'            => 'Public roadmap page',
                'description'      => 'Let visitors see what\'s coming — an embeddable public roadmap with vote support.',
                'status'           => 'shipped',
                'start_date'       => '2026-05-20',
                'date'             => '2026-06-05',
                'category_id'      => $growth->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 9,
            ],
            // ── Archived items (shipped > 1 year ago) ────────────────────────
            [
                'title'            => 'Blog module',
                'description'      => 'Full blogging system with categories, drafts, scheduling, and a public RSS feed.',
                'status'           => 'shipped',
                'start_date'       => '2024-06-01',
                'date'             => '2024-07-15',
                'category_id'      => $dev->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 11,
            ],
            [
                'title'            => 'Consumer portal MVP',
                'description'      => 'First version of the client-facing portal — login, task list, and basic profile.',
                'status'           => 'shipped',
                'start_date'       => '2024-09-01',
                'date'             => '2024-11-30',
                'category_id'      => $dev->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 12,
            ],
            [
                'title'            => 'Brand & logo refresh',
                'description'      => 'Updated colour palette, typography scale, and logo across all public-facing surfaces.',
                'status'           => 'shipped',
                'start_date'       => '2025-01-10',
                'date'             => '2025-02-28',
                'category_id'      => $design->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 13,
            ],
            [
                'title'            => 'Contact form & submissions inbox',
                'description'      => 'Public contact form with admin inbox, read/unread tracking, and email forwarding.',
                'status'           => 'shipped',
                'start_date'       => '2024-11-01',
                'date'             => '2024-12-20',
                'category_id'      => $dev->id,
                'assigned_admin_id'=> $admin?->id,
                'sort_order'       => 14,
            ],

            // Growth — planned
            [
                'title'            => 'Referral programme',
                'description'      => 'Give customers a referral link and reward them when a friend converts.',
                'status'           => 'planned',
                'start_date'       => '2026-10-01',
                'date'             => '2026-11-30',
                'category_id'      => $growth->id,
                'assigned_admin_id'=> null,
                'sort_order'       => 10,
            ],
        ];

        $roadmapRows = [];
        foreach ($items as $data) {
            $roadmapRows[] = RoadmapItem::create($data);
        }

        // ---- Demo tasks linked to roadmap items ------------------------------
        // Relies on TaskSeeder having already run (types + statuses exist)

        $typeFeature     = TaskType::where('name', 'Feature')->first();
        $typeBug         = TaskType::where('name', 'Bug')->first();
        $typeImprovement = TaskType::where('name', 'Improvement')->first();

        $statusBacklog    = TaskStatus::where('name', 'Backlog')->first();
        $statusTodo       = TaskStatus::where('name', 'To Do')->first();
        $statusInProgress = TaskStatus::where('name', 'In Progress')->first();
        $statusInReview   = TaskStatus::where('name', 'In Review')->first();
        $statusDone       = TaskStatus::where('name', 'Done')->first();
        $statusCancelled  = TaskStatus::where('name', 'Cancelled')->first();

        $mobileRoadmapItem = collect($roadmapRows)->firstWhere('title', 'Mobile-responsive admin');
        $apiRoadmapItem    = collect($roadmapRows)->firstWhere('title', 'REST API & webhooks');
        $searchRoadmapItem = collect($roadmapRows)->firstWhere('title', 'Global search');
        $themingRoadmapItem = collect($roadmapRows)->firstWhere('title', 'Theming system');

        $tasks = [
            [
                'title'             => 'Sidebar collapses on tablet width',
                'description'       => 'When the browser is resized to ~768 px the sidebar overlaps the content area instead of collapsing.',
                'type_id'           => $typeBug?->id,
                'status_id'         => $statusInProgress?->id,
                'priority'          => 'high',
                'due_date'          => '2026-07-15',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => null,
                'roadmap_item_id'   => $mobileRoadmapItem?->id,
                'notes'             => 'Reproducible on Chrome and Safari. Not an issue on Firefox.',
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Nav links should be hidden on mobile',
                'description'       => 'The top navigation should collapse into a hamburger menu below 640px.',
                'type_id'           => $typeImprovement?->id,
                'status_id'         => $statusTodo?->id,
                'priority'          => 'medium',
                'due_date'          => '2026-07-20',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => null,
                'roadmap_item_id'   => $mobileRoadmapItem?->id,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Touch targets too small on kanban cards',
                'description'       => 'Card action buttons are 24px — iOS requires at least 44px touch targets.',
                'type_id'           => $typeBug?->id,
                'status_id'         => $statusBacklog?->id,
                'priority'          => 'low',
                'due_date'          => '2026-08-01',
                'assigned_admin_id' => null,
                'consumer_id'       => null,
                'roadmap_item_id'   => $mobileRoadmapItem?->id,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Design webhook event payload schema',
                'description'       => 'Define the JSON shape for task.created, task.updated, roadmap.shipped, and blog.published events.',
                'type_id'           => $typeFeature?->id,
                'status_id'         => $statusInReview?->id,
                'priority'          => 'high',
                'due_date'          => '2026-07-12',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => null,
                'roadmap_item_id'   => $apiRoadmapItem?->id,
                'notes'             => 'Schema doc in Notion. Awaiting review from the API team.',
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Build webhook delivery queue',
                'description'       => 'Use a background job to fan out webhook events with retries and exponential backoff.',
                'type_id'           => $typeFeature?->id,
                'status_id'         => $statusTodo?->id,
                'priority'          => 'high',
                'due_date'          => '2026-07-25',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => null,
                'roadmap_item_id'   => $apiRoadmapItem?->id,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'API rate limiting middleware',
                'description'       => 'Throttle public API endpoints to 120 req/min per API key.',
                'type_id'           => $typeFeature?->id,
                'status_id'         => $statusBacklog?->id,
                'priority'          => 'medium',
                'due_date'          => '2026-08-10',
                'assigned_admin_id' => null,
                'consumer_id'       => null,
                'roadmap_item_id'   => $apiRoadmapItem?->id,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Add Cmd-K shortcut to open search',
                'description'       => 'Wire up the keyboard shortcut globally so it works from any admin page.',
                'type_id'           => $typeFeature?->id,
                'status_id'         => $statusBacklog?->id,
                'priority'          => 'medium',
                'due_date'          => '2026-09-15',
                'assigned_admin_id' => null,
                'consumer_id'       => null,
                'roadmap_item_id'   => $searchRoadmapItem?->id,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Index pages, tasks, and blogs in search',
                'description'       => 'Create a unified search index that spans pages, tasks, blog posts, and roadmap items.',
                'type_id'           => $typeFeature?->id,
                'status_id'         => $statusBacklog?->id,
                'priority'          => 'medium',
                'due_date'          => '2026-09-25',
                'assigned_admin_id' => null,
                'consumer_id'       => null,
                'roadmap_item_id'   => $searchRoadmapItem?->id,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'New website quote',
                'description'       => 'Putting together a quote for a 5-page website with a contact form and blog. Let me know if you have any questions.',
                'type_id'           => $typeFeature?->id,
                'status_id'         => $statusTodo?->id,
                'priority'          => 'high',
                'due_date'          => '2026-07-14',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => $consumer?->id,
                'roadmap_item_id'   => null,
                'notes'             => 'Client confirmed they want the customer portal add-on too.',
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Homepage design review',
                'description'       => 'Draft homepage design is ready for your feedback. Please review and let me know any changes.',
                'type_id'           => $typeImprovement?->id,
                'status_id'         => $statusInProgress?->id,
                'priority'          => 'medium',
                'due_date'          => '2026-07-18',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => $consumer?->id,
                'roadmap_item_id'   => null,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Domain & hosting setup',
                'description'       => 'Setting up hosting and pointing your domain. No action needed from you — I\'ll update this once it\'s live.',
                'type_id'           => $typeFeature?->id,
                'status_id'         => $statusDone?->id,
                'priority'          => 'high',
                'due_date'          => '2026-06-30',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => $consumer?->id,
                'roadmap_item_id'   => null,
                'notes'             => 'Domain propagated successfully. SSL active.',
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Consumer portal: fix task count badge',
                'description'       => 'The unread task badge on the consumer dashboard shows the wrong count after a task is closed.',
                'type_id'           => $typeBug?->id,
                'status_id'         => $statusDone?->id,
                'priority'          => 'high',
                'due_date'          => '2026-06-28',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => $consumer?->id,
                'roadmap_item_id'   => null,
                'notes'             => 'Fixed by excluding closed statuses from the count query.',
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Add gallery section to About page',
                'description'       => 'You mentioned wanting a photo gallery on the About page. I\'ve added a gallery block — send over your photos when ready.',
                'type_id'           => $typeImprovement?->id,
                'status_id'         => $statusBacklog?->id,
                'priority'          => 'low',
                'due_date'          => '2026-07-25',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => $consumer?->id,
                'roadmap_item_id'   => null,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Blog: scheduled posts not publishing at midnight UTC',
                'description'       => 'Posts scheduled for midnight are publishing up to 45 minutes late.',
                'type_id'           => $typeBug?->id,
                'status_id'         => $statusDone?->id,
                'priority'          => 'medium',
                'due_date'          => '2026-06-20',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => null,
                'roadmap_item_id'   => null,
                'notes'             => 'Cron was running every 15 min — switched to every minute.',
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Add colour picker to theming settings',
                'description'       => 'Let admins pick a primary brand colour directly from settings. Use CSS custom properties.',
                'type_id'           => $typeFeature?->id,
                'status_id'         => $statusBacklog?->id,
                'priority'          => 'low',
                'due_date'          => '2026-08-20',
                'assigned_admin_id' => null,
                'consumer_id'       => null,
                'roadmap_item_id'   => $themingRoadmapItem?->id,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Task drawer: notes field loses focus on autosave',
                'description'       => 'Typing in the notes textarea triggers an autosave that re-renders the component, losing cursor position.',
                'type_id'           => $typeBug?->id,
                'status_id'         => $statusInProgress?->id,
                'priority'          => 'medium',
                'due_date'          => '2026-07-18',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => null,
                'roadmap_item_id'   => null,
                'notes'             => 'Debounce + v-model instead of reactive watcher should fix it.',
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Email notification when task is assigned',
                'description'       => 'Send the assigned admin a brief email when a task is assigned or reassigned to them.',
                'type_id'           => $typeFeature?->id,
                'status_id'         => $statusTodo?->id,
                'priority'          => 'medium',
                'due_date'          => '2026-07-30',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => null,
                'roadmap_item_id'   => null,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Improve empty state illustrations',
                'description'       => 'Replace plain text empty states with illustrated SVGs across tasks, blog, and roadmap.',
                'type_id'           => $typeImprovement?->id,
                'status_id'         => $statusBacklog?->id,
                'priority'          => 'low',
                'due_date'          => '2026-09-01',
                'assigned_admin_id' => null,
                'consumer_id'       => null,
                'roadmap_item_id'   => null,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
            [
                'title'             => 'Onboarding checklist for new admins',
                'description'       => 'Show a dismissible checklist (add a page, write a post, invite a team member) on first login.',
                'type_id'           => $typeFeature?->id,
                'status_id'         => $statusTodo?->id,
                'priority'          => 'medium',
                'due_date'          => '2026-07-28',
                'assigned_admin_id' => $admin?->id,
                'consumer_id'       => null,
                'roadmap_item_id'   => null,
                'notes'             => null,
                'created_by_admin_id' => $admin?->id,
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
