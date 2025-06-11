<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BusListPageController extends Controller
{
    public function index(Request $request, string $route_slug)
    {
        $departure_date_str = $request->query('departure_date', session('departure_date'));
        $departure_date = Carbon::parse($departure_date_str)->startOfDay();

        session(['departure_date' => $departure_date->format('Y-m-d')]);

        try {
            $route = DB::table('routes')
                ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
                ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
                ->where('routes.slug', $route_slug)
                ->select(
                    'routes.id',
                    'routes.title',
                    'routes.slug',
                    'p_start.name as start_province_name',
                    'p_end.name as end_province_name'
                )
                ->first();

            if (!$route) {
                abort(404, 'Không tìm thấy tuyến đường.');
            }

            // Fetch stops for the entire route once
            $stops = DB::table('stops')
                ->join('districts', 'stops.district_id', '=', 'districts.id')
                ->where('stops.route_id', $route->id) // CHANGED: Fetch by route_id
                ->orderBy('stops.stop_at', 'asc')
                ->select('stops.title as stop_title', 'stops.stop_at', 'districts.name as district_name', 'districts.type as district_type')
                ->get();

            $query = DB::table('bus_routes')
                ->join('buses', 'bus_routes.bus_id', '=', 'buses.id')
                ->where('bus_routes.route_id', $route->id)
                ->select(
                    'bus_routes.id as bus_route_id',
                    'bus_routes.title as bus_route_title',
                    'bus_routes.slug as bus_route_slug',
                    'bus_routes.start_at',
                    'bus_routes.end_at',
                    'bus_routes.price',
                    'bus_routes.description as bus_route_description',
                    'buses.id as bus_id',
                    'buses.name as bus_name',
                    'buses.type as bus_type',
                    'buses.thumbnail as bus_thumbnail',
                    'buses.services as bus_services',
                    'buses.number_of_seats as total_seats'
                );

            // Filtering logic
            $filter_time_start = $request->query('filter_time_start');
            if ($filter_time_start && preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $filter_time_start)) {
                $query->whereTime('bus_routes.start_at', '>=', $filter_time_start);
            }
            $filter_time_end = $request->query('filter_time_end');
            if ($filter_time_end && preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $filter_time_end)) {
                $query->whereTime('bus_routes.start_at', '<=', $filter_time_end);
            }
            $filter_bus_type = $request->query('filter_bus_type');
            if ($filter_bus_type && in_array($filter_bus_type, ['sleeper', 'cabin', 'doublecabin', 'limousine'])) {
                $query->where('buses.type', $filter_bus_type);
            }

            // Sorting logic
            $sort_by = $request->query('sort_by', 'time_asc');
            switch ($sort_by) {
                case 'time_desc':
                    $query->orderBy('bus_routes.start_at', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('bus_routes.price', 'asc')->orderBy('bus_routes.start_at', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('bus_routes.price', 'desc')->orderBy('bus_routes.start_at', 'asc');
                    break;
                default:
                    $query->orderBy('bus_routes.start_at', 'asc');
            }
            $query->orderBy('bus_routes.priority', 'asc');

            $busRoutes = $query->get()->map(function ($item) use ($stops) { // Pass stops collection
                try {
                    $item->bus_services = json_decode($item->bus_services, true);
                    if (!is_array($item->bus_services)) $item->bus_services = [];
                } catch (\Exception $e) {
                    $item->bus_services = [];
                }

                $item->bus_type_name = match ($item->bus_type) {
                    'sleeper' => 'Giường nằm',
                    'cabin' => 'Cabin đơn',
                    'doublecabin' => 'Cabin đôi',
                    'limousine' => 'Limousine ghế ngồi',
                    default => ucfirst($item->bus_type)
                };

                try {
                    $start = Carbon::parse($item->start_at);
                    $end = Carbon::parse($item->end_at);
                    if ($end->lt($start)) {
                        $end->addDay();
                    }
                    $item->duration_formatted = $start->diffForHumans($end, true, false, 2);
                } catch (\Exception $e) {
                    $item->duration_formatted = null;
                }

                $item->stops = $stops; // Assign the pre-fetched stops

                return $item;
            });

            $availableBusTypes = $busRoutes->pluck('bus_type', 'bus_type_name')
                ->unique()
                ->sort();

        } catch (\Exception $e) {
            Log::error('Error fetching bus list: ' . $e->getMessage(), ['route_slug' => $route_slug]);
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi tải danh sách chuyến xe.');
        }

        return view("kingexpressbus.client.modules.bus_list.index", compact(
            'route',
            'busRoutes',
            'departure_date',
            'availableBusTypes',
            'sort_by',
            'filter_time_start',
            'filter_time_end',
            'filter_bus_type',
            'request'
        ));
    }
}
