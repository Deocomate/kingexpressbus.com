<?php

namespace App\Filament\Support;

use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\DB;

class BookingDeleteGuard
{
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

    public static function stopBookingCount(int $stopId): int
    {
        return (int) DB::table('bookings')
            ->where(function ($query) use ($stopId) {
                $query->where('pickup_stop_id', $stopId)
                    ->orWhere('dropoff_stop_id', $stopId);
            })
            ->count();
    }

    public static function haltIfBookingsExist(int $count): void
    {
        if ($count <= 0) {
            return;
        }

        Notification::make()
            ->danger()
            ->title("Không thể xóa: còn {$count} đơn đặt vé liên quan.")
            ->send();

        throw new Halt();
    }
}
