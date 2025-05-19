@extends("kingexpressbus.client.layouts.main")

{{-- Section Title --}}
@section("title")
    Đặt vé xe {{ $busRouteData->bus_name }} ({{ $busRouteData->start_province_name }} - {{ $busRouteData->end_province_name }})
@endsection

{{-- Push CSS/JS cần thiết --}}
@pushonce('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    {{-- CSS tùy chỉnh cho sơ đồ ghế --}}
    <style>
        .seat-layout-container {
            width: 100%;
        }

        .seat-map-wrapper {
            overflow-x: auto;
            padding: 10px 0;
            text-align: center;
        }

        .seat-container {
            display: inline-grid;
            gap: 8px; /* Tăng khoảng cách để tạo lối đi rõ hơn */
            justify-items: center;
            /* grid-template-columns sẽ được đặt bằng Alpine :style */
        }

        .seat {
            width: 40px; /* Có thể tăng lại kích thước nếu muốn */
            height: 40px;
            border: 1px solid #ccc;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 10px;
            font-weight: bold;
            transition: all 0.2s ease-in-out;
            user-select: none;
            position: relative;
        }

        .seat.available {
            background-color: #fff;
            color: #4b5563;
            border-color: #d1d5db;
        }

        .seat.available:hover {
            background-color: #fef9c3;
            border-color: #facc15;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .seat.selected {
            background-color: #f59e0b;
            color: white;
            border-color: #d97706;
            transform: translateY(-2px);
            box-shadow: 0 0 0 3px rgba(245, 159, 11, 0.4);
        }

        .seat.booked {
            background-color: #e5e7eb;
            color: #9ca3af;
            border-color: #d1d5db;
            cursor: not-allowed;
            opacity: 0.7;
        }

        /* Bỏ class .seat.aisle vì không còn dùng */
        .upper-deck {
            border-top: 2px dashed #d1d5db;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
        }

        @media (max-width: 640px) {
            .seat {
                width: 36px;
                height: 36px;
                font-size: 9px;
                border-radius: 4px;
            }

            .seat-container {
                gap: 5px;
            }

            /* Giảm gap trên mobile */
        }

        /* Loading spinner */
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

        /* Tooltip styling */
        [x-tooltip] {
            position: absolute;
            z-index: 10;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            margin-bottom: 6px;
            padding: 4px 8px;
            background-color: #374151;
            color: white;
            font-size: 11px;
            font-weight: normal;
            border-radius: 4px;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }

        [x-tooltip].visible {
            opacity: 1;
        }
    </style>
@endpushonce

@pushonce('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
@endpushonce


@section("content")
    <div class="bg-gray-50 py-8 md:py-12">
        <div class="container mx-auto px-4">
            {{-- Bọc toàn bộ nội dung trong Alpine component --}}
            <div x-data="bookingForm({
                rows: {{ $busRouteData->seat_row_number ?? 0 }},
                cols: {{ $busRouteData->seat_column_number ?? 0 }},
                floors: {{ $busRouteData->floors ?? 1 }},
                bookedSeats: {{ json_encode($bookedSeats) }},
                pricePerSeat: {{ $busRouteData->start_price ?? 0 }}
            })"
                 class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start"
            >

                {{-- Cột 1+2: Thông tin chuyến và Sơ đồ ghế --}}
                <div class="lg:col-span-2 bg-white rounded-lg shadow-lg border border-gray-200 p-6 md:p-8">
                    {{-- Page Heading --}}
                    <div class="mb-6 text-center border-b border-gray-200 pb-4">
                        <h1 class="text-2xl md:text-3xl font-bold text-yellow-800">Chọn vị trí ghế</h1>
                    </div>

                    {{-- Thông tin chuyến đi (Tóm tắt) --}}
                    <div class="mb-6 text-sm bg-yellow-50 p-4 rounded-md border border-yellow-200">
                        <div class="grid grid-cols-2 gap-x-4 gap-y-1">
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
                        </div>
                    </div>

                    {{-- Sơ đồ ghế --}}
                    <div class="seat-layout-container">
                        <div class="seat-map-wrapper">
                            <div class="inline-block">
                                {{-- Tầng 1 --}}
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-center text-gray-600 mb-3">Tầng 1</p>
                                    <div class="seat-container"
                                         :style="`grid-template-columns: repeat(${cols}, auto);`">
                                        {{-- Chỉ render ghế thực tế --}}
                                        <template x-for="seat in generateSeats(1)" :key="seat.id">
                                            <div x-data="{ tooltipVisible: false }" @mouseenter="tooltipVisible = true"
                                                 @mouseleave="tooltipVisible = false" class="relative">
                                                <div x-text="seat.id"
                                                     @click="toggleSeat(seat.id)"
                                                     :class="getSeatClasses(seat)"
                                                     :aria-label="`Ghế ${seat.id} - ${getSeatStatusText(seat.status)}`"
                                                     role="button"
                                                     :tabindex="seat.status === 'booked' ? -1 : 0"
                                                >
                                                </div>
                                                {{-- Tooltip --}}
                                                <div x-show="tooltipVisible" x-tooltip
                                                     :class="{ 'visible': tooltipVisible }">
                                                    <template
                                                        x-if="seat.status === 'available' || seat.status === 'selected'">
                                                        <span>Ghế <span x-text="seat.id"></span> - <span
                                                                x-text="formatCurrency(pricePerSeat)"></span></span>
                                                    </template>
                                                    <template x-if="seat.status === 'booked'">
                                                        <span>Đã đặt</span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                {{-- Tầng 2 --}}
                                <template x-if="floors > 1">
                                    <div class="upper-deck">
                                        <p class="text-sm font-medium text-center text-gray-600 mb-3">Tầng 2</p>
                                        <div class="seat-container"
                                             :style="`grid-template-columns: repeat(${cols}, auto);`">
                                            <template x-for="seat in generateSeats(2)" :key="seat.id">
                                                <div x-data="{ tooltipVisible: false }"
                                                     @mouseenter="tooltipVisible = true"
                                                     @mouseleave="tooltipVisible = false" class="relative">
                                                    <div x-text="seat.id"
                                                         @click="toggleSeat(seat.id)"
                                                         :class="getSeatClasses(seat)"
                                                         :aria-label="`Ghế ${seat.id} - ${getSeatStatusText(seat.status)}`"
                                                         role="button"
                                                         :tabindex="seat.status === 'booked' ? -1 : 0"
                                                    >
                                                    </div>
                                                    {{-- Tooltip --}}
                                                    <div x-show="tooltipVisible" x-tooltip
                                                         :class="{ 'visible': tooltipVisible }">
                                                        <template
                                                            x-if="seat.status === 'available' || seat.status === 'selected'">
                                                            <span>Ghế <span x-text="seat.id"></span> - <span
                                                                    x-text="formatCurrency(pricePerSeat)"></span></span>
                                                        </template>
                                                        <template x-if="seat.status === 'booked'">
                                                            <span>Đã đặt</span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        {{-- Chú thích --}}
                        <div
                            class="flex flex-wrap justify-center gap-x-4 gap-y-1 text-xs mt-4 pt-3 border-t border-gray-200">
                            <div class="flex items-center">
                                <div class="seat available w-4 h-4 mr-1.5 !cursor-default"></div>
                                Ghế trống
                            </div>
                            <div class="flex items-center">
                                <div class="seat selected w-4 h-4 mr-1.5 !cursor-default"></div>
                                Đang chọn
                            </div>
                            <div class="flex items-center">
                                <div class="seat booked w-4 h-4 mr-1.5 !cursor-default"></div>
                                Đã đặt
                            </div>
                        </div>
                    </div>

                    {{-- Thông tin ghế đã chọn và tổng tiền --}}
                    <div class="mt-6 border-t border-gray-200 pt-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Ghế đã chọn (<span
                                x-text="selectedSeats.length">0</span>):</h4>
                        <div x-show="selectedSeats.length === 0" class="text-gray-500 text-sm italic">Vui lòng chọn ghế
                            trên sơ đồ
                        </div>
                        <div x-show="selectedSeats.length > 0" class="flex flex-wrap gap-2 mb-4 min-h-[36px]">
                            <template x-for="seatId in selectedSeats" :key="seatId">
                             <span
                                 class="bg-yellow-500 text-white text-sm font-bold px-3 py-1 rounded shadow-sm inline-flex items-center">
                                 <span x-text="seatId"></span>
                                 <button type="button" @click="toggleSeat(seatId); $event.stopPropagation();"
                                         class="ml-1.5 text-yellow-100 hover:text-white focus:outline-none"
                                         aria-label="Bỏ chọn ghế">
                                     <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path
                                             fill-rule="evenodd"
                                             d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                             clip-rule="evenodd"></path></svg>
                                 </button>
                             </span>
                            </template>
                        </div>
                        @error('seats') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('seats.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 text-lg font-medium">Tổng tiền:</span>
                                <span class="text-2xl font-bold text-yellow-600"
                                      x-text="formatCurrency(totalPrice)">0đ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <form x-ref="bookingFormElement"
                          @submit="isSubmitting = true; updateHiddenInputs();"
                          action="{{ route('client.booking', ['bus_route_slug' => $busRouteData->bus_route_slug]) }}"
                          method="POST"
                          class="bg-white rounded-lg shadow-lg border border-gray-200 p-6 md:p-8 sticky top-8"
                    >
                        @csrf
                        <div id="hidden-seats-container">
                            {{-- Input ẩn seats[] sẽ được Alpine thêm vào đây --}}
                            @if(old('seats'))
                                @foreach(old('seats') as $seat)
                                    <input type="hidden" name="seats[]" value="{{ $seat }}">
                                @endforeach
                            @endif
                        </div>

                        {{-- Thông tin khách hàng --}}
                        <div class="mb-6">
                            {{-- ... Input thông tin khách hàng giữ nguyên ... --}}
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Thông tin liên hệ</h2>
                            <div class="space-y-4">
                                <div>
                                    <label for="fullname" class="block mb-1 text-sm font-medium text-gray-700">Họ và tên
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" id="fullname" name="fullname"
                                           value="{{ old('fullname', $customer->fullname ?? '') }}" required
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5"
                                           placeholder="Nguyễn Văn A">
                                    @error('fullname') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="phone" class="block mb-1 text-sm font-medium text-gray-700">Số điện
                                        thoại <span class="text-red-500">*</span></label>
                                    <input type="tel" id="phone" name="phone"
                                           value="{{ old('phone', $customer->phone ?? '') }}" required
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5"
                                           placeholder="09xxxxxxxx">
                                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="email" class="block mb-1 text-sm font-medium text-gray-700">Email <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" id="email" name="email"
                                           value="{{ old('email', $customer->email ?? '') }}" required
                                           {{ $customer ? 'readonly' : '' }}
                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-yellow-500 focus:border-yellow-500 block w-full p-2.5 {{ $customer ? 'bg-gray-100 cursor-not-allowed' : '' }}"
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

                        {{-- Phương thức thanh toán --}}
                        <div class="mb-6 border-t border-gray-200 pt-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Phương thức thanh toán</h2>
                            <fieldset>
                                <legend class="sr-only">Chọn phương thức thanh toán</legend>
                                <div class="space-y-3">
                                    {{-- Thanh toán Offline --}}
                                    <div
                                        class="flex items-center ps-4 border border-gray-200 rounded hover:bg-gray-50 has-[:checked]:border-yellow-400 has-[:checked]:ring-1 has-[:checked]:ring-yellow-400">
                                        <input id="payment_offline" type="radio" value="offline" name="payment_method"
                                               required
                                               {{ old('payment_method', 'offline') == 'offline' ? 'checked' : '' }}
                                               class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 focus:ring-yellow-500 focus:ring-2">
                                        <label for="payment_offline"
                                               class="w-full py-3 ms-2 text-sm font-medium text-gray-900 cursor-pointer">
                                            Thanh toán sau (Tại VP/Lên xe)
                                            <p class="text-xs text-gray-500 mt-0.5">Giữ chỗ và thanh toán trực tiếp.</p>
                                        </label>
                                    </div>
                                    {{-- Thanh toán Online --}}
                                    <div
                                        class="flex items-center ps-4 border border-gray-200 rounded hover:bg-gray-50 has-[:checked]:border-yellow-400 has-[:checked]:ring-1 has-[:checked]:ring-yellow-400">
                                        {{-- *** Bỏ thuộc tính 'disabled' *** --}}
                                        <input id="payment_online" type="radio" value="online" name="payment_method"
                                               required disabled
                                               {{ old('payment_method') == 'online' ? 'checked' : '' }}
                                               class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 focus:ring-yellow-500 focus:ring-2">
                                        {{-- *** Sửa lại label *** --}}
                                        <label for="payment_online"
                                               class="w-full py-3 ms-2 text-sm font-medium text-gray-900 cursor-pointer"> {{-- Bỏ class màu xám và cursor-not-allowed --}}
                                            Thanh toán Online qua VNPAY {{-- Bỏ chữ (Sắp có) --}}
                                            <p class="text-xs text-gray-500 mt-0.5">Thanh toán ngay để xác nhận vé.</p>
                                        </label>
                                    </div>
                                </div>
                                @error('payment_method') <p
                                    class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </fieldset>
                        </div>

                        {{-- Nút Đặt vé --}}
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            {{-- ... Nút submit giữ nguyên ... --}}
                            <button type="submit"
                                    :disabled="selectedSeats.length === 0 || isSubmitting"
                                    class="w-full text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-bold rounded-lg text-lg px-5 py-3 text-center disabled:opacity-50 disabled:cursor-not-allowed transition duration-200 flex items-center justify-center"
                            >
                                <template x-if="isSubmitting">
                                    <div class="spinner mr-2"></div>
                                </template>
                                <span
                                    x-text="isSubmitting ? 'Đang xử lý...' : `Xác nhận đặt vé (${selectedSeats.length} ghế)`">
                     Xác nhận đặt vé (0 ghế)
                 </span>
                            </button>
                        </div>
                    </form>
                </div>

            </div> {{-- Đóng grid chính --}}
        </div>
    </div>

    {{-- Alpine.js Logic for Seat Selection (Đã sửa lỗi và tối ưu) --}}
    <script>
        function bookingForm(config) {
            return {
                // Config Data
                rows: parseInt(config.rows) || 0, // Đảm bảo là số nguyên
                cols: parseInt(config.cols) || 0, // Đảm bảo là số nguyên
                floors: parseInt(config.floors) || 1,
                bookedSeats: Array.isArray(config.bookedSeats) ? config.bookedSeats : [],
                pricePerSeat: parseFloat(config.pricePerSeat) || 0,

                // Alpine State
                selectedSeats: [],
                seats: [],
                isSubmitting: false,

                init() {
                    // Chỉ chạy nếu rows và cols hợp lệ
                    if (this.rows > 0 && this.cols > 0) {
                        this.seats = this.generateInitialSeats();
                        const oldSelectedSeats = {!! json_encode(old('seats', [])) !!};
                        this.$nextTick(() => {
                            if (Array.isArray(oldSelectedSeats) && oldSelectedSeats.length > 0) {
                                oldSelectedSeats.forEach(seatId => {
                                    const seat = this.seats.find(s => s.id === seatId);
                                    if (seat && seat.status === 'available') {
                                        this.toggleSeat(seatId, false);
                                    }
                                });
                            }
                            this.updateHiddenInputs();
                        });
                    } else {
                        console.error("Invalid rows or cols configuration:", config.rows, config.cols);
                    }
                },

                generateInitialSeats() {
                    let seatsArray = [];
                    const seatChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    if (this.rows === 0 || this.cols === 0) return seatsArray; // Trả về mảng rỗng nếu cấu hình không hợp lệ

                    for (let f = 1; f <= this.floors; f++) {
                        for (let r = 0; r < this.rows; r++) {
                            if (r >= seatChars.length) continue;
                            let rowChar = seatChars[r];
                            for (let c = 1; c <= this.cols; c++) {
                                // Tạo ID ghế thực tế
                                let seatId = `${f > 1 ? 'T' + f + '-' : ''}${rowChar}${c}`;
                                let status = 'available';

                                // Không còn logic đoán lối đi ở đây
                                // isAisle = false; // Bỏ biến isAisle

                                // Kiểm tra ghế đã đặt
                                if (this.bookedSeats.includes(seatId)) {
                                    status = 'booked';
                                }

                                // Thêm ghế vào mảng
                                seatsArray.push({
                                    id: seatId,
                                    floor: f,
                                    row: r,
                                    col: c,
                                    status: status
                                    // isAisle: false // Bỏ isAisle
                                });
                            }
                        }
                    }
                    return seatsArray;
                },

                // Lấy danh sách ghế cho một tầng cụ thể để render
                generateSeats(floor) {
                    // Lọc ghế thuộc tầng và không phải lối đi (vì không còn isAisle)
                    return this.seats.filter(seat => seat.floor === floor);
                },

                // Lấy class CSS cho ghế dựa vào trạng thái
                getSeatClasses(seat) {
                    return {
                        'seat': true, // Luôn là ghế
                        'available': seat.status === 'available',
                        'selected': seat.status === 'selected',
                        'booked': seat.status === 'booked',
                        // Bỏ class 'aisle'
                    };
                },

                // Lấy text trạng thái cho ARIA label
                getSeatStatusText(status) {
                    switch (status) {
                        case 'available':
                            return 'Trống';
                        case 'selected':
                            return 'Đang chọn';
                        case 'booked':
                            return 'Đã đặt';
                        default:
                            return '';
                    }
                },

                // Xử lý khi click vào ghế
                toggleSeat(seatId, shouldSort = true) {
                    let seat = this.seats.find(s => s.id === seatId);
                    // Chỉ xử lý ghế chưa đặt
                    if (!seat || seat.status === 'booked') {
                        return;
                    }

                    const indexInSelected = this.selectedSeats.indexOf(seatId);

                    if (seat.status === 'available' && indexInSelected === -1) {
                        seat.status = 'selected';
                        this.selectedSeats.push(seatId);
                    } else if (seat.status === 'selected' && indexInSelected !== -1) {
                        seat.status = 'available';
                        this.selectedSeats.splice(indexInSelected, 1);
                    }

                    if (shouldSort) {
                        this.selectedSeats.sort((a, b) => {
                            const [aFloor, aRow, aCol] = this.parseSeatId(a);
                            const [bFloor, bRow, bCol] = this.parseSeatId(b);
                            if (aFloor !== bFloor) return aFloor - bFloor;
                            if (aRow !== bRow) return aRow.localeCompare(bRow);
                            return aCol - bCol;
                        });
                    }
                    this.$nextTick(() => this.updateHiddenInputs());
                },

                // Helper tách ID ghế
                parseSeatId(seatId) {
                    const match = seatId.match(/^(?:T(\d+)-)?([A-Z]+)(\d+)$/);
                    if (match) {
                        const floor = match[1] ? parseInt(match[1], 10) : 1;
                        const row = match[2];
                        const col = parseInt(match[3], 10);
                        return [floor, row, col];
                    }
                    return [1, '', 0];
                },

                // Cập nhật các input ẩn seats[]
                updateHiddenInputs() {
                    const container = document.getElementById('hidden-seats-container');
                    if (!container) {
                        console.error("Cannot find #hidden-seats-container");
                        return;
                    }
                    container.innerHTML = '';
                    this.selectedSeats.forEach(seatId => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'seats[]';
                        input.value = seatId;
                        container.appendChild(input);
                    });
                },

                // Tính tổng tiền (getter)
                get totalPrice() {
                    return this.selectedSeats.length * this.pricePerSeat;
                },

                // Định dạng tiền tệ
                formatCurrency(value) {
                    if (isNaN(value)) return '0đ';
                    return new Intl.NumberFormat('vi-VN', {
                        style: 'currency',
                        currency: 'VND',
                        minimumFractionDigits: 0
                    }).format(value);
                },
            }
        }
    </script>
@endsection
