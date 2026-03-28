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

        // Fetch bookings with optimized query
        $bookings = $this->getBookings($user->id);

        $today = Carbon::today();

        // Partition bookings into upcoming and history
        $upcomingBookings = $bookings->filter(fn($b) => Carbon::parse($b->booking_date)->gte($today));
        $bookingHistory = $bookings->reject(fn($b) => Carbon::parse($b->booking_date)->gte($today));

        // Calculate statistics
        $stats = [
            'total_bookings' => $bookings->count(),
            'upcoming' => $upcomingBookings->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
            'total_spent' => $bookings->whereIn('status', ['confirmed', 'completed'])->sum('total_price'),
        ];

        // Process preferred routes
        $preferredRoutes = $this->getPreferredRoutes($bookings);

        return view('client.profile.index', [
            'user' => $user,
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
            ->get();
    }

    private function getPreferredRoutes($bookings)
    {
        return $bookings
            ->groupBy('route_slug')
            ->map(function ($items, $slug) {
                return [
                    'slug' => $slug,
                    'name' => $items->first()->route_name ?? $slug,
                    'count' => $items->count(),
                ];
            })
            ->sortByDesc('count')
            ->values();
    }
}
