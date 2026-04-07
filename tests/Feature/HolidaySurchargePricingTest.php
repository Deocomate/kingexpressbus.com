<?php

use App\Services\HolidaySurchargeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function seedTripFixture(string $slugSuffix = 'a', bool $availableHotelPickup = false): array
{
    $now = now();

    $provinceStartId = DB::table('provinces')->insertGetId([
        'name' => 'Ha Noi ' . $slugSuffix,
        'slug' => 'ha-noi-' . $slugSuffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $provinceEndId = DB::table('provinces')->insertGetId([
        'name' => 'Lao Cai ' . $slugSuffix,
        'slug' => 'lao-cai-' . $slugSuffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $districtTypeId = DB::table('district_types')->insertGetId([
        'name' => 'Urban ' . $slugSuffix,
        'priority' => 0,
    ]);

    $districtStartId = DB::table('districts')->insertGetId([
        'province_id' => $provinceStartId,
        'district_type_id' => $districtTypeId,
        'name' => 'Thanh Xuan ' . $slugSuffix,
        'slug' => 'thanh-xuan-' . $slugSuffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $districtEndId = DB::table('districts')->insertGetId([
        'province_id' => $provinceEndId,
        'district_type_id' => $districtTypeId,
        'name' => 'Sapa ' . $slugSuffix,
        'slug' => 'sapa-' . $slugSuffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $pickupStopId = DB::table('stops')->insertGetId([
        'district_id' => $districtStartId,
        'name' => 'Ben Xe My Dinh ' . $slugSuffix,
        'address' => 'Ha Noi',
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $dropoffStopId = DB::table('stops')->insertGetId([
        'district_id' => $districtEndId,
        'name' => 'Ben Xe Sapa ' . $slugSuffix,
        'address' => 'Lao Cai',
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $routeId = DB::table('routes')->insertGetId([
        'province_start_id' => $provinceStartId,
        'province_end_id' => $provinceEndId,
        'name' => 'Ha Noi - Sapa ' . $slugSuffix,
        'slug' => 'ha-noi-sapa-' . $slugSuffix,
        'price_default' => 0,
        'available_hotel_pickup' => $availableHotelPickup,
        'priority' => 0,
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
        'name' => 'King Sleeper ' . $slugSuffix,
        'model_name' => 'Limousine',
        'seat_count' => 40,
        'seat_map' => json_encode([]),
        'services' => json_encode([]),
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $tripId = DB::table('trips')->insertGetId([
        'bus_id' => $busId,
        'route_id' => $routeId,
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'price' => 300000,
        'is_active' => true,
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    return [
        'trip_id' => $tripId,
        'route_id' => $routeId,
        'district_start_id' => $districtStartId,
        'district_end_id' => $districtEndId,
        'pickup_stop_id' => $pickupStopId,
        'dropoff_stop_id' => $dropoffStopId,
    ];
}

it('calculates additive global and route surcharge by travel date', function () {
    $fixture = seedTripFixture('calc');
    $now = now();

    $ruleId = DB::table('holiday_surcharges')->insertGetId([
        'name' => 'Tet 2026',
        'reason' => 'Peak holiday demand',
        'start_date' => '2026-02-01',
        'end_date' => '2026-02-05',
        'global_surcharge_amount' => 50000,
        'is_active' => true,
        'priority' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    DB::table('holiday_surcharge_routes')->insert([
        'holiday_surcharge_id' => $ruleId,
        'route_id' => $fixture['route_id'],
        'route_surcharge_amount' => 30000,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $breakdown = app(HolidaySurchargeService::class)
        ->calculateBreakdownByTripId($fixture['trip_id'], '2026-02-02');

    expect($breakdown['base_unit_price'])->toBe(300000)
        ->and($breakdown['global_surcharge_unit'])->toBe(50000)
        ->and($breakdown['route_surcharge_unit'])->toBe(30000)
        ->and($breakdown['total_surcharge_unit'])->toBe(80000)
        ->and($breakdown['final_unit_price'])->toBe(380000)
        ->and($breakdown['has_surcharge'])->toBeTrue();
});

it('returns base price when date is outside surcharge window', function () {
    $fixture = seedTripFixture('outside');
    $now = now();

    DB::table('holiday_surcharges')->insert([
        'name' => 'Tet 2026',
        'reason' => 'Peak holiday demand',
        'start_date' => '2026-02-01',
        'end_date' => '2026-02-05',
        'global_surcharge_amount' => 50000,
        'is_active' => true,
        'priority' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $breakdown = app(HolidaySurchargeService::class)
        ->calculateBreakdownByTripId($fixture['trip_id'], '2026-03-10');

    expect($breakdown['global_surcharge_unit'])->toBe(0)
        ->and($breakdown['route_surcharge_unit'])->toBe(0)
        ->and($breakdown['final_unit_price'])->toBe(300000)
        ->and($breakdown['has_surcharge'])->toBeFalse();
});

it('stacks overlapping holiday rules additively by design', function () {
    $fixture = seedTripFixture('overlap');
    $now = now();

    DB::table('holiday_surcharges')->insert([
        [
            'name' => 'Lunar New Year Week',
            'reason' => 'High seasonal demand',
            'start_date' => '2026-02-01',
            'end_date' => '2026-02-05',
            'global_surcharge_amount' => 30000,
            'is_active' => true,
            'priority' => 10,
            'created_at' => $now,
            'updated_at' => $now,
        ],
        [
            'name' => 'Peak Weekend',
            'reason' => 'Weekend demand spike',
            'start_date' => '2026-02-03',
            'end_date' => '2026-02-04',
            'global_surcharge_amount' => 20000,
            'is_active' => true,
            'priority' => 8,
            'created_at' => $now,
            'updated_at' => $now,
        ],
    ]);

    $breakdown = app(HolidaySurchargeService::class)
        ->calculateBreakdownByTripId($fixture['trip_id'], '2026-02-03');

    expect($breakdown['global_surcharge_unit'])->toBe(50000)
        ->and($breakdown['route_surcharge_unit'])->toBe(0)
        ->and($breakdown['final_unit_price'])->toBe(350000)
        ->and($breakdown['has_surcharge'])->toBeTrue();
});

it('uses server price calculation even when client submits tampered total', function () {
    $fixture = seedTripFixture('booking');
    $now = now();

    $ruleId = DB::table('holiday_surcharges')->insertGetId([
        'name' => 'Tet 2026',
        'reason' => 'Peak holiday demand',
        'start_date' => '2026-02-01',
        'end_date' => '2026-02-05',
        'global_surcharge_amount' => 50000,
        'is_active' => true,
        'priority' => 10,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    DB::table('holiday_surcharge_routes')->insert([
        'holiday_surcharge_id' => $ruleId,
        'route_id' => $fixture['route_id'],
        'route_surcharge_amount' => 30000,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $response = $this->post(route('client.booking.store'), [
        'trip_id' => $fixture['trip_id'],
        'booking_date' => '02/02/2026',
        'quantity' => 2,
        'customer_name' => 'Test User',
        'customer_phone' => '0900000000',
        'customer_email' => 'test@example.com',
        'pickup_stop_id' => $fixture['pickup_stop_id'],
        'dropoff_stop_id' => $fixture['dropoff_stop_id'],
        'total_price' => 1,
        'payment_method' => 'cash_on_pickup',
    ]);

    $response->assertRedirect(route('client.booking.success'));

    $booking = DB::table('bookings')->first();

    expect($booking)->not->toBeNull()
        ->and((int) $booking->final_unit_price)->toBe(380000)
        ->and((int) $booking->total_price)->toBe(760000)
        ->and((int) $booking->total_surcharge_amount)->toBe(160000);
});

it('rejects hotel pickup when route does not support it', function () {
    $fixture = seedTripFixture('hotel-off', false);

    $response = $this->from(route('client.booking.create', [
        'trip_id' => $fixture['trip_id'],
        'date' => '02/02/2026',
    ]))->post(route('client.booking.store'), [
        'trip_id' => $fixture['trip_id'],
        'booking_date' => '02/02/2026',
        'quantity' => 1,
        'customer_name' => 'Test User',
        'customer_phone' => '0900000000',
        'customer_email' => 'test@example.com',
        'pickup_stop_id' => 'hotel_pickup',
        'hotel_pickup_address' => '12 Nguyen Trai',
        'dropoff_stop_id' => $fixture['dropoff_stop_id'],
        'total_price' => 300000,
        'payment_method' => 'cash_on_pickup',
    ]);

    $response->assertRedirect(route('client.booking.create', [
        'trip_id' => $fixture['trip_id'],
        'date' => '02/02/2026',
    ]));

    $response->assertSessionHas('error', __('client.booking.store.hotel_pickup_not_available'));
    expect(DB::table('bookings')->count())->toBe(0);
});

it('rejects pickup stop outside the selected trip route', function () {
    $fixture = seedTripFixture('invalid-pickup');
    $now = now();

    $invalidPickupStopId = DB::table('stops')->insertGetId([
        'district_id' => $fixture['district_start_id'],
        'name' => 'Outside Route Pickup',
        'address' => 'Outside route',
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $response = $this->from(route('client.booking.create', [
        'trip_id' => $fixture['trip_id'],
        'date' => '02/02/2026',
    ]))->post(route('client.booking.store'), [
        'trip_id' => $fixture['trip_id'],
        'booking_date' => '02/02/2026',
        'quantity' => 1,
        'customer_name' => 'Test User',
        'customer_phone' => '0900000000',
        'customer_email' => 'test@example.com',
        'pickup_stop_id' => $invalidPickupStopId,
        'dropoff_stop_id' => $fixture['dropoff_stop_id'],
        'total_price' => 300000,
        'payment_method' => 'cash_on_pickup',
    ]);

    $response->assertRedirect(route('client.booking.create', [
        'trip_id' => $fixture['trip_id'],
        'date' => '02/02/2026',
    ]));

    $response->assertSessionHas('error', __('client.booking.store.invalid_pickup_stop'));
    expect(DB::table('bookings')->count())->toBe(0);
});

it('keeps seat availability failures as user-facing business messages', function () {
    $fixture = seedTripFixture('seat-check');
    $now = now();

    DB::table('bookings')->insert([
        'booking_code' => 'EXIST-SEAT-1',
        'trip_id' => $fixture['trip_id'],
        'booking_date' => '2026-02-02',
        'customer_name' => 'Existing Customer',
        'customer_phone' => '0900000001',
        'customer_email' => 'existing@example.com',
        'pickup_stop_id' => $fixture['pickup_stop_id'],
        'dropoff_stop_id' => $fixture['dropoff_stop_id'],
        'quantity' => 40,
        'total_price' => 12000000,
        'payment_method' => 'cash_on_pickup',
        'payment_status' => 'unpaid',
        'status' => 'confirmed',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $response = $this->from(route('client.booking.create', [
        'trip_id' => $fixture['trip_id'],
        'date' => '02/02/2026',
    ]))->post(route('client.booking.store'), [
        'trip_id' => $fixture['trip_id'],
        'booking_date' => '02/02/2026',
        'quantity' => 2,
        'customer_name' => 'Test User',
        'customer_phone' => '0900000000',
        'customer_email' => 'test@example.com',
        'pickup_stop_id' => $fixture['pickup_stop_id'],
        'dropoff_stop_id' => $fixture['dropoff_stop_id'],
        'total_price' => 600000,
        'payment_method' => 'cash_on_pickup',
    ]);

    $response->assertRedirect(route('client.booking.create', [
        'trip_id' => $fixture['trip_id'],
        'date' => '02/02/2026',
    ]));

    $response->assertSessionHas('error', __('client.booking.store.not_enough_seats', [
        'requested' => 2,
        'available' => 0,
    ]));
});

it('returns false when updating a non-existent holiday surcharge rule', function () {
    $payload = [
        'name' => 'Non-existent Rule',
        'reason' => 'N/A',
        'start_date' => '2026-02-01',
        'end_date' => '2026-02-05',
        'global_surcharge_amount' => 25000,
        'is_active' => true,
        'priority' => 0,
        'route_adjustments' => [],
    ];

    $updated = app(HolidaySurchargeService::class)->updateForAdmin(999999, $payload);

    expect($updated)->toBeFalse();
});
