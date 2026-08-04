<?php

use App\Models\Menu;
use App\Models\Province;
use App\Models\Route;
use App\Models\RouteStop;
use App\Services\RouteService;
use App\Support\PriorityOrder;
use App\Support\RouteStopQuery;
use App\View\Components\Client\Layout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('orders route stops by descending priority on client queries', function () {
    $now = now();

    $routeId = DB::table('routes')->insertGetId([
        'province_start_id' => DB::table('provinces')->insertGetId([
            'name' => 'Province A',
            'slug' => 'province-a',
            'priority' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]),
        'province_end_id' => DB::table('provinces')->insertGetId([
            'name' => 'Province B',
            'slug' => 'province-b',
            'priority' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]),
        'name' => 'Route A-B',
        'slug' => 'route-a-b',
        'price_default' => 100000,
        'available_hotel_pickup' => false,
        'priority' => 5,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $firstStopId = DB::table('stops')->insertGetId([
        'district_id' => DB::table('districts')->insertGetId([
            'province_id' => DB::table('provinces')->value('id'),
            'district_type_id' => DB::table('district_types')->insertGetId(['name' => 'Urban', 'priority' => 0]),
            'name' => 'District A',
            'slug' => 'district-a',
            'priority' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]),
        'name' => 'Pickup Stop',
        'address' => 'Address A',
        'priority' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $secondStopId = DB::table('stops')->insertGetId([
        'district_id' => DB::table('districts')->orderByDesc('id')->value('id'),
        'name' => 'Dropoff Stop',
        'address' => 'Address B',
        'priority' => 9,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    DB::table('route_stops')->insert([
        [
            'route_id' => $routeId,
            'stop_id' => $firstStopId,
            'stop_type' => 'pickup',
            'priority' => 20,
            'created_at' => $now,
            'updated_at' => $now,
        ],
        [
            'route_id' => $routeId,
            'stop_id' => $secondStopId,
            'stop_type' => 'dropoff',
            'priority' => 10,
            'created_at' => $now,
            'updated_at' => $now,
        ],
    ]);

    $orderedStopIds = RouteStopQuery::forRoute($routeId)->pluck('id')->all();

    expect($orderedStopIds)->toBe([$firstStopId, $secondStopId]);
});

it('assigns descending priorities when syncing route stops', function () {
    $now = now();

    $routeId = DB::table('routes')->insertGetId([
        'province_start_id' => DB::table('provinces')->insertGetId([
            'name' => 'Province C',
            'slug' => 'province-c',
            'priority' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]),
        'province_end_id' => DB::table('provinces')->insertGetId([
            'name' => 'Province D',
            'slug' => 'province-d',
            'priority' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]),
        'name' => 'Route C-D',
        'slug' => 'route-c-d',
        'price_default' => 100000,
        'available_hotel_pickup' => false,
        'priority' => 1,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $stopIds = [
        DB::table('stops')->insertGetId([
            'district_id' => DB::table('districts')->insertGetId([
                'province_id' => DB::table('provinces')->orderBy('id')->value('id'),
                'district_type_id' => DB::table('district_types')->insertGetId(['name' => 'City', 'priority' => 0]),
                'name' => 'District C',
                'slug' => 'district-c',
                'priority' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]),
            'name' => 'Stop C',
            'address' => 'Address C',
            'priority' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]),
        DB::table('stops')->insertGetId([
            'district_id' => DB::table('districts')->orderByDesc('id')->value('id'),
            'name' => 'Stop D',
            'address' => 'Address D',
            'priority' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]),
    ];

    app(RouteService::class)->syncRouteStops($routeId, [
        ['stop_id' => $stopIds[0], 'stop_type' => 'pickup'],
        ['stop_id' => $stopIds[1], 'stop_type' => 'dropoff'],
    ]);

    $priorities = DB::table('route_stops')
        ->where('route_id', $routeId)
        ->orderByDesc('priority')
        ->pluck('stop_id')
        ->all();

    expect($priorities)->toBe($stopIds);
});

it('inverts sibling orders for menu tree saves', function () {
    $items = [
        1 => ['parent_id' => -1, 'order' => 1],
        2 => ['parent_id' => -1, 'order' => 2],
        3 => ['parent_id' => 1, 'order' => 1],
    ];

    $inverted = PriorityOrder::invertSiblingOrders($items);

    expect($inverted[1]['order'])->toBe(2)
        ->and($inverted[2]['order'])->toBe(1)
        ->and($inverted[3]['order'])->toBe(1);
});

it('keeps route relation stops aligned with descending priority', function () {
    $route = Route::factory()->create();
    $first = RouteStop::factory()->for($route)->create(['priority' => 30]);
    $second = RouteStop::factory()->for($route)->create(['priority' => 10]);

    expect($route->fresh()->routeStops->pluck('id')->all())->toBe([$first->id, $second->id]);
});

it('scopes ordered models by descending priority', function () {
    $high = Province::factory()->create(['priority' => 100]);
    $low = Province::factory()->create(['priority' => 1]);

    expect(Province::ordered()->whereIn('id', [$high->id, $low->id])->pluck('id')->all())
        ->toBe([$high->id, $low->id]);
});

it('builds client menu tree with higher priority first', function () {
    $parent = Menu::factory()->create(['priority' => 50, 'parent_id' => Menu::ROOT_PARENT_ID]);
    $firstChild = Menu::factory()->create(['parent_id' => $parent->id, 'priority' => 40, 'name' => 'First Child']);
    $secondChild = Menu::factory()->create(['parent_id' => $parent->id, 'priority' => 10, 'name' => 'Second Child']);

    $component = new Layout;
    $method = new ReflectionMethod($component, 'buildMenuTree');
    $method->setAccessible(true);

    $tree = $method->invoke($component, collect([$parent, $firstChild, $secondChild]), Menu::ROOT_PARENT_ID);

    expect($tree[0]->id)->toBe($parent->id)
        ->and($tree[0]->children[0]->id)->toBe($firstChild->id)
        ->and($tree[0]->children[1]->id)->toBe($secondChild->id);
});
