<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\RouteService;
use App\Services\TripService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Handles trip search and display for clients.
 * Renamed from RouteController to better reflect single-tenant architecture.
 */
class TripController extends Controller
{
    protected TripService $tripService;
    protected RouteService $routeService;

    public function __construct(TripService $tripService, RouteService $routeService)
    {
        $this->tripService = $tripService;
        $this->routeService = $routeService;
    }

    /**
     * Resolve location name based on type and id.
     */
    private function resolveLocationName(string $type, int $id): ?string
    {
        return match ($type) {
            'province' => DB::table('provinces')->where('id', $id)->value('name'),
            'district' => DB::table('districts')->where('id', $id)->value('name'),
            'stop' => DB::table('stops')->where('id', $id)->value('name'),
            default => null,
        };
    }

    /**
     * Resolve location context (parent location info) based on type and id.
     */
    private function resolveLocationContext(string $type, int $id): ?string
    {
        return match ($type) {
            'province' => __('client.search.types.province'),
            'district' => DB::table('districts as d')
                ->join('provinces as p', 'd.province_id', '=', 'p.id')
                ->where('d.id', $id)
                ->value('p.name'),
            'stop' => DB::table('stops as s')
                ->join('districts as d', 's.district_id', '=', 'd.id')
                ->join('provinces as p', 'd.province_id', '=', 'p.id')
                ->where('s.id', $id)
                ->selectRaw("CONCAT(d.name, ' · ', p.name) as context")
                ->value('context'),
            default => null,
        };
    }

    /**
     * Display routes/trips search page.
     */
    public function index(Request $request)
    {
        $searchDefaults = [
            'origin' => null,
            'destination' => null,
            'departure_date' => Carbon::today()->format('d/m/Y'),
        ];

        // Get selected province filter from request
        $selectedProvince = $request->input('province');

        // Get popular routes (optionally filtered by province)
        $popularRoutes = $this->routeService->getPopularRoutes(8, $selectedProvince);

        // Get provinces that have active routes for filter tabs
        $provinces = $this->routeService->getProvincesWithRoutes();

        // Get quick route suggestions (top 6 most popular routes)
        $quickRouteSuggestions = $this->routeService->getPopularRoutes(6);

        return view('client.routes.index', [
            'searchData' => ['defaults' => $searchDefaults],
            'popularRoutes' => $popularRoutes,
            'provinces' => $provinces,
            'selectedProvince' => $selectedProvince,
            'quickRouteSuggestions' => $quickRouteSuggestions,
            'title' => __('client.routes.index.meta_title', ['default' => 'Tìm kiếm tuyến xe']),
            'description' => __('client.routes.index.meta_description', ['default' => 'Đặt vé xe khách trực tuyến dễ dàng, tiện lợi.']),
        ]);
    }

    /**
     * Travel tips for the route page.
     */
    private function travelTips(): array
    {
        return [
            [
                'icon' => 'fa-solid fa-clock',
                'title' => __('client.route_show.tips.tip_1_title'),
                'content' => __('client.route_show.tips.tip_1_content'),
            ],
            [
                'icon' => 'fa-solid fa-suitcase-rolling',
                'title' => __('client.route_show.tips.tip_2_title'),
                'content' => __('client.route_show.tips.tip_2_content'),
            ],
            [
                'icon' => 'fa-solid fa-mug-hot',
                'title' => __('client.route_show.tips.tip_3_title'),
                'content' => __('client.route_show.tips.tip_3_content'),
            ],
        ];
    }

    /**
     * FAQ for the route page.
     */
    private function frequentlyAskedQuestions(): array
    {
        return [
            [
                'question' => __('client.route_show.faq.faq_1_question'),
                'answer' => __('client.route_show.faq.faq_1_answer'),
            ],
            [
                'question' => __('client.route_show.faq.faq_2_question'),
                'answer' => __('client.route_show.faq.faq_2_answer'),
            ],
            [
                'question' => __('client.route_show.faq.faq_3_question'),
                'answer' => __('client.route_show.faq.faq_3_answer'),
            ],
        ];
    }

