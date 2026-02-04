<?php

namespace App\Services;

use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service class for handling trip-related business logic.
 */
class TripService
{
    /**
     * Search trips by origin and destination province IDs.
     */
    public function searchTrips(int $originProvinceId, int $destinationProvinceId, string $date): Collection
    {
        $parsedDate = Carbon::parse($date)->format('Y-m-d');

        return DB::table('trips as t')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->join('buses as b', 't.bus_id', '=', 'b.id')
            ->where('r.province_start_id', $originProvinceId)
            ->where('r.province_end_id', $destinationProvinceId)
            ->where('t.is_active', true)
            ->select([
                't.id as trip_id',
                't.route_id',
                't.bus_id',
                't.start_time',
                't.end_time',
                't.price',
                't.priority',
                'r.name as route_name',
                'r.slug as route_slug',
                'r.duration',
                'r.distance_km',
                'r.available_hotel_pickup',
                'b.name as bus_name',
                'b.model_name as bus_model',
                'b.seat_count',
                'b.services as bus_services',
                'b.thumbnail_url as bus_thumbnail',
                'b.image_list_url as bus_images',
            ])
            ->orderBy('t.priority', 'desc')
            ->orderBy('t.start_time')
            ->get()
            ->map(fn($trip) => $this->enrichTripData($trip, $parsedDate));
    }

    /**
     * Get trips for a specific route.
     */
    public function getTripsByRoute(int $routeId, ?string $date = null): Collection
    {
        $parsedDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');

        return DB::table('trips as t')
            ->join('buses as b', 't.bus_id', '=', 'b.id')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->where('t.route_id', $routeId)
            ->where('t.is_active', true)
            ->select([
                't.id as trip_id',
                't.route_id',
                't.bus_id',
                't.start_time',
                't.end_time',
                't.price',
                't.priority',
                'r.name as route_name',
                'r.available_hotel_pickup',
                'b.name as bus_name',
                'b.model_name as bus_model',
                'b.seat_count',
                'b.services as bus_services',
                'b.thumbnail_url as bus_thumbnail',
                'b.image_list_url as bus_images',
            ])
            ->orderBy('t.priority', 'desc')
            ->orderBy('t.start_time')
            ->get()
            ->map(fn($trip) => $this->enrichTripData($trip, $parsedDate));
    }

    /**
     * Get single trip with full details.
     */
    public function getTripDetails(int $tripId): ?object
    {
        $trip = DB::table('trips as t')
            ->join('buses as b', 't.bus_id', '=', 'b.id')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->where('t.id', $tripId)
            ->select([
                't.id as trip_id',
                't.route_id',
                't.bus_id',
                't.start_time',
                't.end_time',
                't.price',
                't.is_active',
                't.priority',
                'r.name as route_name',
                'r.slug as route_slug',
                'r.duration',
                'r.distance_km',
                'r.available_hotel_pickup',
                'ps.name as start_province_name',
                'pe.name as end_province_name',
                'b.name as bus_name',
                'b.model_name as bus_model',
                'b.seat_count',
                'b.seat_map',
                'b.services as bus_services',
                'b.thumbnail_url as bus_thumbnail',
                'b.image_list_url as bus_images',
                'b.content as bus_content',
            ])
            ->first();

        if (!$trip) {
            return null;
        }

        // Get route stops
        $trip->stops = $this->getRouteStops($trip->route_id);
        $trip->bus_services = json_decode($trip->bus_services, true) ?? [];
        $trip->bus_images = json_decode($trip->bus_images, true) ?? [];
        $trip->seat_map = json_decode($trip->seat_map, true) ?? [];

        return $trip;
    }

