<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Thêm DB Facade
use Illuminate\Support\Facades\Log;

// Thêm Log Facade để ghi lỗi nếu cần

class HomePageController extends Controller
{
    /**
     * Hiển thị trang chủ.
     * Lấy dữ liệu từ database để hiển thị các mục như:
     * - Tuyến đường phổ biến
     * - Các loại xe nổi bật
     * - (Tùy chọn) Banner quảng cáo, tin tức...
     */
    public function index()
    {
        $popularRoutes = collect(); // Khởi tạo collection rỗng
        $featuredBuses = collect(); // Khởi tạo collection rỗng

        try {
            // --- Lấy Tuyến đường phổ biến ---
            // Lấy khoảng 6 tuyến có priority cao nhất (hoặc thấp nhất tùy quy ước)
            // Join với bảng provinces để lấy tên điểm đi/đến
            $popularRoutes = DB::table('routes')
                ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
                ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
                ->select(
                    'routes.id',
                    'routes.title',
                    'routes.slug', // Lấy slug để tạo link
                    'routes.distance',
                    'routes.duration',
                    'routes.start_price',
                    'routes.thumbnail', // Lấy ảnh đại diện tuyến
                    'p_start.name as start_province_name',
                    'p_end.name as end_province_name'
                )
                ->orderBy('routes.priority', 'asc') // Sắp xếp theo ưu tiên
                ->orderBy('routes.created_at', 'desc') // Ưu tiên tuyến mới hơn nếu trùng priority
                ->limit(6) // Giới hạn số lượng tuyến hiển thị
                ->get();

            // --- Lấy Các loại xe nổi bật ---
            // Lấy khoảng 3-4 loại xe có priority cao nhất
            $featuredBuses = DB::table('buses')
                ->select('id', 'name', 'type', 'thumbnail', 'description', 'services', 'slug') // Lấy slug nếu có trang chi tiết loại xe
                ->orderBy('priority', 'asc')
                ->limit(4) // Giới hạn số lượng loại xe
                ->get()
                ->map(function ($bus) {
                    // Decode JSON services để hiển thị tiện ích
                    try {
                        $bus->services = json_decode($bus->services, true);
                        // Đảm bảo services luôn là mảng
                        if (!is_array($bus->services)) {
                            $bus->services = [];
                        }
                    } catch (\Exception $e) {
                        $bus->services = []; // Gán mảng rỗng nếu lỗi decode
                    }
                    // Format lại tên loại xe cho dễ đọc
                    $bus->type_name = match ($bus->type) {
                        'sleeper' => 'Giường nằm',
                        'cabin' => 'Cabin đơn',
                        'doublecabin' => 'Cabin đôi',
                        'limousine' => 'Limousine ghế ngồi',
                        default => ucfirst($bus->type)
                    };
                    return $bus;
                });

            // --- (Tùy chọn) Lấy dữ liệu khác ---
            // Ví dụ: Banner quảng cáo, tin tức... nếu có bảng tương ứng

        } catch (\Exception $e) {
            // Ghi log lỗi và tiếp tục hiển thị trang với dữ liệu rỗng
            Log::error("Error fetching data for HomePage: " . $e->getMessage());
            // $popularRoutes và $featuredBuses đã được khởi tạo là collection rỗng
        }

        // Truyền dữ liệu đã lấy được vào view homepage.index
        return view("kingexpressbus.client.modules.homepage.index", compact(
            'popularRoutes',
            'featuredBuses'
        // Thêm các biến dữ liệu khác nếu có
        ));
    }
}
