<?php

use App\Models\Bus;
use App\Models\Province;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Stop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function routeAdminUser(): User
{
    return User::factory()->admin()->create();
}

function makeRoutePayload(array $overrides = []): array
{
    $start = Province::factory()->create();
    $end   = Province::factory()->create();

    return array_merge([
        'province_start_id'      => $start->id,
        'province_end_id'        => $end->id,
        'name'                   => 'Hà Nội - Đà Nẵng',
        'slug'                   => 'ha-noi-da-nang',
        'title'                  => null,
        'description'            => null,
        'duration'               => '8 giờ',
        'distance_km'            => 800,
        'price_default'          => 250000,
        'available_hotel_pickup' => 0,
        'priority'               => 10,
        'content'                => '',
    ], $overrides);
}

// ---------------------------------------------------------------------------
// CRUD
// ---------------------------------------------------------------------------

it('creates a route and redirects to edit page', function () {
    $admin   = routeAdminUser();
    $payload = makeRoutePayload();

    $response = $this->actingAs($admin)->post(route('admin.routes.store'), $payload);

    $route = Route::where('slug', 'ha-noi-da-nang')->first();
    expect($route)->not->toBeNull();
    $response->assertRedirect(route('admin.routes.edit', $route->id));
});

it('slug must be unique on create', function () {
    $admin = routeAdminUser();
    Route::factory()->create(['slug' => 'existing-slug']);

    $payload = makeRoutePayload(['slug' => 'existing-slug']);

    $response = $this->actingAs($admin)->post(route('admin.routes.store'), $payload);
    $response->assertSessionHasErrors('slug');
});

