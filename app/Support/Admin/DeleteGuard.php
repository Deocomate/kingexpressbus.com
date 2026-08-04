<?php

namespace App\Support\Admin;

use RuntimeException;
use Illuminate\Support\Facades\DB;

/**
 * Port of BookingDeleteGuard with 2 additional counters for province/district chains.
 * Throws DeleteBlockedException instead of Filament Halt so controllers can catch and flash.
 */
class DeleteGuard
{
    public static function stopBookingCount(int $stopId): int
    {
        return (int) DB::table('bookings')
            ->where(function ($q) use ($stopId) {
                $q->where('pickup_stop_id', $stopId)
                    ->orWhere('dropoff_stop_id', $stopId);
            })
            ->count();
    }

    public static function tripBookingCount(int $tripId): int
    {
        return (int) DB::table('bookings')->where('trip_id', $tripId)->count();
    }

    public static function routeBookingCount(int $routeId): int
    {
        return (int) DB::table('bookings as b')
            ->join('trips as t', 'b.trip_id', '=', 't.id')
            ->where('t.route_id', $routeId)
            ->count();
    }

    public static function busBookingCount(int $busId): int
    {
        return (int) DB::table('bookings as b')
            ->join('trips as t', 'b.trip_id', '=', 't.id')
            ->where('t.bus_id', $busId)
            ->count();
    }

    /** Bookings reachable via province → districts → stops → bookings. */
    public static function provinceBookingCount(int $provinceId): int
    {
        return (int) DB::table('bookings as b')
            ->join('stops as s', function ($join) {
                $join->on('b.pickup_stop_id', '=', 's.id')
                    ->orOn('b.dropoff_stop_id', '=', 's.id');
            })
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->where('d.province_id', $provinceId)
            ->distinct('b.id')
            ->count('b.id');
    }

    /** Bookings reachable via district → stops → bookings. */
    public static function districtBookingCount(int $districtId): int
    {
        return (int) DB::table('bookings as b')
            ->join('stops as s', function ($join) {
                $join->on('b.pickup_stop_id', '=', 's.id')
                    ->orOn('b.dropoff_stop_id', '=', 's.id');
            })
            ->where('s.district_id', $districtId)
            ->distinct('b.id')
            ->count('b.id');
    }

    /**
     * @throws DeleteBlockedException when $count > 0
     */
    public static function assertNoBookings(int $count, string $label = ''): void
    {
        if ($count <= 0) {
            return;
        }

        $message = $label
            ? "Không thể xóa {$label}: còn {$count} đơn đặt vé liên quan."
            : "Không thể xóa: còn {$count} đơn đặt vé liên quan.";

        throw new DeleteBlockedException($message, $count);
    }
}