    /**
     * Calculate available seats for a trip on a specific date.
     */
    public function calculateAvailableSeats(int $tripId, string $date, ?int $totalSeats = null): int
    {
        if ($totalSeats === null) {
            $totalSeats = DB::table('trips as t')
                ->join('buses as b', 't.bus_id', '=', 'b.id')
                ->where('t.id', $tripId)
                ->value('b.seat_count') ?? 0;
        }

        $bookedSeats = DB::table('bookings')
            ->where('trip_id', $tripId)
            ->whereDate('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->sum('quantity');

        return max(0, $totalSeats - $bookedSeats);
    }

    /**
     * Get route stops with details.
     */
    public function getRouteStops(int $routeId): Collection
    {
        return DB::table('route_stops as rs')
            ->join('stops as s', 'rs.stop_id', '=', 's.id')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->where('rs.route_id', $routeId)
            ->select([
                's.id',
                's.name',
                's.address',
                'rs.stop_type',
                'p.name as province_name',
                'd.name as district_name',
            ])
            ->orderBy('rs.priority')
            ->get();
    }

    /**
     * Enrich trip data with additional calculated fields.
     */
    private function enrichTripData(object $trip, string $date): object
    {
        // Calculate duration
        $start = Carbon::parse($trip->start_time);
        $end = Carbon::parse($trip->end_time);
        if ($end->lessThan($start)) {
            $end->addDay();
        }
        $trip->duration_minutes = $start->diffInMinutes($end);

        // Calculate available seats
        $trip->seats_available = $this->calculateAvailableSeats($trip->trip_id, $date, $trip->seat_count);
        $trip->has_price = $trip->price > 0;

        // Get stops
        $stops = $this->getRouteStops($trip->route_id);
        $trip->pickup_points = $stops->whereIn('stop_type', ['pickup', 'both'])->values();
        $trip->dropoff_points = $stops->whereIn('stop_type', ['dropoff', 'both'])->values();

        // Parse JSON fields
        $trip->services = json_decode($trip->bus_services, true) ?? [];
        $trip->image_gallery = json_decode($trip->bus_images, true) ?? [];
        if ($trip->bus_thumbnail) {
            array_unshift($trip->image_gallery, $trip->bus_thumbnail);
        }
        $trip->primary_bus_image = $trip->image_gallery[0] ?? null;

        return $trip;
    }

    /**
     * Get all trips for admin listing.
     */
    public function getAllTripsForAdmin(): Collection
    {
        return DB::table('trips as t')
            ->join('buses as b', 't.bus_id', '=', 'b.id')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->select([
                't.*',
                'b.name as bus_name',
                'b.model_name as bus_model',
                'r.name as route_name',
            ])
            ->orderBy('r.priority', 'desc')
            ->orderBy('t.priority', 'desc')
            ->orderBy('t.start_time')
            ->get();
    }

    /**
     * Get trips grouped by route for admin view.
     */
    public function getTripsGroupedByRoute(): array
    {
        // Get routes with their start provinces
        $routes = DB::table('routes as r')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->select([
                'r.id',
                'r.name',
                'r.province_start_id',
                'ps.name as start_province_name',
                'pe.name as end_province_name',
            ])
            ->orderBy('r.priority', 'desc')
            ->get();

        // Get all trips grouped by route_id
        $trips = DB::table('trips as t')
            ->join('buses as b', 't.bus_id', '=', 'b.id')
            ->select([
                't.*',
                'b.name as bus_name',
                'b.model_name as bus_model',
            ])
            ->orderBy('t.priority', 'desc')
            ->orderBy('t.start_time')
            ->get()
            ->groupBy('route_id');

        // Get unique start provinces
        $startProvinces = $routes->pluck('start_province_name', 'province_start_id')->unique();

        // Group routes by start province
        $routesByProvince = $routes->groupBy('province_start_id');

        return [
            'startProvinces' => $startProvinces,
            'routesByProvince' => $routesByProvince,
            'tripsByRoute' => $trips,
        ];
    }

    /**
     * Extract filter options from a collection of trips.
     * Returns available filters with their values.
     */
    public function extractFilterOptions(Collection $trips): array
    {
        $services = collect();
        $busCategories = collect();
        $prices = collect();
        $pickupPoints = collect();
        $dropoffPoints = collect();

        foreach ($trips as $trip) {
            // Collect services
            $tripServices = is_array($trip->services) ? $trip->services : [];
            $services = $services->merge($tripServices);

            // Collect bus categories/models
            if (!empty($trip->bus_model)) {
                $busCategories->push($trip->bus_model);
            }

            // Collect prices
            if ($trip->has_price && $trip->price > 0) {
                $prices->push((int) $trip->price);
            }

            // Collect pickup points
            if (isset($trip->pickup_points)) {
                foreach ($trip->pickup_points as $point) {
                    $pickupPoints->push($point->name ?? '');
                }
            }

            // Collect dropoff points
            if (isset($trip->dropoff_points)) {
                foreach ($trip->dropoff_points as $point) {
                    $dropoffPoints->push($point->name ?? '');
                }
            }
        }

        // Time range options with labels
        $timeRanges = [
            'early_morning' => ['label' => 'Sáng sớm (5h-8h)', 'min' => 5, 'max' => 8],
            'morning' => ['label' => 'Buổi sáng (8h-12h)', 'min' => 8, 'max' => 12],
            'afternoon' => ['label' => 'Buổi chiều (12h-17h)', 'min' => 12, 'max' => 17],
            'evening' => ['label' => 'Buổi tối (17h-21h)', 'min' => 17, 'max' => 21],
            'night' => ['label' => 'Đêm khuya (21h-5h)', 'min' => 21, 'max' => 5],
        ];

        return [
            'services' => $services->filter()->unique()->sort()->values()->toArray(),
            'bus_categories' => $busCategories->filter()->unique()->sort()->values()->toArray(),
            'pickup_points' => $pickupPoints->filter()->unique()->sort()->values()->toArray(),
            'dropoff_points' => $dropoffPoints->filter()->unique()->sort()->values()->toArray(),
            'time_ranges' => $timeRanges,
            'price' => [
                'min' => $prices->min() ?? 0,
                'max' => $prices->max() ?? 0,
            ],
        ];
    }

    /**
     * Apply filters to a collection of trips.
     */
    public function applyFilters(Collection $trips, array $filters): Collection
    {
        return $trips->filter(function ($trip) use ($filters) {
            // Price filter
            if (!empty($filters['price_min']) && $trip->price < (int) $filters['price_min']) {
                return false;
            }
            if (!empty($filters['price_max']) && $trip->price > (int) $filters['price_max']) {
                return false;
            }

            // Services filter (trip must have ALL selected services)
            if (!empty($filters['services']) && is_array($filters['services'])) {
                $tripServices = is_array($trip->services) ? $trip->services : [];
                foreach ($filters['services'] as $service) {
                    if (!in_array($service, $tripServices, true)) {
                        return false;
                    }
                }
            }

            // Bus categories filter (trip must match ONE of selected categories)
            if (!empty($filters['bus_categories']) && is_array($filters['bus_categories'])) {
                if (!in_array($trip->bus_model ?? '', $filters['bus_categories'], true)) {
                    return false;
                }
            }

            // Time range filter
            if (!empty($filters['time_ranges']) && is_array($filters['time_ranges'])) {
                $tripHour = (int) Carbon::parse($trip->start_time)->format('H');
                $matchesTimeRange = false;

                $timeRangeDefinitions = [
                    'early_morning' => ['min' => 5, 'max' => 8],
                    'morning' => ['min' => 8, 'max' => 12],
                    'afternoon' => ['min' => 12, 'max' => 17],
                    'evening' => ['min' => 17, 'max' => 21],
                    'night' => ['min' => 21, 'max' => 5],
                ];

                foreach ($filters['time_ranges'] as $range) {
                    if (!isset($timeRangeDefinitions[$range])) {
                        continue;
                    }
                    $def = $timeRangeDefinitions[$range];

                    // Handle night range that crosses midnight
                    if ($def['min'] > $def['max']) {
                        if ($tripHour >= $def['min'] || $tripHour < $def['max']) {
                            $matchesTimeRange = true;
                            break;
                        }
                    } else {
                        if ($tripHour >= $def['min'] && $tripHour < $def['max']) {
                            $matchesTimeRange = true;
                            break;
                        }
                    }
                }

                if (!$matchesTimeRange) {
                    return false;
                }
            }

            // Seats available filter
            if (!empty($filters['has_seats']) && ($trip->seats_available ?? 0) <= 0) {
                return false;
            }

            return true;
        })->values();
    }

    /**
     * Apply sorting to a collection of trips.
     */
    public function applySorting(Collection $trips, string $sortBy = 'recommended'): Collection
    {
        return match ($sortBy) {
            'earliest' => $trips->sortBy(fn($trip) => Carbon::parse($trip->start_time))->values(),
            'latest' => $trips->sortByDesc(fn($trip) => Carbon::parse($trip->start_time))->values(),
            'price_low' => $trips->sortBy(fn($trip) => $trip->price ?? PHP_INT_MAX)->values(),
            'price_high' => $trips->sortByDesc(fn($trip) => $trip->price ?? 0)->values(),
            'seats_available' => $trips->sortByDesc(fn($trip) => $trip->seats_available ?? 0)->values(),
            default => $trips->sortByDesc(fn($trip) => $trip->priority ?? 0)->values(), // recommended
        };
    }

    /**
     * Get trips for a route with filtering and sorting applied.
     */
    public function getFilteredTripsByRoute(int $routeId, ?string $date = null, array $filters = [], string $sortBy = 'recommended'): array
    {
        // Get all trips for the route
        $allTrips = $this->getTripsByRoute($routeId, $date);

        // Extract filter options before filtering
        $filterOptions = $this->extractFilterOptions($allTrips);

        // Apply filters
        $filteredTrips = $this->applyFilters($allTrips, $filters);

        // Apply sorting
        $sortedTrips = $this->applySorting($filteredTrips, $sortBy);

        return [
            'trips' => $sortedTrips,
            'filters' => $filterOptions,
            'stats' => [
                'total' => $allTrips->count(),
                'filtered' => $sortedTrips->count(),
            ],
        ];
    }
}

