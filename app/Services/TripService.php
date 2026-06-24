<?php

namespace App\Services;

use App\Support\RouteStopQuery;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Service class for handling trip-related business logic.
 */
class TripService
{
    protected HolidaySurchargeService $holidaySurchargeService;

    public function __construct(HolidaySurchargeService $holidaySurchargeService)
    {
        $this->holidaySurchargeService = $holidaySurchargeService;
    }

    /**
     * Search trips by origin and destination province IDs.
     */
    public function searchTrips(int $originProvinceId, int $destinationProvinceId, string $date): Collection
    {
        $parsedDate = Carbon::parse($date)->format('Y-m-d');

        $trips = DB::table('trips as t')
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
                'b.thumbnail_url as bus_thumbnail',
                'b.image_list_url as bus_images',
            ])
            ->orderBy('t.priority', 'desc')
            ->orderBy('t.start_time')
            ->get();

        $tripIds = $trips->pluck('trip_id')->all();
        $busServicesMap = $this->getBusServicesMap($trips->pluck('bus_id')->all());
        $tripBlocksMap = $this->getTripBlocksMap($tripIds, $parsedDate);
        $bookedSeatsMap = $this->getBookedSeatsMap($tripIds, $parsedDate);
        $routeStopsMap = RouteStopQuery::groupedByRoute($trips->pluck('route_id')->unique()->all());

        return $trips->map(fn ($trip) => $this->enrichTripData(
            $trip,
            $parsedDate,
            $busServicesMap,
            $tripBlocksMap,
            $bookedSeatsMap,
            $routeStopsMap,
        ));
    }

    /**
     * Get trips for a specific route.
     */
    public function getTripsByRoute(int $routeId, ?string $date = null): Collection
    {
        $parsedDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');

        $trips = DB::table('trips as t')
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
                'b.thumbnail_url as bus_thumbnail',
                'b.image_list_url as bus_images',
            ])
            ->orderBy('t.priority', 'desc')
            ->orderBy('t.start_time')
            ->get();

        $tripIds = $trips->pluck('trip_id')->all();
        $busServicesMap = $this->getBusServicesMap($trips->pluck('bus_id')->all());
        $tripBlocksMap = $this->getTripBlocksMap($tripIds, $parsedDate);
        $bookedSeatsMap = $this->getBookedSeatsMap($tripIds, $parsedDate);
        $routeStopsMap = RouteStopQuery::groupedByRoute([$routeId]);

        return $trips->map(fn ($trip) => $this->enrichTripData(
            $trip,
            $parsedDate,
            $busServicesMap,
            $tripBlocksMap,
            $bookedSeatsMap,
            $routeStopsMap,
        ));
    }

    /**
     * Get single trip with full details.
     */
    public function getTripDetails(int $tripId, ?string $date = null): ?object
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
                'b.thumbnail_url as bus_thumbnail',
                'b.image_list_url as bus_images',
                'b.content as bus_content',
            ])
            ->first();

        if (! $trip) {
            return null;
        }

        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');

        $block = DB::table('trip_blocks')
            ->where('trip_id', $trip->trip_id)
            ->where('start_date', '<=', $targetDate)
            ->where('end_date', '>=', $targetDate)
            ->first();

        if ($block && $block->block_type === 'off_day') {
            $trip->is_off_day = true;
            $trip->block_note = $block->note;
        } else {
            $trip->is_off_day = false;
            $trip->block_note = null;
        }

        // Get route stops
        $trip->stops = RouteStopQuery::forRoute((int) $trip->route_id);
        $serviceDetails = $this->getBusServicesMap([$trip->bus_id])[$trip->bus_id] ?? [];
        $trip->bus_service_details = $this->withoutServicePriority($serviceDetails);
        $trip->bus_service_ids = collect($serviceDetails)->pluck('id')->values()->all();
        $trip->bus_services = collect($serviceDetails)->pluck('name')->values()->all();
        $trip->service_details = $trip->bus_service_details;
        $trip->service_ids = $trip->bus_service_ids;
        $trip->services = $trip->bus_services;
        $trip->bus_images = json_decode($trip->bus_images, true) ?? [];

        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $priceBreakdown = $this->holidaySurchargeService->calculateBreakdownByRouteAndBasePrice(
            (int) $trip->route_id,
            (int) ($trip->price ?? 0),
            $targetDate
        );

        $trip->base_price = $priceBreakdown['base_unit_price'];
        $trip->global_surcharge = $priceBreakdown['global_surcharge_unit'];
        $trip->route_surcharge = $priceBreakdown['route_surcharge_unit'];
        $trip->surcharge_total = $priceBreakdown['total_surcharge_unit'];
        $trip->effective_price = $priceBreakdown['final_unit_price'];
        $trip->has_surcharge = $priceBreakdown['has_surcharge'];
        $trip->surcharge_reasons = $priceBreakdown['surcharge_reasons'];
        $trip->surcharge_reason_snapshot = $priceBreakdown['surcharge_reason_snapshot'];
        $trip->has_price = $trip->effective_price > 0;

        return $trip;
    }

    /**
     * Calculate available seats for a trip on a specific date.
     */
    public function calculateAvailableSeats(int $tripId, string $date, ?int $totalSeats = null): int
    {
        // Check for trip block
        $block = DB::table('trip_blocks')
            ->where('trip_id', $tripId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->first();

        if ($block && in_array($block->block_type, ['sold_out', 'off_day'], true)) {
            return 0;
        }

        if ($totalSeats === null) {
            $totalSeats = DB::table('trips as t')
                ->join('buses as b', 't.bus_id', '=', 'b.id')
                ->where('t.id', $tripId)
                ->value('b.seat_count') ?? 0;
        }

        $bookedSeats = DB::table('bookings')
            ->where('trip_id', $tripId)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->sum('quantity');

        return max(0, $totalSeats - $bookedSeats);
    }

    /**
     * Get route stops with details.
     */
    public function getRouteStops(int $routeId): Collection
    {
        return RouteStopQuery::forRoute($routeId);
    }

    /**
     * Enrich trip data with additional calculated fields.
     */
    private function enrichTripData(
        object $trip,
        string $date,
        array $busServicesMap = [],
        array $tripBlocksMap = [],
        array $bookedSeatsMap = [],
        array $routeStopsMap = [],
    ): object {
        $priceBreakdown = $this->holidaySurchargeService->calculateBreakdownByRouteAndBasePrice(
            (int) $trip->route_id,
            (int) ($trip->price ?? 0),
            $date
        );

        $block = $tripBlocksMap[$trip->trip_id] ?? null;

        if ($block && $block->block_type === 'off_day') {
            $trip->is_off_day = true;
            $trip->block_note = $block->note;
        } else {
            $trip->is_off_day = false;
            $trip->block_note = null;
        }

        // Calculate duration
        $start = Carbon::parse($trip->start_time);
        $end = Carbon::parse($trip->end_time);
        if ($end->lessThan($start)) {
            $end->addDay();
        }
        $trip->duration_minutes = $start->diffInMinutes($end);

        // Calculate available seats
        $trip->seats_available = $this->resolveAvailableSeats(
            (int) $trip->trip_id,
            (int) $trip->seat_count,
            $block,
            $bookedSeatsMap,
        );
        $trip->base_price = $priceBreakdown['base_unit_price'];
        $trip->global_surcharge = $priceBreakdown['global_surcharge_unit'];
        $trip->route_surcharge = $priceBreakdown['route_surcharge_unit'];
        $trip->surcharge_total = $priceBreakdown['total_surcharge_unit'];
        $trip->effective_price = $priceBreakdown['final_unit_price'];
        $trip->has_surcharge = $priceBreakdown['has_surcharge'];
        $trip->surcharge_reasons = $priceBreakdown['surcharge_reasons'];
        $trip->surcharge_reason_snapshot = $priceBreakdown['surcharge_reason_snapshot'];
        $trip->has_price = $trip->effective_price > 0;

        // Get stops
        $stops = collect($routeStopsMap[$trip->route_id] ?? []);
        $trip->pickup_points = $stops->whereIn('stop_type', ['pickup', 'both'])->values();
        $trip->dropoff_points = $stops->whereIn('stop_type', ['dropoff', 'both'])->values();

        // Parse JSON fields
        $serviceDetails = $busServicesMap[$trip->bus_id] ?? [];
        $trip->service_details = $this->withoutServicePriority($serviceDetails);
        $trip->service_ids = collect($serviceDetails)->pluck('id')->values()->all();
        $trip->services = collect($serviceDetails)->pluck('name')->values()->all();
        $trip->image_gallery = json_decode($trip->bus_images, true) ?? [];
        if ($trip->bus_thumbnail) {
            array_unshift($trip->image_gallery, $trip->bus_thumbnail);
        }
        $trip->primary_bus_image = $trip->image_gallery[0] ?? null;

        return $trip;
    }

    private function getTripBlocksMap(array $tripIds, string $date): array
    {
        if (empty($tripIds)) {
            return [];
        }

        return DB::table('trip_blocks')
            ->whereIn('trip_id', $tripIds)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->get()
            ->keyBy('trip_id')
            ->all();
    }

    private function getBookedSeatsMap(array $tripIds, string $date): array
    {
        if (empty($tripIds)) {
            return [];
        }

        return DB::table('bookings')
            ->select('trip_id', DB::raw('SUM(quantity) as booked'))
            ->whereIn('trip_id', $tripIds)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->groupBy('trip_id')
            ->pluck('booked', 'trip_id')
            ->map(fn ($booked) => (int) $booked)
            ->all();
    }

    private function resolveAvailableSeats(int $tripId, int $totalSeats, ?object $block, array $bookedSeatsMap): int
    {
        if ($block && in_array($block->block_type, ['sold_out', 'off_day'], true)) {
            return 0;
        }

        $bookedSeats = (int) ($bookedSeatsMap[$tripId] ?? 0);

        return max(0, $totalSeats - $bookedSeats);
    }

    private function timeRangeBounds(): array
    {
        return [
            'early_morning' => ['min' => 5, 'max' => 8],
            'morning' => ['min' => 8, 'max' => 12],
            'afternoon' => ['min' => 12, 'max' => 17],
            'evening' => ['min' => 17, 'max' => 21],
            'night' => ['min' => 21, 'max' => 5],
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
            ->select([
                'bbs.bus_id',
                'bs.id',
                'bs.name',
                'bs.icon',
                'bs.priority',
            ])
            ->orderBy('bs.priority', 'desc')
            ->orderBy('bs.name')
            ->get()
            ->groupBy('bus_id')
            ->map(fn ($services) => $services
                ->map(fn ($service) => [
                    'id' => (int) $service->id,
                    'name' => $service->name,
                    'icon' => $service->icon,
                    'priority' => (int) $service->priority,
                ])
                ->values()
                ->all())
            ->all();
    }

    private function withoutServicePriority(array $services): array
    {
        return collect($services)
            ->map(fn ($service) => [
                'id' => $service['id'],
                'name' => $service['name'],
                'icon' => $service['icon'] ?? null,
            ])
            ->values()
            ->all();
    }

    private function normalizeServiceFilterIds(mixed $services): array
    {
        $values = collect(is_array($services) ? $services : [$services])
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->values();

        if ($values->isEmpty()) {
            return [];
        }

        $ids = $values
            ->filter(fn ($value) => is_numeric($value))
            ->map(fn ($value) => (int) $value);

        $names = $values
            ->reject(fn ($value) => is_numeric($value))
            ->map(fn ($value) => (string) $value)
            ->values();

        if ($names->isNotEmpty()) {
            $ids = $ids->merge(
                DB::table('bus_services')
                    ->whereIn('name', $names->all())
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
            );
        }

        return $ids->filter()->unique()->values()->all();
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
        $servicesById = collect();
        $busCategories = collect();
        $prices = collect();
        $pickupPoints = collect();
        $dropoffPoints = collect();

        foreach ($trips as $trip) {
            // Collect services
            $tripServices = is_array($trip->service_details ?? null) ? $trip->service_details : [];
            foreach ($tripServices as $service) {
                if (! empty($service['id'])) {
                    $servicesById->put((int) $service['id'], $service);
                }
            }

            // Collect bus categories/models
            if (! empty($trip->bus_model)) {
                $busCategories->push($trip->bus_model);
            }

            // Collect prices
            $effectivePrice = (int) ($trip->effective_price ?? $trip->price ?? 0);
            if ($trip->has_price && $effectivePrice > 0) {
                $prices->push($effectivePrice);
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

        $timeRanges = collect($this->timeRangeBounds())
            ->mapWithKeys(fn (array $bounds, string $key) => [
                $key => array_merge($bounds, [
                    'label' => __("client.route_show.filters.time_range_{$key}"),
                ]),
            ])
            ->all();

        return [
            'services' => $servicesById
                ->sortBy([
                    ['priority', 'desc'],
                    ['name', 'asc'],
                ])
                ->map(fn ($service) => [
                    'id' => $service['id'],
                    'name' => $service['name'],
                    'icon' => $service['icon'] ?? null,
                ])
                ->values()
                ->toArray(),
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
        $serviceFilterIds = $this->normalizeServiceFilterIds($filters['services'] ?? []);

        return $trips->filter(function ($trip) use ($filters, $serviceFilterIds) {
            $priceToCompare = (int) ($trip->effective_price ?? $trip->price ?? 0);

            // Price filter
            if (! empty($filters['price_min']) && $priceToCompare < (int) $filters['price_min']) {
                return false;
            }
            if (! empty($filters['price_max']) && $priceToCompare > (int) $filters['price_max']) {
                return false;
            }

            // Services filter (trip must have ALL selected services)
            if (! empty($serviceFilterIds)) {
                $tripServiceIds = collect($trip->service_ids ?? [])
                    ->map(fn ($serviceId) => (int) $serviceId)
                    ->values()
                    ->all();

                foreach ($serviceFilterIds as $serviceId) {
                    if (! in_array((int) $serviceId, $tripServiceIds, true)) {
                        return false;
                    }
                }
            }

            // Bus categories filter (trip must match ONE of selected categories)
            if (! empty($filters['bus_categories']) && is_array($filters['bus_categories'])) {
                if (! in_array($trip->bus_model ?? '', $filters['bus_categories'], true)) {
                    return false;
                }
            }

            // Time range filter
            if (! empty($filters['time_ranges']) && is_array($filters['time_ranges'])) {
                $tripHour = (int) Carbon::parse($trip->start_time)->format('H');
                $matchesTimeRange = false;

                $timeRangeDefinitions = $this->timeRangeBounds();

                foreach ($filters['time_ranges'] as $range) {
                    if (! isset($timeRangeDefinitions[$range])) {
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

                if (! $matchesTimeRange) {
                    return false;
                }
            }

            // Seats available filter
            if (! empty($filters['has_seats']) && ($trip->seats_available ?? 0) <= 0) {
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
            'earliest' => $trips->sortBy(fn ($trip) => Carbon::parse($trip->start_time))->values(),
            'latest' => $trips->sortByDesc(fn ($trip) => Carbon::parse($trip->start_time))->values(),
            'price_low' => $trips->sortBy(fn ($trip) => $trip->effective_price ?? $trip->price ?? PHP_INT_MAX)->values(),
            'price_high' => $trips->sortByDesc(fn ($trip) => $trip->effective_price ?? $trip->price ?? 0)->values(),
            'seats_available' => $trips->sortByDesc(fn ($trip) => $trip->seats_available ?? 0)->values(),
            default => $trips->sortByDesc(fn ($trip) => $trip->priority ?? 0)->values(), // recommended
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
