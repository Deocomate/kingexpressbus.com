<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Sử dụng DB Facade
use Illuminate\Support\Facades\Log;

// Sử dụng Log Facade
use Carbon\Carbon;

// Sử dụng Carbon để xử lý ngày tháng

class BusListPageController extends Controller
{
    public function index(Request $request, string $route_slug)
    {

        $departure_date_str = $request->query('departure_date', session('departure_date'));

        $departure_date = Carbon::parse($departure_date_str)->startOfDay(); // Chuẩn hóa về đầu ngày

        session(['departure_date' => $departure_date->format('Y-m-d')]);

        try {
            // --- Tìm Tuyến đường (Route) ---
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
                ->first(); // Lấy thông tin tuyến đường

            // Nếu không tìm thấy route, trả về 404
            if (!$route) {
                abort(404, 'Không tìm thấy tuyến đường.');
            }

            // --- Lấy Danh sách Chuyến xe (Bus Routes) ---
            $query = DB::table('bus_routes')
                ->join('buses', 'bus_routes.bus_id', '=', 'buses.id') // Join với bảng buses
                ->where('bus_routes.route_id', $route->id) // Lọc theo route_id
                ->select(
                    'bus_routes.id as bus_route_id', // Đổi tên để tránh trùng lặp
                    'bus_routes.title as bus_route_title',
                    'bus_routes.slug as bus_route_slug',
                    'bus_routes.start_at',
                    'bus_routes.end_at',
                    'bus_routes.price',
                    'bus_routes.description as bus_route_description', // Mô tả của lịch trình
                    'buses.id as bus_id',
                    'buses.name as bus_name',
                    'buses.type as bus_type',
                    'buses.thumbnail as bus_thumbnail',
                    'buses.services as bus_services', // Dạng JSON
                    'buses.number_of_seats as total_seats' // Tổng số ghế
                // Thêm các cột cần thiết khác
                );

            // --- Xử lý Filtering (Ví dụ: theo giờ khởi hành) ---
            $filter_time_start = $request->query('filter_time_start'); // vd: 06:00
            $filter_time_end = $request->query('filter_time_end');     // vd: 12:00
            if ($filter_time_start && preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $filter_time_start)) {
                $query->whereTime('bus_routes.start_at', '>=', $filter_time_start);
            }
            if ($filter_time_end && preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $filter_time_end)) {
                $query->whereTime('bus_routes.start_at', '<=', $filter_time_end);
            }
            // Thêm filter theo loại xe (bus_type) nếu cần
            $filter_bus_type = $request->query('filter_bus_type');
            if ($filter_bus_type && in_array($filter_bus_type, ['sleeper', 'cabin', 'doublecabin', 'limousine'])) {
                $query->where('buses.type', $filter_bus_type);
            }


            // --- Xử lý Sorting (Ví dụ: theo giờ đi hoặc giá) ---
            $sort_by = $request->query('sort_by', 'time_asc'); // Mặc định sắp xếp theo giờ đi tăng dần
            switch ($sort_by) {
                case 'time_asc':
                    $query->orderBy('bus_routes.start_at', 'asc');
                    break;
                case 'time_desc':
                    $query->orderBy('bus_routes.start_at', 'desc');
                    break;
                case 'price_asc':
                    $query->orderBy('bus_routes.price', 'asc'); // <<<< Sắp xếp theo giá của bus_routes
                    $query->orderBy('bus_routes.start_at', 'asc'); // Thứ tự phụ
                    break;
                case 'price_desc':
                    $query->orderBy('bus_routes.price', 'desc'); // <<<< Sắp xếp theo giá của bus_routes
                    $query->orderBy('bus_routes.start_at', 'asc');
                    break;
                default:
                    $query->orderBy('bus_routes.start_at', 'asc');
            }
            // Thêm sắp xếp phụ theo priority của bus_route nếu cần
            $query->orderBy('bus_routes.priority', 'asc');


            // Lấy kết quả danh sách chuyến xe (có thể phân trang nếu cần)
            $busRoutes = $query->get()->map(function ($item) {
                // Decode JSON services
                try {
                    $item->bus_services = json_decode($item->bus_services, true);
                    if (!is_array($item->bus_services)) $item->bus_services = [];
                } catch (\Exception $e) {
                    $item->bus_services = [];
                }
                // Format lại tên loại xe
                $item->bus_type_name = match ($item->bus_type) {
                    'sleeper' => 'Giường nằm',
                    'cabin' => 'Cabin đơn',
                    'doublecabin' => 'Cabin đôi',
                    'limousine' => 'Limousine ghế ngồi',
                    default => ucfirst($item->bus_type)
                };
                // Tính toán thời gian di chuyển (ví dụ)
                try {
                    $start = Carbon::parse($item->start_at);
                    $end = Carbon::parse($item->end_at);
                    // Xử lý trường hợp qua ngày: nếu end < start thì end là ngày hôm sau
                    if ($end->lt($start)) {
                        $end->addDay();
                    }
                    $item->duration_formatted = $start->diffForHumans($end, true, false, 2); // vd: "5 hours 30 minutes"
                } catch (\Exception $e) {
                    $item->duration_formatted = null;
                }

                // Lấy các điểm dừng (stops) cho chuyến xe này
                $item->stops = DB::table('stops')
                    ->join('districts', 'stops.district_id', '=', 'districts.id')
                    ->where('stops.bus_route_id', $item->bus_route_id)
                    ->orderBy('stops.stop_at', 'asc')
                    ->select('stops.title as stop_title', 'stops.stop_at', 'districts.name as district_name', 'districts.type as district_type')
                    ->get();

                return $item;
            });

            // --- Lấy các tùy chọn filter (ví dụ: các loại xe có trong kết quả) ---
            $availableBusTypes = $busRoutes->pluck('bus_type', 'bus_type_name') // Lấy các loại xe unique
            ->unique()
                ->sort(); // Sắp xếp tên loại xe


        } catch (\Exception $e) {
            Log::error('Error fetching bus list: ' . $e->getMessage(), ['route_slug' => $route_slug]);
            // Có thể trả về view lỗi hoặc quay lại trang trước với lỗi
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi tải danh sách chuyến xe.');
        }

//        dd($busRoutes);

        // Truyền dữ liệu vào view
        return view("kingexpressbus.client.modules.bus_list.index", compact(
            'route',                // Thông tin tuyến đường đang xem
            'busRoutes',            // Danh sách các chuyến xe phù hợp
            'departure_date',       // Ngày đi đã chọn (đối tượng Carbon)
            'availableBusTypes',    // Các loại xe có sẵn để filter
            'sort_by',              // Tiêu chí sort hiện tại
            'filter_time_start',    // Giờ lọc hiện tại
            'filter_time_end',
            'filter_bus_type',
            'request'
        ));
    }
}
