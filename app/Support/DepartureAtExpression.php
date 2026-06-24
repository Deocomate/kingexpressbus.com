<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class DepartureAtExpression
{
    public static function asSelect(): string
    {
        return self::expression().' as departure_at';
    }

    public static function expression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "(SELECT datetime(bookings.booking_date || ' ' || t.start_time) FROM trips t WHERE t.id = bookings.trip_id)",
            default => '(SELECT TIMESTAMP(bookings.booking_date, t.start_time) FROM trips t WHERE t.id = bookings.trip_id)',
        };
    }

    public static function tripStartTimeSubquery(): string
    {
        return '(SELECT start_time FROM trips WHERE trips.id = bookings.trip_id)';
    }
}
