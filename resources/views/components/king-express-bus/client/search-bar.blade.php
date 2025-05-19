{{--
    Search Bar Component View
    Sử dụng Tailwind CSS, Alpine.js (nếu cần), và Tom Select cho dropdown.
    Dữ liệu $locations được truyền từ SearchBar Component Class.
--}}

{{-- Đẩy CSS của Tom Select vào stack 'styles' trong layout chính --}}
@pushonce('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    {{-- CSS tùy chỉnh để Tom Select hợp với Tailwind hơn (tùy chọn) --}}
    <style>
        .ts-control {
            border-radius: 0.5rem; /* rounded-lg */
            border-color: #d1d5db; /* border-gray-300 */
            padding: 0.5rem 1rem; /* px-4 py-2 */
            box-shadow: none !important; /* Bỏ shadow mặc định */
        }

        .ts-control:focus-within, .ts-control.focus { /* Giống focus:ring-2 focus:ring-yellow-500 */
            border-color: #f59e0b !important; /* border-yellow-500 */
            box-shadow: 0 0 0 2px #fcd34d !important; /* ring-2 ring-yellow-300/50 */
        }

        .ts-dropdown {
            border-radius: 0.5rem; /* rounded-lg */
            border-color: #d1d5db; /* border-gray-300 */
            z-index: 20; /* Đảm bảo dropdown hiển thị trên các element khác */
        }

        .ts-dropdown .option {
            padding: 0.5rem 1rem; /* px-4 py-2 */
        }

        .ts-dropdown .optgroup-header {
            padding: 0.5rem 1rem;
            font-weight: 600;
            color: #6b7280; /* text-gray-500 */
        }

        .ts-dropdown .active {
            background-color: #fef3c7; /* bg-yellow-100 */
            color: #ca8a04; /* text-yellow-700 */
        }

        /* Placeholder styling */
        .ts-control > input::placeholder {
            color: #9ca3af; /* text-gray-400 */
            opacity: 1;
        }
    </style>
@endpushonce

{{-- Phần HTML của Search Bar --}}
<section class="bg-white py-10"> {{-- Giảm padding một chút --}}
    <div class="container mx-auto px-4">
        {{-- Sử dụng màu nền vàng nhạt cho card tìm kiếm --}}
        <div
            class="bg-yellow-50 shadow-lg rounded-xl p-6 -mt-20 relative z-10 mx-4 md:mx-auto max-w-5xl border border-yellow-200">
            <h2 class="text-2xl font-bold text-yellow-800 mb-6 text-center md:text-left">Tìm và đặt vé xe</h2>

            {{-- Form tìm kiếm, trỏ đến route 'client.search' --}}
            <form action="{{ route('client.search') }}" method="POST"
                  class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                @csrf {{-- Token CSRF --}}

                {{-- 1. Chọn Điểm đi --}}
                <div>
                    {{-- Sử dụng icon cho label --}}
                    <label for="select-location-start" class="flex items-center text-sm font-medium text-gray-700 mb-1">
                        <svg class="w-4 h-4 mr-1 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Điểm đi
                    </label>
                    {{-- Select với ID để Tom Select nhận diện --}}
                    <select id="select-location-start" name="location_start_id" required
                            placeholder="Chọn hoặc nhập điểm đi...">
                        <option value="">Chọn hoặc nhập điểm đi...</option>
                        {{-- Lặp qua $locations để tạo options và optgroups --}}
                        @php $currentGroup = null; @endphp
                        @foreach($locations as $location)
                            {{-- Bắt đầu optgroup mới nếu group thay đổi --}}
                            @if($location['group'] !== $currentGroup)
                                @if($currentGroup !== null)
                                    </optgroup> {{-- Đóng optgroup trước đó --}}
                        @endif
                        <optgroup label="{{ $location['group'] }}">
                            @php $currentGroup = $location['group']; @endphp
                            @endif
                            {{-- Thêm option --}}
                            <option value="{{ $location['id'] }}">{{ $location['name'] }}</option>
                            @endforeach
                            {{-- Đóng optgroup cuối cùng --}}
                            @if($currentGroup !== null)
                        </optgroup>
                        @endif
                    </select>
                    @error('location_start_id') {{-- Hiển thị lỗi validation nếu có --}}
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 2. Chọn Điểm đến --}}
                <div>
                    <label for="select-location-end" class="flex items-center text-sm font-medium text-gray-700 mb-1">
                        <svg class="w-4 h-4 mr-1 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Điểm đến
                    </label>
                    <select id="select-location-end" name="location_end_id" required
                            placeholder="Chọn hoặc nhập điểm đến...">
                        <option value="">Chọn hoặc nhập điểm đến...</option>
                        {{-- Lặp qua $locations tương tự điểm đi --}}
                        @php $currentGroup = null; @endphp
                        @foreach($locations as $location)
                            @if($location['group'] !== $currentGroup)
                                @if($currentGroup !== null)
                                    </optgroup>
                        @endif
                        <optgroup label="{{ $location['group'] }}">
                            @php $currentGroup = $location['group']; @endphp
                            @endif
                            <option value="{{ $location['id'] }}">{{ $location['name'] }}</option>
                            @endforeach
                            @if($currentGroup !== null)
                        </optgroup>
                        @endif
                    </select>
                    @error('location_end_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 3. Chọn Ngày đi --}}
                <div>
                    <label for="departure_date" class="flex items-center text-sm font-medium text-gray-700 mb-1">
                        <svg class="w-4 h-4 mr-1 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Ngày đi
                    </label>
                    <input type="date"
                           id="departure_date"
                           name="departure_date"
                           required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           value="{{ old('departure_date', now()->format('Y-m-d')) }}"
                           {{-- Giá trị mặc định là hôm nay --}}
                           min="{{ now()->format('Y-m-d') }}" {{-- Không cho chọn ngày quá khứ --}}
                    >
                    @error('departure_date')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 4. Nút Tìm kiếm --}}
                <div class="pt-2 md:pt-0"> {{-- Thêm padding top trên mobile --}}
                    <button type="submit"
                            class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2.5 px-4 rounded-lg font-semibold text-base transition duration-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Tìm chuyến xe
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Đẩy JS của Tom Select và script khởi tạo vào stack 'scripts' --}}
@pushonce('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@endpushonce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Hàm khởi tạo Tom Select
            function initTomSelect(selector) {
                let el = document.getElementById(selector);
                if (el) {
                    new TomSelect(el, {
                        create: false, // Không cho phép tạo option mới
                        sortField: { // Sắp xếp theo text
                            field: "text",
                            direction: "asc"
                        },
                        // placeholder: el.getAttribute('placeholder') || 'Chọn...', // Lấy placeholder từ attribute
                        // Có thể thêm các tùy chọn khác nếu cần
                        // render: { // Tùy chỉnh cách hiển thị item và option nếu muốn
                        //     option: function(data, escape) {
                        //         return '<div>' + escape(data.text) + '</div>';
                        //     },
                        //     item: function(data, escape) {
                        //         return '<div>' + escape(data.text) + '</div>';
                        //     }
                        // }
                    });
                } else {
                    console.error('Tom Select cannot find element with ID:', selector);
                }
            }

            // Khởi tạo Tom Select cho điểm đi và điểm đến
            initTomSelect('select-location-start');
            initTomSelect('select-location-end');

            // ----- Xử lý ngày mặc định và min date (Đã làm bằng PHP trong input) -----
            // let dateInput = document.getElementById('departure_date');
            // if (dateInput) {
            //     const today = new Date().toISOString().split('T')[0];
            //     if (!dateInput.value) { // Chỉ set nếu chưa có giá trị (ví dụ từ old())
            //         dateInput.value = today;
            //     }
            //     dateInput.min = today;
            // }

        });
    </script>
@endpush

