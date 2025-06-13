<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BusDetailPageController extends Controller
{
    public function index(Request $request, string $bus_route_slug)
    {
        try {
            $departure_date_str = $request->query('departure_date', session('departure_date', now()->format('Y-m-d')));
            try {
                $departure_date = Carbon::parse($departure_date_str)->startOfDay();
            } catch (\Exception $e) {
                Log::error('BusDetailPage: Invalid departure date format.', ['date_str' => $departure_date_str, 'error' => $e->getMessage()]);
                $departure_date = now()->startOfDay();
            }
            session(['departure_date' => $departure_date->format('Y-m-d')]);

            $busRouteData = DB::table('bus_routes')
                ->join('buses', 'bus_routes.bus_id', '=', 'buses.id')
                ->join('routes', 'bus_routes.route_id', '=', 'routes.id')
                ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
                ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
                ->where('bus_routes.slug', $bus_route_slug)
                ->select(
                    'bus_routes.id as bus_route_id',
                    'bus_routes.route_id', // Ensure route_id is selected
                    'bus_routes.title as bus_route_title',
                    'bus_routes.slug as bus_route_slug',
                    'bus_routes.start_at',
                    'bus_routes.end_at',
                    'bus_routes.price',
                    'bus_routes.description as bus_route_description',
                    'bus_routes.detail as bus_route_detail',
                    'buses.name as bus_name',
                    'buses.type as bus_type',
                    'buses.thumbnail as bus_thumbnail',
                    'buses.images as bus_images',
                    'buses.services as bus_services',
                    'buses.number_of_seats as total_seats',
                    'buses.seat_row_number',
                    'buses.seat_column_number',
                    'buses.floors',
                    'buses.detail as bus_detail',
                    'routes.title as route_title',
                    'routes.slug as route_slug',
                    'routes.distance',
                    'routes.duration as route_duration_text',
                    'p_start.name as start_province_name',
                    'p_end.name as end_province_name'
                )
                ->first();

            if (!$busRouteData) {
                abort(404, 'Không tìm thấy chi tiết chuyến xe.');
            }

            $bookedSeatsData = DB::table('bookings')
                ->where('bus_route_id', $busRouteData->bus_route_id)
                ->whereDate('booking_date', $departure_date->format('Y-m-d'))
                ->whereNotIn('status', ['cancelled'])
                ->pluck('seats');

            $bookedSeatsArray = $bookedSeatsData->flatMap(function ($jsonSeats) {
                $decoded = json_decode($jsonSeats, true);
                if (is_array($decoded) && Arr::isList($decoded)) {
                    return $decoded;
                }
                return [];
            })->unique()->values()->toArray();

            try {
                $busRouteData->bus_images = json_decode($busRouteData->bus_images, true);
                if (!is_array($busRouteData->bus_images)) $busRouteData->bus_images = [];
                if ($busRouteData->bus_thumbnail && !in_array($busRouteData->bus_thumbnail, $busRouteData->bus_images)) {
                    array_unshift($busRouteData->bus_images, $busRouteData->bus_thumbnail);
                } elseif (empty($busRouteData->bus_images) && $busRouteData->bus_thumbnail) {
                    $busRouteData->bus_images = [$busRouteData->bus_thumbnail];
                }
            } catch (\Exception $e) {
                $busRouteData->bus_images = $busRouteData->bus_thumbnail ? [$busRouteData->bus_thumbnail] : [];
            }

            try {
                $busRouteData->bus_services = json_decode($busRouteData->bus_services, true);
                if (!is_array($busRouteData->bus_services)) $busRouteData->bus_services = [];
            } catch (\Exception $e) {
                $busRouteData->bus_services = [];
            }

            $busRouteData->bus_type_name = match ($busRouteData->bus_type) {
                'sleeper' => 'Giường nằm',
                'cabin' => 'Cabin đơn',
                'doublecabin' => 'Cabin đôi',
                'limousine' => 'Limousine ghế ngồi',
                default => ucfirst($busRouteData->bus_type)
            };

            try {
                $start = Carbon::parse($busRouteData->start_at);
                $end = Carbon::parse($busRouteData->end_at);
                if ($end->lt($start)) $end->addDay();
                $busRouteData->duration_formatted = $start->diffForHumans($end, true, false, 2);
            } catch (\Exception $e) {
                $busRouteData->duration_formatted = $busRouteData->route_duration_text;
            }

            // CHANGED: Fetch stops based on route_id
            $busRouteData->stops = DB::table('stops')
                ->join('districts', 'stops.district_id', '=', 'districts.id')
                ->where('stops.route_id', $busRouteData->route_id)
                ->select('stops.title as stop_title', 'districts.name as district_name', 'districts.type as district_type')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching bus detail: ' . $e->getMessage(), ['bus_route_slug' => $bus_route_slug]);
            return redirect()->route('homepage')->with('error', 'Không thể tải chi tiết chuyến xe.');
        }

        return view("kingexpressbus.client.modules.bus_detail.index", compact(
            'busRouteData',
            'departure_date',
            'bookedSeatsArray'
        ));
    }
}
