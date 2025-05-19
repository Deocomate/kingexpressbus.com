<?php

namespace App\Http\Controllers\KingExpressBus\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Thêm DB Facade
use Illuminate\Validation\Rule;

// Thêm Rule để validate enum
use Carbon\Carbon;

// Thêm Carbon

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     * Liệt kê tất cả các booking, join với các bảng liên quan.
     */
    public function index(Request $request)
    {
        $query = DB::table('bookings')
            ->join('customers', 'bookings.customer_id', '=', 'customers.id')
            ->join('bus_routes', 'bookings.bus_route_id', '=', 'bus_routes.id')
            ->join('buses', 'bus_routes.bus_id', '=', 'buses.id')
            ->join('routes', 'bus_routes.route_id', '=', 'routes.id')
            ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
            ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
            ->select(
                'bookings.*', // Lấy tất cả cột từ bookings
                'customers.fullname as customer_fullname',
                'customers.email as customer_email',
                'customers.phone as customer_phone',
                'bus_routes.title as bus_route_title',
                'bus_routes.start_at',
                'bus_routes.end_at',
                'buses.name as bus_name',
                'routes.title as route_title',
                'p_start.name as start_province_name',
                'p_end.name as end_province_name'
            )
            ->orderBy('bookings.created_at', 'desc'); // Sắp xếp theo ngày tạo mới nhất

        // Optional: Filtering logic based on request input can be added here
        // e.g., filter by status, date range, customer, etc.
        // if ($request->filled('status')) {
        //     $query->where('bookings.status', $request->input('status'));
        // }
        // if ($request->filled('booking_date')) {
        //     $query->whereDate('bookings.booking_date', $request->input('booking_date'));
        // }


        $bookings = $query->paginate(15); // Phân trang, 15 mục mỗi trang

        // Decode JSON seats for each booking (optional, can also be done in view)
        // $bookings->getCollection()->transform(function ($booking) {
        //     $booking->seats = json_decode($booking->seats, true);
        //     return $booking;
        // });

        return view("kingexpressbus.admin.modules.bookings.index", compact('bookings'));
    }

    /**
     * Show the form for creating a new resource.
     * Admin thường không tự tạo booking thủ công nhiều, nhưng để trống hoặc tạo form đơn giản nếu cần.
     */
    public function create()
    {
        // Logic để lấy danh sách customers, bus_routes, etc. nếu cần tạo form
        // return view("kingexpressbus.admin.modules.bookings.create");
        return redirect()->route('admin.bookings.index')->with('info', 'Chức năng tạo booking thủ công chưa được hỗ trợ.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation và logic lưu booking nếu có form create
        return redirect()->route('admin.bookings.index');
    }

    /**
     * Display the specified resource.
     * Hiển thị chi tiết một booking cụ thể.
     */
    public function show(string $id)
    {
        $booking = DB::table('bookings')
            ->join('customers', 'bookings.customer_id', '=', 'customers.id')
            ->join('bus_routes', 'bookings.bus_route_id', '=', 'bus_routes.id')
            ->join('buses', 'bus_routes.bus_id', '=', 'buses.id')
            ->join('routes', 'bus_routes.route_id', '=', 'routes.id')
            ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
            ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
            ->select(
                'bookings.*',
                'customers.fullname as customer_fullname',
                'customers.email as customer_email',
                'customers.phone as customer_phone',
                'customers.address as customer_address',
                'bus_routes.title as bus_route_title',
                'bus_routes.start_at',
                'bus_routes.end_at',
                'buses.name as bus_name',
                'buses.type as bus_type', // Thêm loại xe bus
                'routes.title as route_title',
                'routes.distance',
                'routes.duration',
                'p_start.name as start_province_name',
                'p_end.name as end_province_name'
            )
            ->where('bookings.id', $id)
            ->firstOrFail(); // Sử dụng firstOrFail để tự động 404 nếu không tìm thấy

        // Decode seats JSON
        $booking->seats = json_decode($booking->seats, true);
        if (!is_array($booking->seats)) {
            $booking->seats = []; // Ensure it's an array
        }

        return view("kingexpressbus.admin.modules.bookings.show", compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     * Hiển thị form cho phép sửa status hoặc payment_status.
     */
    public function edit(string $id)
    {
        $booking = DB::table('bookings')
            ->join('customers', 'bookings.customer_id', '=', 'customers.id') // Join để hiển thị tên khách
            ->select('bookings.*', 'customers.fullname as customer_fullname')
            ->where('bookings.id', $id)
            ->firstOrFail();

        // Decode seats JSON just for display if needed in edit form
        $booking->seats = json_decode($booking->seats, true);
        if (!is_array($booking->seats)) {
            $booking->seats = [];
        }

        // Định nghĩa các trạng thái hợp lệ (lấy từ enum trong DB)
        $statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        $paymentStatuses = ['paid', 'unpaid'];
        $paymentMethods = ['online', 'offline']; // Có thể không cho sửa phương thức

        return view("kingexpressbus.admin.modules.bookings.edit", compact('booking', 'statuses', 'paymentStatuses', 'paymentMethods'));
    }

    /**
     * Update the specified resource in storage.
     * Cập nhật status hoặc payment_status.
     */
    public function update(Request $request, string $id)
    {
        $booking = DB::table('bookings')->where('id', $id)->firstOrFail(); // Đảm bảo booking tồn tại

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'confirmed', 'cancelled', 'completed'])],
            'payment_status' => ['required', Rule::in(['paid', 'unpaid'])],
            // 'payment_method' => ['required', Rule::in(['online', 'offline'])], // Thường không nên cho sửa PTTT
            // Thêm validation cho các trường khác nếu cho phép sửa
        ]);

        $validated['updated_at'] = now();

        DB::table('bookings')
            ->where('id', $id)
            ->update($validated);

        return redirect()->route('admin.bookings.show', $id) // Chuyển về trang chi tiết booking vừa sửa
        ->with('success', 'Thông tin đặt vé đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     * Xóa booking.
     */
    public function destroy(string $id)
    {
        $booking = DB::table('bookings')->where('id', $id)->first();

        if (!$booking) {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Không tìm thấy đặt vé để xóa.');
        }

        DB::table('bookings')->where('id', $id)->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Đặt vé đã được xóa thành công!');
    }

    // Helper function to format status (có thể đặt ở Helper class hoặc trong view)
    public static function formatBookingStatus($status)
    {
        switch ($status) {
            case 'pending':
                return '<span class="badge badge-warning">Chờ xử lý</span>';
            case 'confirmed':
                return '<span class="badge badge-success">Đã xác nhận</span>';
            case 'cancelled':
                return '<span class="badge badge-danger">Đã hủy</span>';
            case 'completed':
                return '<span class="badge badge-secondary">Đã hoàn thành</span>';
            default:
                return '<span class="badge badge-light">' . ucfirst($status) . '</span>';
        }
    }

    // Helper function to format payment status
    public static function formatPaymentStatus($status)
    {
        switch ($status) {
            case 'paid':
                return '<span class="badge badge-success">Đã thanh toán</span>';
            case 'unpaid':
                return '<span class="badge badge-warning">Chưa thanh toán</span>';
            default:
                return '<span class="badge badge-light">' . ucfirst($status) . '</span>';
        }
    }

    // Helper function to format payment method
    public static function formatPaymentMethod($method)
    {
        switch ($method) {
            case 'online':
                return 'Trực tuyến';
            case 'offline':
                return 'Thanh toán sau';
            default:
                return ucfirst($method);
        }
    }
}
