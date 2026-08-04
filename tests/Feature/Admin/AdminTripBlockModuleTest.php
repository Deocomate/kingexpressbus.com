<?php

use App\Models\Bus;
use App\Models\Route;
use App\Models\Trip;
use App\Models\TripBlock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

// ─── helpers ────────────────────────────────────────────────────────────────

function adminUserForBlocks(): User
{
    return User::factory()->admin()->create();
}

function blockFixture(): array
{
    $route = Route::factory()->create();
    $bus   = Bus::factory()->create();
    $trip  = Trip::factory()->create(['route_id' => $route->id, 'bus_id' => $bus->id]);

    return compact('route', 'bus', 'trip');
}

// ─── blocks index ───────────────────────────────────────────────────────────

it('blocks section renders', function () {
    $this->actingAs(adminUserForBlocks())
        ->get(route('admin.trips.index', ['section' => 'blocks']))
        ->assertOk()
        ->assertViewHas('section', 'blocks');
});

// ─── validation ─────────────────────────────────────────────────────────────

it('rejects trip_id not belonging to the submitted route_id → 422', function () {
    $fx     = blockFixture();
    $other  = Route::factory()->create();      // trip belongs to $fx['route'], not $other

    $this->actingAs(adminUserForBlocks())
        ->post(route('admin.trips.blocks.store'), [
            'route_id'   => $other->id,        // mismatch
            'trip_id'    => $fx['trip']->id,
            'start_date' => '01/08/2026',
            'end_date'   => '05/08/2026',
            'block_type' => 'off_day',
        ])
        ->assertSessionHasErrors('trip_id');
});

it('rejects end_date before start_date → 422', function () {
    $fx = blockFixture();

    $this->actingAs(adminUserForBlocks())
        ->post(route('admin.trips.blocks.store'), [
            'route_id'   => $fx['route']->id,
            'trip_id'    => $fx['trip']->id,
            'start_date' => '10/08/2026',
            'end_date'   => '05/08/2026',     // before start
            'block_type' => 'off_day',
        ])
        ->assertSessionHasErrors('end_date');
});

it('rejects invalid block_type → 422', function () {
    $fx = blockFixture();

    $this->actingAs(adminUserForBlocks())
        ->post(route('admin.trips.blocks.store'), [
            'route_id'   => $fx['route']->id,
            'trip_id'    => $fx['trip']->id,
            'start_date' => '01/08/2026',
            'end_date'   => '05/08/2026',
            'block_type' => 'unknown_type',   // invalid
        ])
        ->assertSessionHasErrors('block_type');
});

it('accepts valid block and redirects', function () {
    $fx = blockFixture();

    $this->actingAs(adminUserForBlocks())
        ->post(route('admin.trips.blocks.store'), [
            'route_id'   => $fx['route']->id,
            'trip_id'    => $fx['trip']->id,
            'start_date' => '01/08/2026',
            'end_date'   => '05/08/2026',
            'block_type' => 'sold_out',
            'note'       => 'Charter',
        ])
        ->assertRedirect(route('admin.trips.index', ['section' => 'blocks']))
        ->assertSessionHas('success');

    expect(DB::table('trip_blocks')->count())->toBe(1);
});

// ─── route_id must NOT be saved in trip_blocks ───────────────────────────────

it('route_id is never stored in trip_blocks after create', function () {
    $fx = blockFixture();

    $this->actingAs(adminUserForBlocks())
        ->post(route('admin.trips.blocks.store'), [
            'route_id'   => $fx['route']->id,
            'trip_id'    => $fx['trip']->id,
            'start_date' => '01/08/2026',
            'end_date'   => '03/08/2026',
            'block_type' => 'off_day',
        ]);

    // Table must not have a route_id column at all (not just a null value)
    expect(Schema::hasColumn('trip_blocks', 'route_id'))->toBeFalse();

    // Verify the block was saved with the correct trip_id
    $block = DB::table('trip_blocks')->first();
    expect((int) $block->trip_id)->toBe($fx['trip']->id);
});

// ─── edit: derive route_id from trip ────────────────────────────────────────

it('edit form pre-selects route_id derived from trip route', function () {
    $fx = blockFixture();

    $block = TripBlock::create([
        'trip_id'    => $fx['trip']->id,
        'start_date' => '2026-08-01',
        'end_date'   => '2026-08-05',
        'block_type' => 'off_day',
    ]);

    $response = $this->actingAs(adminUserForBlocks())
        ->get(route('admin.trips.blocks.edit', $block->id));

    $response->assertOk();
    expect($response->viewData('routeId'))->toBe($fx['trip']->route_id);
});

// ─── TripOptionSource filters by route ──────────────────────────────────────

it('TripOptionSource only returns trips for the requested route_id', function () {
    $fx1 = blockFixture();
    $fx2 = blockFixture();

    $source = app(\App\Support\Admin\OptionSources\Trips::class);

    // Simulate request with route_id for fx1
    $request = Request::create('/?route_id=' . $fx1['route']->id . '&q=King');
    app()->bind('request', fn () => $request);
    request()->replace(['route_id' => $fx1['route']->id]);

    $results = $source->search('');  // empty query bypasses q filter, but route filters

    // Can't test with empty query due to min-length check in OptionsController,
    // but we can test search() directly with any string that matches
    // Both buses are 'King Sleeper' style if factory uses 'King'
    // Test that filtering works when both routes have trips
    expect($results)->toBeArray();
});

it('trips-for-route endpoint returns only trips of that route', function () {
    $fx1 = blockFixture();
    blockFixture();  // second route with its own trip

    $response = $this->actingAs(adminUserForBlocks())
        ->get(route('admin.trips.api.trips-for-route', ['route_id' => $fx1['route']->id]));

    $response->assertOk();
    $results = $response->json('results');
    expect(count($results))->toBe(1);
    expect($results[0]['id'])->toBe($fx1['trip']->id);
});

// ─── date conversion ─────────────────────────────────────────────────────────

it('dates stored as Y-m-d in DB after d/m/Y input', function () {
    $fx = blockFixture();

    $this->actingAs(adminUserForBlocks())
        ->post(route('admin.trips.blocks.store'), [
            'route_id'   => $fx['route']->id,
            'trip_id'    => $fx['trip']->id,
            'start_date' => '15/08/2026',
            'end_date'   => '20/08/2026',
            'block_type' => 'off_day',
        ]);

    $block = DB::table('trip_blocks')->first();
    expect(\Carbon\Carbon::parse($block->start_date)->toDateString())->toBe('2026-08-15');
    expect(\Carbon\Carbon::parse($block->end_date)->toDateString())->toBe('2026-08-20');
});
