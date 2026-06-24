<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.booking.create.meta_title')" :description="$description ?? ''" body-class="bg-[#F8FAFC]">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
        <style>
            .booking-hero {
                background-image:
                    linear-gradient(112deg, rgba(9, 25, 47, 0.82), rgba(255, 155, 0, 0.52)),
                    url('/client/images/kingexpressbus/cabin/2.jpg');
                background-size: cover;
                background-position: center;
            }

            .booking-panel {
                background: #ffffff;
                border: 1px solid rgba(234, 217, 184, 0.9);
                border-radius: var(--ksb-radius-panel);
                box-shadow: var(--ksb-shadow-soft);
            }

            .booking-sidebar-card {
                background: #ffffff;
                border: 1px solid rgba(234, 217, 184, 0.9);
                border-radius: var(--ksb-radius-panel);
                box-shadow: var(--ksb-shadow-soft);
            }

            /* Custom phone input with searchable country dropdown */
            .phone-input-wrapper {
                display: flex;
                align-items: stretch;
                border: 1px solid #e5e7eb;
                border-radius: 0.75rem;
                background-color: #f8fafc;
                overflow: visible;
                transition: border-color 0.15s ease;
                position: relative;
            }
            .phone-input-wrapper:focus-within {
                border-color: #FF9B00;
                background-color: #fff;
                box-shadow: 0 0 0 2px rgba(255, 155, 0, 0.15);
            }
            .phone-country-btn {
                display: flex;
                align-items: center;
                gap: 6px;
                border: none;
                background: #f8fafc;
                padding: 0 10px;
                cursor: pointer;
                border-right: 1px solid #e5e7eb;
                border-radius: 0.375rem 0 0 0.375rem;
                outline: none;
                white-space: nowrap;
                transition: background 0.15s;
            }
            .phone-country-btn:hover {
                background: #f1f5f9;
            }
            .phone-country-btn img {
                width: 24px;
                height: 18px;
                object-fit: cover;
                border-radius: 2px;
                box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
            }
            .phone-country-btn .arrow {
                font-size: 0.55rem;
                color: #6b7280;
                margin-left: 2px;
            }
            .phone-dial-code {
                display: flex;
                align-items: center;
                width: auto;
                min-width: 48px;
                max-width: 72px;
                border: none;
                background: #f8fafc;
                padding: 0 8px;
                font-size: 0.9375rem;
                font-weight: 600;
                color: #1f2937;
                border-right: 1px solid #e5e7eb;
                outline: none;
                text-align: center;
                font-family: inherit;
            }
            .phone-dial-code::placeholder {
                color: #9ca3af;
            }
            .phone-number-input {
                flex: 1;
                border: none;
                background: transparent;
                padding: 0.75rem;
                font-size: 1rem;
                outline: none;
                min-width: 0;
            }
            .phone-number-input::placeholder {
                color: #9ca3af;
            }
            /* Dropdown */
            .phone-dropdown {
                display: none;
                position: absolute;
                top: calc(100% + 4px);
                left: 0;
                z-index: var(--ksb-z-dropdown);
                background: #fff;
                border: 1px solid #d4d4d4;
                border-radius: 0.5rem;
                box-shadow: 0 8px 24px rgba(0,0,0,0.12);
                width: 300px;
                max-height: 320px;
                overflow: hidden;
                flex-direction: column;
            }
            .phone-dropdown.open {
                display: flex;
            }
            .phone-dropdown-search {
                padding: 8px 10px;
                border-bottom: 1px solid #e5e7eb;
            }
            .phone-dropdown-search input {
                width: 100%;
                border: 1px solid #d4d4d4;
                border-radius: 0.375rem;
                padding: 6px 10px;
                font-size: 0.875rem;
                outline: none;
            }
            .phone-dropdown-search input:focus {
                border-color: #FF9B00;
            }
            .phone-dropdown-list {
                overflow-y: auto;
                flex: 1;
            }
            .phone-dropdown-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 12px;
                cursor: pointer;
                font-size: 0.875rem;
                color: #374151;
                transition: background 0.1s;
            }
            .phone-dropdown-item:hover,
            .phone-dropdown-item.highlighted {
                background: #fff7d6;
            }
            .phone-dropdown-item.selected {
                background: #ffefc0;
                font-weight: 600;
            }
            .phone-dropdown-item img {
                width: 24px;
                height: 18px;
                object-fit: cover;
                border-radius: 2px;
                box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
                flex-shrink: 0;
            }
            .phone-dropdown-item .country-name {
                flex: 1;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .phone-dropdown-item .dial-code {
                color: #6b7280;
                font-size: 0.8125rem;
                flex-shrink: 0;
            }
            .litepicker {
                font-family: 'Be Vietnam Pro', sans-serif;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
                border-radius: 8px;
                border: 1px solid #e5e7eb;
            }

            .quantity-btn {
                transition: all 0.2s ease-in-out;
            }

            .quantity-btn:hover {
                opacity: 0.9;
            }

            .payment-method-label {
                transition: all 0.2s ease-in-out;
                cursor: pointer;
            }

            .payment-method-label.selected {
                border-color: #FF9B00;
                background: #fff9e6;
                box-shadow: var(--ksb-focus);
            }

            /* Stop Selection Cards */
            .stop-card {
                transition: all 0.2s ease-in-out;
                cursor: pointer;
            }

            .stop-card:hover {
                border-color: #FF9B00;
                background-color: #fffdf4;
            }

            .stop-card.selected {
                border-color: #FF9B00;
                background-color: #fff8e6;
                box-shadow: var(--ksb-focus);
            }

            .stop-card.selected .stop-radio {
                border-color: #FF9B00;
                background-color: #FF9B00;
            }

            .stop-card.selected .stop-radio::after {
                content: '';
                display: block;
                width: 8px;
                height: 8px;
                background: white;
                border-radius: 50%;
            }

            .stop-radio {
                width: 20px;
                height: 20px;
                border: 2px solid #d1d5db;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                transition: all 0.2s;
            }

            /* Step Progress */
            .step-progress {
                display: flex;
                justify-content: space-between;
                position: relative;
            }

            .step-progress::before {
                content: '';
                position: absolute;
                top: 16px;
                left: 40px;
                right: 40px;
                height: 2px;
                background: #e5e7eb;
            }

            .step-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
                position: relative;
                z-index: 1;
            }

            .step-circle {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 0.875rem;
                transition: all 0.3s;
            }

            .step-circle.active {
                background: #FF9B00;
                color: white;
            }

            .step-circle.completed {
                background: #22c55e;
                color: white;
            }

            .step-circle.pending {
                background: #e5e7eb;
                color: #9ca3af;
            }

            .step-label {
                font-size: 0.75rem;
                font-weight: 500;
                color: #6b7280;
            }

            .step-label.active {
                color: #b86100;
                font-weight: 600;
            }

            /* Sticky Summary */
            @media (min-width: 1280px) {
                .sticky-summary {
                    position: sticky;
                    top: 132px;
                }
            }

            /* Hotel Pickup Badge */
            .hotel-pickup-badge {
                background: #fef3c7;
                border: 1px solid #f59e0b;
            }
        </style>
    @endpush

    @php
        $busImage = $busImages[0] ?? ($trip->bus_thumbnail ?? '/client/images/kingexpressbus/cabin/1.jpg');
        $baseUnitPrice = (int) ($trip->base_price ?? $trip->price ?? 0);
        $globalSurchargeUnit = (int) ($trip->global_surcharge ?? 0);
        $routeSurchargeUnit = (int) ($trip->route_surcharge ?? 0);
        $totalSurchargeUnit = (int) ($trip->surcharge_total ?? ($globalSurchargeUnit + $routeSurchargeUnit));
        $finalUnitPrice = (int) ($trip->effective_price ?? ($baseUnitPrice + $totalSurchargeUnit));
        $surchargeSnapshot = $trip->surcharge_reason_snapshot ?? null;
        $pickupStops = $stops->where('stop_type', '!=', 'dropoff');
        $dropoffStops = $stops->where('stop_type', '!=', 'pickup');
    @endphp

    {{-- Hero Section --}}
    <section class="booking-hero ksb-section-hero px-4 text-white">
        <div class="container mx-auto max-w-7xl">
            <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-3">
                <div class="space-y-4 lg:col-span-2">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-wider text-white/90">
                        <i class="fa-solid fa-ticket"></i>
                        {{ __('client.booking.create.header_subtitle') }}
                    </span>
                    <h1 class="font-display text-3xl font-extrabold leading-tight md:text-5xl">
                        {{ $trip->route_name }}
                    </h1>
                    <div class="grid max-w-full gap-2 text-sm text-white/80 sm:flex sm:flex-wrap sm:gap-x-5">
                        <span class="inline-flex min-w-0 items-center gap-2">
                            <i class="fa-solid fa-clock w-4 text-center text-accent"></i>
                            {{ \Carbon\Carbon::parse($trip->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($trip->end_time)->format('H:i') }}
                        </span>
                        <span class="inline-flex min-w-0 items-center gap-2">
                            <i class="fa-solid fa-calendar-day w-4 text-center text-green-400"></i>
                            {{ $bookingDate->format('d/m/Y') }}
                        </span>
                        <span class="inline-flex min-w-0 items-center gap-2">
                            <i class="fa-solid fa-bus w-4 text-center text-amber-400"></i>
                            <span class="truncate">{{ $trip->bus_name }}</span>
                        </span>
                    </div>
                </div>
                <div class="relative h-44 overflow-hidden rounded-2xl border border-white/20 shadow-soft lg:h-52">
                    <img src="{{ $busImage }}" alt="{{ $trip->bus_name }}" class="h-full w-full object-cover"
                        loading="lazy">
                    <span
                        class="absolute bottom-3 left-3 inline-flex items-center gap-2 rounded-xl bg-white/95 px-3 py-1.5 text-xs font-semibold text-slate-800">
                        <i class="fa-solid fa-shield-heart text-primary-600"></i>
                        {{ __('client.booking.create.insurance_badge') }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Step Progress Indicator --}}
    <section class="ksb-section-compact border-b border-amber-100 bg-white px-4">
        <div class="container mx-auto max-w-7xl">
            <div class="step-progress ksb-booking-step mx-auto max-w-lg">
                <div class="step-item">
                    <div class="step-circle active" id="step-1-circle">1</div>
                    <span class="step-label active" id="step-1-label">{{ __('client.booking.create.step_trip') }}</span>
                </div>
                <div class="step-item">
                    <div class="step-circle pending" id="step-2-circle">2</div>
                    <span class="step-label" id="step-2-label">{{ __('client.booking.create.step_passenger') }}</span>
                </div>
                <div class="step-item">
                    <div class="step-circle pending" id="step-3-circle">3</div>
                    <span class="step-label" id="step-3-label">{{ __('client.booking.create.step_confirm') }}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="ksb-section ksb-section-band px-4">
        <div class="container mx-auto grid max-w-7xl grid-cols-1 gap-8 xl:grid-cols-[minmax(0,1fr)_380px]">
            <form class="xl:col-span-2 space-y-6" method="POST" action="{{ route('client.booking.store') }}"
                id="booking-form">
                @if (session('error'))
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                        <strong class="font-bold">{{ __('client.booking.create.error_title') }}</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg" role="alert">
                        <strong class="font-bold">{{ __('client.booking.create.validation_error_title') }}</strong>
                        <p class="mt-2">{{ __('client.booking.create.general_validation_error') }}</p>
                    </div>
                @endif

                @csrf
                <input type="hidden" name="trip_id" value="{{ $trip->trip_id }}">
                <input type="hidden" name="total_price" id="total-price-input" value="0">
                <input type="hidden" name="pickup_stop_id" id="pickup_stop_id_hidden"
                    value="{{ old('pickup_stop_id', request('pickup_stop_id')) }}">
                <input type="hidden" name="dropoff_stop_id" id="dropoff_stop_id_hidden"
                    value="{{ old('dropoff_stop_id', request('dropoff_stop_id')) }}">

                {{-- Step 1: Trip Info --}}
                <section class="booking-panel p-5 space-y-5 md:p-6" id="section-step-1">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center gap-2 font-display text-lg font-bold text-gray-900">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm text-white">1</span>
                            {{ __('client.booking.create.trip_info_title') }}
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="booking_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('client.booking.create.departure_date_label') }}
                            </label>
                            <input type="text" id="booking_date" name="booking_date"
                                value="{{ $bookingDate->format('d/m/Y') }}"
                                class="w-full rounded-xl border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-primary-600 focus:ring-primary-100"
                                required>
                        </div>
                        <div>
                            <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('client.booking.create.quantity_label') }}
                            </label>
                            <div class="flex items-center gap-2">
                                {{-- Quantity Control Card --}}
                                <div
                                    class="inline-flex items-center rounded-xl border border-neutral-200 bg-neutral-50 p-1">
                                    <button type="button" id="decrease-quantity"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-900 transition-all shadow-sm">
                                        <i class="fa-solid fa-minus text-sm"></i>
                                    </button>
                                    <div class="w-16 flex flex-col items-center justify-center">
                                        <input type="number" name="quantity" id="quantity"
                                            value="{{ old('quantity', request('quantity', 1)) }}" min="1"
                                            max="{{ $availableSeats }}"
                                            class="w-full text-center font-bold text-xl text-gray-900 bg-transparent border-0 focus:ring-0 p-0"
                                            readonly>
                                        <span
                                            class="text-[10px] text-gray-400 uppercase tracking-wide -mt-1">{{ __('client.booking.create.tickets_unit') }}</span>
                                    </div>
                                    <button type="button" id="increase-quantity"
                                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary-600 text-white hover:bg-primary-700 transition-all shadow-sm">
                                        <i class="fa-solid fa-plus text-sm"></i>
                                    </button>
                                </div>
                                {{-- Available Seats Badge --}}
                                <div
                                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-green-50 border border-green-100 rounded-lg">
                                    <i class="fa-solid fa-couch text-green-500 text-xs"></i>
                                    <span class="text-xs font-medium text-green-700">
                                        {!! trans_choice('client.booking.create.seats_left', $availableSeats, ['count' => $availableSeats]) !!}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Step 2: Pickup & Dropoff --}}
                <section class="booking-panel p-5 space-y-5 md:p-6" id="section-step-2">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center gap-2 font-display text-lg font-bold text-gray-900">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm text-white">2</span>
                            {{ __('client.booking.create.location_title') }}
                        </h2>
                    </div>

                    {{-- Pickup Selection --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fa-solid fa-location-dot text-green-500 mr-1"></i>
                            {{ __('client.booking.create.pickup_label') }}
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="pickup-stops-container">
                            @if ($trip->available_hotel_pickup)
                                <div class="stop-card hotel-pickup-badge flex items-start gap-3 rounded-xl p-4 {{ old('pickup_stop_id', request('pickup_stop_id')) == 'hotel_pickup' ? 'selected' : '' }}"
                                    data-stop-id="hotel_pickup" data-type="pickup">
                                    <div
                                        class="stop-radio {{ old('pickup_stop_id', request('pickup_stop_id')) == 'hotel_pickup' ? '' : '' }}">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <span
                                            class="font-semibold text-gray-900 block">{{ __('client.booking.create.pickup_at_hotel') }}</span>
                                        <p class="text-xs text-amber-700 mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-circle-info"></i>
                                            {{ __('client.booking.create.hotel_pickup_hanoi_only') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @foreach ($pickupStops as $stop)
                                <div class="stop-card flex items-start gap-3 rounded-xl border border-gray-200 p-4 {{ old('pickup_stop_id', request('pickup_stop_id')) == $stop->id ? 'selected' : '' }}"
                                    data-stop-id="{{ $stop->id }}" data-type="pickup">
                                    <div class="stop-radio"></div>
                                    <div class="flex-1 min-w-0">
                                        <span
                                            class="font-semibold text-gray-900 block truncate">{{ $stop->name }}</span>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $stop->address }}</p>
                                        <p class="text-xs text-primary-600 mt-1">{{ $stop->district_name }},
                                            {{ $stop->province_name }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Hotel Pickup Address --}}
                    <div id="hotel-pickup-address-wrapper" class="hidden">
                        <label for="hotel_pickup_address" class="block text-sm font-semibold text-gray-700 mb-2">
                            {{ __('client.booking.create.hotel_address_label') }}
                        </label>
                        <input type="text" id="hotel_pickup_address" name="hotel_pickup_address"
                            value="{{ old('hotel_pickup_address', request('hotel_pickup_address')) }}"
                            class="w-full rounded-xl border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-primary-600 focus:ring-primary-100"
                            placeholder="{{ __('client.booking.create.hotel_address_placeholder') }}">
                    </div>

                    {{-- Dropoff Selection --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fa-solid fa-location-dot text-red-500 mr-1"></i>
                            {{ __('client.booking.create.dropoff_label') }}
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="dropoff-stops-container">
                            @foreach ($dropoffStops as $stop)
                                <div class="stop-card flex items-start gap-3 rounded-xl border border-gray-200 p-4 {{ old('dropoff_stop_id', request('dropoff_stop_id')) == $stop->id ? 'selected' : '' }}"
                                    data-stop-id="{{ $stop->id }}" data-type="dropoff">
                                    <div class="stop-radio"></div>
                                    <div class="flex-1 min-w-0">
                                        <span
                                            class="font-semibold text-gray-900 block truncate">{{ $stop->name }}</span>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $stop->address }}</p>
                                        <p class="text-xs text-primary-600 mt-1">{{ $stop->district_name }},
                                            {{ $stop->province_name }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- Step 3: Passenger Info --}}
                <section class="booking-panel p-5 space-y-5 md:p-6" id="section-step-3">
                    <div class="flex items-center justify-between">
                        <h2 class="flex items-center gap-2 font-display text-lg font-bold text-gray-900">
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm text-white">3</span>
                            {{ __('client.booking.create.passenger_info_title') }}
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="customer_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('client.booking.create.name_label') }}
                            </label>
                            <input type="text" id="customer_name" name="customer_name"
                                value="{{ old('customer_name', request('customer_name', $user->name ?? '')) }}"
                                class="w-full rounded-xl border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-primary-600 focus:ring-primary-100"
                                required placeholder="{{ __('client.booking.create.name_placeholder') }}">
                        </div>
                        <div>
                            <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('client.booking.create.phone_label') }}
                            </label>
                            <div class="phone-input-wrapper" id="phone-input-wrapper">
                                <button type="button" id="phone_country_btn" class="phone-country-btn" aria-haspopup="listbox" aria-expanded="false">
                                    <img id="phone_flag_img" src="https://flagcdn.com/w40/vn.png" alt="VN">
                                    <span class="arrow">&#9660;</span>
                                </button>
                                <div id="phone_dropdown" class="phone-dropdown">
                                    <div class="phone-dropdown-search">
                                        <input type="text" id="phone_search" placeholder="{{ __('client.booking.create.search_country_placeholder') }}" autocomplete="off">
                                    </div>
                                    <div class="phone-dropdown-list" id="phone_dropdown_list"></div>
                                </div>
                                <input type="text" id="phone_dial_code" class="phone-dial-code" value="+84" maxlength="5" placeholder="+84" autocomplete="off">
                                <input type="tel" id="phone_number"
                                    value=""
                                    class="phone-number-input"
                                    inputmode="tel"
                                    required
                                    placeholder="{{ __('client.booking.create.phone_placeholder') }}">
                            </div>
                            <input type="hidden" id="customer_phone_full" name="customer_phone" value="{{ old('customer_phone', request('customer_phone', $user->phone ?? '')) }}">
                            <p id="phone-error-msg" class="text-xs text-red-500 mt-1 hidden"></p>
                        </div>
                        <div>
                            <label for="customer_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('client.booking.create.email_label') }}
                            </label>
                            <input type="email" id="customer_email" name="customer_email"
                                value="{{ old('customer_email', request('customer_email', $user->email ?? '')) }}"
                                class="w-full rounded-xl border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-primary-600 focus:ring-primary-100"
                                required placeholder="{{ __('client.booking.create.email_placeholder') }}">
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('client.booking.create.notes_label') }}
                            </label>
                            <textarea id="notes" name="notes" rows="1"
                                class="w-full rounded-xl border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-primary-600 focus:ring-primary-100"
                                placeholder="{{ __('client.booking.create.notes_placeholder') }}">{{ old('notes', request('notes')) }}</textarea>
                        </div>
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">
                            {{ __('client.booking.create.payment_method_title') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($paymentMethods as $method)
                                <label
                                    class="payment-method-label block rounded-xl border border-gray-200 p-4 {{ old('payment_method', request('payment_method', 'cash_on_pickup')) === $method['key'] ? 'selected' : '' }}">
                                    <input type="radio" name="payment_method" value="{{ $method['key'] }}"
                                        class="hidden payment-method-input" @checked(old('payment_method', request('payment_method', 'cash_on_pickup')) === $method['key'])>
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="mt-0.5 w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center radio-icon">
                                            <div class="w-2.5 h-2.5 rounded-full bg-primary-600 hidden"></div>
                                        </div>
                                        <div class="flex-1">
                                            <span
                                                class="text-sm font-semibold text-gray-900">{{ $method['label'] }}</span>
                                            <p class="text-xs text-gray-600 mt-0.5">{{ $method['description'] }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- Submit Section --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-xs text-gray-500">
                        {!! __('client.booking.create.terms_agreement', ['link' => '#']) !!}
                    </div>
                    <button type="submit"
                        class="ksb-btn-primary px-6 py-3.5 text-sm disabled:cursor-not-allowed disabled:opacity-60"
                        id="submit-booking">
                        <span id="submit-text">{{ __('client.booking.create.submit_button') }}</span>
                        <i id="submit-spinner" class="fa-solid fa-spinner animate-spin hidden"></i>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </form>

            {{-- Sidebar Summary --}}
            <aside class="sticky-summary ksb-sticky-summary space-y-5">
                <div class="booking-sidebar-card p-5 space-y-4 md:p-6">
                    <h2 class="font-display text-lg font-bold text-gray-900">{{ __('client.booking.create.summary_title') }}</h2>

                    {{-- Selected Locations --}}
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-green-500 mt-0.5"></i>
                            <div>
                                <span
                                    class="text-gray-500 text-xs">{{ __('client.booking.create.pickup_label') }}</span>
                                <p class="font-medium text-gray-900" id="summary-pickup">
                                    {{ __('client.booking.create.not_selected') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-red-500 mt-0.5"></i>
                            <div>
                                <span
                                    class="text-gray-500 text-xs">{{ __('client.booking.create.dropoff_label') }}</span>
                                <p class="font-medium text-gray-900" id="summary-dropoff">
                                    {{ __('client.booking.create.not_selected') }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Price Summary --}}
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center justify-between">
                            <span>{{ __('client.booking.create.summary_base_price') }}</span>
                            <span class="font-semibold text-gray-900" id="summary-base-price">
                                {{ $baseUnitPrice > 0 ? number_format($baseUnitPrice) . 'đ' : __('client.booking.create.summary_contact_price') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('client.booking.create.summary_global_surcharge') }}</span>
                            <span class="font-semibold text-amber-600" id="summary-global-surcharge">
                                {{ $globalSurchargeUnit > 0 ? '+' . number_format($globalSurchargeUnit) . 'đ' : '0đ' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('client.booking.create.summary_route_surcharge') }}</span>
                            <span class="font-semibold text-amber-600" id="summary-route-surcharge">
                                {{ $routeSurchargeUnit > 0 ? '+' . number_format($routeSurchargeUnit) . 'đ' : '0đ' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('client.booking.create.summary_price_per_ticket') }}</span>
                            <span class="font-semibold text-gray-900" id="summary-unit-price">
                                {{ $finalUnitPrice > 0 ? number_format($finalUnitPrice) . 'đ' : __('client.booking.create.summary_contact_price') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('client.booking.create.summary_quantity') }}</span>
                            <span class="font-semibold text-gray-900" id="summary-quantity">1</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('client.booking.create.summary_service_fee') }}</span>
                            <span
                                class="text-green-600 font-semibold">{{ __('client.booking.create.summary_free') }}</span>
                        </div>
                        @if ($surchargeSnapshot)
                            <p class="text-xs text-amber-700 leading-relaxed pt-1">
                                {{ __('client.booking.create.summary_surcharge_note') }}: {{ $surchargeSnapshot }}
                            </p>
                        @endif
                    </div>

                    <div
                        class="mt-4 flex items-center justify-between border-t border-gray-200 pt-4 text-lg font-bold text-gray-900">
                        <span>{{ __('client.booking.create.summary_total') }}</span>
                        <span class="ksb-price text-primary-600" id="summary-total-price">{{ number_format($finalUnitPrice) }}đ</span>
                    </div>
                </div>

                {{-- Amenities --}}
                <div class="booking-sidebar-card p-5 space-y-3 text-sm text-slate-700 md:p-6 bg-[#fffdf5]">
                    @php
                        $amenityServices = collect($serviceDetails ?? [])
                            ->filter()
                            ->values();

                        if ($amenityServices->isEmpty()) {
                            $amenityServices = collect($services ?? [])
                                ->filter()
                                ->map(fn ($service) => [
                                    'name' => $service,
                                    'icon' => 'fa-solid fa-circle-check',
                                ])
                                ->values();
                        }
                    @endphp
                    <h3 class="text-sm font-semibold text-slate-800">{{ __('client.booking.create.amenities_title') }}
                    </h3>
                    <ul class="grid grid-cols-2 gap-x-3 gap-y-2">
                        @forelse ($amenityServices as $service)
                            @php
                                $serviceName = is_array($service) ? ($service['name'] ?? '') : $service;
                                $serviceIcon = is_array($service) ? ($service['icon'] ?? 'fa-solid fa-circle-check') : 'fa-solid fa-circle-check';
                            @endphp
                            <li class="flex items-center gap-2 text-xs">
                                <i class="{{ $serviceIcon }} text-primary-600"></i>
                                {{ $serviceName }}
                            </li>
                        @empty
                            <li class="flex items-center gap-2 text-xs"><i
                                    class="fa-solid fa-circle-check text-primary-600"></i>{{ __('client.booking.create.amenity_ac') }}
                            </li>
                            <li class="flex items-center gap-2 text-xs"><i
                                    class="fa-solid fa-circle-check text-primary-600"></i>{{ __('client.booking.create.amenity_blanket') }}
                            </li>
                            <li class="flex items-center gap-2 text-xs"><i
                                    class="fa-solid fa-circle-check text-primary-600"></i>{{ __('client.booking.create.amenity_water') }}
                            </li>
                            <li class="flex items-center gap-2 text-xs"><i
                                    class="fa-solid fa-circle-check text-primary-600"></i>{{ __('client.booking.create.amenity_wifi') }}
                            </li>
                        @endforelse
                    </ul>
                </div>

                {{-- Support --}}
                <div class="booking-sidebar-card p-5 space-y-3 md:p-6">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('client.booking.create.support_title') }}
                    </h3>
                    <div class="space-y-2">
                        @if (!empty($web_profile?->hotline))
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $web_profile->hotline) }}"
                                class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition text-sm">
                                <i class="fa-solid fa-phone text-primary-600"></i>
                                <span class="font-medium text-gray-800">{{ $web_profile->hotline }}</span>
                            </a>
                        @endif
                        @if (!empty($web_profile?->zalo_url))
                            <a href="{{ $web_profile->zalo_url }}" target="_blank"
                                class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition text-sm">
                                <i class="fa-solid fa-comment-dots text-primary-600"></i>
                                <span class="font-medium text-gray-800">Zalo</span>
                            </a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const bookingForm = document.getElementById('booking-form');
                const submitButton = document.getElementById('submit-booking');
                const submitText = document.getElementById('submit-text');
                const submitSpinner = document.getElementById('submit-spinner');
                const validationMessages = {
                    generalValidationError: @json(__('client.booking.create.general_validation_error')),
                    pickupRequired: @json(__('client.booking.create.frontend_pickup_required')),
                    dropoffRequired: @json(__('client.booking.create.frontend_dropoff_required')),
                    hotelAddressRequired: @json(__('client.booking.create.frontend_hotel_address_required')),
                    nameRequired: @json(__('client.booking.create.frontend_name_required')),
                    phoneRequired: @json(__('client.booking.create.frontend_phone_required')),
                    phoneInvalid: @json(__('client.booking.create.frontend_phone_invalid')),
                    emailRequired: @json(__('client.booking.create.frontend_email_required')),
                    emailInvalid: @json(__('client.booking.create.frontend_email_invalid')),
                };

                const pickupHiddenInput = document.getElementById('pickup_stop_id_hidden');
                const dropoffHiddenInput = document.getElementById('dropoff_stop_id_hidden');
                const hotelPickupInput = document.getElementById('hotel_pickup_address');
                const customerNameInput = document.getElementById('customer_name');
                const phoneNumberInput = document.getElementById('phone_number');
                const phoneCountryBtn = document.getElementById('phone_country_btn');
                const phoneFlagImg = document.getElementById('phone_flag_img');
                const phoneDropdown = document.getElementById('phone_dropdown');
                const phoneDropdownList = document.getElementById('phone_dropdown_list');
                const phoneSearchInput = document.getElementById('phone_search');
                const phoneDialCodeEl = document.getElementById('phone_dial_code');
                const customerPhoneFullInput = document.getElementById('customer_phone_full');
                const phoneErrorMsg = document.getElementById('phone-error-msg');
                const customerEmailInput = document.getElementById('customer_email');

                // Country data: [dial_code, iso2, name] — comprehensive list
                const countries = [
                    ['+84','vn','Việt Nam'],['+1','us','United States'],['+44','gb','United Kingdom'],
                    ['+82','kr','South Korea'],['+81','jp','Japan'],['+86','cn','China'],
                    ['+66','th','Thailand'],['+856','la','Laos'],['+855','kh','Cambodia'],
                    ['+61','au','Australia'],['+33','fr','France'],['+49','de','Germany'],
                    ['+91','in','India'],['+65','sg','Singapore'],['+60','my','Malaysia'],
                    ['+63','ph','Philippines'],['+62','id','Indonesia'],['+886','tw','Taiwan'],
                    ['+852','hk','Hong Kong'],['+853','mo','Macau'],['+7','ru','Russia'],
                    ['+39','it','Italy'],['+34','es','Spain'],['+31','nl','Netherlands'],
                    ['+46','se','Sweden'],['+41','ch','Switzerland'],['+64','nz','New Zealand'],
                    ['+353','ie','Ireland'],['+48','pl','Poland'],['+380','ua','Ukraine'],
                    ['+90','tr','Türkiye'],['+966','sa','Saudi Arabia'],['+971','ae','UAE'],
                    ['+972','il','Israel'],['+55','br','Brazil'],['+52','mx','Mexico'],
                    ['+54','ar','Argentina'],['+57','co','Colombia'],['+234','ng','Nigeria'],
                    ['+27','za','South Africa'],['+20','eg','Egypt'],
                    // Europe
                    ['+43','at','Austria'],['+32','be','Belgium'],['+359','bg','Bulgaria'],
                    ['+385','hr','Croatia'],['+357','cy','Cyprus'],['+420','cz','Czech Republic'],
                    ['+45','dk','Denmark'],['+372','ee','Estonia'],['+358','fi','Finland'],
                    ['+30','gr','Greece'],['+36','hu','Hungary'],['+354','is','Iceland'],
                    ['+371','lv','Latvia'],['+370','lt','Lithuania'],['+352','lu','Luxembourg'],
                    ['+356','mt','Malta'],['+373','md','Moldova'],['+382','me','Montenegro'],
                    ['+389','mk','North Macedonia'],['+47','no','Norway'],['+351','pt','Portugal'],
                    ['+40','ro','Romania'],['+381','rs','Serbia'],['+421','sk','Slovakia'],
                    ['+386','si','Slovenia'],['+375','by','Belarus'],['+995','ge','Georgia'],
                    ['+374','am','Armenia'],['+994','az','Azerbaijan'],
                    // Asia & Oceania
                    ['+93','af','Afghanistan'],['+880','bd','Bangladesh'],['+975','bt','Bhutan'],
                    ['+673','bn','Brunei'],['+95','mm','Myanmar'],['+670','tl','Timor-Leste'],
                    ['+679','fj','Fiji'],['+992','tj','Tajikistan'],['+993','tm','Turkmenistan'],
                    ['+998','uz','Uzbekistan'],['+996','kg','Kyrgyzstan'],['+976','mn','Mongolia'],
                    ['+977','np','Nepal'],['+92','pk','Pakistan'],['+94','lk','Sri Lanka'],
                    ['+960','mv','Maldives'],['+675','pg','Papua New Guinea'],
                    ['+685','ws','Samoa'],['+676','to','Tonga'],['+678','vu','Vanuatu'],
                    ['+682','ck','Cook Islands'],['+691','fm','Micronesia'],
                    ['+680','pw','Palau'],['+677','sb','Solomon Islands'],
                    ['+688','tv','Tuvalu'],['+674','nr','Nauru'],['+686','ki','Kiribati'],
                    ['+692','mh','Marshall Islands'],
                    // Middle East
                    ['+973','bh','Bahrain'],['+964','iq','Iraq'],['+962','jo','Jordan'],
                    ['+965','kw','Kuwait'],['+961','lb','Lebanon'],['+968','om','Oman'],
                    ['+974','qa','Qatar'],['+963','sy','Syria'],['+967','ye','Yemen'],
                    ['+970','ps','Palestine'],
                    // Africa
                    ['+213','dz','Algeria'],['+244','ao','Angola'],['+229','bj','Benin'],
                    ['+267','bw','Botswana'],['+226','bf','Burkina Faso'],['+257','bi','Burundi'],
                    ['+237','cm','Cameroon'],['+238','cv','Cape Verde'],['+236','cf','Central African Republic'],
                    ['+235','td','Chad'],['+269','km','Comoros'],['+242','cg','Congo'],
                    ['+243','cd','DR Congo'],['+253','dj','Djibouti'],
                    ['+240','gq','Equatorial Guinea'],['+291','er','Eritrea'],
                    ['+268','sz','Eswatini'],['+251','et','Ethiopia'],['+241','ga','Gabon'],
                    ['+220','gm','Gambia'],['+233','gh','Ghana'],['+224','gn','Guinea'],
                    ['+245','gw','Guinea-Bissau'],['+225','ci','Ivory Coast'],
                    ['+254','ke','Kenya'],['+266','ls','Lesotho'],['+231','lr','Liberia'],
                    ['+218','ly','Libya'],['+261','mg','Madagascar'],['+265','mw','Malawi'],
                    ['+223','ml','Mali'],['+222','mr','Mauritania'],['+230','mu','Mauritius'],
                    ['+212','ma','Morocco'],['+258','mz','Mozambique'],['+264','na','Namibia'],
                    ['+227','ne','Niger'],['+250','rw','Rwanda'],
                    ['+239','st','São Tomé and Príncipe'],['+221','sn','Senegal'],
                    ['+248','sc','Seychelles'],['+232','sl','Sierra Leone'],
                    ['+252','so','Somalia'],['+211','ss','South Sudan'],['+249','sd','Sudan'],
                    ['+255','tz','Tanzania'],['+228','tg','Togo'],['+216','tn','Tunisia'],
                    ['+256','ug','Uganda'],['+260','zm','Zambia'],['+263','zw','Zimbabwe'],
                    // Americas
                    ['+591','bo','Bolivia'],['+56','cl','Chile'],['+593','ec','Ecuador'],
                    ['+595','py','Paraguay'],['+51','pe','Peru'],['+598','uy','Uruguay'],
                    ['+58','ve','Venezuela'],['+592','gy','Guyana'],['+597','sr','Suriname'],
                    ['+501','bz','Belize'],['+506','cr','Costa Rica'],['+53','cu','Cuba'],
                    ['+1','do','Dominican Republic'],['+503','sv','El Salvador'],
                    ['+502','gt','Guatemala'],['+509','ht','Haiti'],['+504','hn','Honduras'],
                    ['+876','jm','Jamaica'],['+505','ni','Nicaragua'],['+507','pa','Panama'],
                    ['+868','tt','Trinidad and Tobago'],['+1','ca','Canada'],
                    ['+297','aw','Aruba'],['+1','bs','Bahamas'],['+1','bb','Barbados'],
                ];

                let selectedDialCode = '+84';
                let selectedIso2 = 'vn';

                // Render dropdown items
                const renderDropdownItems = (filter = '') => {
                    phoneDropdownList.innerHTML = '';
                    const lowerFilter = filter.toLowerCase();
                    const filtered = countries.filter(([dialCode, iso2, name]) =>
                        !filter || name.toLowerCase().includes(lowerFilter) || dialCode.includes(filter) || iso2.includes(lowerFilter)
                    );
                    filtered.forEach(([dialCode, iso2, name]) => {
                        const item = document.createElement('div');
                        item.className = 'phone-dropdown-item' + (dialCode === selectedDialCode ? ' selected' : '');
                        item.dataset.dialCode = dialCode;
                        item.dataset.iso2 = iso2;
                        item.innerHTML = `<img src="https://flagcdn.com/w40/${iso2}.png" alt="${iso2}" loading="lazy"><span class="country-name">${name}</span><span class="dial-code">${dialCode}</span>`;
                        item.addEventListener('click', () => selectCountry(dialCode, iso2));
                        phoneDropdownList.appendChild(item);
                    });
                };

                const selectCountry = (dialCode, iso2, focusNumber = true) => {
                    selectedDialCode = dialCode;
                    selectedIso2 = iso2;
                    phoneFlagImg.src = `https://flagcdn.com/w40/${iso2}.png`;
                    phoneFlagImg.alt = iso2.toUpperCase();
                    phoneDialCodeEl.value = dialCode;
                    closeDropdown();
                    syncFullPhone();
                    if (focusNumber) phoneNumberInput.focus();
                };

                const openDropdown = () => {
                    phoneDropdown.classList.add('open');
                    phoneCountryBtn.setAttribute('aria-expanded', 'true');
                    phoneSearchInput.value = '';
                    renderDropdownItems();
                    setTimeout(() => phoneSearchInput.focus(), 50);
                };

                const closeDropdown = () => {
                    phoneDropdown.classList.remove('open');
                    phoneCountryBtn.setAttribute('aria-expanded', 'false');
                };

                phoneCountryBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    phoneDropdown.classList.contains('open') ? closeDropdown() : openDropdown();
                });

                phoneSearchInput.addEventListener('input', () => {
                    renderDropdownItems(phoneSearchInput.value);
                });

                // Close on click outside
                document.addEventListener('click', (e) => {
                    if (!document.getElementById('phone-input-wrapper').contains(e.target)) {
                        closeDropdown();
                    }
                });

                // Sync full phone to hidden input
                const syncFullPhone = () => {
                    const number = phoneNumberInput.value.replace(/[^\d]/g, '');
                    const dialCode = phoneDialCodeEl.value.startsWith('+') ? phoneDialCodeEl.value : '+' + phoneDialCodeEl.value;
                    customerPhoneFullInput.value = number ? dialCode + number : '';
                };

                // Editable dial code: auto-detect country when user types
                phoneDialCodeEl.addEventListener('input', () => {
                    let val = phoneDialCodeEl.value.replace(/[^\d+]/g, '');
                    if (!val.startsWith('+')) val = '+' + val;
                    phoneDialCodeEl.value = val;
                    selectedDialCode = val;

                    // Match longest dial code first
                    const sortedCountries = [...countries].sort((a, b) => b[0].length - a[0].length);
                    for (const [dialCode, iso2] of sortedCountries) {
                        if (val === dialCode) {
                            selectedIso2 = iso2;
                            phoneFlagImg.src = `https://flagcdn.com/w40/${iso2}.png`;
                            phoneFlagImg.alt = iso2.toUpperCase();
                            break;
                        }
                    }
                    syncFullPhone();
                });

                phoneDialCodeEl.addEventListener('keypress', (e) => {
                    const char = String.fromCharCode(e.which || e.keyCode);
                    if (/[a-zA-Z]/.test(char)) e.preventDefault();
                });

                phoneDialCodeEl.addEventListener('blur', () => {
                    if (!phoneDialCodeEl.value || phoneDialCodeEl.value === '+') {
                        phoneDialCodeEl.value = '+84';
                        selectedDialCode = '+84';
                        selectedIso2 = 'vn';
                        phoneFlagImg.src = 'https://flagcdn.com/w40/vn.png';
                        phoneFlagImg.alt = 'VN';
                    }
                    syncFullPhone();
                });

                // Block letters
                phoneNumberInput.addEventListener('keypress', (e) => {
                    const char = String.fromCharCode(e.which || e.keyCode);
                    if (/[a-zA-Z]/.test(char)) {
                        e.preventDefault();
                    }
                });

                // On input: strip letters, detect +XX auto-switch, sync
                phoneNumberInput.addEventListener('input', () => {
                    let val = phoneNumberInput.value.replace(/[a-zA-Z]/g, '');

                    // Auto-detect country code from +XX
                    if (val.startsWith('+')) {
                        const sortedCountries = [...countries].sort((a, b) => b[0].length - a[0].length);
                        for (const [dialCode, iso2] of sortedCountries) {
                            if (val.startsWith(dialCode)) {
                                selectCountry(dialCode, iso2);
                                val = val.substring(dialCode.length);
                                phoneNumberInput.value = val;
                                return;
                            }
                        }
                        if (val.startsWith('+')) {
                            val = val.substring(1);
                            phoneNumberInput.value = val;
                        }
                    }

                    syncFullPhone();
                    phoneErrorMsg.classList.add('hidden');
                });

                phoneNumberInput.addEventListener('blur', syncFullPhone);

                // Pre-fill from old value
                const oldPhone = @json(old('customer_phone', request('customer_phone', $user->phone ?? '')));
                if (oldPhone) {
                    if (oldPhone.startsWith('+')) {
                        const sortedCountries = [...countries].sort((a, b) => b[0].length - a[0].length);
                        let matched = false;
                        for (const [dialCode, iso2] of sortedCountries) {
                            if (oldPhone.startsWith(dialCode)) {
                                selectCountry(dialCode, iso2);
                                phoneNumberInput.value = oldPhone.substring(dialCode.length);
                                matched = true;
                                break;
                            }
                        }
                        if (!matched) {
                            phoneNumberInput.value = oldPhone.substring(1);
                        }
                    } else {
                        phoneNumberInput.value = oldPhone;
                    }
                    syncFullPhone();
                }

                const resetSubmitButtonState = () => {
                    submitButton.disabled = false;
                    submitText.classList.remove('hidden');
                    submitSpinner.classList.add('hidden');
                };

                const showValidationError = (message) => {
                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        window.Swal.fire({
                            icon: 'warning',
                            title: validationMessages.generalValidationError,
                            text: message,
                            confirmButtonText: 'OK',
                        });
                        return;
                    }

                    if (window.toastr && typeof window.toastr.warning === 'function') {
                        window.toastr.warning(message);
                        return;
                    }

                    alert(message);
                };

                const getFrontendValidationError = () => {
                    const pickupValue = (pickupHiddenInput?.value || '').trim();
                    const dropoffValue = (dropoffHiddenInput?.value || '').trim();
                    const customerName = (customerNameInput?.value || '').trim();
                    const customerPhone = (phoneNumberInput?.value || '').trim();
                    const customerEmail = (customerEmailInput?.value || '').trim();
                    const hotelAddress = (hotelPickupInput?.value || '').trim();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (!pickupValue) {
                        return validationMessages.pickupRequired;
                    }

                    if (!dropoffValue) {
                        return validationMessages.dropoffRequired;
                    }

                    if (pickupValue === 'hotel_pickup' && !hotelAddress) {
                        return validationMessages.hotelAddressRequired;
                    }

                    if (!customerName) {
                        return validationMessages.nameRequired;
                    }

                    if (!customerPhone) {
                        return validationMessages.phoneRequired;
                    }

                    // Sync final full number before submit
                    syncFullPhone();

                    if (!customerEmail) {
                        return validationMessages.emailRequired;
                    }

                    if (!emailRegex.test(customerEmail)) {
                        return validationMessages.emailInvalid;
                    }

                    return null;
                };

                let isSubmitting = false;

                bookingForm.addEventListener('submit', function(e) {
                    if (isSubmitting) {
                        return;
                    }

                    e.preventDefault();
                    resetSubmitButtonState();

                    const frontendError = getFrontendValidationError();

                    if (frontendError) {
                        showValidationError(frontendError);
                        return;
                    }

                    if (!bookingForm.checkValidity()) {
                        bookingForm.reportValidity();
                        showValidationError(validationMessages.generalValidationError);
                        return;
                    }

                    if (document.getElementById('quantity').value <= 0) {
                        showValidationError(validationMessages.generalValidationError);
                        return;
                    }

                    isSubmitting = true;
                    submitButton.disabled = true;
                    submitText.classList.add('hidden');
                    submitSpinner.classList.remove('hidden');
                    bookingForm.submit();
                });

                // Quantity controls
                const quantityInput = document.getElementById('quantity');
                const decreaseBtn = document.getElementById('decrease-quantity');
                const increaseBtn = document.getElementById('increase-quantity');
                const maxQuantity = {{ $availableSeats }};
                const finalUnitPrice = {{ $finalUnitPrice }};
                const summaryQuantity = document.getElementById('summary-quantity');
                const summaryTotalPrice = document.getElementById('summary-total-price');
                const totalPriceInput = document.getElementById('total-price-input');

                const updateSummary = () => {
                    const quantity = parseInt(quantityInput.value);
                    const totalPrice = quantity * finalUnitPrice;
                    summaryQuantity.textContent = quantity;
                    summaryTotalPrice.textContent = totalPrice > 0 ? totalPrice.toLocaleString('vi-VN') + 'đ' :
                        '0đ';
                    totalPriceInput.value = totalPrice;
                    submitButton.disabled = quantity === 0;
                };

                decreaseBtn.addEventListener('click', () => {
                    let currentVal = parseInt(quantityInput.value);
                    if (currentVal > 1) {
                        quantityInput.value = currentVal - 1;
                        updateSummary();
                    }
                });

                increaseBtn.addEventListener('click', () => {
                    let currentVal = parseInt(quantityInput.value);
                    if (currentVal < maxQuantity) {
                        quantityInput.value = currentVal + 1;
                        updateSummary();
                    }
                });

                // Date picker
                const picker = new Litepicker({
                    element: document.getElementById('booking_date'),
                    format: 'DD/MM/YYYY',
                    minDate: new Date(),
                    singleMode: true,
                    setup: (picker) => {
                        picker.on('selected', (date) => {
                            const selectedDate = new Date(date.dateInstance).toLocaleDateString(
                                'fr-CA');
                            const url = new URL(window.location.href);
                            url.searchParams.set('date', selectedDate);
                            window.location.href = url.toString();
                        });
                    },
                });

                // Stop card selection
                const hotelPickupWrapper = document.getElementById('hotel-pickup-address-wrapper');
                const summaryPickup = document.getElementById('summary-pickup');
                const summaryDropoff = document.getElementById('summary-dropoff');

                function selectStop(card, type) {
                    const container = type === 'pickup' ? document.getElementById('pickup-stops-container') : document
                        .getElementById('dropoff-stops-container');
                    const hiddenInput = type === 'pickup' ? pickupHiddenInput : dropoffHiddenInput;
                    const summaryEl = type === 'pickup' ? summaryPickup : summaryDropoff;

                    // Deselect all in container
                    container.querySelectorAll('.stop-card').forEach(c => c.classList.remove('selected'));

                    // Select this card
                    card.classList.add('selected');
                    const stopId = card.dataset.stopId;
                    hiddenInput.value = stopId;

                    // Update summary
                    const stopName = card.querySelector('.font-semibold').textContent;
                    summaryEl.textContent = stopName;

                    // Handle hotel pickup
                    if (type === 'pickup') {
                        if (stopId === 'hotel_pickup') {
                            hotelPickupWrapper.classList.remove('hidden');
                            hotelPickupInput.required = true;
                        } else {
                            hotelPickupWrapper.classList.add('hidden');
                            hotelPickupInput.required = false;
                        }
                    }

                    updateStepProgress();
                }

                // Attach click events to stop cards
                document.querySelectorAll('.stop-card[data-type="pickup"]').forEach(card => {
                    card.addEventListener('click', () => selectStop(card, 'pickup'));
                });
                document.querySelectorAll('.stop-card[data-type="dropoff"]').forEach(card => {
                    card.addEventListener('click', () => selectStop(card, 'dropoff'));
                });

                // Initialize selected cards
                const initialPickup = pickupHiddenInput.value;
                const initialDropoff = dropoffHiddenInput.value;
                if (initialPickup) {
                    const card = document.querySelector(
                        `.stop-card[data-stop-id="${initialPickup}"][data-type="pickup"]`);
                    if (card) selectStop(card, 'pickup');
                }
                if (initialDropoff) {
                    const card = document.querySelector(
                        `.stop-card[data-stop-id="${initialDropoff}"][data-type="dropoff"]`);
                    if (card) selectStop(card, 'dropoff');
                }

                // Payment method selection
                const paymentLabels = document.querySelectorAll('.payment-method-label');
                const updatePaymentSelection = () => {
                    paymentLabels.forEach(label => {
                        const input = label.querySelector('.payment-method-input');
                        const radioIcon = label.querySelector('.radio-icon');
                        const innerDot = radioIcon.querySelector('div');
                        if (input.checked) {
                            label.classList.add('selected');
                            radioIcon.classList.add('border-primary-600');
                            innerDot.classList.remove('hidden');
                        } else {
                            label.classList.remove('selected');
                            radioIcon.classList.remove('border-primary-600');
                            innerDot.classList.add('hidden');
                        }
                    });
                };
                paymentLabels.forEach(label => {
                    label.addEventListener('click', () => {
                        label.querySelector('.payment-method-input').checked = true;
                        updatePaymentSelection();
                    });
                });

                // Step progress update
                function updateStepProgress() {
                    const hasPickup = pickupHiddenInput.value !== '';
                    const hasDropoff = dropoffHiddenInput.value !== '';
                    const step2Circle = document.getElementById('step-2-circle');
                    const step2Label = document.getElementById('step-2-label');

                    if (hasPickup && hasDropoff) {
                        step2Circle.classList.remove('pending');
                        step2Circle.classList.add('completed');
                        step2Circle.innerHTML = '<i class="fa-solid fa-check text-xs"></i>';
                        step2Label.classList.add('active');
                    }
                }

                updateSummary();
                updatePaymentSelection();
                updateStepProgress();
            });
        </script>
    @endpush
</x-client.layout>
