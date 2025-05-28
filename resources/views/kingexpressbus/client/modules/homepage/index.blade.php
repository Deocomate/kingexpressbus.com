@extends("kingexpressbus.client.layouts.main")
@section("title", $webInfoGlobal->title ?? "Trang chủ Kingexpressbus") {{-- Lấy title từ webInfoGlobal --}}

{{-- Đẩy CSS của Swiper.js vào stack 'styles' --}}
@pushonce('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <style>
        /* Tùy chỉnh style cho Swiper nếu cần */
        .swiper-button-next,
        .swiper-button-prev {
            color: #f59e0b; /* Màu vàng */
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            transition: background-color 0.3s ease;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background-color: rgba(255, 255, 255, 0.9);
            color: #d97706; /* Vàng đậm hơn khi hover */
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 18px;
            font-weight: bold;
        }

        .swiper-pagination-bullet-active {
            background-color: #f59e0b; /* Màu vàng */
        }

        /* Đảm bảo chiều cao nhất quán cho card trong slider */
        .route-card, .bus-card {
            display: flex;
            flex-direction: column;
            height: 100%; /* Quan trọng cho layout flex */
        }

        .route-card .p-6, .bus-card .p-6 {
            flex-grow: 1; /* Phần nội dung sẽ co giãn */
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Đẩy nút xuống dưới cùng */
        }

        .route-card-content, .bus-card-content {
            flex-grow: 1;
        }
    </style>
@endpushonce

@section("content")
    {{-- Sử dụng màu vàng chủ đạo --}}
    <section class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white">
        <div class="container mx-auto px-4 py-16 md:py-24 flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0 text-center md:text-left">
                {{-- Lấy tiêu đề và mô tả từ $webInfoGlobal nếu có --}}
                <h1 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight">
                    {{ $webInfoGlobal->hero_title ?? 'Đặt vé xe khách trực tuyến dễ dàng' }} {{-- Giả sử có trường hero_title --}}
                </h1>
                <p class="text-lg lg:text-xl mb-8 text-yellow-100">
                    {{ $webInfoGlobal->hero_description ?? 'Hệ thống đặt vé xe khách nhanh chóng, tiện lợi với nhiều tuyến đường và nhà xe uy tín.' }} {{-- Giả sử có trường hero_description --}}
                </p>
                <div
                    class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center md:justify-start">
                    {{-- Nút Đặt vé ngay (có thể scroll xuống search bar) --}}
                    <a href="#search-section" {{-- Link tới search bar --}}
                    class="bg-white text-yellow-600 hover:bg-yellow-100 py-3 px-8 rounded-lg font-bold text-center shadow-md transition duration-300 ease-in-out transform hover:-translate-y-1">
                        Đặt vé ngay
                    </a>
                    {{-- Nút Tìm lịch trình (link tới trang lịch trình nếu có) --}}
                    <a href="#"
                       class="bg-transparent border-2 border-white hover:bg-white hover:text-yellow-600 py-3 px-8 rounded-lg font-bold text-center transition duration-300 ease-in-out">
                        Tìm lịch trình
                    </a>
                </div>
            </div>
            <div class="md:w-1/2 flex justify-center">
                <img
                    src="{{ "/client/images/banner.png" ?? 'https://placehold.co/600x400/facc15/ffffff?text=KingExpressBus' }}"
                    alt="Đặt vé xe KingExpressBus" class="rounded-lg shadow-xl w-full max-w-md">
            </div>
        </div>
    </section>

    {{-- Search Bar Section --}}
    <div id="search-section"> {{-- Thêm ID để link từ Hero Section --}}
        <x-king-express-bus.client.search-bar/>
    </div>

    @if($popularRoutes->isNotEmpty())
        <section class="bg-yellow-50 py-16">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-yellow-800 mb-2 text-center">Tuyến đường phổ biến</h2>
                <p class="text-gray-600 mb-12 text-center">Những tuyến đường được khách hàng lựa chọn nhiều nhất</p>

                {{-- Swiper Slider --}}
                <div class="swiper popular-routes-slider relative">
                    <div class="swiper-wrapper">
                        {{-- Lặp qua các tuyến đường phổ biến --}}
                        @foreach($popularRoutes as $route)
                            <div class="swiper-slide h-auto"> {{-- h-auto để chiều cao tự điều chỉnh --}}
                                {{-- Card Tuyến đường --}}
                                <div
                                    class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 route-card">
                                    {{-- Ảnh tuyến đường --}}
                                    <img
                                        src="{{ $route->thumbnail ?? 'https://placehold.co/600x400/fef3c7/ca8a04?text=' . urlencode($route->start_province_name . ' - ' . $route->end_province_name) }}"
                                        alt="Tuyến {{ $route->title }}" class="w-full h-60 object-cover">
                                    {{-- Nội dung card --}}
                                    <div class="p-5"> {{-- Giảm padding chút --}}
                                        <div class="route-card-content">
                                            <div class="flex justify-between items-start mb-3">
                                                <div>
                                                    <h3 class="text-lg font-bold text-gray-800 hover:text-yellow-600 transition-colors">
                                                        <a href="{{ route('client.bus_list', ['route_slug' => $route->slug]) }}">
                                                            {{ $route->start_province_name }}
                                                            ⟶ {{ $route->end_province_name }}
                                                        </a>
                                                    </h3>
                                                    <p class="text-sm text-gray-500">{{ $route->distance ? number_format($route->distance) . 'km' : '' }}{{ $route->distance && $route->duration ? ' - ' : '' }}{{ $route->duration ?? '' }}</p>
                                                </div>
                                                <div class="text-yellow-600 font-bold text-lg whitespace-nowrap ml-2"> Từ
                                                    {{ $route->start_price ? number_format($route->start_price) . 'VND' : 'Liên hệ' }}
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                                {{ $route->description ?? 'Khám phá tuyến đường hấp dẫn này cùng KingExpressBus.' }}
                                            </p>
                                        </div>
                                        {{-- Nút Đặt vé --}}
                                        <a href="{{ route('client.bus_list', ['route_slug' => $route->slug]) }}"
                                           class="block text-center w-full mt-4 bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg font-medium transition duration-200">
                                            Xem chuyến xe
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Thêm Navigation Buttons và Pagination --}}
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination text-center mt-8 relative"></div> {{-- Đẩy pagination xuống dưới --}}
                </div>
            </div>
        </section>
    @endif

    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-800 mb-2 text-center">Tại sao chọn KingExpressBus?</h2>
            <p class="text-gray-600 mb-12 text-center">Chúng tôi cam kết mang đến trải nghiệm tốt nhất cho hành trình
                của bạn</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Service 1: Đa dạng tuyến --}}
                <div class="text-center p-6 hover:shadow-lg rounded-lg transition-shadow duration-300">
                    <div
                        class="bg-yellow-100 text-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Mạng lưới rộng khắp</h3>
                    <p class="text-gray-600 text-sm">Phục vụ hàng trăm tuyến đường kết nối các tỉnh thành trên cả
                        nước.</p>
                </div>
                {{-- Service 2: Giá tốt --}}
                <div class="text-center p-6 hover:shadow-lg rounded-lg transition-shadow duration-300">
                    <div
                        class="bg-yellow-100 text-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Giá vé cạnh tranh</h3>
                    <p class="text-gray-600 text-sm">Luôn có mức giá hợp lý cùng nhiều chương trình ưu đãi hấp dẫn.</p>
                </div>
                {{-- Service 3: Đặt vé dễ dàng --}}
                <div class="text-center p-6 hover:shadow-lg rounded-lg transition-shadow duration-300">
                    <div
                        class="bg-yellow-100 text-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Đặt vé Online 24/7</h3>
                    <p class="text-gray-600 text-sm">Đặt vé mọi lúc mọi nơi qua website hoặc ứng dụng di động tiện
                        lợi.</p>
                </div>
                {{-- Service 4: Chất lượng cao --}}
                <div class="text-center p-6 hover:shadow-lg rounded-lg transition-shadow duration-300">
                    <div
                        class="bg-yellow-100 text-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Dịch vụ chất lượng</h3>
                    <p class="text-gray-600 text-sm">Xe đời mới, sạch sẽ, tiện nghi cùng đội ngũ phục vụ chuyên
                        nghiệp.</p>
                </div>
            </div>
        </div>
    </section>

    @if($featuredBuses->isNotEmpty())
        <section class="py-16 bg-gray-50"> {{-- Nền xám nhạt hơn --}}
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-gray-800 mb-2 text-center">Đội xe hiện đại</h2>
                <p class="text-gray-600 mb-12 text-center">Trải nghiệm hành trình thoải mái với các dòng xe cao cấp</p>

                {{-- Grid hoặc Slider tùy số lượng $featuredBuses --}}
                @if($featuredBuses->count() > 3)
                    {{-- Nếu nhiều hơn 3 thì dùng slider --}}
                    <div class="swiper fleet-slider relative">
                        <div class="swiper-wrapper">
                            @foreach($featuredBuses as $bus)
                                <div class="swiper-slide h-auto">
                                    <div
                                        class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 bus-card">
                                        <img
                                            src="{{ $bus->thumbnail ?? 'https://placehold.co/600x400/fcd34d/1f2937?text=' . urlencode($bus->name) }}"
                                            alt="{{ $bus->name }}" class="w-full h-48 object-cover">
                                        <div class="p-5">
                                            <div class="bus-card-content">
                                                <h3 class="text-xl font-semibold text-gray-800 mb-1">{{ $bus->name }}</h3>
                                                <p class="text-sm text-yellow-700 font-medium mb-3">{{ $bus->type_name }}</p>
                                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                                    {{ $bus->description ?? 'Dòng xe hiện đại, tiện nghi, mang lại trải nghiệm tốt nhất.' }}
                                                </p>
                                                {{-- Hiển thị một vài tiện ích nổi bật --}}
                                                @if(!empty($bus->services))
                                                    <div class="flex flex-wrap gap-2 mb-4">
                                                        @foreach(array_slice($bus->services, 0, 3) as $service)
                                                            {{-- Chỉ hiển thị 3 tiện ích đầu --}}
                                                            <span
                                                                class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-xs font-medium">{{ $service }}</span>
                                                        @endforeach
                                                        @if(count($bus->services) > 3)
                                                            <span class="text-gray-500 text-xs">...</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            {{-- Link chi tiết (tạm thời vô hiệu hóa hoặc link tới trang chung) --}}
                                            {{-- <a href="#" class="text-yellow-600 font-medium hover:text-yellow-800 text-sm mt-auto">Xem chi tiết →</a> --}}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-pagination text-center mt-8 relative"></div>
                    </div>
                @else
                    {{-- Nếu ít hơn hoặc bằng 3 thì dùng grid --}}
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $featuredBuses->count() > 0 ? $featuredBuses->count() : 1 }} gap-6 justify-center">
                        @foreach($featuredBuses as $bus)
                            <div
                                class="bg-white rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 bus-card max-w-sm mx-auto"> {{-- Giới hạn chiều rộng card trong grid --}}
                                <img
                                    src="{{ $bus->thumbnail ?? 'https://placehold.co/600x400/fcd34d/1f2937?text=' . urlencode($bus->name) }}"
                                    alt="{{ $bus->name }}" class="w-full h-48 object-cover">
                                <div class="p-5">
                                    <div class="bus-card-content">
                                        <h3 class="text-xl font-semibold text-gray-800 mb-1">{{ $bus->name }}</h3>
                                        <p class="text-sm text-yellow-700 font-medium mb-3">{{ $bus->type_name }}</p>
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                            {{ $bus->description ?? 'Dòng xe hiện đại, tiện nghi, mang lại trải nghiệm tốt nhất.' }}
                                        </p>
                                        @if(!empty($bus->services))
                                            <div class="flex flex-wrap gap-2 mb-4">
                                                @foreach(array_slice($bus->services, 0, 3) as $service)
                                                    <span
                                                        class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded text-xs font-medium">{{ $service }}</span>
                                                @endforeach
                                                @if(count($bus->services) > 3)
                                                    <span class="text-gray-500 text-xs">...</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    {{-- <a href="#" class="text-yellow-600 font-medium hover:text-yellow-800 text-sm mt-auto">Xem chi tiết →</a> --}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </section>
    @endif

@endsection

{{-- Đẩy JS của Swiper và script khởi tạo vào stack 'scripts' --}}
@pushonce('scripts')
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
@endpushonce

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Khởi tạo Swiper cho Tuyến đường phổ biến
            if (document.querySelector('.popular-routes-slider')) {
                const popularRoutesSwiper = new Swiper('.popular-routes-slider', {
                    // Optional parameters
                    loop: true, // Lặp lại slider
                    slidesPerView: 1, // Số lượng slide hiển thị trên mobile
                    spaceBetween: 15, // Khoảng cách giữa các slide

                    // Responsive breakpoints
                    breakpoints: {
                        // when window width is >= 640px
                        640: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        // when window width is >= 1024px
                        1024: {
                            slidesPerView: 2,
                            spaceBetween: 30
                        }
                    },

                    // If we need pagination
                    pagination: {
                        el: '.popular-routes-slider .swiper-pagination',
                        clickable: true,
                    },

                    // Navigation arrows
                    navigation: {
                        nextEl: '.popular-routes-slider .swiper-button-next',
                        prevEl: '.popular-routes-slider .swiper-button-prev',
                    },

                    // Autoplay
                    // autoplay: {
                    //     delay: 5000, // 5 giây
                    //     disableOnInteraction: false, // Không dừng khi người dùng tương tác
                    // },
                });
            }

            // Khởi tạo Swiper cho Đội xe (nếu có)
            if (document.querySelector('.fleet-slider')) {
                const fleetSwiper = new Swiper('.fleet-slider', {
                    loop: true,
                    slidesPerView: 1,
                    spaceBetween: 15,
                    breakpoints: {
                        640: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        1024: {
                            slidesPerView: 3, // Có thể tăng lên 4 nếu muốn
                            spaceBetween: 30
                        }
                    },
                    pagination: {
                        el: '.fleet-slider .swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.fleet-slider .swiper-button-next',
                        prevEl: '.fleet-slider .swiper-button-prev',
                    },
                    // autoplay: {
                    //     delay: 6000,
                    //     disableOnInteraction: false,
                    // },
                });
            }
        });
    </script>
@endpush
