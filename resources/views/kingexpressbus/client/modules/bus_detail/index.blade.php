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
    </style>
@endpushonce

@pushonce('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
@endpushonce


@section("content")
    <div class="bg-gray-50 py-8 md:py-12">
        <div class="container mx-auto px-4">

            {{-- Breadcrumbs (Tùy chọn) --}}
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
                            {{-- Link về trang danh sách xe của tuyến này --}}
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

            {{-- Phần chính: Ảnh và Thông tin --}}
            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">

                    {{-- Cột 1: Thư viện ảnh (Swiper) --}}
                    <div class="p-4 md:p-6 lg:border-r lg:border-gray-200">
                        @if(!empty($busRouteData->bus_images))
                            {{-- Swiper ảnh chính --}}
                            <div class="swiper gallery-top mb-3">
                                <div class="swiper-wrapper">
                                    @foreach($busRouteData->bus_images as $image)
                                        <div class="swiper-slide">
                                            <img src="{{ $image }}" alt="Ảnh xe {{ $busRouteData->bus_name }}">
                                        </div>
                                    @endforeach
                                </div>
                                {{-- Add Arrows --}}
                                <div class="swiper-button-next !text-yellow-500 !w-8 !h-8 after:!text-base"></div>
                                <div class="swiper-button-prev !text-yellow-500 !w-8 !h-8 after:!text-base"></div>
                            </div>
                            {{-- Swiper ảnh thumbnail --}}
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
                            {{-- Ảnh mặc định nếu không có gallery --}}
                            <img class="w-full h-auto object-cover rounded-lg border border-gray-200"
                                 src="{{ $busRouteData->bus_thumbnail ?? 'https://placehold.co/800x600/fef3c7/ca8a04?text=' . urlencode($busRouteData->bus_name) }}"
                                 alt="Xe {{ $busRouteData->bus_name }}">
                        @endif
                    </div>

                    {{-- Cột 2: Thông tin chi tiết và nút đặt vé --}}
                    <div class="p-4 md:p-6">
                        {{-- Tên xe và loại xe --}}
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-1">{{ $busRouteData->bus_name }}</h1>
                        <p class="text-md font-semibold text-yellow-700 mb-4">{{ $busRouteData->bus_type_name }}</p>

                        {{-- Thông tin chuyến --}}
                        <div class="mb-5 border-b border-gray-200 pb-4">
                            <h4 class="text-lg font-semibold text-gray-700 mb-2">Thông tin chuyến đi</h4>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm text-gray-600">
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
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Dự kiến đến: <span
                                                class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($busRouteData->end_at)->format('H:i') }}</span></span>
                                </div>
                                <div class="flex items-center col-span-2">
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

                        {{-- Giá vé và Nút Đặt vé --}}
                        <div class="mt-auto pt-4">
                            <div class="flex justify-between items-center mb-4">
                                <p class="text-gray-700">Giá vé chỉ từ:</p>
                                <p class="text-2xl font-bold text-yellow-600">
                                    {{ $busRouteData->start_price ? number_format($busRouteData->start_price) . 'đ' : 'Liên hệ' }}
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

            {{-- Phần Tab: Tiện ích, Điểm dừng, Mô tả chi tiết --}}
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
                                    aria-controls="stops"
                                    aria-selected="false">Lộ trình & Điểm dừng
                            </button>
                        </li>
                        <li class="me-2" role="presentation">
                            <button
                                    class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300"
                                    id="description-tab" data-tabs-target="#description" type="button" role="tab"
                                    aria-controls="description" aria-selected="false">Mô tả chi tiết
                            </button>
                        </li>
                        {{-- Thêm tab Đánh giá nếu có --}}
                    </ul>
                </div>
                <div id="bus-detail-tab-content">
                    {{-- Tab Tiện ích --}}
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
                    {{-- Tab Điểm dừng --}}
                    <div class="hidden p-4 rounded-lg bg-gray-50" id="stops" role="tabpanel"
                         aria-labelledby="stops-tab">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Các điểm dừng dự kiến</h4>
                        @if($busRouteData->stops->isNotEmpty())
                            <ul class="space-y-2 text-sm text-gray-700">
                                {{-- Điểm bắt đầu --}}
                                <li class="flex items-center">
                                <span
                                        class="flex items-center justify-center w-6 h-6 mr-3 bg-yellow-500 text-white rounded-full text-xs font-bold">
                                    {{ \Carbon\Carbon::parse($busRouteData->start_at)->format('H:i') }}
                                </span>
                                    <span class="font-semibold">{{ $busRouteData->start_province_name }} (Điểm xuất phát)</span>
                                </li>
                                {{-- Các điểm dừng trung gian --}}
                                @foreach($busRouteData->stops as $stop)
                                    <li class="flex items-center pl-3 border-l-2 border-dashed border-gray-300 ml-3">
                                     <span
                                             class="flex items-center justify-center w-6 h-6 mr-3 bg-gray-400 text-white rounded-full text-xs font-bold">
                                         {{ \Carbon\Carbon::parse($stop->stop_at)->format('H:i') }}
                                     </span>
                                        <span>{{ $stop->stop_title ? $stop->stop_title . ' (' . $stop->district_name . ')' : $stop->district_name }}</span>
                                    </li>
                                @endforeach
                                {{-- Điểm kết thúc --}}
                                <li class="flex items-center pl-3 border-l-2 border-transparent ml-3">
                                 <span
                                         class="flex items-center justify-center w-6 h-6 mr-3 bg-yellow-500 text-white rounded-full text-xs font-bold">
                                     {{ \Carbon\Carbon::parse($busRouteData->end_at)->format('H:i') }}
                                 </span>
                                    <span class="font-semibold">{{ $busRouteData->end_province_name }} (Điểm đến dự kiến)</span>
                                </li>
                            </ul>
                        @else
                            <p class="text-gray-500">Chưa có thông tin chi tiết về các điểm dừng cho chuyến xe này.</p>
                        @endif
                    </div>
                    {{-- Tab Mô tả chi tiết --}}
                    <div class="hidden p-4 rounded-lg bg-gray-50" id="description" role="tabpanel"
                         aria-labelledby="description-tab">
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Mô tả chi tiết về loại xe</h4>
                        {{-- Sử dụng prose của Tailwind để định dạng HTML từ CKEditor --}}
                        <div class="prose prose-sm max-w-none text-gray-700">
                            {!! $busRouteData->bus_detail ?? '<p class="text-gray-500">Chưa có mô tả chi tiết cho loại xe này.</p>' !!}
                        </div>
                        {{-- Thêm mô tả riêng của lịch trình nếu có --}}
                        @if($busRouteData->bus_route_detail)
                            <h5 class="text-md font-semibold text-gray-800 mt-4 mb-2">Thông tin thêm về lịch trình</h5>
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {!! $busRouteData->bus_route_detail !!}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Khởi tạo Swiper Gallery nếu có ảnh
            if (document.querySelector('.gallery-thumbs') && document.querySelector('.gallery-top')) {
                var galleryThumbs = new Swiper('.gallery-thumbs', {
                    spaceBetween: 10,
                    slidesPerView: 4, // Số ảnh thumbnail hiển thị
                    freeMode: true,
                    watchSlidesProgress: true,
                    breakpoints: {
                        // when window width is >= 640px
                        640: {
                            slidesPerView: 5,
                            spaceBetween: 10
                        },
                        // when window width is >= 768px
                        768: {
                            slidesPerView: 6,
                            spaceBetween: 10
                        }
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
                    // Autoplay (tùy chọn)
                    // autoplay: {
                    //   delay: 4000,
                    //   disableOnInteraction: false,
                    // },
                });
            }

            // Khởi tạo Tabs (Flowbite tự động làm việc này nếu JS được load)
            // Nếu bạn không dùng data-* attributes, bạn cần khởi tạo thủ công:
            // const tabsElement = document.getElementById('bus-detail-tabs');
            // const tabElements = [ ... ]; // Lấy các tab elements
            // const contentElements = [ ... ]; // Lấy các content elements
            // const tabs = new Tabs(tabsElement, tabElements, contentElements, options);
        });
    </script>
@endpush
