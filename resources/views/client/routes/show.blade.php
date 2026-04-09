{{-- ===== resources\views\client\routes\show.blade.php ===== --}}
<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.route_show.meta_title')" :description="$description ?? ''">
    @php
        $heroImage = $route->banner_url ?? ($route->thumbnail_url ?? '/client/images/city_imgs/ha-noi.jpg');
        $minPrice = (int) ($route->min_price ?? 0);
        $priceDisplay =
            $minPrice > 0
                ? __('client.route_show.price_from', ['price' => number_format($minPrice) . 'đ'])
                : __('client.route_show.price_contact');

        $filterKeys = [
            'sort',
            'price_min',
            'price_max',
            'services',
            'pickup_points',
            'dropoff_points',
            'bus_categories',
            'time_ranges',
            'has_seats',
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
            'has_seats' => false,
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
        $galleryFallback = '/client/images/kingexpressbus/sleeper/1.jpg';
    @endphp

    @push('styles')
        <style>
            .route-hero {
                background: linear-gradient(110deg, rgba(8, 23, 43, 0.78), rgba(255, 155, 0, 0.56)), url('{{ $heroImage }}');
                background-size: cover;
                background-position: center;
                background-attachment: fixed;
            }

            @media (max-width: 768px) {
                .route-hero {
                    background-attachment: scroll;
                }
            }

            .trip-card-details {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.35s ease;
            }

            .trip-card-details.is-open {
                max-height: 5000px;
                border-top: 1px solid #e5e7eb;
            }

            .is-expanded .chevron-icon {
                transform: rotate(180deg);
            }
        </style>
    @endpush

    {{-- Hero + Search --}}
    <section id="search-section" class="route-hero text-white">
        <div class="container mx-auto max-w-7xl px-4 py-6 lg:py-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <span class="mb-1 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-white/70">
                        <i class="fa-solid fa-map-location-dot"></i>
                        {{ __('client.route_show.hero_brand') }}
                    </span>
                    <h1 class="text-2xl font-semibold leading-tight md:text-3xl">{{ $route->name }}</h1>
                </div>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-white/85">
                    <span class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-bus"></i>
                        {{ $trips->count() }} {{ __('client.route_show.hero_trips', ['default' => 'chuyến']) }}
                    </span>
                    <span class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-tag"></i>
                        {{ $priceDisplay }}
                    </span>
                </div>
            </div>

            <div class="mt-5 rounded-2xl border border-white/30 bg-white/95 p-3 text-slate-800 shadow-soft lg:p-4">
                <x-client.search-bar :search-data="$searchData" :submit-label="__('client.route_show.search_submit_label')" />
            </div>
        </div>
    </section>

    @if ($trips->isNotEmpty() || $hasActiveFilters)
        {{-- Quick Filter Pills --}}
        <section class="border-y border-amber-100 bg-white py-3">
            <div class="container mx-auto max-w-7xl px-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <span class="mr-1 whitespace-nowrap text-sm font-semibold text-gray-500">
                        {{ __('client.route_show.quick_filters.label', ['default' => 'Lọc nhanh:']) }}
                    </span>

                    {{-- Time Range Quick Filters --}}
                    @php
                        $quickTimeFilters = [
                            'early_morning' => ['label' => __('client.route_show.filters.time_range_early_morning'), 'icon' => 'fa-sun'],
                            'morning' => ['label' => __('client.route_show.filters.time_range_morning'), 'icon' => 'fa-cloud-sun'],
                            'afternoon' => ['label' => __('client.route_show.filters.time_range_afternoon'), 'icon' => 'fa-sun'],
                            'evening' => ['label' => __('client.route_show.filters.time_range_evening'), 'icon' => 'fa-moon'],
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
                        <a href="{{ $filterUrl }}"
                            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ $isActive ? 'border-primary-600 bg-primary-600 text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' }}"
                            @if ($isActive) aria-current="true" @endif>
                            <i class="fa-solid {{ $filter['icon'] }}"></i>
                            {{ $filter['label'] }}
                        </a>
                    @endforeach

                    {{-- Seats Available Filter --}}
                    @php
                        $hasSeatsActive = $filterState['has_seats'] ?? false;
                        $seatsUrl = request()->fullUrlWithQuery(['has_seats' => $hasSeatsActive ? null : 1]);
                    @endphp
                    <a href="{{ $seatsUrl }}"
                        class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-3 py-1.5 text-xs font-semibold transition {{ $hasSeatsActive ? 'border-primary-600 bg-primary-600 text-white' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' }}"
                        @if ($hasSeatsActive) aria-current="true" @endif>
                        <i class="fa-solid fa-chair"></i>
                        {{ __('client.route_show.quick_filters.has_seats', ['default' => 'Còn chỗ']) }}
                    </a>

                    {{-- Clear All Filters --}}
                    @if ($hasActiveFilters)
                        <a href="{{ $clearFiltersUrl }}"
                            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100">
                            <i class="fa-solid fa-xmark"></i>
                            {{ __('client.route_show.quick_filters.clear_all', ['default' => 'Xóa lọc']) }}
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Results Section --}}
        <section id="availabilities" class="bg-gray-50 py-6 lg:py-8">
            <div class="container mx-auto max-w-7xl px-4">
                {{-- Results Header --}}
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3 mb-6">
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900">
                            {{ __('client.route_show.results_title') }}
                        </h2>
                        <p class="text-gray-500 mt-0.5 text-sm">
                            @if ($tripStats['filtered'] < $tripStats['total'])
                                {{ __('client.route_show.results_subtitle_filtered', ['filtered' => $tripStats['filtered'], 'total' => $tripStats['total'], 'date' => $departureDate, 'default' => $tripStats['filtered'] . ' / ' . $tripStats['total'] . ' chuyến • ' . $departureDate]) }}
                            @else
                                {{ __('client.route_show.results_subtitle', ['filtered' => $tripStats['filtered'], 'total' => $tripStats['total'], 'date' => $departureDate]) }}
                            @endif
                        </p>
                    </div>
                    <button id="mobile-filter-toggle"
                        class="lg:hidden inline-flex items-center gap-2 px-5 py-3 bg-white border border-gray-200 rounded-xl font-semibold text-gray-700 shadow-sm"
                        aria-controls="filter-panel-mobile" aria-expanded="false">
                        <i class="fa-solid fa-sliders"></i>
                        <span>{{ __('client.route_show.filters.mobile_button') }}</span>
                        @if ($hasActiveFilters)
                            <span
                                class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-primary-600 text-xs font-bold text-white">{{ $activeFilterCount }}</span>
                        @endif
                    </button>
                </div>

                {{-- Mobile Filter Backdrop --}}
                <div id="mobile-filter-backdrop" class="fixed inset-0 z-40 hidden bg-slate-900/50 lg:hidden" style="z-index: 100000;"></div>

                {{-- Mobile Filter Slide-in Panel --}}
                <div id="filter-panel-mobile"
                    class="fixed inset-y-0 left-0 z-50 w-[320px] max-w-[90vw] -translate-x-full overflow-y-auto bg-white transition-transform duration-300 lg:hidden" style="z-index: 100010;"
                    role="dialog" aria-modal="true" aria-hidden="true" aria-label="Bộ lọc chuyến xe" tabindex="-1">
                    {{-- Mobile Header --}}
                    <div class="flex justify-between items-center p-5 border-b border-gray-100">
                        <h3 class="text-lg font-bold">{{ __('client.route_show.filters.mobile_title') }}</h3>
                        <button id="mobile-filter-close"
                                class="text-gray-400 hover:text-gray-600 text-2xl"
                                aria-label="Đóng bộ lọc">&times;</button>
                    </div>
                    <form id="filter-form-mobile" action="{{ $clearFiltersUrl }}" method="GET"
                        class="h-[calc(100vh-72px)] overflow-y-auto">
                        @include('client.routes.partials.filter-form', [
                            'filterState' => $filterState,
                            'sortOptions' => $sortOptions,
                            'priceRange' => $priceRange,
                            'timeRangeOptions' => $timeRangeOptions,
                            'availableServices' => $availableServices,
                            'busCategoryOptions' => $busCategoryOptions,
                            'filterIdPrefix' => 'mobile-',
                            'clearFiltersUrl' => $clearFiltersUrl,
                        ])
                    </form>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {{-- Filters Sidebar (Desktop only) --}}
                    <aside class="hidden lg:col-span-3 lg:block">
                        <div id="filter-panel-desktop" class="h-full overflow-hidden rounded-2xl border border-amber-100 bg-white shadow-soft">
                            <div class="flex items-center justify-between border-b border-slate-100 bg-amber-50/60 px-5 py-4">
                                <h3 class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                    <i class="fa-solid fa-sliders text-primary-600"></i>
                                    {{ __('client.route_show.filters.sidebar_title', ['default' => 'Bộ lọc']) }}
                                </h3>
                                @if ($hasActiveFilters)
                                    <span class="inline-flex items-center justify-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-bold text-primary-700">
                                        {{ $activeFilterCount }} {{ __('client.route_show.filters.active', ['default' => 'đang lọc']) }}
                                    </span>
                                @endif
                            </div>
                            <form id="filter-form" action="{{ $clearFiltersUrl }}" method="GET" class="flex h-full flex-col">
                                @include('client.routes.partials.filter-form', [
                                    'filterState' => $filterState,
                                    'sortOptions' => $sortOptions,
                                    'priceRange' => $priceRange,
                                    'timeRangeOptions' => $timeRangeOptions,
                                    'availableServices' => $availableServices,
                                    'busCategoryOptions' => $busCategoryOptions,
                                    'filterIdPrefix' => 'desktop-',
                                    'clearFiltersUrl' => $clearFiltersUrl,
                                ])
                            </form>
                        </div>
                    </aside>

                    {{-- Trip Cards - Compact Horizontal Layout --}}
                    <div class="lg:col-span-9 space-y-4">
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

                            <article class="trip-card overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:border-slate-300 hover:shadow-md"
                                id="trip-card-{{ $trip->trip_id }}">
                                <div class="trip-card-inner flex flex-col gap-3 p-3 sm:flex-row sm:gap-4">
                                    <div class="trip-image-wrapper relative aspect-video w-full overflow-hidden rounded-lg sm:aspect-square sm:w-32 sm:shrink-0 sm:self-center">
                                        <img id="trip-image-{{ $trip->trip_id }}" src="{{ $primaryImage }}"
                                            alt="{{ $trip->bus_name }}" loading="lazy"
                                            class="h-full w-full object-cover">
                                        @if ($hasSeats && ($trip->seats_available ?? 0) <= 5 && ($trip->seats_available ?? 0) > 0)
                                            <div class="absolute right-1.5 top-1.5">
                                                <span class="inline-flex items-center gap-1 rounded bg-red-500/90 px-1.5 py-0.5 text-[10px] font-bold text-white">
                                                    <i class="fa-solid fa-fire"></i>
                                                    {{ __('client.route_show.trip_card.seats_left', ['count' => $trip->seats_available, 'default' => 'Còn ' . $trip->seats_available . ' chỗ']) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="trip-body flex min-w-0 flex-1 flex-col gap-2.5">
                                        <div class="trip-card-header flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="min-w-0">
                                                <h3 class="truncate text-sm font-bold leading-tight text-gray-900">{{ $trip->bus_name }}</h3>
                                                <p class="mt-0.5 text-xs text-gray-400">{{ $trip->bus_model ?? __('client.booking.service.not_available') }}</p>
                                            </div>
                                            <div class="flex shrink-0 flex-col items-start gap-1 sm:items-end sm:text-right">
                                                @if ($trip->has_price)
                                                    <p class="text-xl font-extrabold leading-none text-amber-600">
                                                        {{ number_format($trip->effective_price ?? $trip->price) }}<span class="text-xs font-medium text-slate-500">đ</span>
                                                    </p>
                                                    @if (!empty($trip->has_surcharge))
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                                                            <i class="fa-solid fa-bolt"></i>
                                                            {{ __('client.route_show.trip_card.holiday_surcharge_badge') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <p class="text-sm font-bold text-primary-600">{{ __('client.route_show.price_contact') }}</p>
                                                @endif
                                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $hasSeats ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                    <i class="fa-solid fa-circle text-[5px]"></i>
                                                    @if ($hasSeats)
                                                        {{ __('client.route_show.trip_card.seats_available') }} ({{ $trip->seats_available }})
                                                    @else
                                                        {{ __('client.route_show.trip_card.seats_full') }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        <div class="rounded-lg border border-slate-100 bg-slate-50 p-2.5">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                                <div class="min-w-0">
                                                    <p class="text-base font-extrabold leading-none text-slate-900">{{ $tripStart->format('H:i') }}</p>
                                                    <p class="mt-0.5 truncate text-xs text-slate-500" title="{{ $firstPickup->name ?? __('client.route_show.trip_card.pickup_point') }}">
                                                        {{ $firstPickup->name ?? __('client.route_show.trip_card.pickup_point') }}
                                                    </p>
                                                </div>
                                                <div class="flex items-center gap-2 sm:flex-1 sm:px-2">
                                                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                                    <span class="h-px flex-1 border-t border-dashed border-slate-300"></span>
                                                    <span class="whitespace-nowrap text-[11px] font-semibold text-slate-500">{{ $durationLabel }}</span>
                                                    <span class="h-px flex-1 border-t border-dashed border-slate-300"></span>
                                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                </div>
                                                <div class="min-w-0 sm:text-right">
                                                    <p class="text-base font-extrabold leading-none text-slate-900">{{ $tripEnd->format('H:i') }}</p>
                                                    <p class="mt-0.5 truncate text-xs text-slate-500" title="{{ $firstDropoff->name ?? __('client.route_show.trip_card.dropoff_point') }}">
                                                        {{ $firstDropoff->name ?? __('client.route_show.trip_card.dropoff_point') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-auto flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="flex min-w-0 flex-wrap items-center gap-1.5">
                                                @foreach ($serviceList->take(3) as $service)
                                                    <span class="hidden items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600 sm:inline-flex">
                                                        <i class="fa-solid fa-check-circle text-[9px] text-primary-600"></i>
                                                        {{ $service }}
                                                    </span>
                                                @endforeach
                                                @if ($serviceList->count() > 3)
                                                    <span class="hidden rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600 sm:inline-flex">+{{ $serviceList->count() - 3 }}</span>
                                                @endif
                                            </div>
                                            <div class="trip-card-actions flex flex-col gap-2 sm:flex-row sm:items-center">
                                                <button type="button"
                                                    class="btn-details-toggle inline-flex items-center justify-center gap-1 rounded-lg border border-amber-200 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-50"
                                                    aria-expanded="false"
                                                    aria-controls="trip-details-{{ $trip->trip_id }}"
                                                    data-toggle-details="#trip-details-{{ $trip->trip_id }}">
                                                    {{ __('client.route_show.trip_card.details_button', ['default' => 'Chi tiết']) }}
                                                    <i class="fa-solid fa-chevron-down chevron-icon text-[10px] transition-transform duration-300"></i>
                                                </button>
                                                <a href="{{ route('client.booking.create', ['trip_id' => $trip->trip_id, 'date' => $departureDate]) }}"
                                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-amber-700 {{ $hasSeats ? '' : 'pointer-events-none opacity-50' }}">
                                                    <i class="fa-solid fa-ticket"></i>
                                                    {{ $hasSeats ? __('client.route_show.trip_card.book_button', ['default' => 'Chọn chuyến']) : __('client.route_show.trip_card.sold_out_button', ['default' => 'Hết chỗ']) }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="trip-card-details bg-slate-50/70" id="trip-details-{{ $trip->trip_id }}" aria-hidden="true">
                                    <div class="space-y-4 p-4">
                                        <div>
                                            <h4 class="mb-3 flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-gray-500">
                                                <i class="fa-solid fa-route text-primary-600"></i>
                                                {{ __('client.route_show.trip_card.route_timeline_title') }}
                                            </h4>
                                            <div class="relative pl-6">
                                                <span class="absolute bottom-2 left-[7px] top-2 w-0.5 rounded bg-linear-to-b from-amber-500 to-emerald-500"></span>
                                                <div class="relative py-1.5">
                                                    <span class="absolute -left-[17px] top-2.5 h-2.5 w-2.5 rounded-full border-2 border-amber-200 bg-amber-500"></span>
                                                    <p class="text-sm font-bold text-slate-800">{{ $tripStart->format('H:i') }}</p>
                                                    <p class="text-xs text-slate-600">{{ $firstPickup->name ?? __('client.route_show.hero_origin') }}</p>
                                                </div>
                                                <div class="relative py-1.5">
                                                    <span class="absolute -left-[17px] top-2.5 h-2.5 w-2.5 rounded-full border-2 border-emerald-200 bg-emerald-500"></span>
                                                    <p class="text-sm font-bold text-slate-800">{{ $tripEnd->format('H:i') }}</p>
                                                    <p class="text-xs text-slate-600">{{ $firstDropoff->name ?? __('client.route_show.hero_destination') }} <span class="ml-1 text-[11px] text-slate-400">({{ $durationLabel }})</span></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                            <div class="rounded-lg border border-amber-200 bg-amber-50/70 p-3">
                                                <h4 class="mb-2 flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-amber-700">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                    {{ __('client.route_show.trip_card.pickup_point', ['default' => 'Điểm đón']) }}
                                                    <span class="text-[10px] font-normal text-amber-500">({{ __('client.route_show.trip_card.point_count', ['count' => $pickupPoints->count()]) }})</span>
                                                </h4>
                                                @forelse ($pickupPoints as $pickup)
                                                    <p class="text-xs text-slate-600">• {{ $pickup->name }}</p>
                                                @empty
                                                    <p class="text-xs text-slate-400">{{ __('client.route_show.trip_card.not_updated') }}</p>
                                                @endforelse
                                            </div>
                                            <div class="rounded-lg border border-emerald-200 bg-emerald-50/70 p-3">
                                                <h4 class="mb-2 flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-emerald-700">
                                                    <i class="fa-solid fa-flag-checkered"></i>
                                                    {{ __('client.route_show.trip_card.dropoff_point', ['default' => 'Điểm trả']) }}
                                                    <span class="text-[10px] font-normal text-emerald-500">({{ __('client.route_show.trip_card.point_count', ['count' => $dropoffPoints->count()]) }})</span>
                                                </h4>
                                                @forelse ($dropoffPoints as $dropoff)
                                                    <p class="text-xs text-slate-600">• {{ $dropoff->name }}</p>
                                                @empty
                                                    <p class="text-xs text-slate-400">{{ __('client.route_show.trip_card.not_updated') }}</p>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                            <div>
                                                <h4 class="mb-2 flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-gray-500">
                                                    <i class="fa-solid fa-star text-amber-400"></i>
                                                    {{ __('client.route_show.details_modal.services_title', ['default' => 'Tiện ích']) }}
                                                </h4>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @forelse ($serviceList as $service)
                                                        <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">
                                                            <i class="fa-solid fa-check-circle text-[9px] text-primary-600"></i>
                                                            {{ $service }}
                                                        </span>
                                                    @empty
                                                        <span class="text-xs text-gray-400">{{ __('client.route_show.trip_card.no_services_updated') }}</span>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="mb-2 flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-gray-500">
                                                    <i class="fa-solid fa-bus text-primary-500"></i>
                                                    {{ __('client.route_show.details_modal.bus_info_title') }}
                                                </h4>
                                                <div class="space-y-1 text-xs text-gray-500">
                                                    <p><span class="font-semibold text-gray-600">{{ __('client.route_show.details_modal.bus_type', ['default' => 'Loại xe']) }}:</span> {{ $trip->bus_model ?? __('client.booking.service.not_available') }}</p>
                                                    <p><span class="font-semibold text-gray-600">{{ __('client.route_show.trip_card.trip_code') }}:</span> #{{ $trip->trip_id }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($imageGallery->count() > 1)
                                            <div class="mt-4">
                                                <h4 class="mb-2 text-xs font-bold uppercase tracking-wider text-gray-500">{{ __('client.route_show.details_modal.gallery_title') }}</h4>
                                                <div class="detail-gallery mt-1 flex gap-2 overflow-x-auto pb-1">
                                                    @foreach ($imageGallery->take(6) as $idx => $image)
                                                        <button type="button"
                                                            class="detail-gallery-thumb h-16 w-20 shrink-0 overflow-hidden rounded-md border border-transparent transition hover:border-amber-300"
                                                            data-image-trigger
                                                            data-target="#trip-image-{{ $trip->trip_id }}"
                                                            data-image="{{ $image }}"
                                                            data-lightbox-gallery="gallery-{{ $trip->trip_id }}"
                                                            data-lightbox-index="{{ $idx }}"
                                                            data-lightbox-images='@json($imageGallery->values())'>
                                                            <img src="{{ $image }}" alt="{{ __('client.route_show.trip_card.bus_image_alt') }}" loading="lazy"
                                                                class="h-full w-full object-cover">
                                                        </button>
                                                    @endforeach
                                                    @if ($imageGallery->count() > 6)
                                                        <button type="button"
                                                            class="detail-gallery-thumb flex h-16 w-20 shrink-0 items-center justify-center rounded-md border border-transparent bg-gray-100 text-xs font-bold text-gray-500 transition hover:border-amber-300"
                                                            data-lightbox-gallery="gallery-{{ $trip->trip_id }}"
                                                            data-lightbox-index="6"
                                                            data-lightbox-images='@json($imageGallery->values())'>
                                                            +{{ $imageGallery->count() - 6 }}
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        @if (!empty($returnDate) && isset($returnRoute) && isset($returnTrips))
            <section class="bg-white py-10 border-t border-amber-100">
                <div class="container mx-auto max-w-7xl px-4">
                    <div class="mb-6">
                        <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-amber-700">
                            <i class="fa-solid fa-rotate-left"></i>
                            {{ __('client.route_show.return_section.badge') }}
                        </span>
                        <h3 class="mt-3 text-2xl font-extrabold text-slate-800">
                            {{ __('client.route_show.return_section.title') }}
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ __('client.route_show.return_section.subtitle', ['date' => $returnDate]) }}
                        </p>
                    </div>

                    @if ($returnTrips->isNotEmpty())
                        <div class="grid gap-3">
                            @foreach ($returnTrips as $returnTrip)
                                @php
                                    $returnStart = \Carbon\Carbon::createFromFormat('H:i:s', $returnTrip->start_time);
                                    $returnEnd = \Carbon\Carbon::createFromFormat('H:i:s', $returnTrip->end_time);
                                    $returnHasSeats = ($returnTrip->seats_available ?? 0) > 0;
                                    $returnPrice = (int) ($returnTrip->effective_price ?? $returnTrip->price ?? 0);
                                @endphp
                                <article class="rounded-2xl border border-amber-100 bg-white p-4 shadow-soft">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-800">{{ $returnTrip->bus_name }}</p>
                                            <p class="text-xs text-slate-500 mt-1">
                                                {{ $returnRoute->name }} · {{ $returnStart->format('H:i') }} - {{ $returnEnd->format('H:i') }}
                                            </p>
                                            <div class="mt-2 flex items-center gap-2 text-xs">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-slate-600">
                                                    <i class="fa-solid fa-couch"></i>
                                                    {{ $returnHasSeats ? __('client.route_show.trip_card.seats_available') . ' (' . ($returnTrip->seats_available ?? 0) . ')' : __('client.route_show.trip_card.seats_full') }}
                                                </span>
                                                @if (!empty($returnTrip->has_surcharge))
                                                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-1 text-amber-700">
                                                        <i class="fa-solid fa-bolt"></i>
                                                        {{ __('client.route_show.trip_card.holiday_surcharge_badge') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 md:justify-end">
                                            <p class="text-lg font-extrabold text-primary-600">
                                                {{ $returnPrice > 0 ? number_format($returnPrice) . 'đ' : __('client.route_show.price_contact') }}
                                            </p>
                                            <a href="{{ route('client.booking.create', ['trip_id' => $returnTrip->trip_id, 'date' => $returnDate]) }}"
                                                class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-700 {{ $returnHasSeats ? '' : 'opacity-50 pointer-events-none' }}">
                                                {{ $returnHasSeats ? __('client.route_show.trip_card.book_button', ['default' => 'Chọn chuyến']) : __('client.route_show.trip_card.sold_out_button', ['default' => 'Hết chỗ']) }}
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-amber-200 bg-amber-50/50 p-6 text-center text-sm text-amber-700">
                            {{ __('client.route_show.return_section.no_trips') }}
                        </div>
                    @endif
                </div>
            </section>
        @endif
    @else
        {{-- No Results --}}
        <section class="bg-gray-50 py-20">
            <div class="container mx-auto px-4 text-center max-w-lg">
                <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-2xl bg-linear-to-br from-gray-100 to-gray-200 shadow-inner">
                    <i class="fa-solid fa-calendar-xmark text-4xl text-gray-400"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-3">{{ __('client.route_show.no_trips.title') }}</h2>
                <p class="text-gray-500 mb-8 leading-relaxed">{{ __('client.route_show.no_trips.description') }}</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#search-section"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border-2 border-primary-600 px-6 py-3.5 font-semibold text-primary-600 transition hover:bg-primary-50">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        {{ __('client.route_show.no_trips.research_button') }}
                    </a>
                    @if ($hasActiveFilters ?? false)
                        <a href="{{ $clearFiltersUrl }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-3.5 font-semibold text-white shadow-lg shadow-primary-600/25 transition hover:bg-primary-700">
                            <i class="fa-solid fa-xmark"></i>
                            {{ __('client.route_show.no_trips.clear_filters_button') }}
                        </a>
                    @endif
                </div>
                {{-- Suggestion --}}
                <div class="mt-10 p-6 bg-white rounded-2xl border border-gray-200 text-left">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-lightbulb text-yellow-500"></i>
                        {{ __('client.route_show.no_trips.suggestion_title', ['default' => 'Gợi ý']) }}
                    </h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-emerald-500 mt-0.5"></i>
                            {{ __('client.route_show.no_trips.suggestion_1', ['default' => 'Thử chọn ngày khác để tìm thêm chuyến xe']) }}
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-emerald-500 mt-0.5"></i>
                            {{ __('client.route_show.no_trips.suggestion_2', ['default' => 'Liên hệ hotline để được hỗ trợ đặt vé']) }}
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Collapsible Filter Sections
                document.querySelectorAll('.filter-toggle').forEach(function(toggle) {
                    const targetId = toggle.getAttribute('data-target');
                    const content = targetId ? document.getElementById(targetId) : null;
                    const chevron = toggle.querySelector('.filter-chevron');

                    if (!content) {
                        return;
                    }

                    toggle.addEventListener('click', function() {
                        const isHidden = content.classList.toggle('hidden');
                        toggle.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
                        if (chevron) {
                            chevron.classList.toggle('-rotate-90', isHidden);
                        }
                    });
                });

                // Image Trigger (for gallery in expanded details)
                document.querySelectorAll('[data-image-trigger]').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const targetSelector = button.getAttribute('data-target');
                        const imageUrl = button.getAttribute('data-image');
                        const target = document.querySelector(targetSelector);
                        if (target && imageUrl) {
                            target.setAttribute('src', imageUrl);
                        }
                        // Update active state
                        const gallery = button.closest('.detail-gallery');
                        if (gallery) {
                            gallery.querySelectorAll('.detail-gallery-thumb').forEach(t => t.classList.remove('ring-2', 'ring-primary-600'));
                        }
                        button.classList.add('ring-2', 'ring-primary-600');
                    });
                });

                // Trip Card Details Toggle (Expand/Collapse)
                document.querySelectorAll('[data-toggle-details]').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const targetSelector = button.getAttribute('data-toggle-details');
                        const detailsPanel = document.querySelector(targetSelector);
                        if (!detailsPanel) return;

                        const isOpen = detailsPanel.classList.contains('is-open');
                        if (isOpen) {
                            detailsPanel.classList.remove('is-open');
                            button.classList.remove('is-expanded');
                            button.setAttribute('aria-expanded', 'false');
                            detailsPanel.setAttribute('aria-hidden', 'true');
                        } else {
                            detailsPanel.classList.add('is-open');
                            button.classList.add('is-expanded');
                            button.setAttribute('aria-expanded', 'true');
                            detailsPanel.setAttribute('aria-hidden', 'false');
                        }
                    });
                });
            });

            // Fullscreen Image Lightbox
            (function() {
                const lightbox = document.getElementById('image-lightbox');
                if (!lightbox) return;

                const lightboxImg = lightbox.querySelector('[data-lightbox-image]');
                const lightboxClose = lightbox.querySelector('[data-lightbox-close]');
                const lightboxPrev = lightbox.querySelector('[data-lightbox-prev]');
                const lightboxNext = lightbox.querySelector('[data-lightbox-next]');
                const lightboxCounter = lightbox.querySelector('[data-lightbox-counter]');

                let currentImages = [];
                let currentIndex = 0;

                function showLightbox(images, index) {
                    currentImages = images;
                    currentIndex = index;
                    updateLightboxImage();
                    lightbox.classList.remove('hidden');
                    lightbox.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function hideLightbox() {
                    lightbox.classList.add('hidden');
                    lightbox.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                function updateLightboxImage() {
                    if (currentImages.length === 0) return;
                    lightboxImg.src = currentImages[currentIndex];
                    lightboxCounter.textContent = (currentIndex + 1) + ' / ' + currentImages.length;
                    if (currentImages.length > 1) {
                        lightboxPrev.classList.remove('hidden');
                        lightboxNext.classList.remove('hidden');
                        lightboxPrev.classList.add('flex');
                        lightboxNext.classList.add('flex');
                    } else {
                        lightboxPrev.classList.add('hidden');
                        lightboxNext.classList.add('hidden');
                        lightboxPrev.classList.remove('flex');
                        lightboxNext.classList.remove('flex');
                    }
                }

                function nextImage() {
                    currentIndex = (currentIndex + 1) % currentImages.length;
                    updateLightboxImage();
                }

                function prevImage() {
                    currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
                    updateLightboxImage();
                }

                // Click gallery thumbnails to open lightbox
                document.addEventListener('click', function(e) {
                    const trigger = e.target.closest('[data-lightbox-gallery]');
                    if (!trigger) return;

                    e.preventDefault();
                    e.stopPropagation();

                    try {
                        const images = JSON.parse(trigger.getAttribute('data-lightbox-images'));
                        const index = parseInt(trigger.getAttribute('data-lightbox-index'), 10) || 0;
                        if (images && images.length > 0) {
                            showLightbox(images, Math.min(index, images.length - 1));
                        }
                    } catch (err) {
                        console.error('Lightbox parse error:', err);
                    }
                });

                // Also allow clicking the main trip image to open lightbox
                document.querySelectorAll('.trip-image-wrapper img').forEach(function(img) {
                    img.style.cursor = 'pointer';
                    img.addEventListener('click', function() {
                        const card = img.closest('.trip-card');
                        if (!card) return;
                        const galleryBtn = card.querySelector('[data-lightbox-images]');
                        if (galleryBtn) {
                            try {
                                const images = JSON.parse(galleryBtn.getAttribute('data-lightbox-images'));
                                if (images && images.length > 0) {
                                    // Find current image index
                                    const currentSrc = img.src;
                                    let idx = images.findIndex(function(s) { return currentSrc.includes(s) || s.includes(currentSrc.split('/').pop()); });
                                    showLightbox(images, idx >= 0 ? idx : 0);
                                }
                            } catch (err) {
                                // Fallback: show just this image
                                showLightbox([img.src], 0);
                            }
                        } else {
                            showLightbox([img.src], 0);
                        }
                    });
                });

                lightboxClose.addEventListener('click', hideLightbox);
                lightboxPrev.addEventListener('click', prevImage);
                lightboxNext.addEventListener('click', nextImage);

                lightbox.addEventListener('click', function(e) {
                    if (e.target === lightbox) hideLightbox();
                });

                document.addEventListener('keydown', function(e) {
                    if (lightbox.classList.contains('hidden')) return;
                    if (e.key === 'Escape') hideLightbox();
                    if (e.key === 'ArrowRight') nextImage();
                    if (e.key === 'ArrowLeft') prevImage();
                });
            })();

            // Mobile Filter Panel Toggle
            (function() {
                const filterToggle = document.getElementById('mobile-filter-toggle');
                const filterPanel = document.getElementById('filter-panel-mobile');
                const filterBackdrop = document.getElementById('mobile-filter-backdrop');
                const filterClose = document.getElementById('mobile-filter-close');
                let previousFocusedElement = null;

                if (!filterPanel || !filterBackdrop) {
                    return;
                }

                function openFilterPanel() {
                    previousFocusedElement = document.activeElement;
                    filterPanel.classList.remove('-translate-x-full');
                    filterPanel.classList.add('translate-x-0');
                    filterBackdrop.classList.remove('hidden');
                    filterPanel.setAttribute('aria-hidden', 'false');
                    if (filterToggle) {
                        filterToggle.setAttribute('aria-expanded', 'true');
                    }
                    document.body.style.overflow = 'hidden';
                    if (filterClose) {
                        filterClose.focus();
                    }
                }

                function closeFilterPanel() {
                    filterPanel.classList.add('-translate-x-full');
                    filterPanel.classList.remove('translate-x-0');
                    filterBackdrop.classList.add('hidden');
                    filterPanel.setAttribute('aria-hidden', 'true');
                    if (filterToggle) {
                        filterToggle.setAttribute('aria-expanded', 'false');
                    }
                    document.body.style.overflow = '';
                    if (previousFocusedElement && typeof previousFocusedElement.focus === 'function') {
                        previousFocusedElement.focus();
                    }
                }

                filterPanel.addEventListener('keydown', function(e) {
                    if (e.key !== 'Tab' || filterPanel.classList.contains('-translate-x-full')) {
                        return;
                    }

                    const focusable = filterPanel.querySelectorAll(
                        'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
                    );

                    if (focusable.length === 0) {
                        return;
                    }

                    const firstFocusable = focusable[0];
                    const lastFocusable = focusable[focusable.length - 1];

                    if (e.shiftKey && document.activeElement === firstFocusable) {
                        e.preventDefault();
                        lastFocusable.focus();
                    } else if (!e.shiftKey && document.activeElement === lastFocusable) {
                        e.preventDefault();
                        firstFocusable.focus();
                    }
                });

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
                    if (e.key === 'Escape' && !filterPanel.classList.contains('-translate-x-full')) {
                        closeFilterPanel();
                    }
                });
            })();
        </script>
    @endpush

    {{-- Fullscreen Image Lightbox --}}
    <div id="image-lightbox" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/90 p-4" style="z-index: 100020;" role="dialog" aria-modal="true" aria-label="Image preview">
        <button type="button"
            class="absolute right-4 top-4 inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/25 bg-white/15 text-white transition hover:bg-white/25"
            data-lightbox-close aria-label="Close">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <button type="button"
            class="absolute left-4 top-1/2 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/25 bg-white/15 text-white transition hover:bg-white/25"
            data-lightbox-prev aria-label="Previous image">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <img class="max-h-[85vh] max-w-[90vw] rounded-lg object-contain shadow-2xl" data-lightbox-image src="" alt="Preview">
        <button type="button"
            class="absolute right-4 top-1/2 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-white/25 bg-white/15 text-white transition hover:bg-white/25"
            data-lightbox-next aria-label="Next image">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
        <div class="absolute bottom-5 rounded-full bg-black/45 px-4 py-1.5 text-xs font-semibold text-white/75" data-lightbox-counter></div>
    </div>

    {{-- Mobile Sticky Booking Bar --}}
    @if ($trips->isNotEmpty())
        @php
            $lowestPrice = $trips->min(fn($trip) => (int) ($trip->effective_price ?? $trip->price ?? 0));
        @endphp
        <div id="mobile-booking-bar"
            class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-50 transform translate-y-full transition-transform duration-300 lg:hidden">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-gray-500">
                            {{ __('client.route_show.mobile_bar.from', ['default' => 'Chỉ từ']) }}</p>
                        <p class="text-xl font-bold text-primary-600">
                            {{ number_format($lowestPrice, 0, ',', '.') }}₫
                        </p>
                    </div>
                    <a href="#availabilities"
                        class="inline-flex max-w-40 flex-1 items-center justify-center gap-2 rounded-md bg-accent px-6 py-3 font-semibold text-slate-900 transition-colors hover:bg-primary-600 hover:text-white">
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
