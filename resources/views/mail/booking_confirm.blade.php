<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Tiếp nhận yêu cầu đặt vé - {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}</title>
</head>
<body
    style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f7f6;">
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
                        <h2 style="margin: 0 0 15px 0; font-size: 22px; color: #1e88e5; text-align: center;">Tiếp nhận
                            yêu cầu đặt vé</h2>
                        <p style="margin: 10px 0; font-size: 15px;">
                            Kính gửi Quý khách <strong>{{ $bookingDetails['customer_name'] ?? 'N/A' }}</strong> (SĐT:
                            <strong>{{ $bookingDetails['customer_phone'] ?? 'N/A' }}</strong>),
                        </p>
                        <p style="margin: 10px 0; font-size: 15px;">
                            Chúng tôi đã tiếp nhận yêu cầu đặt vé của bạn. Nhân viên của chúng tôi sẽ sớm liên hệ qua số điện thoại để xác nhận chuyến đi cho bạn.
                        </p>
                        <p style="margin: 10px 0; font-size: 15px;">
                            Cảm ơn Quý khách đã tin tưởng và sử dụng dịch vụ của {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}.
                            Thông tin yêu cầu đặt vé hiện tại của Quý khách như sau:
                        </p>

                        <table width="100%" border="0" cellpadding="10" cellspacing="0"
                               style="border-collapse: collapse; margin: 25px 0; font-size: 14px;">
                            <tr style="background-color:#f5faff;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; width: 160px; font-weight: bold; color: #555;">
                                    Mã đặt vé (ID)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #1e88e5;">
                                    #{{ $bookingDetails['booking_code'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Hành trình (Route)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ $bookingDetails['route_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr style="background-color:#f5faff;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Ngày đi (Date)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;">{{ $bookingDetails['departure_date'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Giờ đi (Time)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold;">{{ $bookingDetails['start_time'] ?? 'N/A' }}</td>
                            </tr>
                            <tr style="background-color:#f5faff;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Điểm đón (Pickup)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ $bookingDetails['pickup_info'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Số điện thoại (Phone)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ $bookingDetails['customer_phone'] ?? 'N/A' }}</td>
                            </tr>
                            <tr style="background-color:#f5faff;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Email
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ $bookingDetails['customer_email'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">Số
                                    lượng (Quantity)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">{{ $bookingDetails['quantity'] ?? 'N/A' }}
                                    vé
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Tổng tiền (Price)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #2e7d32;">{{ isset($bookingDetails['total_price']) ? number_format($bookingDetails['total_price']) . 'đ' : 'Liên hệ' }}</td>
                            </tr>
                            <tr style="background-color:#f5faff;">
                                <td style="padding: 12px; border: 1px solid #e0e0e0; font-weight: bold; color: #555;">
                                    Thanh toán (Payment)
                                </td>
                                <td style="padding: 12px; border: 1px solid #e0e0e0;">
                                    @if ($bookingDetails['payment_method'] === 'cash_on_pickup')
                                        Thanh toán khi lên xe
                                    @elseif($bookingDetails['payment_method'] === 'online_banking')
                                        @if ($bookingDetails['payment_status'] === 'paid')
                                            Đã thanh toán chuyển khoản
                                        @else
                                            Chuyển khoản ngân hàng
                                        @endif
                                    @else
                                        {{ ucfirst($bookingDetails['payment_method'] ?? 'N/A') }}
                                    @endif
                                </td>
                            </tr>
                        </table>

                        @if ($bookingDetails['needs_bank_transfer_info'])
                            <div
                                style="padding: 20px; background-color: #e3f2fd; border: 1px solid #bbdefb; border-radius: 5px; margin: 20px 0;">
                                <h3 style="color: #0d47a1; margin-top: 0; font-size: 16px;">Thông tin thanh toán</h3>
                                <p style="font-size: 14px; margin-bottom: 15px;">
                                    Quý khách vui lòng chuyển khoản theo thông tin dưới đây với nội dung:
                                    <b style="color: #D9534F;">{{ $bookingDetails['customer_name'] }}
                                        - {{ $bookingDetails['customer_phone'] }}</b>
                                </p>
                                <table width="100%" border="0" cellpadding="8" cellspacing="0" style="font-size: 14px;">
                                    <tr>
                                        <td style="padding: 8px; font-weight: bold; color: #555; width: 150px;">Ngân
                                            hàng:
                                        </td>
                                        <td style="padding: 8px;">Vietcombank</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px; font-weight: bold; color: #555;">Số tài khoản:</td>
                                        <td style="padding: 8px; font-weight: bold; color: #1e88e5;">2924300366</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 8px; font-weight: bold; color: #555;">Chủ tài khoản:</td>
                                        <td style="padding: 8px;">Nguyen Vu Ha My</td>
                                    </tr>
                                </table>
                            </div>
                        @endif

                        <p style="font-weight: bold; margin-top: 25px; font-size: 15px;">
                            Cần hỗ trợ? Vui lòng liên hệ Hotline: <a href="tel:{{ $bookingDetails['web_phone'] ?? '' }}"
                                                                     style="color: #1e88e5; text-decoration: none;">{{ $bookingDetails['web_phone'] ?? 'N/A' }}</a>
                        </p>
                        <p style="margin-top: 20px; font-size: 15px;">Cảm ơn Quý khách đã lựa
                            chọn {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}!</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 20px; border-top: 1px solid #eeeeee; font-size: 12px; color: #777777; text-align: center;">
                        <p style="margin: 5px 0;">
                            © {{ date('Y') }} {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}. All rights
                            reserved.
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
