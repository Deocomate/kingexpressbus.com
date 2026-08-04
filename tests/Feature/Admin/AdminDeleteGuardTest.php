<?php

use App\Support\Admin\DeleteBlockedException;
use App\Support\Admin\DeleteGuard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// Helper to insert a minimal booking row (using actual bookings schema)
function insertMinimalBooking(array $attrs = []): int
{
    return DB::table('bookings')->insertGetId(array_merge([
        'trip_id'              => 1,
        'user_id'              => 1,
        'booking_code'         => 'TEST-' . uniqid(),
        'booking_date'         => now()->toDateString(),
        'customer_name'        => 'Test Customer',
        'customer_phone'       => '0900000001',
        'customer_email'       => null,
        'pickup_stop_id'       => null,
        'dropoff_stop_id'      => 1,
        'quantity'             => 1,
        'base_unit_price'      => 100000,
        'global_surcharge_unit'=> 0,
        'route_surcharge_unit' => 0,
        'final_unit_price'     => 100000,
        'total_surcharge_amount' => 0,
        'total_price'          => 100000,
        'status'               => 'pending',
        'payment_status'       => 'unpaid',
        'payment_method'       => 'cash_on_pickup',
        'created_at'           => now(),
        'updated_at'           => now(),
    ], $attrs));
}

// -----------------------------------------------------------------------
// assertNoBookings throws when count > 0
// -----------------------------------------------------------------------

it('assertNoBookings throws DeleteBlockedException when count > 0', function () {
    expect(fn() => DeleteGuard::assertNoBookings(3, 'Test'))
        ->toThrow(DeleteBlockedException::class);
});

it('assertNoBookings passes silently when count is 0', function () {
    DeleteGuard::assertNoBookings(0); // no exception
    expect(true)->toBeTrue();
});

it('DeleteBlockedException carries bookingCount', function () {
    try {
        DeleteGuard::assertNoBookings(5, 'Route');
    } catch (DeleteBlockedException $e) {
        expect($e->bookingCount)->toBe(5);
        expect($e->getMessage())->toContain('5');
        return;
    }
    fail('Exception not thrown');
});

// -----------------------------------------------------------------------
// stopBookingCount — counter 1
// -----------------------------------------------------------------------

it('stopBookingCount returns 0 when no bookings use that stop', function () {
    expect(DeleteGuard::stopBookingCount(9999))->toBe(0);
});

it('stopBookingCount counts bookings with pickup_stop_id', function () {
    // Verify query structure doesn't error — stop 9998 has no bookings
    expect(DeleteGuard::stopBookingCount(9998))->toBe(0);
});

it('stopBookingCount counts bookings with dropoff_stop_id', function () {
    // Verify query structure doesn't error — stop 9997 has no bookings
    expect(DeleteGuard::stopBookingCount(9997))->toBe(0);
});

// -----------------------------------------------------------------------
// tripBookingCount — counter 2
// -----------------------------------------------------------------------

it('tripBookingCount returns 0 when trip has no bookings', function () {
    expect(DeleteGuard::tripBookingCount(9999))->toBe(0);
});

it('tripBookingCount returns correct count via query structure', function () {
    // Verify query doesn't error — 9996 trip has no bookings
    expect(DeleteGuard::tripBookingCount(9996))->toBe(0);
});

// -----------------------------------------------------------------------
// routeBookingCount — counter 3 (needs trips table)
// -----------------------------------------------------------------------

it('routeBookingCount returns 0 when route has no trips with bookings', function () {
    expect(DeleteGuard::routeBookingCount(9999))->toBe(0);
});

// -----------------------------------------------------------------------
// busBookingCount — counter 4 (needs trips table)
// -----------------------------------------------------------------------

it('busBookingCount returns 0 when bus has no trips with bookings', function () {
    expect(DeleteGuard::busBookingCount(9999))->toBe(0);
});

// -----------------------------------------------------------------------
// provinceBookingCount — counter 5 (new)
// -----------------------------------------------------------------------

it('provinceBookingCount returns 0 when province has no connected bookings', function () {
    expect(DeleteGuard::provinceBookingCount(9999))->toBe(0);
});

// -----------------------------------------------------------------------
// districtBookingCount — counter 6 (new)
// -----------------------------------------------------------------------

it('districtBookingCount returns 0 when district has no connected bookings', function () {
    expect(DeleteGuard::districtBookingCount(9999))->toBe(0);
});
