<?php

use App\Models\FooterSection;

test('public footer index returns all footer sections', function () {
    FooterSection::factory(3)->create();

    $this->getJson('/api/public/footer')
        ->assertOk()
        ->assertJsonCount(3);
});

test('public footer index returns sections ordered by order', function () {
    $second = FooterSection::factory()->create(['order' => 2]);
    $first = FooterSection::factory()->create(['order' => 1]);

    $response = $this->getJson('/api/public/footer')->assertOk();

    expect($response->json('0.id'))->toBe($first->id)
        ->and($response->json('1.id'))->toBe($second->id);
});

test('public footer index returns empty array when no sections exist', function () {
    $this->getJson('/api/public/footer')
        ->assertOk()
        ->assertExactJson([]);
});
