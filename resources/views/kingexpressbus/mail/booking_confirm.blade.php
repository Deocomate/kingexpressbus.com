<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Xác nhận đặt vé - {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}</title>
    {{-- Bỏ khối <style> đi --}}
</head>
<body
    style="font-family: Arial, Helvetica, sans-serif; line-height: 1.6; color: #333333; margin: 0; padding: 0; background-color: #f4f4f4;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4;">
    <tr>
        <td align="center">
            {{-- Container chính --}}
            <table width="600" border="0" cellpadding="0" cellspacing="0"
                   style="max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #dddddd; border-radius: 5px; background-color: #ffffff;">
                {{-- Header --}}
                <tr>
                    <td align="center"
                        style="margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eeeeee;">
                        <h2 style="margin: 5px 0 0 0; font-size: 20px; color: #333333;">Xác nhận đặt vé thành công</h2>
                        <p style="margin: 5px 0 0 0; font-style: italic; color: #555555; font-size: 14px;">Booking
                            Confirmation Successful</p>
                    </td>
                </tr>

                {{-- Content --}}
                <tr>
                    <td style="padding: 10px 0;">
                        <p style="margin: 10px 0; font-size: 14px;"><strong style="font-weight: bold;">Kính gửi Quý
                                khách {{ $bookingDetails['customer_name'] ?? '' }},</strong><br>
                            <span style="font-style: italic; color: #555555;">Dear Mr/Ms {{ $bookingDetails['customer_name'] ?? '' }},</span>
                        </p>
                        <p style="margin: 10px 0; font-size: 14px;">
                            King Express Bus xin chân thành cảm ơn Quý khách đã tin tưởng và sử dụng dịch vụ của chúng
                            tôi. Chúng tôi xác nhận thông tin đặt vé của Quý khách như sau:<br>
                            <span style="font-style: italic; color: #555555;">King Express Bus would like to thank you for trusting and using our services. We confirm your booking information as follows:</span>
                        </p>

                        <h3 style="color: #B8860B; border-bottom: 1px solid #eeeeee; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; font-size: 16px;">
                            Chi tiết đặt vé / <span style="font-style: italic;">Booking Details</span></h3>

                        {{-- Bảng chi tiết đặt vé --}}
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
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left;">
                                    {{ $bookingDetails['bus_name'] ?? 'N/A' }}
                                    ({{ $bookingDetails['bus_type_name'] ?? 'N/A' }})
                                    {{-- Bỏ link xem chi tiết xe trong mail cho đơn giản --}}
                                </td>
                            </tr>
                            <tr>
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Số ghế (Seats)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left;">
                                    @if(!empty($bookingDetails['seats']) && is_array($bookingDetails['seats']))
                                        @foreach($bookingDetails['seats'] as $seat)
                                            {{-- Style cho từng ghế --}}
                                            <span
                                                style="display: inline-block; background-color: #f59e0b; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px; margin-bottom: 5px; font-weight: bold;">{{ $seat }}</span>
                                        @endforeach
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr style="background-color:#f9f9f9">
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Tổng tiền (Price)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left; font-weight: bold; color: #333333;">{{ $bookingDetails['total_price'] ? number_format($bookingDetails['total_price']) . 'đ' : 'Liên hệ' }}</td>
                            </tr>
                            <tr>
                                <th style="border: 1px solid #dddddd; padding: 10px; text-align: left; width: 150px; font-weight: bold; color: #555555;">
                                    Thanh toán (Payment)
                                </th>
                                <td style="border: 1px solid #dddddd; padding: 10px; text-align: left;">
                                    @if($bookingDetails['payment_method'] === 'offline')
                                        Thanh toán sau (Tại văn phòng/lên xe) / <span
                                            style="font-style: italic; color: #555555;">Pay later (At office/on board)</span>
                                    @elseif($bookingDetails['payment_method'] === 'online')
                                        {{-- Cập nhật trạng thái nếu đã thanh toán online thành công --}}
                                        @if($bookingDetails['payment_status'] === 'paid')
                                            Đã thanh toán trực tuyến / <span
                                                style="font-style: italic; color: #555555;">Paid Online</span>
                                        @else
                                            Thanh toán trực tuyến (Chờ xử lý) / <span
                                                style="font-style: italic; color: #555555;">Online Payment (Pending)</span>
                                        @endif
                                    @else
                                        {{ ucfirst($bookingDetails['payment_method'] ?? 'N/A') }}
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>

                        <h3 style="color: #B8860B; border-bottom: 1px solid #eeeeee; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; font-size: 16px;">
                            Lưu ý / <span style="font-style: italic;">Notes</span></h3>
                        <ul style="font-size: 14px; list-style-type: disc; padding-left: 20px; margin: 0 0 15px 0;">
                            <li style="margin-bottom: 5px;">Vui lòng có mặt tại điểm đón <strong
                                    style="font-weight: bold;">trước 30 phút</strong> so với giờ khởi hành.
                            </li>
                            <li style="margin-bottom: 5px; font-style: italic; color: #555555;">Please be present at the
                                pick-up point <strong style="font-weight: bold;">30 minutes prior</strong> to departure
                                time.
                            </li>
                            <li style="margin-bottom: 5px;">Xuất trình email này hoặc tin nhắn xác nhận khi lên xe.</li>
                            <li style="margin-bottom: 5px; font-style: italic; color: #555555;">Present this email or
                                confirmation message when boarding.
                            </li>
                            @if($bookingDetails['payment_method'] === 'offline')
                                <li style="margin-bottom: 5px;">Vui lòng thanh toán vé trước giờ khởi hành tại văn phòng
                                    hoặc trực tiếp cho phụ xe.
                                </li>
                                <li style="margin-bottom: 5px; font-style: italic; color: #555555;">Please pay for your
                                    ticket before departure time at our office or directly to the bus staff.
                                </li>
                            @endif
                        </ul>

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

                {{-- Footer --}}
                <tr>
                    <td style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #eeeeee; font-size: 12px; color: #777777; text-align: center;">
                        <p style="margin: 5px 0;">
                            &copy; {{ date('Y') }} {{ $bookingDetails['web_title'] ?? 'King Express Bus' }}. All rights
                            reserved.</p>
                        @if(!empty($bookingDetails['web_link']))
                            <p style="margin: 5px 0;"><a href="{{ $bookingDetails['web_link'] }}"
                                                         style="color: #B8860B; text-decoration: none;">{{ $bookingDetails['web_link'] }}</a>
                            </p>
                        @endif
                    </td>
                </tr>
            </table> {{-- Đóng container chính --}}
        </td>
    </tr>
</table> {{-- Đóng table bao ngoài --}}
</body>
</html>
