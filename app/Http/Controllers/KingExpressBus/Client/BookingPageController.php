<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use App\Mail\KingExpressBus\BookingConfirmMail;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Carbon\Carbon;

class BookingPageController extends Controller
{
    public function index(Request $request, string $bus_route_slug)
    {
        $departure_date_str = $request->query('departure_date', session('departure_date'));
        try {
            $departure_date = Carbon::parse($departure_date_str)->startOfDay();
            session(['departure_date' => $departure_date->format('Y-m-d')]);
        } catch (\Exception $e) {
            Log::error('BookingPage: Invalid departure date format.', ['date_str' => $departure_date_str, 'error' => $e->getMessage()]); //
            return redirect()->route('homepage')->with('error', 'Định dạng ngày đi không hợp lệ.'); //
        }

        try {
            $busRouteData = DB::table('bus_routes')
                ->join('buses', 'bus_routes.bus_id', '=', 'buses.id') //
                ->join('routes', 'bus_routes.route_id', '=', 'routes.id') //
                ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id') //
                ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id') //
                ->where('bus_routes.slug', $bus_route_slug) //
                ->select(
                    'bus_routes.id as bus_route_id', //
                    'bus_routes.start_at', //
                    'bus_routes.slug as bus_route_slug', //
                    'bus_routes.price', //
                    'buses.id as bus_id', //
                    'buses.name as bus_name', //
                    'buses.type as bus_type', //
                    'buses.number_of_seats as total_seats', //
                    'routes.title as route_title', //
                    'p_start.name as start_province_name', //
                    'p_end.name as end_province_name' //
                )
                ->first(); //
            if (!$busRouteData) {
                abort(404, 'Không tìm thấy chuyến xe.'); //
            }

            $busRouteData->bus_type_name = match ($busRouteData->bus_type) { //
                'sleeper' => 'Giường nằm', //
                'cabin' => 'Cabin đơn', //
                'doublecabin' => 'Cabin đôi', //
                'limousine' => 'Limousine ghế ngồi', //
                default => ucfirst($busRouteData->bus_type) //
            };
            $stops = DB::table('stops') //
            ->join('districts', 'stops.district_id', '=', 'districts.id') //
            ->where('stops.bus_route_id', $busRouteData->bus_route_id) //
            ->select('stops.id as stop_id', 'stops.title as stop_title_specific', 'stops.stop_at', 'districts.name as district_name', 'districts.type as district_type') //
            ->orderBy('stops.stop_at', 'asc') //
            ->get() //
            ->map(function ($stop) { //
                $stop->display_name = $stop->stop_title_specific ?: $stop->district_name; //
                if ($stop->stop_title_specific && $stop->stop_title_specific !== $stop->district_name) { //
                    $stop->display_name = $stop->stop_title_specific . ' (' . $stop->district_name . ')'; //
                }
                return $stop; //
            });

            $customer = session()->has('customer_id') ? DB::table('customers')->find(session('customer_id')) : null; //
            $webInfo = DB::table('web_info')->first(); //

        } catch (\Exception $e) {
            Log::error('Error fetching booking page data: ' . $e->getMessage(), ['bus_route_slug' => $bus_route_slug]); //
            return redirect()->route('homepage')->with('error', 'Đã xảy ra lỗi khi tải trang đặt vé.'); //
        }

        return view("kingexpressbus.client.modules.booking.index", compact( //
            'busRouteData', //
            'departure_date', //
            'customer', //
            'stops', //
            'webInfo' //
        ));
    }

