<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Xác nhận đặt vé - {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}</title>
</head>
<body
    style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4;">
    <tr>
        <td align="center">
            <table width="600" border="0" cellpadding="0" cellspacing="0"
                   style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #dddddd; border-radius: 5px; background-color: #ffffff;">
                <tr>
                    <td align="center"
                        style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eeeeee;">
                        @if(!empty($bookingDetails['web_logo']))
                            <img src="{{ $bookingDetails['web_logo'] }}"
                                 alt="{{ $bookingDetails['web_title'] ?? 'King Express Bus' }}"
                                 style="max-height: 70px; margin-bottom: 10px;">
                        @endif
                        <h2 style="margin: 5px 0 0 0; font-size: 20px; color: #333333;">Xác nhận yêu cầu đặt vé</h2>
                        <p style="margin: 5px 0 0 0; font-style: italic; color: #555555; font-size: 14px;">Booking
                            Request Confirmation</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;">
                        <p style="margin: 10px 0; font-size: 14px;"><strong style="font-weight: bold;">Kính gửi Quý
                                khách {{ $bookingDetails['customer_name'] ?? '' }},</strong><br>
                            <span style="font-style: italic; color: #555555;">Dear Mr/Ms {{ $bookingDetails['customer_name'] ?? '' }},</span>
                        </p>
                        <p style="margin: 10px 0; font-size: 14px;">
                            King Express Bus xin chân thành cảm ơn Quý khách đã tin tưởng và sử dụng dịch vụ của chúng
                            tôi. Chúng tôi xác nhận thông tin yêu cầu đặt vé của Quý khách như sau:<br>
                            <span style="font-style: italic; color: #555555;">King Express Bus would like to thank you for trusting and using our services. We confirm your booking request information as follows:</span>
                        </p>

                        <h3 style="color: #B8860B; border-bottom: 1px solid #eeeeee; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; font-size: 16px;">
                            Chi tiết đặt vé / <span style="font-style: italic;">Booking Details</span></h3>

                        <table width="100%" border="0" cellpadding="10" cellspacing="0"
                               style="border-collapse: collapse; margin: 20px 0; font-size: 14px;">
                            <tbody>
                            <tr>
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; background-color: #f9f9f9; width: 150px; font-weight: bold; color: #555555;">
                                    Mã đặt vé (ID)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left; font-weight: bold; color: #333333;">
                                    #{{ $bookingDetails['booking_id'] ?? 'N/A' }}</td>
                            </tr>
                            <tr style="background-color:#f9f9f9">
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Họ tên (Name)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left;">{{ $bookingDetails['customer_name'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Email
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left;"><a
                                        href="mailto:{{ $bookingDetails['customer_email'] ?? '' }}"
                                        style="color: #B8860B; text-decoration: none;">{{ $bookingDetails['customer_email'] ?? 'N/A' }}</a>
                                </td>
                            </tr>
                            <tr style="background-color:#f9f9f9">
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Điện thoại (Phone)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left;">{{ $bookingDetails['customer_phone'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Tuyến đường (Route)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left;">{{ $bookingDetails['start_province'] ?? 'N/A' }}
                                    ➟ {{ $bookingDetails['end_province'] ?? 'N/A' }}</td>
                            </tr>
                            <tr style="background-color:#f9f9f9">
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Ngày đi (Date)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left; font-weight: bold; color: #333333;">{{ $bookingDetails['departure_date'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Giờ đi (Time)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left; font-weight: bold; color: #333333;">{{ $bookingDetails['start_time'] ?? 'N/A' }}</td>
                            </tr>
                            <tr style="background-color:#f9f9f9">
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Loại xe (Bus)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left;">{{ $bookingDetails['bus_name'] ?? 'N/A' }}
                                    ({{ $bookingDetails['bus_type_name'] ?? 'N/A' }})
                                </td>
                            </tr>
                            <tr>
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Số lượng vé (Quantity)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left; font-weight: bold; color: #333333;">{{ $bookingDetails['quantity'] ?? 'N/A' }}</td>
                            </tr>
                            <tr style="background-color:#f9f9f9">
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Điểm đón (Pickup Point)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left;">{{ $bookingDetails['pickup_info'] ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Tổng tiền (Price)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left; font-weight: bold; color: #333333;">{{ $bookingDetails['total_price'] ? number_format($bookingDetails['total_price']) . 'đ' : 'Liên hệ' }}</td>
                            </tr>
                            <tr style="background-color:#f9f9f9">
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Thanh toán (Payment)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left;">
                                    @if($bookingDetails['payment_method'] === 'offline')
                                        Thanh toán sau (Tại văn phòng/lên xe) / <span
                                            style="font-style: italic; color: #555555;">Pay later (At office/on board)</span>
                                    @elseif($bookingDetails['payment_method'] === 'online')
                                        @if($bookingDetails['payment_status'] === 'paid')
                                            Đã thanh toán trực tuyến / <span
                                                style="font-style: italic; color: #555555;">Paid Online</span>
                                        @else
                                            Chuyển khoản ngân hàng (Chờ xác nhận) / <span
                                                style="font-style: italic; color: #555555;">Bank Transfer (Awaiting confirmation)</span>
                                        @endif
                                    @else
                                        {{ ucfirst($bookingDetails['payment_method'] ?? 'N/A') }}
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        {{-- Payment Information - Conditional --}}
                        @if($bookingDetails['needs_bank_transfer_info'] && $bookingDetails['payment_status'] !== 'paid')
                            <h3 style="color: #B8860B; border-bottom: 1px solid #eeeeee; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; font-size: 16px;">
                                Hình thức thanh toán / <span style="font-style: italic;">Payment Methods</span>
                            </h3>
                            <p style="font-size: 14px; margin-bottom: 15px;">
                                Khi đặt các dịch vụ tại King Express Bus, để giữ và đảm bảo dịch vụ quý khách vui lòng
                                thanh toán cho booking. Việc thanh toán thông thường được tiến hành bằng các cách sau:
                            </p>
                            <p style="font-size: 14px; margin-bottom: 10px; padding-left: 15px;">
                                <strong>1 – Thanh toán tiền mặt:</strong> Quý khách đến và thanh toán trực tiếp tại công
                                ty theo địa chỉ sau:<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;Địa chỉ: 19 Hàng Thiếc – Hoàn Kiếm – Hà Nội<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;Điện thoại: 02438281996<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;Di động: +84924300366
                            </p>
                            <p style="font-size: 14px; margin-bottom: 10px; padding-left: 15px;">
                                <strong>2 – Chuyển khoản qua ngân hàng:</strong> Quý khách chuyển khoản cho chúng tôi
                                theo thông tin tài khoản mà chúng tôi cung cấp dưới đây. Khi chuyển khoản quý khách lưu
                                ý ghi rõ thông tin trong nội dung chuyển khoản <b
                                    style="font-weight: bold; color: #D9534F;">(Tên + Số điện thoại khách hàng)</b> để
                                chúng tôi tiện theo dõi và chuyển ủy
                                nhiệm chi cho chúng tôi qua mail kingexpressbus@gmail.com.
                            </p>
                            <table width="100%" border="0" cellpadding="8" cellspacing="0"
                                   style="border-collapse: collapse; margin-bottom: 15px; margin-left: 30px; max-width: 540px; font-size: 14px;">
                                <tr style="background-color:#f9f9f9;">
                                    <td style="border: 1px solid #dddddd; padding: 8px; font-weight: bold; color: #555555; width: 170px;">
                                        Ngân hàng (Bank):
                                    </td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">Vietcombank</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; font-weight: bold; color: #555555;">
                                        Số tài khoản (Account No.):
                                    </td>
                                    <td style="border: 1px solid #dddddd; padding: 8px; font-weight: bold; color: #B8860B;">
                                        2924300366
                                    </td>
                                </tr>
                                <tr style="background-color:#f9f9f9;">
                                    <td style="border: 1px solid #dddddd; padding: 8px; font-weight: bold; color: #555555;">
                                        Chủ tài khoản (Beneficiary):
                                    </td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">Nguyen Vu Ha My</td>
                                </tr>
                            </table>
                            <p style="font-size: 13px; color: #777777; margin-bottom: 5px; padding-left: 15px;">
                                <i>Lưu ý: Quý khách vui lòng chỉ chuyển khoản vào các số tài khoản trên. Trong trường
                                    hợp quý khách chuyển khoản ngoài các số trên nếu có rủi ro nào khác, công ty không
                                    chịu trách nhiệm.</i>
                            </p>
                        @endif

                        <p style="font-weight: bold; margin-top: 20px; font-size: 14px;">
                            Nếu Quý khách có bất kỳ thắc mắc nào, vui lòng liên hệ Hotline/Zalo/WhatsApp: <a
                                href="tel:{{ $bookingDetails['web_phone'] ?? '+84924300366' }}"
                                style="color: #B8860B; text-decoration: none;">{{ $bookingDetails['web_phone'] ?? '+84924300366' }}</a>
                            hoặc Email: <a href="mailto:{{ $bookingDetails['web_email'] ?? '' }}"
                                           style="color: #B8860B; text-decoration: none;">{{ $bookingDetails['web_email'] ?? '' }}</a>.
                        </p>
                        <p style="font-style: italic; font-weight: bold; color: #555555; font-size: 14px;">
                            If you have any further questions, please contact us via Hotline/Zalo/WhatsApp: <a
                                href="tel:{{ $bookingDetails['web_phone'] ?? '+84924300366' }}"
                                style="color: #B8860B; text-decoration: none;">{{ $bookingDetails['web_phone'] ?? '+84924300366' }}</a>
                            or Email: <a href="mailto:{{ $bookingDetails['web_email'] ?? '' }}"
                                         style="color: #B8860B; text-decoration: none;">{{ $bookingDetails['web_email'] ?? '' }}</a>.
                        </p>
                        <p style="margin-top: 20px; font-size: 14px;">Cảm ơn Quý khách đã lựa chọn King Express Bus!</p>
                        <p style="font-style: italic; color: #555555; font-size: 14px;">Thank you for choosing King
                            Express Bus!</p>
                    </td>
                </tr>
                <tr>
                    <td style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eeeeee; font-size: 12px; color: #777777; text-align: center;">
                        <p style="margin: 5px 0;">
                            © {{ date('Y') }} {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}. All rights
                            reserved.</p>
                        @if(!empty($bookingDetails['web_link']))
                            <p style="margin: 5px 0;"><a href="{{ $bookingDetails['web_link'] }}"
                                                         style="color: #B8860B; text-decoration: none;">{{ $bookingDetails['web_link'] }}</a>
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
