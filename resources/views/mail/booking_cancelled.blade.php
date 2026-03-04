<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Thông báo hủy vé - {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f7f6;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f4f7f6;">
    <tr>
        <td align="center">
            <table width="600" border="0" cellpadding="0" cellspacing="0"
                   style="max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e0e0e0;">

                {{-- Header with logo --}}
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

                {{-- Body content --}}
                <tr>
                    <td style="padding: 25px 30px;">
                        {{-- Title with warning icon --}}
                        <h2 style="margin: 0 0 15px 0; font-size: 22px; color: #dc3545; text-align: center;">
                            ⚠️ Thông báo hủy vé
                        </h2>

                        <p style="margin: 10px 0; font-size: 15px;">
                            Kính gửi Quý khách <strong>{{ $bookingDetails['customer_name'] ?? 'N/A' }}</strong>,
                        </p>

                        <p style="margin: 10px 0; font-size: 15px;">
                            Chúng tôi rất tiếc phải thông báo rằng vé xe của Quý khách đã bị <strong style="color: #dc3545;">HỦY</strong> với thông tin chi tiết như sau:
                        </p>

                        {{-- Cancellation reason box --}}
                        <div style="padding: 15px 20px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 5px; margin: 20px 0;">
                            <p style="margin: 0; font-size: 14px; font-weight: bold; color: #856404;">
                                📋 Lý do hủy vé:
                            </p>
                            <p style="margin: 8px 0 0 0; font-size: 14px; color: #856404;">
                                {{ $bookingDetails['cancel_reason'] ?? 'Không có lý do cụ thể' }}
                            </p>
                        </div>

                        {{-- Booking details table --}}
                        <table width="100%" border="0" cellpadding="10" cellspacing="0"
                               style="border-collapse: collapse; margin: 25px 0; font-size: 14px;">
                            <tr style="background-color:#fff5f5;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; width: 160px; font-weight: bold; color: #555;">
                                    Mã đặt vé (ID)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #dc3545;">
                                    #{{ $bookingDetails['booking_code'] ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Hành trình (Route)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">
                                    {{ $bookingDetails['route_name'] ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr style="background-color:#fff5f5;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Ngày đi (Date)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;">
                                    {{ $bookingDetails['departure_date'] ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Giờ đi (Time)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;">
                                    {{ $bookingDetails['start_time'] ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr style="background-color:#fff5f5;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Điểm đón (Pickup)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">
                                    {{ $bookingDetails['pickup_info'] ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Số lượng (Quantity)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">
                                    {{ $bookingDetails['quantity'] ?? 'N/A' }} vé
                                </td>
                            </tr>
                            <tr style="background-color:#fff5f5;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Email
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">
                                    {{ $bookingDetails['customer_email'] ?? 'N/A' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Tổng tiền (Price)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    {{ isset($bookingDetails['total_price']) ? number_format($bookingDetails['total_price']) . 'đ' : 'Liên hệ' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Trạng thái (Status)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #dc3545;">
                                    ĐÃ HỦY
                                </td>
                            </tr>
                        </table>

                        {{-- Refund notice --}}
                        @if(($bookingDetails['payment_status'] ?? '') === 'paid')
                            <div style="padding: 15px 20px; background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;">
                                <p style="margin: 0; font-size: 14px; font-weight: bold; color: #155724;">
                                    💰 Thông tin hoàn tiền:
                                </p>
                                <p style="margin: 8px 0 0 0; font-size: 14px; color: #155724;">
                                    Do vé đã được thanh toán, Quý khách sẽ được hoàn tiền trong vòng 3-5 ngày làm việc.
                                    Vui lòng liên hệ hotline để được hỗ trợ chi tiết.
                                </p>
                            </div>
                        @endif

                        {{-- Apology and alternative suggestion --}}
                        <div style="padding: 15px 20px; background-color: #e3f2fd; border: 1px solid #bbdefb; border-radius: 5px; margin: 20px 0;">
                            <p style="margin: 0; font-size: 14px; color: #0d47a1;">
                                Chúng tôi thành thật xin lỗi vì sự bất tiện này. Quý khách có thể đặt lại vé cho chuyến khác hoặc ngày khác trên website của chúng tôi.
                            </p>
                            @if(!empty($bookingDetails['web_link']))
                                <p style="margin: 10px 0 0 0; text-align: center;">
                                    <a href="{{ $bookingDetails['web_link'] }}"
                                       style="display: inline-block; padding: 10px 25px; background-color: #1e88e5; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                        Đặt vé mới
                                    </a>
                                </p>
                            @endif
                        </div>

                        <p style="font-weight: bold; margin-top: 25px; font-size: 15px;">
                            Cần hỗ trợ? Vui lòng liên hệ Hotline:
                            <a href="tel:{{ $bookingDetails['web_phone'] ?? '' }}"
                               style="color: #1e88e5; text-decoration: none;">
                                {{ $bookingDetails['web_phone'] ?? 'N/A' }}
                            </a>
                        </p>
                        <p style="margin-top: 20px; font-size: 15px;">
                            Cảm ơn Quý khách đã thông cảm và tiếp tục ủng hộ {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}!
                        </p>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding: 20px; border-top: 1px solid #eeeeee; font-size: 12px; color: #777777; text-align: center;">
                        <p style="margin: 5px 0;">
                            © {{ date('Y') }} {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}. All rights reserved.
                        </p>
                        @if (!empty($bookingDetails['web_link']))
                            <p style="margin: 5px 0;">
                                <a href="{{ $bookingDetails['web_link'] }}"
                                   style="color: #1e88e5; text-decoration: none;">{{ $bookingDetails['web_link'] }}</a>
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
