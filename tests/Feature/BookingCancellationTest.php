<?php

use App\Mail\BookingCancelledMail;
use App\Services\BookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

function seedCancellationFixture(string $slugSuffix = 'cancel'): array
{
    $now = now();

    $provinceStartId = DB::table('provinces')->insertGetId([
        'name' => 'Ha Noi '.$slugSuffix,
        'slug' => 'ha-noi-'.$slugSuffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $provinceEndId = DB::table('provinces')->insertGetId([
        'name' => 'Lao Cai '.$slugSuffix,
        'slug' => 'lao-cai-'.$slugSuffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $districtTypeId = DB::table('district_types')->insertGetId([
        'name' => 'Urban '.$slugSuffix,
        'priority' => 0,
    ]);

    $districtStartId = DB::table('districts')->insertGetId([
        'province_id' => $provinceStartId,
        'district_type_id' => $districtTypeId,
        'name' => 'Thanh Xuan '.$slugSuffix,
        'slug' => 'thanh-xuan-'.$slugSuffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $districtEndId = DB::table('districts')->insertGetId([
        'province_id' => $provinceEndId,
        'district_type_id' => $districtTypeId,
        'name' => 'Sapa '.$slugSuffix,
        'slug' => 'sapa-'.$slugSuffix,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $pickupStopId = DB::table('stops')->insertGetId([
        'district_id' => $districtStartId,
        'name' => 'Ben Xe My Dinh '.$slugSuffix,
        'address' => 'Ha Noi',
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $dropoffStopId = DB::table('stops')->insertGetId([
        'district_id' => $districtEndId,
        'name' => 'Ben Xe Sapa '.$slugSuffix,
        'address' => 'Lao Cai',
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $routeId = DB::table('routes')->insertGetId([
        'province_start_id' => $provinceStartId,
        'province_end_id' => $provinceEndId,
        'name' => 'Ha Noi - Sapa '.$slugSuffix,
        'slug' => 'ha-noi-sapa-'.$slugSuffix,
        'price_default' => 0,
        'available_hotel_pickup' => false,
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
        'name' => 'King Sleeper '.$slugSuffix,
        'model_name' => 'Limousine',
        'seat_count' => 40,
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
        'pickup_stop_id' => $pickupStopId,
        'dropoff_stop_id' => $dropoffStopId,
    ];
}

it('queues booking cancelled mail when cancelBooking succeeds', function () {
    Mail::fake();

    $fixture = seedCancellationFixture('cancel-mail');
    $now = now();

    $bookingId = DB::table('bookings')->insertGetId([
        'booking_code' => 'CANCEL-001',
        'trip_id' => $fixture['trip_id'],
        'booking_date' => '2026-02-02',
        'customer_name' => 'Cancel User',
        'customer_phone' => '0900000000',
        'customer_email' => 'cancel@example.com',
        'pickup_stop_id' => $fixture['pickup_stop_id'],
        'dropoff_stop_id' => $fixture['dropoff_stop_id'],
        'quantity' => 1,
        'total_price' => 300000,
        'payment_method' => 'cash_on_pickup',
        'payment_status' => 'unpaid',
        'status' => 'confirmed',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $result = app(BookingService::class)->cancelBooking($bookingId, 'Customer request');

    expect($result['success'])->toBeTrue();

    $updatedBooking = DB::table('bookings')->where('id', $bookingId)->first();
    expect($updatedBooking->status)->toBe('cancelled');

    Mail::assertQueued(BookingCancelledMail::class, 2);
});
