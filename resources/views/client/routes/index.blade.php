{{-- ===== resources/views/client/routes/index.blade.php ===== --}}
<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.routes.index.meta_title')" :description="$description ?? ''">

    @push('styles')
        <style>
            /* HERO/SEARCH */
            .hero-routes {
                background: linear-gradient(110deg, rgba(8, 23, 43, 0.78), rgba(255, 155, 0, 0.58)),
                    url('/client/images/city_imgs/sapa.jpg');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
                min-height: 85vh;
                position: relative;
                overflow: hidden;
            }

            .hero-routes::before {
                content: '';
                position: absolute;
                inset: 0;
                background-image: radial-gradient(circle at 18% 18%, rgba(255, 225, 0, 0.28), transparent 48%),
                    radial-gradient(circle at 82% 100%, rgba(255, 155, 0, 0.32), transparent 52%);
            }

            .hero-routes::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 120px;
                background: linear-gradient(to top, rgba(249, 250, 251, 1) 0%, transparent 100%);
                pointer-events: none;
                z-index: 1;
            }

            @media (max-width: 768px) {
                .hero-routes {
                    background-attachment: scroll;
                }
            }

            /* MAIN CONTENT */
            .route-card {
                background: #ffffff;
                border-radius: 1rem;
                transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
                overflow: hidden;
                border: 1px solid #f1d7a0;
                box-shadow: 0 12px 28px -18px rgba(15, 23, 42, 0.28);
            }

            .route-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 38px -24px rgba(15, 23, 42, 0.35);
                border-color: #ffdd99;
            }

            .route-card-image-wrapper {
                position: relative;
                height: 220px;
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

            /* STATS */
            .stat-card {
                background: #ffffff;
                border-radius: 1rem;
                padding: 1.5rem;
                text-align: center;
                border: 1px solid #f1d7a0;
                box-shadow: 0 12px 28px -18px rgba(15, 23, 42, 0.2);
            }

            .stat-icon {
                width: 56px;
                height: 56px;
                border-radius: 0.9rem;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 16px;
                font-size: 22px;
            }

            /* PROVINCE TABS */
            .province-tabs {
                display: flex;
                gap: 10px;
                overflow-x: auto;
                padding-bottom: 10px;
                scrollbar-width: thin;
            }

            .province-tabs::-webkit-scrollbar {
                height: 6px;
            }

            .province-tabs::-webkit-scrollbar-thumb {
                background: #ffdd99;
                border-radius: 9999px;
            }

            .province-tab {
                padding: 11px 20px;
                border-radius: 0.85rem;
                font-size: 0.875rem;
                font-weight: 600;
                white-space: nowrap;
                transition: all 0.2s ease;
                border: 1px solid #fde4b6;
                background: #ffffff;
                color: #475569;
                cursor: pointer;
            }

            .province-tab:hover {
                background: #fff7d6;
                border-color: #ffc900;
            }

            .province-tab.active {
                background: #FF9B00;
                border-color: #FF9B00;
                color: #ffffff;
                box-shadow: 0 10px 20px -16px rgba(255, 155, 0, 0.8);
            }

            /* QUICK SEARCH PILLS */
            .quick-route-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 10px 16px;
                background: rgba(255, 255, 255, 0.18);
                border: 1px solid rgba(255, 255, 255, 0.28);
                border-radius: 0.85rem;
                color: #fff;
                font-size: 13px;
                font-weight: 600;
                transition: all 0.25s ease;
                white-space: nowrap;
            }

            .quick-route-pill:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: translateY(-1px);
            }

            /* PRICE BADGE */
            .price-badge {
                background: linear-gradient(135deg, #FF9B00, #FFC900);
                color: #ffffff;
                padding: 7px 14px;
                border-radius: 0.75rem;
                font-weight: 700;
                font-size: 14px;
            }

            /* FEATURES */
            .feature-card {
                padding: 24px;
                border-radius: 1rem;
                transition: all 0.25s ease;
                border: 1px solid #f4e3bf;
                background: #ffffff;
            }

            .feature-card:hover {
                background: #fffcf3;
                border-color: #ffdd99;
                transform: translateY(-3px);
            }

            .feature-icon-wrapper {
                width: 56px;
                height: 56px;
                border-radius: 1rem;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }

            /* FOOTER CTA */
            .cta-section {
                background: linear-gradient(120deg, #FF9B00 0%, #FFC900 56%, #FFE100 100%);
                position: relative;
                overflow: hidden;
            }

            .cta-section::before {
                content: '';
                position: absolute;
                inset: 0;
                background: radial-gradient(circle at 10% 16%, rgba(255, 255, 255, 0.35), transparent 40%),
                    radial-gradient(circle at 90% 100%, rgba(255, 255, 255, 0.22), transparent 44%);
            }
        </style>
    @endpush

    {{-- Hero Section --}}
    <section class="hero-routes flex flex-col items-center justify-center px-4 py-24 lg:py-32">
        <div class="container mx-auto max-w-7xl relative z-10 w-full text-center space-y-8">

            {{-- Badge --}}
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/15 px-5 py-2 text-sm font-bold tracking-wider text-white">
                    <i class="fa-solid fa-route"></i>
                    {{ __('client.routes.index.badge', ['default' => 'TÌM TUYẾN XE']) }}
                </span>
            </div>

            {{-- Hero Title --}}
            <h1 class="text-4xl font-extrabold leading-tight text-white sm:text-5xl md:text-6xl">
                {{ __('client.routes.index.typing_1', ['default' => 'Vi vu khắp Việt Nam']) }}
            </h1>

            {{-- Subtitle --}}
            <p class="mx-auto max-w-2xl text-base text-slate-100/95 md:text-xl">
                {{ __('client.routes.index.hero_subtitle', ['default' => 'Hơn 100+ tuyến đường chất lượng cao đang chờ đón bạn']) }}
            </p>

            {{-- Search Bar --}}
            <div class="mt-10 mx-auto w-full max-w-6xl text-left">
                <x-client.search-bar :search-data="$searchData" :submit-label="__('client.route_show.search_submit_label', ['default' => 'Tìm chuyến'])" />
            </div>

            {{-- Quick Route Suggestions --}}
            @if (isset($quickRouteSuggestions) && $quickRouteSuggestions->isNotEmpty())
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <span class="mr-2 self-center text-sm font-semibold text-white/70">
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
    <section class="relative z-20 -mt-14 bg-[#F8FAFC] py-12">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <div class="stat-card" x-data="statsCounter(100, 2, 30)">
                    <div class="stat-icon bg-accent-100 text-accent-600">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <p class="text-3xl font-extrabold text-slate-800 md:text-4xl">
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
                    <p class="text-3xl font-extrabold text-slate-800 md:text-4xl">
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
                    <p class="text-3xl font-extrabold text-slate-800 md:text-4xl">
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
                    <p class="text-3xl font-extrabold text-slate-800 md:text-4xl">
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
        <section class="bg-[#F8FAFC] py-16 md:py-24">
            <div class="container mx-auto max-w-7xl px-4">

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
                <div class="mb-12 flex flex-col items-start justify-between gap-6 md:flex-row md:items-end">
                    <div class="space-y-3">
                        <span class="inline-flex items-center gap-2 rounded-full bg-primary-50 px-4 py-1.5 text-sm font-bold text-primary-700">
                            <i class="fa-solid fa-fire"></i>
                            {{ __('client.routes.index.trending_badge', ['default' => 'Được yêu thích']) }}
                        </span>
                        <h2 class="text-3xl font-extrabold text-slate-800 md:text-4xl">
                            {{ __('client.routes.index.popular_title', ['default' => 'Tuyến đường']) }}
                            <span class="text-primary-600">{{ __('client.routes.index.popular_highlight', ['default' => 'Phổ biến']) }}</span>
                        </h2>
                        <p class="max-w-lg text-base text-slate-500 md:text-lg">
                            {{ __('client.routes.index.popular_subtitle', ['default' => 'Các tuyến đường được khách hàng yêu thích nhất']) }}
                        </p>
                    </div>
                    <a href="{{ route('client.routes.index') }}"
                        class="hidden items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 font-bold text-white shadow-soft transition hover:bg-primary-700 active:scale-95 md:inline-flex">
                        {{ __('client.routes.index.view_all', ['default' => 'Xem tất cả']) }}
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                {{-- Routes Grid --}}
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
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
                                <img src="{{ $route->thumbnail_url ?? '/client/images/city_imgs/ha-noi.jpg' }}"
                                    alt="{{ $route->name }}" class="route-card-image" loading="lazy">
                                <div class="route-card-overlay"></div>

                                {{-- Trip Count Badge --}}
                                <div class="absolute left-4 top-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-white/95 px-3 py-1.5 text-xs font-semibold text-slate-800">
                                        <i class="fa-solid fa-bus text-primary-600"></i>
                                        {{ $route->trip_count ?? 0 }}
                                        {{ __('client.routes.index.trips', ['default' => 'chuyến']) }}
                                    </span>
                                </div>

                                {{-- Route Name on Image --}}
                                <div class="absolute bottom-4 left-4 right-4">
                                    <h3 class="line-clamp-2 text-lg font-extrabold text-white">
                                        {{ $route->name }}
                                    </h3>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-5">
                                <div class="mb-4 flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-2 text-sm text-slate-500">
                                        <i class="fa-regular fa-clock"></i>
                                        <span>{{ $route->duration ?? 'N/A' }}</span>
                                    </div>
                                    @if ($route->distance_km ?? false)
                                        <div class="flex items-center gap-2 text-sm text-slate-500">
                                            <i class="fa-solid fa-road"></i>
                                            <span>{{ $route->distance_km }}km</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between border-t border-amber-100 pt-4">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-400">
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
                    <a href="{{ route('client.routes.index') }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-4 font-bold text-white shadow-soft active:scale-95">
                        {{ __('client.routes.index.view_all_routes', ['default' => 'Xem tất cả tuyến đường']) }}
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Why Choose Us Section --}}
    <section class="relative bg-white py-20">
        <div class="container mx-auto max-w-7xl px-4 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
                {{-- Text Content --}}
                <div class="space-y-8">
                    <div class="space-y-4">
                        <span class="inline-flex items-center gap-2 rounded-full border border-primary-100 bg-primary-50 px-4 py-2 text-sm font-bold text-primary-700">
                            <i class="fa-solid fa-award"></i>
                            {{ __('client.about.subtitle', ['default' => 'Về King Express Bus']) }}
                        </span>
                        <h2 class="text-3xl font-extrabold leading-tight text-slate-800 md:text-4xl">
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
                    <div class="relative aspect-4/3 overflow-hidden rounded-2xl shadow-soft">
                        <img src="/client/images/city_imgs/sapa.jpg"
                            alt="{{ __('client.about.image_alt', ['default' => 'Nội thất xe King Express Bus']) }}"
                            class="w-full h-full object-cover">
                    </div>

                    {{-- Badge --}}
                    <div class="absolute -bottom-6 -left-4 z-10 hidden items-center gap-4 rounded-2xl bg-white p-5 shadow-soft lg:flex">
                        <div class="rounded-xl bg-primary-600 p-4 text-white">
                            <i class="fa-solid fa-thumbs-up text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-neutral-500 font-medium">
                                {{ __('client.about.badge_subtitle', ['default' => 'Hài lòng']) }}</p>
                            <p class="text-xl font-semibold text-neutral-800">98% Review 5★</p>
                        </div>
                    </div>

                    {{-- Badge 2 --}}
                    <div class="absolute -right-4 -top-4 hidden rounded-2xl bg-primary-600 p-5 text-white shadow-soft lg:block">
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
        <div class="container mx-auto max-w-7xl px-4 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 text-center md:text-left">
                <div class="space-y-3 max-w-xl">
                    <h2 class="text-2xl font-extrabold text-white md:text-4xl">
                        {{ __('client.routes.index.cta_title', ['default' => 'Sẵn sàng cho chuyến đi tiếp theo?']) }}
                    </h2>
                    <p class="text-lg text-slate-900/75 md:text-xl">
                        {{ __('client.routes.index.cta_subtitle', ['default' => 'Đặt vé ngay hôm nay để nhận ưu đãi hấp dẫn']) }}
                    </p>
                </div>
                <a href="#top"
                    class="inline-flex items-center gap-3 rounded-xl border border-white/35 bg-slate-900 px-10 py-5 text-lg font-bold text-white shadow-soft transition hover:bg-slate-800 active:scale-95">
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
