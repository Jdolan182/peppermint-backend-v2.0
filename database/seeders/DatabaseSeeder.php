<?php

namespace Database\Seeders;

use App\Models\Consumer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // forceCreate (not factories): factories pull in Faker, which is a
        // dev-only dependency and absent on production installs.

        // Credentials come from config/seed.php (SEED_ADMIN_* in .env) —
        // read via config(), never env(), so they survive config:cache.
        $password = config('seed.admin_password');

        if (app()->isProduction() && $password === 'password') {
            throw new RuntimeException(
                'Refusing to seed a production install with the default admin password. '
                . 'Set SEED_ADMIN_PASSWORD in .env (and run config:cache if config is cached).'
            );
        }

        User::forceCreate([
            'name'              => 'Admin User',
            'email'             => config('seed.admin_email'),
            'email_verified_at' => now(),
            'password'          => Hash::make($password),
            'is_default'        => true,
            'notify_contact'    => true,
        ]);

        // Demo account advertised in the "Try the demo" box on the consumer
        // login page (shown only when VITE_DEMO_MODE=true on the frontend).
        // Credentials must match Login.vue. Undeletable via is_default.
        Consumer::forceCreate([
            'name'              => 'Demo Customer',
            'email'             => 'demo@example.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
            'is_default'        => true,
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
