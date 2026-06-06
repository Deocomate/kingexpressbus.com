<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.routes.index.meta_title')" :description="$description ?? ''">
    <section class="ksb-hero ksb-section-hero px-4">
        <div class="ksb-hero-media-wrapper">
            <div class="ksb-hero-media" style="background-image: url('/client/images/city_imgs/sapa.jpg');"></div>
        </div>

        <div class="container relative mx-auto max-w-7xl">
            <div class="max-w-3xl text-white">
                <h1 class="ksb-text-balance font-display text-4xl font-extrabold leading-tight sm:text-5xl md:text-6xl">
                    {{ __('client.routes.index.typing_1', ['default' => 'Vi vu khắp Việt Nam']) }}
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-8 text-slate-100/90 md:text-lg">
                    {{ __('client.routes.index.hero_subtitle', ['default' => 'Hơn 100+ tuyến đường chất lượng cao đang chờ đón bạn']) }}
                </p>
            </div>

            <div class="relative z-dropdown mt-9 lg:mt-10">
                <x-client.search-bar :search-data="$searchData" :submit-label="__('client.route_show.search_submit_label', ['default' => 'Tìm chuyến'])" />
            </div>
        </div>
    </section>

    <section class="ksb-trust-strip ksb-section-compact px-4 text-white">
        <div class="container mx-auto max-w-7xl">
            @if (isset($quickRouteSuggestions) && $quickRouteSuggestions->isNotEmpty())
                <div class="flex gap-2 overflow-x-auto pb-2">
                    <span class="mr-1 shrink-0 self-center text-sm font-semibold text-white/70">
                        {{ __('client.routes.index.popular_searches', ['default' => 'Phổ biến:']) }}
                    </span>
                    @foreach ($quickRouteSuggestions->take(6) as $suggestion)
                        <a href="{{ route('client.routes.show', ['slug' => $suggestion->slug]) }}"
                            class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2 text-sm font-semibold text-white transition hover:bg-white/20">
                            <i class="fa-solid fa-location-arrow text-accent"></i>
                            {{ $suggestion->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="ksb-section px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="ksb-section-label">{{ __('client.routes.index.popular_highlight', ['default' => 'Phổ biến']) }}</p>
                    <h2 class="mt-2 font-display text-3xl font-extrabold text-slate-900 md:text-4xl">
                        {{ __('client.routes.index.popular_title', ['default' => 'Tuyến đường']) }}
                    </h2>
                    <p class="mt-3 max-w-2xl text-slate-600">
                        {{ __('client.routes.index.popular_subtitle', ['default' => 'Các tuyến đường được khách hàng yêu thích nhất']) }}
                    </p>
                </div>
            </div>

            @if (isset($provinces) && $provinces->isNotEmpty())
                <div class="mb-8 flex gap-2 overflow-x-auto pb-2">
                    <a href="{{ route('client.routes.index') }}"
                        class="shrink-0 rounded-xl border px-4 py-2 text-sm font-semibold transition {{ !$selectedProvince ? 'border-primary-600 bg-primary-600 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-primary-300 hover:bg-primary-50' }}">
                        <i class="fa-solid fa-globe mr-1"></i>
                        {{ __('client.routes.index.all_provinces', ['default' => 'Tất cả']) }}
                    </a>
                    @foreach ($provinces as $province)
                        <a href="{{ route('client.routes.index', ['province' => $province->id]) }}"
                            class="shrink-0 rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $selectedProvince == $province->id ? 'border-primary-600 bg-primary-600 text-white' : 'border-slate-200 bg-white text-slate-600 hover:border-primary-300 hover:bg-primary-50' }}">
                            {{ $province->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            @if (isset($popularRoutes) && $popularRoutes->isNotEmpty())
                <div class="space-y-4">
                    @foreach ($popularRoutes as $route)
                        @php
                            $minPrice = $route->min_price ?? 0;
                            $priceDisplay = $minPrice > 0
                                ? number_format($minPrice) . 'đ'
                                : __('client.common.contact_price', ['default' => 'Liên hệ']);
                        @endphp
                        <a href="{{ route('client.routes.show', ['slug' => $route->slug]) }}"
                            class="ksb-route-card group grid overflow-hidden md:grid-cols-[220px_1fr_auto]">
                            <div class="relative min-h-48 overflow-hidden md:min-h-0">
                                <img src="{{ \App\Helpers\SystemHelper::mediaUrl($route->thumbnail_url, \App\Helpers\SystemHelper::mediaUrl('/client/images/city_imgs/ha-noi.jpg')) }}"
                                    alt="{{ $route->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
                            </div>

                            <div class="flex min-w-0 flex-col justify-between gap-5 p-5">
                                <div>
                                    <h3 class="line-clamp-2 text-xl font-extrabold text-slate-900">{{ $route->name }}</h3>
                                    <div class="mt-4 grid gap-3 text-sm text-slate-500 sm:grid-cols-3">
                                        <span class="inline-flex items-center gap-2"><i class="fa-solid fa-bus text-primary-600"></i>{{ $route->trip_count ?? 0 }} {{ __('client.routes.index.trips', ['default' => 'chuyến']) }}</span>
                                        <span class="inline-flex items-center gap-2"><i class="fa-regular fa-clock text-primary-600"></i>{{ $route->duration ?? 'N/A' }}</span>
                                        @if ($route->distance_km ?? false)
                                            <span class="inline-flex items-center gap-2"><i class="fa-solid fa-road text-primary-600"></i>{{ $route->distance_km }}km</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ksb-route-timeline text-slate-300"></div>
                            </div>

                            <div class="flex items-center justify-between gap-4 border-t border-slate-100 p-5 md:flex-col md:items-end md:justify-center md:border-l md:border-t-0">
                                <div class="text-right">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">{{ __('client.routes.index.from', ['default' => 'Giá từ']) }}</p>
                                    <p class="ksb-price mt-1 text-2xl font-extrabold">{{ $priceDisplay }}</p>
                                </div>
                                <span class="ksb-btn-primary px-4 text-sm">
                                    {{ __('client.routes.index.cta_button') }}
                                    <i class="fa-solid fa-arrow-right"></i>
                                </span>
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
