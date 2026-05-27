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
                min-height: clamp(420px, 66vh, 620px);
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

            .hero-search-shell {
                background: rgba(255, 255, 255, 0.98);
                border-radius: 1.15rem;
                padding: 0.85rem;
                border: 1px solid rgba(255, 255, 255, 0.75);
                box-shadow: 0 18px 34px -22px rgba(15, 23, 42, 0.5);
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
        </style>
    @endpush

    {{-- Hero Section --}}
    <section class="hero-routes flex flex-col items-center justify-center px-4 py-20 lg:py-24">
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
            <div class="hero-search-shell mt-8 mx-auto w-full max-w-6xl text-left">
                <x-client.search-bar :search-data="$searchData" :submit-label="__('client.route_show.search_submit_label', ['default' => 'Tìm chuyến'])" />
            </div>

            {{-- Quick Route Suggestions --}}
            @if (isset($quickRouteSuggestions) && $quickRouteSuggestions->isNotEmpty())
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <span class="mr-2 self-center text-sm font-semibold text-white/70">
                        {{ __('client.routes.index.popular_searches', ['default' => 'Phổ biến:']) }}
                    </span>
                    @foreach ($quickRouteSuggestions->take(6) as $suggestion)
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

    {{-- Popular Routes Section --}}
    <section class="bg-[#F8FAFC] py-10 md:py-14">
        <div class="container mx-auto max-w-7xl px-4">
            {{-- Section Header --}}
            <div class="mb-8 md:mb-10">
                <h2 class="text-2xl font-extrabold text-slate-800 md:text-3xl">
                    {{ __('client.routes.index.popular_title', ['default' => 'Tuyến đường']) }}
                    <span class="text-primary-600">{{ __('client.routes.index.popular_highlight', ['default' => 'Phổ biến']) }}</span>
                </h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500 md:text-base">
                    {{ __('client.routes.index.popular_subtitle', ['default' => 'Các tuyến đường được khách hàng yêu thích nhất']) }}
                </p>
            </div>

            {{-- Province Filter Tabs --}}
            @if (isset($provinces) && $provinces->isNotEmpty())
                <div class="mb-8">
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

            @if (isset($popularRoutes) && $popularRoutes->isNotEmpty())
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
                                <img src="{{ \App\Helpers\SystemHelper::mediaUrl($route->thumbnail_url, \App\Helpers\SystemHelper::mediaUrl('/client/images/city_imgs/ha-noi.jpg')) }}"
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
            @else
                <div class="rounded-2xl border border-dashed border-amber-200 bg-white p-8 text-center text-sm text-slate-500 md:text-base">
                    <p>{{ __('client.routes.index.empty', ['default' => 'Hiện chưa có tuyến phù hợp. Vui lòng đổi bộ lọc hoặc thử lại sau.']) }}</p>
                </div>
            @endif
        </div>
    </section>

</x-client.layout>