    /**
     * Show trips for a specific route.
     */
    public function show($slug, Request $request)
    {
        // 1. Get Route info
        $route = $this->routeService->getRouteBySlug($slug);

        if (!$route) {
            abort(404);
        }

        // 2. Get Search Params
        $departureDate = $request->input('departure_date', Carbon::today()->format('d/m/Y'));
        $returnDate = $request->input('return_date');
        try {
            $parsedDate = Carbon::createFromFormat('d/m/Y', $departureDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $parsedDate = Carbon::today()->format('Y-m-d');
        }

        $parsedReturnDate = null;
        if (!empty($returnDate)) {
            try {
                $parsedReturnDate = Carbon::createFromFormat('d/m/Y', $returnDate)->format('Y-m-d');
            } catch (\Exception $e) {
                $returnDate = null;
            }
        }

        // 3. Get Filters State from request
        $filterState = [
            'sort' => $request->input('sort', 'recommended'),
            'price_min' => $request->input('price_min'),
            'price_max' => $request->input('price_max'),
            'services' => $request->input('services', []),
            'pickup_points' => $request->input('pickup_points', []),
            'dropoff_points' => $request->input('dropoff_points', []),
            'bus_categories' => $request->input('bus_categories', []),
            'time_ranges' => $request->input('time_ranges', []),
            'has_seats' => $request->boolean('has_seats'),
        ];

        // 4. Get filtered trips with filter options and stats
        $result = $this->tripService->getFilteredTripsByRoute(
            $route->id,
            $parsedDate,
            $filterState,
            $filterState['sort']
        );

        $trips = $result['trips'];
        $filters = $result['filters'];
        $tripStats = $result['stats'];

        // Calculate active filter count for UI
        $activeFilterCount = 0;
        if (!empty($filterState['price_min']))
            $activeFilterCount++;
        if (!empty($filterState['price_max']))
            $activeFilterCount++;
        if (!empty($filterState['services']))
            $activeFilterCount += count($filterState['services']);
        if (!empty($filterState['bus_categories']))
            $activeFilterCount += count($filterState['bus_categories']);
        if (!empty($filterState['time_ranges']))
            $activeFilterCount += count($filterState['time_ranges']);
        if ($filterState['has_seats'])
            $activeFilterCount++;

        // 5. Prepare Search Data for View
        $searchDefaults = [
            'origin' => null,
            'destination' => null,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
        ];

        $returnRoute = null;
        $returnTrips = collect();
        if ($parsedReturnDate) {
            $returnRoute = $this->routeService->findRouteByProvinces(
                (int) $route->province_end_id,
                (int) $route->province_start_id
            );

            if ($returnRoute) {
                $returnTrips = $this->tripService->getTripsByRoute((int) $returnRoute->id, $parsedReturnDate);
            }
        }

        if ($request->has('origin_id')) {
            $originType = $request->input('origin_type', 'province');
            $originId = (int) $request->input('origin_id');
            $originName = $this->resolveLocationName($originType, $originId) ?? $route->start_province_name;
            $originContext = $this->resolveLocationContext($originType, $originId);

            $searchDefaults['origin'] = [
                'id' => $originId,
                'type' => $originType,
                'name' => $originName,
                'context' => $originContext,
            ];
        } else {
            $searchDefaults['origin'] = [
                'id' => $route->province_start_id,
                'type' => 'province',
                'name' => $route->start_province_name,
                'context' => __('client.search.types.province'),
            ];
        }

        if ($request->has('destination_id')) {
            $destType = $request->input('destination_type', 'province');
            $destId = (int) $request->input('destination_id');
            $destName = $this->resolveLocationName($destType, $destId) ?? $route->end_province_name;
            $destContext = $this->resolveLocationContext($destType, $destId);

            $searchDefaults['destination'] = [
                'id' => $destId,
                'type' => $destType,
                'name' => $destName,
                'context' => $destContext,
            ];
        } else {
            $searchDefaults['destination'] = [
                'id' => $route->province_end_id,
                'type' => 'province',
                'name' => $route->end_province_name,
                'context' => __('client.search.types.province'),
            ];
        }

        // 6. Return View
        return view('client.routes.show', [
            'route' => $route,
            'trips' => $trips,
            'searchData' => ['defaults' => $searchDefaults],
            'filters' => $filters,
            'filterState' => $filterState,
            'tripStats' => $tripStats,
            'activeFilterCount' => $activeFilterCount,
            'hasActiveFilters' => $activeFilterCount > 0,
            'departureDate' => $departureDate,
            'returnDate' => $returnDate,
            'returnRoute' => $returnRoute,
            'returnTrips' => $returnTrips,
            'travelTips' => $this->travelTips(),
            'frequentlyAskedQuestions' => $this->frequentlyAskedQuestions(),
            'title' => $route->title ?? $route->name,
            'description' => $route->description,
        ]);
    }

    /**
     * Search trips (API endpoint for AJAX search).
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'origin_province_id' => 'required|integer',
            'destination_province_id' => 'required|integer',
            'date' => 'required|date_format:Y-m-d',
        ]);

        $trips = $this->tripService->searchTrips(
            $validated['origin_province_id'],
            $validated['destination_province_id'],
            $validated['date']
        );

        return response()->json([
            'success' => true,
            'data' => $trips,
            'count' => $trips->count(),
        ]);
    }
}
