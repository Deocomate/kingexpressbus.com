<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class DepartureAtExpression
{
    public static function asSelect(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "datetime(bookings.booking_date || ' ' || trips.start_time) as departure_at",
            default => 'TIMESTAMP(bookings.booking_date, trips.start_time) as departure_at',
        };
    }
}
