<?php

use App\Models\User;
use App\Support\Admin\OptionSources\OptionSourceRegistry;
use App\Support\Admin\OptionSources\Users;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure the users source is registered
    if (! OptionSourceRegistry::has('users')) {
        OptionSourceRegistry::register('users', Users::class);
    }
});

function makeAdmin(): User
{
    return User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);
}

it('returns 404 for unknown option source', function () {
    $admin = makeAdmin();
    $this->actingAs($admin)
        ->getJson(route('admin.api.options', 'nonexistent_resource') . '?q=test')
        ->assertStatus(404);
});

it('returns empty results when query is less than 2 chars', function () {
    $admin = makeAdmin();
    $this->actingAs($admin)
        ->getJson(route('admin.api.options', 'users') . '?q=a')
        ->assertStatus(200)
        ->assertJson(['results' => []]);
});

it('returns results when query is at least 2 chars', function () {
    User::factory()->create(['name' => 'Alice Nguyen', 'role' => 'customer']);
    $admin = makeAdmin();
    $this->actingAs($admin)
        ->getJson(route('admin.api.options', 'users') . '?q=Ali')
        ->assertStatus(200)
        ->assertJsonStructure(['results' => [['id', 'text']]]);
});

it('limits results to 50 maximum', function () {
    User::factory()->count(60)->sequence(fn ($seq) => ['name' => 'TestUser ' . $seq->index, 'role' => 'customer'])->create();
    $admin = makeAdmin();
    $response = $this->actingAs($admin)
        ->getJson(route('admin.api.options', 'users') . '?q=TestUser')
        ->assertStatus(200);

    $results = $response->json('results');
    expect(count($results))->toBeLessThanOrEqual(50);
});

it('users source does not expose email or phone', function () {
    User::factory()->create(['name' => 'Secret User', 'email' => 'secret@example.com', 'role' => 'customer']);
    $admin = makeAdmin();
    $response = $this->actingAs($admin)
        ->getJson(route('admin.api.options', 'users') . '?q=Secret')
        ->assertStatus(200);

    $results = $response->json('results');
    foreach ($results as $item) {
        expect($item)->not->toHaveKey('email');
        expect($item)->not->toHaveKey('phone');
    }
});

it('guest cannot access options endpoint', function () {
    $this->getJson(route('admin.api.options', 'users') . '?q=test')
        ->assertRedirectToRoute('admin.login');
});
