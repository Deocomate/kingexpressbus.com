<?php

namespace App\Support\Admin\Dashboard;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Support\DepartureAtExpression;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Latest upcoming bookings for the dashboard table widget.
 * Mirrors LatestBookings widget: upcoming (pending+confirmed), ordered by departure, limit 10.
 */
class LatestBookingsData
{
    public readonly Collection $bookings;

    private function __construct()
    {
        $this->bookings = Booking::query()
            ->with(['trip.route'])
            ->select('bookings.*')
            ->addSelect(DB::raw(DepartureAtExpression::asSelect()))
            ->whereDate('bookings.booking_date', '>=', now()->toDateString())
            ->whereIn('bookings.status', [BookingStatus::Pending, BookingStatus::Confirmed])
            ->orderBy('bookings.booking_date')
            ->orderByRaw(DepartureAtExpression::tripStartTimeSubquery())
            ->limit(10)
            ->get();
    }

    public static function load(): self
    {
        return new self();
    }
}
