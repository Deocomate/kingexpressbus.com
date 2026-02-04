{{-- ===== resources/views/client/about/index.blade.php ===== --}}
<x-client.layout :title="$title" :description="$description" body-class="bg-gray-50 overflow-x-hidden">

    @push('styles')
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <style>
            /* Prevent horizontal scroll */
            html,
            body {
                overflow-x: hidden;
            }

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

            /* Typing Cursor Animation */
            .typing-cursor {
                animation: blink 1s step-end infinite;
            }

            @keyframes blink {
                50% {
                    opacity: 0;
                }
            }

            /* Floating Animation */
            .float-animation {
                animation: float 3s ease-in-out infinite;
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-10px);
                }
            }

            /* Stats Counter Animation */
            .stat-number {
                font-variant-numeric: tabular-nums;
            }

            /* Feature Card Hover */
            .feature-card {
                transition: all 0.3s ease;
            }

            .feature-card:hover {
                transform: translateY(-8px);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            }

            .feature-card:hover .feature-icon {
                transform: scale(1.1) rotate(-5deg);
            }

            .feature-icon {
                transition: transform 0.3s ease;
            }

            /* Glass Effect */
            .glass-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            /* Image Gallery Hover */
            .gallery-item {
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .gallery-item:hover {
                transform: scale(1.03);
                z-index: 10;
            }

            .gallery-item:hover img {
                transform: scale(1.1);
            }

            .gallery-item img {
                transition: transform 0.5s ease;
            }

            /* Value Card Gradient */
            .value-card {
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                transition: all 0.3s ease;
            }

            .value-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.1);
            }

            /* Mission Card */
            .mission-card {
                transition: all 0.3s ease;
            }

            .mission-card:hover {
                transform: translateX(5px);
            }

            /* Pulse Ring */
            .pulse-ring {
                animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }

            @keyframes pulse-ring {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }
            }

            /* Gradient Text */
            .gradient-text {
                background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>
    @endpush

    {{-- Hero Section with Typing Effect --}}
    <section
        class="relative min-h-[500px] md:min-h-[600px] hero-about-bg flex flex-col justify-center items-center px-4 py-16 pt-24 lg:py-32 overflow-hidden"
        x-data="aboutHeroTypewriter">

        <div class="container relative z-10 w-full max-w-5xl space-y-4 md:space-y-6 text-center" data-aos="fade-up"
            data-aos-duration="800">
            {{-- Badge --}}
            <span
                class="inline-block py-1.5 px-4 rounded-full bg-yellow-400/20 text-yellow-300 border border-yellow-400/30 text-xs md:text-sm font-semibold tracking-wider mb-2 backdrop-blur-sm"
                data-aos="fade-up" data-aos-delay="100">
                <i class="fa-solid fa-crown mr-2"></i>{{ __('client.about.hero.tagline') }}
            </span>

            {{-- Hero Title --}}
            <h1
                class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold text-white leading-tight drop-shadow-lg">
                {{ __('client.about.hero.title') }}
            </h1>

            {{-- Subtitle --}}
            <p class="text-lg md:text-xl lg:text-2xl text-yellow-400 font-semibold" data-aos="fade-up"
                data-aos-delay="150">
                {{ __('client.about.hero.subtitle') }}
            </p>

            {{-- Typing Effect --}}
            <div class="min-h-[50px] md:min-h-[60px] mt-4 md:mt-6" data-aos="fade-up" data-aos-delay="200">
                <p class="text-base md:text-lg lg:text-xl text-gray-200 max-w-2xl mx-auto px-2">
                    <span x-text="text"></span><span class="typing-cursor text-yellow-400">|</span>
                </p>
            </div>

            {{-- CTA Buttons --}}
            <div class="flex flex-col sm:flex-row gap-3 md:gap-4 justify-center mt-6 md:mt-8" data-aos="fade-up"
                data-aos-delay="250">
                <a href="{{ route('client.routes.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-6 md:px-8 py-3 md:py-4 bg-yellow-400 text-gray-900 rounded-full font-bold text-base md:text-lg hover:bg-yellow-300 transform hover:scale-105 transition-all shadow-xl">
                    <i class="fa-solid fa-route"></i>
                    {{ __('client.about.cta.routes_button') }}
                </a>
                <a href="#about-content"
                    class="inline-flex items-center justify-center gap-2 px-6 md:px-8 py-3 md:py-4 bg-white/10 text-white rounded-full font-bold text-base md:text-lg hover:bg-white/20 backdrop-blur-sm border border-white/30 transition-all">
                    <i class="fa-solid fa-arrow-down"></i>
                    Tìm hiểu thêm
                </a>
            </div>
        </div>

        {{-- Scroll Down Indicator --}}
        <div class="absolute bottom-6 md:bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce text-white/70">
            <i class="fa-solid fa-chevron-down text-xl md:text-2xl"></i>
        </div>
    </section>

    {{-- Stats Bar --}}
    <section class="py-6 md:py-8 bg-white border-b border-gray-100 relative -mt-6 md:-mt-8 z-20 overflow-hidden"
        id="about-content">
        <div class="container mx-auto px-4">
            <div class="glass-card rounded-xl md:rounded-2xl shadow-xl p-4 md:p-6 lg:p-8 grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 lg:gap-8"
                data-aos="fade-up" data-aos-offset="-50">
                <div class="text-center" x-data="statsCounter({{ $stats['route_count'] ?? 100 }}, 2, 30)">
                    <p class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-yellow-500 stat-number">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-gray-500 text-xs md:text-sm mt-1">{{ __('client.about.stats.routes') }}</p>
                </div>
                <div class="text-center" x-data="statsCounter({{ $stats['bus_count'] ?? 10 }}, 1, 40)">
                    <p class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-blue-500 stat-number">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-gray-500 text-xs md:text-sm mt-1">{{ __('client.about.stats.buses', ['default' => 'Xe chất lượng']) }}</p>
                </div>
                <div class="text-center" x-data="statsCounter({{ $stats['booking_count'] ?? 10000 }}, 200, 30, true)">
                    <p class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-green-500 stat-number">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-gray-500 text-xs md:text-sm mt-1">{{ __('client.about.stats.bookings') }}</p>
                </div>
                <div class="text-center" x-data="statsCounter({{ $stats['years_experience'] ?? 7 }}, 1, 100)">
                    <p class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-purple-500 stat-number">
                        <span x-text="displayCount">0</span>+
                    </p>
                    <p class="text-gray-500 text-xs md:text-sm mt-1">{{ __('client.about.stats.years') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Position & Milestone Section --}}
    <section class="py-12 md:py-16 lg:py-24 bg-white overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
                {{-- Text Content --}}
                <div class="space-y-4 md:space-y-6" data-aos="fade-up" data-aos-duration="800">
                    <span
                        class="inline-block py-1.5 px-4 rounded-full bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-700 font-semibold text-xs md:text-sm border border-yellow-200">
                        <i class="fa-solid fa-award mr-2"></i>{{ __('client.about.position.badge') }}
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">
                        {{ __('client.about.position.title') }}
                    </h2>
                    <div class="space-y-3 md:space-y-4 text-base md:text-lg text-gray-600 leading-relaxed">
                        <p>{{ __('client.about.position.paragraph1') }}</p>
                        <p>{{ __('client.about.position.paragraph2') }}</p>
                        <p class="font-medium text-gray-800">{{ __('client.about.position.paragraph3') }}</p>
                    </div>
                </div>

                {{-- Images --}}
                <div class="relative" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                    <div class="grid grid-cols-2 gap-3 md:gap-4">
                        <div class="space-y-3 md:space-y-4">
                            <div class="rounded-xl md:rounded-2xl overflow-hidden shadow-lg gallery-item">
                                <img src="/client/images/kingexpressbus/cabin/2.jpg" alt="King Express Bus Cabin"
                                    class="w-full h-32 md:h-48 object-cover">
                            </div>
                            <div class="rounded-xl md:rounded-2xl overflow-hidden shadow-lg gallery-item">
                                <img src="/client/images/city_imgs/sapa.jpg" alt="Sa Pa"
                                    class="w-full h-24 md:h-32 object-cover">
                            </div>
                        </div>
                        <div class="space-y-3 md:space-y-4 pt-6 md:pt-8">
                            <div class="rounded-xl md:rounded-2xl overflow-hidden shadow-lg gallery-item">
                                <img src="/client/images/city_imgs/ninh-binh.jpg" alt="Ninh Bình"
                                    class="w-full h-24 md:h-32 object-cover">
                            </div>
                            <div class="rounded-xl md:rounded-2xl overflow-hidden shadow-lg gallery-item">
                                <img src="/client/images/kingexpressbus/cabin/3.jpg" alt="King Express Bus Interior"
                                    class="w-full h-32 md:h-48 object-cover">
                            </div>
                        </div>
                    </div>

                    {{-- Floating Badge --}}
                    <div
                        class="absolute -bottom-4 -left-4 md:-bottom-6 md:-left-6 bg-gradient-to-br from-yellow-400 to-orange-500 p-3 md:p-4 rounded-xl md:rounded-2xl shadow-xl text-white float-animation hidden md:block z-10">
                        <div class="text-center">
                            <p class="text-2xl md:text-3xl font-extrabold">7+</p>
                            <p class="text-[10px] md:text-xs font-medium opacity-90">
                                {{ __('client.about.stats.years') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Premium Fleet Section --}}
    <section
        class="py-12 md:py-16 lg:py-24 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 text-white relative overflow-hidden">
        {{-- Decorative Elements - Contained --}}
        <div
            class="absolute top-20 right-0 w-40 md:w-64 h-40 md:h-64 bg-yellow-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 float-animation">
        </div>
        <div class="absolute bottom-20 left-0 w-40 md:w-64 h-40 md:h-64 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10"
            style="animation: float 4s ease-in-out infinite 1s;"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="grid lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
                {{-- Images Gallery --}}
                <div class="order-2 lg:order-1" data-aos="fade-up" data-aos-duration="800">
                    <div class="grid grid-cols-3 gap-2 md:gap-3">
                        <div class="col-span-2 rounded-xl md:rounded-2xl overflow-hidden shadow-2xl gallery-item">
                            <img src="/client/images/kingexpressbus/cabin/4.jpg" alt="Limousine Cabin"
                                class="w-full h-40 md:h-64 object-cover">
                        </div>
                        <div class="rounded-xl md:rounded-2xl overflow-hidden shadow-2xl gallery-item">
                            <img src="/client/images/kingexpressbus/cabin/5.jpg" alt="VIP Cabin"
                                class="w-full h-40 md:h-64 object-cover">
                        </div>
                        <div class="rounded-xl md:rounded-2xl overflow-hidden shadow-2xl gallery-item">
                            <img src="/client/images/kingexpressbus/sleeper/1.jpg" alt="Sleeper Bus"
                                class="w-full h-28 md:h-40 object-cover">
                        </div>
                        <div class="col-span-2 rounded-xl md:rounded-2xl overflow-hidden shadow-2xl gallery-item">
                            <img src="/client/images/kingexpressbus/sleeper/2.jpg" alt="Interior"
                                class="w-full h-28 md:h-40 object-cover">
                        </div>
                    </div>
                </div>

                {{-- Text Content --}}
                <div class="order-1 lg:order-2 space-y-4 md:space-y-6" data-aos="fade-up" data-aos-duration="800"
                    data-aos-delay="100">
                    <span
                        class="inline-block py-1.5 px-4 rounded-full bg-yellow-400/20 text-yellow-300 font-semibold text-xs md:text-sm border border-yellow-400/30">
                        <i class="fa-solid fa-bus mr-2"></i>{{ __('client.about.fleet.badge') }}
                    </span>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold leading-tight">
                        {{ __('client.about.fleet.title') }}
                    </h2>
                    <p class="text-base md:text-lg text-gray-300 leading-relaxed">
                        {{ __('client.about.fleet.description') }}
                    </p>
                    <p class="text-base md:text-lg text-yellow-300 font-medium italic">
                        "{{ __('client.about.fleet.highlight') }}"
                    </p>

                    {{-- Features Grid --}}
                    <div class="grid grid-cols-2 gap-3 md:gap-4 pt-2 md:pt-4">
                        @foreach(__('client.about.fleet.features') as $feature)
                            <div
                                class="flex items-center gap-2 md:gap-3 p-2 md:p-3 rounded-lg md:rounded-xl bg-white/5 backdrop-blur-sm">
                                <div
                                    class="w-6 h-6 md:w-8 md:h-8 rounded-full bg-yellow-400 flex items-center justify-center text-gray-900">
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
    <section class="py-12 md:py-16 lg:py-24 bg-gray-50 overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center mb-8 md:mb-12" data-aos="fade-up">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900 mb-3 md:mb-4">
                    {{ __('client.about.vision.title') }} & {{ __('client.about.mission.title') }}
                </h2>
            </div>

            <div class="grid lg:grid-cols-2 gap-6 md:gap-8 max-w-5xl mx-auto">
                {{-- Vision Card --}}
                <div class="bg-white rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-lg border border-gray-100"
                    data-aos="fade-up" data-aos-delay="0">
                    <div class="flex items-center gap-3 md:gap-4 mb-4 md:mb-6">
                        <div
                            class="w-12 h-12 md:w-16 md:h-16 rounded-xl md:rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white shadow-lg">
                            <i class="fa-solid fa-eye text-xl md:text-2xl"></i>
                        </div>
                        <div>
                            <span
                                class="text-[10px] md:text-xs font-semibold text-yellow-600 uppercase tracking-wider">{{ __('client.about.vision.badge') }}</span>
                            <h3 class="text-xl md:text-2xl font-bold text-gray-900">
                                {{ __('client.about.vision.title') }}</h3>
                        </div>
                    </div>
                    <p class="text-gray-600 leading-relaxed text-sm md:text-base">
                        {{ __('client.about.vision.description') }}
                    </p>
                </div>

                {{-- Mission Card --}}
                <div class="bg-white rounded-2xl md:rounded-3xl p-6 md:p-8 shadow-lg border border-gray-100"
                    data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center gap-3 md:gap-4 mb-4 md:mb-6">
                        <div
                            class="w-12 h-12 md:w-16 md:h-16 rounded-xl md:rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg">
                            <i class="fa-solid fa-bullseye text-xl md:text-2xl"></i>
                        </div>
                        <h3 class="text-xl md:text-2xl font-bold text-gray-900">{{ __('client.about.mission.title') }}
                        </h3>
                    </div>

                    <div class="space-y-3 md:space-y-4">
                        {{-- With Customers --}}
                        <div
                            class="mission-card p-3 md:p-4 rounded-lg md:rounded-xl bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-yellow-400">
                            <h4 class="font-bold text-gray-900 mb-1 text-sm md:text-base">
                                <i
                                    class="fa-solid fa-heart text-yellow-500 mr-2"></i>{{ __('client.about.mission.with_customers.title') }}
                            </h4>
                            <p class="text-gray-600 text-xs md:text-sm">
                                {{ __('client.about.mission.with_customers.content') }}</p>
                        </div>

                        {{-- With Society --}}
                        <div
                            class="mission-card p-3 md:p-4 rounded-lg md:rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400">
                            <h4 class="font-bold text-gray-900 mb-1 text-sm md:text-base">
                                <i
                                    class="fa-solid fa-globe text-blue-500 mr-2"></i>{{ __('client.about.mission.with_society.title') }}
                            </h4>
                            <p class="text-gray-600 text-xs md:text-sm">
                                {{ __('client.about.mission.with_society.content') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Core Values Section --}}
    <section class="py-12 md:py-16 lg:py-24 bg-white overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center mb-8 md:mb-12" data-aos="fade-up">
                <span
                    class="inline-block py-1.5 px-4 rounded-full bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-700 font-semibold text-xs md:text-sm border border-blue-200 mb-3 md:mb-4">
                    <i class="fa-solid fa-gem mr-2"></i>{{ __('client.about.core_values.badge') }}
                </span>
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900 mb-3 md:mb-4">
                    {{ __('client.about.core_values.title') }}
                </h2>
                <p class="text-sm md:text-base lg:text-lg text-gray-600 max-w-2xl mx-auto">
                    {{ __('client.about.core_values.description') }}
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 max-w-6xl mx-auto">
                @foreach(__('client.about.core_values.items') as $index => $item)
                    @php
                        $colors = ['yellow', 'blue', 'red', 'purple'];
                        $color = $colors[$index % 4];
                    @endphp
                    <div class="value-card p-4 md:p-6 rounded-xl md:rounded-2xl shadow-sm border border-gray-100 text-center"
                        data-aos="fade-up" data-aos-delay="{{ min($index * 75, 225) }}">
                        <div
                            class="w-12 h-12 md:w-16 md:h-16 rounded-xl md:rounded-2xl bg-{{ $color }}-100 text-{{ $color }}-600 flex items-center justify-center mx-auto mb-3 md:mb-4">
                            <i class="{{ $item['icon'] }} text-xl md:text-2xl"></i>
                        </div>
                        <h3 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-1 md:mb-2">
                            {{ $item['title'] }}</h3>
                        <p class="text-gray-600 text-xs md:text-sm">{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Why Choose Us Section --}}
    <section
        class="py-12 md:py-16 lg:py-24 bg-gradient-to-br from-gray-50 via-white to-yellow-50 relative overflow-hidden">
        <div
            class="absolute top-20 right-0 w-40 md:w-64 h-40 md:h-64 bg-yellow-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 float-animation">
        </div>

        <div class="container mx-auto px-4">
            <div class="grid lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
                {{-- Text Content --}}
                <div class="space-y-6 md:space-y-8" data-aos="fade-up" data-aos-duration="800">
                    <div class="space-y-3 md:space-y-4">
                        <span
                            class="inline-block py-1.5 px-4 rounded-full bg-gradient-to-r from-yellow-100 to-yellow-50 text-yellow-700 font-semibold text-xs md:text-sm border border-yellow-200">
                            <i class="fa-solid fa-star mr-2"></i>{{ __('client.about.subtitle') }}
                        </span>
                        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">
                            {{ __('client.about.why_choose.title') }}
                        </h2>
                        <p class="text-base md:text-lg text-gray-600 leading-relaxed">
                            {{ __('client.about.why_choose.subtitle') }}
                        </p>
                    </div>

                    <ul class="space-y-4 md:space-y-6">
                        @foreach(__('client.about.why_choose.items') as $index => $item)
                            @php
                                $gradients = [
                                    'from-yellow-400 to-yellow-500',
                                    'from-blue-400 to-blue-500',
                                    'from-green-400 to-green-500'
                                ];
                                $hovers = ['yellow', 'blue', 'green'];
                            @endphp
                            <li class="feature-card flex items-start gap-3 md:gap-4 p-3 md:p-4 rounded-lg md:rounded-xl hover:bg-{{ $hovers[$index % 3] }}-50/50 transition-colors"
                                data-aos="fade-up" data-aos-delay="{{ min(($index + 1) * 75, 225) }}">
                                <div
                                    class="feature-icon w-10 h-10 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-gradient-to-br {{ $gradients[$index % 3] }} flex items-center justify-center text-white shrink-0 shadow-lg">
                                    <i class="{{ $item['icon'] }} text-lg md:text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-base md:text-lg lg:text-xl font-bold text-gray-900 mb-1 md:mb-2">
                                        {{ $item['title'] }}</h4>
                                    <p class="text-gray-600 leading-relaxed text-sm md:text-base">{{ $item['description'] }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Image --}}
                <div class="relative" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                    <div class="relative rounded-2xl md:rounded-3xl overflow-hidden shadow-2xl group">
                        <img src="/client/images/kingexpressbus/cabin/1.jpg" alt="{{ __('client.about.image_alt') }}"
                            class="w-full object-cover aspect-[4/3] group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent">
                        </div>
                    </div>

                    {{-- Floating Badge --}}
                    <div
                        class="absolute -bottom-4 -left-4 md:-bottom-6 md:-left-6 bg-white p-3 md:p-4 rounded-xl md:rounded-2xl shadow-xl hidden md:block float-animation z-10">
                        <div class="flex items-center gap-2 md:gap-3">
                            <div
                                class="bg-gradient-to-br from-yellow-400 to-yellow-500 p-2 md:p-3 rounded-full text-white">
                                <i class="fa-solid fa-thumbs-up text-base md:text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs md:text-sm text-gray-500 font-semibold">
                                    {{ __('client.about.badge_subtitle') }}
                                </p>
                                <p class="text-sm md:text-lg font-bold text-gray-900">98% Review 5★</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Image Gallery Section --}}
    <section class="py-12 md:py-16 lg:py-24 bg-slate-900 text-white overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="text-center mb-8 md:mb-12" data-aos="fade-up">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold mb-3 md:mb-4">
                    {{ __('client.about.gallery.title') }}</h2>
                <p class="text-gray-400 text-sm md:text-base">{{ __('client.about.gallery.subtitle') }}</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 md:gap-4 max-w-6xl mx-auto">
                <div class="col-span-2 row-span-2 rounded-xl md:rounded-2xl overflow-hidden gallery-item"
                    data-aos="fade-up" data-aos-delay="0">
                    <img src="/client/images/city_imgs/hue.jpg" alt="Huế" class="w-full h-full object-cover">
                </div>
                <div class="rounded-xl md:rounded-2xl overflow-hidden gallery-item" data-aos="fade-up"
                    data-aos-delay="50">
                    <img src="/client/images/city_imgs/hoi-an.jpg" alt="Hội An" class="w-full h-full object-cover">
                </div>
                <div class="rounded-xl md:rounded-2xl overflow-hidden gallery-item" data-aos="fade-up"
                    data-aos-delay="100">
                    <img src="/client/images/city_imgs/da-nang.jpg" alt="Đà Nẵng" class="w-full h-full object-cover">
                </div>
                <div class="rounded-xl md:rounded-2xl overflow-hidden gallery-item" data-aos="fade-up"
                    data-aos-delay="150">
                    <img src="/client/images/kingexpressbus/sleeper/3.jpg" alt="Sleeper Bus"
                        class="w-full h-full object-cover">
                </div>
                <div class="rounded-xl md:rounded-2xl overflow-hidden gallery-item" data-aos="fade-up"
                    data-aos-delay="200">
                    <img src="/client/images/city_imgs/ha-noi.jpg" alt="Hà Nội" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section
        class="py-12 md:py-16 lg:py-20 bg-gradient-to-r from-yellow-400 via-yellow-500 to-orange-500 overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 md:gap-8 text-center md:text-left"
                data-aos="fade-up">
                <div class="space-y-2 md:space-y-3 max-w-2xl">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900">
                        {{ __('client.about.cta.title') }}
                    </h2>
                    <p class="text-gray-800/80 text-base md:text-lg">
                        {{ __('client.about.cta.subtitle') }}
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 md:gap-4 w-full md:w-auto">
                    <a href="{{ route('client.home') }}"
                        class="inline-flex items-center justify-center gap-2 md:gap-3 px-6 md:px-8 py-3 md:py-4 bg-gray-900 text-white rounded-full font-bold text-base md:text-lg hover:bg-gray-800 transform hover:scale-105 transition-all shadow-xl">
                        <i class="fa-solid fa-ticket"></i>
                        {{ __('client.about.cta.button') }}
                    </a>
                    <a href="{{ route('client.routes.index') }}"
                        class="inline-flex items-center justify-center gap-2 md:gap-3 px-6 md:px-8 py-3 md:py-4 bg-white text-gray-900 rounded-full font-bold text-base md:text-lg hover:bg-gray-100 transform hover:scale-105 transition-all shadow-xl">
                        <i class="fa-solid fa-route"></i>
                        {{ __('client.about.cta.routes_button') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
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

                // Hero Typewriter Component
                Alpine.data('aboutHeroTypewriter', () => ({
                    text: '',
                    textArray: [
                        '{{ __("client.about.typing.1") }}',
                        '{{ __("client.about.typing.2") }}',
                        '{{ __("client.about.typing.3") }}',
                        '{{ __("client.about.typing.4") }}'
                    ],
                    textIndex: 0,
                    charIndex: 0,
                    typeSpeed: 60,
                    eraseSpeed: 30,
                    newTextDelay: 2500,
                    init() {
                        setTimeout(() => this.type(), 800);
                    },
                    type() {
                        if (this.charIndex < this.textArray[this.textIndex].length) {
                            this.text += this.textArray[this.textIndex].charAt(this.charIndex);
                            this.charIndex++;
                            setTimeout(() => this.type(), this.typeSpeed);
                        } else {
                            setTimeout(() => this.erase(), this.newTextDelay);
                        }
                    },
                    erase() {
                        if (this.charIndex > 0) {
                            this.text = this.textArray[this.textIndex].substring(0, this.charIndex - 1);
                            this.charIndex--;
                            setTimeout(() => this.erase(), this.eraseSpeed);
                        } else {
                            this.textIndex++;
                            if (this.textIndex >= this.textArray.length) this.textIndex = 0;
                            setTimeout(() => this.type(), this.typeSpeed + 500);
                        }
                    }
                }));
            });

            document.addEventListener('DOMContentLoaded', function () {
                AOS.init({
                    once: true,
                    offset: 50,
                    duration: 600,
                    easing: 'ease-out-cubic',
                    disable: window.innerWidth < 768 ? 'mobile' : false
                });
            });
        </script>
    @endpush

</x-client.layout>
