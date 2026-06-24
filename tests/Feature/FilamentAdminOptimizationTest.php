<?php

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function optimizationBookingFixture(): array
{
    $now = now();

    $provinceId = DB::table('provinces')->insertGetId([
        'name' => 'Ha Noi',
        'slug' => 'ha-noi-opt',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $districtTypeId = DB::table('district_types')->insertGetId([
        'name' => 'Urban',
        'priority' => 0,
    ]);

    $districtId = DB::table('districts')->insertGetId([
        'province_id' => $provinceId,
        'district_type_id' => $districtTypeId,
        'name' => 'District',
        'slug' => 'district-opt',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $pickupStopId = DB::table('stops')->insertGetId([
        'district_id' => $districtId,
        'name' => 'Pickup',
        'address' => 'Address',
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $dropoffStopId = DB::table('stops')->insertGetId([
        'district_id' => $districtId,
        'name' => 'Dropoff',
        'address' => 'Address',
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $routeId = DB::table('routes')->insertGetId([
        'province_start_id' => $provinceId,
        'province_end_id' => $provinceId,
        'name' => 'Route',
        'slug' => 'route-opt',
        'price_default' => 0,
        'available_hotel_pickup' => false,
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $busId = DB::table('buses')->insertGetId([
        'name' => 'Bus',
        'model_name' => 'Limousine',
        'seat_count' => 40,
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    return compact('pickupStopId', 'dropoffStopId', 'routeId', 'busId');
}

test('application timezone is configured for vietnam', function () {
    expect(config('app.timezone'))->toBe('Asia/Ho_Chi_Minh')
        ->and(now()->timezone->getName())->toBe('Asia/Ho_Chi_Minh');
});

test('bookings list defaults to upcoming tab and sorts nearest departures first', function () {
    $this->actingAs(User::factory()->admin()->create());

    $fixture = optimizationBookingFixture();
    $now = now();

    $earlyTripId = DB::table('trips')->insertGetId([
        'bus_id' => $fixture['busId'],
        'route_id' => $fixture['routeId'],
        'start_time' => '06:00:00',
        'end_time' => '10:00:00',
        'price' => 300000,
        'is_active' => true,
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $lateTripId = DB::table('trips')->insertGetId([
        'bus_id' => $fixture['busId'],
        'route_id' => $fixture['routeId'],
        'start_time' => '18:00:00',
        'end_time' => '22:00:00',
        'price' => 300000,
        'is_active' => true,
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $laterBookingId = DB::table('bookings')->insertGetId([
        'booking_code' => 'LATE-BOOKING',
        'trip_id' => $lateTripId,
        'booking_date' => now()->addDay()->toDateString(),
        'customer_name' => 'Late User',
        'customer_phone' => '0900000001',
        'pickup_stop_id' => $fixture['pickupStopId'],
        'dropoff_stop_id' => $fixture['dropoffStopId'],
        'quantity' => 1,
        'total_price' => 300000,
        'payment_method' => 'cash_on_pickup',
        'payment_status' => 'unpaid',
        'status' => 'confirmed',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $soonerBookingId = DB::table('bookings')->insertGetId([
        'booking_code' => 'EARLY-BOOKING',
        'trip_id' => $earlyTripId,
        'booking_date' => now()->addDay()->toDateString(),
        'customer_name' => 'Early User',
        'customer_phone' => '0900000002',
        'pickup_stop_id' => $fixture['pickupStopId'],
        'dropoff_stop_id' => $fixture['dropoffStopId'],
        'quantity' => 1,
        'total_price' => 300000,
        'payment_method' => 'cash_on_pickup',
        'payment_status' => 'unpaid',
        'status' => 'pending',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $soonerBooking = Booking::query()->findOrFail($soonerBookingId);
    $laterBooking = Booking::query()->findOrFail($laterBookingId);

    Livewire::test(ListBookings::class)
        ->assertSuccessful()
        ->assertSee('Sắp đi')
        ->assertSee('Chờ xác nhận')
        ->assertCanSeeTableRecords([$soonerBooking, $laterBooking], inOrder: true);
});

test('booking status enum provides vietnamese labels and colors', function () {
    expect(BookingStatus::Pending->getLabel())->toBe('Chờ xác nhận')
        ->and(BookingStatus::Confirmed->getColor())->toBe('success');
});

test('booking model casts status to enum', function () {
    $fixture = optimizationBookingFixture();
    $now = now();

    $tripId = DB::table('trips')->insertGetId([
        'bus_id' => $fixture['busId'],
        'route_id' => $fixture['routeId'],
        'start_time' => '08:00:00',
        'end_time' => '12:00:00',
        'price' => 300000,
        'is_active' => true,
        'priority' => 0,
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $bookingId = DB::table('bookings')->insertGetId([
        'booking_code' => 'ENUM-CAST',
        'trip_id' => $tripId,
        'booking_date' => now()->addDay()->toDateString(),
        'customer_name' => 'Enum User',
        'customer_phone' => '0900000003',
        'pickup_stop_id' => $fixture['pickupStopId'],
        'dropoff_stop_id' => $fixture['dropoffStopId'],
        'quantity' => 1,
        'total_price' => 300000,
        'payment_method' => 'online_banking',
        'payment_status' => 'unpaid',
        'status' => 'pending',
        'created_at' => $now,
        'updated_at' => $now,
    ]);

    $booking = Booking::query()->findOrFail($bookingId);

    expect($booking->status)->toBe(BookingStatus::Pending)
        ->and($booking->payment_status)->toBe(PaymentStatus::Unpaid);
});
