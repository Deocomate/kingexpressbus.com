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

class PaymentController extends Controller
{
    /**
     * Tạo yêu cầu thanh toán VNPAY và chuyển hướng người dùng.
     */
    public function createPayment(Request $request, $bookingId)
    {
        // 1. Lấy thông tin đơn hàng
        $booking = DB::table('bookings')
            ->join('bus_routes', 'bookings.bus_route_id', '=', 'bus_routes.id')
            ->where('bookings.id', $bookingId)
            ->select('bookings.*', 'bus_routes.price as bus_route_price')
            ->first();

        if (!$booking) {
            Log::error("VNPAY Create Payment Error: Booking not found.", ['booking_id' => $bookingId]);
            return redirect()->route('homepage')->with('error', 'Không tìm thấy thông tin đặt vé để thanh toán.');
        }
        if ($booking->status !== 'pending' || $booking->payment_status !== 'unpaid') {
            Log::warning("VNPAY Create Payment Warning: Invalid booking status.", ['booking_id' => $bookingId, 'status' => $booking->status, 'payment_status' => $booking->payment_status]);
            return redirect()->route('homepage')->with('error', 'Đơn hàng này không hợp lệ hoặc đã được xử lý.');
        }

        // 2. Tính toán tổng tiền
        $bookingSeatsInfo = json_decode($booking->seats, true);
        // Lấy số lượng vé từ JSON, mặc định là 0 nếu không tìm thấy hoặc không hợp lệ
        $ticketQuantity = isset($bookingSeatsInfo['quantity']) ? (int)$bookingSeatsInfo['quantity'] : 0;

        if ($ticketQuantity <= 0) {
            Log::error("VNPAY Create Payment Error: Invalid ticket quantity.", ['booking_id' => $bookingId, 'quantity' => $ticketQuantity, 'seats_json' => $booking->seats]);
            return redirect()->back()->with('error', 'Số lượng vé không hợp lệ.');
        }

        $amount = ($booking->bus_route_price ?? 0) * $ticketQuantity;
        $vnp_Amount = (int)($amount * 100); // VNPAY yêu cầu đơn vị là đồng * 100

        if ($vnp_Amount <= 0) {
            Log::error("VNPAY Create Payment Error: Invalid amount.", ['booking_id' => $bookingId, 'amount' => $amount, 'ticket_quantity' => $ticketQuantity]);
            return redirect()->back()->with('error', 'Số tiền thanh toán không hợp lệ.');
        }


        // 3. Chuẩn bị dữ liệu gửi sang VNPAY
        $vnp_TmnCode = env('VNP_TMN_CODE'); // Sửa lại thành VNP_TMN_CODE
        $vnp_HashSecret = env('VNP_HASH_SECRET');
        $vnp_Url = env('VNP_URL'); // Sửa lại thành VNP_URL
        $vnp_Returnurl = env('VNPAY_RETURN_URL'); // Giữ nguyên

        if (!$vnp_TmnCode || !$vnp_HashSecret || !$vnp_Url || !$vnp_Returnurl) {
            Log::critical('VNPAY Configuration Error: Missing required VNPAY config values.');
            return redirect()->back()->with('error', 'Lỗi cấu hình hệ thống thanh toán.');
        }

        $vnp_TxnRef = $booking->id . '_' . time(); // Thêm timestamp để đảm bảo mã giao dịch là duy nhất cho mỗi lần thử thanh toán
        $orderInfoUnaccented = $this->unaccent("Thanh toan ve xe booking #" . $booking->id);
        $vnp_OrderInfo = preg_replace('/[^a-zA-Z0-9\s]/', '', $orderInfoUnaccented);
        $vnp_OrderInfo = substr(trim($vnp_OrderInfo), 0, 255);
        $vnp_OrderType = 'other'; // Hoặc 'billpayment', 'fashion', etc.
        $vnp_Locale = 'vn';
        $vnp_BankCode = $request->input('bank_code', ''); // Lấy bank_code nếu có (cho thanh toán qua thẻ/tk cụ thể)
        $vnp_IpAddr = $request->ip();

        $startTime = Carbon::now('Asia/Ho_Chi_Minh');
        $vnp_CreateDate = $startTime->format('YmdHis');
        $vnp_ExpireDate = $startTime->addMinutes(15)->format('YmdHis'); // Thời gian hết hạn thanh toán (vd: 15 phút)

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate,
        );

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

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
        // Sử dụng URL từ env đã lấy ở trên
        $vnp_checkout_url = $vnp_Url . "?" . $query;
        $vnp_SecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_checkout_url .= 'vnp_SecureHash=' . $vnp_SecureHash;

