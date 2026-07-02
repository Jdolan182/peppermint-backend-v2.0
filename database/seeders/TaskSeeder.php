<?php

namespace Database\Seeders;

use App\Models\TaskStatus;
use App\Models\TaskType;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Feature',     'color' => '#6366f1', 'icon' => 'sparkles',      'is_appointment' => false, 'sort_order' => 0],
            ['name' => 'Bug',         'color' => '#ef4444', 'icon' => 'bug-ant',        'is_appointment' => false, 'sort_order' => 1],
            ['name' => 'Improvement', 'color' => '#f59e0b', 'icon' => 'arrow-trending-up', 'is_appointment' => false, 'sort_order' => 2],
            ['name' => 'Appointment', 'color' => '#10b981', 'icon' => 'calendar',       'is_appointment' => true,  'sort_order' => 3],
        ];

        foreach ($types as $type) {
            TaskType::firstOrCreate(['name' => $type['name']], $type);
        }

        $statuses = [
            ['name' => 'Backlog',     'color' => '#9ca3af', 'sort_order' => 0, 'is_default' => false, 'is_closed' => false],
            ['name' => 'To Do',       'color' => '#6b7280', 'sort_order' => 1, 'is_default' => true,  'is_closed' => false],
            ['name' => 'In Progress', 'color' => '#3b82f6', 'sort_order' => 2, 'is_default' => false, 'is_closed' => false],
            ['name' => 'In Review',   'color' => '#f59e0b', 'sort_order' => 3, 'is_default' => false, 'is_closed' => false],
            ['name' => 'Done',        'color' => '#10b981', 'sort_order' => 4, 'is_default' => false, 'is_closed' => true],
            ['name' => 'Cancelled',   'color' => '#ef4444', 'sort_order' => 5, 'is_default' => false, 'is_closed' => true],
        ];

        foreach ($statuses as $status) {
            TaskStatus::firstOrCreate(['name' => $status['name']], $status);
        }
    }
}
