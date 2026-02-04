<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service class for handling homepage-related business logic.
 */
class HomeService
{
    /**
     * Get popular routes for homepage display.
     * Returns routes with min price from trips table.
     */
    public function getPopularRoutes(int $limit = 8): Collection
    {
        return DB::table('routes as r')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->select([
                'r.id',
                'r.name',
                'r.slug',
                'r.description',
                'r.duration',
                'r.distance_km',
                'r.thumbnail_url',
                'r.price_default',
                'ps.name as start_province_name',
                'pe.name as end_province_name',
                // Get min active price from trips
                DB::raw('COALESCE(
                    (SELECT MIN(t.price) FROM trips t
                     WHERE t.route_id = r.id
                     AND t.price > 0
                     AND t.is_active = 1),
                    r.price_default,
                    0
                ) as min_price'),
                // Count active trips for this route
                DB::raw('(SELECT COUNT(*) FROM trips t
                         WHERE t.route_id = r.id
                         AND t.is_active = 1) as trip_count'),
            ])
            ->orderByDesc('r.priority')
            ->orderByDesc('r.created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured buses for homepage display.
     * In single-tenant mode, we show featured buses instead of companies.
     */
    public function getFeaturedBuses(int $limit = 8): Collection
    {
        return DB::table('buses as b')
            ->select([
                'b.id',
                'b.name',
                'b.model_name',
                'b.thumbnail_url',
                'b.seat_count',
                'b.services',
                // Count active trips using this bus
                DB::raw('(SELECT COUNT(*) FROM trips t
                         WHERE t.bus_id = b.id
                         AND t.is_active = 1) as trip_count'),
            ])
            ->orderByDesc('b.priority')
            ->limit($limit)
            ->get()
            ->map(function ($bus) {
                $bus->services = json_decode($bus->services, true) ?? [];
                return $bus;
            });
    }

    /**
     * Get homepage statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total_routes' => DB::table('routes')->count(),
            'total_buses' => DB::table('buses')->count(),
            'total_trips' => DB::table('trips')->where('is_active', true)->count(),
            'total_bookings' => DB::table('bookings')
                ->whereIn('status', ['confirmed', 'completed'])
                ->count(),
        ];
    }
}
