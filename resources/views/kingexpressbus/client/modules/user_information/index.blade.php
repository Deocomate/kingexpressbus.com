@extends("kingexpressbus.client.layouts.main")

@section("title", "Thông tin tài khoản")

@pushonce('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <style>
        /* Style cho tab active */
        [data-tabs-toggle] button[aria-selected="true"] {
            color: #ca8a04; /* text-yellow-600 */
            border-bottom-color: #f59e0b !important; /* border-yellow-500 */
        }

        [data-tabs-toggle] button:hover {
            border-bottom-color: #fcd34d; /* border-yellow-300 */
            color: #d97706; /* text-yellow-700 */
        }

        /* Style cho các trạng thái booking */
        .status-pending {
            background-color: #fef3c7;
            color: #ca8a04;
            border-color: #fde68a;
        }

        /* yellow */
        .status-confirmed {
            background-color: #dcfce7;
            color: #16a34a;
            border-color: #bbf7d0;
        }

        /* green */
        .status-completed {
            background-color: #e5e7eb;
            color: #4b5563;
            border-color: #d1d5db;
        }

        /* gray */
        .status-cancelled {
            background-color: #fee2e2;
            color: #dc2626;
            border-color: #fecaca;
        }

        /* red */
        .status-paid {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .status-unpaid {
            background-color: #fffbeb;
            color: #d97706;
        }

    </style>
@endpushonce

@pushonce('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
@endpushonce


@section("content")
    <div class="bg-gray-100 py-8 md:py-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">

                {{-- Page Heading --}}
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-yellow-800">Thông tin tài khoản</h1>
                    <p class="text-gray-600 mt-1">Quản lý thông tin cá nhân và xem lịch sử đặt vé của bạn.</p>
                </div>

                {{-- Hiển thị thông báo thành công/lỗi/info --}}
                @if(session('success'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                         x-transition
                         class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                         role="alert">
                        {{ session('success') }}
                        <button @click="show = false"
                                class="absolute top-0 bottom-0 right-0 px-4 py-3 text-green-700 hover:text-green-900">
                            &times;
                        </button>
                    </div>
                @endif
                @if(session('error'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 7000)" x-show="show"
                         x-transition
                         class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                         role="alert">
                        {{ session('error') }}
                        <button @click="show = false"
                                class="absolute top-0 bottom-0 right-0 px-4 py-3 text-red-700 hover:text-red-900">
                            &times;
                        </button>
                    </div>
                @endif
                @if(session('info'))
                    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                         x-transition
                         class="mb-6 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative"
                         role="alert">
                        {{ session('info') }}
                        <button @click="show = false"
                                class="absolute top-0 bottom-0 right-0 px-4 py-3 text-blue-700 hover:text-blue-900">
                            &times;
                        </button>
                    </div>
                @endif


                {{-- Sử dụng Tabs của Flowbite --}}
                <div class="mb-4 border-b border-gray-200 bg-white rounded-t-lg shadow">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="user-info-tabs"
                        data-tabs-toggle="#user-info-tab-content" role="tablist">
                        <li class="me-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-tab"
                                    data-tabs-target="#profile" type="button" role="tab" aria-controls="profile"
                                    aria-selected="true">Thông tin cá nhân
                            </button>
                        </li>
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                id="bookings-tab" data-tabs-target="#bookings" type="button" role="tab"
                                aria-controls="bookings" aria-selected="false">Lịch sử đặt vé
                            </button>
                        </li>
                        {{-- Có thể thêm tab Đổi mật khẩu ở đây --}}
                        {{-- <li class="me-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="password-tab" data-tabs-target="#password" type="button" role="tab" aria-controls="password" aria-selected="false">Đổi mật khẩu</button>
                        </li> --}}
                    </ul>
                </div>
                <div id="user-info-tab-content">
                    {{-- Tab 1: Thông tin cá nhân --}}
                    <div class="p-6 bg-white rounded-b-lg shadow border border-t-0 border-gray-200" id="profile"
                         role="tabpanel" aria-labelledby="profile-tab">
                        <h3 class="text-xl font-semibold text-gray-800 mb-5">Cập nhật thông tin</h3>
                        <form action="{{ route('client.user_information.update') }}" method="POST" class="space-y-4">
                            @csrf
                            {{-- @method('PUT') --}} {{-- Dùng POST cho đơn giản --}}

                            <div>
                                <label for="fullname" class="block mb-1 text-sm font-medium text-gray-700">Họ và tên
                                    <span class="text-red-500">*</span></label>
                                <input type="text" id="fullname" name="fullname"
                                       value="{{ old('fullname', $customer->fullname ?? '') }}" required
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5">
                                @error('fullname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="email" class="block mb-1 text-sm font-medium text-gray-500">Email (Không thể
                                    thay đổi)</label>
                                <input type="email" id="email" name="email" value="{{ $customer->email ?? '' }}"
                                       readonly disabled
                                       class="bg-gray-100 border border-gray-300 text-gray-500 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 cursor-not-allowed">
                            </div>
                            <div>
                                <label for="phone" class="block mb-1 text-sm font-medium text-gray-700">Số điện thoại
                                    <span class="text-red-500">*</span></label>
                                <input type="tel" id="phone" name="phone"
                                       value="{{ old('phone', $customer->phone ?? '') }}" required
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5">
                                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="address" class="block mb-1 text-sm font-medium text-gray-700">Địa
                                    chỉ</label>
                                <input type="text" id="address" name="address"
                                       value="{{ old('address', $customer->address ?? '') }}"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5">
                                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Nút cập nhật --}}
                            <div class="pt-2">
                                <button type="submit"
                                        class="inline-flex justify-center py-2 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Tab 2: Lịch sử đặt vé --}}
                    <div class="hidden p-0 md:p-6 bg-white rounded-b-lg shadow border border-t-0 border-gray-200"
                         id="bookings" role="tabpanel" aria-labelledby="bookings-tab">
                        <h3 class="text-xl font-semibold text-gray-800 mb-5 px-6 md:px-0 pt-6 md:pt-0">Lịch sử đặt
                            vé</h3>
                        @if($bookings->isEmpty())
                            <p class="text-gray-500 px-6 md:px-0 pb-6 md:pb-0">Bạn chưa có giao dịch đặt vé nào.</p>
                        @else
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                    <tr>
                                        <th scope="col" class="px-4 py-3">Mã vé</th>
                                        <th scope="col" class="px-4 py-3">Tuyến đường</th>
                                        <th scope="col" class="px-4 py-3">Ngày đi</th>
                                        <th scope="col" class="px-4 py-3">Giờ đi</th>
                                        <th scope="col" class="px-4 py-3">Ghế</th>
                                        <th scope="col" class="px-4 py-3">Tổng tiền</th>
                                        <th scope="col" class="px-4 py-3">Trạng thái</th>
                                        <th scope="col" class="px-4 py-3">Ngày đặt</th>
                                        {{-- <th scope="col" class="px-4 py-3">Hành động</th> --}}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($bookings as $booking)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <th scope="row"
                                                class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                                #{{ $booking->booking_id }}</th>
                                            <td class="px-4 py-3">{{ $booking->start_province_name }}
                                                ➟ {{ $booking->end_province_name }}</td>
                                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->start_at)->format('H:i') }}</td>
                                            <td class="px-4 py-3">
                                                <span class="font-semibold">{{ $booking->seats_list }}</span>
                                                ({{ count(json_decode($booking->seats, true) ?: []) }} ghế)
                                            </td>
                                            <td class="px-4 py-3 font-semibold">{{ number_format($booking->total_price) }}
                                                đ
                                            </td>
                                            <td class="px-4 py-3">
                                                {{-- Format trạng thái --}}
                                                @php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    switch ($booking->booking_status) {
                                                        case 'pending': $statusClass = 'status-pending'; $statusText = 'Chờ xử lý'; break;
                                                        case 'confirmed': $statusClass = 'status-confirmed'; $statusText = 'Đã xác nhận'; break;
                                                        case 'completed': $statusClass = 'status-completed'; $statusText = 'Đã hoàn thành'; break;
                                                        case 'cancelled': $statusClass = 'status-cancelled'; $statusText = 'Đã hủy'; break;
                                                        default: $statusClass = 'status-completed'; $statusText = ucfirst($booking->booking_status); break;
                                                    }
                                                    $paymentClass = $booking->payment_status == 'paid' ? 'status-paid' : 'status-unpaid';
                                                    $paymentText = $booking->payment_status == 'paid' ? 'Đã trả' : 'Chưa trả';
                                                @endphp
                                                <span
                                                    class="inline-block px-2 py-0.5 text-xs font-medium rounded border {{ $statusClass }}">{{ $statusText }}</span>
                                                <span
                                                    class="inline-block px-2 py-0.5 text-xs font-medium rounded {{ $paymentClass }}">{{ $paymentText }}</span>
                                            </td>
                                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->booking_created_at)->format('d/m/Y H:i') }}</td>
                                            {{-- <td class="px-4 py-3">
                                                <a href="#" class="font-medium text-yellow-600 hover:underline">Chi tiết</a>
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{-- Phân trang --}}
                            <div class="mt-4 px-4 md:px-0">
                                {{ $bookings->links() }} {{-- Sử dụng view phân trang mặc định của Laravel/Tailwind --}}
                            </div>
                        @endif
                    </div>
                    {{-- Tab 3: Đổi mật khẩu (Nếu có) --}}
                    {{-- <div class="hidden p-6 bg-white rounded-b-lg shadow border border-t-0 border-gray-200" id="password" role="tabpanel" aria-labelledby="password-tab">
                        <h3 class="text-xl font-semibold text-gray-800 mb-5">Đổi mật khẩu</h3>
                         Form đổi mật khẩu ở đây
                    </div> --}}
                </div>

            </div>
        </div>
    </div>
@endsection
