<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTripRequest;
use App\Http\Requests\Admin\UpdateTripRequest;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Trip;
use App\Models\TripBlock;
use App\Support\Admin\AdminResponse;
use App\Support\Admin\DeleteBlockedException;
use App\Support\Admin\DeleteGuard;
use App\Support\Admin\Tables\TripBlockTableConfig;
use App\Support\Admin\Tables\TripTableConfig;
use App\Support\Admin\TableQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TripController extends Controller
{
    public function index(Request $request): View
    {
        $section = $request->input('section', 'trips');
        $routes = Route::orderBy('name')->get();
        $buses  = Bus::orderBy('name')->get();

        if ($section === 'blocks') {
            return $this->indexBlocks($request, $routes, $buses);
        }

        return $this->indexTrips($request, $routes, $buses);
    }

    public function create(): View
    {
        $routes = Route::orderBy('name')->get();
        $buses  = Bus::orderBy('name')->get();

        return view('admin.trips._form', [
            'trip'   => null,
            'routes' => $routes,
            'buses'  => $buses,
        ]);
    }

    public function store(StoreTripRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['price']     = (int) preg_replace('/\D/', '', (string) $data['price']);

        Trip::create($data);

        return redirect()->route('admin.trips.index')
            ->with('success', 'Đã thêm chuyến xe.');
    }

    public function edit(Trip $trip): View
    {
        $routes = Route::orderBy('name')->get();
        $buses  = Bus::orderBy('name')->get();

        return view('admin.trips._form', [
            'trip'   => $trip->load(['route', 'bus']),
            'routes' => $routes,
            'buses'  => $buses,
        ]);
    }

    public function update(UpdateTripRequest $request, Trip $trip): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['price']     = (int) preg_replace('/\D/', '', (string) $data['price']);

        $trip->update($data);

        return redirect()->route('admin.trips.index')
            ->with('success', 'Đã cập nhật chuyến xe.');
    }

    public function destroy(Trip $trip): RedirectResponse
    {
        try {
            DeleteGuard::assertNoBookings(
                DeleteGuard::tripBookingCount($trip->id),
                "chuyến {$trip->id}"
            );
        } catch (DeleteBlockedException $e) {
            return back()->with('error', $e->getMessage());
        }

        $trip->delete();

        return redirect()->route('admin.trips.index')
            ->with('success', 'Đã xóa chuyến xe.');
    }

    public function bulkDestroy(Request $request): RedirectResponse|JsonResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));

        if (empty($ids)) {
            return AdminResponse::bulkResult($request, false, 'Không có chuyến xe nào được chọn.');
        }

        try {
            $total = collect($ids)->sum(fn ($id) => DeleteGuard::tripBookingCount($id));
            DeleteGuard::assertNoBookings($total);
        } catch (DeleteBlockedException $e) {
            return AdminResponse::bulkResult($request, false, $e->getMessage());
        }

        Trip::whereIn('id', $ids)->delete();

        return AdminResponse::bulkResult($request, true, 'Đã xóa ' . count($ids) . ' chuyến xe.');
    }

    public function toggleActive(Request $request, Trip $trip): JsonResponse
    {
        $trip->update(['is_active' => ! $trip->is_active]);

        return response()->json(['is_active' => $trip->is_active]);
    }

    /** JSON endpoint for TripBlock dependent select: trips filtered by route_id. */
    public function tripsForRoute(Request $request): JsonResponse
    {
        $routeId = (int) $request->input('route_id', 0);

        if ($routeId <= 0) {
            return response()->json(['results' => []]);
        }

        $trips = Trip::with('bus')
            ->where('route_id', $routeId)
            ->orderByDesc('priority')
            ->orderBy('start_time')
            ->get()
            ->map(function (Trip $trip) {
                $time     = substr((string) $trip->start_time, 0, 5);
                $busName  = $trip->bus?->name ?? 'N/A';
                $busModel = $trip->bus?->model_name ?? 'N/A';

                return ['id' => $trip->id, 'text' => "{$time} - {$busName} ({$busModel})"];
            });

        return response()->json(['results' => $trips]);
    }

    // -------------------------------------------------------------------------

    private function indexTrips(Request $request, $routes, $buses): View
    {
        $baseQuery = Trip::query()
            ->join('routes', 'trips.route_id', '=', 'routes.id')
            ->join('buses', 'trips.bus_id', '=', 'buses.id')
            ->select('trips.*', 'routes.name as route_name', 'buses.name as bus_name')
            ->orderByDesc('trips.priority');

        // Apply filters before TableQuery tab processing
        if ($routeId = (int) $request->input('filter.route_id', 0)) {
            $baseQuery->where('trips.route_id', $routeId);
        }
        if ($busId = (int) $request->input('filter.bus_id', 0)) {
            $baseQuery->where('trips.bus_id', $busId);
        }

        $config = TripTableConfig::make();
        $tq     = TableQuery::make($baseQuery, $config)->process($request);

        // Group current page items by route name for display
        $grouped = collect($tq->paginator()->items())
            ->groupBy(fn ($t) => $t->route_name ?? '—');

        if ($request->header('X-Partial') === 'table') {
            return view('admin.trips._table-rows', [
                'grouped'   => $grouped,
                'paginator' => $tq->paginator(),
            ]);
        }

        return view('admin.trips.index', [
            'section'       => 'trips',
            'paginator'     => $tq->paginator(),
            'grouped'       => $grouped,
            'activeTab'     => $tq->activeTab(),
            'tabBadges'     => $tq->tabBadges(),
            'tabs'          => $config->getTabs(),
            'routes'        => $routes,
            'buses'         => $buses,
            'filterRouteId' => $request->input('filter.route_id'),
            'filterBusId'   => $request->input('filter.bus_id'),
            'search'        => $tq->activeSearch(),
        ]);
    }

    private function indexBlocks(Request $request, $routes, $buses): View
    {
        $baseQuery = TripBlock::query()
            ->join('trips', 'trip_blocks.trip_id', '=', 'trips.id')
            ->join('routes', 'trips.route_id', '=', 'routes.id')
            ->join('buses', 'trips.bus_id', '=', 'buses.id')
            ->select(
                'trip_blocks.*',
                'routes.name as route_name',
                'routes.id as route_id_val',
                'buses.name as bus_name',
                'buses.model_name as bus_model',
                'trips.start_time as trip_start_time',
            )
            ->orderByDesc('trip_blocks.start_date');

        // Apply route filter
        if ($routeId = (int) $request->input('filter.route_id', 0)) {
            $baseQuery->where('trips.route_id', $routeId);
        }

        // Apply date range filter
        if ($dateFrom = $request->input('filter.date_from')) {
            try {
                $df = \Carbon\Carbon::createFromFormat('d/m/Y', $dateFrom);
                $baseQuery->where('trip_blocks.end_date', '>=', $df->format('Y-m-d'));
            } catch (\Exception) {}
        }
        if ($dateTo = $request->input('filter.date_to')) {
            try {
                $dt = \Carbon\Carbon::createFromFormat('d/m/Y', $dateTo);
                $baseQuery->where('trip_blocks.start_date', '<=', $dt->format('Y-m-d'));
            } catch (\Exception) {}
        }

        $config = TripBlockTableConfig::make();
        $tq     = TableQuery::make($baseQuery, $config)->process($request);

        if ($request->header('X-Partial') === 'table') {
            return view('admin.trips._table-blocks-rows', [
                'paginator' => $tq->paginator(),
            ]);
        }

        return view('admin.trips.index', [
            'section'       => 'blocks',
            'paginator'     => $tq->paginator(),
            'grouped'       => null,
            'activeTab'     => '',
            'tabBadges'     => [],
            'tabs'          => [],
            'routes'        => $routes,
            'buses'         => $buses,
            'filterRouteId' => $request->input('filter.route_id'),
            'filterBusId'   => null,
            'search'        => $tq->activeSearch(),
        ]);
    }
}
