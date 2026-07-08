<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name'              => 'Fresh Build',
            'maintenance_enabled'    => 'false',
            'maintenance_message'    => "We'll be back shortly.",
            'blog_title'             => 'Blog',
            'blog_description'       => 'Tips, case studies, and updates for local businesses thinking about their online presence.',
            'module_blogs'           => 'true',
            'module_consumers'       => 'true',
            'module_public_login'    => 'true',
            'module_team'            => 'true',
            'module_public'          => 'true',
            'module_pages'           => 'true',
            'module_tasks'           => 'true',
            'module_tasks_consumer'  => 'true',
            'module_roadmap'         => 'true',
            'module_roadmap_public'  => 'true',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
