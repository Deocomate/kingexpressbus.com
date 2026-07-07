<x-client.layout :title="__('client.home.meta_title')" body-class="bg-page text-ink">
    @push('styles')
        <link rel="preload" as="image" href="/assets/client/images/kingexpressbus/cabin/1.jpg" imagesizes="(min-width: 1024px) 42vw, 100vw">
    @endpush

    <section class="ksb-hero ksb-home-hero relative z-elevated overflow-visible">
        <div class="ksb-hero-media-wrapper">
            <div class="ksb-hero-media" style="background-image: url('/assets/client/images/city_imgs/sapa.jpg');"></div>
        </div>

        <div class="ksb-home-hero-body">
            <div class="ksb-home-hero-main">
                <div class="max-w-2xl text-white">
                    <h1 class="ksb-text-balance font-display text-3xl font-extrabold leading-tight sm:text-4xl md:text-5xl">
                        {{ __('client.home_page.hero.title_line_1') }}
                        <span class="block text-brand-400">{{ __('client.home_page.hero.title_line_2') }}</span>
                    </h1>
                    <p class="mt-3 max-w-xl text-sm leading-7 text-slate-100/90 md:text-base">
                        {{ __('client.home_page.hero.description') }}
                    </p>
                </div>

                <div class="ksb-hero-search mt-6 md:mt-8">
                    <x-client.search-bar :submit-label="__('client.home_page.hero.search_submit')" />
                </div>
            </div>

            <div class="ksb-home-hero-features border-t border-white/15 pt-6 md:pt-8">
                <div class="ksb-trust-item">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-sm bg-brand-500/20 text-brand-400">
                            <i class="fa-solid fa-bus text-sm"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-white">{{ __('client.home_page.advantages.item_1_title') }}</p>
                            <p class="mt-0.5 text-xs leading-5 text-slate-200/80">{{ __('client.home_page.advantages.item_1_desc') }}</p>
                        </div>
                    </div>
                </div>
                <div class="ksb-trust-item">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-sm bg-brand-500/20 text-brand-400">
                            <i class="fa-regular fa-clock text-sm"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-white">{{ __('client.home_page.advantages.item_2_title') }}</p>
                            <p class="mt-0.5 text-xs leading-5 text-slate-200/80">{{ __('client.home_page.advantages.item_2_desc') }}</p>
                        </div>
                    </div>
                </div>
                <div class="ksb-trust-item">
                    <div class="flex items-start gap-3">
                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-sm bg-brand-500/20 text-brand-400">
                            <i class="fa-solid fa-headset text-sm"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-white">{{ __('client.home_page.advantages.item_3_title') }}</p>
                            <p class="mt-0.5 text-xs leading-5 text-slate-200/80">{{ __('client.home_page.advantages.item_3_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ksb-section px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="kx-section-label">{{ __('client.home_page.popular_routes.label') }}</p>
                    <h2 class="text-balance mt-1 font-display text-2xl font-extrabold text-ink md:text-3xl">
                        {{ __('client.home_page.popular_routes.title') }}</h2>
                </div>
                <a href="{{ route('client.routes.index') }}" class="kx-btn-secondary px-4 text-sm">
                    {{ __('client.home_page.popular_routes.view_all') }}
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid gap-3 lg:grid-cols-2">
                @forelse ($popularRoutes as $route)
                    <a href="{{ route('client.routes.show', ['slug' => $route->slug]) }}"
                        class="kx-card group grid overflow-hidden sm:grid-cols-[180px_1fr]">
                        <div class="relative min-h-40 overflow-hidden">
                            <img src="{{ \App\Helpers\SystemHelper::mediaUrl($route->thumbnail_url, \App\Helpers\SystemHelper::mediaUrl('/assets/client/images/city_imgs/ha-noi.jpg')) }}"
                                alt="{{ $route->name }}"
                                width="360" height="288" decoding="async" sizes="(min-width: 1024px) 180px, 100vw"
                                class="h-full w-full object-cover"
                                loading="lazy">
                        </div>
                        <div class="flex flex-col justify-between gap-3 p-4">
                            <div>
                                <h3 class="line-clamp-2 text-lg font-extrabold text-ink">{{ $route->name }}</h3>
                                <div class="mt-2 grid grid-cols-2 gap-2 text-sm text-muted">
                                    <span class="inline-flex items-center gap-2"><i class="fa-regular fa-clock text-brand-600"></i>{{ $route->duration ?: __('client.booking.common.updating') }}</span>
                                    <span class="inline-flex items-center gap-2"><i class="fa-solid fa-road text-brand-600"></i>{{ $route->distance_km ? $route->distance_km . ' km' : __('client.common.contact') }}</span>
                                </div>
                            </div>
                            <div class="flex items-end justify-between border-t border-line pt-3">
                                <span class="text-sm text-muted">{{ __('client.home_page.popular_routes.price_from') }}</span>
                                <span class="kx-price text-xl font-extrabold">
                                    {{ $route->min_price > 0 ? number_format($route->min_price) . 'đ' : __('client.common.contact') }}
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="col-span-full rounded-sm border border-dashed border-line-strong bg-surface p-6 text-center text-muted">
                        {{ __('client.home_page.popular_routes.empty') }}
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="ksb-section bg-white px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                <div class="max-w-2xl">
                    <p class="kx-section-label">{{ __('client.home_page.destinations.label') }}</p>
                    <h2 class="text-balance mt-1 font-display text-2xl font-extrabold text-ink md:text-3xl">
                        {{ __('client.home_page.destinations.title') }}</h2>
                    <p class="mt-3 max-w-xl text-sm leading-7 text-muted">
                        {{ __('client.home_page.destinations.description') }}</p>
                </div>
                <a href="{{ route('client.routes.index') }}" class="kx-btn-secondary shrink-0 px-4 text-sm">
                    {{ __('client.home_page.destinations.view_all') }}
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="ksb-destination-mosaic">
                @foreach ($featuredDestinations as $destination)
                    <a href="{{ route('client.routes.index') }}"
                        class="ksb-destination-tile ksb-destination-tile--{{ $destination['layout'] }}">
                        <img src="{{ $destination['image'] }}"
                            alt="{{ __('client.home_page.destinations.cities.' . $destination['key']) }}"
                            width="480" height="360" decoding="async"
                            sizes="(min-width: 1024px) 25vw, (min-width: 768px) 33vw, 50vw"
                            class="h-full w-full object-cover"
                            loading="lazy">
                        <span class="ksb-destination-tile-overlay" aria-hidden="true"></span>
                        <span class="ksb-destination-tile-copy">
                            <span class="ksb-destination-tile-name block text-base font-extrabold text-white">
                                {{ __('client.home_page.destinations.cities.' . $destination['key']) }}
                            </span>
                            <span class="ksb-destination-tile-tag">
                                {{ __('client.home_page.destinations.tags.' . $destination['key']) }}
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if ($featuredBuses->isNotEmpty())
        <section class="ksb-section bg-page px-4">
            <div class="container mx-auto max-w-7xl">
                <div class="mb-6">
                    <p class="kx-section-label">{{ __('client.home_page.fleet.label') }}</p>
                    <h2 class="mt-1 font-display text-2xl font-extrabold text-ink md:text-3xl">
                        {{ __('client.home_page.fleet.title') }}</h2>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($featuredBuses as $index => $bus)
                        @php
                            $fallbackImages = [
                                '/assets/client/images/kingexpressbus/cabin/1.jpg',
                                '/assets/client/images/kingexpressbus/limousine/1.png',
                                '/assets/client/images/kingexpressbus/sleeper/1.jpg',
                                '/assets/client/images/kingexpressbus/cabin_double/1.jpg',
                            ];
                            $fallbackImage = $fallbackImages[$index % count($fallbackImages)];
                            $busServices = collect($bus->service_details ?? [])->filter()->values();
                            if ($busServices->isEmpty()) {
                                $busServices = collect($bus->services ?? [])->filter()->map(fn($service) => ['name' => $service])->values();
                            }
                        @endphp
                        <article class="kx-card overflow-hidden">
                            <img src="{{ \App\Helpers\SystemHelper::mediaUrl($bus->thumbnail_url, \App\Helpers\SystemHelper::mediaUrl($fallbackImage)) }}"
                                alt="{{ $bus->name }}" width="360" height="176" decoding="async"
                                sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
                                class="h-40 w-full object-cover" loading="lazy">
                            <div class="space-y-2 p-3">
                                <h3 class="line-clamp-1 text-base font-extrabold text-ink">{{ $bus->name }}</h3>
                                <p class="text-sm text-muted">
                                    {{ $bus->model_name ?: __('client.home.bus_highlights.model_fallback') }} ·
                                    {{ $bus->seat_count }} {{ __('client.home_page.fleet.seats') }}</p>
                                @if ($busServices->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 pt-1">
                                        @foreach ($busServices->take(3) as $service)
                                            <span class="kx-chip px-2 py-1 text-[11px] font-semibold">{{ $service['name'] ?? $service }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="ksb-section-cta px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="overflow-hidden rounded-sm border border-line bg-contrast-900 p-5 text-white md:p-8">
                <div class="grid items-center gap-5 lg:grid-cols-[1.15fr_0.85fr]">
                    <div>
                        <h3 class="text-balance text-xl font-extrabold leading-tight md:text-3xl">
                            {{ __('client.home_page.cta.title') }}</h3>
                        <p class="mt-3 max-w-2xl text-sm leading-7 text-white/70">
                            {{ __('client.home_page.cta.description') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2 lg:justify-end">
                        <a href="{{ route('client.routes.index') }}" class="kx-btn-primary px-5 text-sm">
                            <i class="fa-solid fa-ticket"></i>
                            {{ __('client.home_page.cta.primary_button') }}
                        </a>
                        <a href="{{ route('client.contact') }}" class="kx-btn-ghost px-5 text-sm">
                            <i class="fa-solid fa-headset"></i>
                            {{ __('client.home_page.cta.secondary_button') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-client.layout>
