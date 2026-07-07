<?php

return [

    'frontend_url' => rtrim(env('FRONTEND_URL', env('APP_URL', 'http://localhost')), '/'),

    'admin_slug' => strtolower(env('ADMIN_SLUG', 'peppermint')),

    'maintenance_mode' => (bool) env('MAINTENANCE_MODE', false),

    'maintenance_bypass_password' => env('MAINTENANCE_BYPASS_PASSWORD'),

    'max_pages' => env('MAX_PAGES') !== null ? (int) env('MAX_PAGES') : null,

    'modules' => [
        'admin'          => (bool) env('MODULE_ADMIN_ENABLED', false),
        'public'         => (bool) env('MODULE_PUBLIC_ENABLED', false),
        'blogs'          => (bool) env('MODULE_BLOGS_ENABLED', false),
        'consumers'      => (bool) env('MODULE_CONSUMERS_ENABLED', false),
        'public_login'   => (bool) env('MODULE_PUBLIC_LOGIN_ENABLED', false),
        'team'           => (bool) env('MODULE_TEAM_ENABLED', false),
        'settings'       => (bool) env('MODULE_SETTINGS_ENABLED', false),
        'pages'          => (bool) env('MODULE_PAGES_ENABLED', false),
        'tasks'          => (bool) env('MODULE_TASKS_ENABLED', false),
        'tasks_consumer' => (bool) env('MODULE_TASKS_CONSUMER_ENABLED', false),
        'roadmap'        => (bool) env('MODULE_ROADMAP_ENABLED', false),
        'roadmap_public' => (bool) env('MODULE_ROADMAP_PUBLIC_ENABLED', false),
    ],

];
