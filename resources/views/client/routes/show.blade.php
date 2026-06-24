{{-- ===== resources\views\client\routes\show.blade.php ===== --}}
<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.route_show.meta_title')" :description="$description ?? ''">
    @php
        $heroImage = \App\Helpers\SystemHelper::mediaUrl($route->banner_url ?? $route->thumbnail_url, \App\Helpers\SystemHelper::mediaUrl('/client/images/city_imgs/ha-noi.jpg'));
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
        $galleryFallback = \App\Helpers\SystemHelper::mediaUrl('/client/images/kingexpressbus/sleeper/1.jpg');
    @endphp

    @push('styles')
        <style>
            .route-hero {
                background:
                    linear-gradient(105deg, rgba(4, 17, 31, 0.9), rgba(4, 17, 31, 0.64) 52%, rgba(255, 155, 0, 0.34)),
                    url('{{ $heroImage }}');
                background-size: cover;
                background-position: center;
                position: relative;
                overflow: hidden;
            }

            .route-hero::after {
                content: '';
                position: absolute;
                inset: 0;
                pointer-events: none;
                background: linear-gradient(180deg, rgba(4, 17, 31, 0.12), rgba(4, 17, 31, 0.34));
            }

            .trip-detail-modal {
                isolation: isolate;
            }

            .trip-detail-backdrop,
            .trip-detail-panel {
                transition-duration: 220ms;
                transition-property: opacity, transform;
                transition-timing-function: cubic-bezier(0.2, 0.8, 0.2, 1);
            }

            .trip-detail-modal.is-open .trip-detail-backdrop,
            .trip-detail-modal.is-open .trip-detail-panel {
                opacity: 1;
            }

            .trip-detail-modal.is-open .trip-detail-panel {
                transform: translateY(0) scale(1);
            }

            .trip-detail-section + .trip-detail-section {
                border-top: 1px solid rgba(226, 232, 240, 0.92);
                padding-top: 1.25rem;
            }

            @media (prefers-reduced-motion: reduce) {
                .trip-detail-backdrop,
                .trip-detail-panel {
                    transition-duration: 0.01ms !important;
                }
            }
        </style>
    @endpush

    {{-- Hero + Search --}}
    <section id="search-section" class="route-hero ksb-section-hero text-white">
        <div class="container relative z-10 mx-auto max-w-7xl px-4">
            <div class="grid gap-6 lg:grid-cols-[1fr_0.55fr] lg:items-end">
                <div>
                    <span class="mb-3 inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-white/78">
                        <i class="fa-solid fa-map-location-dot"></i>
                        {{ __('client.route_show.hero_brand') }}
                    </span>
                    <h1 class="ksb-text-balance font-display text-4xl font-extrabold leading-tight md:text-5xl lg:text-6xl">{{ $route->name }}</h1>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-sm text-white/86 lg:justify-end">
                    <span class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2">
                        <i class="fa-solid fa-bus text-accent"></i>
                        {{ $trips->count() }} {{ __('client.route_show.hero_trips') }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 py-2">
                        <i class="fa-solid fa-tag text-accent"></i>
                        {{ $priceDisplay }}
                    </span>
                </div>
            </div>

            <div class="ksb-panel-strong mt-8 text-slate-800">
                <x-client.search-bar :search-data="$searchData" :submit-label="__('client.route_show.search_submit_label')" />
            </div>
        </div>
    </section>

    @if ($trips->isNotEmpty() || $hasActiveFilters)
        {{-- Quick Filter Pills --}}
        <section class="ksb-section-compact border-y border-amber-100 bg-white">
            <div class="container mx-auto max-w-7xl px-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-1">
                    <span class="mr-1 whitespace-nowrap text-sm font-semibold text-gray-500">
                        {{ __('client.route_show.quick_filters.label') }}
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
                        {{ __('client.route_show.quick_filters.has_seats') }}
                    </a>

                    {{-- Clear All Filters --}}
                    @if ($hasActiveFilters)
                        <a href="{{ $clearFiltersUrl }}"
                            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100">
                            <i class="fa-solid fa-xmark"></i>
                            {{ __('client.route_show.quick_filters.clear_all') }}
                        </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Results Section --}}
        <section id="availabilities" class="ksb-section ksb-section-band">
            <div class="container mx-auto max-w-7xl px-4">
                {{-- Results Header --}}
                <div class="mb-8 flex flex-col items-start justify-between gap-3 lg:flex-row lg:items-center">
                    <div>
                        <h2 class="font-display text-2xl font-extrabold text-gray-900 md:text-3xl">
                            {{ __('client.route_show.results_title') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            @if ($tripStats['filtered'] < $tripStats['total'])
                                {{ __('client.route_show.results_subtitle_filtered', ['filtered' => $tripStats['filtered'], 'total' => $tripStats['total'], 'date' => $departureDate]) }}
                            @else
                                {{ __('client.route_show.results_subtitle', ['filtered' => $tripStats['filtered'], 'total' => $tripStats['total'], 'date' => $departureDate]) }}
                            @endif
                        </p>
                    </div>
                    <button id="mobile-filter-toggle"
                        class="ksb-btn-secondary px-5 text-sm lg:hidden"
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
                <div id="mobile-filter-backdrop" class="ksb-drawer-backdrop fixed inset-0 hidden bg-slate-900/50 lg:hidden"></div>

                {{-- Mobile Filter Slide-in Panel --}}
                <div id="filter-panel-mobile"
                    class="ksb-drawer fixed inset-y-0 left-0 w-[320px] max-w-[90vw] -translate-x-full overflow-y-auto bg-white shadow-card transition-transform duration-300 lg:hidden"
                    role="dialog" aria-modal="true" aria-hidden="true" aria-label="{{ __('client.route_show.filters.mobile_title') }}" tabindex="-1">
                    {{-- Mobile Header --}}
                    <div class="flex justify-between items-center p-5 border-b border-gray-100">
                        <h3 class="text-lg font-bold">{{ __('client.route_show.filters.mobile_title') }}</h3>
                        <button id="mobile-filter-close"
                                class="text-gray-400 hover:text-gray-600 text-2xl"
                                aria-label="ÄÃ³ng bá»™ lá»c">&times;</button>
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

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    {{-- Filters Sidebar (Desktop only) --}}
                    <aside class="hidden lg:col-span-3 lg:block">
                        <div id="filter-panel-desktop" class="ksb-filter-shell">
                            <div class="flex items-center justify-between border-b border-slate-100 bg-amber-50/60 px-5 py-4">
                                <h3 class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                    <i class="fa-solid fa-sliders text-primary-600"></i>
                                    {{ __('client.route_show.filters.sidebar_title') }}
                                </h3>
                                @if ($hasActiveFilters)
                                    <span class="inline-flex items-center justify-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-bold text-primary-700">
                                        {{ $activeFilterCount }} {{ __('client.route_show.filters.active') }}
                                    </span>
                                @endif
                            </div>
                            <form id="filter-form" action="{{ $clearFiltersUrl }}" method="GET" class="flex flex-col">
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
                    <div class="space-y-4 lg:col-span-9">
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
                                    ->map(fn ($image) => \App\Helpers\SystemHelper::mediaUrl($image))
                                    ->filter()
                                    ->values();
                                if ($imageGallery->isEmpty() && $trip->bus_thumbnail) {
                                    $imageGallery = collect([\App\Helpers\SystemHelper::mediaUrl($trip->bus_thumbnail)]);
                                }
                                $primaryImage = \App\Helpers\SystemHelper::mediaUrl($trip->primary_bus_image ?? null, $imageGallery->first() ?: $galleryFallback);
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
                                $serviceList = collect($trip->service_details ?? [])
                                    ->filter()
                                    ->values();

                                if ($serviceList->isEmpty()) {
                                    $serviceList = collect($trip->services ?? [])
                                        ->filter()
                                        ->map(fn ($service) => [
                                            'name' => $service,
                                            'icon' => 'fa-solid fa-circle-check',
                                        ])
                                        ->values();
                                }
                                $hasSeats = ($trip->seats_available ?? 0) > 0;
                                $tripPrice = $trip->effective_price ?? $trip->price ?? null;
                            @endphp

                            <article class="trip-card ksb-trip-row"
                                id="trip-card-{{ $trip->trip_id }}">
                                <div class="trip-card-inner grid gap-4 p-4 md:grid-cols-[160px_1fr] lg:p-5">
                                    <div class="trip-image-wrapper relative aspect-video w-full overflow-hidden rounded-xl md:aspect-square md:w-40 md:shrink-0">
                                        <img id="trip-image-{{ $trip->trip_id }}" src="{{ $primaryImage }}"
                                            alt="{{ $trip->bus_name }}" loading="lazy"
                                            class="h-full w-full object-cover">
                                    </div>

                                    <div class="trip-body flex min-w-0 flex-1 flex-col gap-3">
                                        <div class="trip-card-header flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="min-w-0">
                                                <h3 class="truncate font-display text-lg font-extrabold leading-tight text-gray-900">{{ $trip->bus_name }}</h3>
                                                <p class="mt-1 text-sm text-gray-500">{{ $trip->bus_model ?? __('client.booking.service.not_available') }}</p>
                                            </div>
                                            <div class="flex shrink-0 flex-col items-start gap-1 sm:items-end sm:text-right">
                                                @if ($trip->has_price)
                                                    <p class="ksb-price text-xl font-extrabold leading-none">
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
                                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ !empty($trip->is_off_day) || !$hasSeats ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                                    <i class="fa-solid fa-circle text-[5px]"></i>
                                                    @if (!empty($trip->is_off_day))
                                                        {{ __('client.route_show.trip_card.off_day_badge') }}
                                                    @elseif ($hasSeats)
                                                        {{ __('client.route_show.trip_card.seats_available') }}
                                                    @else
                                                        {{ __('client.route_show.trip_card.seats_full') }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        <div class="rounded-2xl border border-slate-100 bg-slate-50 p-3">
                                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                                <div class="min-w-0">
                                                    <p class="font-display text-xl font-extrabold leading-none text-slate-900">{{ $tripStart->format('H:i') }}</p>
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
                                                    <p class="font-display text-xl font-extrabold leading-none text-slate-900">{{ $tripEnd->format('H:i') }}</p>
                                                    <p class="mt-0.5 truncate text-xs text-slate-500" title="{{ $firstDropoff->name ?? __('client.route_show.trip_card.dropoff_point') }}">
                                                        {{ $firstDropoff->name ?? __('client.route_show.trip_card.dropoff_point') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-auto flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="flex min-w-0 flex-wrap items-center gap-1.5">
                                                @foreach ($serviceList->take(3) as $service)
                                                    @php
                                                        $serviceName = is_array($service) ? ($service['name'] ?? '') : $service;
                                                        $serviceIcon = is_array($service) ? ($service['icon'] ?? 'fa-solid fa-circle-check') : 'fa-solid fa-circle-check';
                                                    @endphp
                                                    <span class="hidden items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600 sm:inline-flex">
                                                        <i class="{{ $serviceIcon }} text-[9px] text-primary-600"></i>
                                                        {{ $serviceName }}
                                                    </span>
                                                @endforeach
                                                @if ($serviceList->count() > 3)
                                                    <span class="hidden rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600 sm:inline-flex">+{{ $serviceList->count() - 3 }}</span>
                                                @endif
                                            </div>
                                            <div class="trip-card-actions flex flex-col gap-2 sm:flex-row sm:items-center">
                                                <button type="button"
                                                    class="btn-details-toggle ksb-btn-secondary min-h-10 px-3 text-xs"
                                                    aria-haspopup="dialog"
                                                    aria-controls="trip-detail-modal-{{ $trip->trip_id }}"
                                                    data-trip-modal-target="#trip-detail-modal-{{ $trip->trip_id }}">
                                                    {{ __('client.route_show.trip_card.details_button') }}
                                                    <i class="fa-solid fa-circle-info text-[10px]"></i>
                                                </button>
                                                <a href="{{ route('client.booking.create', ['trip_id' => $trip->trip_id, 'date' => $departureDate]) }}"
                                                    class="ksb-btn-primary min-h-10 px-4 text-xs {{ $hasSeats && empty($trip->is_off_day) ? '' : 'pointer-events-none opacity-50' }}">
                                                    <i class="fa-solid fa-ticket"></i>
                                                    @if (!empty($trip->is_off_day))
                                                        {{ __('client.route_show.trip_card.off_day_button') }}
                                                    @elseif ($hasSeats)
                                                        {{ __('client.route_show.trip_card.book_button') }}
                                                    @else
                                                        {{ __('client.route_show.trip_card.sold_out_button') }}
                                                    @endif
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>

                            <div class="trip-detail-modal fixed inset-0 hidden items-end justify-center p-0 sm:items-center sm:p-4"
                                id="trip-detail-modal-{{ $trip->trip_id }}"
                                role="dialog"
                                aria-modal="true"
                                aria-hidden="true"
                                aria-labelledby="trip-detail-title-{{ $trip->trip_id }}"
                                tabindex="-1"
                                style="z-index: 100010;">
                                <div class="trip-detail-backdrop absolute inset-0 bg-slate-950/65 opacity-0 backdrop-blur-sm" data-trip-modal-close></div>

                                <div class="trip-detail-panel relative z-10 flex max-h-[94dvh] w-full max-w-4xl translate-y-6 scale-[0.98] flex-col overflow-hidden rounded-t-2xl bg-white opacity-0 shadow-2xl sm:max-h-[90dvh] sm:rounded-2xl">
                                        <div class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 px-4 py-4 backdrop-blur sm:px-5">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="min-w-0">
                                                    <p class="ksb-section-label mb-1 text-[11px]">{{ __('client.route_show.details_modal.overview_title') }}</p>
                                                    <h3 id="trip-detail-title-{{ $trip->trip_id }}" class="font-display text-xl font-extrabold leading-tight text-slate-900 sm:text-2xl">
                                                        {{ $trip->bus_name }}
                                                    </h3>
                                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700">
                                                            {{ __('client.route_show.trip_card.trip_code') }} #{{ $trip->trip_id }}
                                                        </span>
                                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 font-semibold {{ !empty($trip->is_off_day) || !$hasSeats ? 'bg-rose-50 text-rose-700' : 'bg-emerald-50 text-emerald-700' }}">
                                                            <i class="fa-solid fa-circle text-[5px]"></i>
                                                            @if (!empty($trip->is_off_day))
                                                                {{ __('client.route_show.trip_card.off_day_badge') }}
                                                            @elseif ($hasSeats)
                                                                {{ __('client.route_show.trip_card.seats_available') }}
                                                            @else
                                                                {{ __('client.route_show.trip_card.seats_full') }}
                                                            @endif
                                                        </span>
                                                        <span class="font-display text-lg font-extrabold text-primary-700">
                                                            @if ($trip->has_price && $tripPrice !== null)
                                                                {{ number_format($tripPrice) }}<span class="text-xs font-medium text-slate-500">đ</span>
                                                            @else
                                                                {{ __('client.route_show.price_contact') }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>

                                                <button type="button"
                                                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
                                                    aria-label="{{ __('client.route_show.details_modal.close') }}"
                                                    data-trip-modal-close>
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto px-4 py-5 sm:px-6">
                                            <div class="grid gap-6 lg:grid-cols-[minmax(0,0.92fr)_minmax(0,1.08fr)]">
                                                <section class="trip-detail-section lg:border-t-0 lg:pt-0">
                                                    <div class="relative aspect-[4/3] overflow-hidden rounded-xl bg-slate-100 sm:aspect-[16/10]">
                                                        <img id="gallery-featured-{{ $trip->trip_id }}"
                                                            src="{{ $primaryImage }}"
                                                            alt="{{ __('client.route_show.details_modal.bus_image_alt') }}"
                                                            class="h-full w-full object-cover transition duration-500 hover:scale-[1.03]"
                                                            style="cursor: zoom-in;"
                                                            data-lightbox-gallery="gallery-{{ $trip->trip_id }}"
                                                            data-lightbox-index="0"
                                                            data-lightbox-images='@json($imageGallery->values())'>
                                                        <div class="pointer-events-none absolute inset-x-0 bottom-0 flex items-center justify-between gap-3 bg-gradient-to-t from-slate-950/70 to-transparent px-4 pb-4 pt-12 text-white">
                                                            <span class="text-sm font-semibold">{{ __('client.route_show.details_modal.gallery_title') }}</span>
                                                            <span class="text-xs text-white/80">{{ __('client.route_show.details_modal.gallery_hint') }}</span>
                                                        </div>
                                                    </div>

                                                    @if ($imageGallery->count() > 1)
                                                        <div class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-5">
                                                            @foreach ($imageGallery as $idx => $image)
                                                                <button type="button"
                                                                    class="detail-gallery-thumb relative aspect-[4/3] w-full overflow-hidden rounded-lg border-2 {{ $idx === 0 ? 'border-primary-600 ring-2 ring-primary-50' : 'border-transparent' }} transition hover:border-primary-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500"
                                                                    data-gallery-preview="#gallery-featured-{{ $trip->trip_id }}"
                                                                    data-image="{{ $image }}"
                                                                    data-lightbox-gallery="gallery-{{ $trip->trip_id }}"
                                                                    data-lightbox-index="{{ $idx }}"
                                                                    data-lightbox-images='@json($imageGallery->values())'>
                                                                    <img src="{{ $image }}" alt="{{ __('client.route_show.details_modal.bus_image_alt') }}" loading="lazy" class="h-full w-full object-cover">
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                    @elseif ($imageGallery->isEmpty())
                                                        <p class="mt-3 text-sm text-slate-500">{{ __('client.route_show.details_modal.no_gallery') }}</p>
                                                    @endif
                                                </section>

                                                <section class="trip-detail-section lg:border-t-0 lg:pt-0">
                                                    <h4 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase text-slate-700">
                                                        <i class="fa-solid fa-route text-slate-400"></i>
                                                        {{ __('client.route_show.details_modal.journey_summary') }}
                                                    </h4>

                                                    <div class="divide-y divide-slate-200 border-y border-slate-200">
                                                        <div class="grid grid-cols-[76px_minmax(0,1fr)] gap-3 py-3">
                                                            <div>
                                                                <p class="font-display text-lg font-extrabold leading-none text-slate-900">
                                                                    {{ $tripStart->format('H:i') }}
                                                                </p>
                                                                <p class="mt-1 text-[11px] font-semibold uppercase text-slate-500">{{ __('client.route_show.hero_origin') }}</p>
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="text-[11px] font-bold uppercase text-slate-500">Điểm xuất phát</p>
                                                                <h5 class="mt-1 break-words text-base font-bold leading-snug text-slate-900">{{ $firstPickup->name ?? __('client.route_show.hero_origin') }}</h5>
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-[76px_minmax(0,1fr)] gap-3 py-3">
                                                            <div class="flex items-center">
                                                                <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                                                    <i class="fa-regular fa-clock text-sm"></i>
                                                                </span>
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="text-[11px] font-bold uppercase text-slate-500">{{ __('client.route_show.trip_card.duration_label') }}</p>
                                                                <p class="mt-1 text-base font-bold leading-snug text-slate-900">{{ $durationLabel }}</p>
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-[76px_minmax(0,1fr)] gap-3 py-3">
                                                            <div>
                                                                <p class="font-display text-lg font-extrabold leading-none text-slate-900">
                                                                    {{ $tripEnd->format('H:i') }}
                                                                </p>
                                                                <p class="mt-1 text-[11px] font-semibold uppercase text-slate-500">{{ __('client.route_show.hero_destination') }}</p>
                                                            </div>
                                                            <div class="min-w-0">
                                                                <p class="text-[11px] font-bold uppercase text-slate-500">Điểm đến</p>
                                                                <h5 class="mt-1 break-words text-base font-bold leading-snug text-slate-900">{{ $firstDropoff->name ?? __('client.route_show.hero_destination') }}</h5>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-start justify-between gap-4 py-3">
                                                            <span class="text-sm text-slate-500">{{ __('client.route_show.details_modal.bus_type') }}</span>
                                                            <p class="min-w-0 text-right text-sm font-bold leading-6 text-slate-900">{{ $trip->bus_model ?? __('client.booking.service.not_available') }}</p>
                                                        </div>
                                                        <div class="flex items-start justify-between gap-4 py-3">
                                                            <span class="text-sm text-slate-500">{{ __('client.route_show.details_modal.status') }}</span>
                                                            <p class="text-right text-sm font-bold {{ !empty($trip->is_off_day) || !$hasSeats ? 'text-rose-700' : 'text-slate-900' }}">
                                                                @if (!empty($trip->is_off_day))
                                                                    {{ __('client.route_show.trip_card.off_day_badge') }}
                                                                @elseif ($hasSeats)
                                                                    {{ __('client.route_show.trip_card.seats_available') }}
                                                                @else
                                                                    {{ __('client.route_show.trip_card.seats_full') }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </section>
                                            </div>

                                            <section class="trip-detail-section mt-5">
                                                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                                    <div>
                                                        <div class="mb-4 flex items-center justify-between gap-3">
                                                            <h4 class="flex min-w-0 items-center gap-2 text-sm font-bold text-slate-900">
                                                                <i class="fa-solid fa-location-dot text-slate-400"></i>
                                                                {{ __('client.route_show.details_modal.pickup_points_title') }}
                                                            </h4>
                                                            <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                                                {{ __('client.route_show.trip_card.point_count', ['count' => $pickupPoints->count()]) }}
                                                            </span>
                                                        </div>

                                                        <div class="relative ml-2 space-y-4 border-l border-dashed border-slate-200 pl-5">
                                                            @forelse ($pickupPoints as $pickup)
                                                                <div class="relative">
                                                                    <span class="absolute -left-[27px] top-1.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-slate-400"></span>
                                                                    <p class="text-sm font-semibold leading-6 text-slate-800">{{ $pickup->name }}</p>
                                                                </div>
                                                            @empty
                                                                <p class="text-sm text-slate-500">{{ __('client.route_show.trip_card.not_updated') }}</p>
                                                            @endforelse
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div class="mb-4 flex items-center justify-between gap-3">
                                                            <h4 class="flex min-w-0 items-center gap-2 text-sm font-bold text-slate-900">
                                                                <i class="fa-solid fa-flag-checkered text-slate-400"></i>
                                                                {{ __('client.route_show.details_modal.dropoff_points_title') }}
                                                            </h4>
                                                            <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                                                {{ __('client.route_show.trip_card.point_count', ['count' => $dropoffPoints->count()]) }}
                                                            </span>
                                                        </div>

                                                        <div class="relative ml-2 space-y-4 border-l border-dashed border-slate-200 pl-5">
                                                            @forelse ($dropoffPoints as $dropoff)
                                                                <div class="relative">
                                                                    <span class="absolute -left-[27px] top-1.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-slate-400"></span>
                                                                    <p class="text-sm font-semibold leading-6 text-slate-800">{{ $dropoff->name }}</p>
                                                                </div>
                                                            @empty
                                                                <p class="text-sm text-slate-500">{{ __('client.route_show.trip_card.not_updated') }}</p>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

                                            <section class="trip-detail-section mt-5">
                                                <div class="grid grid-cols-1 gap-5 md:grid-cols-[1.35fr_0.65fr]">
                                                    <div>
                                                        <h4 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase text-slate-700">
                                                            <i class="fa-solid fa-star text-amber-400"></i>
                                                            {{ __('client.route_show.details_modal.services_title') }}
                                                        </h4>
                                                        <div class="flex flex-wrap gap-2.5">
                                                            @forelse ($serviceList as $service)
                                                                @php
                                                                    $serviceName = is_array($service) ? ($service['name'] ?? '') : $service;
                                                                    $serviceIcon = is_array($service) ? ($service['icon'] ?? 'fa-solid fa-circle-check') : 'fa-solid fa-circle-check';
                                                                @endphp
                                                                <span class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700">
                                                                    <i class="{{ $serviceIcon }} text-xs text-primary-600"></i>
                                                                    {{ $serviceName }}
                                                                </span>
                                                            @empty
                                                                <p class="text-sm text-slate-500">{{ __('client.route_show.details_modal.no_services') }}</p>
                                                            @endforelse
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <h4 class="mb-4 flex items-center gap-2 text-sm font-bold uppercase text-slate-700">
                                                            <i class="fa-solid fa-bus text-primary-500"></i>
                                                            {{ __('client.route_show.details_modal.bus_info_title') }}
                                                        </h4>
                                                        <dl class="divide-y divide-slate-200 border-y border-slate-200">
                                                            <div class="flex items-center justify-between gap-4 py-3">
                                                                <dt class="text-sm text-slate-500">{{ __('client.route_show.details_modal.bus_type') }}</dt>
                                                                <dd class="text-right text-sm font-bold text-slate-900">{{ $trip->bus_model ?? __('client.booking.service.not_available') }}</dd>
                                                            </div>
                                                            <div class="flex items-center justify-between gap-4 py-3">
                                                                <dt class="text-sm text-slate-500">{{ __('client.route_show.trip_card.trip_code') }}</dt>
                                                                <dd class="font-mono text-sm font-bold text-slate-900">#{{ $trip->trip_id }}</dd>
                                                            </div>
                                                            <div class="flex items-center justify-between gap-4 py-3">
                                                                <dt class="text-sm text-slate-500">{{ __('client.route_show.trip_card.seats_available') }}</dt>
                                                                <dd class="text-sm font-bold text-slate-900">{{ $trip->seats_available ?? 0 }}</dd>
                                                            </div>
                                                        </dl>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>

                                        <div class="sticky bottom-0 border-t border-slate-200 bg-white px-4 py-4 sm:px-6">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="text-xs font-semibold uppercase text-slate-500">{{ __('client.route_show.trip_card.trip_code') }} #{{ $trip->trip_id }}</p>
                                                    <p class="font-display text-xl font-extrabold text-slate-900">
                                                        @if ($trip->has_price && $tripPrice !== null)
                                                            {{ number_format($tripPrice) }}<span class="text-xs font-medium text-slate-500">đ</span>
                                                        @else
                                                            {{ __('client.route_show.price_contact') }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <a href="{{ route('client.booking.create', ['trip_id' => $trip->trip_id, 'date' => $departureDate]) }}"
                                                    class="ksb-btn-primary min-h-11 px-5 text-sm {{ $hasSeats && empty($trip->is_off_day) ? '' : 'pointer-events-none opacity-50' }}">
                                                    <i class="fa-solid fa-ticket"></i>
                                                    @if (!empty($trip->is_off_day))
                                                        {{ __('client.route_show.trip_card.off_day_button') }}
                                                    @elseif ($hasSeats)
                                                        {{ __('client.route_show.trip_card.book_button') }}
                                                    @else
                                                        {{ __('client.route_show.trip_card.sold_out_button') }}
                                                    @endif
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        @if (!empty($returnDate) && isset($returnRoute) && isset($returnTrips))
            <section class="ksb-section bg-white border-t border-amber-100">
                <div class="container mx-auto max-w-7xl px-4">
                    <div class="mb-6">
                        <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-4 py-1.5 text-xs font-bold uppercase tracking-wide text-amber-700">
                            <i class="fa-solid fa-rotate-left"></i>
                            {{ __('client.route_show.return_section.badge') }}
                        </span>
                        <h3 class="mt-3 font-display text-2xl font-extrabold text-slate-800">
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
                                <article class="ksb-trip-row p-4">
                                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-800">{{ $returnTrip->bus_name }}</p>
                                            <p class="text-xs text-slate-500 mt-1">
                                                {{ $returnRoute->name }} Â· {{ $returnStart->format('H:i') }} - {{ $returnEnd->format('H:i') }}
                                            </p>
                                            <div class="mt-2 flex items-center gap-2 text-xs">
                                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-1 text-slate-600">
                                                    <i class="fa-solid fa-couch"></i>
                                                    @if (!empty($returnTrip->is_off_day))
                                                        {{ __('client.route_show.trip_card.off_day_badge') }}
                                                    @elseif ($returnHasSeats)
                                                        {{ __('client.route_show.trip_card.seats_available') }}
                                                    @else
                                                        {{ __('client.route_show.trip_card.seats_full') }}
                                                    @endif
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
                                                class="ksb-btn-primary px-4 text-sm {{ $returnHasSeats && empty($returnTrip->is_off_day) ? '' : 'opacity-50 pointer-events-none' }}">
                                                @if (!empty($returnTrip->is_off_day))
                                                    {{ __('client.route_show.trip_card.off_day_button') }}
                                                @elseif ($returnHasSeats)
                                                    {{ __('client.route_show.trip_card.book_button') }}
                                                @else
                                                    {{ __('client.route_show.trip_card.sold_out_button') }}
                                                @endif
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
        <section class="ksb-section ksb-section-band">
            <div class="container mx-auto px-4 text-center max-w-lg">
                <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 shadow-inner">
                    <i class="fa-solid fa-calendar-xmark text-4xl text-gray-400"></i>
                </div>
                <h2 class="mb-3 font-display text-2xl font-extrabold text-gray-800">{{ __('client.route_show.no_trips.title') }}</h2>
                <p class="text-gray-500 mb-8 leading-relaxed">{{ __('client.route_show.no_trips.description') }}</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#search-section"
                        class="ksb-btn-secondary px-6">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        {{ __('client.route_show.no_trips.research_button') }}
                    </a>
                    @if ($hasActiveFilters ?? false)
                        <a href="{{ $clearFiltersUrl }}"
                            class="ksb-btn-primary px-6">
                            <i class="fa-solid fa-xmark"></i>
                            {{ __('client.route_show.no_trips.clear_filters_button') }}
                        </a>
                    @endif
                </div>
                {{-- Suggestion --}}
                <div class="ksb-panel mt-10 p-6 text-left">
                    <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-lightbulb text-yellow-500"></i>
                        {{ __('client.route_show.no_trips.suggestion_title') }}
                    </h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-emerald-500 mt-0.5"></i>
                            {{ __('client.route_show.no_trips.suggestion_1') }}
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-check text-emerald-500 mt-0.5"></i>
                            {{ __('client.route_show.no_trips.suggestion_2') }}
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

                // Trip detail modal
                (function() {
                    let activeModal = null;
                    let previousFocus = null;
                    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

                    function openModal(modal, trigger) {
                        previousFocus = trigger || document.activeElement;
                        activeModal = modal;
                        if (modal.parentElement !== document.body) {
                            document.body.appendChild(modal);
                        }
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        modal.setAttribute('aria-hidden', 'false');
                        document.body.classList.add('overflow-hidden');

                        requestAnimationFrame(function() {
                            modal.classList.add('is-open');
                        });

                        const closeButton = modal.querySelector('[data-trip-modal-close]');
                        window.setTimeout(function() {
                            (closeButton || modal).focus({ preventScroll: true });
                        }, reduceMotion ? 0 : 80);
                    }

                    function closeModal(modal) {
                        if (!modal) return;
                        modal.classList.remove('is-open');
                        modal.setAttribute('aria-hidden', 'true');

                        window.setTimeout(function() {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');

                            if (activeModal === modal) {
                                activeModal = null;
                            }

                            if (!document.querySelector('.trip-detail-modal.is-open') && document.getElementById('image-lightbox')?.classList.contains('hidden')) {
                                document.body.classList.remove('overflow-hidden');
                            }

                            if (previousFocus && typeof previousFocus.focus === 'function') {
                                previousFocus.focus({ preventScroll: true });
                            }
                        }, reduceMotion ? 0 : 180);
                    }

                    document.querySelectorAll('[data-trip-modal-target]').forEach(function(button) {
                        button.addEventListener('click', function() {
                            const modal = document.querySelector(button.getAttribute('data-trip-modal-target'));
                            if (modal) {
                                openModal(modal, button);
                            }
                        });
                    });

                    document.querySelectorAll('[data-trip-modal-close]').forEach(function(button) {
                        button.addEventListener('click', function() {
                            closeModal(button.closest('.trip-detail-modal'));
                        });
                    });

                    document.addEventListener('keydown', function(event) {
                        const lightbox = document.getElementById('image-lightbox');
                        if (lightbox && !lightbox.classList.contains('hidden')) {
                            return;
                        }

                        if (event.key === 'Escape' && activeModal) {
                            closeModal(activeModal);
                        }
                    });
                })();

                // Image preview inside trip detail modal
                document.querySelectorAll('[data-gallery-preview]').forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.stopPropagation();

                        const targetSelector = button.getAttribute('data-gallery-preview');
                        const imageUrl = button.getAttribute('data-image');
                        const targetImg = document.querySelector(targetSelector);
                        if (targetImg && imageUrl) {
                            targetImg.setAttribute('src', imageUrl);
                            const idx = button.getAttribute('data-lightbox-index');
                            targetImg.setAttribute('data-lightbox-index', idx);
                        }

                        // Update active state class
                        const parent = button.closest('.trip-detail-section');
                        if (parent) {
                            parent.querySelectorAll('[data-gallery-preview]').forEach(function(btn) {
                                btn.classList.remove('border-primary-600', 'ring-2', 'ring-primary-50');
                                btn.classList.add('border-transparent');
                            });
                        }
                        button.classList.add('border-primary-600', 'ring-2', 'ring-primary-50');
                        button.classList.remove('border-transparent');
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
                    if (!document.querySelector('.trip-detail-modal.is-open')) {
                        document.body.classList.remove('overflow-hidden');
                    }
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
    <div id="image-lightbox" class="ksb-modal fixed inset-0 hidden items-center justify-center bg-black/90 p-4" role="dialog" aria-modal="true" aria-label="Image preview">
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
            class="ksb-alert ksb-mobile-action-bar fixed bottom-0 left-0 right-0 transform border-t border-gray-200 bg-white shadow-lg transition-transform duration-300 lg:hidden translate-y-full">
            <div class="container mx-auto px-4 py-3">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs text-gray-500">
                            {{ __('client.route_show.mobile_bar.from') }}</p>
                        <p class="text-xl font-bold text-primary-600">
                            {{ number_format($lowestPrice, 0, ',', '.') }}â‚«
                        </p>
                    </div>
                    <a href="#availabilities"
                        class="ksb-btn-primary max-w-40 flex-1 px-6 text-sm">
                        {{ __('client.route_show.mobile_bar.view_trips') }}
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
