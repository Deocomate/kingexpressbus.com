<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        abort_if(!$user, 403);

        $bookings = $this->getBookings($user->id);
        $today = Carbon::today();

        $pageBookings = collect($bookings->items());
        $upcomingBookings = $pageBookings->filter(fn ($b) => Carbon::parse($b->booking_date)->gte($today));
        $bookingHistory = $pageBookings->reject(fn ($b) => Carbon::parse($b->booking_date)->gte($today));

        $stats = $this->getBookingStats($user->id);
        $preferredRoutes = $this->getPreferredRoutesForUser($user->id);

        return view('client.profile.index', [
            'user' => $user,
            'bookings' => $bookings,
            'upcomingBookings' => $upcomingBookings,
            'bookingHistory' => $bookingHistory,
            'stats' => $stats,
            'preferredRoutes' => $preferredRoutes,
            'title' => __('client.profile_page.meta.title'),
            'description' => __('client.profile_page.meta.description'),
        ]);
    }

    private function getBookings($userId)
    {
        // Updated for single-tenant schema: trips instead of bus_routes/company_routes
        return DB::table('bookings as b')
            ->join('trips as t', 'b.trip_id', '=', 't.id')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->join('buses as bus', 't.bus_id', '=', 'bus.id')
            ->leftJoin('stops as ps', 'b.pickup_stop_id', '=', 'ps.id')
            ->leftJoin('stops as ds', 'b.dropoff_stop_id', '=', 'ds.id')
            ->select([
                'b.id',
                'b.booking_code',
                'b.booking_date',
                'b.status',
                'b.payment_method',
                'b.payment_status',
                'b.quantity',
                'b.total_price',
                'b.created_at',
                'b.notes',
                't.start_time',
                't.end_time',
                't.price as unit_price',
                'r.name as route_name',
                'r.slug as route_slug',
                'bus.name as bus_name',
                'bus.model_name as bus_model',
                'ps.name as pickup_name',
                'ps.address as pickup_address',
                'ds.name as dropoff_name',
                'ds.address as dropoff_address',
            ])
            ->where('b.user_id', $userId)
            ->orderByDesc('b.booking_date')
            ->orderByDesc('b.created_at')
            ->paginate(15);
    }

    private function getBookingStats(int $userId): array
    {
        $today = Carbon::today()->toDateString();
        $query = DB::table('bookings')->where('user_id', $userId);

        return [
            'total_bookings' => (clone $query)->count(),
            'upcoming' => (clone $query)->where('booking_date', '>=', $today)->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'total_spent' => (clone $query)->whereIn('status', ['confirmed', 'completed'])->sum('total_price'),
        ];
    }

    private function getPreferredRoutesForUser(int $userId)
    {
        return DB::table('bookings as b')
            ->join('trips as t', 'b.trip_id', '=', 't.id')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->where('b.user_id', $userId)
            ->select([
                'r.slug as route_slug',
                'r.name as route_name',
                DB::raw('count(*) as booking_count'),
            ])
            ->groupBy('r.slug', 'r.name')
            ->orderByDesc('booking_count')
            ->get()
            ->map(fn ($row) => [
                'slug' => $row->route_slug,
                'name' => $row->route_name ?? $row->route_slug,
                'count' => (int) $row->booking_count,
            ])
            ->values();
    }
}
