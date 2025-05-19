<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Có thể cần nếu cho đổi mật khẩu
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UserInformationController extends Controller
{
    /**
     * Hiển thị trang thông tin tài khoản và lịch sử đặt vé.
     * Yêu cầu đã đăng nhập (được xử lý bởi middleware 'client.auth').
     */
    public function index()
    {
        try {
            $customerId = session('customer_id'); // Lấy ID từ session

            // Lấy thông tin khách hàng hiện tại
            $customer = DB::table('customers')->find($customerId);

            if (!$customer) {
                // Nếu không tìm thấy thông tin (lỗi lạ), đăng xuất và báo lỗi
                session()->forget(['customer_id', 'customer_name', 'customer_email']);
                return redirect()->route('client.login_page')->with('error', 'Không tìm thấy thông tin tài khoản.');
            }

            // Lấy lịch sử đặt vé của khách hàng
            // Sắp xếp theo ngày đặt mới nhất
            $bookings = DB::table('bookings')
                ->join('bus_routes', 'bookings.bus_route_id', '=', 'bus_routes.id')
                ->join('buses', 'bus_routes.bus_id', '=', 'buses.id')
                ->join('routes', 'bus_routes.route_id', '=', 'routes.id')
                ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
                ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
                ->where('bookings.customer_id', $customerId)
                ->select(
                    'bookings.id as booking_id',
                    'bookings.booking_date',
                    'bookings.seats', // JSON
                    'bookings.status as booking_status',
                    'bookings.payment_status',
                    'bookings.payment_method',
                    'bookings.created_at as booking_created_at',
                    'bus_routes.start_at',
                    'bus_routes.slug as bus_route_slug',
                    'buses.name as bus_name',
                    'routes.start_price', // Giá gốc của tuyến
                    'p_start.name as start_province_name',
                    'p_end.name as end_province_name'
                )
                ->orderBy('bookings.created_at', 'desc') // Sắp xếp mới nhất lên đầu
                ->paginate(10); // Phân trang, ví dụ 10 vé/trang

            // Xử lý thêm cho mỗi booking (ví dụ: tính tổng tiền, format seats)
            $bookings->getCollection()->transform(function ($booking) {
                $seatsArray = json_decode($booking->seats, true) ?: [];
                $booking->seats_list = implode(', ', $seatsArray); // Tạo chuỗi ghế
                // Tính tổng tiền tạm thời (cần logic giá chính xác hơn nếu có)
                $booking->total_price = count($seatsArray) * ($booking->start_price ?? 0);
                return $booking;
            });


        } catch (\Exception $e) {
            Log::error('Error fetching user information: ' . $e->getMessage(), ['customer_id' => $customerId ?? null]);
            // Nếu lỗi DB, có thể trả về view lỗi hoặc redirect về home với lỗi
            return redirect()->route('homepage')->with('error', 'Không thể tải thông tin tài khoản.');
        }

        // Trả về view với dữ liệu khách hàng và lịch sử đặt vé
        return view("kingexpressbus.client.modules.user_information.index", compact(
            'customer',
            'bookings'
        ));
    }

    /**
     * Cập nhật thông tin khách hàng.
     * Yêu cầu đã đăng nhập.
     */
    public function update(Request $request): RedirectResponse
    {
        $customerId = session('customer_id');
        if (!$customerId) {
            return redirect()->route('client.login_page')->with('error', 'Phiên đăng nhập đã hết hạn.');
        }

        // Validate dữ liệu cập nhật (không cho sửa email)
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
            'address' => 'nullable|string|max:255',
            // Thêm validation cho đổi mật khẩu nếu có
            // 'current_password' => 'nullable|required_with:new_password|string|current_password:customer', // Cần custom guard 'customer' nếu dùng
            // 'new_password' => ['nullable', 'required_with:current_password', 'confirmed', Password::min(8)],
        ], [
            // Messages
            'fullname.required' => 'Vui lòng nhập họ và tên.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $updateData = $request->only(['fullname', 'phone', 'address']);
            $updateData['updated_at'] = now();

            // (Tùy chọn) Xử lý đổi mật khẩu
            // if ($request->filled('new_password')) {
            //     $updateData['password'] = Hash::make($request->input('new_password'));
            // }

            $updated = DB::table('customers')->where('id', $customerId)->update($updateData);

            if ($updated) {
                // Cập nhật tên trong session nếu thay đổi
                if ($request->input('fullname') !== session('customer_name')) {
                    session(['customer_name' => $request->input('fullname')]);
                }
                Log::info('Customer information updated.', ['customer_id' => $customerId]);
                return redirect()->route('client.user_information_page')->with('success', 'Cập nhật thông tin thành công!');
            } else {
                // Không có gì thay đổi hoặc lỗi không xác định
                return redirect()->route('client.user_information_page')->with('info', 'Không có thông tin nào được thay đổi.');
            }

        } catch (\Exception $e) {
            Log::error('Error updating customer information: ' . $e->getMessage(), ['customer_id' => $customerId]);
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật thông tin.')->withInput();
        }
    }
}
