<?php

// Seed-time credentials for the default admin account. These live in a
// config file (not read via env() in the seeder) so they survive
// `config:cache` — env() returns null everywhere else once config is cached.
return [
    'admin_email'    => env('SEED_ADMIN_EMAIL', 'admin@example.com'),
    'admin_password' => env('SEED_ADMIN_PASSWORD', 'password'),
];
