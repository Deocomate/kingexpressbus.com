<?php

use App\Services\BusService as AdminBusService;
use App\Services\TripService;
use Database\Seeders\BusSeeder;
use Database\Seeders\BusServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function seedPivotServices(): void
{
    DB::table('bus_services')->insert([
        ['id' => 1, 'name' => 'Wi-Fi', 'icon' => 'fas fa-wifi', 'priority' => 100],
        ['id' => 2, 'name' => 'Nước uống', 'icon' => 'fas fa-bottle-water', 'priority' => 95],
        ['id' => 3, 'name' => 'Chăn gối', 'icon' => 'fas fa-bed', 'priority' => 90],
    ]);
}

function createPivotBus(string $name, array $serviceIds = [], ?int $priority = null): int
{
    static $counter = 0;
    $counter++;

    $busId = DB::table('buses')->insertGetId([
        'name' => $name,
        'model_name' => 'Limousine',
        'seat_count' => 40,
        'thumbnail_url' => null,
        'image_list_url' => json_encode([]),
        'content' => null,
        'priority' => $priority ?? $counter,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    foreach ($serviceIds as $serviceId) {
        DB::table('bus_bus_service')->insert([
            'bus_id' => $busId,
            'bus_service_id' => $serviceId,
        ]);
    }

    return $busId;
}

function createPivotRoute(): int
{
    $now = now();

    $startProvinceId = DB::table('provinces')->insertGetId([
        'name' => 'Ha Noi Pivot',
        'slug' => 'ha-noi-pivot',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $endProvinceId = DB::table('provinces')->insertGetId([
        'name' => 'Lao Cai Pivot',
        'slug' => 'lao-cai-pivot',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    return DB::table('routes')->insertGetId([
        'province_start_id' => $startProvinceId,
        'province_end_id' => $endProvinceId,
        'name' => 'Ha Noi - Lao Cai Pivot',
        'slug' => 'ha-noi-lao-cai-pivot',
        'price_default' => 250000,
        'available_hotel_pickup' => false,
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);
}

function createPivotTrip(int $routeId, int $busId, string $startTime = '08:00:00'): int
{
    return DB::table('trips')->insertGetId([
        'bus_id' => $busId,
        'route_id' => $routeId,
        'start_time' => $startTime,
        'end_time' => '12:00:00',
        'price' => 300000,
        'is_active' => true,
        'priority' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

it('syncs service ids when creating and updating buses', function () {
    seedPivotServices();

    $service = app(AdminBusService::class);
    $busId = $service->createBus([
        'name' => 'VIP Cabin',
        'model_name' => 'VIP 22',
        'seat_count' => 40,
        'services' => [1, 2],
        'thumbnail_url' => null,
        'image_list_url' => json_encode([]),
        'content' => null,
        'priority' => 10,
    ]);

    expect(DB::table('bus_bus_service')->where('bus_id', $busId)->pluck('bus_service_id')->sort()->values()->all())
        ->toBe([1, 2]);

    $service->updateBus($busId, [
        'name' => 'VIP Cabin Updated',
        'model_name' => 'VIP 22',
        'seat_count' => 22,
        'services' => [2, 3],
        'thumbnail_url' => null,
        'image_list_url' => json_encode([]),
        'content' => null,
        'priority' => 20,
    ]);

    expect(DB::table('bus_bus_service')->where('bus_id', $busId)->pluck('bus_service_id')->sort()->values()->all())
        ->toBe([2, 3]);
});

it('filters admin datatable buses by service id', function () {
    seedPivotServices();
    createPivotBus('Bus With Wi-Fi', [1]);
    createPivotBus('Bus With Water', [2]);

    $result = app(AdminBusService::class)->getBusesForDataTable([
        'service_filter' => 1,
        'start' => 0,
        'length' => 10,
    ]);

    expect($result['recordsFiltered'])->toBe(1)
        ->and($result['data']->first()->name)->toBe('Bus With Wi-Fi')
        ->and($result['data']->first()->services->first()->name)->toBe('Wi-Fi');
});

it('filters client trips by service id and legacy service name', function () {
    seedPivotServices();
    $routeId = createPivotRoute();
    $wifiBusId = createPivotBus('Bus With Wi-Fi', [1, 2]);
    $blanketBusId = createPivotBus('Bus With Blanket', [3]);
    createPivotTrip($routeId, $wifiBusId, '08:00:00');
    createPivotTrip($routeId, $blanketBusId, '09:00:00');

    $tripService = app(TripService::class);

    $byId = $tripService->getFilteredTripsByRoute($routeId, '2026-05-27', ['services' => [1]], 'recommended');
    $byName = $tripService->getFilteredTripsByRoute($routeId, '2026-05-27', ['services' => ['Wi-Fi']], 'recommended');

    expect($byId['trips'])->toHaveCount(1)
        ->and($byId['trips']->first()->bus_name)->toBe('Bus With Wi-Fi')
        ->and($byId['trips']->first()->service_ids)->toBe([1, 2])
        ->and($byName['trips'])->toHaveCount(1)
        ->and($byName['trips']->first()->bus_name)->toBe('Bus With Wi-Fi');
});

it('loads booking amenities from pivot services', function () {
    seedPivotServices();
    $routeId = createPivotRoute();
    $busId = createPivotBus('Bus With Amenities', [1, 2]);
    $tripId = createPivotTrip($routeId, $busId);

    $this->get(route('client.booking.create', ['trip_id' => $tripId, 'date' => '2026-05-27']))
        ->assertSuccessful()
        ->assertSee('Wi-Fi')
        ->assertSee('Nước uống');
});

it('seeds bus service mappings for the default fleet', function () {
    $this->seed(BusServiceSeeder::class);
    $this->seed(BusSeeder::class);

    $mapping = DB::table('bus_bus_service')
        ->select('bus_id', 'bus_service_id')
        ->orderBy('bus_id')
        ->orderBy('bus_service_id')
        ->get()
        ->groupBy('bus_id')
        ->map(fn ($rows) => $rows->pluck('bus_service_id')->values()->all());

    expect($mapping[1])->toBe([2, 3, 4])
        ->and($mapping[4])->toBe([2, 4])
        ->and($mapping[5])->toBe([2, 4])
        ->and($mapping[6])->toBe([2, 3, 4, 6])
        ->and($mapping[7])->toBe([2, 3, 4, 6, 8, 9])
        ->and($mapping[8])->toBe([2, 3, 4, 6, 8, 9]);
});
