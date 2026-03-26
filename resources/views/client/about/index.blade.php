{{-- ===== resources/views/client/about/index.blade.php ===== --}}
<x-client.layout :title="$title" :description="$description" body-class="bg-[#F8FAFC]">

    @php
        $fleetCards = [
            [
                'name' => 'Cabin VIP',
                'image' => '/client/images/kingexpressbus/cabin/1.jpg',
                'meta' => 'Riêng tư, êm ái, premium',
            ],
            [
                'name' => 'Limousine',
                'image' => '/client/images/kingexpressbus/limousine/1.png',
                'meta' => 'Ghế rộng, tiện nghi thế hệ mới',
            ],
            [
                'name' => 'Sleeper',
                'image' => '/client/images/kingexpressbus/sleeper/1.jpg',
                'meta' => 'Phù hợp hành trình đêm đường dài',
            ],
        ];

        $destinationCards = [
            ['name' => 'Sa Pa', 'image' => '/client/images/city_imgs/sapa.jpg'],
            ['name' => 'Hà Nội', 'image' => '/client/images/city_imgs/ha-noi.jpg'],
            ['name' => 'Ninh Bình', 'image' => '/client/images/city_imgs/ninh-binh.jpg'],
            ['name' => 'Đà Nẵng', 'image' => '/client/images/city_imgs/da-nang.jpg'],
            ['name' => 'Hội An', 'image' => '/client/images/city_imgs/hoi-an.jpg'],
        ];
    @endphp

    @push('styles')
        <style>
            @keyframes about-hero-pan {
                0% {
                    background-position: center top;
                }

                100% {
                    background-position: center bottom;
                }
            }

            .about-hero-bg {
                background-image:
                    linear-gradient(114deg, rgba(9, 25, 47, 0.86), rgba(255, 155, 0, 0.56)),
                    url('/client/images/kingexpressbus/cabin/2.jpg');
                background-size: cover;
                background-position: center;
                animation: about-hero-pan 18s ease-in-out infinite alternate;
            }

            .about-glass {
                backdrop-filter: blur(8px);
                background: linear-gradient(140deg, rgba(255, 255, 255, 0.23), rgba(255, 255, 255, 0.08));
            }

            .about-grid-card:hover .about-grid-image {
                transform: scale(1.06);
            }

            @media (prefers-reduced-motion: reduce) {
                .about-hero-bg {
                    animation: none;
                }

                .about-grid-card,
                .about-grid-image {
                    transition: none !important;
                }
            }
        </style>
    @endpush

    {{-- HEADER --}}
    {{-- Header được render trong x-client.layout --}}

    {{-- HERO/SEARCH --}}
    <section class="about-hero-bg relative overflow-hidden px-4 pb-14 pt-24 md:pb-20 md:pt-28">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_10%,rgba(255,225,0,0.28),transparent_42%),radial-gradient(circle_at_85%_90%,rgba(235,227,137,0.24),transparent_44%)]"></div>

        <div class="container mx-auto max-w-7xl relative z-10">
            <div class="grid items-center gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <span class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/30 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-white">
                        <i class="fa-solid fa-crown"></i>
                        {{ __('client.about.hero.tagline') }}
                    </span>
                    <h1 class="text-3xl font-extrabold leading-tight text-white sm:text-4xl lg:text-6xl">
                        {{ __('client.about.hero.title') }}
                    </h1>
                    <p class="mt-4 text-base font-semibold text-accent sm:text-lg lg:text-xl">
                        {{ __('client.about.hero.subtitle') }}
                    </p>
                    <p class="mt-3 max-w-2xl text-sm text-slate-100/95 sm:text-base lg:text-lg">
                        {{ __('client.about.typing.1') }}
                    </p>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('client.routes.search') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-3 text-sm font-bold text-white shadow-lg transition hover:bg-primary-700 active:scale-95">
                            <i class="fa-solid fa-ticket"></i>
                            Đặt vé ngay
                        </a>
                        <a href="#about-content"
                            class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/10 px-5 py-3 text-sm font-bold text-white transition hover:bg-white/20 active:scale-95">
                            <i class="fa-solid fa-circle-info"></i>
                            Tìm hiểu thêm
                        </a>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div class="about-glass rounded-2xl border border-white/20 p-4 text-white shadow-soft sm:col-span-2">
                        <p class="text-xs uppercase tracking-wide text-white/80">Khách hàng nói gì</p>
                        <p class="mt-2 text-lg font-extrabold md:text-xl">"Đặt vé cực nhanh, giao diện dễ hiểu và mượt trên điện thoại."</p>
                        <p class="mt-2 text-xs text-white/85">Minh Anh · Tuyến Hà Nội - Sa Pa</p>
                    </div>
                    <div class="about-glass rounded-2xl border border-white/20 p-4 text-white shadow-soft">
                        <p class="text-xs uppercase tracking-wide text-white/80">Đánh giá trung bình</p>
                        <p class="mt-2 text-2xl font-extrabold">4.9/5</p>
                    </div>
                    <div class="about-glass rounded-2xl border border-white/20 p-4 text-white shadow-soft">
                        <p class="text-xs uppercase tracking-wide text-white/80">Tỷ lệ quay lại</p>
                        <p class="mt-2 text-2xl font-extrabold">92%</p>
                    </div>
                </div>
            </div>

            <div class="relative z-40 mt-8" style="z-index: 90;">
                <x-client.search-bar submit-label="Tìm chuyến ngay" />
            </div>
        </div>
    </section>

    {{-- MAIN CONTENT --}}
    <section id="about-content" class="relative -mt-6 px-4 md:-mt-8">
        <div class="container mx-auto max-w-7xl">
            <div class="grid gap-3 rounded-2xl border border-amber-100 bg-white p-4 shadow-soft sm:grid-cols-2 lg:grid-cols-4 lg:p-6">
                <article class="rounded-xl bg-slate-50 p-4 text-center" x-data="statsCounter({{ $stats['route_count'] ?? 100 }}, 2, 26)">
                    <p class="text-2xl font-extrabold text-primary-600"><span x-text="displayCount">0</span>+</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('client.about.stats.routes') }}</p>
                </article>
                <article class="rounded-xl bg-slate-50 p-4 text-center" x-data="statsCounter({{ $stats['bus_count'] ?? 10 }}, 1, 36)">
                    <p class="text-2xl font-extrabold text-primary-600"><span x-text="displayCount">0</span>+</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('client.about.stats.buses') }}</p>
                </article>
                <article class="rounded-xl bg-slate-50 p-4 text-center" x-data="statsCounter({{ $stats['booking_count'] ?? 10000 }}, 200, 24, true)">
                    <p class="text-2xl font-extrabold text-primary-600"><span x-text="displayCount">0</span>+</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('client.about.stats.bookings') }}</p>
                </article>
                <article class="rounded-xl bg-slate-50 p-4 text-center" x-data="statsCounter({{ $stats['years_experience'] ?? 7 }}, 1, 100)">
                    <p class="text-2xl font-extrabold text-primary-600"><span x-text="displayCount">0</span>+</p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('client.about.stats.years') }}</p>
                </article>
            </div>
        </div>
    </section>

    <section class="px-4 py-12 md:py-16">
        <div class="container mx-auto max-w-7xl">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <article class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-7">
                    <p class="text-xs font-bold uppercase tracking-wider text-primary-600">Vị thế thương hiệu</p>
                    <h2 class="mt-2 text-2xl font-extrabold text-slate-800 md:text-4xl">{{ __('client.about.position.title') }}</h2>
                    <div class="mt-4 space-y-3 text-sm leading-relaxed text-slate-600 md:text-base">
                        <p>{{ __('client.about.position.paragraph1') }}</p>
                        <p>{{ __('client.about.position.paragraph2') }}</p>
                        <p class="font-semibold text-slate-700">{{ __('client.about.position.paragraph3') }}</p>
                    </div>

                    <ul class="mt-6 space-y-3">
                        @foreach(__('client.about.why_choose.items') as $item)
                            <li class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                                <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-700">
                                    <i class="{{ $item['icon'] }}"></i>
                                </span>
                                <span>
                                    <span class="block text-sm font-bold text-slate-800">{{ $item['title'] }}</span>
                                    <span class="mt-0.5 block text-xs text-slate-500 md:text-sm">{{ $item['description'] }}</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </article>

                <aside x-data="{ tab: 'vision' }" class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-7">
                    <div class="mb-5 flex flex-wrap gap-2 rounded-xl bg-slate-100 p-1" role="tablist" aria-label="Giá trị thương hiệu">
                        <button type="button" @click="tab = 'vision'"
                            id="about-vision-tab"
                            role="tab"
                            class="flex-1 rounded-lg px-3 py-2 text-sm font-bold transition"
                            :aria-selected="tab === 'vision'"
                            aria-controls="about-vision-panel"
                            :class="tab === 'vision' ? 'bg-white text-primary-700 shadow-soft' : 'text-slate-500 hover:text-slate-700'">
                            {{ __('client.about.vision.title') }}
                        </button>
                        <button type="button" @click="tab = 'mission'"
                            id="about-mission-tab"
                            role="tab"
                            class="flex-1 rounded-lg px-3 py-2 text-sm font-bold transition"
                            :aria-selected="tab === 'mission'"
                            aria-controls="about-mission-panel"
                            :class="tab === 'mission' ? 'bg-white text-primary-700 shadow-soft' : 'text-slate-500 hover:text-slate-700'">
                            {{ __('client.about.mission.title') }}
                        </button>
                    </div>

                    <div id="about-vision-panel" role="tabpanel" aria-labelledby="about-vision-tab" x-show="tab === 'vision'" x-transition.opacity.duration.220ms>
                        <div class="rounded-2xl border border-amber-100 bg-primary-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-primary-700">{{ __('client.about.vision.badge') }}</p>
                            <p class="mt-2 text-sm leading-relaxed text-slate-700 md:text-base">{{ __('client.about.vision.description') }}</p>
                        </div>
                    </div>

                    <div id="about-mission-panel" role="tabpanel" aria-labelledby="about-mission-tab" x-show="tab === 'mission'" x-transition.opacity.duration.220ms>
                        <div class="space-y-3">
                            <div class="rounded-xl border border-amber-100 bg-[#FFFDF3] p-4">
                                <p class="text-sm font-bold text-slate-800">
                                    <i class="fa-solid fa-heart text-primary-600"></i>
                                    {{ __('client.about.mission.with_customers.title') }}
                                </p>
                                <p class="mt-1.5 text-xs text-slate-600 md:text-sm">{{ __('client.about.mission.with_customers.content') }}</p>
                            </div>
                            <div class="rounded-xl border border-amber-100 bg-[#FFFDF3] p-4">
                                <p class="text-sm font-bold text-slate-800">
                                    <i class="fa-solid fa-earth-asia text-primary-600"></i>
                                    {{ __('client.about.mission.with_society.title') }}
                                </p>
                                <p class="mt-1.5 text-xs text-slate-600 md:text-sm">{{ __('client.about.mission.with_society.content') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 rounded-2xl overflow-hidden border border-amber-100">
                        <img src="/client/images/kingexpressbus/cabin/3.jpg" alt="King Express Bus" loading="lazy"
                            class="h-52 w-full object-cover md:h-60">
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="bg-white px-4 py-12 md:py-16">
        <div class="container mx-auto max-w-7xl">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-primary-600">Popular routes</p>
                    <h3 class="mt-1 text-2xl font-extrabold text-slate-800 md:text-4xl">Tuyến nổi bật từ King Express</h3>
                </div>
                <a href="{{ route('client.routes.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-primary-600/25 bg-white px-4 py-2 text-sm font-bold text-primary-700 transition hover:bg-primary-50 active:scale-95">
                    Xem toàn bộ tuyến
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse ($popularRoutes as $route)
                    <a href="{{ route('client.routes.show', ['slug' => $route->slug]) }}"
                        class="about-grid-card overflow-hidden rounded-2xl border border-amber-100 bg-[#fffdf8] shadow-soft hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
                        <div class="relative h-44 overflow-hidden">
                            <img src="{{ $route->thumbnail_url ?: '/client/images/city_imgs/sapa.jpg' }}" alt="{{ $route->name }}" loading="lazy"
                                class="about-grid-image h-full w-full object-cover transition duration-500">
                            <div class="absolute inset-0 bg-linear-to-t from-slate-900/70 via-slate-900/5 to-transparent"></div>
                            <p class="absolute bottom-3 left-3 right-3 line-clamp-1 text-sm font-extrabold text-white">{{ $route->name }}</p>
                        </div>
                        <div class="space-y-3 p-4">
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-1">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $route->duration ?: 'Đang cập nhật' }}
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-1">
                                    <i class="fa-solid fa-route"></i>
                                    {{ $route->trip_count ?? 0 }} chuyến
                                </span>
                            </div>
                            <div class="flex items-end justify-between border-t border-amber-100 pt-3">
                                <p class="text-xs text-slate-500">Giá từ</p>
                                <p class="text-lg font-extrabold text-primary-600">
                                    {{ (int) $route->min_price > 0 ? number_format($route->min_price) . 'đ' : 'Liên hệ' }}
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                        Chưa có tuyến nổi bật để hiển thị.
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="px-4 py-12 md:py-16">
        <div class="container mx-auto max-w-7xl">
            <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <article class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-7">
                    <p class="text-xs font-bold uppercase tracking-wider text-primary-600">Premium fleet</p>
                    <h3 class="mt-1 text-2xl font-extrabold text-slate-800 md:text-4xl">{{ __('client.about.fleet.title') }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600 md:text-base">{{ __('client.about.fleet.description') }}</p>

                    <div class="mt-5 grid gap-3 sm:grid-cols-3">
                        @foreach($fleetCards as $fleet)
                            <article class="about-grid-card overflow-hidden rounded-2xl border border-amber-100 bg-[#fffdf8] shadow-soft hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
                                <div class="h-36 overflow-hidden">
                                    <img src="{{ $fleet['image'] }}" alt="{{ $fleet['name'] }}" loading="lazy"
                                        class="about-grid-image h-full w-full object-cover transition duration-500">
                                </div>
                                <div class="p-3">
                                    <p class="text-sm font-bold text-slate-800">{{ $fleet['name'] }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $fleet['meta'] }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </article>

                <article class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft md:p-7">
                    <p class="text-xs font-bold uppercase tracking-wider text-primary-600">Core values</p>
                    <h3 class="mt-1 text-2xl font-extrabold text-slate-800 md:text-3xl">{{ __('client.about.core_values.title') }}</h3>
                    <p class="mt-3 text-sm text-slate-600 md:text-base">{{ __('client.about.core_values.description') }}</p>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        @foreach(__('client.about.core_values.items') as $item)
                            <div class="rounded-xl border border-amber-100 bg-[#FFFDF3] p-3">
                                <p class="text-sm font-bold text-slate-800"><i class="{{ $item['icon'] }} text-primary-600"></i> {{ $item['title'] }}</p>
                                <p class="mt-1 text-xs text-slate-500 md:text-sm">{{ $item['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="bg-white px-4 py-12 md:py-16">
        <div class="container mx-auto max-w-7xl">
            <div class="mb-8 text-center">
                <p class="text-xs font-bold uppercase tracking-wider text-primary-600">Destinations</p>
                <h3 class="mt-1 text-2xl font-extrabold text-slate-800 md:text-4xl">Điểm đến truyền cảm hứng</h3>
                <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-500 md:text-base">Mỗi chuyến đi là một hành trình trải nghiệm văn hóa, ẩm thực và thiên nhiên theo cách trẻ trung, linh hoạt.</p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach($destinationCards as $destination)
                    <a href="{{ route('client.routes.index') }}"
                        class="about-grid-card group relative overflow-hidden rounded-2xl border border-amber-100 shadow-soft hover:-translate-y-1 hover:shadow-xl transition-all duration-300 {{ $loop->first ? 'lg:col-span-2 lg:row-span-2 h-72' : 'h-52' }}">
                        <img src="{{ $destination['image'] }}" alt="{{ $destination['name'] }}" loading="lazy"
                            class="about-grid-image h-full w-full object-cover transition duration-500">
                        <div class="absolute inset-0 bg-linear-to-t from-slate-900/70 via-slate-900/10 to-transparent"></div>
                        <p class="absolute bottom-3 left-3 text-lg font-bold text-white">{{ $destination['name'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="px-4 pb-14 pt-12 md:pb-20 md:pt-16">
        <div class="container mx-auto max-w-7xl">
            <div class="relative overflow-hidden rounded-3xl border border-slate-800 bg-slate-900 shadow-soft">
                <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_18%_14%,rgba(255,201,0,0.24),transparent_42%),radial-gradient(circle_at_85%_92%,rgba(255,155,0,0.28),transparent_44%)]"></div>
                <div class="relative grid items-center gap-6 p-6 text-white md:p-12 lg:grid-cols-[1.15fr_0.85fr]">
                    <div>
                        <p class="inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-amber-200">Ready to ride</p>
                        <h3 class="mt-3 text-2xl font-extrabold leading-tight md:text-4xl">Sẵn sàng cho hành trình tiếp theo cùng King Express Bus?</h3>
                        <p class="mt-4 max-w-2xl text-sm text-slate-200 md:text-base">Tối ưu thao tác tìm chuyến, chọn chỗ và xác nhận đặt vé theo một luồng trực quan, thân thiện cho người dùng trẻ.</p>
                    </div>
                    <div class="flex flex-wrap gap-3 lg:justify-end">
                        <a href="{{ route('client.routes.search') }}"
                            class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-bold text-white shadow-soft transition hover:bg-primary-700 active:scale-95">
                            <i class="fa-solid fa-bolt"></i>
                            Tìm chuyến ngay
                        </a>
                        <a href="{{ route('client.contact') }}"
                            class="inline-flex items-center gap-2 rounded-xl border border-white/25 bg-white/10 px-6 py-3 text-sm font-bold text-white transition hover:bg-white/20 active:scale-95">
                            <i class="fa-solid fa-headset"></i>
                            Liên hệ tư vấn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    {{-- Footer được render trong x-client.layout --}}

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('statsCounter', (target, step = 1, speed = 30, formatNumber = false) => ({
                    count: 0,
                    started: false,
                    get displayCount() {
                        return formatNumber ? this.count.toLocaleString() : this.count;
                    },
                    init() {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach((entry) => {
                                if (entry.isIntersecting && !this.started) {
                                    this.started = true;
                                    this.animate();
                                }
                            });
                        }, { threshold: 0.35 });

                        observer.observe(this.$el);
                    },
                    animate() {
                        const timer = setInterval(() => {
                            if (this.count >= target) {
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
