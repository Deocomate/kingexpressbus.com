<?php

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// ─── helpers ────────────────────────────────────────────────────────────────

function adminUserForTrips(): User
{
    return User::factory()->admin()->create();
}

function makeTripFixture(bool $isActive = true, int $priority = 0): array
{
    $route = Route::factory()->create();
    $bus   = Bus::factory()->create();
    $trip  = Trip::factory()->create([
        'route_id'  => $route->id,
        'bus_id'    => $bus->id,
        'is_active' => $isActive,
        'priority'  => $priority,
    ]);

    return compact('route', 'bus', 'trip');
}

// ─── index / tabs ───────────────────────────────────────────────────────────

it('trips index page returns 200', function () {
    $this->actingAs(adminUserForTrips())
        ->get(route('admin.trips.index'))
        ->assertOk()
        ->assertViewIs('admin.trips.index');
});

it('default tab is all and shows all trips', function () {
    makeTripFixture(true);
    makeTripFixture(false);

    $response = $this->actingAs(adminUserForTrips())
        ->get(route('admin.trips.index'));

    $response->assertOk();
    $response->assertViewHas('activeTab', 'all');
    $response->assertViewHas('section', 'trips');
});

it('active tab filters to only active trips', function () {
    makeTripFixture(true);
    makeTripFixture(false);

    $response = $this->actingAs(adminUserForTrips())
        ->get(route('admin.trips.index', ['tab' => 'active']));

    $response->assertOk();
    $response->assertViewHas('activeTab', 'active');

    $paginator = $response->viewData('paginator');
    expect($paginator->total())->toBe(1);
    expect($paginator->items()[0]->is_active)->toBeTrue();
});

it('inactive tab filters to only inactive trips', function () {
    makeTripFixture(true);
    makeTripFixture(false);

    $response = $this->actingAs(adminUserForTrips())
        ->get(route('admin.trips.index', ['tab' => 'inactive']));

    $response->assertOk();
    $response->assertViewHas('activeTab', 'inactive');

    $paginator = $response->viewData('paginator');
    expect($paginator->total())->toBe(1);
    expect((bool) $paginator->items()[0]->is_active)->toBeFalse();
});

it('tab badges reflect real counts', function () {
    makeTripFixture(true);
    makeTripFixture(true);
    makeTripFixture(false);

    $response = $this->actingAs(adminUserForTrips())
        ->get(route('admin.trips.index'));

    $badges = $response->viewData('tabBadges');
    expect($badges['all'])->toBe(3)
        ->and($badges['active'])->toBe(2)
        ->and($badges['inactive'])->toBe(1);
});

it('section blocks shows blocks section', function () {
    $response = $this->actingAs(adminUserForTrips())
        ->get(route('admin.trips.index', ['section' => 'blocks']));

    $response->assertOk();
    $response->assertViewHas('section', 'blocks');
});

it('route filter restricts results to that route', function () {
    $fx1 = makeTripFixture();
    $fx2 = makeTripFixture();

    $response = $this->actingAs(adminUserForTrips())
        ->get(route('admin.trips.index', ['filter' => ['route_id' => $fx1['route']->id]]));

    $response->assertOk();
    $paginator = $response->viewData('paginator');
    expect($paginator->total())->toBe(1);
    expect($paginator->items()[0]->route_id)->toBe($fx1['route']->id);
});

it('trips are grouped by route name', function () {
    $fx1 = makeTripFixture();
    $fx2 = makeTripFixture();

    $response = $this->actingAs(adminUserForTrips())
        ->get(route('admin.trips.index'));

    $grouped = $response->viewData('grouped');
    // Should have 2 groups (one per route)
    expect($grouped->count())->toBe(2);
});

// ─── toggle active ──────────────────────────────────────────────────────────

it('toggle active flips is_active to false and returns JSON', function () {
    $fx   = makeTripFixture(true);
    $trip = $fx['trip'];

    $response = $this->actingAs(adminUserForTrips())
        ->patch(route('admin.trips.toggle-active', $trip->id));

    $response->assertOk()->assertJson(['is_active' => false]);
    expect($trip->fresh()->is_active)->toBeFalse();
});

it('toggle active flips is_active to true and returns JSON', function () {
    $fx   = makeTripFixture(false);
    $trip = $fx['trip'];

    $response = $this->actingAs(adminUserForTrips())
        ->patch(route('admin.trips.toggle-active', $trip->id));

    $response->assertOk()->assertJson(['is_active' => true]);
    expect($trip->fresh()->is_active)->toBeTrue();
});

// ─── CRUD ───────────────────────────────────────────────────────────────────

it('store creates trip and redirects', function () {
    $route = Route::factory()->create();
    $bus   = Bus::factory()->create();

    $this->actingAs(adminUserForTrips())
        ->post(route('admin.trips.store'), [
            'route_id'   => $route->id,
            'bus_id'     => $bus->id,
            'start_time' => '08:00',
            'end_time'   => '12:00',
            'price'      => '250000',
            'is_active'  => '1',
            'priority'   => '10',
        ])
        ->assertRedirect(route('admin.trips.index'))
        ->assertSessionHas('success');

    expect(DB::table('trips')->count())->toBe(1);
});

it('store rejects same start and end time', function () {
    $route = Route::factory()->create();
    $bus   = Bus::factory()->create();

    $this->actingAs(adminUserForTrips())
        ->post(route('admin.trips.store'), [
            'route_id'   => $route->id,
            'bus_id'     => $bus->id,
            'start_time' => '08:00',
            'end_time'   => '08:00',
            'price'      => '250000',
            'is_active'  => '1',
            'priority'   => '0',
        ])
        ->assertSessionHasErrors('end_time');
});

// ─── bulk delete guard ───────────────────────────────────────────────────────

it('bulk delete blocked when trips have bookings', function () {
    $fx   = makeTripFixture();
    $trip = $fx['trip'];

    Booking::factory()->create(['trip_id' => $trip->id]);

    $this->actingAs(adminUserForTrips())
        ->delete(route('admin.trips.bulk-destroy'), ['ids' => [$trip->id]])
        ->assertSessionHas('error');

    // Booking row not deleted, trip still exists
    expect(DB::table('bookings')->count())->toBe(1);
    expect(DB::table('trips')->count())->toBe(1);
});

it('bulk delete succeeds when trips have no bookings', function () {
    $fx1 = makeTripFixture();
    $fx2 = makeTripFixture();

    $this->actingAs(adminUserForTrips())
        ->delete(route('admin.trips.bulk-destroy'), ['ids' => [$fx1['trip']->id, $fx2['trip']->id]])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(DB::table('trips')->count())->toBe(0);
});

// ─── trips-for-route JSON endpoint ──────────────────────────────────────────

it('tripsForRoute returns empty array when no route_id given', function () {
    $this->actingAs(adminUserForTrips())
        ->get(route('admin.trips.api.trips-for-route'))
        ->assertOk()
        ->assertJson(['results' => []]);
});

it('tripsForRoute returns only trips for the requested route', function () {
    $fx1 = makeTripFixture();
    $fx2 = makeTripFixture();

    $response = $this->actingAs(adminUserForTrips())
        ->get(route('admin.trips.api.trips-for-route', ['route_id' => $fx1['route']->id]));

    $response->assertOk();
    $data = $response->json('results');
    expect(count($data))->toBe(1);
    expect($data[0]['id'])->toBe($fx1['trip']->id);
});