it('allows same slug when updating the same route', function () {
    $admin = routeAdminUser();
    $route = Route::factory()->create(['slug' => 'my-slug']);

    $payload = array_merge(makeRoutePayload(['slug' => 'my-slug']), [
        'province_start_id' => $route->province_start_id,
        'province_end_id'   => $route->province_end_id,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.routes.update', $route->id), $payload);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect(route('admin.routes.edit', $route->id));
});

it('index page lists routes', function () {
    $admin = routeAdminUser();
    Route::factory()->count(3)->create();

    $this->actingAs($admin)->get(route('admin.routes.index'))->assertOk()->assertViewIs('admin.routes.index');
});

it('edit page shows route form', function () {
    $admin = routeAdminUser();
    $route = Route::factory()->create();

    $this->actingAs($admin)->get(route('admin.routes.edit', $route->id))->assertOk()->assertViewIs('admin.routes.form');
});

// ---------------------------------------------------------------------------
// Delete guard — both paths
// ---------------------------------------------------------------------------

it('blocks deleting a route with bookings via record action', function () {
    $admin = routeAdminUser();
    $route = Route::factory()->create();
    $trip  = \App\Models\Trip::factory()->create(['route_id' => $route->id]);
    \App\Models\Booking::factory()->create(['trip_id' => $trip->id]);

    $response = $this->actingAs($admin)->delete(route('admin.routes.destroy', $route->id));

    $response->assertRedirect();
    $response->assertSessionHas('error');
    expect(Route::find($route->id))->not->toBeNull();
    expect(\App\Models\Booking::count())->toBe(1);
});

it('blocks bulk deleting routes with bookings', function () {
    $admin  = routeAdminUser();
    $route1 = Route::factory()->create();
    $route2 = Route::factory()->create();
    $trip   = \App\Models\Trip::factory()->create(['route_id' => $route1->id]);
    \App\Models\Booking::factory()->create(['trip_id' => $trip->id]);

    $response = $this->actingAs($admin)->post(route('admin.routes.bulk-destroy'), [
        'ids' => [$route1->id, $route2->id],
    ]);

    // route1 blocked, route2 deleted
    expect(Route::find($route1->id))->not->toBeNull();
    expect(Route::find($route2->id))->toBeNull();
    expect(\App\Models\Booking::count())->toBe(1);
    $response->assertSessionHas('error'); // partial failure flash
});

it('deletes route without bookings', function () {
    $admin = routeAdminUser();
    $route = Route::factory()->create();

    $this->actingAs($admin)->delete(route('admin.routes.destroy', $route->id))
        ->assertRedirect(route('admin.routes.index'));

    expect(Route::find($route->id))->toBeNull();
});

// ---------------------------------------------------------------------------
// Route stops CRUD
// ---------------------------------------------------------------------------

it('adds a stop to a route', function () {
    $admin = routeAdminUser();
    $route = Route::factory()->create();
    $stop  = Stop::factory()->create();

    $response = $this->actingAs($admin)->postJson(
        route('admin.routes.stops.store', $route->id),
        ['stop_id' => $stop->id, 'stop_type' => 'both']
    );

    $response->assertCreated()->assertJsonPath('ok', true);
    expect(RouteStop::where('route_id', $route->id)->where('stop_id', $stop->id)->exists())->toBeTrue();
});

it('rejects a stop store with missing stop_id', function () {
    $admin = routeAdminUser();
    $route = Route::factory()->create();

    $this->actingAs($admin)->postJson(
        route('admin.routes.stops.store', $route->id),
        ['stop_type' => 'both']
    )->assertUnprocessable();
});

it('deletes a route stop', function () {
    $admin = routeAdminUser();
    $route = Route::factory()->create();
    $stop  = Stop::factory()->create();

    $rs = RouteStop::create([
        'route_id'  => $route->id,
        'stop_id'   => $stop->id,
        'stop_type' => 'both',
        'priority'  => 10,
    ]);

    $this->actingAs($admin)->deleteJson(
        route('admin.routes.stops.destroy', [$route->id, $rs->id])
    )->assertOk()->assertJsonPath('ok', true);

    expect(RouteStop::find($rs->id))->toBeNull();
});

// ---------------------------------------------------------------------------
// Route stop reorder
// ---------------------------------------------------------------------------

it('reorders stops and writes priority desc', function () {
    $admin = routeAdminUser();
    $route = Route::factory()->create();
    $stop1 = Stop::factory()->create();
    $stop2 = Stop::factory()->create();
    $stop3 = Stop::factory()->create();

    $rs1 = RouteStop::create(['route_id' => $route->id, 'stop_id' => $stop1->id, 'stop_type' => 'both', 'priority' => 3]);
    $rs2 = RouteStop::create(['route_id' => $route->id, 'stop_id' => $stop2->id, 'stop_type' => 'both', 'priority' => 2]);
    $rs3 = RouteStop::create(['route_id' => $route->id, 'stop_id' => $stop3->id, 'stop_type' => 'both', 'priority' => 1]);

    // New order: rs3, rs1, rs2 → priorities should be 3, 2, 1
    $this->actingAs($admin)->postJson(
        route('admin.routes.stops.reorder', $route->id),
        ['ids' => [$rs3->id, $rs1->id, $rs2->id]]
    )->assertOk()->assertJsonPath('ok', true);

    expect(RouteStop::find($rs3->id)->priority)->toBe(3);
    expect(RouteStop::find($rs1->id)->priority)->toBe(2);
    expect(RouteStop::find($rs2->id)->priority)->toBe(1);
});

it('rejects reorder with stop IDs from a different route', function () {
    $admin  = routeAdminUser();
    $route1 = Route::factory()->create();
    $route2 = Route::factory()->create();
    $stop   = Stop::factory()->create();

    $rs = RouteStop::create([
        'route_id'  => $route2->id, // belongs to route2
        'stop_id'   => $stop->id,
        'stop_type' => 'both',
        'priority'  => 1,
    ]);

    $response = $this->actingAs($admin)->postJson(
        route('admin.routes.stops.reorder', $route1->id), // reorder against route1
        ['ids' => [$rs->id]]
    );

    $response->assertStatus(403);
});
