<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RouteStopQuery
{
    /**
     * @return array<int, list<object>>
     */
    public static function groupedByRoute(array $routeIds): array
    {
        $routeIds = collect($routeIds)->filter()->unique()->values()->all();

        if (empty($routeIds)) {
            return [];
        }

        return DB::table('route_stops as rs')
            ->join('stops as s', 'rs.stop_id', '=', 's.id')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->whereIn('rs.route_id', $routeIds)
            ->select([
                'rs.route_id',
                's.id',
                's.name',
                's.address',
                'rs.stop_type',
                'p.name as province_name',
                'd.name as district_name',
            ])
            ->orderBy('rs.route_id')
            ->orderByDesc('rs.priority')
            ->get()
            ->groupBy('route_id')
            ->map(fn (Collection $stops) => $stops->values()->all())
            ->all();
    }

    public static function forRoute(int $routeId): Collection
    {
        return collect(self::groupedByRoute([$routeId])[$routeId] ?? []);
    }
}
