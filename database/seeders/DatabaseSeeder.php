<?php

namespace Database\Seeders;

use App\Models\Consumer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Password configurable via SEED_ADMIN_PASSWORD in .env
        User::factory()->create([
            'name'       => 'Admin User',
            'email'      => env('SEED_ADMIN_EMAIL', 'admin@example.com'),
            'password'   => Hash::make(env('SEED_ADMIN_PASSWORD', 'password')),
            'is_default' => true,
            'notify_contact' => true,
        ]);

        // Demo account advertised in the "Try the demo" box on the consumer
        // login page (shown only when VITE_DEMO_MODE=true on the frontend).
        // Credentials must match Login.vue. Undeletable via is_default.
        Consumer::factory()->create([
            'name'       => 'Demo Customer',
            'email'      => 'demo@example.com',
            'password'   => Hash::make('password'),
            'is_default' => true,
        ]);

        $this->call([
            SettingsSeeder::class,
            TaskSeeder::class,
            RoadmapSeeder::class,
            BlogSeeder::class,
            PageSeeder::class,
        ]);
    }
}
