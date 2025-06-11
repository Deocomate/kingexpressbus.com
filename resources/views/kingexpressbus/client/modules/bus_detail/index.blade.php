@extends("kingexpressbus.client.layouts.main")

{{-- Section Title --}}
@section("title")
    Chi tiết chuyến {{ $busRouteData->bus_name }} - {{ $busRouteData->start_province_name }} đi {{ $busRouteData->end_province_name }}
@endsection

{{-- Push CSS/JS của Swiper và Flowbite --}}
@pushonce('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <style>
        /* CSS cho Swiper Gallery */
        .gallery-top {
            height: 60%; /* Chiều cao ảnh chính */
            width: 100%;
            border-radius: 0.5rem; /* rounded-lg */
            overflow: hidden;
            border: 1px solid #e5e7eb; /* border-gray-200 */
        }

        .gallery-top img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .gallery-thumbs {
            height: 20%; /* Chiều cao thumbnail */
            box-sizing: border-box;
            padding: 10px 0;
        }

        .gallery-thumbs .swiper-slide {
            width: 25%;
            height: 100%;
            opacity: 0.6;
            cursor: pointer;
            border-radius: 0.375rem; /* rounded-md */
            overflow: hidden;
            border: 2px solid transparent;
            transition: opacity 0.3s, border-color 0.3s;
        }

        .gallery-thumbs .swiper-slide:hover {
            opacity: 1;
        }

        .gallery-thumbs .swiper-slide-thumb-active {
            opacity: 1;
            border-color: #f59e0b; /* border-yellow-500 */
        }

        .gallery-thumbs img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Style cho các tab nếu dùng */
        [data-tabs-toggle] button[aria-selected="true"] {
            color: #ca8a04; /* text-yellow-600 */
            border-bottom-color: #f59e0b !important; /* border-yellow-500 */
        }

        [data-tabs-toggle] button:hover {
            border-bottom-color: #fcd34d; /* border-yellow-300 */
            color: #d97706; /* text-yellow-700 */
        }

        /* CSS cho sơ đồ ghế (giống trang booking) */
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
            gap: 8px;
            justify-items: center;
        }

        .seat {
            width: 40px;
            height: 40px;
            border: 1px solid #ccc;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
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

        .seat.booked {
            background-color: #e5e7eb;
            color: #9ca3af;
            border-color: #d1d5db;
            cursor: not-allowed;
            opacity: 0.7;
        }

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
        }

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
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
@endpushonce


