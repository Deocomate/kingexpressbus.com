<?php

use App\Models\User;
use App\Models\WebProfile;
use App\Support\ClientCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

// ─── Helpers ────────────────────────────────────────────────────────────────

function adminUser(): User
{
    return User::factory()->admin()->create();
}

// ─── Index page ─────────────────────────────────────────────────────────────

it('shows web profile index with section tabs', function () {
    $admin = adminUser();
    WebProfile::factory()->default()->create(['profile_name' => 'Main Profile']);

    $this->actingAs($admin)
        ->get(route('admin.website.index', ['section' => 'profile']))
        ->assertOk()
        ->assertSee('Main Profile');
});

it('defaults section to profile when no query param given', function () {
    $this->actingAs(adminUser())
        ->get(route('admin.website.index'))
        ->assertOk();
});

// ─── Create + store ──────────────────────────────────────────────────────────

it('renders the create profile form', function () {
    $this->actingAs(adminUser())
        ->get(route('admin.website.profiles.create'))
        ->assertOk();
});

it('stores a new web profile and redirects', function () {
    $admin = adminUser();

    $this->actingAs($admin)
        ->post(route('admin.website.profiles.store'), [
            'profile_name' => 'New Profile',
            'is_default'   => false,
            'email'        => 'hello@example.com',
        ])
        ->assertRedirect(route('admin.website.index', ['section' => 'profile']));

    expect(WebProfile::where('profile_name', 'New Profile')->exists())->toBeTrue();
});

it('validates required fields on store', function () {
    $this->actingAs(adminUser())
        ->post(route('admin.website.profiles.store'), [])
        ->assertSessionHasErrors('profile_name');
});

// ─── Edit + update ───────────────────────────────────────────────────────────

it('renders the edit profile form', function () {
    $profile = WebProfile::factory()->create(['profile_name' => 'Edit Me']);

    $this->actingAs(adminUser())
        ->get(route('admin.website.profiles.edit', $profile))
        ->assertOk()
        ->assertSee('Edit Me');
});

it('updates a profile and redirects back to edit form', function () {
    $profile = WebProfile::factory()->create(['profile_name' => 'Old Name']);

    $this->actingAs(adminUser())
        ->put(route('admin.website.profiles.update', $profile), [
            'profile_name' => 'New Name',
            'is_default'   => false,
        ])
        ->assertRedirect(route('admin.website.profiles.edit', $profile));

    expect($profile->fresh()->profile_name)->toBe('New Name');
});

// ─── is_default business rules ───────────────────────────────────────────────

it('switching is_default demotes other profiles and flushes cache', function () {
    $default = WebProfile::factory()->default()->create();
    $other   = WebProfile::factory()->create(['profile_name' => 'Other', 'is_default' => false]);

    // Seed the cache with a fake value to verify it gets cleared
    Cache::put(ClientCache::WEB_PROFILE, 'fake', 3600);
    expect(Cache::has(ClientCache::WEB_PROFILE))->toBeTrue();

    $this->actingAs(adminUser())
        ->post(route('admin.website.profiles.setDefault', $other))
        ->assertRedirect(route('admin.website.index', ['section' => 'profile']));

    expect($other->fresh()->is_default)->toBeTrue()
        ->and($default->fresh()->is_default)->toBeFalse()
        ->and(Cache::has(ClientCache::WEB_PROFILE))->toBeFalse();
});

it('setting is_default via update also flushes cache', function () {
    $a = WebProfile::factory()->default()->create();
    $b = WebProfile::factory()->create(['is_default' => false]);

    Cache::put(ClientCache::WEB_PROFILE, 'fake', 3600);

    $this->actingAs(adminUser())
        ->put(route('admin.website.profiles.update', $b), [
            'profile_name' => $b->profile_name,
            'is_default'   => true,
        ])
        ->assertRedirect();

    expect(Cache::has(ClientCache::WEB_PROFILE))->toBeFalse();
});

// ─── Delete rules ────────────────────────────────────────────────────────────

it('cannot delete the default profile when other profiles exist', function () {
    $default = WebProfile::factory()->default()->create();
    WebProfile::factory()->create(); // another profile exists

    $this->actingAs(adminUser())
        ->delete(route('admin.website.profiles.destroy', $default))
        ->assertRedirect(route('admin.website.index', ['section' => 'profile']))
        ->assertSessionHas('error');

    expect(WebProfile::find($default->id))->not->toBeNull();
});

it('can delete the default profile when it is the only one', function () {
    $default = WebProfile::factory()->default()->create();

    $this->actingAs(adminUser())
        ->delete(route('admin.website.profiles.destroy', $default))
        ->assertRedirect(route('admin.website.index', ['section' => 'profile']))
        ->assertSessionHas('success');

    expect(WebProfile::find($default->id))->toBeNull();
});

it('can delete a non-default profile regardless', function () {
    WebProfile::factory()->default()->create();
    $other = WebProfile::factory()->create(['is_default' => false]);

    $this->actingAs(adminUser())
        ->delete(route('admin.website.profiles.destroy', $other))
        ->assertRedirect();

    expect(WebProfile::find($other->id))->toBeNull();
});

// ─── map_embedded preserves raw <iframe> ────────────────────────────────────

it('stores map_embedded raw iframe without sanitising', function () {
    $iframe = '<iframe src="https://maps.google.com/maps?q=hanoi" width="600" height="450" frameborder="0"></iframe>';

    $admin = adminUser();
    $response = $this->actingAs($admin)
        ->post(route('admin.website.profiles.store'), [
            'profile_name' => 'Map Test',
            'is_default'   => false,
            'map_embedded' => $iframe,
        ])
        ->assertRedirect();

    $profile = WebProfile::where('profile_name', 'Map Test')->first();
    expect($profile->map_embedded)->toBe($iframe);
});