    public function booking(Request $request, string $bus_route_slug): RedirectResponse
    {
        $departure_date_str = session('departure_date'); //
        try {
            $departure_date = Carbon::parse($departure_date_str)->startOfDay(); //
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Phiên làm việc đã hết hạn hoặc ngày đi không hợp lệ. Vui lòng thử lại.')->withInput(); //
        }

        $busRouteInfo = DB::table('bus_routes')
            ->join('buses', 'bus_routes.bus_id', '=', 'buses.id') //
            ->join('routes', 'bus_routes.route_id', '=', 'routes.id') //
            ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id') //
            ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id') //
            ->where('bus_routes.slug', $bus_route_slug) //
            ->select(
                'bus_routes.id as bus_route_id', //
                'bus_routes.start_at', //
                'bus_routes.price', //
                'buses.name as bus_name', //
                'buses.type as bus_type', //
                'routes.title as route_title', //
                'p_start.name as start_province_name', //
                'p_end.name as end_province_name' //
            )
            ->first(); //
        if (!$busRouteInfo) {
            return redirect()->route('homepage')->with('error', 'Chuyến xe không tồn tại.'); //
        }
        $bus_route_id = $busRouteInfo->bus_route_id; //

        $validator = Validator::make($request->all(), [ //
            'number_of_tickets' => 'required|integer|min:1', //
            'fullname' => 'required|string|max:255', //
            'email' => 'required|email|max:255', //
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15', //
            'address' => 'nullable|string|max:255', //
            'payment_method' => 'required|in:online,offline', //
            'pickup_point' => 'required|string', //
            'hotel_address_detail' => 'nullable|string|max:255|required_if:pickup_point,hotel_old_quarter', //
        ], [
            'number_of_tickets.required' => 'Vui lòng nhập số lượng vé.', //
            'number_of_tickets.integer' => 'Số lượng vé phải là số.', //
            'number_of_tickets.min' => 'Vui lòng đặt ít nhất 1 vé.', //
            'fullname.required' => 'Vui lòng nhập họ và tên.', //
            'email.required' => 'Vui lòng nhập địa chỉ email.', //
            'email.email' => 'Địa chỉ email không hợp lệ.', //
            'phone.required' => 'Vui lòng nhập số điện thoại.', //
            'phone.regex' => 'Số điện thoại không hợp lệ.', //
            'phone.min' => 'Số điện thoại quá ngắn.', //
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.', //
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.', //
            'pickup_point.required' => 'Vui lòng chọn điểm đón.', //
            'hotel_address_detail.required_if' => 'Vui lòng nhập địa chỉ khách sạn tại Phố Cổ.', //
        ]);
        if ($validator->fails()) { //
            return redirect()->back()->withErrors($validator)->withInput(); //
        }

        $paymentMethod = $request->input('payment_method'); //
        $customerInfo = $request->only(['fullname', 'email', 'phone', 'address']); //
        $numberOfTickets = (int)$request->input('number_of_tickets'); //
        $pickupOptionValue = $request->input('pickup_point'); //
        $hotelAddressDetail = $request->input('hotel_address_detail'); //

        $bookingResult = null; //
        $customerForMail = null; //
        $totalPrice = 0; //
        $pickupInfoForMail = ''; //

        try {
            DB::transaction(function () use ( //
                $request, $bus_route_id, $departure_date, $paymentMethod, $customerInfo,
                $busRouteInfo, &$bookingResult, &$customerForMail, &$totalPrice,
                $numberOfTickets, $pickupOptionValue, $hotelAddressDetail, &$pickupInfoForMail
            ) {
                $customer_id = session('customer_id'); //
                $customerRecord = null; //

                if (!$customer_id) { //
                    $customerRecord = DB::table('customers')->where('email', $customerInfo['email'])->first(); //
                    if ($customerRecord) {
                        DB::table('customers')->where('id', $customerRecord->id)->update([ //
                            'fullname' => $customerInfo['fullname'], //
                            'phone' => $customerInfo['phone'], //
                            'address' => $customerInfo['address'], //
                            'updated_at' => now(), //
                        ]);
                        $customerRecord = DB::table('customers')->find($customerRecord->id); //
                    } else {
                        $newCustomerId = DB::table('customers')->insertGetId([ //
                            'fullname' => $customerInfo['fullname'], //
                            'email' => $customerInfo['email'], //
                            'phone' => $customerInfo['phone'], //
                            'address' => $customerInfo['address'], //
                            'is_registered' => false, //
                            'password' => null, //
                            'created_at' => now(), //
                            'updated_at' => now(), //
                        ]);
                        $customerRecord = DB::table('customers')->find($newCustomerId); //
                    }
                    if (!$customerRecord || !$customerRecord->id) { //
                        throw new \Exception("Không thể tạo hoặc cập nhật thông tin khách hàng."); //
                    }
                    $customer_id = $customerRecord->id; //
                } else {
                    $customerRecord = DB::table('customers')->find($customer_id); //
                    if (!$customerRecord) { //
                        session()->forget('customer_id'); //
                        throw new \Exception("Thông tin tài khoản không hợp lệ, vui lòng đăng nhập lại."); //
                    }
                }
                $customerForMail = $customerRecord; //

                $pickupDisplayText = ''; //
                if ($pickupOptionValue === 'hotel_old_quarter') { //
                    $pickupDisplayText = "Đón tại khách sạn Phố Cổ: " . $hotelAddressDetail; //
                } else {
                    if (preg_match('/^stop_id_(\d+)_(.+)$/', $pickupOptionValue, $matches)) { //
                        $stopId = $matches[1]; //
                        $pickupDisplayText = $matches[2]; //
                        $stopData = DB::table('stops')->where('id', $stopId)->first(); //
                        if ($stopData && $stopData->stop_at) { //
                            $pickupDisplayText .= ' (Dự kiến: ' . Carbon::parse($stopData->stop_at)->format('H:i') . ')'; //
                        }
                    } else {
                        $pickupDisplayText = $pickupOptionValue; //
                    }
                }
                $pickupInfoForMail = $pickupDisplayText; //

                $seatsJsonData = json_encode([ //
                    'quantity' => $numberOfTickets, //
                    'pickup_option_value' => $pickupOptionValue, //
                    'pickup_display_text' => $pickupDisplayText, //
                    'pickup_address_detail' => ($pickupOptionValue === 'hotel_old_quarter') ? $hotelAddressDetail : null //
                ]);

                $totalPrice = $numberOfTickets * ($busRouteInfo->price ?? 0); //
                $bookingData = [ //
                    'customer_id' => $customer_id, //
                    'bus_route_id' => $bus_route_id, //
                    'booking_date' => $departure_date->format('Y-m-d'), //
                    'seats' => $seatsJsonData, //
                    'payment_method' => $paymentMethod, //
                    'status' => ($paymentMethod === 'offline') ? 'confirmed' : 'pending', // // For 'online' (bank transfer), status is 'pending'
                    'payment_status' => 'unpaid', //
                    'created_at' => now(), //
                    'updated_at' => now(), //
                ];
                $bookingId = DB::table('bookings')->insertGetId($bookingData); //

                $bookingResult = [ //
                    'success' => true, //
                    'booking_id' => $bookingId, //
                    'total_price' => $totalPrice, //
                ];
            });

            if ($bookingResult && $bookingResult['success']) { //
                $bookingId = $bookingResult['booking_id']; //
                $totalPrice = $bookingResult['total_price']; //

                if ($customerForMail) { //
                    try {
                        $webInfo = DB::table('web_info')->first(); //
                        $mailData = [ //
                            'booking_id' => $bookingId, //
                            'customer_name' => $customerForMail->fullname, //
                            'customer_email' => $customerForMail->email, //
                            'customer_phone' => $customerForMail->phone, //
                            'route_title' => $busRouteInfo->route_title, //
                            'start_province' => $busRouteInfo->start_province_name, //
                            'end_province' => $busRouteInfo->end_province_name, //
                            'departure_date' => $departure_date->format('d/m/Y'), //
                            'start_time' => Carbon::parse($busRouteInfo->start_at)->format('H:i'), //
                            'bus_name' => $busRouteInfo->bus_name, //
                            'bus_type_name' => match ($busRouteInfo->bus_type) { //
                                'sleeper' => 'Giường nằm', //
                                'cabin' => 'Cabin đơn', //
                                'doublecabin' => 'Cabin đôi', //
                                'limousine' => 'Limousine', //
                                default => ucfirst($busRouteInfo->bus_type) //
                            },
                            'bus_route_slug' => $bus_route_slug, //
                            'quantity' => $numberOfTickets, //
                            'pickup_info' => $pickupInfoForMail, //
                            'total_price' => $totalPrice, //
                            'payment_method' => $paymentMethod, //
                            'payment_status' => 'unpaid', //
                            'web_logo' => $webInfo->logo ?? null, //
                            'web_title' => $webInfo->title ?? config('app.name'), //
                            'web_phone' => $webInfo->hotline ?? $webInfo->phone ?? null, //
                            'web_email' => $webInfo->email ?? null, //
                            'web_link' => $webInfo->web_link ?? null, //
                            // Add flag for bank transfer info in email
                            'needs_bank_transfer_info' => ($paymentMethod === 'online'),
                        ];
                        Mail::to($customerForMail->email)->send(new BookingConfirmMail($mailData)); //
                        Mail::to("kingexpressbus@gmail.com")->send(new BookingConfirmMail($mailData)); //
                        Log::info('Booking confirmation email queued.', ['booking_id' => $bookingId, 'payment_method' => $paymentMethod]); //
                    } catch (\Exception $e) {
                        Log::error('Failed to queue booking email.', ['booking_id' => $bookingId, 'error' => $e->getMessage()]); //
                    }
                } else {
                    Log::warning('Customer info not available for sending email.', ['booking_id' => $bookingId]); //
                }

                session()->forget('departure_date'); //

                if ($paymentMethod === 'online') {
                    return redirect()->route('homepage')
                        ->with('success', 'Yêu cầu đặt vé #' . $bookingId . ' đã được ghi nhận. Vui lòng chuyển khoản theo thông tin trong email để hoàn tất đặt vé.');
                } else { // Thanh toán offline
                    return redirect()->route('homepage') //
                    ->with('success', 'Đặt vé thành công! Mã đặt vé của bạn là #' . $bookingId . '. Vui lòng thanh toán khi lên xe hoặc tại văn phòng.'); //
                }
            } else {
                throw new \Exception("Không thể hoàn tất đặt vé do lỗi không xác định trong transaction."); //
            }
        } catch (\Exception $e) {
            Log::error('Booking process failed: ' . $e->getMessage(), ['request_data' => $request->except(['_token', 'password', 'password_confirmation'])]); //
            return redirect()->back()->with('error', 'Đặt vé thất bại: ' . $e->getMessage())->withInput(); //
        }
    }
}
