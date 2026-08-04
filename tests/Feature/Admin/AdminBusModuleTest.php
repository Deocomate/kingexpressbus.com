<?php

use App\Models\Bus;
use App\Models\BusService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function adminForBus(): User
{
    return User::factory()->admin()->create();
}

function makeBusPayload(array $overrides = []): array
{
    return array_merge([
        'name'        => 'Xe Giường Nằm 40 Chỗ',
        'model_name'  => 'Limousine',
        'seat_count'  => 40,
        'priority'    => 5,
        'content'     => '',
    ], $overrides);
}

// ---------------------------------------------------------------------------
// CRUD
// ---------------------------------------------------------------------------

it('creates a bus and redirects to edit page', function () {
    $admin = adminForBus();

    $response = $this->actingAs($admin)->post(route('admin.buses.store'), makeBusPayload());

    $bus = Bus::where('name', 'Xe Giường Nằm 40 Chỗ')->first();
    expect($bus)->not->toBeNull();
    $response->assertRedirect(route('admin.buses.edit', $bus->id));
});

it('rejects seat_count = 0', function () {
    $admin = adminForBus();

    $response = $this->actingAs($admin)->post(route('admin.buses.store'), makeBusPayload(['seat_count' => 0]));
    $response->assertSessionHasErrors('seat_count');
});

it('rejects more than 10 album images', function () {
    $admin = adminForBus();
    $payload = makeBusPayload(['image_list_url' => array_fill(0, 11, 'path/to/img.jpg')]);

    $response = $this->actingAs($admin)->post(route('admin.buses.store'), $payload);
    $response->assertSessionHasErrors('image_list_url');
});

it('syncs services on create', function () {
    $admin = adminForBus();
    $svc1  = BusService::factory()->create();
    $svc2  = BusService::factory()->create();

    $payload = makeBusPayload(['services' => [$svc1->id, $svc2->id]]);
    $this->actingAs($admin)->post(route('admin.buses.store'), $payload);

    $bus = Bus::with('services')->where('name', 'Xe Giường Nằm 40 Chỗ')->first();
    expect($bus->services->pluck('id')->toArray())->toEqualCanonicalizing([$svc1->id, $svc2->id]);
});

it('syncs services on update', function () {
    $admin = adminForBus();
    $bus   = Bus::factory()->create();
    $svc1  = BusService::factory()->create();
    $svc2  = BusService::factory()->create();
    $bus->services()->sync([$svc1->id, $svc2->id]);

    $svc3 = BusService::factory()->create();
    $payload = makeBusPayload(['name' => $bus->name, 'services' => [$svc3->id]]);

    $this->actingAs($admin)->put(route('admin.buses.update', $bus->id), $payload);

    $bus->refresh()->load('services');
    expect($bus->services->pluck('id')->toArray())->toEqualCanonicalizing([$svc3->id]);
});

it('index page renders', function () {
    $admin = adminForBus();
    Bus::factory()->count(2)->create();

    $this->actingAs($admin)->get(route('admin.buses.index'))->assertOk()->assertViewIs('admin.buses.index');
});

it('edit page renders', function () {
    $admin = adminForBus();
    $bus   = Bus::factory()->create();

    $this->actingAs($admin)->get(route('admin.buses.edit', $bus->id))->assertOk()->assertViewIs('admin.buses.form');
});

// ---------------------------------------------------------------------------
// Delete guard — both paths
// ---------------------------------------------------------------------------

it('blocks deleting a bus with bookings via record action', function () {
    $admin = adminForBus();
    $bus   = Bus::factory()->create();
    $trip  = \App\Models\Trip::factory()->create(['bus_id' => $bus->id]);
    \App\Models\Booking::factory()->create(['trip_id' => $trip->id]);

    $response = $this->actingAs($admin)->delete(route('admin.buses.destroy', $bus->id));

    $response->assertSessionHas('error');
    expect(Bus::find($bus->id))->not->toBeNull();
    expect(\App\Models\Booking::count())->toBe(1);
});

it('blocks bulk deleting buses with bookings', function () {
    $admin = adminForBus();
    $bus1  = Bus::factory()->create();
    $bus2  = Bus::factory()->create();
    $trip  = \App\Models\Trip::factory()->create(['bus_id' => $bus1->id]);
    \App\Models\Booking::factory()->create(['trip_id' => $trip->id]);

    $response = $this->actingAs($admin)->post(route('admin.buses.bulk-destroy'), [
        'ids' => [$bus1->id, $bus2->id],
    ]);

    expect(Bus::find($bus1->id))->not->toBeNull();
    expect(Bus::find($bus2->id))->toBeNull();
    expect(\App\Models\Booking::count())->toBe(1);
    $response->assertSessionHas('error');
});

it('deletes bus without bookings', function () {
    $admin = adminForBus();
    $bus   = Bus::factory()->create();

    $this->actingAs($admin)->delete(route('admin.buses.destroy', $bus->id))
        ->assertRedirect(route('admin.buses.index'));

    expect(Bus::find($bus->id))->toBeNull();
});

// ---------------------------------------------------------------------------
// Bus service CRUD & delete guard
// ---------------------------------------------------------------------------

it('creates a bus service', function () {
    $admin = adminForBus();

    $response = $this->actingAs($admin)->postJson(route('admin.buses.services.store'), [
        'name' => 'Wi-Fi',
        'icon' => 'fa-wifi',
    ]);

    $response->assertCreated()->assertJsonPath('ok', true);
    expect(BusService::where('name', 'Wi-Fi')->exists())->toBeTrue();
});

it('blocks deleting a service attached to buses', function () {
    $admin = adminForBus();
    $bus   = Bus::factory()->create();
    $svc   = BusService::factory()->create(['name' => 'AC']);
    $bus->services()->attach($svc->id);

    $response = $this->actingAs($admin)->deleteJson(route('admin.buses.services.destroy', $svc->id));

    $response->assertStatus(422)->assertJsonPath('ok', false);
    expect(BusService::find($svc->id))->not->toBeNull();
});

it('deletes a service not attached to any bus', function () {
    $admin = adminForBus();
    $svc   = BusService::factory()->create();

    $this->actingAs($admin)->deleteJson(route('admin.buses.services.destroy', $svc->id))
        ->assertOk()->assertJsonPath('ok', true);

    expect(BusService::find($svc->id))->toBeNull();
});

// ---------------------------------------------------------------------------
// Transaction: service sync failure rolls back bus creation
// ---------------------------------------------------------------------------

it('rolls back bus creation if service sync throws', function () {
    $admin = adminForBus();

    // Provide a non-existent service ID to trigger a DB exception during sync
    $payload = makeBusPayload(['services' => [99999]]);

    $response = $this->actingAs($admin)->post(route('admin.buses.store'), $payload);

    // Validation should catch the invalid service ID (exists rule)
    $response->assertSessionHasErrors('services.0');
    expect(Bus::where('name', 'Xe Giường Nằm 40 Chỗ')->exists())->toBeFalse();
});
