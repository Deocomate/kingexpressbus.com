{{-- ===== resources\views\client\routes\show.blade.php ===== --}}
<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.route_show.meta_title')" :description="$description ?? ''">
    @php
        $heroImage = $route->banner_url ?? ($route->thumbnail_url ?? '/userfiles/files/city_imgs/ha-noi.jpg');
        $minPrice = (int) ($route->min_price ?? 0);
        $priceDisplay =
            $minPrice > 0
                ? __('client.route_show.price_from', ['price' => number_format($minPrice) . 'đ'])
                : __('client.route_show.price_contact');
        $routeHighlights = [
            [
                'icon' => 'fa-solid fa-location-dot',
                'color' => 'from-blue-400 to-blue-600',
                'label' => __('client.route_show.hero_origin'),
                'value' => $route->start_province_name,
            ],
            [
                'icon' => 'fa-solid fa-map-marker-alt',
                'color' => 'from-emerald-400 to-emerald-600',
                'label' => __('client.route_show.hero_destination'),
                'value' => $route->end_province_name,
            ],
            [
                'icon' => 'fa-solid fa-bus',
                'color' => 'from-purple-400 to-purple-600',
                'label' => __('client.route_show.hero_trips', ['default' => 'Chuyến xe']),
                'value' => __('client.route_show.hero_trip_count', [
                    'count' => $trips->count(),
                    'default' => $trips->count() . ' chuyến',
                ]),
            ],
            [
                'icon' => 'fa-solid fa-tag',
                'color' => 'from-yellow-400 to-amber-500',
                'label' => __('client.route_show.hero_price_label'),
                'value' => $priceDisplay,
            ],
        ];

        $filterKeys = [
            'sort',
            'price_min',
            'price_max',
            'services',
            'pickup_points',
            'dropoff_points',
            'bus_categories',
            'time_ranges',
        ];
        $filterDefaults = [
            'sort' => 'recommended',
            'price_min' => null,
            'price_max' => null,
            'services' => [],
            'pickup_points' => [],
            'dropoff_points' => [],
            'bus_categories' => [],
            'time_ranges' => [],
        ];

        $filterState = array_merge($filterDefaults, $filterState ?? []);
        $filters = $filters ?? [];
        $tripStats = $tripStats ?? ['total' => $trips->count(), 'filtered' => $trips->count()];
        $activeFilterCount = $activeFilterCount ?? 0;
        $hasActiveFilters = $hasActiveFilters ?? $activeFilterCount > 0;

        $persistedQuery = collect(request()->query())->except($filterKeys);
        if (!$persistedQuery->has('departure_date')) {
            $persistedQuery = $persistedQuery->put('departure_date', $departureDate);
        }
        $clearFiltersUrl = route(
            'client.routes.show',
            array_merge(['slug' => $route->slug], $persistedQuery->toArray()),
        );

        $availableServices = collect($filters['services'] ?? [])
            ->filter()
            ->values();
        $pickupOptions = collect($filters['pickup_points'] ?? [])
            ->filter()
            ->values();
        $dropoffOptions = collect($filters['dropoff_points'] ?? [])
            ->filter()
            ->values();
        $busCategoryOptions = collect($filters['bus_categories'] ?? [])
            ->filter()
            ->values();
        $timeRangeOptions = collect($filters['time_ranges'] ?? []);
        $priceRange = $filters['price'] ?? ['min' => null, 'max' => null];
        $sortOptions = [
            'recommended' => __('client.route_show.filters.sort_recommended'),
            'earliest' => __('client.route_show.filters.sort_earliest'),
            'latest' => __('client.route_show.filters.sort_latest'),
            'price_low' => __('client.route_show.filters.sort_price_low'),
            'price_high' => __('client.route_show.filters.sort_price_high'),
            'seats_available' => __('client.route_show.filters.sort_seats'),
        ];
        $galleryFallback = '/userfiles/files/king/sleeper/5.jpg';
    @endphp

    @push('styles')
        <style>
            /* Hero Section */
            .route-hero {
                background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 100%),
                    url('{{ $heroImage }}');
                background-size: cover;
                background-position: center;
                min-height: 420px;
            }

            /* Highlight Cards */
            .highlight-card {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 16px;
                padding: 20px;
                transition: all 0.3s ease;
            }

            .highlight-card:hover {
                background: rgba(255, 255, 255, 0.15);
                transform: translateY(-2px);
            }

            /* Filter Sidebar */
            .filters-sidebar {
                background: #ffffff;
                border-radius: 20px;
                border: 1px solid #e5e7eb;
                box-shadow: 0 10px 40px -15px rgba(0, 0, 0, 0.1);
                position: sticky;
                top: 100px;
            }

            .filter-section {
                padding: 20px;
                border-bottom: 1px solid #f1f5f9;
            }

            .filter-section:last-child {
                border-bottom: none;
            }

            .filter-title {
                font-size: 13px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #64748b;
                margin-bottom: 14px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .filter-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 14px;
                border-radius: 9999px;
                border: 1px solid #e2e8f0;
                background: #f8fafc;
                color: #475569;
                font-size: 13px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .filter-pill:hover {
                border-color: #3b82f6;
                background: #eff6ff;
            }

            .filter-pill.active,
            .filter-pill:has(input:checked) {
                background: #3b82f6;
                border-color: #3b82f6;
                color: #ffffff;
            }

            .filter-pill input {
                position: absolute;
                opacity: 0;
                pointer-events: none;
            }

            /* Trip Cards */
            .trip-card {
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 20px;
                overflow: hidden;
                transition: all 0.3s ease;
            }

            .trip-card:hover {
                border-color: #fbbf24;
                box-shadow: 0 20px 50px -15px rgba(0, 0, 0, 0.12);
                transform: translateY(-4px);
            }

            .trip-image-wrapper {
                position: relative;
                height: 180px;
                overflow: hidden;
            }

            .trip-image-wrapper img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }

            .trip-card:hover .trip-image-wrapper img {
                transform: scale(1.05);
            }

            .trip-body {
                padding: 20px;
            }

            /* Time Display */
            .time-display {
                display: flex;
                align-items: center;
                gap: 16px;
                padding: 16px;
                background: #f8fafc;
                border-radius: 14px;
                border: 1px solid #e2e8f0;
            }

            .time-block {
                text-align: center;
            }

            .time-value {
                font-size: 28px;
                font-weight: 800;
                color: #0f172a;
                line-height: 1;
            }

            .time-label {
                font-size: 12px;
                color: #64748b;
                margin-top: 4px;
            }

            .time-connector {
                flex: 1;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 4px;
            }

            .time-line {
                height: 2px;
                width: 100%;
                background: linear-gradient(90deg, #cbd5e1 0%, #94a3b8 50%, #cbd5e1 100%);
                position: relative;
            }

            .time-line::before {
                content: '';
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                width: 8px;
                height: 8px;
                background: #3b82f6;
                border-radius: 50%;
            }

            .duration-badge {
                font-size: 11px;
                font-weight: 600;
                color: #3b82f6;
                background: #eff6ff;
                padding: 4px 10px;
                border-radius: 9999px;
            }

            /* Price Display */
            .price-tag {
                font-size: 24px;
                font-weight: 800;
                color: #059669;
            }

            .price-tag small {
                font-size: 14px;
                font-weight: 500;
                color: #64748b;
            }

            /* Availability Badge */
            .availability-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                border-radius: 9999px;
                font-size: 12px;
                font-weight: 600;
            }

            .availability-badge.available {
                background: #dcfce7;
                color: #166534;
            }

            .availability-badge.unavailable {
                background: #fee2e2;
                color: #991b1b;
            }

            /* Points Display */
            .points-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .point-card {
                padding: 14px;
                border-radius: 12px;
                border: 1px solid;
            }

            .point-card.pickup {
                background: #eff6ff;
                border-color: #bfdbfe;
            }

            .point-card.dropoff {
                background: #f0fdf4;
                border-color: #bbf7d0;
            }

            .point-title {
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 8px;
                display: flex;
                align -items: center;
                gap: 6px;
            }

            .point-card.pickup .point-title {
                color: #1d4ed8;
            }

            .point-card.dropoff .point-title {
                color: #15803d;
            }

            .point-item {
                font-size: 12px;
                color: #475569;
                padding-left: 12px;
                position: relative;
                margin-bottom: 4px;
            }

            .point-item::before {
                content: '•';
                position: absolute;
                left: 0;
                color: #94a3b8;
            }

            /* Services */
            .service-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                background: #f1f5f9;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 500;
                color: #475569;
            }

            .service-chip i {
                color: #3b82f6;
                font-size: 10px;
            }

            /* Action Buttons */
            .btn-book {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 14px 28px;
                background: linear-gradient(135deg, #fbbf24, #f59e0b);
                color: #0f172a;
                font-weight: 700;
                font-size: 15px;
                border-radius: 14px;
                transition: all 0.3s ease;
                box-shadow: 0 8px 20px -8px rgba(251, 191, 36, 0.5);
            }

            .btn-book:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 25px -8px rgba(251, 191, 36, 0.6);
            }

            .btn-details {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 14px 24px;
                background: #f1f5f9;
                color: #475569;
                font-weight: 600;
                font-size: 15px;
                border-radius: 14px;
                transition: all 0.3s ease;
            }

            .btn-details:hover {
                background: #e2e8f0;
            }

            /* Gallery Thumbnails */
            .gallery-thumbs {
                display: flex;
                gap: 8px;
                overflow-x: auto;
                padding-bottom: 8px;
            }

            .gallery-thumb {
                width: 48px;
                height: 48px;
                border-radius: 10px;
                overflow: hidden;
                border: 2px solid transparent;
                flex-shrink: 0;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .gallery-thumb:hover,
            .gallery-thumb.active {
                border-color: #3b82f6;
            }

            .gallery-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            /* Mobile Filter */
            .mobile-filter-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 100;
            }

            .mobile-filter-panel {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: 320px;
                max-width: 90vw;
                background: #ffffff;
                z-index: 110;
                overflow-y: auto;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .mobile-filter-panel.open {
                transform: translateX(0);
            }

            /* Desktop Filter Panel Reset */
            @media (min-width: 1024px) {
                .lg\:mobile-filter-panel-reset {
                    position: static;
                    width: auto;
                    max-width: none;
                    transform: none;
                    z-index: auto;
                    overflow: visible;
                }
            }

            /* Scrollbar */
            .scrollbar-thin::-webkit-scrollbar {
                height: 4px;
            }

            .scrollbar-thin::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 4px;
            }

            .scrollbar-thin::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            /* Quick Filter Pills */
            .quick-filter-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 8px 16px;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 9999px;
                color: #374151;
                font-size: 13px;
                font-weight: 600;
                white-space: nowrap;
                transition: all 0.2s ease;
                cursor: pointer;
            }

            .quick-filter-pill:hover {
                background: #f3f4f6;
                border-color: #d1d5db;
            }

            .quick-filter-pill.active {
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                border-color: transparent;
                color: #ffffff;
            }

            .quick-filter-pill.active:hover {
                background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            }

            .quick-filter-pill.clear-pill {
                background: #fef2f2;
                border-color: #fecaca;
                color: #dc2626;
            }

            .quick-filter-pill.clear-pill:hover {
                background: #fee2e2;
                border-color: #f87171;
            }

            /* Responsive */
            @media (min-width: 1024px) {
                .trip-card {
                    display: grid;
                    grid-template-columns: 240px 1fr;
                }

                .trip-image-wrapper {
                    height: auto;
                    min-height: 280px;
                }
            }

            @media (max-width: 1023px) {
                .filters-sidebar {
                    position: static;
                }
            }

            @media (max-width: 639px) {
                .points-grid {
                    grid-template-columns: 1fr;
                }

                .time-value {
                    font-size: 24px;
                }
            }

            /* Modal */
            .modal-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.6);
                backdrop-filter: blur(4px);
                z-index: 120;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }

            .modal-content {
                background: #ffffff;
                border-radius: 24px;
                width: 100%;
                max-width: 900px;
                max-height: 90vh;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            .modal-header {
                padding: 24px;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modal-body {
                padding: 24px;
                overflow-y: auto;
                flex: 1;
            }

            .modal-thumb {
                width: 80px;
                height: 80px;
                border-radius: 12px;
                overflow: hidden;
                border: 2px solid transparent;
                cursor: pointer;
                transition: border-color 0.2s ease;
            }

            .modal-thumb.is-active {
                border-color: #3b82f6;
            }
        </style>
    @endpush

    {{-- Hero Section --}}
    <section class="route-hero flex items-end text-white">
        <div class="container mx-auto px-4 py-16 lg:py-20">
            <div class="max-w-4xl space-y-6">
                <span class="inline-flex items-center gap-2 text-sm font-bold uppercase tracking-wider text-yellow-400">
                    <i class="fa-solid fa-map-location-dot"></i>
                    {{ __('client.route_show.hero_brand') }}
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">{{ $route->name }}</h1>
                @if ($route->description)
                    <p class="text-lg text-white/80 max-w-2xl">{{ $route->description }}</p>
                @endif
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-10">
                @foreach ($routeHighlights as $highlight)
                    <div class="highlight-card flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $highlight['color'] }} flex items-center justify-center text-white flex-shrink-0">
                            <i class="{{ $highlight['icon'] }} text-lg"></i>
                        </div>
                        <div>
                            <p class="font-bold text-white">{{ $highlight['value'] }}</p>
                            <p class="text-sm text-white/60">{{ $highlight['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Search Section --}}
    <section id="search-section" class="bg-white py-8 border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="bg-gray-50 rounded-2xl p-4 md:p-6 border border-gray-200">
                <x-client.search-bar :search-data="$searchData" :submit-label="__('client.route_show.search_submit_label')" />
            </div>
        </div>
    </section>

    @if ($trips->isNotEmpty() || $hasActiveFilters)
        {{-- Quick Filter Pills --}}
        <section class="bg-white py-3 border-b border-gray-100 sticky top-0 z-40 shadow-sm">
            <div class="container mx-auto px-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-thin">
                    <span class="text-sm font-semibold text-gray-500 whitespace-nowrap mr-1">
                        {{ __('client.route_show.quick_filters.label', ['default' => 'Lọc nhanh:']) }}
                    </span>

                    {{-- Time Range Quick Filters --}}
                    @php
                        $quickTimeFilters = [
                            'early_morning' => ['label' => 'Sáng sớm (5h-8h)', 'icon' => 'fa-sun'],
                            'morning' => ['label' => 'Buổi sáng (8h-12h)', 'icon' => 'fa-cloud-sun'],
                            'afternoon' => ['label' => 'Buổi chiều (12h-17h)', 'icon' => 'fa-sun'],
                            'evening' => ['label' => 'Buổi tối (17h-21h)', 'icon' => 'fa-moon'],
                        ];
                    @endphp

                    @foreach ($quickTimeFilters as $key => $filter)
                        @php
                            $isActive = in_array($key, $filterState['time_ranges'] ?? []);
                            $newTimeRanges = $isActive
                                ? array_diff($filterState['time_ranges'] ?? [], [$key])
                                : array_merge($filterState['time_ranges'] ?? [], [$key]);
                            $filterUrl = request()->fullUrlWithQuery(['time_ranges' => $newTimeRanges]);
                        @endphp
                        <a href="{{ $filterUrl }}" class="quick-filter-pill {{ $isActive ? 'active' : '' }}"
                            aria-pressed="{{ $isActive ? 'true' : 'false' }}">
                            <i class="fa-solid {{ $filter['icon'] }}"></i>
                            {{ $filter['label'] }}
                        </a>
                    @endforeach

                    {{-- Seats Available Filter --}}
                    @php
                        $hasSeatsActive = $filterState['has_seats'] ?? false;
                        $seatsUrl = request()->fullUrlWithQuery(['has_seats' => $hasSeatsActive ? null : 1]);
                    @endphp
                    <a href="{{ $seatsUrl }}" class="quick-filter-pill {{ $hasSeatsActive ? 'active' : '' }}"
                        aria-pressed="{{ $hasSeatsActive ? 'true' : 'false' }}">
                        <i class="fa-solid fa-chair"></i>
                        {{ __('client.route_show.quick_filters.has_seats', ['default' => 'Còn chỗ']) }}
                    </a>

                    {{-- Clear All Filters --}}
                    @if ($hasActiveFilters)
                        <a href="{{ $clearFiltersUrl }}" class="quick-filter-pill clear-pill">
                            <i class="fa-solid fa-xmark"></i>
                            {{ __('client.route_show.quick_filters.clear_all', ['default' => 'Xóa lọc']) }}
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Results Section --}}
        <section id="availabilities" class="py-12 lg:py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                {{-- Results Header --}}
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-8">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                            {{ __('client.route_show.results_title') }}
                        </h2>
                        <p class="text-gray-500 mt-1">
                            @if ($tripStats['filtered'] < $tripStats['total'])
                                {{ __('client.route_show.results_subtitle_filtered', ['filtered' => $tripStats['filtered'], 'total' => $tripStats['total'], 'date' => $departureDate, 'default' => $tripStats['filtered'] . ' / ' . $tripStats['total'] . ' chuyến • ' . $departureDate]) }}
                            @else
                                {{ __('client.route_show.results_subtitle', ['filtered' => $tripStats['filtered'], 'total' => $tripStats['total'], 'date' => $departureDate]) }}
                            @endif
                        </p>
                    </div>
                    <button id="mobile-filter-toggle"
                        class="lg:hidden inline-flex items-center gap-2 px-5 py-3 bg-white border border-gray-200 rounded-xl font-semibold text-gray-700 shadow-sm">
                        <i class="fa-solid fa-sliders"></i>
                        <span>{{ __('client.route_show.filters.mobile_button') }}</span>
                        @if ($hasActiveFilters)
                            <span
                                class="inline-flex items-center justify-center w-5 h-5 bg-blue-600 text-white text-xs font-bold rounded-full">{{ $activeFilterCount }}</span>
                        @endif
                    </button>
                </div>

                {{-- Mobile Filter Backdrop --}}
                <div id="mobile-filter-backdrop" class="mobile-filter-backdrop hidden lg:hidden"></div>

                {{-- Mobile Filter Slide-in Panel --}}
                <div id="filter-panel-mobile" class="mobile-filter-panel lg:hidden">
                    {{-- Mobile Header --}}
                    <div class="flex justify-between items-center p-5 border-b border-gray-100">
                        <h3 class="text-lg font-bold">{{ __('client.route_show.filters.mobile_title') }}</h3>
                        <button id="mobile-filter-close"
                            class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
                    </div>
                    <form id="filter-form-mobile" action="{{ $clearFiltersUrl }}" method="GET"
                        class="overflow-y-auto max-h-[calc(100vh-70px)]">
                        @include('client.routes.partials.filter-form', [
                            'filterState' => $filterState,
                            'sortOptions' => $sortOptions,
                            'priceRange' => $priceRange,
                            'timeRangeOptions' => $timeRangeOptions,
                            'availableServices' => $availableServices,
                            'busCategoryOptions' => $busCategoryOptions,
                            'clearFiltersUrl' => $clearFiltersUrl,
                        ])
                    </form>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    {{-- Filters Sidebar (Desktop only) --}}
                    <aside class="hidden lg:block lg:col-span-3">
                        <div id="filter-panel-desktop" class="filters-sidebar">
                            <form id="filter-form" action="{{ $clearFiltersUrl }}" method="GET">
                                @include('client.routes.partials.filter-form', [
                                    'filterState' => $filterState,
                                    'sortOptions' => $sortOptions,
                                    'priceRange' => $priceRange,
                                    'timeRangeOptions' => $timeRangeOptions,
                                    'availableServices' => $availableServices,
                                    'busCategoryOptions' => $busCategoryOptions,
                                    'clearFiltersUrl' => $clearFiltersUrl,
                                ])
                            </form>
                        </div>
                    </aside>

                    {{-- Trip Cards --}}
                    <div class="lg:col-span-9 space-y-6">
                        @foreach ($trips as $trip)
                            @php
                                $tripStart = \Carbon\Carbon::createFromFormat('H:i:s', $trip->start_time);
                                $tripEnd = \Carbon\Carbon::createFromFormat('H:i:s', $trip->end_time);
                                $pickupPoints = collect($trip->pickup_points ?? []);
                                $dropoffPoints = collect($trip->dropoff_points ?? []);
                                $firstPickup = $pickupPoints->first();
                                $firstDropoff = $dropoffPoints->first();
                                $imageGallery = collect($trip->image_gallery ?? ($trip->bus_images ?? []))
                                    ->filter()
                                    ->values();
                                if ($imageGallery->isEmpty() && $trip->bus_thumbnail) {
                                    $imageGallery = collect([$trip->bus_thumbnail]);
                                }
                                $primaryImage =
                                    $trip->primary_bus_image ?? ($imageGallery->first() ?: $galleryFallback);
                                $durationMinutes = $trip->duration_minutes ?? 0;
                                $durationLabel =
                                    $durationMinutes > 0
                                        ? __('client.route_show.trip_card.duration_format', [
                                            'hours' => intdiv($durationMinutes, 60),
                                            'minutes' => $durationMinutes % 60,
                                        ])
                                        : __('client.route_show.trip_card.duration_format', [
                                            'hours' => (int) $tripStart->diff($tripEnd)->format('%h'),
                                            'minutes' => (int) $tripStart->diff($tripEnd)->format('%i'),
                                        ]);
                                $serviceList = collect($trip->services ?? [])
                                    ->filter()
                                    ->values();
                                $hasSeats = ($trip->seats_available ?? 0) > 0;
                            @endphp

                            <article class="trip-card">
                                {{-- Image --}}
                                <div class="trip-image-wrapper">
                                    <img id="trip-image-{{ $trip->trip_id }}" src="{{ $primaryImage }}"
                                        alt="{{ $trip->bus_name }}" loading="lazy">
                                    <div class="absolute top-4 left-4">
                                        <span
                                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/95 backdrop-blur-sm rounded-full text-xs font-bold text-gray-800 shadow">
                                            <i class="fa-solid fa-bus text-yellow-500"></i>
                                            {{ $trip->bus_model ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Content --}}
                                <div class="trip-body flex flex-col">
                                    {{-- Header --}}
                                    <div class="flex items-start justify-between gap-4 mb-5">
                                        <div class="flex items-center gap-4">
                                            <img src="{{ $trip->bus_thumbnail ?: '/userfiles/files/web information/logo.jpg' }}"
                                                alt="{{ $trip->bus_name }}"
                                                class="h-14 w-14 rounded-xl object-cover border border-gray-100 shadow-sm">
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900">{{ $trip->bus_name }}</h3>
                                                <p class="text-sm text-gray-500">{{ $trip->bus_model ?? 'N/A' }}</p>
                                                <p class="text-xs text-gray-400">Mã: {{ $trip->trip_id }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            @if ($trip->has_price)
                                                <p class="price-tag">{{ number_format($trip->price) }}<small>đ</small>
                                                </p>
                                                <div class="mt-2">
                                                    <span
                                                        class="availability-badge {{ $trip->seats_available > 0 ? 'available' : 'unavailable' }}">
                                                        <i class="fa-solid fa-circle text-[6px]"></i>
                                                        {{ $trip->seats_available > 0 ? __('client.route_show.trip_card.seats_available') : __('client.route_show.trip_card.seats_full') }}
                                                    </span>
                                                </div>
                                            @else
                                                <p class="text-lg font-bold text-blue-600">
                                                    {{ __('client.route_show.price_contact') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Time Display --}}
                                    <div class="time-display mb-5">
                                        <div class="time-block">
                                            <p class="time-value">{{ $tripStart->format('H:i') }}</p>
                                            <p class="time-label truncate max-w-[100px]"
                                                title="{{ $firstPickup->name ?? __('client.route_show.trip_card.pickup_point') }}">
                                                {{ $firstPickup->name ?? __('client.route_show.trip_card.pickup_point') }}
                                            </p>
                                        </div>
                                        <div class="time-connector">
                                            <span class="duration-badge">{{ $durationLabel }}</span>
                                            <div class="time-line"></div>
                                        </div>
                                        <div class="time-block">
                                            <p class="time-value">{{ $tripEnd->format('H:i') }}</p>
                                            <p class="time-label truncate max-w-[100px]"
                                                title="{{ $firstDropoff->name ?? __('client.route_show.trip_card.dropoff_point') }}">
                                                {{ $firstDropoff->name ?? __('client.route_show.trip_card.dropoff_point') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Pickup & Dropoff Points --}}
                                    <div class="points-grid mb-5">
                                        <div class="point-card pickup">
                                            <h4 class="point-title">
                                                <i class="fa-solid fa-location-dot"></i>
                                                Điểm đón
                                            </h4>
                                            @forelse ($pickupPoints->take(2) as $pickup)
                                                <p class="point-item">{{ $pickup->name }}</p>
                                            @empty
                                                <p class="text-xs text-gray-400">Chưa cập nhật</p>
                                            @endforelse
                                            @if ($pickupPoints->count() > 2)
                                                <p class="text-xs text-blue-600 font-medium mt-1">
                                                    +{{ $pickupPoints->count() - 2 }}
                                                    điểm khác</p>
                                            @endif
                                        </div>
                                        <div class="point-card dropoff">
                                            <h4 class="point-title">
                                                <i class="fa-solid fa-flag-checkered"></i>
                                                Điểm trả
                                            </h4>
                                            @forelse ($dropoffPoints->take(2) as $dropoff)
                                                <p class="point-item">{{ $dropoff->name }}</p>
                                            @empty
                                                <p class="text-xs text-gray-400">Chưa cập nhật</p>
                                            @endforelse
                                            @if ($dropoffPoints->count() > 2)
                                                <p class="text-xs text-emerald-600 font-medium mt-1">
                                                    +{{ $dropoffPoints->count() - 2 }} điểm khác</p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Services --}}
                                    <div class="flex flex-wrap gap-2 mb-5">
                                        @forelse ($serviceList->take(4) as $service)
                                            <span class="service-chip">
                                                <i class="fa-solid fa-check-circle"></i>
                                                {{ $service }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400">Chưa có tiện ích</span>
                                        @endforelse
                                        @if ($serviceList->count() > 4)
                                            <span class="service-chip">+{{ $serviceList->count() - 4 }}</span>
                                        @endif
                                    </div>

                                    {{-- Gallery Thumbnails --}}
                                    @if ($imageGallery->count() > 1)
                                        <div class="gallery-thumbs scrollbar-thin mb-5">
                                            @foreach ($imageGallery->take(5) as $image)
                                                <button type="button" class="gallery-thumb" data-image-trigger
                                                    data-target="#trip-image-{{ $trip->trip_id }}"
                                                    data-image="{{ $image }}">
                                                    <img src="{{ $image }}" alt="Bus image" loading="lazy">
                                                </button>
                                            @endforeach
                                            @if ($imageGallery->count() > 5)
                                                <div
                                                    class="gallery-thumb bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                                                    +{{ $imageGallery->count() - 5 }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Action Buttons --}}
                                    <div class="flex gap-3 mt-auto pt-4 border-t border-gray-100">
                                        <a href="{{ route('client.booking.create', ['trip_id' => $trip->trip_id, 'date' => $departureDate]) }}"
                                            class="btn-book flex-1">
                                            <i class="fa-solid fa-ticket"></i>
                                            Chọn chuyến
                                        </a>
                                        <button type="button" class="btn-details view-trip-details-btn"
                                            data-trip='{{ json_encode($trip) }}'>
                                            <i class="fa-solid fa-circle-info"></i>
                                            Chi tiết
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @else
        {{-- No Results --}}
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4 text-center max-w-lg">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-xmark text-4xl text-gray-400"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-3">{{ __('client.route_show.no_trips.title') }}</h2>
                <p class="text-gray-500 mb-8">{{ __('client.route_show.no_trips.description') }}</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#search-section"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 border-2 border-blue-600 text-blue-600 rounded-xl font-semibold hover:bg-blue-50 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        {{ __('client.route_show.no_trips.research_button') }}
                    </a>
                    @if ($hasActiveFilters ?? false)
                        <a href="{{ $clearFiltersUrl }}"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition">
                            <i class="fa-solid fa-xmark"></i>
                            {{ __('client.route_show.no_trips.clear_filters_button') }}
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Travel Tips --}}
    @if (!empty($travelTips))
        <section id="travel-tips" class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center">
                        <i class="fa-solid fa-lightbulb text-yellow-500 text-xl"></i>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900">{{ __('client.route_show.tips.title') }}
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($travelTips as $tip)
                        <article
                            class="bg-gray-50 border border-gray-100 rounded-2xl p-6 hover:border-yellow-200 hover:bg-yellow-50/30 transition">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">{{ $tip['title'] }}</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $tip['content'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Trip Details Modal --}}
    <div id="trip-details-modal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-xl font-bold text-gray-900">{{ __('client.route_show.details_modal.title') }}</h3>
                <button id="close-modal-btn"
                    class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-bold text-gray-800 mb-4">
                            {{ __('client.route_show.details_modal.bus_info_title') }}</h4>
                        <img id="modal-bus-image" src=""
                            alt="{{ __('client.route_show.details_modal.bus_image_alt') }}"
                            class="w-full h-52 object-cover rounded-xl mb-4 bg-gray-100">
                        <div id="modal-gallery" class="flex gap-3 overflow-x-auto pb-2 scrollbar-thin"></div>
                        <ul class="space-y-3 text-sm mt-5">
                            <li class="flex gap-2"><span
                                    class="font-semibold text-gray-700 w-20">{{ __('client.route_show.details_modal.company') }}</span><span
                                    id="modal-company-name" class="text-gray-600"></span></li>
                            <li class="flex gap-2"><span
                                    class="font-semibold text-gray-700 w-20">{{ __('client.route_show.details_modal.bus_type') }}</span><span
                                    id="modal-bus-category" class="text-gray-600"></span></li>
                            <li class="flex gap-2"><span
                                    class="font-semibold text-gray-700 w-20">{{ __('client.route_show.details_modal.bus_details') }}</span><span
                                    id="modal-bus-name" class="text-gray-600"></span> (<span id="modal-bus-model"
                                    class="text-gray-600"></span>)</li>
                        </ul>
                        <h4 class="font-bold text-gray-800 mt-6 mb-3">
                            {{ __('client.route_show.details_modal.services_title') }}</h4>
                        <div id="modal-services" class="flex flex-wrap gap-2"></div>
                    </div>
                    <div class="space-y-6">
                        <div>
                            <h4 class="font-bold text-gray-800 mb-4">
                                {{ __('client.route_show.details_modal.stops_info_title') }}</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="point-card pickup">
                                    <h5 class="point-title">
                                        {{ __('client.route_show.details_modal.pickup_points_title') }}</h5>
                                    <ul id="modal-pickup-points" class="space-y-1"></ul>
                                </div>
                                <div class="point-card dropoff">
                                    <h5 class="point-title">
                                        {{ __('client.route_show.details_modal.dropoff_points_title') }}</h5>
                                    <ul id="modal-dropoff-points" class="space-y-1"></ul>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray-600">{{ __('client.route_show.details_modal.status') }}
                                <span id="modal-availability" class="font-bold text-emerald-600"></span>
                            </p>
                        </div>
                        <a id="modal-booking-link" href="#" class="btn-book w-full text-center">
                            <i class="fa-solid fa-ticket"></i>
                            {{ __('client.route_show.details_modal.book_now_button') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const body = document.body;

                // Mobile Filter
                const filterPanel = document.getElementById('filter-panel');
                const filterBackdrop = document.getElementById('mobile-filter-backdrop');
                const filterToggle = document.getElementById('mobile-filter-toggle');
                const filterClose = document.getElementById('mobile-filter-close');

                function openFilters() {
                    if (!filterPanel) return;
                    filterPanel.classList.add('open');
                    filterBackdrop?.classList.remove('hidden');
                    body.classList.add('overflow-hidden');
                }

                function closeFilters() {
                    if (!filterPanel) return;
                    filterPanel.classList.remove('open');
                    filterBackdrop?.classList.add('hidden');
                    body.classList.remove('overflow-hidden');
                }

                filterToggle?.addEventListener('click', openFilters);
                filterClose?.addEventListener('click', closeFilters);
                filterBackdrop?.addEventListener('click', closeFilters);

                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 1024) {
                        body.classList.remove('overflow-hidden');
                        filterBackdrop?.classList.add('hidden');
                        filterPanel?.classList.remove('open');
                    }
                });

                // Image Trigger
                document.querySelectorAll('[data-image-trigger]').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const targetSelector = button.getAttribute('data-target');
                        const imageUrl = button.getAttribute('data-image');
                        const target = document.querySelector(targetSelector);
                        if (target && imageUrl) {
                            target.setAttribute('src', imageUrl);
                        }
                        // Update active state
                        button.closest('.gallery-thumbs')?.querySelectorAll('.gallery-thumb').forEach(
                            t => t.classList.remove('active'));
                        button.classList.add('active');
                    });
                });

                // Modal
                const modal = document.getElementById('trip-details-modal');
                const closeModalBtn = document.getElementById('close-modal-btn');
                const modalCompanyName = document.getElementById('modal-company-name');
                const modalBusName = document.getElementById('modal-bus-name');
                const modalBusModel = document.getElementById('modal-bus-model');
                const modalBusCategory = document.getElementById('modal-bus-category');
                const modalServices = document.getElementById('modal-services');
                const modalBusImage = document.getElementById('modal-bus-image');
                const modalGallery = document.getElementById('modal-gallery');
                const modalPickupPoints = document.getElementById('modal-pickup-points');
                const modalDropoffPoints = document.getElementById('modal-dropoff-points');
                const modalAvailability = document.getElementById('modal-availability');
                const modalBookingLink = document.getElementById('modal-booking-link');

                function openModal() {
                    modal?.classList.remove('hidden');
                    body.classList.add('overflow-hidden');
                }

                function closeModal() {
                    modal?.classList.add('hidden');
                    body.classList.remove('overflow-hidden');
                }

                closeModalBtn?.addEventListener('click', closeModal);
                modal?.addEventListener('click', function(event) {
                    if (event.target === modal) closeModal();
                });

                document.querySelectorAll('.view-trip-details-btn').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const rawData = button.getAttribute('data-trip');
                        if (!rawData) return;
                        const tripData = JSON.parse(rawData);
                        if (!tripData) return;

                        modalCompanyName.textContent = "{{ config('app.name') }}";
                        modalBusName.textContent = tripData.bus_model || '';
                        modalBusModel.textContent = tripData.bus_model || '';
                        modalBusCategory.textContent = tripData.bus_category ||
                            "{{ __('client.route_show.details_modal.not_updated') }}";

                        const galleryImages = Array.isArray(tripData.image_gallery) ? tripData
                            .image_gallery : [];
                        let initialImage = tripData.primary_bus_image || galleryImages[0] || tripData
                            .bus_thumbnail || '{{ $galleryFallback }}';
                        modalBusImage.src = initialImage;

                        modalGallery.innerHTML = '';
                        if (galleryImages.length > 0) {
                            let activeThumb = null;
                            galleryImages.forEach(function(src) {
                                const thumbBtn = document.createElement('button');
                                thumbBtn.type = 'button';
                                thumbBtn.className = 'modal-thumb';
                                thumbBtn.innerHTML = '<img src="' + src +
                                    '" alt="{{ __('client.route_show.details_modal.bus_image_alt') }}">';
                                if (src === initialImage) {
                                    thumbBtn.classList.add('is-active');
                                    activeThumb = thumbBtn;
                                }
                                thumbBtn.addEventListener('click', function() {
                                    modalBusImage.src = src;
                                    if (activeThumb) activeThumb.classList.remove(
                                        'is-active');
                                    thumbBtn.classList.add('is-active');
                                    activeThumb = thumbBtn;
                                });
                                modalGallery.appendChild(thumbBtn);
                            });
                        } else {
                            modalGallery.innerHTML = '<p class="text-sm text-gray-500">' +
                                "{{ __('client.route_show.details_modal.no_gallery') }}" + '</p>';
                        }

                        modalServices.innerHTML = '';
                        if (tripData.services && tripData.services.length > 0) {
                            tripData.services.forEach(function(service) {
                                const chip = document.createElement('span');
                                chip.className = 'service-chip';
                                chip.innerHTML = '<i class="fa-solid fa-check-circle"></i> ' +
                                    service;
                                modalServices.appendChild(chip);
                            });
                        } else {
                            modalServices.innerHTML = '<p class="text-sm text-gray-500">' +
                                "{{ __('client.route_show.details_modal.no_services') }}" + '</p>';
                        }

                        modalPickupPoints.innerHTML = '';
                        if (tripData.pickup_points) {
                            tripData.pickup_points.forEach(function(point) {
                                const item = document.createElement('li');
                                item.className = 'point-item';
                                item.textContent = point.name || '';
                                modalPickupPoints.appendChild(item);
                            });
                        }

                        modalDropoffPoints.innerHTML = '';
                        if (tripData.dropoff_points) {
                            tripData.dropoff_points.forEach(function(point) {
                                const item = document.createElement('li');
                                item.className = 'point-item';
                                item.textContent = point.name || '';
                                modalDropoffPoints.appendChild(item);
                            });
                        }

                        const seatsAvailable = Number(tripData.seats_available ?? 0);
                        modalAvailability.textContent = seatsAvailable > 0 ?
                            "{{ __('client.route_show.trip_card.seats_available') }}" :
                            "{{ __('client.route_show.trip_card.seats_full') }}";

                        const bookingUrl = new URL("{{ route('client.booking.create') }}", window
                            .location.origin);
                        bookingUrl.searchParams.set('trip_id', tripData.trip_id);
                        bookingUrl.searchParams.set('date', '{{ $departureDate }}');
                        modalBookingLink.href = bookingUrl.toString();

                        openModal();
                    });
                });
            });

            // Mobile Filter Panel Toggle
            (function() {
                const filterToggle = document.getElementById('mobile-filter-toggle');
                const filterPanel = document.getElementById('filter-panel-mobile');
                const filterBackdrop = document.getElementById('mobile-filter-backdrop');
                const filterClose = document.getElementById('mobile-filter-close');

                function openFilterPanel() {
                    filterPanel.classList.add('open');
                    filterBackdrop.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }

                function closeFilterPanel() {
                    filterPanel.classList.remove('open');
                    filterBackdrop.classList.add('hidden');
                    document.body.style.overflow = '';
                }

                if (filterToggle) {
                    filterToggle.addEventListener('click', openFilterPanel);
                }

                if (filterClose) {
                    filterClose.addEventListener('click', closeFilterPanel);
                }

                if (filterBackdrop) {
                    filterBackdrop.addEventListener('click', closeFilterPanel);
                }

                // Close on Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && filterPanel.classList.contains('open')) {
                        closeFilterPanel();
                    }
                });
            })();
        </script>
    @endpush

    {{-- Mobile Sticky Booking Bar --}}
    @if ($trips->isNotEmpty())
        @php
            $lowestPrice = $trips->min('price');
        @endphp
        <div id="mobile-booking-bar"
            class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50 transform translate-y-full transition-transform duration-300 lg:hidden">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-gray-500">
                            {{ __('client.route_show.mobile_bar.from', ['default' => 'Chỉ từ']) }}</p>
                        <p class="text-xl font-bold text-blue-600">
                            {{ number_format($lowestPrice, 0, ',', '.') }}₫
                        </p>
                    </div>
                    <a href="#availabilities"
                        class="flex-1 max-w-[160px] inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-yellow-400 to-amber-500 text-slate-900 font-bold rounded-xl shadow-md hover:shadow-lg transition">
                        {{ __('client.route_show.mobile_bar.view_trips', ['default' => 'Xem chuyến']) }}
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                // Mobile Sticky Booking Bar
                document.addEventListener('DOMContentLoaded', function() {
                    const bookingBar = document.getElementById('mobile-booking-bar');
                    const availabilities = document.getElementById('availabilities');

                    if (bookingBar && availabilities) {
                        const observer = new IntersectionObserver(function(entries) {
                            entries.forEach(function(entry) {
                                if (entry.isIntersecting) {
                                    bookingBar.classList.add('translate-y-full');
                                } else {
                                    bookingBar.classList.remove('translate-y-full');
                                }
                            });
                        }, {
                            threshold: 0.3
                        });

                        observer.observe(availabilities);
                    }
                });
            </script>
        @endpush
    @endif

</x-client.layout>
