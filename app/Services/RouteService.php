<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Service class for handling route-related business logic.
 */
class RouteService
{
    /**
     * Get all routes grouped by start province.
     */
    public function getRoutesGroupedByProvince(): array
    {
        $startProvinces = DB::table('routes as r')
            ->join('provinces as p', 'r.province_start_id', '=', 'p.id')
            ->select('p.id', 'p.name')
            ->distinct()
            ->orderBy('p.name')
            ->get();

        $routes = DB::table('routes as r')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->select('r.*', 'ps.name as start_province_name', 'pe.name as end_province_name')
            ->orderBy('r.priority', 'desc')
            ->get()
            ->groupBy('province_start_id');

        return [
            'startProvinces' => $startProvinces,
            'routes' => $routes,
        ];
    }

    /**
     * Get route by slug with full details.
     */
    public function getRouteBySlug(string $slug): ?object
    {
        $route = DB::table('routes')
            ->where('slug', $slug)
            ->select(
                'routes.*',
                DB::raw('(SELECT name FROM provinces WHERE id = routes.province_start_id) as start_province_name'),
                DB::raw('(SELECT name FROM provinces WHERE id = routes.province_end_id) as end_province_name')
            )
            ->first();

        if ($route) {
            // Get stops for this route
            $route->stops = $this->getRouteStops($route->id);
        }

        return $route;
    }

    /**
     * Get route by ID.
     */
    public function getRouteById(int $id): ?object
    {
        return DB::table('routes')->find($id);
    }

    /**
     * Get stops for a route.
     */
    public function getRouteStops(int $routeId): Collection
    {
        return DB::table('route_stops as rs')
            ->join('stops as s', 'rs.stop_id', '=', 's.id')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->where('rs.route_id', $routeId)
            ->select([
                's.id as stop_id',
                's.name',
                's.address',
                'rs.stop_type',
                'd.name as district_name',
                'p.name as province_name',
                DB::raw("CONCAT(d.name, ', ', p.name) as location"),
            ])
            ->orderBy('rs.priority')
            ->get();
    }

    /**
     * Create a new route.
     */
    public function createRoute(array $data): int
    {
        $data['slug'] = Str::slug($data['name']);
        $data['created_at'] = Carbon::now();
        $data['updated_at'] = Carbon::now();

        return DB::transaction(function () use ($data) {
            // Extract stops data if present
            $stopsData = $data['stops'] ?? [];
            unset($data['stops']);

            $routeId = DB::table('routes')->insertGetId($data);

            // Insert route stops
            if (!empty($stopsData)) {
                $this->syncRouteStops($routeId, $stopsData);
            }

            return $routeId;
        });
    }

    /**
     * Update a route.
     */
    public function updateRoute(int $id, array $data): bool
    {
        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['updated_at'] = Carbon::now();

        return DB::transaction(function () use ($id, $data) {
            // Extract stops data if present
            $stopsData = $data['stops'] ?? null;
            unset($data['stops']);

            DB::table('routes')->where('id', $id)->update($data);

            // Sync route stops if provided
            if ($stopsData !== null) {
                $this->syncRouteStops($id, $stopsData);
            }

            return true;
        });
    }

    /**
     * Delete a route.
     */
    public function deleteRoute(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            DB::table('route_stops')->where('route_id', $id)->delete();
            return DB::table('routes')->where('id', $id)->delete() > 0;
        });
    }

    /**
     * Sync route stops.
     */
    public function syncRouteStops(int $routeId, array $stopsData): void
    {
        DB::table('route_stops')->where('route_id', $routeId)->delete();

        if (empty($stopsData)) {
            return;
        }

        $now = Carbon::now();
        $stopsToInsert = [];

        foreach ($stopsData as $index => $stop) {
            $stopsToInsert[] = [
                'route_id' => $routeId,
                'stop_id' => $stop['stop_id'],
                'stop_type' => $stop['stop_type'] ?? 'both',
                'priority' => $index,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('route_stops')->insert($stopsToInsert);
    }

    /**
     * Update route order (priority).
     */
    public function updateOrder(array $order): void
    {
        DB::transaction(function () use ($order) {
            foreach ($order as $index => $routeId) {
                DB::table('routes')->where('id', $routeId)->update(['priority' => $index]);
            }
        });
    }

    /**
     * Get popular routes for homepage.
     * Optionally filter by starting province.
     */
    public function getPopularRoutes(int $limit = 8, ?int $provinceId = null): Collection
    {
        $query = DB::table('routes')
            ->join('provinces as ps', 'routes.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'routes.province_end_id', '=', 'pe.id')
            ->select([
                'routes.*',
                'ps.name as start_province_name',
                'pe.name as end_province_name',
            ]);

        if ($provinceId) {
            $query->where('routes.province_start_id', $provinceId);
        }

        return $query
            ->orderByDesc('routes.priority')
            ->limit($limit)
            ->get()
            ->map(function ($route) {
                // Get min price and trip count for each route
                $route->min_price = DB::table('trips')
                    ->where('route_id', $route->id)
                    ->where('is_active', true)
                    ->min('price');

                $route->trip_count = DB::table('trips')
                    ->where('route_id', $route->id)
                    ->where('is_active', true)
                    ->count();

                return $route;
            });
    }

    /**
     * Get provinces that have active routes (for filter tabs).
     */
    public function getProvincesWithRoutes(): Collection
    {
        return DB::table('provinces')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('routes')
                    ->whereColumn('routes.province_start_id', 'provinces.id');
            })
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Find route by province IDs.
     */
    public function findRouteByProvinces(int $startProvinceId, int $endProvinceId): ?object
    {
        return DB::table('routes')
            ->where('province_start_id', $startProvinceId)
            ->where('province_end_id', $endProvinceId)
            ->first();
    }

    /**
     * Resolve province ID from location type and ID.
     */
    public function resolveProvinceId(string $type, int $id): ?int
    {
        return match ($type) {
            'province' => $id,
            'district' => DB::table('districts')->where('id', $id)->value('province_id'),
            'stop' => DB::table('stops as s')
                ->join('districts as d', 's.district_id', '=', 'd.id')
                ->where('s.id', $id)
                ->value('d.province_id'),
            default => null,
        };
    }

    /**
     * Get all provinces.
     */
    public function getAllProvinces(): Collection
    {
        return DB::table('provinces')->orderBy('name')->get();
    }

    /**
     * Get all stops with location info.
     */
    public function getAllStopsWithLocation(): Collection
    {
        return DB::table('stops as s')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->select('s.id', 's.name', 's.address', DB::raw("CONCAT(d.name, ', ', p.name) as location"))
            ->orderBy('p.name')
            ->orderBy('d.name')
            ->orderBy('s.name')
            ->get();
    }
}
