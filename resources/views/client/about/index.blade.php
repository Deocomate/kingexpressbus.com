{{-- ===== resources/views/client/about/index.blade.php ===== --}}
<x-client.layout :title="$title" :description="$description" body-class="bg-[#F7F8FA] text-slate-900">

    @php
        $fleetCards = [
            [
                'name' => __('client.about_page.fleet_cards.cabin.name'),
                'image' => '/client/images/kingexpressbus/cabin/1.jpg',
                'meta' => __('client.about_page.fleet_cards.cabin.meta'),
            ],
            [
                'name' => __('client.about_page.fleet_cards.limousine.name'),
                'image' => '/client/images/kingexpressbus/limousine/1.png',
                'meta' => __('client.about_page.fleet_cards.limousine.meta'),
            ],
            [
                'name' => __('client.about_page.fleet_cards.sleeper.name'),
                'image' => '/client/images/kingexpressbus/sleeper/1.jpg',
                'meta' => __('client.about_page.fleet_cards.sleeper.meta'),
            ],
        ];

        $destinationCards = [
            ['name' => 'Sa Pa', 'image' => '/client/images/city_imgs/sapa.jpg'],
            ['name' => 'Hà Nội', 'image' => '/client/images/city_imgs/ha-noi.jpg'],
            ['name' => 'Ninh Bình', 'image' => '/client/images/city_imgs/ninh-binh.jpg'],
            ['name' => 'Đà Nẵng', 'image' => '/client/images/city_imgs/da-nang.jpg'],
            ['name' => 'Hội An', 'image' => '/client/images/city_imgs/hoi-an.jpg'],
        ];

        $statItems = [
            [
                'value' => $stats['route_count'] ?? 100,
                'step' => 2,
                'speed' => 34,
                'format' => false,
                'label' => __('client.about.stats.routes'),
            ],
            [
                'value' => $stats['bus_count'] ?? 10,
                'step' => 1,
                'speed' => 48,
                'format' => false,
                'label' => __('client.about.stats.buses'),
            ],
            [
                'value' => 10000,
                'step' => 200,
                'speed' => 30,
                'format' => true,
                'label' => __('client.about.stats.bookings'),
            ],
            [
                'value' => $stats['years_experience'] ?? 7,
                'step' => 1,
                'speed' => 90,
                'format' => false,
                'label' => __('client.about.stats.years'),
            ],
        ];

        $timelineItems = [
            [
                'year' => '2017',
                'title' => __('client.about.position.badge'),
                'description' => __('client.about.position.paragraph1'),
            ],
            [
                'year' => '2021',
                'title' => __('client.about_page.destinations.label'),
                'description' => __('client.about.position.paragraph2'),
            ],
            [
                'year' => now()->year,
                'title' => __('client.about.vision.badge'),
                'description' => __('client.about.position.paragraph3'),
            ],
        ];
    @endphp

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
        <noscript>
            <style>
                [data-aos] {
                    opacity: 1 !important;
                    transform: none !important;
                }
            </style>
        </noscript>
        <style>
            .about-hero {
                position: relative;
                overflow: hidden;
                min-height: clamp(660px, 78vh, 860px);
                background: #04111f;
            }

            .about-hero::before {
                content: "";
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(90deg, rgba(2, 9, 21, 0.94), rgba(4, 17, 31, 0.76) 48%, rgba(255, 155, 0, 0.12)),
                    linear-gradient(180deg, rgba(4, 17, 31, 0.12), rgba(4, 17, 31, 0.62)),
                    url('/client/images/kingexpressbus/cabin/2.jpg');
                background-size: cover;
                background-position: 58% center;
                transform: scale(1.015);
                animation: about-hero-drift 16s ease-in-out infinite alternate;
            }

            .about-proof-strip {
                border-block: 1px solid rgba(255, 255, 255, 0.14);
                background: rgba(255, 255, 255, 0.045);
                backdrop-filter: blur(10px);
            }

            .about-hero-shot,
            .about-image-mask,
            .about-destination-tile {
                overflow: hidden;
                border-radius: 18px;
                background: #e2e8f0;
            }

            .about-hero-shot {
                position: relative;
                border: 1px solid rgba(255, 255, 255, 0.16);
                background: rgba(255, 255, 255, 0.06);
            }

            .about-hero-shot span {
                position: absolute;
                inset: auto 0 0;
                padding: 0.85rem;
                background: linear-gradient(180deg, transparent, rgba(2, 9, 21, 0.84));
                color: #ffffff;
                font-size: 0.78rem;
                font-weight: 800;
            }

            .about-stat-bar {
                border-block: 1px solid rgba(148, 163, 184, 0.24);
                background: #f7f8fa;
            }

            .about-editorial-band {
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(247, 248, 250, 0.96));
            }

            .about-hero-shot img,
            .about-image-mask img,
            .about-route-row img,
            .about-destination-tile img {
                transition: transform 560ms cubic-bezier(0.2, 0.8, 0.2, 1);
            }

            .about-hero-shot:hover img,
            .about-image-mask:hover img,
            .about-route-row:hover img,
            .about-destination-tile:hover img {
                transform: scale(1.045);
            }

            .about-timeline {
                border-left: 1px solid rgba(15, 23, 42, 0.16);
            }

            .about-timeline-dot {
                box-shadow: 0 0 0 6px #f7f8fa;
            }

            .about-value-row + .about-value-row {
                border-top: 1px solid rgba(226, 232, 240, 0.9);
            }

            .about-route-row {
                border: 1px solid rgba(226, 232, 240, 0.95);
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.92);
                transition: transform 220ms ease, border-color 220ms ease, box-shadow 220ms ease;
            }

            .about-route-row:hover {
                border-color: rgba(255, 155, 0, 0.46);
                box-shadow: 0 24px 60px -38px rgba(4, 17, 31, 0.48);
                transform: translateY(-2px);
            }

            .about-cta-band {
                background:
                    radial-gradient(circle at 88% 18%, rgba(255, 201, 0, 0.16), transparent 34%),
                    linear-gradient(100deg, #020915, #04111f 58%, #142033);
            }

            @keyframes about-hero-drift {
                from {
                    background-position: 56% center;
                    transform: scale(1.015);
                }

                to {
                    background-position: 62% center;
                    transform: scale(1.04);
                }
            }

            @media (max-width: 767px) {
                .about-hero {
                    min-height: 0;
                }

                .about-hero::before {
                    background-position: 64% center;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .about-hero::before,
                .about-hero-shot img,
                .about-image-mask img,
                .about-route-row img,
                .about-destination-tile img {
                    animation: none !important;
                    transition: none !important;
                    transform: none !important;
                }
            }
        </style>
    @endpush

    <section class="about-hero px-4 py-14 md:py-20 lg:py-24">
        <div class="container relative z-10 mx-auto w-full max-w-7xl">
            <div class="grid items-end gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(380px,0.78fr)] lg:gap-14">
                <div class="max-w-3xl text-white" data-aos="fade-up" data-aos-duration="650">
                    <p class="inline-flex items-center gap-2 border-l-2 border-accent pl-3 text-xs font-extrabold uppercase tracking-[0.04em] text-accent">
                        <i class="fa-solid fa-shield-halved"></i>
                        {{ __('client.about.hero.tagline') }}
                    </p>

                    <h1 class="ksb-text-balance mt-5 font-display text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">
                        {{ __('client.about.hero.title') }}
                    </h1>

                    <p class="mt-5 max-w-2xl text-lg font-semibold leading-8 text-accent md:text-2xl">
                        {{ __('client.about.hero.subtitle') }}
                    </p>

                    <p class="mt-4 max-w-2xl text-base leading-8 text-slate-100/88 md:text-lg">
                        {{ __('client.about.typing.1') }}
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('client.routes.index') }}" class="ksb-btn-primary px-6 text-sm">
                            <i class="fa-solid fa-ticket"></i>
                            {{ __('client.about_page.hero.primary_button') }}
                        </a>
                        <a href="#about-content" class="ksb-btn-ghost px-6 text-sm">
                            <i class="fa-solid fa-circle-info"></i>
                            {{ __('client.about_page.hero.secondary_button') }}
                        </a>
                    </div>
                </div>

                <aside class="space-y-3 text-white" data-aos="fade-left" data-aos-duration="700" data-aos-delay="120" aria-label="{{ __('client.about_page.hero.review_label') }}">
                    <div class="grid grid-cols-[1.12fr_0.88fr] gap-3">
                        <div class="about-hero-shot min-h-72">
                            <img src="/client/images/kingexpressbus/cabin/1.jpg" alt="{{ __('client.about_page.fleet_cards.cabin.name') }}" class="h-full w-full object-cover">
                            <span>{{ __('client.about_page.fleet_cards.cabin.name') }}</span>
                        </div>
                        <div class="grid gap-3">
                            <div class="about-hero-shot min-h-[8.25rem]">
                                <img src="/client/images/kingexpressbus/limousine/1.png" alt="{{ __('client.about_page.fleet_cards.limousine.name') }}" class="h-full w-full object-cover">
                                <span>{{ __('client.about_page.fleet_cards.limousine.name') }}</span>
                            </div>
                            <div class="about-hero-shot min-h-[8.25rem]">
                                <img src="/client/images/kingexpressbus/sleeper/1.jpg" alt="{{ __('client.about_page.fleet_cards.sleeper.name') }}" class="h-full w-full object-cover">
                                <span>{{ __('client.about_page.fleet_cards.sleeper.name') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="about-proof-strip p-5 md:p-6">
                        <p class="text-xs font-bold uppercase tracking-[0.04em] text-white/62">{{ __('client.about_page.hero.review_label') }}</p>
                        <blockquote class="mt-3 text-lg font-extrabold leading-7 text-white md:text-xl">
                            {{ __('client.about_page.hero.review_quote') }}
                        </blockquote>
                        <p class="mt-4 text-sm text-slate-200">{{ __('client.about_page.hero.review_author') }}</p>
                    </div>

                    <div class="about-proof-strip grid grid-cols-2 gap-5 p-5 md:p-6">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.04em] text-white/58">{{ __('client.about_page.hero.rating_label') }}</p>
                            <p class="mt-1 font-display text-3xl font-extrabold text-accent">4.9/5</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.04em] text-white/58">{{ __('client.about_page.hero.return_rate_label') }}</p>
                            <p class="mt-1 font-display text-3xl font-extrabold text-accent">92%</p>
                        </div>
                    </div>
                </aside>
            </div>

            <div class="ksb-hero-search ksb-dropdown relative z-dropdown mt-10" data-aos="fade-up" data-aos-duration="600" data-aos-delay="220">
                <x-client.search-bar :submit-label="__('client.about_page.hero.search_submit')" />
            </div>
        </div>
    </section>

    <section id="about-content" class="about-stat-bar px-4 py-7">
        <div class="container mx-auto max-w-7xl">
            <div class="grid gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($statItems as $item)
                    <article
                        data-aos="fade-up"
                        data-aos-duration="520"
                        data-aos-delay="{{ $loop->index * 70 }}"
                        class="px-2 text-center sm:px-6 lg:border-l lg:border-slate-200/80 first:lg:border-l-0"
                        x-data="statsCounter({{ (int) $item['value'] }}, {{ (int) $item['step'] }}, {{ (int) $item['speed'] }}, {{ $item['format'] ? 'true' : 'false' }})">
                        <p class="font-display text-4xl font-extrabold tabular-nums text-slate-950 md:text-5xl">
                            <span x-text="displayCount">0</span><span class="text-primary-600">+</span>
                        </p>
                        <p class="mt-2 text-xs font-extrabold uppercase tracking-[0.04em] text-slate-500">{{ $item['label'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ksb-section px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="grid gap-10 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-7" data-aos="fade-up" data-aos-duration="650">
                    <p class="ksb-section-label">{{ __('client.about_page.position.label') }}</p>
                    <h2 class="ksb-text-balance mt-3 font-display text-3xl font-extrabold leading-tight text-slate-950 md:text-5xl">
                        {{ __('client.about.position.title') }}
                    </h2>
                    <div class="mt-6 max-w-3xl space-y-5 text-base leading-8 text-slate-600 md:text-lg">
                        <p>{{ __('client.about.position.paragraph1') }}</p>
                        <p>{{ __('client.about.position.paragraph2') }}</p>
                        <p class="font-semibold text-slate-800">{{ __('client.about.position.paragraph3') }}</p>
                    </div>

                    <div class="mt-8 grid gap-4 border-t border-slate-200 pt-6 md:grid-cols-3">
                        @foreach(__('client.about.why_choose.items') as $item)
                            <div class="group" data-aos="fade-up" data-aos-duration="520" data-aos-delay="{{ $loop->index * 70 }}">
                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-700 transition group-hover:bg-primary-600 group-hover:text-white">
                                    <i class="{{ $item['icon'] }}"></i>
                                </span>
                                <h3 class="mt-4 text-base font-extrabold text-slate-950">{{ $item['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-500">{{ $item['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <aside class="lg:col-span-5" data-aos="fade-left" data-aos-duration="650">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="about-image-mask col-span-2">
                            <img src="/client/images/kingexpressbus/cabin/3.jpg" alt="{{ __('client.about.hero.image_alt') }}" loading="lazy" class="aspect-[16/10] w-full object-cover">
                        </div>
                        <div class="about-image-mask">
                            <img src="/client/images/kingexpressbus/cabin_double/1.jpg" alt="{{ __('client.about_page.fleet_cards.cabin.name') }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
                        </div>
                        <div class="about-image-mask">
                            <img src="/client/images/kingexpressbus/limousine/2.png" alt="{{ __('client.about_page.fleet_cards.limousine.name') }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
                        </div>
                    </div>

                    <div class="about-timeline mt-8 space-y-8 pl-6">
                        @foreach($timelineItems as $item)
                            <article class="relative" data-aos="fade-left" data-aos-duration="520" data-aos-delay="{{ $loop->index * 80 }}">
                                <span class="about-timeline-dot absolute -left-[31px] top-1 inline-flex h-3 w-3 rounded-full bg-primary-600"></span>
                                <p class="font-display text-2xl font-extrabold text-slate-950">{{ $item['year'] }}</p>
                                <h3 class="mt-1 text-sm font-extrabold uppercase tracking-[0.04em] text-primary-700">{{ $item['title'] }}</h3>
                                <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-500">{{ $item['description'] }}</p>
                            </article>
                        @endforeach
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="about-editorial-band ksb-section px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="grid gap-10 lg:grid-cols-12">
                <div class="lg:col-span-5" data-aos="fade-up" data-aos-duration="620">
                    <p class="ksb-section-label">{{ __('client.about.vision.title') }}</p>
                    <h2 class="ksb-text-balance mt-3 font-display text-3xl font-extrabold leading-tight text-slate-950 md:text-5xl">
                        {{ __('client.about.vision.badge') }}
                    </h2>
                </div>
                <div class="lg:col-span-7" data-aos="fade-up" data-aos-duration="620" data-aos-delay="90">
                    <p class="max-w-3xl text-lg font-semibold leading-9 text-slate-700 md:text-2xl">
                        {{ __('client.about.vision.description') }}
                    </p>
                </div>
            </div>

            <div class="mt-10 grid gap-3 md:grid-cols-3">
                <div class="about-image-mask md:col-span-2" data-aos="zoom-in" data-aos-duration="620">
                    <img src="/client/images/kingexpressbus/cabin/4.jpg" alt="{{ __('client.about.hero.image_alt') }}" loading="lazy" class="aspect-[16/7] w-full object-cover">
                </div>
                <div class="about-image-mask" data-aos="zoom-in" data-aos-duration="620" data-aos-delay="90">
                    <img src="/client/images/kingexpressbus/sleeper/2.jpg" alt="{{ __('client.about_page.fleet_cards.sleeper.name') }}" loading="lazy" class="aspect-[16/7] w-full object-cover md:aspect-auto md:h-full">
                </div>
            </div>

            <div class="mt-12 grid gap-8 border-t border-slate-200 pt-10 lg:grid-cols-2">
                <article data-aos="fade-up" data-aos-duration="580">
                    <p class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-700">
                        <i class="fa-solid fa-heart"></i>
                    </p>
                    <h3 class="mt-5 font-display text-2xl font-extrabold text-slate-950">{{ __('client.about.mission.with_customers.title') }}</h3>
                    <p class="mt-3 text-base leading-8 text-slate-600">{{ __('client.about.mission.with_customers.content') }}</p>
                </article>
                <article data-aos="fade-up" data-aos-duration="580" data-aos-delay="90">
                    <p class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-primary-50 text-primary-700">
                        <i class="fa-solid fa-earth-asia"></i>
                    </p>
                    <h3 class="mt-5 font-display text-2xl font-extrabold text-slate-950">{{ __('client.about.mission.with_society.title') }}</h3>
                    <p class="mt-3 text-base leading-8 text-slate-600">{{ __('client.about.mission.with_society.content') }}</p>
                </article>
            </div>

            <div class="mt-12 grid gap-8 border-t border-slate-200 pt-10 lg:grid-cols-[0.8fr_1.2fr]">
                <div data-aos="fade-up" data-aos-duration="580">
                    <p class="ksb-section-label">{{ __('client.about_page.core_values.label') }}</p>
                    <h2 class="mt-3 font-display text-3xl font-extrabold text-slate-950">{{ __('client.about.core_values.title') }}</h2>
                    <p class="mt-4 leading-7 text-slate-600">{{ __('client.about.core_values.description') }}</p>
                </div>
                <div>
                    @foreach(__('client.about.core_values.items') as $item)
                        <article class="about-value-row grid gap-4 py-5 md:grid-cols-[48px_1fr]" data-aos="fade-left" data-aos-duration="520" data-aos-delay="{{ $loop->index * 70 }}">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-white text-primary-700 shadow-soft">
                                <i class="{{ $item['icon'] }}"></i>
                            </span>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-950">{{ $item['title'] }}</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-500">{{ $item['description'] }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="ksb-section px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="grid gap-10 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-7" data-aos="fade-right" data-aos-duration="650">
                    <div class="about-image-mask">
                        <img src="/client/images/kingexpressbus/limousine/1.png" alt="{{ __('client.about.fleet.title') }}" loading="lazy" class="aspect-[16/10] w-full object-cover">
                    </div>
                </div>
                <div class="lg:col-span-5" data-aos="fade-left" data-aos-duration="650">
                    <p class="ksb-section-label">{{ __('client.about_page.fleet.label') }}</p>
                    <h2 class="ksb-text-balance mt-3 font-display text-3xl font-extrabold leading-tight text-slate-950 md:text-5xl">
                        {{ __('client.about.fleet.title') }}
                    </h2>
                    <p class="mt-5 text-base leading-8 text-slate-600">{{ __('client.about.fleet.description') }}</p>
                    <p class="mt-4 border-l-2 border-primary-600 pl-4 text-base font-semibold leading-8 text-slate-800">{{ __('client.about.fleet.highlight') }}</p>

                    <ul class="mt-7 space-y-3">
                        @foreach(__('client.about.fleet.features') as $feature)
                            <li class="flex items-start gap-3 text-sm font-semibold text-slate-700">
                                <i class="fa-solid fa-check mt-1 text-primary-600"></i>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                @foreach($fleetCards as $fleet)
                    <article class="group" data-aos="fade-up" data-aos-duration="520" data-aos-delay="{{ $loop->index * 80 }}">
                        <div class="about-image-mask">
                            <img src="{{ $fleet['image'] }}" alt="{{ $fleet['name'] }}" loading="lazy" class="aspect-[16/10] w-full object-cover">
                        </div>
                        <div class="mt-4 border-l border-slate-200 pl-4">
                            <h3 class="text-base font-extrabold text-slate-950">{{ $fleet['name'] }}</h3>
                            <p class="mt-1 text-sm text-slate-500">{{ $fleet['meta'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ksb-section bg-white px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4" data-aos="fade-up" data-aos-duration="580">
                <div>
                    <p class="ksb-section-label">{{ __('client.about_page.popular_routes.label') }}</p>
                    <h2 class="ksb-text-balance mt-2 font-display text-3xl font-extrabold text-slate-950 md:text-4xl">
                        {{ __('client.about_page.popular_routes.title') }}
                    </h2>
                </div>
                <a href="{{ route('client.routes.index') }}" class="ksb-btn-secondary px-4 text-sm">
                    {{ __('client.about_page.popular_routes.view_all') }}
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                @forelse ($popularRoutes as $route)
                    <a href="{{ route('client.routes.show', ['slug' => $route->slug]) }}" class="about-route-row group grid overflow-hidden sm:grid-cols-[164px_1fr]" data-aos="fade-up" data-aos-duration="520" data-aos-delay="{{ $loop->index * 70 }}">
                        <div class="relative min-h-40 overflow-hidden">
                            <img src="{{ \App\Helpers\SystemHelper::mediaUrl($route->thumbnail_url, \App\Helpers\SystemHelper::mediaUrl('/client/images/city_imgs/sapa.jpg')) }}"
                                alt="{{ $route->name }}"
                                loading="lazy"
                                class="h-full w-full object-cover">
                        </div>
                        <div class="grid gap-4 p-5 md:grid-cols-[1fr_auto] md:items-center">
                            <div class="min-w-0">
                                <h3 class="line-clamp-2 text-lg font-extrabold text-slate-950">{{ $route->name }}</h3>
                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-2 text-sm text-slate-500">
                                    <span class="inline-flex items-center gap-2">
                                        <i class="fa-regular fa-clock text-primary-600"></i>
                                        {{ $route->duration ?: __('client.booking.common.updating') }}
                                    </span>
                                    <span class="inline-flex items-center gap-2">
                                        <i class="fa-solid fa-route text-primary-600"></i>
                                        {{ __('client.about_page.popular_routes.trip_count', ['count' => $route->trip_count ?? 0]) }}
                                    </span>
                                </div>
                            </div>
                            <div class="border-t border-slate-100 pt-4 md:border-l md:border-t-0 md:pl-5 md:pt-0">
                                <p class="text-xs font-semibold uppercase tracking-[0.04em] text-slate-400">{{ __('client.about_page.popular_routes.price_from') }}</p>
                                <p class="mt-1 whitespace-nowrap font-display text-xl font-extrabold text-primary-700">
                                    {{ (int) $route->min_price > 0 ? number_format($route->min_price) . 'đ' : __('client.common.contact') }}
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                        {{ __('client.about_page.popular_routes.empty') }}
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="ksb-section px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="mb-8 grid gap-4 md:grid-cols-[0.8fr_1.2fr] md:items-end" data-aos="fade-up" data-aos-duration="580">
                <div>
                    <p class="ksb-section-label">{{ __('client.about_page.destinations.label') }}</p>
                    <h2 class="ksb-text-balance mt-2 font-display text-3xl font-extrabold text-slate-950 md:text-4xl">
                        {{ __('client.about_page.destinations.title') }}
                    </h2>
                </div>
                <p class="max-w-2xl text-base leading-7 text-slate-600 md:justify-self-end">{{ __('client.about_page.destinations.description') }}</p>
            </div>

            <div class="grid auto-rows-[180px] gap-3 sm:grid-cols-2 lg:grid-cols-5 lg:auto-rows-[160px]">
                @foreach($destinationCards as $destination)
                    <a href="{{ route('client.routes.index') }}"
                        class="about-destination-tile group relative {{ $loop->first ? 'sm:col-span-2 lg:col-span-2 lg:row-span-2' : '' }}"
                        data-aos="zoom-in"
                        data-aos-duration="520"
                        data-aos-delay="{{ $loop->index * 70 }}">
                        <img src="{{ $destination['image'] }}" alt="{{ $destination['name'] }}" loading="lazy" class="h-full w-full object-cover">
                        <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-navy-900/86 to-transparent p-4 text-lg font-extrabold text-white">
                            {{ $destination['name'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="about-cta-band px-4 py-14 md:py-16">
        <div class="container mx-auto max-w-7xl">
            <div class="grid items-center gap-8 text-white lg:grid-cols-[1.15fr_0.85fr]">
                <div data-aos="fade-up" data-aos-duration="620">
                    <p class="inline-flex border-l-2 border-accent pl-3 text-xs font-extrabold uppercase tracking-[0.04em] text-accent">{{ __('client.about_page.cta.label') }}</p>
                    <h2 class="ksb-text-balance mt-4 font-display text-3xl font-extrabold leading-tight md:text-5xl">
                        {{ __('client.about_page.cta.title') }}
                    </h2>
                    <p class="mt-4 max-w-2xl text-base leading-8 text-slate-300">{{ __('client.about_page.cta.description') }}</p>
                </div>
                <div class="flex flex-wrap gap-3 lg:justify-end" data-aos="fade-left" data-aos-duration="620" data-aos-delay="80">
                    <a href="{{ route('client.routes.index') }}" class="ksb-btn-primary px-6 text-sm">
                        <i class="fa-solid fa-bolt"></i>
                        {{ __('client.about_page.cta.primary_button') }}
                    </a>
                    <a href="{{ route('client.contact') }}" class="ksb-btn-ghost px-6 text-sm">
                        <i class="fa-solid fa-headset"></i>
                        {{ __('client.about_page.cta.secondary_button') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                if (window.AOS && !reduceMotion) {
                    AOS.init({
                        once: true,
                        offset: 80,
                        duration: 560,
                        easing: 'ease-out-cubic',
                    });
                    return;
                }

                document.querySelectorAll('[data-aos]').forEach((element) => {
                    element.classList.add('aos-animate');
                });
            });

            document.addEventListener('alpine:init', () => {
                Alpine.data('statsCounter', (target, step = 1, speed = 44, formatNumber = false) => ({
                    count: 0,
                    observer: null,
                    started: false,
                    get displayCount() {
                        return formatNumber ? this.count.toLocaleString() : this.count;
                    },
                    init() {
                        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                            this.count = target;
                            return;
                        }

                        if (!('IntersectionObserver' in window)) {
                            this.animate();
                            return;
                        }

                        this.observer = new IntersectionObserver((entries) => {
                            entries.forEach((entry) => {
                                if (entry.isIntersecting && !this.started) {
                                    this.started = true;
                                    this.animate();
                                    this.observer.disconnect();
                                }
                            });
                        }, { threshold: 0.4 });

                        this.observer.observe(this.$el);
                    },
                    animate() {
                        const timer = setInterval(() => {
                            if (this.count >= target) {
                                this.count = target;
                                clearInterval(timer);
                                return;
                            }

                            this.count = Math.min(this.count + step, target);
                        }, speed);
                    },
                }));
            });
        </script>
    @endpush

</x-client.layout>
