<?php

namespace Database\Seeders;

use App\Models\Consumer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        Consumer::factory()->create([
            'name' => 'Test Consumer',
            'email' => 'consumer@example.com',
        ]);
    }
}
