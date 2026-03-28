<x-client.layout :title="__('client.home.meta_title')" body-class="bg-[#F8FAFC]">
    @push('styles')
        <style>
            @keyframes hero-shift {
                0% {
                    background-position: center top;
                }

                100% {
                    background-position: center bottom;
                }
            }

            .home-hero-bg {
                background-image:
                    linear-gradient(105deg, rgba(8, 23, 43, 0.72), rgba(255, 155, 0, 0.52)),
                    url('/client/images/city_imgs/sapa.jpg');
                background-size: cover;
                background-position: center;
                animation: hero-shift 16s ease-in-out infinite alternate;
            }
        </style>
    @endpush

    {{-- HEADER --}}
    {{-- Header được render trong x-client.layout --}}

    {{-- HERO/SEARCH --}}
    <section class="home-hero-bg relative overflow-visible px-4 pb-14 pt-24 md:pb-20 md:pt-28">
        <div class="container mx-auto max-w-7xl">
            <div class="max-w-3xl">
                <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-white">
                    <i class="fa-solid fa-bolt"></i>
                    {{ __('client.home_page.hero.badge') }}
                </span>
                <h1 class="text-3xl font-extrabold leading-tight text-white sm:text-4xl lg:text-6xl">
                    {{ __('client.home_page.hero.title_line_1') }}
                    <span class="text-accent">{{ __('client.home_page.hero.title_line_2') }}</span>
                </h1>
                <p class="mt-4 max-w-2xl text-sm text-slate-100/95 sm:text-base lg:text-lg">
                    {{ __('client.home_page.hero.description') }}
                </p>
            </div>

            <div class="relative z-50 mt-8" style="z-index: 90;">
                <x-client.search-bar :submit-label="__('client.home_page.hero.search_submit')" />
            </div>

            <div class="mt-8 grid gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/20 bg-white/10 p-4 text-white backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-wide text-white/80">{{ __('client.home_page.hero.stats.routes') }}</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ number_format(data_get($statistics, 'total_routes', 0)) }}+</p>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/10 p-4 text-white backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-wide text-white/80">{{ __('client.home_page.hero.stats.trips') }}</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ number_format(data_get($statistics, 'total_trips', 0)) }}+</p>
                </div>
                <div class="rounded-2xl border border-white/20 bg-white/10 p-4 text-white backdrop-blur-sm">
                    <p class="text-xs uppercase tracking-wide text-white/80">{{ __('client.home_page.hero.stats.bookings') }}</p>
                    <p class="mt-2 text-2xl font-extrabold">{{ number_format(data_get($statistics, 'total_bookings', 0)) }}+</p>
                </div>
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section class="px-4 py-12 md:py-16">
        <div class="container mx-auto max-w-7xl">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-primary-600">{{ __('client.home_page.popular_routes.label') }}</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-800 md:text-4xl">{{ __('client.home_page.popular_routes.title') }}</h2>
                </div>
                <a href="{{ route('client.routes.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-primary-600/25 bg-white px-4 py-2 text-sm font-bold text-primary-700 transition hover:bg-primary-50 active:scale-95">
                    {{ __('client.home_page.popular_routes.view_all') }}
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse ($popularRoutes as $route)
                    <a href="{{ route('client.routes.show', ['slug' => $route->slug]) }}"
                        class="group overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-soft hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
                        <div class="relative h-44 overflow-hidden">
                            <img src="{{ $route->thumbnail_url ?: '/client/images/city_imgs/ha-noi.jpg' }}" alt="{{ $route->name }}"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-linear-to-t from-slate-900/65 via-slate-900/10 to-transparent"></div>
                            <span class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-[11px] font-bold text-slate-700">
                                {{ __('client.home_page.popular_routes.trips_per_day', ['count' => $route->trip_count ?? 0]) }}
                            </span>
                            <p class="absolute bottom-3 left-3 right-3 line-clamp-1 text-sm font-extrabold text-white">{{ $route->name }}</p>
                        </div>
                        <div class="space-y-3 p-4">
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-1">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $route->duration ?: __('client.booking.common.updating') }}
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-1">
                                    <i class="fa-solid fa-road"></i>
                                    {{ $route->distance_km ? $route->distance_km . ' km' : __('client.common.contact') }}
                                </span>
                            </div>
                            <div class="flex items-end justify-between border-t border-amber-100 pt-3">
                                <p class="text-xs text-slate-500">{{ __('client.home_page.popular_routes.price_from') }}</p>
                                <p class="text-lg font-extrabold text-primary-600">
                                    {{ $route->min_price > 0 ? number_format($route->min_price) . 'đ' : __('client.common.contact') }}
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                        {{ __('client.home_page.popular_routes.empty') }}
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="px-4 pb-12 md:pb-16">
        <div class="container mx-auto max-w-7xl">
            <div class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
                <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-6">
                    <div class="mb-5 flex items-end justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-primary-600">{{ __('client.home_page.destinations.label') }}</p>
                            <h3 class="mt-1 text-2xl font-extrabold text-slate-800">{{ __('client.home_page.destinations.title') }}</h3>
                        </div>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('client.routes.index') }}" class="group relative h-40 overflow-hidden rounded-2xl">
                            <img src="/client/images/city_imgs/ha-noi.jpg" alt="Hà Nội" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-linear-to-t from-slate-900/65 to-transparent"></div>
                            <p class="absolute bottom-3 left-3 text-lg font-bold text-white">Hà Nội</p>
                        </a>
                        <a href="{{ route('client.routes.index') }}" class="group relative h-40 overflow-hidden rounded-2xl">
                            <img src="/client/images/city_imgs/sapa.jpg" alt="Sa Pa" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-linear-to-t from-slate-900/65 to-transparent"></div>
                            <p class="absolute bottom-3 left-3 text-lg font-bold text-white">Sa Pa</p>
                        </a>
                        <a href="{{ route('client.routes.index') }}" class="group relative h-40 overflow-hidden rounded-2xl">
                            <img src="/client/images/city_imgs/da-nang.jpg" alt="Đà Nẵng" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-linear-to-t from-slate-900/65 to-transparent"></div>
                            <p class="absolute bottom-3 left-3 text-lg font-bold text-white">Đà Nẵng</p>
                        </a>
                        <a href="{{ route('client.routes.index') }}" class="group relative h-40 overflow-hidden rounded-2xl">
                            <img src="/client/images/city_imgs/hoi-an.jpg" alt="Hội An" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            <div class="absolute inset-0 bg-linear-to-t from-slate-900/65 to-transparent"></div>
                            <p class="absolute bottom-3 left-3 text-lg font-bold text-white">Hội An</p>
                        </a>
                    </div>
                </div>

                <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-6">
                    <p class="text-xs font-bold uppercase tracking-wider text-primary-600">{{ __('client.home_page.advantages.label') }}</p>
                    <h3 class="mt-1 text-2xl font-extrabold text-slate-800">{{ __('client.home_page.advantages.title') }}</h3>
                    <div class="mt-6 space-y-3">
                        <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-700"><i class="fa-solid fa-bolt"></i></span>
                            <div>
                                <p class="font-bold text-slate-800">{{ __('client.home_page.advantages.item_1_title') }}</p>
                                <p class="text-sm text-slate-500">{{ __('client.home_page.advantages.item_1_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-700"><i class="fa-solid fa-shield-heart"></i></span>
                            <div>
                                <p class="font-bold text-slate-800">{{ __('client.home_page.advantages.item_2_title') }}</p>
                                <p class="text-sm text-slate-500">{{ __('client.home_page.advantages.item_2_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-700"><i class="fa-solid fa-headset"></i></span>
                            <div>
                                <p class="font-bold text-slate-800">{{ __('client.home_page.advantages.item_3_title') }}</p>
                                <p class="text-sm text-slate-500">{{ __('client.home_page.advantages.item_3_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($featuredBuses->isNotEmpty())
        <section class="bg-white px-4 py-12 md:py-16">
            <div class="container mx-auto max-w-7xl">
                <div class="mb-8 text-center">
                    <p class="text-xs font-bold uppercase tracking-wider text-primary-600">{{ __('client.home_page.fleet.label') }}</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-slate-800 md:text-4xl">{{ __('client.home_page.fleet.title') }}</h2>
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
                        @endphp
                        <article class="overflow-hidden rounded-2xl border border-amber-100 bg-[#fffdf8] shadow-soft hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
                            <img src="{{ $bus->thumbnail_url ?: $fallbackImage }}" alt="{{ $bus->name }}" class="h-44 w-full object-cover">
                            <div class="space-y-2 p-4">
                                <h3 class="line-clamp-1 text-base font-extrabold text-slate-800">{{ $bus->name }}</h3>
                                <p class="text-sm text-slate-500">{{ $bus->model_name ?: __('client.home.bus_highlights.model_fallback') }} · {{ $bus->seat_count }} {{ __('client.home_page.fleet.seats') }}</p>
                                @if (!empty($bus->services))
                                    <div class="flex flex-wrap gap-1 pt-1">
                                        @foreach (array_slice($bus->services, 0, 3) as $service)
                                            <span class="rounded-lg bg-primary-100 px-2 py-1 text-[11px] font-semibold text-primary-700">{{ $service }}</span>
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

    <section class="px-4 py-12 md:py-16">
        <div class="container mx-auto max-w-7xl">
            <div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900 shadow-soft">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_15%_20%,rgba(255,201,0,0.2),transparent_46%),radial-gradient(circle_at_80%_100%,rgba(255,155,0,0.28),transparent_48%)]"></div>
                <div class="relative grid items-center gap-6 p-6 text-white md:p-12 lg:grid-cols-[1.2fr_1fr]">
                    <div>
                        <p class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-200">{{ __('client.home_page.social_proof.label') }}</p>
                        <h3 class="mt-3 text-2xl font-extrabold leading-tight md:text-4xl">{{ __('client.home_page.social_proof.title') }}</h3>
                        <p class="mt-4 max-w-2xl text-sm text-slate-200 md:text-base">{{ __('client.home_page.social_proof.description') }}</p>
                    </div>
                    <div class="grid gap-3">
                        <div class="rounded-2xl border border-white/15 bg-white p-4 text-slate-800 shadow-lg">
                            <div class="mb-1 flex gap-1 text-primary-600"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                            <p class="text-sm font-medium">{{ __('client.home_page.social_proof.review_1_quote') }}</p>
                            <p class="mt-2 text-xs text-slate-500">{{ __('client.home_page.social_proof.review_1_author') }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-white p-4 text-slate-800 shadow-lg">
                            <div class="mb-1 flex gap-1 text-primary-600"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star-half-stroke"></i></div>
                            <p class="text-sm font-medium">{{ __('client.home_page.social_proof.review_2_quote') }}</p>
                            <p class="mt-2 text-xs text-slate-500">{{ __('client.home_page.social_proof.review_2_author') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="px-4 pb-14 md:pb-20">
        <div class="container mx-auto max-w-7xl">
            <div class="rounded-3xl border border-amber-100 bg-white p-6 text-center shadow-soft md:p-10">
                <p class="text-xs font-bold uppercase tracking-wider text-primary-600">{{ __('client.home_page.cta.label') }}</p>
                <h3 class="mt-2 text-2xl font-extrabold text-slate-800 md:text-4xl">{{ __('client.home_page.cta.title') }}</h3>
                <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-500 md:text-base">{{ __('client.home_page.cta.description') }}</p>
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('client.routes.index') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-bold text-white shadow-soft transition hover:bg-primary-700 active:scale-95">
                        <i class="fa-solid fa-ticket"></i>
                        {{ __('client.home_page.cta.primary_button') }}
                    </a>
                    <a href="{{ route('client.contact') }}"
                        class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 transition hover:border-primary-600 hover:text-primary-700 active:scale-95">
                        <i class="fa-solid fa-headset"></i>
                        {{ __('client.home_page.cta.secondary_button') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    {{-- Footer được render trong x-client.layout --}}
</x-client.layout>
