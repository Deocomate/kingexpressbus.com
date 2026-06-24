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
        return app(RouteService::class)->getPopularRoutes($limit);
    }

    /**
     * Get featured buses for homepage display.
     * In single-tenant mode, we show featured buses instead of companies.
     */
    public function getFeaturedBuses(int $limit = 8): Collection
    {
        $buses = DB::table('buses as b')
            ->select([
                'b.id',
                'b.name',
                'b.model_name',
                'b.thumbnail_url',
                'b.seat_count',
                // Count active trips using this bus
                DB::raw('(SELECT COUNT(*) FROM trips t
                         WHERE t.bus_id = b.id
                         AND t.is_active = 1) as trip_count'),
            ])
            ->orderByDesc('b.priority')
            ->limit($limit)
            ->get();

        $serviceMap = $this->getBusServicesMap($buses->pluck('id')->all());

        return $buses
            ->map(function ($bus) use ($serviceMap) {
                $serviceDetails = $serviceMap[$bus->id] ?? [];
                $bus->service_details = $serviceDetails;
                $bus->service_ids = collect($serviceDetails)->pluck('id')->values()->all();
                $bus->services = collect($serviceDetails)->pluck('name')->values()->all();

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
            'total_bookings' => 10_000,
        ];
    }

    private function getBusServicesMap(array $busIds): array
    {
        $busIds = collect($busIds)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($busIds)) {
            return [];
        }

        return DB::table('bus_bus_service as bbs')
            ->join('bus_services as bs', 'bbs.bus_service_id', '=', 'bs.id')
            ->whereIn('bbs.bus_id', $busIds)
            ->select('bbs.bus_id', 'bs.id', 'bs.name', 'bs.icon')
            ->orderBy('bs.priority', 'desc')
            ->orderBy('bs.name')
            ->get()
            ->groupBy('bus_id')
            ->map(fn ($services) => $services
                ->map(fn ($service) => [
                    'id' => (int) $service->id,
                    'name' => $service->name,
                    'icon' => $service->icon,
                ])
                ->values()
                ->all())
            ->all();
    }
}
