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

                        {{-- Bank Transfer Information - Conditional --}}
                        @if($bookingDetails['needs_bank_transfer_info'] && $bookingDetails['payment_status'] !== 'paid')
                            <h3 style="color: #B8860B; border-bottom: 1px solid #eeeeee; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; font-size: 16px;">
                                Thông tin chuyển khoản / <span
                                    style="font-style: italic;">Bank Transfer Information</span>
                            </h3>
                            <p style="font-size: 14px; margin-bottom: 15px;">
                                Vui lòng chuyển khoản số tiền <strong
                                    style="font-weight: bold; color: #D9534F;">{{ $bookingDetails['total_price'] ? number_format($bookingDetails['total_price']) . ' VNĐ' : '...' }}</strong>
                                với nội dung <strong
                                    style="font-weight: bold;">KEB {{ Illuminate\Support\Str::limit(explode(' ', $bookingDetails['customer_name'] ?? '')[count(explode(' ', $bookingDetails['customer_name'] ?? ''))-1], 10, '') }} {{ substr($bookingDetails['customer_phone'] ?? '', -4) }}</strong>
                                (Ví dụ: KEB An 1234) vào một trong các tài khoản sau để giữ vé:
                                <br>
                                <span style="font-style: italic; color: #555555;">Please transfer the amount of <strong
                                        style="font-weight: bold; color: #D9534F;">{{ $bookingDetails['total_price'] ? number_format($bookingDetails['total_price']) . ' VND' : '...' }}</strong> with the memo <strong
                                        style="font-weight: bold;">KEB {{ Illuminate\Support\Str::limit(explode(' ', $bookingDetails['customer_name'] ?? '')[count(explode(' ', $bookingDetails['customer_name'] ?? ''))-1], 10, '') }} {{ substr($bookingDetails['customer_phone'] ?? '', -4) }}</strong> (e.g., KEB An 1234) to one of the following accounts to secure your ticket:</span>
                            </p>
                            <table width="100%" border="0" cellpadding="8" cellspacing="0"
                                   style="border-collapse: collapse; margin-bottom: 20px; font-size: 14px;">
                                <tr style="background-color:#f9f9f9;">
                                    <td style="border: 1px solid #dddddd; padding: 8px; font-weight: bold; color: #555555;">
                                        Ngân hàng (Bank):
                                    </td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">Vietcombank</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid #dddddd; padding: 8px; font-weight: bold; color: #555555;">
                                        Số tài khoản (Account No.):
                                    </td>
                                    <td style="border: 1px solid #dddddd; padding: 8px; font-weight: bold; color: #B8860B;">
                                        0924300366
                                    </td>
                                </tr>
                                <tr style="background-color:#f9f9f9;">
                                    <td style="border: 1px solid #dddddd; padding: 8px; font-weight: bold; color: #555555;">
                                        Chủ tài khoản (Beneficiary):
                                    </td>
                                    <td style="border: 1px solid #dddddd; padding: 8px;">Nguyen Vu Ha My</td>
                                </tr>
                            </table>
                            <p style="font-size: 13px; color: #777777; margin-bottom: 15px;">
                                <i>Lưu ý: Vé của bạn sẽ được xác nhận sau khi chúng tôi nhận được thanh toán. Vui lòng
                                    hoàn tất chuyển khoản trong vòng 24 giờ.</i><br>
                                <span style="font-style: italic;">Note: Your ticket will be confirmed once we receive the payment. Please complete the transfer within 24 hours.</span>
                            </p>
                        @endif


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
