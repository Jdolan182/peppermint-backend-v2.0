<?php

namespace Database\Seeders;

use App\Models\Consumer;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Feature',        'color' => '#6366f1', 'icon' => 'sparkles',           'is_appointment' => false, 'sort_order' => 0],
            ['name' => 'Bug',            'color' => '#ef4444', 'icon' => 'bug-ant',             'is_appointment' => false, 'sort_order' => 1],
            ['name' => 'Improvement',    'color' => '#f59e0b', 'icon' => 'arrow-trending-up',   'is_appointment' => false, 'sort_order' => 2],
            ['name' => 'Appointment',    'color' => '#10b981', 'icon' => 'calendar',            'is_appointment' => true,  'sort_order' => 3],
            ['name' => 'Design',         'color' => '#8b5cf6', 'icon' => 'paint-brush',         'is_appointment' => false, 'sort_order' => 4],
            ['name' => 'Research',       'color' => '#06b6d4', 'icon' => 'magnifying-glass',    'is_appointment' => false, 'sort_order' => 5],
            ['name' => 'Documentation',  'color' => '#64748b', 'icon' => 'document-text',       'is_appointment' => false, 'sort_order' => 6],
            ['name' => 'Testing',        'color' => '#16a34a', 'icon' => 'beaker',              'is_appointment' => false, 'sort_order' => 7],
            ['name' => 'Deployment',     'color' => '#dc2626', 'icon' => 'rocket-launch',       'is_appointment' => false, 'sort_order' => 8],
            ['name' => 'Support',        'color' => '#ea580c', 'icon' => 'lifebuoy',            'is_appointment' => false, 'sort_order' => 9],
            ['name' => 'Refactor',       'color' => '#0891b2', 'icon' => 'wrench',              'is_appointment' => false, 'sort_order' => 10],
            ['name' => 'Security',       'color' => '#b91c1c', 'icon' => 'shield-check',        'is_appointment' => false, 'sort_order' => 11],
        ];

        foreach ($types as $type) {
            TaskType::firstOrCreate(['name' => $type['name']], $type);
        }

        $statuses = [
            ['name' => 'Backlog',     'color' => '#9ca3af', 'sort_order' => 0,  'is_default' => false, 'is_closed' => false],
            ['name' => 'To Do',       'color' => '#6b7280', 'sort_order' => 1,  'is_default' => true,  'is_closed' => false],
            ['name' => 'In Progress', 'color' => '#3b82f6', 'sort_order' => 2,  'is_default' => false, 'is_closed' => false],
            ['name' => 'In Review',   'color' => '#f59e0b', 'sort_order' => 3,  'is_default' => false, 'is_closed' => false],
            ['name' => 'Testing',     'color' => '#8b5cf6', 'sort_order' => 4,  'is_default' => false, 'is_closed' => false],
            ['name' => 'Staging',     'color' => '#06b6d4', 'sort_order' => 5,  'is_default' => false, 'is_closed' => false],
            ['name' => 'Waiting',     'color' => '#f97316', 'sort_order' => 6,  'is_default' => false, 'is_closed' => false],
            ['name' => 'Blocked',     'color' => '#dc2626', 'sort_order' => 7,  'is_default' => false, 'is_closed' => false],
            ['name' => 'On Hold',     'color' => '#d97706', 'sort_order' => 8,  'is_default' => false, 'is_closed' => false],
            ['name' => 'Done',        'color' => '#10b981', 'sort_order' => 9,  'is_default' => false, 'is_closed' => true],
            ['name' => 'Cancelled',   'color' => '#ef4444', 'sort_order' => 10, 'is_default' => false, 'is_closed' => true],
            ['name' => 'Archived',    'color' => '#94a3b8', 'sort_order' => 11, 'is_default' => false, 'is_closed' => true],
        ];

        foreach ($statuses as $status) {
            TaskStatus::firstOrCreate(['name' => $status['name']], $status);
        }

        $typeIds     = TaskType::pluck('id')->toArray();
        $statusIds   = TaskStatus::pluck('id')->toArray();
        $adminIds    = User::pluck('id')->toArray();
        $consumerIds = Consumer::pluck('id')->toArray();

        $titles = [
            'Set up CI/CD pipeline', 'Fix login page redirect', 'Design new homepage hero',
            'Update API documentation', 'Implement dark mode toggle', 'Optimise database queries',
            'Write unit tests for auth module', 'Deploy to staging environment', 'Review pull request #42',
            'Fix broken image uploads', 'Add email notifications', 'Migrate legacy data',
            'Implement search functionality', 'Add export to CSV feature', 'Fix mobile navigation menu',
            'Update dependencies to latest versions', 'Add two-factor authentication', 'Write onboarding guide',
            'Performance audit and fixes', 'Implement file upload limits', 'Fix date formatting bug',
            'Create new consumer portal design', 'Add pagination to all tables', 'Review security headers',
            'Set up error monitoring', 'Fix broken contact form', 'Add Google Analytics',
            'Redesign settings page', 'Fix timezone handling', 'Implement role-based permissions',
            'Add webhook support', 'Fix payment gateway timeout', 'Update privacy policy page',
            'Add drag-and-drop file upload', 'Fix cross-browser compatibility', 'Create API rate limiting',
            'Refactor authentication logic', 'Add activity logging', 'Fix email template styling',
            'Implement real-time notifications', 'Update SSL certificate', 'Add keyboard shortcuts',
            'Fix memory leak in image processing', 'Add bulk actions to tables', 'Review and update sitemap',
            'Implement lazy loading for images', 'Fix session timeout issue', 'Add multi-language support',
            'Create backup restore workflow', 'Fix race condition in order processing',
        ];

        $priorities = ['low', 'medium', 'high', 'critical'];

        for ($i = 0; $i < 100; $i++) {
            Task::create([
                'title'             => $titles[$i % count($titles)],
                'type_id'           => $typeIds[array_rand($typeIds)],
                'status_id'         => $statusIds[array_rand($statusIds)],
                'priority'          => $priorities[array_rand($priorities)],
                'assigned_admin_id' => rand(0, 2) ? $adminIds[array_rand($adminIds)] : null,
                'consumer_id'       => rand(0, 2) ? $consumerIds[array_rand($consumerIds)] : null,
                'created_by_admin_id' => $adminIds[0],
                'due_date'          => rand(0, 1) ? now()->addDays(rand(-30, 90))->format('Y-m-d') : null,
            ]);
        }
    }
}
