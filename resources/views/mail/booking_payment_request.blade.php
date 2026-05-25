<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Yêu cầu thanh toán / Payment Request - {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f7f6;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f4f7f6;">
    <tr>
        <td align="center">
            <table width="600" border="0" cellpadding="0" cellspacing="0"
                   style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e0e0e0;">
                <tr>
                    <td align="center" style="padding: 20px; border-bottom: 1px solid #eeeeee;">
                        @if(!empty($bookingDetails['web_logo']))
                            <img src="{{ $bookingDetails['web_logo'] }}"
                                 alt="{{ $bookingDetails['web_title'] ?? 'King Express Bus' }}"
                                 style="max-height: 50px; width: auto;">
                        @else
                            <h1 style="margin: 0; font-size: 24px; color: #0d47a1;">{{ $bookingDetails['web_title'] ?? 'King Express Bus' }}</h1>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 25px 30px;">
                        <h2 style="margin: 0 0 15px 0; font-size: 22px; color: #0d47a1; text-align: center;">Yêu cầu thanh toán vé / Payment Request</h2>

                        <p style="margin: 10px 0; font-size: 15px;">
                            Kính gửi Quý khách / Dear Customer <strong>{{ ($bookingDetails['customer_name'] ?? 'N/A') . ' ' . ($bookingDetails['customer_phone'] ?? 'N/A') }}</strong>,
                        </p>
                        <p style="margin: 10px 0; font-size: 15px;">
                            Chúng tôi đã xác nhận còn chỗ cho yêu cầu đặt vé của Quý khách. Vui lòng bấm nút bên dưới để thanh toán bằng mã QR SePay.
                            <br>We have confirmed seat availability for your booking request. Please use the button below to pay via SePay QR.
                        </p>

                        <table width="100%" border="0" cellpadding="10" cellspacing="0"
                               style="border-collapse: collapse; margin: 25px 0; font-size: 14px;">
                            <tr style="background-color:#f5faff;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; width: 170px; font-weight: bold; color: #555;">Mã đặt vé / Booking Code</td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #1e88e5;">#{{ $bookingDetails['booking_code'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">Hành trình / Route</td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ $bookingDetails['route_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr style="background-color:#f5faff;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">Ngày đi / Date</td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;">{{ $bookingDetails['departure_date'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">Giờ đi / Time</td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;">{{ $bookingDetails['start_time'] ?? 'N/A' }}</td>
                            </tr>
                            <tr style="background-color:#f5faff;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">Điểm đón / Pickup</td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ $bookingDetails['pickup_info'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">Tổng tiền / Total Price</td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #2e7d32;">{{ isset($bookingDetails['total_price']) ? number_format($bookingDetails['total_price']) . 'đ' : 'Liên hệ / Contact us' }}</td>
                            </tr>
                        </table>

                        <div style="text-align: center; margin: 28px 0;">
                            <a href="{{ $bookingDetails['payment_url'] ?? route('client.sepay.redirect', ['code' => $bookingDetails['booking_code'] ?? '']) }}"
                               style="display: inline-block; padding: 14px 24px; background-color: #0d47a1; color: #ffffff; border-radius: 6px; font-weight: bold; text-decoration: none;">
                                Thanh toán ngay / Pay Now
                            </a>
                        </div>

                        <div style="padding: 15px 20px; background-color: #fff8e1; border: 1px solid #ffe082; border-radius: 5px; margin: 20px 0;">
                            <p style="margin: 0; font-size: 14px; color: #795548;">
                                Vui lòng chỉ thanh toán qua link này để hệ thống tự động ghi nhận giao dịch.
                                <br>Please pay through this link so the system can automatically reconcile your payment.
                            </p>
                        </div>

                        <p style="font-weight: bold; margin-top: 25px; font-size: 15px;">
                            Cần hỗ trợ? Vui lòng liên hệ Hotline / Need support? Please contact Hotline:
                            <a href="tel:{{ $bookingDetails['web_phone'] ?? '' }}" style="color: #1e88e5; text-decoration: none;">
                                {{ $bookingDetails['web_phone'] ?? 'N/A' }}
                            </a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 20px; border-top: 1px solid #eeeeee; font-size: 12px; color: #777777; text-align: center;">
                        <p style="margin: 5px 0;">&copy; {{ date('Y') }} {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}. Mọi quyền được bảo lưu / All rights reserved.</p>
                        @if (!empty($bookingDetails['web_link']))
                            <p style="margin: 5px 0;">
                                <a href="{{ $bookingDetails['web_link'] }}" style="color: #1e88e5; text-decoration: none;">{{ $bookingDetails['web_link'] }}</a>
                            </p>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
