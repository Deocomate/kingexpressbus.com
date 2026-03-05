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
            /* Hero Section - Compact Banner */
            .route-hero {
                background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.7)),
                    url('{{ $heroImage }}');
                background-size: cover;
                background-position: center;
                min-height: 140px;
                background-attachment: fixed;
            }

            @media (max-width: 768px) {
                .route-hero {
                    background-attachment: scroll;
                    min-height: 120px;
                }
            }

            /* Filter Sidebar */
            .filters-sidebar {
                background: #ffffff;
                border-radius: 8px;
                border: 1px solid #e5e7eb;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
                position: sticky;
                top: 100px;
                max-height: calc(100vh - 120px);
                display: flex;
                flex-direction: column;
            }

            .filters-sidebar form {
                display: flex;
                flex-direction: column;
                max-height: calc(100vh - 120px);
                overflow: hidden;
            }

            .filters-scrollable {
                flex: 1;
                overflow-y: auto;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 transparent;
            }

            .filters-scrollable::-webkit-scrollbar {
                width: 5px;
            }

            .filters-scrollable::-webkit-scrollbar-track {
                background: transparent;
            }

            .filters-scrollable::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            .filters-scrollable::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            .filters-sticky-footer {
                position: sticky;
                bottom: 0;
                background: #f8fafc;
                border-top: 1px solid #e5e7eb;
                border-radius: 0 0 8px 8px;
                padding: 16px 20px;
                z-index: 2;
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
                justify-content: space-between;
                gap: 10px;
            }

            .filter-toggle {
                cursor: pointer;
                user-select: none;
                margin-bottom: 0;
                transition: color 0.2s ease;
            }

            .filter-toggle:hover {
                color: #475569;
            }

            .filter-collapsible .filter-content {
                max-height: 500px;
                overflow: hidden;
                transition: max-height 0.3s ease, margin-top 0.3s ease, opacity 0.2s ease;
                opacity: 1;
                margin-top: 14px;
            }

            .filter-collapsible.collapsed .filter-content {
                max-height: 0;
                opacity: 0;
                margin-top: 0;
            }

            .filter-collapsible.collapsed .filter-chevron {
                transform: rotate(-90deg);
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

            /* Trip Cards - Compact Horizontal Layout */
            .trip-card {
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                transition: box-shadow 0.2s ease, border-color 0.2s ease;
            }

            .trip-card:hover {
                border-color: #d1d5db;
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            }

            .trip-card-inner {
                display: flex;
                flex-direction: row;
                align-items: stretch;
                padding: 12px;
                gap: 14px;
            }

            .trip-image-wrapper {
                position: relative;
                width: 130px;
                flex-shrink: 0;
                overflow: hidden;
                border-radius: 8px;
                aspect-ratio: 1 / 1;
                align-self: center;
            }

            .trip-image-wrapper img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 8px;
            }

            .trip-body {
                flex: 1;
                display: flex;
                flex-direction: column;
                min-width: 0;
                gap: 6px;
            }

            /* Horizontal Time Display */
            .trip-time-route {
                display: flex;
                align-items: center;
                gap: 0;
                margin: 2px 0;
                padding: 8px 12px;
                background: #f8fafc;
                border-radius: 8px;
                border: 1px solid #f1f5f9;
            }

            .trip-time-block {
                display: flex;
                align-items: center;
                gap: 8px;
                min-width: 0;
            }

            .trip-time-block .time-val {
                font-size: 17px;
                font-weight: 800;
                color: #0f172a;
                white-space: nowrap;
                line-height: 1;
            }

            .trip-time-block .time-dot {
                width: 9px;
                height: 9px;
                border-radius: 50%;
                flex-shrink: 0;
            }

            .trip-time-block .time-dot.departure {
                background: #3b82f6;
                border: 2px solid #93c5fd;
            }

            .trip-time-block .time-dot.arrival {
                background: #10b981;
                border: 2px solid #6ee7b7;
            }

            .trip-time-block .time-location {
                color: #475569;
                font-size: 12px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 140px;
                line-height: 1.2;
            }

            .trip-duration-connector {
                display: flex;
                flex-direction: column;
                align-items: center;
                flex: 1;
                min-width: 60px;
                padding: 0 8px;
            }

            .trip-duration-connector .duration-text {
                font-size: 11px;
                font-weight: 600;
                color: #64748b;
                white-space: nowrap;
                margin-bottom: 3px;
            }

            .trip-duration-connector .duration-line {
                width: 100%;
                height: 0;
                border-top: 2px dashed #cbd5e1;
                position: relative;
            }

            .trip-duration-connector .duration-line::before,
            .trip-duration-connector .duration-line::after {
                content: '';
                position: absolute;
                top: -4px;
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: #cbd5e1;
            }

            .trip-duration-connector .duration-line::before {
                left: -3px;
            }

            .trip-duration-connector .duration-line::after {
                right: -3px;
            }

            /* Price Display - Compact */
            .price-tag {
                font-size: 20px;
                font-weight: 800;
                color: #D97706;
                line-height: 1.1;
            }

            .price-tag small {
                font-size: 12px;
                font-weight: 500;
                color: #64748b;
            }

            /* Availability Badge */
            .availability-badge {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 3px 8px;
                border-radius: 9999px;
                font-size: 11px;
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

            /* Services */
            .service-chip {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 3px 8px;
                background: #f1f5f9;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 500;
                color: #475569;
            }

            .service-chip i {
                color: #3b82f6;
                font-size: 9px;
            }

            /* Action Buttons - Compact */
            .btn-book {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                padding: 8px 18px;
                background: #D97706;
                color: #ffffff;
                font-weight: 600;
                font-size: 13px;
                border-radius: 6px;
                transition: background 0.2s ease;
                white-space: nowrap;
            }

            .btn-book:hover {
                background: #B45309;
            }

            .btn-book:active {
                background: #92400E;
            }

            /* Details Toggle Button */
            .btn-details-toggle {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 7px 12px;
                background: transparent;
                color: #1565C0;
                border: 1px solid #bfdbfe;
                font-weight: 600;
                font-size: 12px;
                border-radius: 6px;
                cursor: pointer;
                transition: all 0.2s ease;
                white-space: nowrap;
            }

            .btn-details-toggle:hover {
                background: #eff6ff;
                border-color: #93c5fd;
            }

            .btn-details-toggle .chevron-icon {
                transition: transform 0.3s ease;
                font-size: 10px;
            }

            .btn-details-toggle.is-expanded .chevron-icon {
                transform: rotate(180deg);
            }

            /* Expandable Details Section */
            .trip-card-details {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.4s ease;
                background: #fafbfc;
            }

            .trip-card-details.is-open {
                max-height: 800px;
                border-top: 1px solid #e5e7eb;
            }

            .trip-card-details-inner {
                padding: 16px;
            }

            /* Route Timeline in Details */
            .route-timeline {
                position: relative;
                padding-left: 24px;
            }

            .route-timeline::before {
                content: '';
                position: absolute;
                left: 7px;
                top: 8px;
                bottom: 8px;
                width: 2px;
                background: linear-gradient(to bottom, #3b82f6, #10b981);
                border-radius: 1px;
            }

            .route-timeline-stop {
                position: relative;
                padding: 6px 0;
            }

            .route-timeline-stop::before {
                content: '';
                position: absolute;
                left: -20px;
                top: 12px;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                border: 2px solid;
            }

            .route-timeline-stop.is-origin::before {
                background: #3b82f6;
                border-color: #93c5fd;
            }

            .route-timeline-stop.is-destination::before {
                background: #10b981;
                border-color: #6ee7b7;
            }

            .route-timeline-stop .stop-time {
                font-size: 14px;
                font-weight: 700;
                color: #0f172a;
            }

            .route-timeline-stop .stop-name {
                font-size: 13px;
                color: #475569;
            }

            /* Points Display in Details */
            .points-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .point-card {
                padding: 12px;
                border-radius: 8px;
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
                margin-bottom: 6px;
                display: flex;
                align-items: center;
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
                margin-bottom: 3px;
            }

            .point-item::before {
                content: '•';
                position: absolute;
                left: 0;
                color: #94a3b8;
            }

            /* Gallery in Details */
            .detail-gallery {
                display: flex;
                gap: 8px;
                overflow-x: auto;
                padding-bottom: 4px;
            }

            .detail-gallery-thumb {
                width: 80px;
                height: 60px;
                border-radius: 6px;
                overflow: hidden;
                flex-shrink: 0;
                cursor: pointer;
                border: 2px solid transparent;
                transition: border-color 0.2s ease;
            }

            .detail-gallery-thumb:hover,
            .detail-gallery-thumb.active {
                border-color: #3b82f6;
            }

            .detail-gallery-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            /* Responsive: Mobile stacks vertically */
            @media (max-width: 639px) {
                .trip-card-inner {
                    flex-direction: column;
                    padding: 10px;
                    gap: 10px;
                }

                .trip-image-wrapper {
                    width: 100%;
                    height: auto;
                    aspect-ratio: 16 / 9;
                }

                .trip-card-header {
                    flex-direction: column;
                    gap: 4px;
                }

                .trip-card-header .text-right {
                    text-align: left;
                }

                .trip-time-route {
                    flex-direction: column;
                    align-items: stretch;
                    gap: 4px;
                    padding: 8px 10px;
                }

                .trip-time-block {
                    justify-content: flex-start;
                }

                .trip-time-block .time-location {
                    max-width: none;
                }

                .trip-duration-connector {
                    flex-direction: row;
                    align-items: center;
                    min-width: auto;
                    padding: 4px 0;
                    gap: 8px;
                }

                .trip-duration-connector .duration-line {
                    flex: 1;
                }

                .trip-duration-connector .duration-text {
                    margin-bottom: 0;
                }

                .points-grid {
                    grid-template-columns: 1fr;
                }

                .trip-card-actions {
                    flex-direction: column;
                }

                .trip-card-actions .btn-book,
                .trip-card-actions .btn-details-toggle {
                    width: 100%;
                    justify-content: center;
                }
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
                background: #1565C0;
                border-color: #1565C0;
                color: #ffffff;
            }

            .quick-filter-pill.active:hover {
                background: #0D47A1;
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

            /* Trust Badges */
            .trust-bar {
                background: #ffffff;
                border-bottom: 1px solid #e5e7eb;
            }

            .trust-item {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 13px;
                font-weight: 600;
                color: #475569;
                white-space: nowrap;
            }

            .trust-icon {
                width: 28px;
                height: 28px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                font-size: 12px;
            }

            /* Active Filters Count Badge */
            .filter-sidebar-header {
                padding: 16px 20px;
                border-bottom: 1px solid #f1f5f9;
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: #f8fafc;
                border-radius: 8px 8px 0 0;
            }

            .filter-sidebar-header h3 {
                font-size: 15px;
                font-weight: 700;
                color: #1e293b;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            /* Trip Card Enhancements */
            .trip-card-badge {
                position: absolute;
                top: 12px;
                right: 12px;
                padding: 4px 10px;
                border-radius: 8px;
                font-size: 11px;
                font-weight: 700;
                backdrop-filter: blur(8px);
            }

            .trip-card-badge.hot {
                background: rgba(239, 68, 68, 0.9);
                color: #ffffff;
            }

            .trip-card-badge.new {
                background: rgba(16, 185, 129, 0.9);
                color: #ffffff;
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
                border-radius: 8px;
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

            /* Fullscreen Image Lightbox */
            .image-lightbox {
                position: fixed;
                inset: 0;
                z-index: 200;
                background: rgba(0, 0, 0, 0.92);
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }

            .image-lightbox.is-visible {
                opacity: 1;
                visibility: visible;
            }

            .image-lightbox__close {
                position: absolute;
                top: 16px;
                right: 16px;
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: #ffffff;
                font-size: 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.2s ease;
                z-index: 210;
            }

            .image-lightbox__close:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            .image-lightbox__img {
                max-width: 90vw;
                max-height: 85vh;
                object-fit: contain;
                border-radius: 8px;
                box-shadow: 0 8px 40px rgba(0, 0, 0, 0.4);
                transform: scale(0.92);
                transition: transform 0.3s ease;
            }

            .image-lightbox.is-visible .image-lightbox__img {
                transform: scale(1);
            }

            .image-lightbox__nav {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 44px;
                height: 44px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.15);
                backdrop-filter: blur(8px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: #ffffff;
                font-size: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: background 0.2s ease;
                z-index: 210;
            }

            .image-lightbox__nav:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            .image-lightbox__nav--prev {
                left: 16px;
            }

            .image-lightbox__nav--next {
                right: 16px;
            }

            .image-lightbox__counter {
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                color: rgba(255, 255, 255, 0.7);
                font-size: 13px;
                font-weight: 600;
                background: rgba(0, 0, 0, 0.4);
                backdrop-filter: blur(8px);
                padding: 6px 16px;
                border-radius: 9999px;
            }

            .detail-gallery-thumb {
                position: relative;
            }

            .detail-gallery-thumb::after {
                content: '';
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, 0);
                transition: background 0.2s ease;
                border-radius: 4px;
                pointer-events: none;
            }

            .detail-gallery-thumb:hover::after {
                background: rgba(0, 0, 0, 0.1);
            }
        </style>
    @endpush

    {{-- Hero Section - Compact Banner --}}
    <section class="route-hero flex items-center text-white">
        <div class="container mx-auto px-4 py-6 lg:py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <span class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-white/70 mb-1">
                        <i class="fa-solid fa-map-location-dot"></i>
                        {{ __('client.route_show.hero_brand') }}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-semibold leading-tight">{{ $route->name }}</h1>
                </div>
                <div class="flex items-center gap-4 text-sm text-white/80">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-bus"></i>
                        {{ $trips->count() }} {{ __('client.route_show.hero_trips', ['default' => 'chuyến']) }}
                    </span>
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-tag"></i>
                        {{ $priceDisplay }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Search Section - Compact --}}
    <section id="search-section" class="bg-white py-4 border-b border-gray-100">
        <div class="container mx-auto px-4">
            <div class="bg-neutral-50 rounded-lg p-3 md:p-4 border border-neutral-200">
                <x-client.search-bar :search-data="$searchData" :submit-label="__('client.route_show.search_submit_label')" />
            </div>
        </div>
    </section>

    {{-- Trust Bar --}}
    <section class="trust-bar py-3">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-center gap-6 md:gap-10 overflow-x-auto scrollbar-thin pb-1">
                <div class="trust-item">
                    <span class="trust-icon bg-emerald-100 text-emerald-600">
                        <i class="fa-solid fa-shield-halved"></i>
                    </span>
                    <span>{{ __('client.route_show.trust.safe_booking', ['default' => 'Đặt vé an toàn']) }}</span>
                </div>
                <div class="trust-item">
                    <span class="trust-icon bg-blue-100 text-blue-600">
                        <i class="fa-solid fa-headset"></i>
                    </span>
                    <span>{{ __('client.route_show.trust.support_247', ['default' => 'Hỗ trợ 24/7']) }}</span>
                </div>
                <div class="trust-item">
                    <span class="trust-icon bg-amber-100 text-amber-600">
                        <i class="fa-solid fa-rotate-left"></i>
                    </span>
                    <span>{{ __('client.route_show.trust.easy_refund', ['default' => 'Hoàn vé dễ dàng']) }}</span>
                </div>
                <div class="trust-item">
                    <span class="trust-icon bg-purple-100 text-purple-600">
                        <i class="fa-solid fa-percent"></i>
                    </span>
                    <span>{{ __('client.route_show.trust.best_price', ['default' => 'Giá tốt nhất']) }}</span>
                </div>
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
        <section id="availabilities" class="py-6 lg:py-8 bg-gray-50">
            <div class="container mx-auto px-4">
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

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    {{-- Filters Sidebar (Desktop only) --}}
                    <aside class="hidden lg:block lg:col-span-3">
                        <div id="filter-panel-desktop" class="filters-sidebar">
                            <div class="filter-sidebar-header">
                                <h3>
                                    <i class="fa-solid fa-sliders text-blue-500"></i>
                                    {{ __('client.route_show.filters.sidebar_title', ['default' => 'Bộ lọc']) }}
                                </h3>
                                @if ($hasActiveFilters)
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                                        {{ $activeFilterCount }} {{ __('client.route_show.filters.active', ['default' => 'đang lọc']) }}
                                    </span>
                                @endif
                            </div>
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

                            <article class="trip-card" id="trip-card-{{ $trip->trip_id }}">
                                {{-- Main Card Row --}}
                                <div class="trip-card-inner">
                                    {{-- Square Image with padding --}}
                                    <div class="trip-image-wrapper">
                                        <img id="trip-image-{{ $trip->trip_id }}" src="{{ $primaryImage }}"
                                            alt="{{ $trip->bus_name }}" loading="lazy">
                                        @if ($hasSeats && ($trip->seats_available ?? 0) <= 5 && ($trip->seats_available ?? 0) > 0)
                                            <div class="absolute top-1.5 right-1.5">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-red-500/90 text-white rounded text-[10px] font-bold">
                                                    <i class="fa-solid fa-fire"></i>
                                                    {{ __('client.route_show.trip_card.seats_left', ['count' => $trip->seats_available, 'default' => 'Còn ' . $trip->seats_available . ' chỗ']) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="trip-body">
                                        {{-- Header: Bus info + Price + Availability --}}
                                        <div class="flex items-start justify-between gap-3 trip-card-header">
                                            <div class="min-w-0">
                                                <h3 class="text-sm font-bold text-gray-900 truncate leading-tight">{{ $trip->bus_name }}</h3>
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $trip->bus_model ?? 'N/A' }}</p>
                                            </div>
                                            <div class="text-right shrink-0 flex flex-col items-end gap-1">
                                                @if ($trip->has_price)
                                                    <p class="price-tag">{{ number_format($trip->price) }}<small>đ</small></p>
                                                @else
                                                    <p class="text-sm font-bold text-blue-600">{{ __('client.route_show.price_contact') }}</p>
                                                @endif
                                                <span class="availability-badge {{ $hasSeats ? 'available' : 'unavailable' }}">
                                                    <i class="fa-solid fa-circle text-[5px]"></i>
                                                    @if ($hasSeats)
                                                        {{ __('client.route_show.trip_card.seats_available') }}
                                                        ({{ $trip->seats_available }})
                                                    @else
                                                        {{ __('client.route_show.trip_card.seats_full') }}
                                                    @endif
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Time & Route - Horizontal --}}
                                        <div class="trip-time-route">
                                            <div class="trip-time-block">
                                                <span class="time-dot departure"></span>
                                                <div>
                                                    <span class="time-val">{{ $tripStart->format('H:i') }}</span>
                                                    <span class="time-location block" title="{{ $firstPickup->name ?? __('client.route_show.trip_card.pickup_point') }}">
                                                        {{ $firstPickup->name ?? __('client.route_show.trip_card.pickup_point') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="trip-duration-connector">
                                                <span class="duration-text">{{ $durationLabel }}</span>
                                                <div class="duration-line"></div>
                                            </div>
                                            <div class="trip-time-block">
                                                <span class="time-dot arrival"></span>
                                                <div>
                                                    <span class="time-val">{{ $tripEnd->format('H:i') }}</span>
                                                    <span class="time-location block" title="{{ $firstDropoff->name ?? __('client.route_show.trip_card.dropoff_point') }}">
                                                        {{ $firstDropoff->name ?? __('client.route_show.trip_card.dropoff_point') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Footer: Services + Actions --}}
                                        <div class="flex items-center justify-between gap-2 mt-auto">
                                            <div class="flex items-center gap-1.5 flex-wrap min-w-0">
                                                @foreach ($serviceList->take(3) as $service)
                                                    <span class="service-chip hidden sm:inline-flex">
                                                        <i class="fa-solid fa-check-circle"></i>
                                                        {{ $service }}
                                                    </span>
                                                @endforeach
                                                @if ($serviceList->count() > 3)
                                                    <span class="service-chip hidden sm:inline-flex">+{{ $serviceList->count() - 3 }}</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0 trip-card-actions">
                                                <button type="button"
                                                    class="btn-details-toggle"
                                                    data-toggle-details="#trip-details-{{ $trip->trip_id }}">
                                                    {{ __('client.route_show.trip_card.details_button', ['default' => 'Chi tiết']) }}
                                                    <i class="fa-solid fa-chevron-down chevron-icon"></i>
                                                </button>
                                                <a href="{{ route('client.booking.create', ['trip_id' => $trip->trip_id, 'date' => $departureDate]) }}"
                                                    class="btn-book"
                                                    @if (!$hasSeats) style="opacity: 0.5; pointer-events: none;" @endif>
                                                    <i class="fa-solid fa-ticket"></i>
                                                    {{ $hasSeats ? __('client.route_show.trip_card.book_button', ['default' => 'Chọn chuyến']) : __('client.route_show.trip_card.sold_out_button', ['default' => 'Hết chỗ']) }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Expandable Details Section --}}
                                <div class="trip-card-details" id="trip-details-{{ $trip->trip_id }}">
                                    <div class="trip-card-details-inner">
                                        {{-- Route Timeline --}}
                                        <div class="mb-4">
                                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 flex items-center gap-1.5">
                                                <i class="fa-solid fa-route text-blue-500"></i>
                                                Lộ trình chuyến xe
                                            </h4>
                                            <div class="route-timeline">
                                                <div class="route-timeline-stop is-origin">
                                                    <span class="stop-time">{{ $tripStart->format('H:i') }}</span>
                                                    <span class="stop-name">{{ $firstPickup->name ?? 'Điểm xuất phát' }}</span>
                                                </div>
                                                <div class="route-timeline-stop is-destination">
                                                    <span class="stop-time">{{ $tripEnd->format('H:i') }}</span>
                                                    <span class="stop-name">{{ $firstDropoff->name ?? 'Điểm đến' }}</span>
                                                    <span class="text-xs text-gray-400 ml-1">({{ $durationLabel }})</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Pickup & Dropoff Points --}}
                                        <div class="points-grid mb-4">
                                            <div class="point-card pickup">
                                                <h4 class="point-title">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                    {{ __('client.route_show.trip_card.pickup_point', ['default' => 'Điểm đón']) }}
                                                    <span class="text-[10px] font-normal text-blue-400">({{ $pickupPoints->count() }} điểm)</span>
                                                </h4>
                                                @forelse ($pickupPoints as $pickup)
                                                    <p class="point-item">{{ $pickup->name }}</p>
                                                @empty
                                                    <p class="text-xs text-gray-400">Chưa cập nhật</p>
                                                @endforelse
                                            </div>
                                            <div class="point-card dropoff">
                                                <h4 class="point-title">
                                                    <i class="fa-solid fa-flag-checkered"></i>
                                                    {{ __('client.route_show.trip_card.dropoff_point', ['default' => 'Điểm trả']) }}
                                                    <span class="text-[10px] font-normal text-green-500">({{ $dropoffPoints->count() }} điểm)</span>
                                                </h4>
                                                @forelse ($dropoffPoints as $dropoff)
                                                    <p class="point-item">{{ $dropoff->name }}</p>
                                                @empty
                                                    <p class="text-xs text-gray-400">Chưa cập nhật</p>
                                                @endforelse
                                            </div>
                                        </div>

                                        {{-- Services & Bus Info Row --}}
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-star text-amber-400"></i>
                                                    {{ __('client.route_show.details_modal.services_title', ['default' => 'Tiện ích']) }}
                                                </h4>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @forelse ($serviceList as $service)
                                                        <span class="service-chip">
                                                            <i class="fa-solid fa-check-circle"></i>
                                                            {{ $service }}
                                                        </span>
                                                    @empty
                                                        <span class="text-xs text-gray-400">Chưa có tiện ích</span>
                                                    @endforelse
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2 flex items-center gap-1.5">
                                                    <i class="fa-solid fa-bus text-blue-400"></i>
                                                    Thông tin xe
                                                </h4>
                                                <div class="text-xs text-gray-500 space-y-1">
                                                    <p><span class="font-semibold text-gray-600">{{ __('client.route_show.details_modal.bus_type', ['default' => 'Loại xe']) }}:</span> {{ $trip->bus_model ?? 'N/A' }}</p>
                                                    <p><span class="font-semibold text-gray-600">Mã chuyến:</span> #{{ $trip->trip_id }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Gallery --}}
                                        @if ($imageGallery->count() > 1)
                                            <div class="mt-4">
                                                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">Hình ảnh</h4>
                                                <div class="detail-gallery scrollbar-thin">
                                                    @foreach ($imageGallery->take(6) as $idx => $image)
                                                        <button type="button" class="detail-gallery-thumb"
                                                            data-image-trigger
                                                            data-target="#trip-image-{{ $trip->trip_id }}"
                                                            data-image="{{ $image }}"
                                                            data-lightbox-gallery="gallery-{{ $trip->trip_id }}"
                                                            data-lightbox-index="{{ $idx }}"
                                                            data-lightbox-images='@json($imageGallery->take(6)->values())'>
                                                            <img src="{{ $image }}" alt="Bus image" loading="lazy">
                                                        </button>
                                                    @endforeach
                                                    @if ($imageGallery->count() > 6)
                                                        <button type="button" class="detail-gallery-thumb bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500"
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
    @else
        {{-- No Results --}}
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4 text-center max-w-lg">
                <div class="w-24 h-24 mx-auto mb-6 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-inner">
                    <i class="fa-solid fa-calendar-xmark text-4xl text-gray-400"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-3">{{ __('client.route_show.no_trips.title') }}</h2>
                <p class="text-gray-500 mb-8 leading-relaxed">{{ __('client.route_show.no_trips.description') }}</p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="#search-section"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 border-2 border-blue-600 text-blue-600 rounded-xl font-semibold hover:bg-blue-50 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        {{ __('client.route_show.no_trips.research_button') }}
                    </a>
                    @if ($hasActiveFilters ?? false)
                        <a href="{{ $clearFiltersUrl }}"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition shadow-lg shadow-blue-600/25">
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

                // Collapsible Filter Sections
                document.querySelectorAll('.filter-toggle').forEach(function(toggle) {
                    toggle.addEventListener('click', function() {
                        const section = toggle.closest('.filter-collapsible');
                        if (section) {
                            section.classList.toggle('collapsed');
                        }
                    });
                });

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
                            gallery.querySelectorAll('.detail-gallery-thumb').forEach(t => t.classList.remove('active'));
                        }
                        button.classList.add('active');
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
                        } else {
                            detailsPanel.classList.add('is-open');
                            button.classList.add('is-expanded');
                        }
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

            // Fullscreen Image Lightbox
            (function() {
                const lightbox = document.getElementById('image-lightbox');
                if (!lightbox) return;

                const lightboxImg = lightbox.querySelector('.image-lightbox__img');
                const lightboxClose = lightbox.querySelector('.image-lightbox__close');
                const lightboxPrev = lightbox.querySelector('.image-lightbox__nav--prev');
                const lightboxNext = lightbox.querySelector('.image-lightbox__nav--next');
                const lightboxCounter = lightbox.querySelector('.image-lightbox__counter');

                let currentImages = [];
                let currentIndex = 0;

                function showLightbox(images, index) {
                    currentImages = images;
                    currentIndex = index;
                    updateLightboxImage();
                    lightbox.classList.add('is-visible');
                    document.body.classList.add('overflow-hidden');
                }

                function hideLightbox() {
                    lightbox.classList.remove('is-visible');
                    document.body.classList.remove('overflow-hidden');
                }

                function updateLightboxImage() {
                    if (currentImages.length === 0) return;
                    lightboxImg.src = currentImages[currentIndex];
                    lightboxCounter.textContent = (currentIndex + 1) + ' / ' + currentImages.length;
                    lightboxPrev.style.display = currentImages.length > 1 ? 'flex' : 'none';
                    lightboxNext.style.display = currentImages.length > 1 ? 'flex' : 'none';
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
                    if (!lightbox.classList.contains('is-visible')) return;
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

    {{-- Fullscreen Image Lightbox --}}
    <div id="image-lightbox" class="image-lightbox" role="dialog" aria-modal="true" aria-label="Image preview">
        <button type="button" class="image-lightbox__close" aria-label="Close">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <button type="button" class="image-lightbox__nav image-lightbox__nav--prev" aria-label="Previous image">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <img class="image-lightbox__img" src="" alt="Preview">
        <button type="button" class="image-lightbox__nav image-lightbox__nav--next" aria-label="Next image">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
        <div class="image-lightbox__counter"></div>
    </div>

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
                        class="flex-1 max-w-[160px] inline-flex items-center justify-center gap-2 px-6 py-3 bg-accent-500 text-white font-semibold rounded-md hover:bg-accent-600 transition-colors">
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
