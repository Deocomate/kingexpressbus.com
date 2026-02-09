{{-- ===== resources/views/client/routes/index.blade.php ===== --}}
<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.routes.index.meta_title')" :description="$description ?? ''">

    @push('styles')
        <style>
            /* Hero Section */
            .hero-routes {
                background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.6)),
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

            @media (max-width: 768px) {
                .hero-routes {
                    background-attachment: scroll;
                }
            }

            /* Route Card */
            .route-card {
                background: #ffffff;
                border-radius: 8px;
                transition: box-shadow 0.2s ease;
                overflow: hidden;
                border: 1px solid #e5e7eb;
            }

            .route-card:hover {
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
                border-color: #d1d5db;
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
            }

            .route-card-overlay {
                position: absolute;
                inset: 0;
                background: linear-gradient(0deg, rgba(0, 0, 0, 0.65) 0%, transparent 60%);
            }

            /* Stats Cards */
            .stat-card {
                background: #ffffff;
                border-radius: 8px;
                padding: 24px;
                text-align: center;
                border: 1px solid #e5e7eb;
            }

            .stat-icon {
                width: 56px;
                height: 56px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px;
                font-size: 22px;
            }

            /* Province Filter Tabs */
            .province-tabs {
                display: flex;
                gap: 8px;
                overflow-x: auto;
                padding-bottom: 8px;
                scrollbar-width: thin;
            }

            .province-tab {
                padding: 10px 20px;
                border-radius: 6px;
                font-size: 14px;
                font-weight: 600;
                white-space: nowrap;
                transition: background 0.2s ease;
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
                background: #1565C0;
                border-color: #1565C0;
                color: #ffffff;
            }

            /* Quick Route Pills */
            .quick-route-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 16px;
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.25);
                border-radius: 6px;
                color: #fff;
                font-size: 13px;
                font-weight: 500;
                transition: background 0.2s ease;
                white-space: nowrap;
            }

            .quick-route-pill:hover {
                background: rgba(255, 255, 255, 0.25);
            }

            /* Price Badge */
            .price-badge {
                background: #D97706;
                color: #ffffff;
                padding: 6px 14px;
                border-radius: 6px;
                font-weight: 600;
                font-size: 14px;
            }

            /* Feature Cards */
            .feature-card {
                padding: 24px;
                border-radius: 8px;
                transition: background 0.2s ease;
                border: 1px solid transparent;
                background: #ffffff;
            }

            .feature-card:hover {
                background: #f8f9fa;
                border-color: #e5e7eb;
            }

            .feature-icon-wrapper {
                width: 56px;
                height: 56px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            /* CTA Section */
            .cta-section {
                background: #1565C0;
                position: relative;
                overflow: hidden;
            }
        </style>
    @endpush

    {{-- Hero Section --}}
    <section class="hero-routes flex flex-col justify-center items-center px-4 py-28 lg:py-32">
        <div class="container relative z-10 w-full max-w-5xl text-center space-y-8">

            {{-- Badge --}}
            <div>
                <span class="inline-flex items-center gap-2 py-2 px-5 rounded-md bg-white/15 text-white border border-white/20 text-sm font-semibold tracking-wider">
                    <i class="fa-solid fa-route"></i>
                    {{ __('client.routes.index.badge', ['default' => 'TÌM TUYẾN XE']) }}
                </span>
            </div>

            {{-- Hero Title --}}
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-semibold text-white leading-tight">
                {{ __('client.routes.index.typing_1', ['default' => 'Vi vu khắp Việt Nam']) }}
            </h1>

            {{-- Subtitle --}}
            <p class="text-lg md:text-xl text-gray-200 max-w-2xl mx-auto">
                {{ __('client.routes.index.hero_subtitle', ['default' => 'Hơn 100+ tuyến đường chất lượng cao đang chờ đón bạn']) }}
            </p>

            {{-- Search Bar --}}
            <div class="mt-10 w-full text-left">
                <x-client.search-bar :search-data="$searchData" :submit-label="__('client.route_show.search_submit_label', ['default' => 'Tìm chuyến'])" />
            </div>

            {{-- Quick Route Suggestions --}}
            @if (isset($quickRouteSuggestions) && $quickRouteSuggestions->isNotEmpty())
                <div class="mt-6 flex flex-wrap gap-2 justify-center">
                    <span class="text-white/60 text-sm font-medium mr-2 self-center">
                        {{ __('client.routes.index.popular_searches', ['default' => 'Phổ biến:']) }}
                    </span>
                    @foreach ($quickRouteSuggestions->take(4) as $suggestion)
                        <a href="{{ route('client.routes.show', ['slug' => $suggestion->slug]) }}"
                            class="quick-route-pill">
                            <i class="fa-solid fa-location-arrow text-white/70 text-xs"></i>
                            {{ $suggestion->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Stats Section --}}
    <section class="py-12 bg-neutral-50 relative -mt-16 z-20">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <div class="stat-card" x-data="statsCounter(100, 2, 30)">
                    <div class="stat-icon bg-accent-100 text-accent-600">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <p class="text-3xl md:text-4xl font-semibold text-neutral-800">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-neutral-500 text-sm mt-2 font-medium">
                        {{ __('client.routes.index.stat_routes', ['default' => 'Tuyến đường']) }}
                    </p>
                </div>
                <div class="stat-card" x-data="statsCounter(50, 1, 40)">
                    <div class="stat-icon bg-primary-50 text-primary-600">
                        <i class="fa-solid fa-bus"></i>
                    </div>
                    <p class="text-3xl md:text-4xl font-semibold text-neutral-800">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-neutral-500 text-sm mt-2 font-medium">
                        {{ __('client.routes.index.stat_fleet', ['default' => 'Đội xe']) }}
                    </p>
                </div>
                <div class="stat-card" x-data="statsCounter(10000, 200, 30, true)">
                    <div class="stat-icon bg-emerald-50 text-emerald-600">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <p class="text-3xl md:text-4xl font-semibold text-neutral-800">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-neutral-500 text-sm mt-2 font-medium">
                        {{ __('client.routes.index.stat_customers', ['default' => 'Khách hàng mỗi năm']) }}
                    </p>
                </div>
                <div class="stat-card" x-data="statsCounter(98, 2, 30)">
                    <div class="stat-icon bg-purple-50 text-purple-600">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <p class="text-3xl md:text-4xl font-semibold text-neutral-800">
                        <span x-text="displayCount">0</span>%
                    </p>
                    <p class="text-neutral-500 text-sm mt-2 font-medium">
                        {{ __('client.routes.index.stat_satisfaction', ['default' => 'Hài lòng']) }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Popular Routes Section --}}
    @if (isset($popularRoutes) && $popularRoutes->isNotEmpty())
        <section class="py-16 md:py-24 bg-neutral-50">
            <div class="container mx-auto px-4">

                {{-- Province Filter Tabs --}}
                @if (isset($provinces) && $provinces->isNotEmpty())
                    <div class="mb-10">
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
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6">
                    <div class="space-y-3">
                        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-md bg-primary-50 text-primary-700 text-sm font-semibold">
                            <i class="fa-solid fa-fire"></i>
                            {{ __('client.routes.index.trending_badge', ['default' => 'Được yêu thích']) }}
                        </span>
                        <h2 class="text-3xl md:text-4xl font-semibold text-neutral-800">
                            {{ __('client.routes.index.popular_title', ['default' => 'Tuyến đường']) }}
                            <span class="text-primary-600">{{ __('client.routes.index.popular_highlight', ['default' => 'Phổ biến']) }}</span>
                        </h2>
                        <p class="text-neutral-500 text-base md:text-lg max-w-lg">
                            {{ __('client.routes.index.popular_subtitle', ['default' => 'Các tuyến đường được khách hàng yêu thích nhất']) }}
                        </p>
                    </div>
                    <a href="{{ route('client.routes.search') }}"
                        class="hidden md:inline-flex items-center gap-2 px-6 py-3 bg-neutral-800 text-white rounded-md font-semibold hover:bg-neutral-700 transition-colors duration-200">
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
                            class="route-card group block">

                            {{-- Image --}}
                            <div class="route-card-image-wrapper">
                                <img src="{{ $route->thumbnail_url ?? '/userfiles/files/city_imgs/ha-noi.jpg' }}"
                                    alt="{{ $route->name }}" class="route-card-image" loading="lazy">
                                <div class="route-card-overlay"></div>

                                {{-- Trip Count Badge --}}
                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/95 rounded-md text-xs font-semibold text-neutral-800">
                                        <i class="fa-solid fa-bus text-primary-600"></i>
                                        {{ $route->trip_count ?? 0 }}
                                        {{ __('client.routes.index.trips', ['default' => 'chuyến']) }}
                                    </span>
                                </div>

                                {{-- Route Name on Image --}}
                                <div class="absolute bottom-4 left-4 right-4">
                                    <h3 class="text-lg font-semibold text-white line-clamp-2">
                                        {{ $route->name }}
                                    </h3>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-5">
                                <div class="flex items-center justify-between gap-3 mb-4">
                                    <div class="flex items-center gap-2 text-neutral-500 text-sm">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>{{ $route->duration ?? 'N/A' }}</span>
                                    </div>
                                    @if ($route->distance_km ?? false)
                                        <div class="flex items-center gap-2 text-neutral-500 text-sm">
                                            <i class="fa-solid fa-road"></i>
                                            <span>{{ $route->distance_km }}km</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-neutral-100">
                                    <span class="text-xs text-neutral-400 uppercase tracking-wide font-medium">
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
                        class="inline-flex items-center justify-center gap-2 w-full px-6 py-4 bg-neutral-800 text-white rounded-md font-semibold">
                        {{ __('client.routes.index.view_all_routes', ['default' => 'Xem tất cả tuyến đường']) }}
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Why Choose Us Section --}}
    <section class="py-20 bg-white relative">
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                {{-- Text Content --}}
                <div class="space-y-8">
                    <div class="space-y-4">
                        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary-50 text-primary-700 font-semibold text-sm border border-primary-100">
                            <i class="fa-solid fa-award"></i>
                            {{ __('client.about.subtitle', ['default' => 'Về King Express Bus']) }}
                        </span>
                        <h2 class="text-3xl md:text-4xl font-semibold text-neutral-800 leading-tight">
                            {{ __('client.about.title', ['default' => 'Tại sao nên chọn chúng tôi?']) }}
                        </h2>
                        <p class="text-lg text-neutral-600 leading-relaxed">
                            {{ __('client.about.description', ['default' => 'King Express Bus cam kết mang đến trải nghiệm hành trình an toàn, tiện nghi và đẳng cấp nhất.']) }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="feature-card flex items-start gap-5">
                            <div class="feature-icon-wrapper bg-accent-100 text-accent-600">
                                <i class="fa-solid fa-star text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-semibold text-neutral-800 mb-2">
                                    {{ __('client.about.feature_1_title', ['default' => 'Dịch vụ 5 sao']) }}
                                </h4>
                                <p class="text-neutral-600 leading-relaxed">
                                    {{ __('client.about.feature_1_desc', ['default' => 'Hệ thống xe cabin VIP và limousine đời mới, được trang bị đầy đủ tiện nghi hiện đại.']) }}
                                </p>
                            </div>
                        </div>

                        <div class="feature-card flex items-start gap-5">
                            <div class="feature-icon-wrapper bg-primary-50 text-primary-600">
                                <i class="fa-solid fa-shield-halved text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-semibold text-neutral-800 mb-2">
                                    {{ __('client.about.feature_2_title', ['default' => 'An toàn tuyệt đối']) }}
                                </h4>
                                <p class="text-neutral-600 leading-relaxed">
                                    {{ __('client.about.feature_2_desc', ['default' => 'Đội ngũ tài xế chuyên nghiệp, giàu kinh nghiệm. Kiểm tra kỹ thuật xe nghiêm ngặt.']) }}
                                </p>
                            </div>
                        </div>

                        <div class="feature-card flex items-start gap-5">
                            <div class="feature-icon-wrapper bg-emerald-50 text-emerald-600">
                                <i class="fa-solid fa-headset text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-semibold text-neutral-800 mb-2">
                                    {{ __('client.about.feature_3_title', ['default' => 'Hỗ trợ 24/7']) }}
                                </h4>
                                <p class="text-neutral-600 leading-relaxed">
                                    {{ __('client.about.feature_3_desc', ['default' => 'Tổng đài chăm sóc khách hàng hoạt động 24/7, sẵn sàng giải đáp mọi thắc mắc.']) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Image --}}
                <div class="relative">
                    <div class="relative rounded-lg overflow-hidden shadow-card aspect-[4/3]">
                        <img src="/userfiles/files/city_imgs/sapa.jpg"
                            alt="{{ __('client.about.image_alt', ['default' => 'Nội thất xe King Express Bus']) }}"
                            class="w-full h-full object-cover">
                    </div>

                    {{-- Badge --}}
                    <div class="absolute -bottom-6 -left-4 bg-white p-5 rounded-lg shadow-card hidden lg:flex items-center gap-4 z-10">
                        <div class="bg-accent-500 p-4 rounded-md text-white">
                            <i class="fa-solid fa-thumbs-up text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-neutral-500 font-medium">
                                {{ __('client.about.badge_subtitle', ['default' => 'Hài lòng']) }}</p>
                            <p class="text-xl font-semibold text-neutral-800">98% Review 5★</p>
                        </div>
                    </div>

                    {{-- Badge 2 --}}
                    <div class="absolute -top-4 -right-4 bg-primary-600 p-5 rounded-lg shadow-card hidden lg:block text-white">
                        <div class="text-center">
                            <p class="text-3xl font-semibold">7+</p>
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
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                <div class="space-y-3 max-w-xl">
                    <h2 class="text-2xl md:text-4xl font-semibold text-white">
                        {{ __('client.routes.index.cta_title', ['default' => 'Sẵn sàng cho chuyến đi tiếp theo?']) }}
                    </h2>
                    <p class="text-white/80 text-lg">
                        {{ __('client.routes.index.cta_subtitle', ['default' => 'Đặt vé ngay hôm nay để nhận ưu đãi hấp dẫn']) }}
                    </p>
                </div>
                <a href="#top"
                    class="inline-flex items-center gap-3 px-10 py-5 bg-accent-500 text-white rounded-md font-semibold text-lg hover:bg-accent-600 transition-colors duration-200">
                    <i class="fa-solid fa-ticket"></i>
                    {{ __('client.routes.index.cta_button', ['default' => 'Đặt vé ngay']) }}
                </a>
            </div>
        </div>
    </section>

    @push('scripts')
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
            })
        </script>
    @endpush

</x-client.layout>
