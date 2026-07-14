<?php

test('middleware passes request through when module is enabled', function () {
    // MODULE_ADMIN_ENABLED=true in phpunit.xml — admin login route uses module:admin
    $this->postJson('/api/admin/auth/login', ['email' => 'test@example.com', 'password' => 'password'])
        ->assertStatus(401); // module enabled, passes through, fails on bad credentials
});

test('middleware returns 404 when module env var is not set', function () {
    config(['peppermint.modules.blogs' => false]);

    try {
        $this->getJson('/api/public/blogs')
            ->assertStatus(404)
            ->assertJson(['message' => 'Module not available']);
    } finally {
        config(['peppermint.modules.blogs' => true]);
    }
});
