<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use App\Mail\KingExpressBus\BookingConfirmMail;

// Đảm bảo đã import Mailable
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

// Thêm RedirectResponse
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// Đảm bảo đã import Mail Facade
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class BookingPageController extends Controller
{
    public function index(Request $request, string $bus_route_slug)
    {
        // --- Lấy và Validate Ngày đi ---
        $departure_date_str = $request->query('departure_date', session('departure_date'));
        try {
            $departure_date = Carbon::parse($departure_date_str)->startOfDay();
            session(['departure_date' => $departure_date->format('Y-m-d')]);
        } catch (\Exception $e) {
            Log::error('BookingPage: Invalid departure date format.', ['date_str' => $departure_date_str, 'error' => $e->getMessage()]);
            return redirect()->route('homepage')->with('error', 'Định dạng ngày đi không hợp lệ.');
        }

        try {
            // --- Lấy thông tin Chuyến xe (Bus Route) và Xe (Bus) ---
            $busRouteData = DB::table('bus_routes')
                ->join('buses', 'bus_routes.bus_id', '=', 'buses.id')
                ->join('routes', 'bus_routes.route_id', '=', 'routes.id')
                ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
                ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
                ->where('bus_routes.slug', $bus_route_slug)
                ->select(
                    'bus_routes.id as bus_route_id',
                    'bus_routes.start_at',
                    'bus_routes.slug as bus_route_slug',
                    'buses.id as bus_id',
                    'buses.name as bus_name',
                    'buses.type as bus_type',
                    'buses.seat_row_number',
                    'buses.seat_column_number',
                    'buses.floors',
                    'buses.number_of_seats as total_seats',
                    'routes.start_price',
                    'routes.title as route_title',
                    'p_start.name as start_province_name',
                    'p_end.name as end_province_name'
                )
                ->first();

            if (!$busRouteData) {
                abort(404, 'Không tìm thấy chuyến xe.');
            }

            $busRouteData->bus_type_name = match ($busRouteData->bus_type) {
                'sleeper' => 'Giường nằm',
                'cabin' => 'Cabin đơn',
                'doublecabin' => 'Cabin đôi',
                'limousine' => 'Limousine ghế ngồi',
                default => ucfirst($busRouteData->bus_type)
            };

            // --- Lấy danh sách ghế đã đặt ---
            $bookedSeatsData = DB::table('bookings')
                ->where('bus_route_id', $busRouteData->bus_route_id)
                ->whereDate('booking_date', $departure_date->format('Y-m-d'))
                ->whereNotIn('status', ['cancelled'])
                ->pluck('seats');

            $bookedSeats = $bookedSeatsData->flatMap(fn($jsonSeats) => json_decode($jsonSeats, true) ?: [])->unique()->values()->toArray();

            // --- Lấy thông tin khách hàng nếu đã đăng nhập ---
            $customer = session()->has('customer_id') ? DB::table('customers')->find(session('customer_id')) : null;

        } catch (\Exception $e) {
            Log::error('Error fetching booking page data: ' . $e->getMessage(), ['bus_route_slug' => $bus_route_slug]);
            return redirect()->route('homepage')->with('error', 'Đã xảy ra lỗi khi tải trang đặt vé.');
        }

        return view("kingexpressbus.client.modules.booking.index", compact(
            'busRouteData',
            'departure_date',
            'bookedSeats',
            'customer'
        ));
    }

    public function booking(Request $request, string $bus_route_slug): RedirectResponse // Thêm type hint
    {
        // --- Lấy Ngày đi từ session ---
        $departure_date_str = session('departure_date');

        try {
            $departure_date = Carbon::parse($departure_date_str)->startOfDay();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Phiên làm việc đã hết hạn hoặc ngày đi không hợp lệ. Vui lòng thử lại.')->withInput();
        }

        // --- Lấy thông tin Bus Route, Route, Bus ---
        $busRouteInfo = DB::table('bus_routes')
            ->join('buses', 'bus_routes.bus_id', '=', 'buses.id')
            ->join('routes', 'bus_routes.route_id', '=', 'routes.id')
            ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
            ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
            ->where('bus_routes.slug', $bus_route_slug)
            ->select(
                'bus_routes.id as bus_route_id',
                'bus_routes.start_at',
                'buses.name as bus_name',
                'buses.type as bus_type',
                'routes.start_price',
                'routes.title as route_title',
                'p_start.name as start_province_name',
                'p_end.name as end_province_name'
            )
            ->first();
        if (!$busRouteInfo) {
            return redirect()->route('homepage')->with('error', 'Chuyến xe không tồn tại.');
        }
        $bus_route_id = $busRouteInfo->bus_route_id;

        // --- Validation Dữ liệu ---
        $validator = Validator::make($request->all(), [
            'seats' => 'required|array|min:1',
            'seats.*' => 'required|string|max:10',
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15', // Cập nhật regex nếu cần
            'address' => 'nullable|string|max:255',
            'payment_method' => 'required|in:online,offline',
        ], [
            'seats.required' => 'Vui lòng chọn ít nhất một ghế.',
            'seats.min' => 'Vui lòng chọn ít nhất một ghế.',
            'seats.*.required' => 'Mã ghế không hợp lệ.',
            'fullname.required' => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.regex' => 'Số điện thoại không hợp lệ.',
            'phone.min' => 'Số điện thoại quá ngắn.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $selectedSeats = $request->input('seats');
        $paymentMethod = $request->input('payment_method');
        $customerInfo = $request->only(['fullname', 'email', 'phone', 'address']);

        // --- Xử lý Đặt vé trong Transaction ---
        $bookingResult = null;
        $customerForMail = null; // Biến này sẽ chứa thông tin khách hàng để gửi mail (cho offline)
        $totalPrice = 0;

        try {
            // Sử dụng DB::transaction để đảm bảo tính toàn vẹn
            DB::transaction(function () use ($request, $bus_route_id, $departure_date, $selectedSeats, $paymentMethod, $customerInfo, $busRouteInfo, &$bookingResult, &$customerForMail, &$totalPrice) {

                // 1. Kiểm tra lại ghế trống (quan trọng)
                $allCurrentlyBookedSeats = DB::table('bookings')
                    ->where('bus_route_id', $bus_route_id)
                    ->whereDate('booking_date', $departure_date->format('Y-m-d'))
                    ->whereNotIn('status', ['cancelled'])
                    ->lockForUpdate() // *** Thêm lock để tránh race condition ***
                    ->pluck('seats')
                    ->flatMap(fn($json) => json_decode($json, true) ?: [])
                    ->unique()->toArray();

                $conflictingSeats = [];
                foreach ($selectedSeats as $seat) {
                    if (in_array($seat, $allCurrentlyBookedSeats)) {
                        $conflictingSeats[] = $seat;
                    }
                }
                if (!empty($conflictingSeats)) {
                    throw new \Exception("Ghế [" . implode(', ', $conflictingSeats) . "] vừa được đặt. Vui lòng chọn lại.");
                }

                // 2. Tìm hoặc Tạo Khách hàng
                $customer_id = session('customer_id');
                $customerRecord = null; // Biến lưu trữ bản ghi customer đầy đủ

                if (!$customer_id) {
                    // Dùng updateOrCreate để tránh lỗi nếu email đã tồn tại
                    $customerRecord = DB::table('customers')->updateOrInsert(
                        ['email' => $customerInfo['email']],
                        [
                            'fullname' => $customerInfo['fullname'],
                            'phone' => $customerInfo['phone'],
                            'address' => $customerInfo['address'],
                            'is_registered' => false,
                            'password' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    // Lấy lại ID sau khi updateOrCreate (vì nó có thể trả về boolean)
                    if (is_bool($customerRecord) && $customerRecord) { // Nếu trả về true (chỉ update)
                        $customerRecord = DB::table('customers')->where('email', $customerInfo['email'])->first();
                    } elseif (!is_bool($customerRecord)) { // Nếu trả về model (khi insert)
                        // Đã có $customerRecord
                    } else { // Lỗi không xác định
                        throw new \Exception("Không thể tạo hoặc cập nhật thông tin khách hàng.");
                    }
                    $customer_id = $customerRecord->id;

                } else {
                    // Lấy thông tin khách hàng đã đăng nhập
                    $customerRecord = DB::table('customers')->find($customer_id);
                    if (!$customerRecord) {
                        // Nếu không tìm thấy customer dù có session_id (lỗi lạ)
                        session()->forget('customer_id'); // Xóa session lỗi
                        throw new \Exception("Thông tin tài khoản không hợp lệ, vui lòng đăng nhập lại.");
                    }
                    // Cập nhật thông tin nếu cần (ví dụ: cập nhật sđt, địa chỉ nếu khác)
                    // DB::table('customers')->where('id', $customer_id)->update([...]);
                }
                // Gán customer record để dùng cho mail
                $customerForMail = $customerRecord;


                // 3. Tạo Booking Mới
                $totalPrice = count($selectedSeats) * ($busRouteInfo->start_price ?? 0);
                $bookingData = [
                    'customer_id' => $customer_id,
                    'bus_route_id' => $bus_route_id,
                    'booking_date' => $departure_date->format('Y-m-d'),
                    'seats' => json_encode($selectedSeats),
                    'payment_method' => $paymentMethod,
                    'status' => ($paymentMethod === 'offline') ? 'confirmed' : 'pending', // Online là pending
                    'payment_status' => 'unpaid',
                    'created_at' => now(),
                    'updated_at' => now(),
                    // 'total_price' => $totalPrice, // Thêm nếu có cột này
                ];
                $bookingId = DB::table('bookings')->insertGetId($bookingData);

                // Gán kết quả trả về từ transaction
                $bookingResult = [
                    'success' => true,
                    'booking_id' => $bookingId,
                    'total_price' => $totalPrice, // Trả về tổng giá để dùng sau
                ];

            }); // Kết thúc DB::transaction

            // --- Xử lý sau Transaction ---
            if ($bookingResult && $bookingResult['success']) {
                $bookingId = $bookingResult['booking_id'];
                $totalPrice = $bookingResult['total_price']; // Lấy tổng giá từ kết quả transaction

                // *** Xử lý dựa trên phương thức thanh toán ***
                if ($paymentMethod === 'online') {
                    // *** CHUYỂN HƯỚNG ĐẾN VNPAY ***
                    Log::info('Redirecting to VNPAY create payment.', ['booking_id' => $bookingId]);
                    return redirect()->route('payment.vnpay.create', ['bookingId' => $bookingId]);

                } else { // Thanh toán offline
                    // *** GỬI EMAIL XÁC NHẬN (OFFLINE) ***
                    if ($customerForMail) { // $customerForMail đã được gán trong transaction
                        try {
                            $webInfo = DB::table('web_info')->first();
                            $mailData = [
                                'booking_id' => $bookingId,
                                'customer_name' => $customerForMail->fullname,
                                'customer_email' => $customerForMail->email,
                                'customer_phone' => $customerForMail->phone,
                                'route_title' => $busRouteInfo->route_title,
                                'start_province' => $busRouteInfo->start_province_name,
                                'end_province' => $busRouteInfo->end_province_name,
                                'departure_date' => $departure_date->format('d/m/Y'),
                                'start_time' => Carbon::parse($busRouteInfo->start_at)->format('H:i'),
                                'bus_name' => $busRouteInfo->bus_name,
                                'bus_type_name' => match ($busRouteInfo->bus_type) { /*...*/
                                    'sleeper' => 'Giường nằm',
                                    'cabin' => 'Cabin đơn',
                                    'doublecabin' => 'Cabin đôi',
                                    'limousine' => 'Limousine',
                                    default => ucfirst($busRouteInfo->bus_type)
                                },
                                'bus_route_slug' => $bus_route_slug,
                                'seats' => $selectedSeats,
                                'total_price' => $totalPrice, // Sử dụng $totalPrice đã tính
                                'payment_method' => $paymentMethod,
                                'payment_status' => 'unpaid',
                                'web_logo' => $webInfo->logo ?? null,
                                'web_title' => $webInfo->title ?? config('app.name'),
                                'web_phone' => $webInfo->hotline ?? $webInfo->phone ?? null,
                                'web_email' => $webInfo->email ?? null,
                                'web_link' => $webInfo->web_link ?? null,
                            ];
                            Mail::to($customerForMail->email)->send(new BookingConfirmMail($mailData)); // Dùng queue
                            Mail::to("kingexpressbus@gmail.com")->send(new BookingConfirmMail($mailData)); // Dùng queue
                            Log::info('Offline booking confirmation email queued.', ['booking_id' => $bookingId]);
                        } catch (\Exception $e) {
                            dd($e);
                            Log::error('Failed to queue offline booking email.', ['booking_id' => $bookingId, 'error' => $e->getMessage()]);
                        }
                    } else {
                        Log::warning('Customer info not available for sending offline email.', ['booking_id' => $bookingId]);
                    }
                    session()->forget('departure_date');
                    return redirect()->route('homepage')
                        ->with('success', 'Đặt vé thành công! Mã đặt vé của bạn là #' . $bookingId . '. Vui lòng thanh toán khi lên xe hoặc tại văn phòng.');
                }
            } else {
                // Trường hợp transaction không thành công nhưng không ném Exception
                throw new \Exception("Không thể hoàn tất đặt vé do lỗi không xác định trong transaction.");
            }

        } catch (\Exception $e) {
            Log::error('Booking process failed: ' . $e->getMessage(), ['request_data' => $request->except(['_token'])]);
            return redirect()->back()->with('error', 'Đặt vé thất bại: ' . $e->getMessage())->withInput();
        }
    }
}
