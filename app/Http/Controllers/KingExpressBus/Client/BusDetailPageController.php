<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Request vẫn cần thiết nếu xử lý query param sau này
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BusDetailPageController extends Controller
{
    /**
     * Hiển thị trang chi tiết của một chuyến xe (bus_route).
     *
     * @param string $bus_route_slug Slug của bus_route từ URL.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(string $bus_route_slug)
    {
        try {
            // --- Lấy thông tin Bus Route, Bus, và Route liên quan ---
            $busRouteData = DB::table('bus_routes')
                ->join('buses', 'bus_routes.bus_id', '=', 'buses.id')
                ->join('routes', 'bus_routes.route_id', '=', 'routes.id')
                ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
                ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
                ->where('bus_routes.slug', $bus_route_slug)
                ->select(
                // Bus Route Info
                    'bus_routes.id as bus_route_id',
                    'bus_routes.title as bus_route_title',
                    'bus_routes.slug as bus_route_slug',
                    'bus_routes.start_at',
                    'bus_routes.end_at',
                    'bus_routes.description as bus_route_description',
                    'bus_routes.detail as bus_route_detail', // Chi tiết riêng của lịch trình nếu có
                    // Bus Info
                    'buses.name as bus_name',
                    'buses.type as bus_type',
                    'buses.thumbnail as bus_thumbnail',
                    'buses.images as bus_images', // JSON
                    'buses.services as bus_services', // JSON
                    'buses.number_of_seats as total_seats',
                    'buses.detail as bus_detail', // Chi tiết chung của loại xe
                    'buses.floors as bus_floors',
                    // Route Info
                    'routes.title as route_title',
                    'routes.start_price',
                    'routes.distance',
                    'routes.slug as route_slug',
                    'routes.duration as route_duration_text', // Thời gian dạng text của route
                    'p_start.name as start_province_name',
                    'p_end.name as end_province_name'
                )
                ->first(); // Lấy một bản ghi

            // Nếu không tìm thấy bus_route, trả về 404
            if (!$busRouteData) {
                abort(404, 'Không tìm thấy chi tiết chuyến xe.');
            }

            // --- Xử lý dữ liệu ---
            // Decode JSON images và services
            try {
                $busRouteData->bus_images = json_decode($busRouteData->bus_images, true);
                if (!is_array($busRouteData->bus_images)) $busRouteData->bus_images = [];
                // Thêm thumbnail vào đầu danh sách ảnh nếu chưa có
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

            // Format tên loại xe
            $busRouteData->bus_type_name = match ($busRouteData->bus_type) {
                'sleeper' => 'Giường nằm',
                'cabin' => 'Cabin đơn',
                'doublecabin' => 'Cabin đôi',
                'limousine' => 'Limousine ghế ngồi',
                default => ucfirst($busRouteData->bus_type)
            };

            // Tính toán thời gian di chuyển của chuyến xe cụ thể
            try {
                $start = Carbon::parse($busRouteData->start_at);
                $end = Carbon::parse($busRouteData->end_at);
                if ($end->lt($start)) $end->addDay();
                $busRouteData->duration_formatted = $start->diffForHumans($end, true, false, 2);
            } catch (\Exception $e) {
                $busRouteData->duration_formatted = $busRouteData->route_duration_text; // Fallback về text của route
            }

            // --- Lấy danh sách điểm dừng ---
            $busRouteData->stops = DB::table('stops')
                ->join('districts', 'stops.district_id', '=', 'districts.id')
                ->where('stops.bus_route_id', $busRouteData->bus_route_id)
                ->orderBy('stops.stop_at', 'asc')
                ->select('stops.title as stop_title', 'stops.stop_at', 'districts.name as district_name')
                ->get();

            // Lấy ngày đi từ session (do SearchController lưu)
            // Cần có ngày đi để tạo link đặt vé chính xác
            $departure_date_str = session('departure_date', now()->format('Y-m-d')); // Lấy ngày hôm nay nếu session không có
            try {
                $departure_date = Carbon::parse($departure_date_str);
            } catch (\Exception $e) {
                $departure_date = now(); // Fallback về ngày hiện tại nếu parse lỗi
            }


        } catch (\Exception $e) {
            Log::error('Error fetching bus detail: ' . $e->getMessage(), ['bus_route_slug' => $bus_route_slug]);
            return redirect()->route('homepage')->with('error', 'Không thể tải chi tiết chuyến xe.');
        }

        // Truyền dữ liệu vào view
        return view("kingexpressbus.client.modules.bus_detail.index", compact(
            'busRouteData',     // Dữ liệu chi tiết đã xử lý
            'departure_date'    // Ngày đi (để tạo link đặt vé)
        ));
    }
}
