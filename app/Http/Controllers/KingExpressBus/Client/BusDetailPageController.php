<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Added Request
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BusDetailPageController extends Controller
{
    /**
     * Hiển thị trang chi tiết của một chuyến xe (bus_route).
     *
     * @param Request $request
     * @param string $bus_route_slug Slug của bus_route từ URL.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request, string $bus_route_slug)
    {
        try {
            // Lấy ngày đi từ query param hoặc session, ưu tiên query param
            // Mặc định là ngày hiện tại nếu không có
            $departure_date_str = $request->query('departure_date', session('departure_date', now()->format('Y-m-d')));
            try {
                $departure_date = Carbon::parse($departure_date_str)->startOfDay();
            } catch (\Exception $e) {
                Log::error('BusDetailPage: Invalid departure date format.', ['date_str' => $departure_date_str, 'error' => $e->getMessage()]);
                $departure_date = now()->startOfDay(); // Fallback về ngày hiện tại
            }
            // Lưu lại session để tiện cho việc chuyển qua trang đặt vé (nếu user click từ đây)
            session(['departure_date' => $departure_date->format('Y-m-d')]);


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
                    'bus_routes.price',
                    'bus_routes.description as bus_route_description',
                    'bus_routes.detail as bus_route_detail',
                    // Bus Info
                    'buses.name as bus_name',
                    'buses.type as bus_type',
                    'buses.thumbnail as bus_thumbnail',
                    'buses.images as bus_images', // JSON
                    'buses.services as bus_services', // JSON
                    'buses.number_of_seats as total_seats',
                    'buses.seat_row_number', // Needed for seat map
                    'buses.seat_column_number', // Needed for seat map
                    'buses.floors', // Needed for seat map
                    'buses.detail as bus_detail',
                    // Route Info
                    'routes.title as route_title',
                    'routes.slug as route_slug', // Lấy route_slug để tạo link quay lại danh sách xe
                    'routes.distance',
                    'routes.duration as route_duration_text',
                    'p_start.name as start_province_name',
                    'p_end.name as end_province_name'
                )
                ->first();

            if (!$busRouteData) {
                abort(404, 'Không tìm thấy chi tiết chuyến xe.');
            }

            // --- Lấy danh sách ghế đã đặt cho ngày này ---
            $bookedSeatsData = DB::table('bookings')
                ->where('bus_route_id', $busRouteData->bus_route_id)
                ->whereDate('booking_date', $departure_date->format('Y-m-d'))
                ->whereNotIn('status', ['cancelled']) // Chỉ lấy vé không bị hủy
                ->pluck('seats'); // Lấy cột 'seats' chứa JSON

            // Xử lý dữ liệu ghế đã đặt:
            // - Nếu `seats` là JSON của mảng seat IDs (cũ): `["A1", "B2"]`
            // - Nếu `seats` là JSON của object chứa quantity (mới): `{"quantity": 2, "pickup_info": "..."}`
            // Đối với trang chi tiết xe, chúng ta chỉ quan tâm đến việc hiển thị ghế nào đã bị chiếm.
            // Nếu cấu trúc mới không lưu danh sách ghế cụ thể, chúng ta không thể hiển thị chính xác ghế nào đã đặt.
            // Giả định: Nếu là cấu trúc mới, chúng ta không có thông tin ghế cụ thể để hiển thị là "booked" trên sơ đồ.
            // Sơ đồ xe lúc này chỉ mang tính trực quan về cách bố trí.
            // Nếu bạn vẫn muốn logic "ghế nào đã bị đặt" cho cấu trúc mới, bạn cần thay đổi cách lưu `seats` trong `BookingPageController@booking`
            // để nó bao gồm cả danh sách ghế cụ thể (nếu có) bên cạnh số lượng.
            // Hiện tại, với yêu cầu chỉ hiển thị sơ đồ xe trực quan, chúng ta có thể bỏ qua việc đánh dấu ghế "booked" nếu dữ liệu mới không cung cấp.
            // Tuy nhiên, để tương thích ngược và nếu bạn quyết định lưu cả ghế cụ thể, logic dưới đây sẽ hữu ích.

            $bookedSeatsArray = $bookedSeatsData->flatMap(function ($jsonSeats) {
                $decoded = json_decode($jsonSeats, true);
                // Kiểm tra xem có phải là cấu trúc cũ (mảng các seat ID) không
                if (is_array($decoded) && Arr::isList($decoded)) { // Arr::isList kiểm tra xem có phải mảng tuần tự không
                    return $decoded; // Trả về mảng seat ID
                }
                // Nếu là cấu trúc mới dạng {"quantity": N, ...} hoặc cấu trúc không xác định,
                // thì không có danh sách ghế cụ thể để trả về.
                return [];
            })->unique()->values()->toArray();


            // --- Xử lý dữ liệu khác ---
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

            $busRouteData->stops = DB::table('stops')
                ->join('districts', 'stops.district_id', '=', 'districts.id')
                ->where('stops.bus_route_id', $busRouteData->bus_route_id)
                ->orderBy('stops.stop_at', 'asc')
                ->select('stops.title as stop_title', 'stops.stop_at', 'districts.name as district_name', 'districts.type as district_type')
                ->get();

        } catch (\Exception $e) {
            Log::error('Error fetching bus detail: ' . $e->getMessage(), ['bus_route_slug' => $bus_route_slug]);
            return redirect()->route('homepage')->with('error', 'Không thể tải chi tiết chuyến xe.');
        }

        return view("kingexpressbus.client.modules.bus_detail.index", compact(
            'busRouteData',
            'departure_date', // Cần cho link đặt vé và hiển thị ngày xem sơ đồ
            'bookedSeatsArray' // Truyền danh sách ghế đã đặt (có thể rỗng nếu cấu trúc booking mới không lưu ghế cụ thể)
        ));
    }
}
