<x-client.layout :title="__('client.home.meta_title')" body-class="bg-[#F5F7FA] text-slate-900">
    <section class="ksb-hero ksb-home-hero ksb-section-hero px-4">
        <div class="ksb-hero-media-wrapper">
            <div class="ksb-hero-media" style="background-image: url('/client/images/city_imgs/sapa.jpg');"></div>
        </div>

        <div class="container relative mx-auto w-full max-w-7xl">
            <div class="grid items-center gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(360px,0.82fr)] lg:gap-12">
                <div class="min-w-0 max-w-3xl pr-1 text-white sm:pr-0">
                    <div class="ksb-reveal mb-5 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-xs font-semibold tracking-wide text-white/90 backdrop-blur-md" style="--ksb-delay: 0ms;">
                        <span class="flex h-2 w-2 rounded-full bg-accent animate-pulse"></span>
                        {{ __('client.home_page.hero.badge') }}
                    </div>

                    <h1 class="ksb-reveal ksb-text-balance font-display text-3xl font-extrabold leading-tight sm:text-5xl lg:text-6xl" style="--ksb-delay: 80ms;">
                        {{ __('client.home_page.hero.title_line_1') }}
                        <span class="block text-accent">{{ __('client.home_page.hero.title_line_2') }}</span>
                    </h1>

                    <p class="ksb-reveal mt-5 max-w-[21rem] text-base leading-8 text-slate-100/90 sm:max-w-2xl md:text-lg" style="--ksb-delay: 160ms;">
                        {{ __('client.home_page.hero.description') }}
                    </p>

                    <div class="ksb-reveal mt-8 grid gap-4 text-sm text-white/88 sm:grid-cols-3" style="--ksb-delay: 240ms;">
                        <div class="group border-l-2 border-white/15 pl-4 py-1 pr-2 rounded-r-xl transition-all duration-300 hover:border-accent hover:bg-white/5">
                            <p class="font-bold text-slate-300 group-hover:text-white transition-colors duration-300">{{ __('client.home_page.hero.stats.routes') }}</p>
                            <p class="mt-1 text-2xl font-extrabold text-accent transition-transform duration-300 group-hover:translate-x-1 origin-left">
                                {{ number_format(data_get($statistics, 'total_routes', 0)) }}+</p>
                        </div>
                        <div class="group border-l-2 border-white/15 pl-4 py-1 pr-2 rounded-r-xl transition-all duration-300 hover:border-accent hover:bg-white/5">
                            <p class="font-bold text-slate-300 group-hover:text-white transition-colors duration-300">{{ __('client.home_page.hero.stats.trips') }}</p>
                            <p class="mt-1 text-2xl font-extrabold text-accent transition-transform duration-300 group-hover:translate-x-1 origin-left">
                                {{ number_format(data_get($statistics, 'total_trips', 0)) }}+</p>
                        </div>
                        <div class="group border-l-2 border-white/15 pl-4 py-1 pr-2 rounded-r-xl transition-all duration-300 hover:border-accent hover:bg-white/5">
                            <p class="font-bold text-slate-300 group-hover:text-white transition-colors duration-300">{{ __('client.home_page.hero.stats.bookings') }}</p>
                            <p class="mt-1 text-2xl font-extrabold text-accent transition-transform duration-300 group-hover:translate-x-1 origin-left">
                                {{ number_format(data_get($statistics, 'total_bookings', 0)) }}+</p>
                        </div>
                    </div>
                </div>

                <div class="ksb-reveal" style="--ksb-delay: 320ms;">
                    <div class="ksb-hero-visual relative group">
                        <!-- Ambient Glow behind the bus -->
                        <div class="absolute -inset-4 rounded-[40px] bg-accent/10 opacity-60 blur-2xl transition duration-1000 group-hover:bg-accent/20 group-hover:opacity-80"></div>

                        <div class="relative overflow-hidden rounded-[22px] border border-white/15 bg-white/5 p-3 shadow-2xl backdrop-blur-md transition-all duration-500 hover:border-white/25">
                            <img src="/client/images/kingexpressbus/cabin/1.jpg" alt="King Express Bus"
                                class="aspect-[4/3] w-full rounded-2xl bg-white/5 object-cover transition-transform duration-700 ease-out group-hover:scale-[1.025]">
                        </div>
                    </div>
                </div>
            </div>

            <div class="ksb-hero-search relative z-dropdown mt-9 lg:mt-10" style="animation-delay: 400ms;">
                <x-client.search-bar :submit-label="__('client.home_page.hero.search_submit')" />
            </div>
        </div>
    </section>

    <section class="ksb-trust-strip ksb-section-compact overflow-hidden text-white">
        <div class="container mx-auto max-w-7xl">
            <div class="ksb-marquee-container relative">
                <!-- Marquee content block 1 -->
                <div class="ksb-marquee-content py-1">
                    <div class="flex items-center gap-3 text-white mx-8 shrink-0">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-accent"><i class="fa-solid fa-bus"></i></span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm">{{ __('client.home_page.advantages.item_1_title') }}</span>
                            <span class="hidden md:inline text-xs text-slate-300/80">• {{ __('client.home_page.advantages.item_1_desc') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-white mx-8 shrink-0">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-accent"><i class="fa-regular fa-clock"></i></span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm">{{ __('client.home_page.advantages.item_2_title') }}</span>
                            <span class="hidden md:inline text-xs text-slate-300/80">• {{ __('client.home_page.advantages.item_2_desc') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-white mx-8 shrink-0">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-accent"><i class="fa-solid fa-headset"></i></span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm">{{ __('client.home_page.advantages.item_3_title') }}</span>
                            <span class="hidden md:inline text-xs text-slate-300/80">• {{ __('client.home_page.advantages.item_3_desc') }}</span>
                        </div>
                    </div>
                </div>
                <!-- Marquee content block 2 (identical copy for seamless looping) -->
                <div class="ksb-marquee-content py-1" aria-hidden="true">
                    <div class="flex items-center gap-3 text-white mx-8 shrink-0">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-accent"><i class="fa-solid fa-bus"></i></span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm">{{ __('client.home_page.advantages.item_1_title') }}</span>
                            <span class="hidden md:inline text-xs text-slate-300/80">• {{ __('client.home_page.advantages.item_1_desc') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-white mx-8 shrink-0">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-accent"><i class="fa-regular fa-clock"></i></span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm">{{ __('client.home_page.advantages.item_2_title') }}</span>
                            <span class="hidden md:inline text-xs text-slate-300/80">• {{ __('client.home_page.advantages.item_2_desc') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-white mx-8 shrink-0">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-white/10 text-accent"><i class="fa-solid fa-headset"></i></span>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm">{{ __('client.home_page.advantages.item_3_title') }}</span>
                            <span class="hidden md:inline text-xs text-slate-300/80">• {{ __('client.home_page.advantages.item_3_desc') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="ksb-page-band ksb-section px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="ksb-section-label">{{ __('client.home_page.popular_routes.label') }}</p>
                    <h2 class="ksb-text-balance mt-2 font-display text-3xl font-extrabold text-slate-900 md:text-4xl">
                        {{ __('client.home_page.popular_routes.title') }}</h2>
                </div>
                <a href="{{ route('client.routes.index') }}" class="ksb-btn-secondary px-4 text-sm">
                    {{ __('client.home_page.popular_routes.view_all') }}
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                @forelse ($popularRoutes as $route)
                    <a href="{{ route('client.routes.show', ['slug' => $route->slug]) }}"
                        class="ksb-route-card group grid overflow-hidden sm:grid-cols-[180px_1fr]">
                        <div class="relative min-h-44 overflow-hidden">
                            <img src="{{ \App\Helpers\SystemHelper::mediaUrl($route->thumbnail_url, \App\Helpers\SystemHelper::mediaUrl('/client/images/city_imgs/ha-noi.jpg')) }}"
                                alt="{{ $route->name }}"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                loading="lazy">
                        </div>
                        <div class="flex flex-col justify-between gap-4 p-5">
                            <div>
                                <h3 class="line-clamp-2 text-lg font-extrabold text-slate-900">{{ $route->name }}</h3>
                                <div class="mt-3 grid grid-cols-2 gap-2 text-sm text-slate-500">
                                    <span class="inline-flex items-center gap-2"><i
                                            class="fa-regular fa-clock text-primary-600"></i>{{ $route->duration ?: __('client.booking.common.updating') }}</span>
                                    <span class="inline-flex items-center gap-2"><i
                                            class="fa-solid fa-road text-primary-600"></i>{{ $route->distance_km ? $route->distance_km . ' km' : __('client.common.contact') }}</span>
                                </div>
                            </div>
                            <div class="flex items-end justify-between border-t border-slate-100 pt-4">
                                <span
                                    class="text-sm text-slate-500">{{ __('client.home_page.popular_routes.price_from') }}</span>
                                <span class="ksb-price text-xl font-extrabold">
                                    {{ $route->min_price > 0 ? number_format($route->min_price) . 'đ' : __('client.common.contact') }}
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <p
                        class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                        {{ __('client.home_page.popular_routes.empty') }}
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="ksb-section bg-white px-4">
        <div class="container mx-auto grid max-w-7xl gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <div class="ksb-reveal">
                <p class="ksb-section-label">{{ __('client.home_page.destinations.label') }}</p>
                <h2 class="ksb-text-balance mt-2 font-display text-3xl font-extrabold text-slate-900 md:text-4xl">
                    {{ __('client.home_page.destinations.title') }}</h2>
                <p class="mt-4 max-w-xl leading-7 text-slate-600">{{ __('client.home_page.hero.description') }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
                @foreach ([['name' => 'Hà Nội', 'image' => '/client/images/city_imgs/ha-noi.jpg'], ['name' => 'Sa Pa', 'image' => '/client/images/city_imgs/sapa.jpg'], ['name' => 'Đà Nẵng', 'image' => '/client/images/city_imgs/da-nang.jpg'], ['name' => 'Hội An', 'image' => '/client/images/city_imgs/hoi-an.jpg']] as $destination)
                    <a href="{{ route('client.routes.index') }}"
                        class="group relative h-48 overflow-hidden rounded-2xl {{ $loop->first ? 'md:col-span-2 md:h-full' : '' }}">
                        <img src="{{ $destination['image'] }}" alt="{{ $destination['name'] }}"
                            class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            loading="lazy">
                        <span
                            class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-900/85 to-transparent p-4 text-lg font-bold text-white">{{ $destination['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @if ($featuredBuses->isNotEmpty())
        <section class="ksb-section px-4">
            <div class="container mx-auto max-w-7xl">
                <div class="mb-8">
                    <p class="ksb-section-label">{{ __('client.home_page.fleet.label') }}</p>
                    <h2 class="mt-2 font-display text-3xl font-extrabold text-slate-900 md:text-4xl">
                        {{ __('client.home_page.fleet.title') }}</h2>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($featuredBuses as $index => $bus)
                        @php
                            $fallbackImages = [
                                '/client/images/kingexpressbus/cabin/1.jpg',
                                '/client/images/kingexpressbus/limousine/1.png',
                                '/client/images/kingexpressbus/sleeper/1.jpg',
                                '/client/images/kingexpressbus/cabin_double/1.jpg',
                            ];
                            $fallbackImage = $fallbackImages[$index % count($fallbackImages)];
                            $busServices = collect($bus->service_details ?? [])
                                ->filter()
                                ->values();

                            if ($busServices->isEmpty()) {
                                $busServices = collect($bus->services ?? [])
                                    ->filter()
                                    ->map(fn($service) => ['name' => $service])
                                    ->values();
                            }
                        @endphp
                        <article class="ksb-route-card overflow-hidden">
                            <img src="{{ \App\Helpers\SystemHelper::mediaUrl($bus->thumbnail_url, \App\Helpers\SystemHelper::mediaUrl($fallbackImage)) }}"
                                alt="{{ $bus->name }}" class="h-44 w-full object-cover" loading="lazy">
                            <div class="space-y-3 p-4">
                                <h3 class="line-clamp-1 text-base font-extrabold text-slate-900">{{ $bus->name }}
                                </h3>
                                <p class="text-sm text-slate-500">
                                    {{ $bus->model_name ?: __('client.home.bus_highlights.model_fallback') }} ·
                                    {{ $bus->seat_count }} {{ __('client.home_page.fleet.seats') }}</p>
                                @if ($busServices->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 pt-1">
                                        @foreach ($busServices->take(3) as $service)
                                            <span
                                                class="ksb-chip px-2 py-1 text-[11px] font-semibold">{{ $service['name'] ?? $service }}</span>
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
            <div class="overflow-hidden rounded-[22px] bg-navy-900 p-6 text-white shadow-card md:p-10">
                <div class="grid items-center gap-6 lg:grid-cols-[1.15fr_0.85fr]">
                    <div>
                        <h3 class="ksb-text-balance text-2xl font-extrabold leading-tight md:text-4xl">
                            {{ __('client.home_page.cta.title') }}</h3>
                        <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-300 md:text-base">
                            {{ __('client.home_page.cta.description') }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3 lg:justify-end">
                        <a href="{{ route('client.routes.index') }}" class="ksb-btn-primary px-6 text-sm">
                            <i class="fa-solid fa-ticket"></i>
                            {{ __('client.home_page.cta.primary_button') }}
                        </a>
                        <a href="{{ route('client.contact') }}" class="ksb-btn-ghost px-6 text-sm">
                            <i class="fa-solid fa-headset"></i>
                            {{ __('client.home_page.cta.secondary_button') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-client.layout>
