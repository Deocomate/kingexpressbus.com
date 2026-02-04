{{-- ===== resources/views/client/routes/index.blade.php ===== --}}
<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.routes.index.meta_title')" :description="$description ?? ''">

    @push('styles')
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <style>
            /* Hero Section */
            .hero-routes {
                background: linear-gradient(135deg, rgba(15, 23, 42, 0.85) 0%, rgba(30, 41, 59, 0.8) 100%),
                    url('/userfiles/files/city_imgs/sapa.jpg');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                min-height: 85vh;
                position: relative;
            }

            .hero-routes::before {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 120px;
                background: linear-gradient(to top, rgba(249, 250, 251, 1) 0%, transparent 100%);
                pointer-events: none;
            }

            /* Typing Cursor */
            .typing-cursor {
                animation: blink 1s step-end infinite;
            }

            @keyframes blink {
                50% {
                    opacity: 0;
                }
            }

            /* Modern Card Effects */
            .route-card {
                background: #ffffff;
                border-radius: 20px;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                overflow: hidden;
                border: 1px solid #e5e7eb;
            }

            .route-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.2);
                border-color: #fbbf24;
            }

            .route-card:hover .route-card-image {
                transform: scale(1.1);
            }

            .route-card-image-wrapper {
                position: relative;
                height: 200px;
                overflow: hidden;
            }

            .route-card-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.6s ease;
            }

            .route-card-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(0deg, rgba(0, 0, 0, 0.75) 0%, transparent 60%);
            }

            /* Stats Cards */
            .stat-card {
                background: #ffffff;
                border-radius: 20px;
                padding: 24px;
                text-align: center;
                transition: all 0.3s ease;
                border: 1px solid #e5e7eb;
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15);
            }

            .stat-icon {
                width: 60px;
                height: 60px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px;
                font-size: 24px;
            }

            /* Province Filter Tabs */
            .province-tabs {
                display: flex;
                gap: 8px;
                overflow-x: auto;
                padding-bottom: 8px;
                scrollbar-width: thin;
            }

            .province-tabs::-webkit-scrollbar {
                height: 4px;
            }

            .province-tabs::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.1);
                border-radius: 4px;
            }

            .province-tabs::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.3);
                border-radius: 4px;
            }

            .province-tab {
                padding: 10px 20px;
                border-radius: 9999px;
                font-size: 14px;
                font-weight: 600;
                white-space: nowrap;
                transition: all 0.2s ease;
                border: 1px solid #e5e7eb;
                background: #ffffff;
                color: #374151;
                cursor: pointer;
            }

            .province-tab:hover {
                background: #f3f4f6;
                border-color: #d1d5db;
            }

            .province-tab.active {
                background: linear-gradient(135deg, #fbbf24, #f59e0b);
                border-color: transparent;
                color: #0f172a;
            }

            /* Quick Route Pills */
            .quick-route-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 16px;
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(4px);
                border: 1px solid rgba(255, 255, 255, 0.25);
                border-radius: 9999px;
                color: #fff;
                font-size: 13px;
                font-weight: 500;
                transition: all 0.2s ease;
                white-space: nowrap;
            }

            .quick-route-pill:hover {
                background: rgba(251, 191, 36, 0.3);
                border-color: rgba(251, 191, 36, 0.6);
            }

            /* Skeleton Loading */
            .skeleton {
                background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                background-size: 200% 100%;
                animation: skeleton-loading 1.5s infinite;
            }

            @keyframes skeleton-loading {
                0% {
                    background-position: 200% 0;
                }

                100% {
                    background-position: -200% 0;
                }

                justify-content: center;
                margin: 0 auto 16px;
                font-size: 24px;
            }

            /* Feature Cards */
            .feature-card {
                padding: 24px;
                border-radius: 20px;
                transition: all 0.3s ease;
                border: 1px solid transparent;
                background: #ffffff;
            }

            .feature-card:hover {
                background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
                border-color: #fcd34d;
                transform: translateX(8px);
            }

            .feature-card:hover .feature-icon-wrapper {
                transform: scale(1.1) rotate(-5deg);
            }

            .feature-icon-wrapper {
                width: 56px;
                height: 56px;
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                transition: transform 0.3s ease;
            }

            /* Floating Animation */
            @keyframes float {

                0%,
                100% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-12px);
                }
            }

            .float-animation {
                animation: float 4s ease-in-out infinite;
            }

            /* Price Badge */
            .price-badge {
                background: linear-gradient(135deg, #fbbf24, #f59e0b);
                color: #0f172a;
                padding: 6px 14px;
                border-radius: 9999px;
                font-weight: 700;
                font-size: 14px;
            }

            /* CTA Section */
            .cta-section {
                background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #ea580c 100%);
                position: relative;
                overflow: hidden;
            }

            .cta-section::before {
                content: '';
                position: absolute;
                width: 300px;
                height: 300px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                top: -100px;
                right: -100px;
            }

            .cta-section::after {
                content: '';
                position: absolute;
                width: 200px;
                height: 200px;
                background: rgba(255, 255, 255, 0.08);
                border-radius: 50%;
                bottom: -80px;
                left: -80px;
            }
        </style>
    @endpush

    {{-- Hero Section --}}
    <section class="hero-routes flex flex-col justify-center items-center px-4 py-28 lg:py-32" x-data="routesHeroTypewriter">
        <div class="container relative z-10 w-full max-w-5xl text-center space-y-8" data-aos="fade-up"
            data-aos-duration="1000">

            {{-- Badge --}}
            <div data-aos="fade-down" data-aos-delay="200">
                <span
                    class="inline-flex items-center gap-2 py-2 px-5 rounded-full bg-yellow-400/20 text-yellow-300 border border-yellow-400/40 text-sm font-bold tracking-wider backdrop-blur-sm">
                    <i class="fa-solid fa-route"></i>
                    {{ __('client.routes.index.badge', ['default' => 'TÌM TUYẾN XE']) }}
                </span>
            </div>

            {{-- Hero Title --}}
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white leading-tight min-h-[80px]">
                <span x-text="text"></span><span class="typing-cursor text-yellow-400">|</span>
            </h1>

            {{-- Subtitle --}}
            <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="400">
                {{ __('client.routes.index.hero_subtitle', ['default' => 'Hơn 100+ tuyến đường chất lượng cao đang chờ đón bạn']) }}
            </p>

            {{-- Search Bar --}}
            <div class="mt-10 w-full text-left" data-aos="fade-up" data-aos-delay="500">
                <x-client.search-bar :search-data="$searchData" :submit-label="__('client.route_show.search_submit_label', ['default' => 'Tìm chuyến'])" />
            </div>

            {{-- Quick Route Suggestions --}}
            @if (isset($quickRouteSuggestions) && $quickRouteSuggestions->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2 justify-center" data-aos="fade-up" data-aos-delay="600">
                    <span class="text-white/60 text-sm font-medium mr-2 self-center">
                        {{ __('client.routes.index.popular_searches', ['default' => 'Phổ biến:']) }}
                    </span>
                    @foreach ($quickRouteSuggestions->take(4) as $suggestion)
                        <a href="{{ route('client.routes.show', ['slug' => $suggestion->slug]) }}"
                            class="quick-route-pill">
                            <i class="fa-solid fa-location-arrow text-yellow-400 text-xs"></i>
                            {{ $suggestion->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Scroll Indicator --}}
        <div class="absolute bottom-16 left-1/2 -translate-x-1/2 z-10">
            <div class="flex flex-col items-center text-white/60 animate-bounce">
                <span
                    class="text-xs font-medium mb-2">{{ __('client.routes.index.scroll_hint', ['default' => 'Cuộn xuống']) }}</span>
                <i class="fa-solid fa-chevron-down text-lg"></i>
            </div>
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-12 bg-gray-50 relative -mt-16 z-20">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6" data-aos="fade-up">
                <div class="stat-card" x-data="statsCounter(100, 2, 30)">
                    <div class="stat-icon bg-gradient-to-br from-yellow-400 to-amber-500 text-white">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <p class="text-3xl md:text-4xl font-extrabold text-gray-900">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-gray-500 text-sm mt-2 font-medium">
                        {{ __('client.routes.index.stat_routes', ['default' => 'Tuyến đường']) }}
                    </p>
                </div>
                <div class="stat-card" x-data="statsCounter(50, 1, 40)">
                    <div class="stat-icon bg-gradient-to-br from-blue-400 to-blue-600 text-white">
                        <i class="fa-solid fa-bus"></i>
                    </div>
                    <p class="text-3xl md:text-4xl font-extrabold text-gray-900">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-gray-500 text-sm mt-2 font-medium">
                        {{ __('client.routes.index.stat_fleet', ['default' => 'Đội xe']) }}
                    </p>
                </div>
                <div class="stat-card" x-data="statsCounter(10000, 200, 30, true)">
                    <div class="stat-icon bg-gradient-to-br from-emerald-400 to-emerald-600 text-white">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <p class="text-3xl md:text-4xl font-extrabold text-gray-900">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-gray-500 text-sm mt-2 font-medium">
                        {{ __('client.routes.index.stat_customers', ['default' => 'Khách hàng mỗi năm']) }}
                    </p>
                </div>
                <div class="stat-card" x-data="statsCounter(98, 2, 30)">
                    <div class="stat-icon bg-gradient-to-br from-purple-400 to-purple-600 text-white">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-3xl md:text-4xl font-extrabold text-gray-900">
                        <span x-text="displayCount">0</span>%
                    </p>
                    <p class="text-gray-500 text-sm mt-2 font-medium">
                        {{ __('client.routes.index.stat_satisfaction', ['default' => 'Hài lòng']) }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Popular Routes Section --}}
    @if (isset($popularRoutes) && $popularRoutes->isNotEmpty())
        <section class="py-16 md:py-24 bg-gray-50">
            <div class="container mx-auto px-4">

                {{-- Province Filter Tabs --}}
                @if (isset($provinces) && $provinces->isNotEmpty())
                    <div class="mb-10" data-aos="fade-up">
                        <div class="province-tabs">
                            <a href="{{ route('client.routes.index') }}"
                                class="province-tab {{ !$selectedProvince ? 'active' : '' }}">
                                <i class="fa-solid fa-globe mr-1"></i>
                                {{ __('client.routes.index.all_provinces', ['default' => 'Tất cả']) }}
                            </a>
                            @foreach ($provinces as $province)
                                <a href="{{ route('client.routes.index', ['province' => $province->id]) }}"
                                    class="province-tab {{ $selectedProvince == $province->id ? 'active' : '' }}">
                                    {{ $province->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
                {{-- Section Header --}}
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6"
                    data-aos="fade-right">
                    <div class="space-y-3">
                        <span
                            class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-yellow-100 text-yellow-700 text-sm font-semibold">
                            <i class="fa-solid fa-fire"></i>
                            {{ __('client.routes.index.trending_badge', ['default' => 'Được yêu thích']) }}
                        </span>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">
                            {{ __('client.routes.index.popular_title', ['default' => 'Tuyến đường']) }}
                            <span
                                class="text-yellow-500">{{ __('client.routes.index.popular_highlight', ['default' => 'Phổ biến']) }}</span>
                        </h2>
                        <p class="text-gray-500 text-base md:text-lg max-w-lg">
                            {{ __('client.routes.index.popular_subtitle', ['default' => 'Các tuyến đường được khách hàng yêu thích nhất']) }}
                        </p>
                    </div>
                    <a href="{{ route('client.routes.search') }}"
                        class="hidden md:inline-flex items-center gap-2 px-6 py-3 bg-gray-900 text-white rounded-full font-semibold hover:bg-gray-800 transition-all hover:gap-3 shadow-lg hover:shadow-xl">
                        {{ __('client.routes.index.view_all', ['default' => 'Xem tất cả']) }}
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                {{-- Routes Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($popularRoutes as $route)
                        @php
                            $minPrice = $route->min_price ?? 0;
                            $priceDisplay =
                                $minPrice > 0
                                    ? number_format($minPrice) . 'đ'
                                    : __('client.common.contact_price', ['default' => 'Liên hệ']);
                        @endphp
                        <a href="{{ route('client.routes.show', ['slug' => $route->slug]) }}"
                            class="route-card group block" data-aos="fade-up" data-aos-delay="{{ $loop->index * 80 }}">

                            {{-- Image --}}
                            <div class="route-card-image-wrapper">
                                <img src="{{ $route->thumbnail_url ?? '/userfiles/files/city_imgs/ha-noi.jpg' }}"
                                    alt="{{ $route->name }}" class="route-card-image" loading="lazy">
                                <div class="route-card-overlay"></div>

                                {{-- Trip Count Badge --}}
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/95 backdrop-blur-sm rounded-full text-xs font-bold text-gray-800 shadow-sm">
                                        <i class="fa-solid fa-bus text-yellow-500"></i>
                                        {{ $route->trip_count ?? 0 }}
                                        {{ __('client.routes.index.trips', ['default' => 'chuyến']) }}
                                    </span>
                                </div>

                                {{-- Route Name on Image --}}
                                <div class="absolute bottom-4 left-4 right-4">
                                    <h3
                                        class="text-lg font-bold text-white group-hover:text-yellow-300 transition-colors line-clamp-2">
                                        {{ $route->name }}
                                    </h3>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3 mb-4">
                                    <div class="flex items-center gap-2 text-gray-500 text-sm">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>{{ $route->duration ?? 'N/A' }}</span>
                                    </div>
                                    @if ($route->distance_km ?? false)
                                        <div class="flex items-center gap-2 text-gray-500 text-sm">
                                            <i class="fa-solid fa-road"></i>
                                            <span>{{ $route->distance_km }}km</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <span class="text-xs text-gray-400 uppercase tracking-wide font-medium">
                                        {{ __('client.routes.index.from', ['default' => 'Giá từ']) }}
                                    </span>
                                    <span class="price-badge">{{ $priceDisplay }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Mobile View All --}}
                <div class="mt-10 text-center md:hidden">
                    <a href="{{ route('client.routes.search') }}"
                        class="inline-flex items-center justify-center gap-2 w-full px-6 py-4 bg-gray-900 text-white rounded-2xl font-bold shadow-lg">
                        {{ __('client.routes.index.view_all_routes', ['default' => 'Xem tất cả tuyến đường']) }}
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Why Choose Us Section --}}
    <section class="py-20 bg-white relative overflow-hidden">
        {{-- Background Decoration --}}
        <div class="absolute inset-0 bg-gradient-to-br from-yellow-50/50 via-white to-blue-50/50"></div>
        <div class="absolute top-20 right-10 w-72 h-72 bg-yellow-400/10 rounded-full blur-3xl float-animation"></div>
        <div class="absolute bottom-20 left-10 w-64 h-64 bg-blue-400/10 rounded-full blur-3xl"
            style="animation: float 5s ease-in-out infinite 1s;"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                {{-- Text Content --}}
                <div class="space-y-8" data-aos="fade-right" data-aos-duration="1000">
                    <div class="space-y-4">
                        <span
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-yellow-100 to-amber-50 text-yellow-700 font-semibold text-sm border border-yellow-200">
                            <i class="fa-solid fa-award"></i>
                            {{ __('client.about.subtitle', ['default' => 'Về King Express Bus']) }}
                        </span>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 leading-tight">
                            {{ __('client.about.title', ['default' => 'Tại sao nên chọn chúng tôi?']) }}
                        </h2>
                        <p class="text-lg text-gray-600 leading-relaxed">
                            {{ __('client.about.description', ['default' => 'King Express Bus cam kết mang đến trải nghiệm hành trình an toàn, tiện nghi và đẳng cấp nhất.']) }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="feature-card flex items-start gap-5" data-aos="fade-up" data-aos-delay="100">
                            <div
                                class="feature-icon-wrapper bg-gradient-to-br from-yellow-400 to-amber-500 text-white shadow-lg shadow-yellow-200">
                                <i class="fa-solid fa-star text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ __('client.about.feature_1_title', ['default' => 'Dịch vụ 5 sao']) }}
                                </h4>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ __('client.about.feature_1_desc', ['default' => 'Hệ thống xe cabin VIP và limousine đời mới, được trang bị đầy đủ tiện nghi hiện đại.']) }}
                                </p>
                            </div>
                        </div>

                        <div class="feature-card flex items-start gap-5" data-aos="fade-up" data-aos-delay="200">
                            <div
                                class="feature-icon-wrapper bg-gradient-to-br from-blue-400 to-blue-600 text-white shadow-lg shadow-blue-200">
                                <i class="fa-solid fa-shield-halved text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ __('client.about.feature_2_title', ['default' => 'An toàn tuyệt đối']) }}
                                </h4>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ __('client.about.feature_2_desc', ['default' => 'Đội ngũ tài xế chuyên nghiệp, giàu kinh nghiệm. Kiểm tra kỹ thuật xe nghiêm ngặt.']) }}
                                </p>
                            </div>
                        </div>

                        <div class="feature-card flex items-start gap-5" data-aos="fade-up" data-aos-delay="300">
                            <div
                                class="feature-icon-wrapper bg-gradient-to-br from-emerald-400 to-emerald-600 text-white shadow-lg shadow-emerald-200">
                                <i class="fa-solid fa-headset text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 mb-2">
                                    {{ __('client.about.feature_3_title', ['default' => 'Hỗ trợ 24/7']) }}
                                </h4>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ __('client.about.feature_3_desc', ['default' => 'Tổng đài chăm sóc khách hàng hoạt động 24/7, sẵn sàng giải đáp mọi thắc mắc.']) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Image --}}
                <div class="relative" data-aos="fade-left" data-aos-duration="1000">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl group aspect-[4/3]">
                        <img src="/userfiles/files/city_imgs/sapa.jpg"
                            alt="{{ __('client.about.image_alt', ['default' => 'Nội thất xe King Express Bus']) }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent">
                        </div>
                    </div>

                    {{-- Floating Badge 1 --}}
                    <div
                        class="absolute -bottom-6 -left-4 bg-white p-5 rounded-2xl shadow-xl hidden lg:flex items-center gap-4 float-animation z-10">
                        <div
                            class="bg-gradient-to-br from-yellow-400 to-amber-500 p-4 rounded-xl text-white shadow-lg">
                            <i class="fa-solid fa-thumbs-up text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium">
                                {{ __('client.about.badge_subtitle', ['default' => 'Hài lòng']) }}</p>
                            <p class="text-xl font-extrabold text-gray-900">98% Review 5★</p>
                        </div>
                    </div>

                    {{-- Floating Badge 2 --}}
                    <div class="absolute -top-4 -right-4 bg-gradient-to-br from-blue-500 to-blue-700 p-5 rounded-2xl shadow-xl hidden lg:block text-white"
                        style="animation: float 4s ease-in-out infinite 0.5s;">
                        <div class="text-center">
                            <p class="text-3xl font-extrabold">7+</p>
                            <p class="text-xs font-medium opacity-90">
                                {{ __('client.about.years_experience', ['default' => 'Năm kinh nghiệm']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="cta-section py-16 md:py-20 relative">
        <div class="container mx-auto px-4 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left"
                data-aos="fade-up">
                <div class="space-y-3 max-w-xl">
                    <h2 class="text-2xl md:text-4xl font-extrabold text-gray-900">
                        {{ __('client.routes.index.cta_title', ['default' => 'Sẵn sàng cho chuyến đi tiếp theo?']) }}
                    </h2>
                    <p class="text-gray-800/80 text-lg">
                        {{ __('client.routes.index.cta_subtitle', ['default' => 'Đặt vé ngay hôm nay để nhận ưu đãi hấp dẫn']) }}
                    </p>
                </div>
                <a href="#top"
                    class="inline-flex items-center gap-3 px-10 py-5 bg-gray-900 text-white rounded-2xl font-bold text-lg hover:bg-gray-800 transform hover:scale-105 transition-all shadow-2xl group">
                    <i class="fa-solid fa-ticket group-hover:rotate-12 transition-transform"></i>
                    {{ __('client.routes.index.cta_button', ['default' => 'Đặt vé ngay']) }}
                </a>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            document.addEventListener('alpine:init', () => {
                // Stats Counter Component with IntersectionObserver
                Alpine.data('statsCounter', (target, step = 1, speed = 30, formatNumber = false) => ({
                    count: 0,
                    target: target,
                    step: step,
                    speed: speed,
                    formatNumber: formatNumber,
                    started: false,
                    get displayCount() {
                        return this.formatNumber ? this.count.toLocaleString('vi-VN') : this.count;
                    },
                    init() {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting && !this.started) {
                                    this.started = true;
                                    this.animate();
                                }
                            });
                        }, {
                            threshold: 0.3
                        });
                        observer.observe(this.$el);
                    },
                    animate() {
                        const interval = setInterval(() => {
                            if (this.count < this.target) {
                                this.count = Math.min(this.count + this.step, this.target);
                            } else {
                                clearInterval(interval);
                            }
                        }, this.speed);
                    }
                }));

                // Hero Typewriter Component
                Alpine.data('routesHeroTypewriter', () => ({
                    text: '',
                    textArray: [
                        '{{ __('client.routes.index.typing_1', ['default' => 'Vi vu khắp Việt Nam']) }}',
                        '{{ __('client.routes.index.typing_2', ['default' => 'Hà Nội - Sa Pa']) }}',
                        '{{ __('client.routes.index.typing_3', ['default' => '100+ tuyến đường']) }}',
                        '{{ __('client.routes.index.typing_4', ['default' => 'Xe chất lượng cao']) }}'
                    ],
                    textIndex: 0,
                    charIndex: 0,
                    typeSpeed: 80,
                    eraseSpeed: 40,
                    newTextDelay: 2500,
                    init() {
                        setTimeout(() => this.type(), 800);
                    },
                    type() {
                        if (this.charIndex < this.textArray[this.textIndex].length) {
                            this.text += this.textArray[this.textIndex].charAt(this.charIndex);
                            this.charIndex++;
                            setTimeout(() => this.type(), this.typeSpeed);
                        } else {
                            setTimeout(() => this.erase(), this.newTextDelay);
                        }
                    },
                    erase() {
                        if (this.charIndex > 0) {
                            this.text = this.textArray[this.textIndex].substring(0, this.charIndex - 1);
                            this.charIndex--;
                            setTimeout(() => this.erase(), this.eraseSpeed);
                        } else {
                            this.textIndex++;
                            if (this.textIndex >= this.textArray.length) this.textIndex = 0;
                            setTimeout(() => this.type(), this.typeSpeed + 800);
                        }
                    }
                }))
            })

            document.addEventListener('DOMContentLoaded', function() {
                AOS.init({
                    once: true,
                    offset: 50,
                    duration: 800,
                    easing: 'ease-out-cubic',
                });
            });
        </script>
    @endpush

</x-client.layout>