@section("content")
    <div class="bg-gray-50 py-8 md:py-12">
        <div class="container mx-auto px-4">

            {{-- Breadcrumbs --}}
            <nav class="flex mb-5 text-sm" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ route('homepage') }}"
                           class="inline-flex items-center text-gray-700 hover:text-yellow-600">
                            <svg class="w-3 h-3 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                 fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                            </svg>
                            Trang chủ
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <a href="{{ route('client.bus_list', ['route_slug' => $busRouteData->route_slug ?? '#', 'departure_date' => $departure_date->format('Y-m-d')]) }}"
                               class="ms-1 text-gray-700 hover:text-yellow-600 md:ms-2">{{ $busRouteData->start_province_name }}
                                - {{ $busRouteData->end_province_name }}</a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ms-1 font-medium text-gray-500 md:ms-2">{{ $busRouteData->bus_name }} ({{ \Carbon\Carbon::parse($busRouteData->start_at)->format('H:i') }})</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
                    <div class="p-4 md:p-6 lg:border-r lg:border-gray-200">
                        @if(!empty($busRouteData->bus_images))
                            <div class="swiper gallery-top mb-3">
                                <div class="swiper-wrapper">
                                    @foreach($busRouteData->bus_images as $image)
                                        <div class="swiper-slide">
                                            <img src="{{ $image }}" alt="Ảnh xe {{ $busRouteData->bus_name }}">
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-button-next !text-yellow-500 !w-8 !h-8 after:!text-base"></div>
                                <div class="swiper-button-prev !text-yellow-500 !w-8 !h-8 after:!text-base"></div>
                            </div>
                            <div class="swiper gallery-thumbs">
                                <div class="swiper-wrapper">
                                    @foreach($busRouteData->bus_images as $image)
                                        <div class="swiper-slide">
                                            <img src="{{ $image }}" alt="Thumbnail xe {{ $busRouteData->bus_name }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <img class="w-full h-auto object-cover rounded-lg border border-gray-200"
                                 src="{{ $busRouteData->bus_thumbnail ?? 'https://placehold.co/800x600/fef3c7/ca8a04?text=' . urlencode($busRouteData->bus_name) }}"
                                 alt="Xe {{ $busRouteData->bus_name }}">
                        @endif
                    </div>

                    <div class="p-4 md:p-6">
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-1">{{ $busRouteData->bus_name }}</h1>
                        <p class="text-md font-semibold text-yellow-700 mb-4">{{ $busRouteData->bus_type_name }}</p>

                        <div class="mb-5 border-b border-gray-200 pb-4">
                            <h4 class="text-lg font-semibold text-gray-700 mb-2">Thông tin chuyến đi</h4>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Ngày đi: <span
                                            class="font-semibold text-gray-800">{{ $departure_date->format('d/m/Y') }}</span></span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Khởi hành: <span
                                            class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($busRouteData->start_at)->format('H:i') }}</span></span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.657 16.657l-1.414-1.414a1 1 0 00-1.414 0L12 17.586l-2.828-2.829a1 1 0 00-1.414 1.414l3.535 3.536a1 1 0 001.414 0l5.93-5.93a1 1 0 000-1.414zM12 21a9 9 0 100-18 9 9 0 000 18z"></path>
                                    </svg>
                                    <span>Điểm đi: <span
                                            class="font-semibold text-gray-800">{{ $busRouteData->start_province_name }}</span></span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17.657 16.657l-1.414-1.414a1 1 0 00-1.414 0L12 17.586l-2.828-2.829a1 1 0 00-1.414 1.414l3.535 3.536a1 1 0 001.414 0l5.93-5.93a1 1 0 000-1.414zM12 21a9 9 0 100-18 9 9 0 000 18z"></path>
                                    </svg>
                                    <span>Điểm đến: <span
                                            class="font-semibold text-gray-800">{{ $busRouteData->end_province_name }}</span></span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <span>Thời gian: <span
                                            class="font-semibold text-gray-800">{{ $busRouteData->duration_formatted ?? 'N/A' }}</span></span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span>Số chỗ: <span
                                            class="font-semibold text-gray-800">{{ $busRouteData->total_seats ?? 'N/A' }}</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-4">
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-gray-700">Giá vé chỉ từ:</p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    {{ $busRouteData->price ? number_format($busRouteData->price) . 'đ' : 'Liên hệ' }}
                                </p>
                            </div>
                            <a href="{{ route('client.booking_page', ['bus_route_slug' => $busRouteData->bus_route_slug, 'departure_date' => $departure_date->format('Y-m-d')]) }}"
                               class="block w-full text-center text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-base px-5 py-3 transition duration-200">
                                Đặt vé ngay
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white rounded-lg shadow-lg border border-gray-200 p-4 md:p-6">
                <div class="mb-4 border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="bus-detail-tabs"
                        data-tabs-toggle="#bus-detail-tab-content" role="tablist">
                        <li class="me-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="services-tab"
                                    data-tabs-target="#services" type="button" role="tab" aria-controls="services"
                                    aria-selected="false">Tiện ích
                            </button>
                        </li>
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                id="stops-tab" data-tabs-target="#stops" type="button" role="tab"
                                aria-controls="stops" aria-selected="false">Lộ trình & Điểm dừng
                            </button>
                        </li>
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                id="description-tab" data-tabs-target="#description" type="button" role="tab"
                                aria-controls="description" aria-selected="false">Mô tả chi tiết
                            </button>
                        </li>
                        <li class="me-2" role="presentation">
                            <button
                                class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                id="seatmap-tab" data-tabs-target="#seatmap" type="button" role="tab"
                                aria-controls="seatmap" aria-selected="false">Sơ đồ xe
                            </button>
                        </li>
                    </ul>
                </div>
                <div id="bus-detail-tab-content">
                    <div class="hidden p-4 rounded-lg bg-gray-50" id="services" role="tabpanel"
                         aria-labelledby="services-tab">
                        @if(!empty($busRouteData->bus_services))
                            <h4 class="text-lg font-semibold text-gray-800 mb-3">Tiện nghi trên xe</h4>
                            <ul class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 text-sm text-gray-700">
                                @foreach($busRouteData->bus_services as $service)
                                    <li class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $service }}
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500">Chưa cập nhật thông tin tiện ích cho loại xe này.</p>
                        @endif
                    </div>
                    <div class="hidden p-4 rounded-lg bg-gray-50" id="stops" role="tabpanel"
                         aria-labelledby="stops-tab">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Các điểm dừng dự kiến</h4>
                        @if($busRouteData->stops->isNotEmpty())
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-center">
                                    <span class="font-semibold">{{ $busRouteData->start_province_name }} (Điểm xuất phát)</span>
                                </li>
                                @foreach($busRouteData->stops as $stop)
                                    <li class="flex items-center pl-3 border-l-2 border-dashed border-gray-300 ml-3">
                                        <span>{{ $stop->stop_title ? $stop->stop_title . ' (' . $stop->district_name . ')' : $stop->district_name }}</span>
                                    </li>
                                @endforeach
                                <li class="flex items-center pl-3 border-l-2 border-transparent ml-3">
                                    <span class="font-semibold">{{ $busRouteData->end_province_name }} (Điểm đến dự kiến)</span>
                                </li>
                            </ul>
                        @else
                            <p class="text-gray-500">Chưa có thông tin chi tiết về các điểm dừng cho chuyến xe này.</p>
                        @endif
                    </div>
                    <div class="hidden p-4 rounded-lg bg-gray-50" id="description" role="tabpanel"
                         aria-labelledby="description-tab">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Mô tả chi tiết về loại xe</h4>
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! $busRouteData->bus_detail ?? '<p class="text-gray-500">Chưa có mô tả chi tiết cho loại xe này.</p>' !!}
                        </div>
                        @if($busRouteData->bus_route_detail)
                            <h5 class="text-md font-semibold text-gray-800 mt-4 mb-2">Thông tin thêm về lịch trình</h5>
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {!! $busRouteData->bus_route_detail !!}
                            </div>
                        @endif
                    </div>

                    <div class="hidden p-4 rounded-lg bg-gray-50" id="seatmap" role="tabpanel"
                         aria-labelledby="seatmap-tab">
                        <h4 class="text-lg font-semibold text-gray-800 mb-1">Sơ đồ vị trí ghế</h4>
                        <p class="text-xs text-gray-500 mb-3">Sơ đồ chỉ mang tính chất tham khảo vị trí các ghế trên xe
                            cho ngày <strong class="text-yellow-700">{{ $departure_date->format('d/m/Y') }}</strong>.
                        </p>
                        <div x-data="seatMapViewer({
                                rows: {{ $busRouteData->seat_row_number ?? 0 }},
                                cols: {{ $busRouteData->seat_column_number ?? 0 }},
                                floors: {{ $busRouteData->floors ?? 1 }},
                                bookedSeats: {{ json_encode($bookedSeatsArray) }},
                                pricePerSeat: {{ $busRouteData->price ?? 0 }}
                            })" class="seat-layout-container">
                            <div class="seat-map-wrapper">
                                <div class="inline-block">
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-center text-gray-600 mb-3">Tầng 1</p>
                                        <div class="seat-container"
                                             :style="`grid-template-columns: repeat(${cols}, auto);`">
                                            <template x-for="seat in generateSeats(1)" :key="seat.id">
                                                <div x-data="{ tooltipVisible: false }"
                                                     @mouseenter="tooltipVisible = true"
                                                     @mouseleave="tooltipVisible = false" class="relative">
                                                    <div x-text="seat.id"
                                                         :class="getSeatClasses(seat)"
                                                         :aria-label="`Ghế ${seat.id} - ${getSeatStatusText(seat.status)}`"
                                                         class="!cursor-default"
                                                    >
                                                    </div>
                                                    <div x-show="tooltipVisible" x-tooltip
                                                         :class="{ 'visible': tooltipVisible }">
                                                        <template x-if="seat.status === 'available'">
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
                                                             :class="getSeatClasses(seat)"
                                                             :aria-label="`Ghế ${seat.id} - ${getSeatStatusText(seat.status)}`"
                                                             class="!cursor-default"
                                                        >
                                                        </div>
                                                        <div x-show="tooltipVisible" x-tooltip
                                                             :class="{ 'visible': tooltipVisible }">
                                                            <template x-if="seat.status === 'available'">
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
                            <div
                                class="flex flex-wrap justify-center gap-x-4 gap-y-1 text-xs mt-4 pt-3 border-t border-gray-200">
                                <div class="flex items-center">
                                    <div class="seat available w-4 h-4 mr-1.5 !cursor-default"></div>
                                    Ghế trống
                                </div>
                                <div class="flex items-center">
                                    <div class="seat booked w-4 h-4 mr-1.5 !cursor-default"></div>
                                    Đã đặt
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.querySelector('.gallery-thumbs') && document.querySelector('.gallery-top')) {
                var galleryThumbs = new Swiper('.gallery-thumbs', {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    watchSlidesProgress: true,
                    breakpoints: {
                        640: {slidesPerView: 5, spaceBetween: 10},
                        768: {slidesPerView: 6, spaceBetween: 10}
                    },
                });
                var galleryTop = new Swiper('.gallery-top', {
                    spaceBetween: 10,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    thumbs: {
                        swiper: galleryThumbs,
                    },
                });
            }
        });

        function seatMapViewer(config) {
            return {
                rows: parseInt(config.rows) || 0,
                cols: parseInt(config.cols) || 0,
                floors: parseInt(config.floors) || 1,
                bookedSeats: Array.isArray(config.bookedSeats) ? config.bookedSeats : [],
                pricePerSeat: parseFloat(config.pricePerSeat) || 0,
                seats: [],
                init() {
                    if (this.rows > 0 && this.cols > 0) {
                        this.seats = this.generateInitialSeats();
                    } else {
                        console.error("Invalid seat map config for viewer:", config.rows, config.cols);
                    }
                },
                generateInitialSeats() {
                    let seatsArray = [];
                    const seatChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    if (this.rows === 0 || this.cols === 0) return seatsArray;

                    for (let f = 1; f <= this.floors; f++) {
                        for (let r = 0; r < this.rows; r++) {
                            if (r >= seatChars.length) continue;
                            let rowChar = seatChars[r];
                            for (let c = 1; c <= this.cols; c++) {
                                let seatId = `${f > 1 ? 'T' + f + '-' : ''}${rowChar}${c}`;
                                let status = this.bookedSeats.includes(seatId) ? 'booked' : 'available';
                                seatsArray.push({id: seatId, floor: f, row: r, col: c, status: status});
                            }
                        }
                    }
                    return seatsArray;
                },
                generateSeats(floor) {
                    return this.seats.filter(seat => seat.floor === floor);
                },
                getSeatClasses(seat) {
                    return {
                        'seat': true,
                        'available': seat.status === 'available',
                        'booked': seat.status === 'booked',
                        '!cursor-default': true
                    };
                },
                getSeatStatusText(status) {
                    switch (status) {
                        case 'available':
                            return 'Trống';
                        case 'booked':
                            return 'Đã đặt';
                        default:
                            return '';
                    }
                },
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
@endpush
