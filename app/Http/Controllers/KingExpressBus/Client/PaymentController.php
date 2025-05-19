<?php

namespace App\Http\Controllers\KingExpressBus\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Mail\KingExpressBus\BookingConfirmMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

// *** Đảm bảo đã import Carbon ***

class PaymentController extends Controller
{
    /**
     * Tạo yêu cầu thanh toán VNPAY và chuyển hướng người dùng.
     */
    public function createPayment(Request $request, $bookingId)
    {
        // ... (Phần lấy booking và tính amount giữ nguyên) ...
        // 1. Lấy thông tin đơn hàng
        $booking = DB::table('bookings')
            ->join('bus_routes', 'bookings.bus_route_id', '=', 'bus_routes.id')
            ->join('routes', 'bus_routes.route_id', '=', 'routes.id')
            ->where('bookings.id', $bookingId)
            ->select('bookings.*', 'routes.start_price')
            ->first();

        if (!$booking) { /* ... xử lý lỗi ... */
            Log::error("VNPAY Create Payment Error: Booking not found.", ['booking_id' => $bookingId]);
            return redirect()->route('homepage')->with('error', 'Không tìm thấy thông tin đặt vé để thanh toán.');
        }
        if ($booking->status !== 'pending' || $booking->payment_status !== 'unpaid') { /* ... xử lý lỗi ... */
            Log::warning("VNPAY Create Payment Warning: Invalid booking status.", ['booking_id' => $bookingId, 'status' => $booking->status, 'payment_status' => $booking->payment_status]);
            return redirect()->route('homepage')->with('error', 'Đơn hàng này không hợp lệ hoặc đã được xử lý.');
        }

        // 2. Tính toán tổng tiền
        $seatCount = count(json_decode($booking->seats, true) ?: []);
        $amount = ($booking->start_price ?? 0) * $seatCount;
        $vnp_Amount = (int)($amount * 100);
        if ($vnp_Amount <= 0) { /* ... xử lý lỗi ... */
            Log::error("VNPAY Create Payment Error: Invalid amount.", ['booking_id' => $bookingId, 'amount' => $amount]);
            return redirect()->back()->with('error', 'Số tiền thanh toán không hợp lệ.');
        }


        // 3. Chuẩn bị dữ liệu gửi sang VNPAY
        $vnp_TmnCode = env('vnp_tmn_code');
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('vnp_url');
        $vnp_Returnurl = env('vnpay_return_url');

        if (!$vnp_TmnCode || !$vnp_HashSecret || !$vnp_Url || !$vnp_Returnurl) { /* ... xử lý lỗi config ... */
            Log::critical('VNPAY Configuration Error: Missing required VNPAY config values.');
            return redirect()->back()->with('error', 'Lỗi cấu hình hệ thống thanh toán.');
        }

        $vnp_TxnRef = $booking->id;
        $orderInfoUnaccented = $this->unaccent("Thanh toan ve xe booking #" . $booking->id);
        $vnp_OrderInfo = preg_replace('/[^a-zA-Z0-9\s]/', '', $orderInfoUnaccented);
        $vnp_OrderInfo = substr(trim($vnp_OrderInfo), 0, 255);
        $vnp_OrderType = 'other';
        $vnp_Locale = 'vn';
        $vnp_BankCode = $request->input('bank_code', '');
        $vnp_IpAddr = $request->ip();

        // *** SỬA LỖI: Sử dụng Carbon với Timezone Asia/Ho_Chi_Minh ***
        $startTime = Carbon::now('Asia/Ho_Chi_Minh'); // Lấy thời gian hiện tại ở VN
        $vnp_CreateDate = $startTime->format('YmdHis'); // Format YmdHis
        $vnp_ExpireDate = $startTime->addMinutes(15)->format('YmdHis'); // Thêm 15 phút và format

        Log::debug('VNPAY Dates:', ['CreateDate' => $vnp_CreateDate, 'ExpireDate' => $vnp_ExpireDate, 'Timezone' => 'Asia/Ho_Chi_Minh']);

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate, // Đã có timezone đúng
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate, // Đã có timezone đúng
        );

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        // ... (Phần tạo hash và URL giữ nguyên) ...
        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = config('vnpay.url') . "?" . $query; // Lấy URL từ config
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= 'vnp_SecureHash=' . $vnp_SecureHash;

        Log::debug('VNPAY Request Data:', ['inputData' => $inputData, 'hashdata' => $hashdata, 'secureHash' => $vnp_SecureHash]);
        Log::info('Redirecting to VNPAY', ['url' => $vnp_Url]);