        Log::debug('VNPAY Request Data:', ['inputData' => $inputData, 'hashdata' => $hashdata, 'secureHash' => $vnp_SecureHash]);
        Log::info('Redirecting to VNPAY', ['url' => $vnp_checkout_url]);

        return Redirect::to($vnp_checkout_url);
    }

    public function handleReturn(Request $request)
    {
        Log::info('VNPAY Return Data:', $request->all());
        $vnp_HashSecret = env("VNP_HASH_SECRET");
        $inputData = $request->all();

        if (empty($vnp_HashSecret) || !isset($inputData['vnp_SecureHash'])) {
            Log::error('VNPAY Return Error: Missing HashSecret or vnp_SecureHash.');
            return redirect()->route('homepage')->with('error', 'Lỗi cấu hình hoặc phản hồi thanh toán không hợp lệ.');
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHashType']); // Remove if present
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
            // Lấy bookingId từ vnp_TxnRef (phần trước dấu '_')
            $txnRefParts = explode('_', $inputData['vnp_TxnRef'] ?? '');
            $bookingId = $txnRefParts[0] ?? null;

            if (!$bookingId || !is_numeric($bookingId)) {
                Log::error('VNPAY Return Error: Invalid or Missing bookingId from vnp_TxnRef.', ['vnp_TxnRef' => $inputData['vnp_TxnRef'] ?? null]);
                return redirect()->route('homepage')->with('error', 'Phản hồi thanh toán không hợp lệ (mã đơn hàng sai định dạng).');
            }

            try {
                $updateResult = DB::transaction(function () use ($bookingId, $inputData) {
                    $booking = DB::table('bookings')->where('id', $bookingId)->lockForUpdate()->first();
                    if (!$booking) {
                        return ['status' => 'error', 'message' => 'Không tìm thấy đơn hàng tương ứng.', 'log_level' => 'error'];
                    }
                    // Chỉ xử lý nếu trạng thái là 'pending' và 'unpaid'
                    if ($booking->status === 'pending' && $booking->payment_status === 'unpaid') {
                        if ($inputData['vnp_ResponseCode'] == '00') { // Giao dịch thành công
                            $updated = DB::table('bookings')->where('id', $bookingId)
                                ->update([
                                    'status' => 'confirmed',
                                    'payment_status' => 'paid',
                                    'updated_at' => now()
                                ]);
                            if ($updated) {
                                $this->sendBookingConfirmationEmail((int)$bookingId); // Send email on successful return
                                return ['status' => 'success', 'message' => 'Thanh toán thành công! Cảm ơn bạn đã đặt vé. Email xác nhận đã được gửi.', 'log_level' => 'info'];
                            } else {
                                // This case might happen if IPN updated it first, or DB error
                                return ['status' => 'info', 'message' => 'Trạng thái đơn hàng có thể đã được cập nhật.', 'log_level' => 'warning'];
                            }
                        } else { // Giao dịch thất bại hoặc bị hủy
                            // Optionally, update status to 'payment_failed' or similar
                            // DB::table('bookings')->where('id', $bookingId)->update(['status' => 'payment_failed', 'updated_at' => now()]);
                            return ['status' => 'error', 'message' => 'Thanh toán thất bại. Mã lỗi VNPAY: ' . $inputData['vnp_ResponseCode'] . '. Vui lòng thử lại hoặc chọn phương thức khác.', 'log_level' => 'warning'];
                        }
                    } else { // Đơn hàng đã được xử lý trước đó (có thể bởi IPN)
                        return ['status' => 'info', 'message' => 'Đơn hàng đã được xử lý trước đó.', 'log_level' => 'info'];
                    }
                });

                session()->forget('departure_date'); // Clear session after processing

                if ($updateResult['status'] === 'success') {
                    Log::info($updateResult['message'], ['booking_id' => $bookingId]);
                    return redirect()->route('homepage')->with('success', $updateResult['message']);
                } elseif ($updateResult['status'] === 'info') {
                    Log::info($updateResult['message'], ['booking_id' => $bookingId]);
                    return redirect()->route('homepage')->with('info', $updateResult['message']);
                } else { // error
                    Log::log($updateResult['log_level'], $updateResult['message'], ['booking_id' => $bookingId]);
                    return redirect()->route('homepage')->with('error', $updateResult['message']);
                }

            } catch (\Exception $e) {
                Log::error('Error processing VNPAY return: ' . $e->getMessage(), ['booking_id' => $bookingId]);
                return redirect()->route('homepage')->with('error', 'Đã xảy ra lỗi trong quá trình xử lý thanh toán.');
            }
        } else {
            Log::error('Invalid VNPAY signature on return.', ['booking_id' => $inputData['vnp_TxnRef'] ?? 'N/A']);
            return redirect()->route('homepage')->with('error', 'Xác thực thanh toán không thành công (Chữ ký không hợp lệ).');
        }
    }

    public function handleIPN(Request $request)
    {
        Log::info('VNPAY IPN Received:', $request->all());
        $vnp_HashSecret = env("VNP_HASH_SECRET");
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
                $txnRefParts = explode('_', $inputData['vnp_TxnRef'] ?? '');
                $bookingId = $txnRefParts[0] ?? null;

                if (!$bookingId || !is_numeric($bookingId)) {
                    Log::error('VNPAY IPN Error: Invalid or Missing bookingId from vnp_TxnRef.', ['vnp_TxnRef' => $inputData['vnp_TxnRef'] ?? null]);
                    return response()->json(['RspCode' => '01', 'Message' => 'Order not found']); // Order not found
                }

                DB::transaction(function () use ($bookingId, $inputData, &$response) {
                    $booking = DB::table('bookings')->where('id', $bookingId)->lockForUpdate()->first();
                    if ($booking) {
                        // Chỉ cập nhật nếu đơn hàng đang chờ thanh toán và chưa được thanh toán
                        if ($booking->status === 'pending' && $booking->payment_status === 'unpaid') {
                            if ($inputData['vnp_ResponseCode'] == '00' && $inputData['vnp_TransactionStatus'] == '00') { // Giao dịch thành công
                                $updated = DB::table('bookings')->where('id', $bookingId)
                                    ->update([
                                        'status' => 'confirmed',
                                        'payment_status' => 'paid',
                                        'updated_at' => now()
                                    ]);
                                if ($updated) {
                                    Log::info('Booking updated successfully via IPN.', ['booking_id' => $bookingId]);
                                    $this->sendBookingConfirmationEmail((int)$bookingId);
                                    $response = ['RspCode' => '00', 'Message' => 'Confirm Success'];
                                } else {
                                    // Có thể đã được cập nhật bởi Return URL hoặc một IPN khác
                                    Log::warning('Booking update via IPN failed or already updated.', ['booking_id' => $bookingId]);
                                    $response = ['RspCode' => '02', 'Message' => 'Order already confirmed'];
                                }
                            } else { // Giao dịch thất bại hoặc bị hủy bởi VNPAY
                                Log::warning('VNPAY IPN reported payment failure for pending order.', ['booking_id' => $bookingId, 'vnp_ResponseCode' => $inputData['vnp_ResponseCode'], 'vnp_TransactionStatus' => $inputData['vnp_TransactionStatus'] ?? 'N/A']);
                                // DB::table('bookings')->where('id', $bookingId)->update(['status' => 'payment_failed', 'updated_at' => now()]);
                                $response = ['RspCode' => '99', 'Message' => 'Payment Failed or Cancelled by VNPAY']; // Use a generic failure code for VNPAY failures
                            }
                        } else if ($booking->status === 'confirmed' && $booking->payment_status === 'paid') {
                            // Đơn hàng đã được xác nhận và thanh toán trước đó (ví dụ qua Return URL)
                            Log::info('Booking already confirmed and paid (IPN).', ['booking_id' => $bookingId]);
                            $response = ['RspCode' => '02', 'Message' => 'Order already confirmed'];
                        } else {
                            // Các trường hợp khác (ví dụ: đơn đã hủy, đã hoàn thành)
                            Log::info('Booking status not eligible for IPN update.', ['booking_id' => $bookingId, 'status' => $booking->status, 'payment_status' => $booking->payment_status]);
                            $response = ['RspCode' => '99', 'Message' => 'Order status not eligible for update']; // Or another appropriate code
                        }
                    } else { // Không tìm thấy đơn hàng
                        Log::error('Booking not found via IPN.', ['booking_id' => $bookingId]);
                        $response = ['RspCode' => '01', 'Message' => 'Order not found'];
                    }
                });
            } else { // Chữ ký không hợp lệ
                Log::error('Invalid VNPAY signature (IPN).', ['booking_id' => $inputData['vnp_TxnRef'] ?? 'N/A']);
                $response = ['RspCode' => '97', 'Message' => 'Invalid Signature'];
            }
        } catch (\Exception $e) {
            Log::error('Error processing VNPAY IPN: ' . $e->getMessage(), $inputData);
            $response = ['RspCode' => '99', 'Message' => 'Unknown error']; // Generic error for VNPAY
        }
        return response()->json($response);
    }

    private function unaccent(string $str): string
    {
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

    private function sendBookingConfirmationEmail(int $bookingId): void
    {
        try {
            $bookingDetailsForMail = DB::table('bookings')
                ->join('customers', 'bookings.customer_id', '=', 'customers.id')
                ->join('bus_routes', 'bookings.bus_route_id', '=', 'bus_routes.id')
                ->join('buses', 'bus_routes.bus_id', '=', 'buses.id')
                ->join('routes', 'bus_routes.route_id', '=', 'routes.id')
                ->join('provinces as p_start', 'routes.province_id_start', '=', 'p_start.id')
                ->join('provinces as p_end', 'routes.province_id_end', '=', 'p_end.id')
                ->where('bookings.id', $bookingId)
                ->select(
                    'bookings.id as booking_id',
                    'bookings.seats', // This is now JSON: {"quantity": N, "pickup_info": "..."}
                    'bookings.payment_method',
                    'bookings.payment_status',
                    'bookings.booking_date',
                    'customers.fullname as customer_name',
                    'customers.email as customer_email',
                    'customers.phone as customer_phone',
                    'bus_routes.start_at',
                    'bus_routes.slug as bus_route_slug',
                    'bus_routes.price as bus_route_price',
                    'buses.name as bus_name',
                    'buses.type as bus_type',
                    'routes.title as route_title',
                    'p_start.name as start_province_name',
                    'p_end.name as end_province_name'
                )->first();

            if (!$bookingDetailsForMail) {
                Log::error("Cannot find booking details to send email.", ['booking_id' => $bookingId]);
                return;
            }

            $seatsInfo = json_decode($bookingDetailsForMail->seats, true) ?: [];
            $quantity = $seatsInfo['quantity'] ?? 0;
            $pickupInfo = $seatsInfo['pickup_display_text'] ?? 'Không xác định';

            $totalPrice = $quantity * ($bookingDetailsForMail->bus_route_price ?? 0);
            $webInfo = DB::table('web_info')->first();

            $mailData = [
                'booking_id' => $bookingDetailsForMail->booking_id,
                'customer_name' => $bookingDetailsForMail->customer_name,
                'customer_email' => $bookingDetailsForMail->customer_email,
                'customer_phone' => $bookingDetailsForMail->customer_phone,
                'route_title' => $bookingDetailsForMail->route_title,
                'start_province' => $bookingDetailsForMail->start_province_name,
                'end_province' => $bookingDetailsForMail->end_province_name,
                'departure_date' => Carbon::parse($bookingDetailsForMail->booking_date)->format('d/m/Y'),
                'start_time' => Carbon::parse($bookingDetailsForMail->start_at)->format('H:i'),
                'bus_name' => $bookingDetailsForMail->bus_name,
                'bus_type_name' => match ($bookingDetailsForMail->bus_type) {
                    'sleeper' => 'Giường nằm',
                    'cabin' => 'Cabin đơn',
                    'doublecabin' => 'Cabin đôi',
                    'limousine' => 'Limousine',
                    default => ucfirst($bookingDetailsForMail->bus_type)
                },
                'bus_route_slug' => $bookingDetailsForMail->bus_route_slug,
                'quantity' => $quantity,
                'pickup_info' => $pickupInfo,
                'total_price' => $totalPrice,
                'payment_method' => $bookingDetailsForMail->payment_method,
                'payment_status' => $bookingDetailsForMail->payment_status, // This will be 'paid' if email sent after successful payment
                'web_logo' => $webInfo->logo ?? null,
                'web_title' => $webInfo->title ?? config('app.name'),
                'web_phone' => $webInfo->hotline ?? $webInfo->phone ?? null,
                'web_email' => $webInfo->email ?? null,
                'web_link' => $webInfo->web_link ?? null,
            ];
            Mail::to($bookingDetailsForMail->customer_email)->queue(new BookingConfirmMail($mailData)); // Use queue for better performance
            Log::info('Booking confirmation email queued after successful payment/IPN.', ['booking_id' => $bookingId]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email after payment.', ['booking_id' => $bookingId, 'error' => $e->getMessage()]);
        }
    }
}
