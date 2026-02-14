<x-client.layout :title="__('client.home.meta_title')" body-class="bg-neutral-50">
    @push('styles')
        <style>
            .hero-bg {
                background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.4)), url('/userfiles/files/city_imgs/sapa.jpg');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
            }

            @media (max-width: 768px) {
                .hero-bg {
                    background-attachment: scroll;
                }
            }

            .text-shadow {
                text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            }
        </style>
    @endpush

    {{-- 1. HERO SECTION & SEARCH --}}
    <section class="relative min-h-[600px] md:min-h-[700px] hero-bg flex flex-col justify-center items-center text-center px-4 py-16 pt-24 md:pt-20">

        <div class="relative z-10 w-full max-w-5xl space-y-4 md:space-y-6">
            <span class="inline-block py-1.5 px-4 rounded-md bg-white/15 text-white border border-white/20 text-xs md:text-sm font-semibold tracking-wider mb-2">
                #1 NỀN TẢNG ĐẶT VÉ XE KHÁCH
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold text-white text-shadow leading-tight">
                King Express Bus & Tour
            </h1>
            <p class="text-base md:text-lg lg:text-xl text-gray-200 max-w-2xl mx-auto px-2">
                Kết nối hàng nghìn chuyến xe chất lượng cao trên khắp Việt Nam. Đặt vé dễ dàng, thanh toán an toàn.
            </p>

            {{-- Search Component --}}
            <div class="mt-8 md:mt-10 w-full text-left">
                <x-client.search-bar submit-label="Tìm chuyến xe ngay" />
            </div>
        </div>
    </section>

    {{-- 1.5. BOOKING PROCESS --}}
    <section class="py-12 md:py-16 lg:py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-10 md:mb-16">
                <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-semibold text-neutral-800 uppercase tracking-wide">
                    Dễ dàng đặt vé xe tại King Express Bus
                </h2>
                <div class="w-16 h-1 bg-primary-600 mx-auto mt-4"></div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 lg:gap-8 relative">
                {{-- Connector Line (Desktop) --}}
                <div class="hidden md:block absolute top-[48px] left-[12.5%] right-[12.5%] h-px bg-neutral-200 -z-10"></div>

                {{-- Step 1 --}}
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 md:w-20 lg:w-24 md:h-20 lg:h-24 rounded-full bg-primary-50 flex items-center justify-center mb-4 md:mb-6 border-2 md:border-4 border-white relative z-10">
                        <i class="fa-solid fa-location-dot text-xl md:text-2xl lg:text-3xl text-primary-600"></i>
                    </div>
                    <h3 class="text-base md:text-lg lg:text-xl font-semibold text-neutral-800 mb-2 md:mb-3">Tìm kiếm</h3>
                    <p class="text-neutral-500 text-xs md:text-sm lg:text-base leading-relaxed max-w-[150px] md:max-w-[200px]">
                        Chọn thông tin hành trình ấn "Tìm chuyến"
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 md:w-20 lg:w-24 md:h-20 lg:h-24 rounded-full bg-primary-50 flex items-center justify-center mb-4 md:mb-6 border-2 md:border-4 border-white relative z-10">
                        <i class="fa-solid fa-bus text-xl md:text-2xl lg:text-3xl text-primary-600"></i>
                    </div>
                    <h3 class="text-base md:text-lg lg:text-xl font-semibold text-neutral-800 mb-2 md:mb-3">Chọn chuyến</h3>
                    <p class="text-neutral-500 text-xs md:text-sm lg:text-base leading-relaxed max-w-[150px] md:max-w-[200px]">
                        Lựa chọn chuyến phù hợp và điền thông tin cá nhân
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 md:w-20 lg:w-24 md:h-20 lg:h-24 rounded-full bg-primary-50 flex items-center justify-center mb-4 md:mb-6 border-2 md:border-4 border-white relative z-10">
                        <i class="fa-regular fa-credit-card text-xl md:text-2xl lg:text-3xl text-primary-600"></i>
                    </div>
                    <h3 class="text-base md:text-lg lg:text-xl font-semibold text-neutral-800 mb-2 md:mb-3">Thanh toán</h3>
                    <p class="text-neutral-500 text-xs md:text-sm lg:text-base leading-relaxed max-w-[150px] md:max-w-[200px]">
                        Tiến hành thanh toán online hoặc giữ chỗ trước
                    </p>
                </div>

                {{-- Step 4 --}}
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 md:w-20 lg:w-24 md:h-20 lg:h-24 rounded-full bg-primary-50 flex items-center justify-center mb-4 md:mb-6 border-2 md:border-4 border-white relative z-10">
                        <i class="fa-solid fa-ticket text-xl md:text-2xl lg:text-3xl text-primary-600"></i>
                    </div>
                    <h3 class="text-base md:text-lg lg:text-xl font-semibold text-neutral-800 mb-2 md:mb-3">Nhận vé</h3>
                    <p class="text-neutral-500 text-xs md:text-sm lg:text-base leading-relaxed max-w-[150px] md:max-w-[200px]">
                        Nhận mã vé, xác nhận và lên xe!
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. SERVICE HIGHLIGHTS (Static Data) --}}
    <section class="py-10 md:py-16 lg:py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-8 lg:gap-16">

                {{-- Left: Experience Badge --}}
                <div class="flex items-center gap-4 md:gap-6 w-full lg:w-auto justify-center lg:justify-start">
                    <div class="relative">
                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full border-4 border-primary-50 bg-white flex flex-col items-center justify-center text-primary-600 shadow-soft">
                            <span class="text-2xl md:text-3xl font-semibold leading-none">07</span>
                            <span class="text-[9px] md:text-[10px] uppercase font-semibold tracking-wider mt-1 text-neutral-400">Năm</span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl lg:text-2xl font-semibold text-neutral-800 mb-1">07 năm kinh nghiệm</h3>
                        <p class="text-neutral-500 text-xs md:text-sm lg:text-base">Tự hào phục vụ hơn <span class="font-semibold text-primary-600">10.000+</span> lượt khách mỗi năm</p>
                    </div>
                </div>

                {{-- Right: Features Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4 lg:gap-6 w-full lg:w-auto lg:flex-1">
                    {{-- Item 1 --}}
                    <div class="flex items-center gap-3 md:gap-4 p-3 md:p-4 rounded-lg bg-neutral-50 border border-neutral-200 hover:border-neutral-300 transition-colors duration-200">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-md bg-primary-50 text-primary-600 flex items-center justify-center text-lg md:text-xl shrink-0">
                            <i class="fa-solid fa-headset"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-neutral-800 text-sm md:text-base">Hỗ trợ tận tâm 24/7</h4>
                            <p class="text-[10px] md:text-xs text-neutral-500 mt-0.5">Luôn sẵn sàng giải đáp</p>
                        </div>
                    </div>

                    {{-- Item 2 --}}
                    <div class="flex items-center gap-3 md:gap-4 p-3 md:p-4 rounded-lg bg-neutral-50 border border-neutral-200 hover:border-neutral-300 transition-colors duration-200">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-md bg-primary-50 text-primary-600 flex items-center justify-center text-lg md:text-xl shrink-0">
                            <i class="fa-solid fa-route"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-neutral-800 text-sm md:text-base">Hành trình đa dạng</h4>
                            <p class="text-[10px] md:text-xs text-neutral-500 mt-0.5">Kết nối mọi miền</p>
                        </div>
                    </div>

                    {{-- Item 3 --}}
                    <div class="flex items-center gap-3 md:gap-4 p-3 md:p-4 rounded-lg bg-neutral-50 border border-neutral-200 hover:border-neutral-300 transition-colors duration-200">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-md bg-primary-50 text-primary-600 flex items-center justify-center text-lg md:text-xl shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-neutral-800 text-sm md:text-base">Đảm bảo chất lượng</h4>
                            <p class="text-[10px] md:text-xs text-neutral-500 mt-0.5">Dịch vụ tiêu chuẩn 5 sao</p>
                        </div>
                    </div>

                    {{-- Item 4 --}}
                    <div class="flex items-center gap-3 md:gap-4 p-3 md:p-4 rounded-lg bg-neutral-50 border border-neutral-200 hover:border-neutral-300 transition-colors duration-200">
                        <div class="w-10 h-10 md:w-12 md:h-12 rounded-md bg-primary-50 text-primary-600 flex items-center justify-center text-lg md:text-xl shrink-0">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-neutral-800 text-sm md:text-base">Tối ưu chi phí</h4>
                            <p class="text-[10px] md:text-xs text-neutral-500 mt-0.5">Giá vé cạnh tranh nhất</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- 3. POPULAR ROUTES (Dynamic Data) --}}
    @if ($popularRoutes->isNotEmpty())
        <section class="py-12 md:py-16 lg:py-20 bg-neutral-50">
            <div class="container mx-auto px-4">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 md:mb-10 gap-3">
                    <div class="space-y-1 md:space-y-2">
                        <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-semibold text-neutral-800">Tuyến đường
                            <span class="text-primary-600">Phổ biến</span></h2>
                        <p class="text-neutral-500 text-xs md:text-sm lg:text-base">Các tuyến đường được khách hàng yêu thích nhất.</p>
                    </div>
                    <a href="{{ route('client.routes.search') }}"
                        class="hidden md:inline-flex items-center gap-2 text-primary-600 font-semibold hover:text-primary-700 transition-colors duration-200">
                        Xem tất cả <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                    @foreach ($popularRoutes as $route)
                        <a href="{{ route('client.routes.show', ['slug' => $route->slug]) }}"
                            class="group block h-full bg-white rounded-lg overflow-hidden shadow-soft hover:shadow-card transition-shadow duration-200 border border-neutral-200 hover:border-neutral-300">

                            {{-- Image Container --}}
                            <div class="relative h-40 md:h-48 overflow-hidden">
                                <img src="{{ $route->thumbnail_url ?? '/userfiles/files/city_imgs/ha-noi.jpg' }}"
                                    alt="{{ $route->name }}"
                                    class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

                                {{-- Route Info on Image --}}
                                <div class="absolute bottom-2 md:bottom-3 left-2 md:left-3 right-2 md:right-3 text-white">
                                    <div class="flex justify-between items-end">
                                        <div>
                                            <p class="text-[10px] md:text-xs font-semibold uppercase tracking-wider text-white/80 mb-1 flex items-center gap-1">
                                                <i class="fa-solid fa-road"></i>
                                                {{ $route->distance_km ? $route->distance_km . 'km' : 'Liên hệ' }}
                                            </p>
                                            <h3 class="text-sm md:text-lg font-semibold truncate pr-2">
                                                {{ $route->name }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 md:p-5">
                                <div class="flex justify-between items-center mb-3 md:mb-4">
                                    <span class="text-xs md:text-sm text-neutral-500 bg-neutral-100 px-2 py-1 rounded-md"><i
                                            class="fa-regular fa-clock mr-1"></i>
                                        {{ $route->duration ?? 'N/A' }}</span>
                                    <span class="text-xs md:text-sm text-neutral-500 bg-neutral-100 px-2 py-1 rounded-md"><i
                                            class="fa-solid fa-bus mr-1"></i>
                                        {{ $route->trip_count ?? 0 }} chuyến</span>
                                </div>
                                <div class="pt-3 md:pt-4 border-t border-neutral-100 flex justify-between items-center">
                                    <span class="text-[10px] md:text-xs text-neutral-500">Giá chỉ từ</span>
                                    <span class="text-base md:text-lg font-semibold text-accent-500">
                                        {{ $route->min_price > 0 ? number_format($route->min_price) . 'đ' : 'Liên hệ' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-6 md:mt-8 text-center md:hidden">
                    <a href="{{ route('client.routes.search') }}"
                        class="inline-block px-6 py-3 bg-white border border-neutral-300 rounded-md font-semibold text-neutral-700 hover:bg-neutral-50 transition-colors duration-200 w-full text-sm">
                        Xem tất cả tuyến đường
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- 4. FEATURED BUSES (Single-tenant: Show buses instead of companies) --}}
    @if ($featuredBuses->isNotEmpty())
        <section class="py-12 md:py-16 lg:py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-10 md:mb-16">
                    <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-semibold text-neutral-800 mb-3 md:mb-4">Đội xe
                        <span class="text-primary-600">Chất lượng</span>
                    </h2>
                    <p class="text-neutral-500 text-xs md:text-sm lg:text-base">Xe chất lượng cao, đầy đủ tiện nghi cho hành trình thoải mái của bạn.</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 md:gap-4 lg:gap-6">
                    @foreach ($featuredBuses as $bus)
                        <div class="flex flex-col items-center justify-center p-4 md:p-6 bg-neutral-50 rounded-lg border border-neutral-200 hover:border-neutral-300 hover:shadow-card transition-shadow duration-200">
                            <img src="{{ $bus->thumbnail_url ?? '/userfiles/files/web%20information/logo.jpg' }}"
                                alt="{{ $bus->name }}"
                                class="w-16 h-16 md:w-20 lg:w-24 md:h-20 lg:h-24 object-cover rounded-md mb-3 md:mb-4">
                            <h4 class="font-semibold text-neutral-800 text-center text-xs md:text-sm lg:text-base line-clamp-2">
                                {{ $bus->name }}
                            </h4>
                            <p class="text-[10px] md:text-xs text-neutral-500 mt-1">{{ $bus->model_name ?? 'N/A' }} ·
                                {{ $bus->seat_count }} chỗ</p>
                            @if (!empty($bus->services) && count($bus->services) > 0)
                                <div class="flex flex-wrap gap-1 mt-2 justify-center">
                                    @foreach (array_slice($bus->services, 0, 3) as $service)
                                        <span class="text-[8px] md:text-[10px] px-1.5 py-0.5 bg-primary-50 text-primary-700 rounded">{{ $service }}</span>
                                    @endforeach
                                    @if (count($bus->services) > 3)
                                        <span class="text-[8px] md:text-[10px] px-1.5 py-0.5 bg-neutral-100 text-neutral-500 rounded">+{{ count($bus->services) - 3 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- 5. TESTIMONIALS (Static UI) --}}
    <section class="py-12 md:py-16 lg:py-20 bg-primary-700 text-white relative overflow-hidden">
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-10 md:mb-16">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold mb-3 md:mb-4">Khách hàng nói gì về chúng tôi?</h2>
                <div class="w-16 h-1 bg-accent-500 mx-auto"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                <div class="bg-white/10 p-5 md:p-6 lg:p-8 rounded-lg border border-white/10">
                    <div class="flex gap-1 text-accent-400 mb-3 md:mb-4">
                        <i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i>
                    </div>
                    <p class="text-gray-200 mb-4 md:mb-6 italic text-sm md:text-base leading-relaxed">"Trải nghiệm đặt vé rất mượt mà, giao diện dễ sử dụng. Tôi thích cách hiển thị đầy đủ tiện ích của từng xe."</p>
                    <div class="flex items-center gap-3 md:gap-4">
                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-md bg-primary-500 flex items-center justify-center font-semibold text-sm md:text-base">A</div>
                        <div>
                            <h4 class="font-semibold text-sm md:text-base">Nguyễn Văn An</h4>
                            <p class="text-[10px] md:text-xs text-gray-300">Khách hàng thân thiết</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 p-5 md:p-6 lg:p-8 rounded-lg border border-white/10">
                    <div class="flex gap-1 text-accent-400 mb-3 md:mb-4">
                        <i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i>
                    </div>
                    <p class="text-gray-200 mb-4 md:mb-6 italic text-sm md:text-base leading-relaxed">"Xe chạy đúng giờ, tài xế thân thiện. Giá vé trên website thường rẻ hơn mua trực tiếp tại bến."</p>
                    <div class="flex items-center gap-3 md:gap-4">
                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-md bg-green-600 flex items-center justify-center font-semibold text-sm md:text-base">L</div>
                        <div>
                            <h4 class="font-semibold text-sm md:text-base">Lê Thị Bình</h4>
                            <p class="text-[10px] md:text-xs text-gray-300">Hà Nội</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 p-5 md:p-6 lg:p-8 rounded-lg border border-white/10">
                    <div class="flex gap-1 text-accent-400 mb-3 md:mb-4">
                        <i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star text-sm md:text-base"></i><i class="fa-solid fa-star-half-stroke text-sm md:text-base"></i>
                    </div>
                    <p class="text-gray-200 mb-4 md:mb-6 italic text-sm md:text-base leading-relaxed">"Hỗ trợ khách hàng rất nhiệt tình. Tôi từng bị nhầm ngày và được hỗ trợ đổi vé nhanh chóng."</p>
                    <div class="flex items-center gap-3 md:gap-4">
                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-md bg-purple-600 flex items-center justify-center font-semibold text-sm md:text-base">T</div>
                        <div>
                            <h4 class="font-semibold text-sm md:text-base">Trần Minh Long</h4>
                            <p class="text-[10px] md:text-xs text-gray-300">Đà Nẵng</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. CALL TO ACTION --}}
    <section class="py-10 md:py-16 lg:py-20 bg-neutral-50">
        <div class="container mx-auto px-4">
            <div class="bg-primary-600 rounded-lg p-6 md:p-10 lg:p-16 text-center relative overflow-hidden">
                <div class="relative z-10 space-y-4 md:space-y-6">
                    <h2 class="text-2xl md:text-3xl lg:text-5xl font-semibold text-white">Sẵn sàng cho chuyến đi tiếp theo?</h2>
                    <p class="text-primary-100 text-sm md:text-base lg:text-lg max-w-2xl mx-auto">Đặt vé ngay hôm nay để nhận ưu đãi lên đến 30% cho khách hàng mới.</p>
                    <div class="pt-2 md:pt-4">
                        <a href="#top"
                            class="inline-flex items-center gap-2 md:gap-3 px-6 md:px-8 py-3 md:py-4 bg-accent-500 text-white rounded-md font-semibold text-sm md:text-lg hover:bg-accent-600 transition-colors duration-200">
                            <i class="fa-solid fa-ticket"></i> Đặt vé ngay
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-client.layout>