        return Redirect::to($vnp_Url);
    }

    // --- Các hàm handleReturn và handleIPN giữ nguyên ---
    public function handleReturn(Request $request)
    { /* ... code ... */
        Log::info('VNPAY Return Data:', $request->all());
        $vnp_HashSecret = env("vnp_hash_secret");
        $inputData = $request->all();
        if (empty($vnp_HashSecret) || !isset($inputData['vnp_SecureHash'])) {
            Log::error('VNPAY Return Error: Missing HashSecret or vnp_SecureHash.');
            return redirect()->route('homepage')->with('error', 'Lỗi cấu hình hoặc phản hồi thanh toán không hợp lệ.');
        }
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash == $vnp_SecureHash) {
            $bookingId = $inputData['vnp_TxnRef'] ?? null;
            if (!$bookingId) {
                Log::error('VNPAY Return Error: Missing vnp_TxnRef.');
                return redirect()->route('homepage')->with('error', 'Phản hồi thanh toán không hợp lệ (thiếu mã đơn hàng).');
            }
            try {
                $updateResult = DB::transaction(function () use ($bookingId, $inputData) {
                    $booking = DB::table('bookings')->where('id', $bookingId)->lockForUpdate()->first();
                    if (!$booking) {
                        return ['status' => 'error', 'message' => 'Không tìm thấy đơn hàng tương ứng.', 'log_level' => 'error'];
                    }
                    if ($booking->status !== 'pending' || $booking->payment_status !== 'unpaid') {
                        return ['status' => 'info', 'message' => 'Đơn hàng đã được xử lý trước đó.', 'log_level' => 'info'];
                    }
                    if ($inputData['vnp_ResponseCode'] == '00') {
                        $updated = DB::table('bookings')->where('id', $bookingId)->where('status', 'pending')->update(['status' => 'confirmed', 'payment_status' => 'paid', 'updated_at' => now()]);
                        if ($updated) {
                            return ['status' => 'success', 'message' => 'Thanh toán thành công! Cảm ơn bạn đã đặt vé.', 'log_level' => 'info'];
                        } else {
                            return ['status' => 'error', 'message' => 'Lỗi cập nhật trạng thái đơn hàng.', 'log_level' => 'error'];
                        }
                    } else {
                        return ['status' => 'error', 'message' => 'Thanh toán thất bại. Mã lỗi VNPAY: ' . $inputData['vnp_ResponseCode'], 'log_level' => 'warning'];
                    }
                });
                if ($updateResult['status'] === 'success') {
                    Log::info($updateResult['message'], ['booking_id' => $bookingId]);
                    return redirect()->route('homepage')->with('success', $updateResult['message']);
                } elseif ($updateResult['status'] === 'info') {
                    Log::info($updateResult['message'], ['booking_id' => $bookingId]);
                    return redirect()->route('homepage')->with('info', $updateResult['message']);
                } else {
                    Log::log($updateResult['log_level'], $updateResult['message'], ['booking_id' => $bookingId]);
                    return redirect()->route('homepage')->with('error', $updateResult['message']);
                }
            } catch (\Exception $e) {
                Log::error('Error processing VNPAY return: ' . $e->getMessage(), ['booking_id' => $inputData['vnp_TxnRef'] ?? 'N/A']);
                return redirect()->route('homepage')->with('error', 'Đã xảy ra lỗi trong quá trình xử lý thanh toán.');
            }
        } else {
            Log::error('Invalid VNPAY signature on return.', ['booking_id' => $inputData['vnp_TxnRef'] ?? 'N/A']);
            return redirect()->route('homepage')->with('error', 'Xác thực thanh toán không thành công (Chữ ký không hợp lệ).');
        }
    }

    public function handleIPN(Request $request)
    { /* ... code IPN giữ nguyên ... */
        Log::info('VNPAY IPN Received:', $request->all());
        $vnp_HashSecret = config('vnpay.hash_secret');
        $inputData = $request->all();
        if (empty($vnp_HashSecret) || !isset($inputData['vnp_SecureHash'])) {
            Log::error('VNPAY IPN Error: Missing HashSecret or vnp_SecureHash.');
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid Signature']);
        }
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $response = [];
        try {
            if ($secureHash == $vnp_SecureHash) {
                $bookingId = $inputData['vnp_TxnRef'] ?? null;
                if (!$bookingId) {
                    Log::error('VNPAY IPN Error: Missing vnp_TxnRef.');
                    return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
                }
                DB::transaction(function () use ($bookingId, $inputData, &$response) {
                    $booking = DB::table('bookings')->where('id', $bookingId)->lockForUpdate()->first();
                    if ($booking) {
                        if (in_array($booking->status, ['pending', 'payment_failed']) && $booking->payment_status == 'unpaid') {
                            if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') {
                                $updated = DB::table('bookings')->where('id', $bookingId)->update(['status' => 'confirmed', 'payment_status' => 'paid', 'updated_at' => now()]);
                                if ($updated) {
                                    Log::info('Booking updated successfully via IPN.', ['booking_id' => $bookingId]);
                                    $response = ['RspCode' => '00', 'Message' => 'Confirm Success']; /* Gửi mail ở đây */
                                    $this->sendBookingConfirmationEmail($bookingId);
                                } else {
                                    Log::warning('Booking update failed via IPN (already updated?).', ['booking_id' => $bookingId]);
                                    $response = ['RspCode' => '02', 'Message' => 'Order already confirmed'];
                                }
                            } else {
                                Log::warning('VNPAY IPN reported payment failure.', ['booking_id' => $bookingId, 'response_code' => $inputData['vnp_ResponseCode']]);
                                $response = ['RspCode' => '99', 'Message' => 'Payment Failed'];
                            }
                        } else {
                            Log::info('Booking already processed (IPN).', ['booking_id' => $bookingId]);
                            $response = ['RspCode' => '02', 'Message' => 'Order already confirmed'];
                        }
                    } else {
                        Log::error('Booking not found via IPN.', ['booking_id' => $bookingId]);
                        $response = ['RspCode' => '01', 'Message' => 'Order not found'];
                    }
                });
            } else {
                Log::error('Invalid VNPAY signature (IPN).', ['booking_id' => $inputData['vnp_TxnRef'] ?? 'N/A']);
                $response = ['RspCode' => '97', 'Message' => 'Invalid Signature'];
            }
        } catch (\Exception $e) {
            Log::error('Error processing VNPAY IPN: ' . $e->getMessage(), $inputData);
            $response = ['RspCode' => '99', 'Message' => 'Unknown error'];
        }
        return response()->json($response);
    }

    /**
     * Helper function to remove accents. (Giữ nguyên)
     */
    private function unaccent(string $str): string
    { /* ... code ... */
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/', 'A', $str);
        $str = preg_replace('/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/', 'E', $str);
        $str = preg_replace('/(Ì|Í|Ị|Ỉ|Ĩ)/', 'I', $str);
        $str = preg_replace('/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/', 'O', $str);
        $str = preg_replace('/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/', 'U', $str);
        $str = preg_replace('/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/', 'Y', $str);
        $str = preg_replace('/(Đ)/', 'D', $str);
        return $str;
    }

    /**
     * (Tùy chọn) Hàm riêng để gửi email. (Giữ nguyên)
     */
    private function sendBookingConfirmationEmail(int $bookingId): void
    { /* ... code ... */
        try {
            $bookingDetailsForMail = DB::table('bookings')->join('customers', 'bookings.customer_id', '=', 'customers.id')->join('bus_routes', 'bookings.bus_route_id', '=', 'bus_routes.id')->join('buses', 'bus_routes.bus_id', '=', 'buses.id')->join('routes', 'bus_routes.route_id', '=', 'routes.id')->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')->where('bookings.id', $bookingId)->select('bookings.id as booking_id', 'bookings.seats', 'bookings.payment_method', 'bookings.payment_status', 'bookings.booking_date', 'customers.fullname as customer_name', 'customers.email as customer_email', 'customers.phone as customer_phone', 'bus_routes.start_at', 'bus_routes.slug as bus_route_slug', 'buses.name as bus_name', 'buses.type as bus_type', 'routes.title as route_title', 'routes.start_price', 'p_start.name as start_province_name', 'p_end.name as end_province_name')->first();
            if (!$bookingDetailsForMail) {
                Log::error("Cannot find booking details to send email.", ['booking_id' => $bookingId]);
                return;
            }
            $selectedSeats = json_decode($bookingDetailsForMail->seats, true) ?: [];
            $totalPrice = count($selectedSeats) * ($bookingDetailsForMail->start_price ?? 0);
            $webInfo = DB::table('web_info')->first();
            $mailData = [ /* ... data mapping ... */
                'booking_id' => $bookingDetailsForMail->booking_id, 'customer_name' => $bookingDetailsForMail->customer_name, 'customer_email' => $bookingDetailsForMail->customer_email, 'customer_phone' => $bookingDetailsForMail->customer_phone, 'route_title' => $bookingDetailsForMail->route_title, 'start_province' => $bookingDetailsForMail->start_province_name, 'end_province' => $bookingDetailsForMail->end_province_name, 'departure_date' => Carbon::parse($bookingDetailsForMail->booking_date)->format('d/m/Y'), 'start_time' => Carbon::parse($bookingDetailsForMail->start_at)->format('H:i'), 'bus_name' => $bookingDetailsForMail->bus_name, 'bus_type_name' => match ($bookingDetailsForMail->bus_type) {
                    'sleeper' => 'Giường nằm',
                    'cabin' => 'Cabin đơn',
                    'doublecabin' => 'Cabin đôi',
                    'limousine' => 'Limousine',
                    default => ucfirst($bookingDetailsForMail->bus_type)
                }, 'bus_route_slug' => $bookingDetailsForMail->bus_route_slug, 'seats' => $selectedSeats, 'total_price' => $totalPrice, 'payment_method' => $bookingDetailsForMail->payment_method, 'payment_status' => $bookingDetailsForMail->payment_status, 'web_logo' => $webInfo->logo ?? null, 'web_title' => $webInfo->title ?? config('app.name'), 'web_phone' => $webInfo->hotline ?? $webInfo->phone ?? null, 'web_email' => $webInfo->email ?? null, 'web_link' => $webInfo->web_link ?? null,];
            Mail::to($bookingDetailsForMail->customer_email)->queue(new BookingConfirmMail($mailData));
            Log::info('Booking confirmation email queued after successful payment/IPN.', ['booking_id' => $bookingId]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email after payment.', ['booking_id' => $bookingId, 'error' => $e->getMessage()]);
        }
    }
}
