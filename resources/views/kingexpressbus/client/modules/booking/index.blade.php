@extends("kingexpressbus.client.layouts.main")

@section("title")
    Đặt vé xe {{ $busRouteData->bus_name }} ({{ $busRouteData->start_province_name }} - {{ $busRouteData->end_province_name }})
@endsection

@pushonce('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <style>
        .spinner {
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpushonce

@pushonce('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
@endpushonce

@section("content")
    <div class="bg-gray-50 py-8 md:py-12">
        <div class="container mx-auto px-4">
            {{-- Alpine.js component for managing form state --}}
            <div x-data="bookingForm({
                pricePerSeat: {{ $busRouteData->price ?? 0 }},
                oldNumberOfTickets: {{ old('number_of_tickets', 1) }},
                oldPickupOption: '{{ old('pickup_point', '') }}',
                oldHotelAddressDetail: '{{ old('hotel_address_detail', '') }}'
            })"
                 class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

                {{-- Cột chính: Thông tin chuyến và Form đặt vé --}}
                <div class="lg:col-span-2 bg-white rounded-lg shadow-lg border border-gray-200 p-6 md:p-8">
                    <div class="mb-6 text-center border-b border-gray-200 pb-4">
                        <h1 class="text-2xl md:text-3xl font-bold text-yellow-800">Thông tin đặt vé</h1>
                    </div>

                    {{-- Thông tin chuyến đi (Tóm tắt) --}}
                    <div class="mb-6 text-sm bg-yellow-50 p-4 rounded-md border border-yellow-200">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1">
                            <p><span class="font-medium text-gray-600">Tuyến:</span> <strong
                                    class="text-gray-800">{{ $busRouteData->start_province_name }}
                                    ➟ {{ $busRouteData->end_province_name }}</strong></p>
                            <p><span class="font-medium text-gray-600">Nhà xe:</span> <strong
                                    class="text-gray-800">{{ $busRouteData->bus_name }}
                                    ({{ $busRouteData->bus_type_name }})</strong></p>
                            <p><span class="font-medium text-gray-600">Ngày đi:</span> <strong
                                    class="text-gray-800">{{ $departure_date->format('d/m/Y') }}</strong></p>
                            <p><span class="font-medium text-gray-600">Giờ đi:</span> <strong
                                    class="text-gray-800">{{ \Carbon\Carbon::parse($busRouteData->start_at)->format('H:i') }}</strong>
                            </p>
                            <p class="sm:col-span-2"><span class="font-medium text-gray-600">Giá vé:</span> <strong
                                    class="text-yellow-600" x-text="formatCurrency(pricePerSeat)"></strong> /vé</p>
                        </div>
                    </div>

                    {{-- Form đặt vé --}}
                    <form x-ref="bookingFormElement"
                          @submit="isSubmitting = true;" {{-- Set submitting state on form submit --}}
                          action="{{ route('client.booking', ['bus_route_slug' => $busRouteData->bus_route_slug]) }}"
                          method="POST"
                          class="mt-6"
                    >
                        @csrf
                        {{-- Thông tin khách hàng --}}
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Thông tin liên hệ</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="fullname" class="block mb-1 text-sm font-medium text-gray-700">Họ và tên
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" id="fullname" name="fullname"
                                           value="{{ old('fullname', $customer->fullname ?? '') }}" required
                                           class="bg-gray-50 border {{ $errors->has('fullname') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5"
                                           placeholder="Nguyễn Văn A">
                                    @error('fullname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="phone" class="block mb-1 text-sm font-medium text-gray-700">Số điện
                                        thoại <span class="text-red-500">*</span></label>
                                    <input type="tel" id="phone" name="phone"
                                           value="{{ old('phone', $customer->phone ?? '') }}" required
                                           class="bg-gray-50 border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5"
                                           placeholder="09xxxxxxxx">
                                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" id="email" name="email"
                                           value="{{ old('email', $customer->email ?? '') }}" required
                                           {{ $customer ? 'readonly' : '' }} class="bg-gray-50 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 {{ $customer ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                           placeholder="email@example.com">
                                    @if($customer)
                                        <p class="text-xs text-gray-500 mt-1">Email không thể thay đổi khi đã đăng
                                            nhập.</p>
                                    @endif
                                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="address" class="block mb-1 text-sm font-medium text-gray-700">Địa chỉ
                                        (Tùy chọn)</label>
                                    <input type="text" id="address" name="address"
                                           value="{{ old('address', $customer->address ?? '') }}"
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5"
                                           placeholder="Số nhà, đường, phường/xã...">
                                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Số lượng vé và Điểm đón --}}
                        <div class="mb-6 border-t border-gray-200 pt-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Chi tiết đặt vé</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="number_of_tickets" class="block mb-1 text-sm font-medium text-gray-700">Số
                                        lượng vé <span class="text-red-500">*</span></label>
                                    <input type="number" id="number_of_tickets" name="number_of_tickets"
                                           x-model.number="numberOfTickets" min="1" required
                                           class="bg-gray-50 border {{ $errors->has('number_of_tickets') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5"
                                           placeholder="Nhập số lượng vé">
                                    @error('number_of_tickets') <p
                                        class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="pickup_point" class="block mb-1 text-sm font-medium text-gray-700">Điểm
                                        đón <span class="text-red-500">*</span></label>
                                    <select id="pickup_point" name="pickup_point" x-model="pickupOption"
                                            @change="handlePickupChange()" required
                                            class="bg-gray-50 border {{ $errors->has('pickup_point') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5">
                                        <option value="">-- Chọn điểm đón --</option>
                                        @foreach($stops as $stop)
                                            <option
                                                value="stop_id_{{ $stop->stop_id }}_{{ $stop->display_name }}">{{ $stop->display_name }}
                                                ({{ \Carbon\Carbon::parse($stop->stop_at ?? '')->format('H:i') }})
                                            </option>
                                        @endforeach
                                        <option value="hotel_old_quarter">Đón tại khách sạn trong Phố Cổ</option>
                                    </select>
                                    @error('pickup_point') <p
                                        class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div x-show="pickupOption === 'hotel_old_quarter'" class="mt-4" x-transition>
                                    <label for="hotel_address_detail"
                                           class="block mb-1 text-sm font-medium text-gray-700">Địa chỉ khách sạn <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" id="hotel_address_detail" name="hotel_address_detail"
                                           x-model="hotelAddressDetail" :required="pickupOption === 'hotel_old_quarter'"
                                           class="bg-gray-50 border {{ $errors->has('hotel_address_detail') ? 'border-red-500' : 'border-gray-300' }} text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5"
                                           placeholder="Nhập tên và địa chỉ khách sạn">
                                    @error('hotel_address_detail') <p
                                        class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Tổng tiền (Alpine managed) --}}
                        <div class="mt-6 mb-6 border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 text-lg font-medium">Tổng tiền:</span>
                                <span class="text-2xl font-bold text-yellow-600"
                                      x-text="formatCurrency(totalPrice)">0đ</span>
                            </div>
                        </div>

                        {{-- Phương thức thanh toán --}}
                        <div class="mb-6 border-t border-gray-200 pt-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Phương thức thanh toán</h2>
                            <fieldset x-data="{ paymentMethod: '{{ old('payment_method', 'offline') }}' }">
                                <legend class="sr-only">Chọn phương thức thanh toán</legend>
                                <div class="space-y-3">
                                    <div
                                        class="flex items-center ps-4 border border-gray-200 rounded hover:bg-gray-50 has-[:checked]:border-yellow-400 has-[:checked]:ring-1 has-[:checked]:ring-yellow-400">
                                        <input id="payment_offline" type="radio" value="offline" name="payment_method"
                                               x-model="paymentMethod"
                                               required
                                               {{ old('payment_method', 'offline') == 'offline' ? 'checked' : '' }} class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 focus:ring-yellow-500 focus:ring-2">
                                        <label for="payment_offline"
                                               class="w-full py-3 ms-2 text-sm font-medium text-gray-900 cursor-pointer">
                                            Thanh toán tiền mặt (Tại VP/Lên xe) <p class="text-xs text-gray-500 mt-0.5">
                                                Giữ
                                                chỗ và thanh toán trực tiếp.</p></label>
                                    </div>
                                    {{-- Bank Transfer Option --}}
                                    <div
                                        class="flex items-center ps-4 border border-gray-200 rounded hover:bg-gray-50 has-[:checked]:border-yellow-400 has-[:checked]:ring-1 has-[:checked]:ring-yellow-400">
                                        <input id="payment_online" type="radio" value="online" name="payment_method"
                                               x-model="paymentMethod"
                                               {{ old('payment_method') == 'online' ? 'checked' : '' }}
                                               required
                                               class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 focus:ring-yellow-500 focus:ring-2">
                                        <label for="payment_online"
                                               class="w-full py-3 ms-2 text-sm font-medium text-gray-900 cursor-pointer">
                                            Chuyển khoản ngân hàng (Giữ vé sau khi chuyển khoản)
                                            <span class="text-xs text-gray-500 mt-0.5 block">Vui lòng chuyển khoản theo thông tin bên dưới để hoàn tất đặt vé.</span>
                                        </label>
                                    </div>
                                    {{-- Bank details shown when "online" payment is selected --}}
                                    <div x-show="paymentMethod === 'online'" x-transition
                                         class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-md text-sm text-blue-700">
                                        <p class="font-semibold">Thông tin chuyển khoản:</p>
                                        <p><strong>Ngân hàng:</strong> Vietcombank</p>
                                        <p><strong>Số tài khoản:</strong> 0924300366</p>
                                        <p><strong>Chủ tài khoản:</strong> Nguyen Vu Ha My</p>
                                        <p><strong>Số tiền:</strong> <span x-text="formatCurrency(totalPrice)"></span>
                                        </p>
                                        <p><strong>Nội dung chuyển khoản:</strong> <span class="font-semibold">KEB <span
                                                    x-text="document.getElementById('fullname')?.value.trim().split(' ').pop() || 'TEN'"></span> <span
                                                    x-text="document.getElementById('phone')?.value.slice(-4) || 'SDT'"></span></span>
                                            (Ví dụ: KEB An 1234)</p>
                                        <p class="mt-1 text-xs text-blue-600">Quý khách vui lòng ghi đúng nội dung
                                            chuyển khoản để việc xác nhận được nhanh chóng.</p>
                                    </div>
                                </div>
                                @error('payment_method') <p
                                    class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </fieldset>
                        </div>

                        {{-- Nút Đặt vé --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <button type="submit"
                                    :disabled="numberOfTickets < 1 || isSubmitting"
                                    class="w-full text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-bold rounded-lg text-lg px-5 py-3 text-center disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 flex items-center justify-center">
                                <template x-if="isSubmitting">
                                    <div class="spinner mr-2"></div>
                                </template>
                                <span
                                    x-text="isSubmitting ? 'Đang xử lý...' : `Xác nhận đặt ${numberOfTickets >= 1 ? numberOfTickets : 0} vé`"></span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Cột phải: Chính sách và thông tin --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-3">Chính sách & Quy định</h3>
                        <div class="prose prose-sm max-w-none text-gray-600 text-xs leading-relaxed">
                            {!! $webInfo->policy ?? '<p>Thông tin chính sách đặt vé, hoàn hủy vé sẽ được hiển thị tại đây.</p>' !!}
                        </div>

                        @if($webInfo->address || $webInfo->phone || $webInfo->email)
                            <h3 class="text-lg font-semibold text-yellow-800 mt-6 mb-3">Địa chỉ thanh toán trực
                                tiếp</h3>
                            <div class="text-xs text-gray-600 space-y-1">
                                @if($webInfo->address)
                                    <p><strong>Địa chỉ:</strong> {{ $webInfo->address }}</p>
                                @endif
                                @if($webInfo->phone)
                                    <p><strong>Điện thoại:</strong> {{ $webInfo->phone }}</p>
                                @endif
                                @if($webInfo->hotline && $webInfo->hotline !== $webInfo->phone)
                                    <p><strong>Hotline:</strong> {{ $webInfo->hotline }}</p>
                                @endif
                                @if($webInfo->email)
                                    <p><strong>Email:</strong> {{ $webInfo->email }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bookingForm(config) {
            return {
                pricePerSeat: parseFloat(config.pricePerSeat) || 0,
                numberOfTickets: parseInt(config.oldNumberOfTickets) || 1,
                pickupOption: config.oldPickupOption || '',
                hotelAddressDetail: config.oldHotelAddressDetail || '',
                isSubmitting: false,
                init() {
                    if ({{ old('number_of_tickets') ? 'true' : 'false' }}) {
                        this.numberOfTickets = parseInt('{{ old('number_of_tickets') }}', 10);
                    }
                    // Đảm bảo pickupOption được khôi phục chính xác
                    this.pickupOption = '{{ old('pickup_point', '') }}';
                    // Nếu pickupOption là hotel_old_quarter, khôi phục hotelAddressDetail
                    if (this.pickupOption === 'hotel_old_quarter') {
                        this.hotelAddressDetail = '{{ old('hotel_address_detail', '') }}';
                    }


                    this.$watch('numberOfTickets', (value) => {
                        if (!Number.isInteger(value) || value < 1) {
                            this.numberOfTickets = 1;
                        }
                    });
                },
                handlePickupChange() {
                    if (this.pickupOption !== 'hotel_old_quarter') {
                        this.hotelAddressDetail = '';
                    }
                },
                get totalPrice() {
                    const numTickets = parseInt(this.numberOfTickets);
                    if (isNaN(numTickets) || numTickets < 1) {
                        return 0;
                    }
                    return numTickets * this.pricePerSeat;
                },
                formatCurrency(value) {
                    if (isNaN(value)) return '0đ';
                    return new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND',
                        minimumFractionDigits: 0
                    }).format(value);
                }
            }
        }
    </script>
@endsection
