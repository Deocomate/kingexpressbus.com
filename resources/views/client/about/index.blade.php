{{-- ===== resources/views/client/about/index.blade.php ===== --}}
<x-client.layout :title="$title" :description="$description" body-class="bg-neutral-50">

    @push('styles')
        <style>
            /* Hero Background */
            .hero-about-bg {
                background-image: linear-gradient(135deg, rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.8)), url('/client/images/kingexpressbus/cabin/1.jpg');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
            }

            @media (max-width: 768px) {
                .hero-about-bg {
                    background-attachment: scroll;
                }
            }

            /* Stats Counter */
            .stat-number {
                font-variant-numeric: tabular-nums;
            }

            /* Gallery Item */
            .gallery-item {
                overflow: hidden;
            }

            .gallery-item img {
                transition: transform 0.3s ease;
            }

            .gallery-item:hover img {
                transform: scale(1.05);
            }
        </style>
    @endpush

    {{-- Hero Section --}}
    <section
        class="relative min-h-[500px] md:min-h-[600px] hero-about-bg flex flex-col justify-center items-center px-4 py-16 pt-24 lg:py-32">

        <div class="container relative z-10 w-full max-w-5xl space-y-4 md:space-y-6 text-center">
            {{-- Badge --}}
            <span
                class="inline-block py-1.5 px-4 rounded-md bg-accent-500/20 text-accent-300 border border-accent-400/30 text-xs md:text-sm font-semibold tracking-wider mb-2">
                <i class="fa-solid fa-crown mr-2"></i>{{ __('client.about.hero.tagline') }}
            </span>

            {{-- Hero Title --}}
            <h1
                class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold text-white leading-tight">
                {{ __('client.about.hero.title') }}
            </h1>

            {{-- Subtitle --}}
            <p class="text-lg md:text-xl lg:text-2xl text-accent-300 font-semibold">
                {{ __('client.about.hero.subtitle') }}
            </p>

            {{-- Description --}}
            <div class="mt-4 md:mt-6">
                <p class="text-base md:text-lg lg:text-xl text-neutral-200 max-w-2xl mx-auto px-2">
                    {{ __('client.about.typing.1') }}
                </p>
            </div>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center mt-6 md:mt-8">
                <a href="{{ route('client.routes.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 md:px-8 py-3 md:py-4 bg-accent-500 text-white rounded-md font-semibold text-base md:text-lg hover:bg-accent-600 transition-colors duration-200">
                    <i class="fa-solid fa-route"></i>
                    {{ __('client.about.cta.routes_button') }}
                </a>
                <a href="#about-content"
                    class="inline-flex items-center justify-center gap-2 px-6 md:px-8 py-3 md:py-4 bg-white/10 text-white rounded-md font-semibold text-base md:text-lg hover:bg-white/20 border border-white/20 transition-colors duration-200">
                    <i class="fa-solid fa-arrow-down"></i>
                    Tìm hiểu thêm
                </a>
            </div>
        </div>
    </section>

    {{-- Stats Bar --}}
    <section class="py-6 md:py-8 bg-white border-b border-neutral-100 relative -mt-6 md:-mt-8 z-20"
        id="about-content">
        <div class="container mx-auto px-4">
            <div class="bg-white rounded-lg shadow-card p-4 md:p-6 lg:p-8 grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 lg:gap-8 border border-neutral-200">
                <div class="text-center" x-data="statsCounter({{ $stats['route_count'] ?? 100 }}, 2, 30)">
                    <p class="text-2xl md:text-3xl lg:text-4xl font-semibold text-accent-500 stat-number">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-neutral-500 text-xs md:text-sm mt-1">{{ __('client.about.stats.routes') }}</p>
                </div>
                <div class="text-center" x-data="statsCounter({{ $stats['bus_count'] ?? 10 }}, 1, 40)">
                    <p class="text-2xl md:text-3xl lg:text-4xl font-semibold text-primary-600 stat-number">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-neutral-500 text-xs md:text-sm mt-1">{{ __('client.about.stats.buses', ['default' => 'Xe chất lượng']) }}</p>
                </div>
                <div class="text-center" x-data="statsCounter({{ $stats['booking_count'] ?? 10000 }}, 200, 30, true)">
                    <p class="text-2xl md:text-3xl lg:text-4xl font-semibold text-green-600 stat-number">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-neutral-500 text-xs md:text-sm mt-1">{{ __('client.about.stats.bookings') }}</p>
                </div>
                <div class="text-center" x-data="statsCounter({{ $stats['years_experience'] ?? 7 }}, 1, 100)">
                    <p class="text-2xl md:text-3xl lg:text-4xl font-semibold text-purple-600 stat-number">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-neutral-500 text-xs md:text-sm mt-1">{{ __('client.about.stats.years') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Position & Milestone Section --}}
    <section class="py-12 md:py-16 lg:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
                {{-- Text Content --}}
                <div class="space-y-4 md:space-y-6">
                    <span
                        class="inline-block py-1.5 px-4 rounded-md bg-accent-50 text-accent-700 font-semibold text-xs md:text-sm border border-accent-200">
                        <i class="fa-solid fa-award mr-2"></i>{{ __('client.about.position.badge') }}
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-neutral-900 leading-tight">
                        {{ __('client.about.position.title') }}
                    </h2>
                    <div class="space-y-3 md:space-y-4 text-base md:text-lg text-neutral-600 leading-relaxed">
                        <p>{{ __('client.about.position.paragraph1') }}</p>
                        <p>{{ __('client.about.position.paragraph2') }}</p>
                        <p class="font-medium text-neutral-800">{{ __('client.about.position.paragraph3') }}</p>
                    </div>
                </div>

                {{-- Images --}}
                <div class="relative">
                    <div class="grid grid-cols-2 gap-3 md:gap-4">
                        <div class="space-y-3 md:space-y-4">
                            <div class="rounded-lg overflow-hidden shadow-card gallery-item">
                                <img src="/client/images/kingexpressbus/cabin/2.jpg" alt="King Express Bus Cabin"
                                    class="w-full h-32 md:h-48 object-cover">
                            </div>
                            <div class="rounded-lg overflow-hidden shadow-card gallery-item">
                                <img src="/client/images/city_imgs/sapa.jpg" alt="Sa Pa"
                                    class="w-full h-24 md:h-32 object-cover">
                            </div>
                        </div>
                        <div class="space-y-3 md:space-y-4 pt-6 md:pt-8">
                            <div class="rounded-lg overflow-hidden shadow-card gallery-item">
                                <img src="/client/images/city_imgs/ninh-binh.jpg" alt="Ninh Bình"
                                    class="w-full h-24 md:h-32 object-cover">
                            </div>
                            <div class="rounded-lg overflow-hidden shadow-card gallery-item">
                                <img src="/client/images/kingexpressbus/cabin/3.jpg" alt="King Express Bus Interior"
                                    class="w-full h-32 md:h-48 object-cover">
                            </div>
                        </div>
                    </div>

                    {{-- Experience Badge --}}
                    <div
                        class="absolute -bottom-4 -left-4 md:-bottom-6 md:-left-6 bg-accent-500 p-3 md:p-4 rounded-lg shadow-card text-white hidden md:block z-10">
                        <div class="text-center">
                            <p class="text-2xl md:text-3xl font-semibold">7+</p>
                            <p class="text-[10px] md:text-xs font-medium opacity-90">
                                {{ __('client.about.stats.years') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Premium Fleet Section --}}
    <section class="py-12 md:py-16 lg:py-24 bg-neutral-800 text-white relative">
        <div class="container mx-auto px-4 relative z-10">
            <div class="grid lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
                {{-- Images Gallery --}}
                <div class="order-2 lg:order-1">
                    <div class="grid grid-cols-3 gap-2 md:gap-3">
                        <div class="col-span-2 rounded-lg overflow-hidden shadow-card gallery-item">
                            <img src="/client/images/kingexpressbus/cabin/4.jpg" alt="Limousine Cabin"
                                class="w-full h-40 md:h-64 object-cover">
                        </div>
                        <div class="rounded-lg overflow-hidden shadow-card gallery-item">
                            <img src="/client/images/kingexpressbus/cabin/5.jpg" alt="VIP Cabin"
                                class="w-full h-40 md:h-64 object-cover">
                        </div>
                        <div class="rounded-lg overflow-hidden shadow-card gallery-item">
                            <img src="/client/images/kingexpressbus/sleeper/1.jpg" alt="Sleeper Bus"
                                class="w-full h-28 md:h-40 object-cover">
                        </div>
                        <div class="col-span-2 rounded-lg overflow-hidden shadow-card gallery-item">
                            <img src="/client/images/kingexpressbus/sleeper/2.jpg" alt="Interior"
                                class="w-full h-28 md:h-40 object-cover">
                        </div>
                    </div>
                </div>

                {{-- Text Content --}}
                <div class="order-1 lg:order-2 space-y-4 md:space-y-6">
                    <span
                        class="inline-block py-1.5 px-4 rounded-md bg-accent-500/20 text-accent-300 font-semibold text-xs md:text-sm border border-accent-400/30">
                        <i class="fa-solid fa-bus mr-2"></i>{{ __('client.about.fleet.badge') }}
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold leading-tight">
                        {{ __('client.about.fleet.title') }}
                    </h2>
                    <p class="text-base md:text-lg text-neutral-300 leading-relaxed">
                        {{ __('client.about.fleet.description') }}
                    </p>
                    <p class="text-base md:text-lg text-accent-300 font-medium italic">
                        "{{ __('client.about.fleet.highlight') }}"
                    </p>

                    {{-- Features Grid --}}
                    <div class="grid grid-cols-2 gap-3 md:gap-4 pt-2 md:pt-4">
                        @foreach(__('client.about.fleet.features') as $feature)
                            <div
                                class="flex items-center gap-2 md:gap-3 p-2 md:p-3 rounded-md bg-white/5">
                                <div
                                    class="w-6 h-6 md:w-8 md:h-8 rounded-md bg-accent-500 flex items-center justify-center text-white">
                                    <i class="fa-solid fa-check text-xs md:text-sm"></i>
                                </div>
                                <span class="text-xs md:text-sm font-medium">{{ $feature }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Vision & Mission Section --}}
    <section class="py-12 md:py-16 lg:py-24 bg-neutral-50">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center mb-8 md:mb-12">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-neutral-900 mb-3 md:mb-4">
                    {{ __('client.about.vision.title') }} & {{ __('client.about.mission.title') }}
                </h2>
            </div>

            <div class="grid lg:grid-cols-2 gap-6 md:gap-8 max-w-5xl mx-auto">
                {{-- Vision Card --}}
                <div class="bg-white rounded-lg p-6 md:p-8 shadow-card border border-neutral-200">
                    <div class="flex items-center gap-3 md:gap-4 mb-4 md:mb-6">
                        <div
                            class="w-12 h-12 md:w-16 md:h-16 rounded-lg bg-accent-500 flex items-center justify-center text-white">
                            <i class="fa-solid fa-eye text-xl md:text-2xl"></i>
                        </div>
                        <div>
                            <span
                                class="text-[10px] md:text-xs font-semibold text-accent-600 uppercase tracking-wider">{{ __('client.about.vision.badge') }}</span>
                            <h3 class="text-xl md:text-2xl font-semibold text-neutral-900">
                                {{ __('client.about.vision.title') }}</h3>
                        </div>
                    </div>
                    <p class="text-neutral-600 leading-relaxed text-sm md:text-base">
                        {{ __('client.about.vision.description') }}
                    </p>
                </div>

                {{-- Mission Card --}}
                <div class="bg-white rounded-lg p-6 md:p-8 shadow-card border border-neutral-200">
                    <div class="flex items-center gap-3 md:gap-4 mb-4 md:mb-6">
                        <div
                            class="w-12 h-12 md:w-16 md:h-16 rounded-lg bg-primary-600 flex items-center justify-center text-white">
                            <i class="fa-solid fa-bullseye text-xl md:text-2xl"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-semibold text-neutral-900">{{ __('client.about.mission.title') }}
                        </h3>
                    </div>

                    <div class="space-y-3 md:space-y-4">
                        {{-- With Customers --}}
                        <div
                            class="p-3 md:p-4 rounded-md bg-accent-50 border-l-4 border-accent-400">
                            <h4 class="font-semibold text-neutral-900 mb-1 text-sm md:text-base">
                                <i
                                    class="fa-solid fa-heart text-accent-500 mr-2"></i>{{ __('client.about.mission.with_customers.title') }}
                            </h4>
                            <p class="text-neutral-600 text-xs md:text-sm">
                                {{ __('client.about.mission.with_customers.content') }}</p>
                        </div>

                        {{-- With Society --}}
                        <div
                            class="p-3 md:p-4 rounded-md bg-primary-50 border-l-4 border-primary-400">
                            <h4 class="font-semibold text-neutral-900 mb-1 text-sm md:text-base">
                                <i
                                    class="fa-solid fa-globe text-primary-500 mr-2"></i>{{ __('client.about.mission.with_society.title') }}
                            </h4>
                            <p class="text-neutral-600 text-xs md:text-sm">
                                {{ __('client.about.mission.with_society.content') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Core Values Section --}}
    <section class="py-12 md:py-16 lg:py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center mb-8 md:mb-12">
                <span
                    class="inline-block py-1.5 px-4 rounded-md bg-primary-50 text-primary-700 font-semibold text-xs md:text-sm border border-primary-200 mb-3 md:mb-4">
                    <i class="fa-solid fa-gem mr-2"></i>{{ __('client.about.core_values.badge') }}
                </span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-neutral-900 mb-3 md:mb-4">
                    {{ __('client.about.core_values.title') }}
                </h2>
                <p class="text-sm md:text-base lg:text-lg text-neutral-600 max-w-2xl mx-auto">
                    {{ __('client.about.core_values.description') }}
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 max-w-6xl mx-auto">
                @foreach(__('client.about.core_values.items') as $index => $item)
                    @php
                        $colors = ['accent', 'primary', 'red', 'purple'];
                        $color = $colors[$index % 4];
                        $bgColors = ['bg-accent-100 text-accent-600', 'bg-primary-100 text-primary-600', 'bg-red-100 text-red-600', 'bg-purple-100 text-purple-600'];
                    @endphp
                    <div class="bg-white p-4 md:p-6 rounded-lg shadow-soft hover:shadow-card transition-shadow duration-200 border border-neutral-200 text-center">
                        <div
                            class="w-12 h-12 md:w-16 md:h-16 rounded-lg {{ $bgColors[$index % 4] }} flex items-center justify-center mx-auto mb-3 md:mb-4">
                            <i class="{{ $item['icon'] }} text-xl md:text-2xl"></i>
                        </div>
                        <h3 class="text-base md:text-lg lg:text-xl font-semibold text-neutral-900 mb-1 md:mb-2">
                            {{ $item['title'] }}</h3>
                        <p class="text-neutral-600 text-xs md:text-sm">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Choose Us Section --}}
    <section class="py-12 md:py-16 lg:py-24 bg-neutral-50">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
                {{-- Text Content --}}
                <div class="space-y-6 md:space-y-8">
                    <div class="space-y-3 md:space-y-4">
                        <span
                            class="inline-block py-1.5 px-4 rounded-md bg-accent-50 text-accent-700 font-semibold text-xs md:text-sm border border-accent-200">
                            <i class="fa-solid fa-star mr-2"></i>{{ __('client.about.subtitle') }}
                        </span>
                        <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-neutral-900 leading-tight">
                            {{ __('client.about.why_choose.title') }}
                        </h2>
                        <p class="text-base md:text-lg text-neutral-600 leading-relaxed">
                            {{ __('client.about.why_choose.subtitle') }}
                        </p>
                    </div>

                    <ul class="space-y-4 md:space-y-6">
                        @foreach(__('client.about.why_choose.items') as $index => $item)
                            @php
                                $iconBgs = ['bg-accent-500', 'bg-primary-600', 'bg-green-600'];
                            @endphp
                            <li class="flex items-start gap-3 md:gap-4 p-3 md:p-4 rounded-md hover:bg-neutral-100 transition-colors duration-200">
                                <div
                                    class="w-10 h-10 md:w-14 md:h-14 rounded-lg {{ $iconBgs[$index % 3] }} flex items-center justify-center text-white shrink-0">
                                    <i class="{{ $item['icon'] }} text-lg md:text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-base md:text-lg lg:text-xl font-semibold text-neutral-900 mb-1 md:mb-2">
                                        {{ $item['title'] }}</h4>
                                    <p class="text-neutral-600 leading-relaxed text-sm md:text-base">{{ $item['description'] }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Image --}}
                <div class="relative">
                    <div class="relative rounded-lg overflow-hidden shadow-card group">
                        <img src="/client/images/kingexpressbus/cabin/1.jpg" alt="{{ __('client.about.image_alt') }}"
                            class="w-full object-cover aspect-[4/3] group-hover:scale-105 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent">
                        </div>
                    </div>

                    {{-- Floating Badge --}}
                    <div
                        class="absolute -bottom-4 -left-4 md:-bottom-6 md:-left-6 bg-white p-3 md:p-4 rounded-lg shadow-card hidden md:block z-10">
                        <div class="flex items-center gap-2 md:gap-3">
                            <div class="bg-accent-500 p-2 md:p-3 rounded-md text-white">
                                <i class="fa-solid fa-thumbs-up text-base md:text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs md:text-sm text-neutral-500 font-semibold">
                                    {{ __('client.about.badge_subtitle') }}
                                </p>
                                <p class="text-sm md:text-lg font-semibold text-neutral-900">98% Review 5★</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Image Gallery Section --}}
    <section class="py-12 md:py-16 lg:py-24 bg-neutral-800 text-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8 md:mb-12">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold mb-3 md:mb-4">
                    {{ __('client.about.gallery.title') }}</h2>
                <p class="text-neutral-400 text-sm md:text-base">{{ __('client.about.gallery.subtitle') }}</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 max-w-6xl mx-auto">
                <div class="col-span-2 row-span-2 rounded-lg overflow-hidden gallery-item">
                    <img src="/client/images/city_imgs/hue.jpg" alt="Huế" class="w-full h-full object-cover">
                </div>
                <div class="rounded-lg overflow-hidden gallery-item">
                    <img src="/client/images/city_imgs/hoi-an.jpg" alt="Hội An" class="w-full h-full object-cover">
                </div>
                <div class="rounded-lg overflow-hidden gallery-item">
                    <img src="/client/images/city_imgs/da-nang.jpg" alt="Đà Nẵng" class="w-full h-full object-cover">
                </div>
                <div class="rounded-lg overflow-hidden gallery-item">
                    <img src="/client/images/kingexpressbus/sleeper/3.jpg" alt="Sleeper Bus"
                        class="w-full h-full object-cover">
                </div>
                <div class="rounded-lg overflow-hidden gallery-item">
                    <img src="/client/images/city_imgs/ha-noi.jpg" alt="Hà Nội" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-12 md:py-16 lg:py-20 bg-accent-500">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 md:gap-8 text-center md:text-left">
                <div class="space-y-2 md:space-y-3 max-w-2xl">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold text-white">
                        {{ __('client.about.cta.title') }}
                    </h2>
                    <p class="text-white/80 text-base md:text-lg">
                        {{ __('client.about.cta.subtitle') }}
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 md:gap-4 w-full md:w-auto">
                    <a href="{{ route('client.home') }}"
                        class="inline-flex items-center justify-center gap-2 md:gap-3 px-6 md:px-8 py-3 md:py-4 bg-neutral-900 text-white rounded-md font-semibold text-base md:text-lg hover:bg-neutral-800 transition-colors duration-200">
                        <i class="fa-solid fa-ticket"></i>
                        {{ __('client.about.cta.button') }}
                    </a>
                    <a href="{{ route('client.routes.index') }}"
                        class="inline-flex items-center justify-center gap-2 md:gap-3 px-6 md:px-8 py-3 md:py-4 bg-white text-neutral-900 rounded-md font-semibold text-base md:text-lg hover:bg-neutral-100 transition-colors duration-200">
                        <i class="fa-solid fa-route"></i>
                        {{ __('client.about.cta.routes_button') }}
                    </a>
                </div>
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
                        return this.formatNumber ? this.count.toLocaleString() : this.count;
                    },
                    init() {
                        const observer = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting && !this.started) {
                                    this.started = true;
                                    this.animate();
                                }
                            });
                        }, { threshold: 0.5 });
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
            });
        </script>
    @endpush

</x-client.layout>
