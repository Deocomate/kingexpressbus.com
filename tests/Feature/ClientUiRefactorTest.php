<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function seedClientUiTripFixture(string $suffix = 'ui'): array
{
    $now = now();

    $provinceStartId = DB::table('provinces')->insertGetId([
        'name' => 'Ha Noi '.$suffix,
        'slug' => 'ha-noi-'.$suffix,
        'priority' => 20,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $provinceEndId = DB::table('provinces')->insertGetId([
        'name' => 'Sa Pa '.$suffix,
        'slug' => 'sa-pa-'.$suffix,
        'priority' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $districtTypeId = DB::table('district_types')->insertGetId([
        'name' => 'Urban '.$suffix,
        'priority' => 0,
    ]);

    $districtStartId = DB::table('districts')->insertGetId([
        'province_id' => $provinceStartId,
        'district_type_id' => $districtTypeId,
        'name' => 'My Dinh '.$suffix,
        'slug' => 'my-dinh-'.$suffix,
        'priority' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $districtEndId = DB::table('districts')->insertGetId([
        'province_id' => $provinceEndId,
        'district_type_id' => $districtTypeId,
        'name' => 'Sa Pa '.$suffix,
        'slug' => 'district-sa-pa-'.$suffix,
        'priority' => 9,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $pickupStopId = DB::table('stops')->insertGetId([
        'district_id' => $districtStartId,
        'name' => 'Ben xe My Dinh '.$suffix,
        'address' => 'Ha Noi',
        'priority' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $dropoffStopId = DB::table('stops')->insertGetId([
        'district_id' => $districtEndId,
        'name' => 'Ben xe Sa Pa '.$suffix,
        'address' => 'Lao Cai',
        'priority' => 9,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $routeId = DB::table('routes')->insertGetId([
        'province_start_id' => $provinceStartId,
        'province_end_id' => $provinceEndId,
        'name' => 'Ha Noi - Sa Pa '.$suffix,
        'slug' => 'ha-noi-sa-pa-'.$suffix,
        'price_default' => 300000,
        'available_hotel_pickup' => false,
        'duration' => '6h',
        'distance_km' => 315,
        'priority' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    DB::table('route_stops')->insert([
        [
            'route_id' => $routeId,
            'stop_id' => $pickupStopId,
            'stop_type' => 'pickup',
            'priority' => 10,
            'created_at' => $now,
            'updated_at' => $now,
        ],
        [
            'route_id' => $routeId,
            'stop_id' => $dropoffStopId,
            'stop_type' => 'dropoff',
            'priority' => 9,
            'created_at' => $now,
            'updated_at' => $now,
        ],
    ]);

    $busId = DB::table('buses')->insertGetId([
        'name' => 'King Sleeper '.$suffix,
        'model_name' => 'Limousine',
        'seat_count' => 34,
        'thumbnail_url' => '/client/images/kingexpressbus/cabin/1.jpg',
        'image_list_url' => json_encode(['/client/images/kingexpressbus/cabin/1.jpg']),
        'content' => null,
        'priority' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $tripId = DB::table('trips')->insertGetId([
        'bus_id' => $busId,
        'route_id' => $routeId,
        'start_time' => '08:00:00',
        'end_time' => '14:00:00',
        'price' => 300000,
        'is_active' => true,
        'priority' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    return [
        'route_id' => $routeId,
        'route_slug' => 'ha-noi-sa-pa-'.$suffix,
        'trip_id' => $tripId,
        'pickup_stop_id' => $pickupStopId,
        'dropoff_stop_id' => $dropoffStopId,
    ];
}

it('renders the client shell with versioned assets and externalized search alpine hook', function () {
    seedClientUiTripFixture('home');

    $this->get(route('client.home'))
        ->assertSuccessful()
        ->assertSee('/client/css/custom.css?v=1.1.0', false)
        ->assertSee('/client/js/client-ui.js?v=1.1.0', false)
        ->assertSee('Be Vietnam Pro', false)
        ->assertSee('Manrope', false)
        ->assertSee('x-data="kingSearchBar', false)
        ->assertSee('ksb-hero', false);
});

it('renders route pages with the professional route list and drawer tokens', function () {
    $fixture = seedClientUiTripFixture('routes');

    $this->get(route('client.routes.index'))
        ->assertSuccessful()
        ->assertSee('ksb-route-card', false)
        ->assertSee('Ha Noi - Sa Pa routes');

    $this->get(route('client.routes.show', ['slug' => $fixture['route_slug']]))
        ->assertSuccessful()
        ->assertSee('ksb-section-hero', false)
        ->assertSee('ksb-filter-shell', false)
        ->assertSee('ksb-trip-row', false)
        ->assertSee('ksb-drawer', false)
        ->assertSee('ksb-btn-primary', false);
});

it('renders the booking create flow with refactored panels and primary booking CTA', function () {
    $fixture = seedClientUiTripFixture('booking');

    $this->get(route('client.booking.create', [
        'trip_id' => $fixture['trip_id'],
        'date' => '2026-05-27',
    ]))
        ->assertSuccessful()
        ->assertSee('ksb-section-hero', false)
        ->assertSee('ksb-booking-step', false)
        ->assertSee('booking-panel', false)
        ->assertSee('stop-card', false)
        ->assertSee('payment-method-label', false)
        ->assertSee('ksb-sticky-summary', false)
        ->assertSee('ksb-btn-primary', false);
});
