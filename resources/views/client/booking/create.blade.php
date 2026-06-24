<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.booking.create.meta_title')" :description="$description ?? ''" body-class="bg-[#F8FAFC]">
    @push('styles')
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

            /* Phone input with searchable country dropdown */
            .phone-input-wrapper {
                display: flex;
                align-items: stretch;
                border: 1px solid #e5e7eb;
                border-radius: 0.75rem;
                background-color: #f8fafc;
                overflow: visible;
                transition: border-color 0.15s ease, box-shadow 0.15s ease;
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
            .phone-country-btn:hover { background: #f1f5f9; }
            .phone-country-btn img {
                width: 24px;
                height: 18px;
                object-fit: cover;
                border-radius: 2px;
                box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
            }
            .phone-country-btn .arrow { font-size: 0.55rem; color: #6b7280; margin-left: 2px; }
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
            .phone-dial-code::placeholder { color: #9ca3af; }
            .phone-number-input {
                flex: 1;
                border: none;
                background: transparent;
                padding: 0.75rem;
                font-size: 1rem;
                outline: none;
                min-width: 0;
            }
            .phone-number-input::placeholder { color: #9ca3af; }
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
                max-width: calc(100vw - 3rem);
                max-height: 320px;
                overflow: hidden;
                flex-direction: column;
            }
            .phone-dropdown.open { display: flex; }
            .phone-dropdown-search { padding: 8px 10px; border-bottom: 1px solid #e5e7eb; }
            .phone-dropdown-search input {
                width: 100%;
                border: 1px solid #d4d4d4;
                border-radius: 0.375rem;
                padding: 6px 10px;
                font-size: 0.875rem;
                outline: none;
            }
            .phone-dropdown-search input:focus { border-color: #FF9B00; }
            .phone-dropdown-list { overflow-y: auto; flex: 1; }
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
            .phone-dropdown-item.highlighted { background: #fff7d6; }
            .phone-dropdown-item.selected { background: #ffefc0; font-weight: 600; }
            .phone-dropdown-item img {
                width: 24px;
                height: 18px;
                object-fit: cover;
                border-radius: 2px;
                box-shadow: 0 0 0 1px rgba(0,0,0,0.08);
                flex-shrink: 0;
            }
            .phone-dropdown-item .country-name { flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .phone-dropdown-item .dial-code { color: #6b7280; font-size: 0.8125rem; flex-shrink: 0; }

            .quantity-btn { transition: all 0.2s ease-in-out; }
            .quantity-btn:hover { opacity: 0.9; }

            /* Stop & payment cards (label-wrapped real radios) */
            .stop-card,
            .payment-method-label {
                position: relative;
                cursor: pointer;
                transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease;
            }
            .stop-card:hover { border-color: #FF9B00; background-color: #fffdf4; }
            .stop-card.selected,
            .payment-method-label.selected {
                border-color: #FF9B00;
                background-color: #fff8e6;
                box-shadow: var(--ksb-focus);
            }
            .stop-card .stop-radio {
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
            .stop-card.selected .stop-radio { border-color: #FF9B00; background-color: #FF9B00; }
            .stop-card.selected .stop-radio::after {
                content: '';
                display: block;
                width: 8px;
                height: 8px;
                background: #fff;
                border-radius: 50%;
            }
            .stop-card input:focus-visible + .stop-radio,
            .payment-method-label input:focus-visible ~ * .radio-icon {
                box-shadow: var(--ksb-focus);
                border-radius: 50%;
            }
            /* Field error state */
            .field-error {
                border-color: #ef4444 !important;
                box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.15) !important;
            }

            /* Step progress */
            .step-progress { display: flex; justify-content: space-between; position: relative; }
            .step-progress::before {
                content: '';
                position: absolute;
                top: 16px;
                left: 40px;
                right: 40px;
                height: 2px;
                background: #e5e7eb;
            }
            .step-item { display: flex; flex-direction: column; align-items: center; gap: 0.5rem; position: relative; z-index: 1; }
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
            .step-circle.active { background: #FF9B00; color: #fff; }
            .step-circle.completed { background: #22c55e; color: #fff; }
            .step-circle.pending { background: #e5e7eb; color: #9ca3af; }
            .step-label { font-size: 0.75rem; font-weight: 500; color: #6b7280; }
            .step-label.active { color: #b86100; font-weight: 600; }

            @media (min-width: 1280px) {
                .sticky-summary { position: sticky; top: 112px; }
            }

            .hotel-pickup-badge { background: #fef3c7; border: 1px solid #f59e0b; }

            /* Mobile price sheet */
            #price-sheet { transition: transform .3s ease; }
            #price-sheet.open { transform: translateY(0); }
            #price-sheet-backdrop { transition: opacity .3s ease; }
            #price-sheet-backdrop.open { opacity: 1; pointer-events: auto; }

            @media (prefers-reduced-motion: reduce) {
                #price-sheet, #price-sheet-backdrop, .stop-card, .payment-method-label, .step-circle { transition: none !important; }
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
        $totalStopCount = $pickupStops->count() + $dropoffStops->count();
        $selectedPickup = old('pickup_stop_id', request('pickup_stop_id'));
        $selectedDropoff = old('dropoff_stop_id', request('dropoff_stop_id'));
        $selectedPayment = old('payment_method', request('payment_method', 'cash_on_pickup'));
        $summaryData = [
            'baseUnitPrice' => $baseUnitPrice,
            'globalSurchargeUnit' => $globalSurchargeUnit,
            'routeSurchargeUnit' => $routeSurchargeUnit,
            'finalUnitPrice' => $finalUnitPrice,
            'surchargeSnapshot' => $surchargeSnapshot,
        ];
    @endphp

    {{-- Hero --}}
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
                    <img src="{{ $busImage }}" alt="{{ $trip->bus_name }}" class="h-full w-full object-cover" loading="lazy">
                    <span class="absolute bottom-3 left-3 inline-flex items-center gap-2 rounded-xl bg-white/95 px-3 py-1.5 text-xs font-semibold text-slate-800">
                        <i class="fa-solid fa-shield-heart text-primary-600"></i>
                        {{ __('client.booking.create.insurance_badge') }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Step progress --}}
    <section class="ksb-section-compact border-b border-amber-100 bg-white px-4 py-4">
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

    {{-- Main --}}
    <section class="ksb-section ksb-section-band px-4 pb-28 xl:pb-16">
        <div class="container mx-auto grid max-w-7xl grid-cols-1 gap-8 xl:grid-cols-[minmax(0,1fr)_380px]">
            <form class="space-y-6" method="POST" action="{{ route('client.booking.store') }}" id="booking-form" novalidate>
                @if (session('error'))
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700" role="alert">
                        <strong class="font-bold">{{ __('client.booking.create.error_title') }}</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700" role="alert">
                        <strong class="font-bold">{{ __('client.booking.create.validation_error_title') }}</strong>
                        <p class="mt-2">{{ __('client.booking.create.general_validation_error') }}</p>
                    </div>
                @endif

                @csrf
                <input type="hidden" name="trip_id" value="{{ $trip->trip_id }}">
                <input type="hidden" name="total_price" id="total-price-input" value="0">

                {{-- Step 1: Trip --}}
                <section class="booking-panel space-y-5 p-5 md:p-6" id="section-step-1">
                    <h2 class="flex items-center gap-2 font-display text-lg font-bold text-gray-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm text-white">1</span>
                        {{ __('client.booking.create.trip_info_title') }}
                    </h2>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="booking_date" class="mb-2 block text-sm font-semibold text-gray-700">
                                {{ __('client.booking.create.departure_date_label') }}
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-calendar-day pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" id="booking_date" name="booking_date"
                                    value="{{ $bookingDate->format('d/m/Y') }}" readonly
                                    class="w-full rounded-xl border-neutral-200 bg-neutral-50 p-3 pl-10 text-base shadow-sm focus:border-primary-600 focus:bg-white focus:ring-primary-100"
                                    required>
                            </div>
                        </div>
                        <div>
                            <label for="quantity" class="mb-2 block text-sm font-semibold text-gray-700">
                                {{ __('client.booking.create.quantity_label') }}
                            </label>
                            <div class="flex items-center gap-2">
                                <div class="inline-flex items-center rounded-xl border border-neutral-200 bg-neutral-50 p-1">
                                    <button type="button" id="decrease-quantity" aria-label="-"
                                        class="quantity-btn flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-600 shadow-sm hover:border-gray-300 hover:text-gray-900">
                                        <i class="fa-solid fa-minus text-sm"></i>
                                    </button>
                                    <div class="flex w-16 flex-col items-center justify-center">
                                        <input type="number" name="quantity" id="quantity"
                                            value="{{ old('quantity', request('quantity', 1)) }}" min="1"
                                            max="{{ $availableSeats }}"
                                            class="w-full border-0 bg-transparent p-0 text-center text-xl font-bold text-gray-900 focus:ring-0"
                                            readonly>
                                        <span class="-mt-1 text-[10px] uppercase tracking-wide text-gray-400">{{ __('client.booking.create.tickets_unit') }}</span>
                                    </div>
                                    <button type="button" id="increase-quantity" aria-label="+"
                                        class="quantity-btn flex h-10 w-10 items-center justify-center rounded-lg bg-primary-600 text-white shadow-sm hover:bg-primary-700">
                                        <i class="fa-solid fa-plus text-sm"></i>
                                    </button>
                                </div>
                                <div class="inline-flex items-center gap-1.5 rounded-lg border border-green-100 bg-green-50 px-3 py-2">
                                    <i class="fa-solid fa-couch text-xs text-green-500"></i>
                                    <span class="text-xs font-medium text-green-700">
                                        {!! trans_choice('client.booking.create.seats_left', $availableSeats, ['count' => $availableSeats]) !!}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Step 2: Pickup & Dropoff --}}
                <section class="booking-panel space-y-5 p-5 md:p-6" id="section-step-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="flex items-center gap-2 font-display text-lg font-bold text-gray-900">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm text-white">2</span>
                            {{ __('client.booking.create.location_title') }}
                        </h2>
                        @if ($totalStopCount > 6)
                            <div class="relative w-full sm:w-64">
                                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                <input type="text" id="stop-search" autocomplete="off"
                                    placeholder="{{ __('client.booking.create.search_stop_placeholder') }}"
                                    class="w-full rounded-lg border-neutral-200 bg-neutral-50 py-2 pl-9 pr-3 text-sm focus:border-primary-600 focus:bg-white focus:ring-primary-100">
                            </div>
                        @endif
                    </div>

                    {{-- Pickup --}}
                    <fieldset>
                        <legend class="mb-3 block text-sm font-semibold text-gray-700">
                            <i class="fa-solid fa-location-dot mr-1 text-green-500"></i>
                            {{ __('client.booking.create.pickup_label') }}
                        </legend>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2" id="pickup-stops-container">
                            @if ($trip->available_hotel_pickup)
                                <label class="stop-card hotel-pickup-badge flex items-start gap-3 rounded-xl p-4 {{ $selectedPickup == 'hotel_pickup' ? 'selected' : '' }}"
                                    data-stop-name="{{ __('client.booking.create.pickup_at_hotel') }}">
                                    <input type="radio" name="pickup_stop_id" value="hotel_pickup" class="sr-only stop-input"
                                        data-type="pickup" @checked($selectedPickup == 'hotel_pickup')>
                                    <span class="stop-radio" aria-hidden="true"></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block font-semibold text-gray-900">{{ __('client.booking.create.pickup_at_hotel') }}</span>
                                        <span class="mt-1 flex items-center gap-1 text-xs text-amber-700">
                                            <i class="fa-solid fa-circle-info"></i>
                                            {{ __('client.booking.create.hotel_pickup_hanoi_only') }}
                                        </span>
                                    </span>
                                </label>
                            @endif
                            @foreach ($pickupStops as $stop)
                                <label class="stop-card flex items-start gap-3 rounded-xl border border-gray-200 p-4 {{ $selectedPickup == $stop->id ? 'selected' : '' }}"
                                    data-stop-name="{{ $stop->name }}"
                                    data-search="{{ \Illuminate\Support\Str::lower($stop->name . ' ' . $stop->address . ' ' . $stop->district_name . ' ' . $stop->province_name) }}">
                                    <input type="radio" name="pickup_stop_id" value="{{ $stop->id }}" class="sr-only stop-input"
                                        data-type="pickup" @checked($selectedPickup == $stop->id)>
                                    <span class="stop-radio" aria-hidden="true"></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate font-semibold text-gray-900">{{ $stop->name }}</span>
                                        <span class="mt-1 line-clamp-2 block text-xs text-gray-500">{{ $stop->address }}</span>
                                        <span class="mt-1 block text-xs text-primary-600">{{ $stop->district_name }}, {{ $stop->province_name }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>

                    {{-- Hotel pickup address --}}
                    <div id="hotel-pickup-address-wrapper" class="hidden">
                        <label for="hotel_pickup_address" class="mb-2 block text-sm font-semibold text-gray-700">
                            {{ __('client.booking.create.hotel_address_label') }}
                        </label>
                        <input type="text" id="hotel_pickup_address" name="hotel_pickup_address"
                            value="{{ old('hotel_pickup_address', request('hotel_pickup_address')) }}"
                            class="w-full rounded-xl border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:border-primary-600 focus:bg-white focus:ring-primary-100"
                            placeholder="{{ __('client.booking.create.hotel_address_placeholder') }}">
                    </div>

                    {{-- Dropoff --}}
                    <fieldset>
                        <legend class="mb-3 block text-sm font-semibold text-gray-700">
                            <i class="fa-solid fa-location-dot mr-1 text-red-500"></i>
                            {{ __('client.booking.create.dropoff_label') }}
                        </legend>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2" id="dropoff-stops-container">
                            @foreach ($dropoffStops as $stop)
                                <label class="stop-card flex items-start gap-3 rounded-xl border border-gray-200 p-4 {{ $selectedDropoff == $stop->id ? 'selected' : '' }}"
                                    data-stop-name="{{ $stop->name }}"
                                    data-search="{{ \Illuminate\Support\Str::lower($stop->name . ' ' . $stop->address . ' ' . $stop->district_name . ' ' . $stop->province_name) }}">
                                    <input type="radio" name="dropoff_stop_id" value="{{ $stop->id }}" class="sr-only stop-input"
                                        data-type="dropoff" @checked($selectedDropoff == $stop->id)>
                                    <span class="stop-radio" aria-hidden="true"></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate font-semibold text-gray-900">{{ $stop->name }}</span>
                                        <span class="mt-1 line-clamp-2 block text-xs text-gray-500">{{ $stop->address }}</span>
                                        <span class="mt-1 block text-xs text-primary-600">{{ $stop->district_name }}, {{ $stop->province_name }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </fieldset>
                    <p id="stop-no-result" class="hidden text-sm text-gray-500">{{ __('client.booking.create.no_stop_found') }}</p>
                </section>

                {{-- Step 3: Passenger --}}
                <section class="booking-panel space-y-5 p-5 md:p-6" id="section-step-3">
                    <h2 class="flex items-center gap-2 font-display text-lg font-bold text-gray-900">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm text-white">3</span>
                        {{ __('client.booking.create.passenger_info_title') }}
                    </h2>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="customer_name" class="mb-2 block text-sm font-semibold text-gray-700">
                                {{ __('client.booking.create.name_label') }}
                            </label>
                            <input type="text" id="customer_name" name="customer_name"
                                value="{{ old('customer_name', request('customer_name', $user->name ?? '')) }}"
                                class="w-full rounded-xl border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:border-primary-600 focus:bg-white focus:ring-primary-100"
                                required placeholder="{{ __('client.booking.create.name_placeholder') }}">
                        </div>
                        <div>
                            <label for="phone_number" class="mb-2 block text-sm font-semibold text-gray-700">
                                {{ __('client.booking.create.phone_label') }}
                            </label>
                            <div class="phone-input-wrapper" id="phone-input-wrapper">
                                <button type="button" id="phone_country_btn" class="phone-country-btn" aria-haspopup="listbox" aria-expanded="false" aria-label="Country code">
                                    <img id="phone_flag_img" src="https://flagcdn.com/w40/vn.png" alt="VN">
                                    <span class="arrow">&#9660;</span>
                                </button>
                                <div id="phone_dropdown" class="phone-dropdown" role="listbox">
                                    <div class="phone-dropdown-search">
                                        <input type="text" id="phone_search" placeholder="{{ __('client.booking.create.search_country_placeholder') }}" autocomplete="off">
                                    </div>
                                    <div class="phone-dropdown-list" id="phone_dropdown_list"></div>
                                </div>
                                <input type="text" id="phone_dial_code" class="phone-dial-code" value="+84" maxlength="5" placeholder="+84" autocomplete="off" aria-label="Dial code">
                                <input type="tel" id="phone_number" value="" class="phone-number-input" inputmode="tel" required
                                    placeholder="{{ __('client.booking.create.phone_placeholder') }}">
                            </div>
                            <input type="hidden" id="customer_phone_full" name="customer_phone" value="{{ old('customer_phone', request('customer_phone', $user->phone ?? '')) }}">
                            <p id="phone-error-msg" class="mt-1 hidden text-xs text-red-500"></p>
                        </div>
                        <div>
                            <label for="customer_email" class="mb-2 block text-sm font-semibold text-gray-700">
                                {{ __('client.booking.create.email_label') }}
                            </label>
                            <input type="email" id="customer_email" name="customer_email"
                                value="{{ old('customer_email', request('customer_email', $user->email ?? '')) }}"
                                class="w-full rounded-xl border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:border-primary-600 focus:bg-white focus:ring-primary-100"
                                required placeholder="{{ __('client.booking.create.email_placeholder') }}">
                        </div>
                        <div>
                            <label for="notes" class="mb-2 block text-sm font-semibold text-gray-700">
                                {{ __('client.booking.create.notes_label') }}
                            </label>
                            <textarea id="notes" name="notes" rows="1"
                                class="w-full rounded-xl border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:border-primary-600 focus:bg-white focus:ring-primary-100"
                                placeholder="{{ __('client.booking.create.notes_placeholder') }}">{{ old('notes', request('notes')) }}</textarea>
                        </div>
                    </div>

                    {{-- Payment --}}
                    <div>
                        <h3 class="mb-3 text-sm font-semibold text-gray-800">{{ __('client.booking.create.payment_method_title') }}</h3>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @foreach ($paymentMethods as $method)
                                <label class="payment-method-label block rounded-xl border border-gray-200 p-4 {{ $selectedPayment === $method['key'] ? 'selected' : '' }}">
                                    <input type="radio" name="payment_method" value="{{ $method['key'] }}"
                                        class="sr-only payment-method-input" @checked($selectedPayment === $method['key'])>
                                    <span class="flex items-start gap-3">
                                        <span class="radio-icon mt-0.5 flex h-5 w-5 items-center justify-center rounded-full border-2 border-gray-300">
                                            <span class="hidden h-2.5 w-2.5 rounded-full bg-primary-600"></span>
                                        </span>
                                        <span class="flex-1">
                                            <span class="block text-sm font-semibold text-gray-900">{{ $method['label'] }}</span>
                                            <span class="mt-0.5 block text-xs text-gray-600">{{ $method['description'] }}</span>
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- Confirm + Trust + Submit --}}
                <section class="booking-panel space-y-4 p-5 md:p-6">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="flex flex-col items-center gap-1.5 rounded-xl bg-gray-50 p-3">
                            <i class="fa-solid fa-shield-halved text-lg text-primary-600"></i>
                            <span class="text-[11px] font-medium text-gray-600">{{ __('client.booking.create.trust_secure') }}</span>
                        </div>
                        <div class="flex flex-col items-center gap-1.5 rounded-xl bg-gray-50 p-3">
                            <i class="fa-solid fa-headset text-lg text-primary-600"></i>
                            <span class="text-[11px] font-medium text-gray-600">{{ __('client.booking.create.trust_support') }}</span>
                        </div>
                        <div class="flex flex-col items-center gap-1.5 rounded-xl bg-gray-50 p-3">
                            <i class="fa-solid fa-bolt text-lg text-primary-600"></i>
                            <span class="text-[11px] font-medium text-gray-600">{{ __('client.booking.create.trust_instant') }}</span>
                        </div>
                    </div>

                    <label for="confirm_info" class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 p-4 transition hover:border-primary-600 hover:bg-primary-50/40">
                        <input type="checkbox" id="confirm_info"
                            class="mt-0.5 h-5 w-5 shrink-0 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700">{{ __('client.booking.create.confirm_info_label') }}</span>
                    </label>

                    <button type="submit" id="submit-booking"
                        class="ksb-btn-primary hidden w-full px-6 py-3.5 text-sm disabled:cursor-not-allowed disabled:opacity-60 xl:inline-flex xl:w-auto">
                        <span id="submit-text">{{ __('client.booking.create.submit_button') }}</span>
                        <i id="submit-spinner" class="fa-solid fa-spinner hidden animate-spin"></i>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </section>
            </form>

            {{-- Sidebar summary (desktop) --}}
            <aside class="sticky-summary ksb-sticky-summary hidden space-y-5 xl:block">
                <div class="booking-sidebar-card space-y-4 p-5 md:p-6">
                    <h2 class="font-display text-lg font-bold text-gray-900">{{ __('client.booking.create.summary_title') }}</h2>

                    {{-- Route timeline --}}
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot mt-0.5 text-green-500"></i>
                            <div class="min-w-0">
                                <span class="text-xs text-gray-500">{{ __('client.booking.create.pickup_label') }}</span>
                                <p class="font-medium text-gray-900" data-summary="pickup">{{ __('client.booking.create.not_selected') }}</p>
                            </div>
                        </div>
                        <div class="ksb-route-timeline mx-3"></div>
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot mt-0.5 text-red-500"></i>
                            <div class="min-w-0">
                                <span class="text-xs text-gray-500">{{ __('client.booking.create.dropoff_label') }}</span>
                                <p class="font-medium text-gray-900" data-summary="dropoff">{{ __('client.booking.create.not_selected') }}</p>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    @include('client.booking.partials.price-summary', $summaryData)
                </div>

                {{-- Amenities --}}
                <div class="booking-sidebar-card space-y-3 bg-[#fffdf5] p-5 text-sm text-slate-700 md:p-6">
                    @php
                        $amenityServices = collect($serviceDetails ?? [])->filter()->values();
                        if ($amenityServices->isEmpty()) {
                            $amenityServices = collect($services ?? [])->filter()->map(fn ($service) => [
                                'name' => $service,
                                'icon' => 'fa-solid fa-circle-check',
                            ])->values();
                        }
                    @endphp
                    <h3 class="text-sm font-semibold text-slate-800">{{ __('client.booking.create.amenities_title') }}</h3>
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
                            <li class="flex items-center gap-2 text-xs"><i class="fa-solid fa-circle-check text-primary-600"></i>{{ __('client.booking.create.amenity_ac') }}</li>
                            <li class="flex items-center gap-2 text-xs"><i class="fa-solid fa-circle-check text-primary-600"></i>{{ __('client.booking.create.amenity_blanket') }}</li>
                            <li class="flex items-center gap-2 text-xs"><i class="fa-solid fa-circle-check text-primary-600"></i>{{ __('client.booking.create.amenity_water') }}</li>
                            <li class="flex items-center gap-2 text-xs"><i class="fa-solid fa-circle-check text-primary-600"></i>{{ __('client.booking.create.amenity_wifi') }}</li>
                        @endforelse
                    </ul>
                </div>

                {{-- Support --}}
                <div class="booking-sidebar-card space-y-3 p-5 md:p-6">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('client.booking.create.support_title') }}</h3>
                    <div class="space-y-2">
                        @if (!empty($web_profile?->hotline))
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $web_profile->hotline) }}"
                                class="flex items-center gap-2 rounded-lg bg-gray-50 p-2 text-sm transition hover:bg-gray-100">
                                <i class="fa-solid fa-phone text-primary-600"></i>
                                <span class="font-medium text-gray-800">{{ $web_profile->hotline }}</span>
                            </a>
                        @endif
                        @if (!empty($web_profile?->zalo_url))
                            <a href="{{ $web_profile->zalo_url }}" target="_blank"
                                class="flex items-center gap-2 rounded-lg bg-gray-50 p-2 text-sm transition hover:bg-gray-100">
                                <i class="fa-solid fa-comment-dots text-primary-600"></i>
                                <span class="font-medium text-gray-800">Zalo</span>
                            </a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </section>

    {{-- Mobile sticky action bar --}}
    <div id="mobile-booking-bar"
        class="ksb-alert ksb-mobile-action-bar fixed bottom-0 left-0 right-0 border-t border-gray-200 bg-white shadow-lg xl:hidden">
        <div class="container mx-auto flex items-center justify-between gap-3 px-4 py-3">
            <button type="button" id="open-price-sheet" class="text-left" aria-haspopup="dialog" aria-controls="price-sheet">
                <span class="block text-[11px] text-gray-500">{{ __('client.booking.create.mobile_total_label') }}</span>
                <span class="ksb-price block text-xl font-bold text-primary-600" data-summary="total">{{ number_format($finalUnitPrice) }}đ</span>
                <span class="block text-[11px] font-medium text-primary-600">
                    <i class="fa-solid fa-chevron-up"></i> {{ __('client.booking.create.view_price_details') }}
                </span>
            </button>
            <button type="submit" form="booking-form" id="submit-booking-mobile"
                class="ksb-btn-primary max-w-[200px] flex-1 px-6 py-3 text-sm disabled:cursor-not-allowed disabled:opacity-60">
                <span class="submit-text-mobile">{{ __('client.booking.create.submit_button') }}</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>

    {{-- Mobile price sheet --}}
    <div id="price-sheet-backdrop" class="fixed inset-0 z-modal bg-black/40 opacity-0 pointer-events-none xl:hidden"></div>
    <div id="price-sheet" class="fixed bottom-0 left-0 right-0 z-modal translate-y-full xl:hidden" role="dialog" aria-modal="true" aria-label="{{ __('client.booking.create.price_details_title') }}">
        <div class="max-h-[80vh] overflow-y-auto rounded-t-2xl bg-white p-5 shadow-2xl">
            <div class="mx-auto mb-3 h-1.5 w-12 rounded-full bg-gray-300"></div>
            <div class="mb-3 flex items-center justify-between">
                <h3 class="font-display font-bold text-gray-900">{{ __('client.booking.create.price_details_title') }}</h3>
                <button type="button" id="close-price-sheet" class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-500" aria-label="{{ __('client.booking.create.hide_price_details') }}">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            {{-- Route summary --}}
            <div class="mb-3 space-y-2 rounded-xl bg-gray-50 p-3 text-sm">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-location-dot mt-0.5 text-green-500"></i>
                    <div class="min-w-0">
                        <span class="text-xs text-gray-500">{{ __('client.booking.create.pickup_label') }}</span>
                        <p class="font-medium text-gray-900" data-summary="pickup">{{ __('client.booking.create.not_selected') }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-location-dot mt-0.5 text-red-500"></i>
                    <div class="min-w-0">
                        <span class="text-xs text-gray-500">{{ __('client.booking.create.dropoff_label') }}</span>
                        <p class="font-medium text-gray-900" data-summary="dropoff">{{ __('client.booking.create.not_selected') }}</p>
                    </div>
                </div>
            </div>

            @include('client.booking.partials.price-summary', $summaryData)
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const bookingForm = document.getElementById('booking-form');
                const submitButton = document.getElementById('submit-booking');
                const submitButtonMobile = document.getElementById('submit-booking-mobile');
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
                    confirmRequired: @json(__('client.booking.create.frontend_confirm_required')),
                };

                /* ---------- Element refs ---------- */
                const hotelPickupInput = document.getElementById('hotel_pickup_address');
                const hotelPickupWrapper = document.getElementById('hotel-pickup-address-wrapper');
                const customerNameInput = document.getElementById('customer_name');
                const customerEmailInput = document.getElementById('customer_email');
                const phoneNumberInput = document.getElementById('phone_number');
                const phoneCountryBtn = document.getElementById('phone_country_btn');
                const phoneFlagImg = document.getElementById('phone_flag_img');
                const phoneDropdown = document.getElementById('phone_dropdown');
                const phoneDropdownList = document.getElementById('phone_dropdown_list');
                const phoneSearchInput = document.getElementById('phone_search');
                const phoneDialCodeEl = document.getElementById('phone_dial_code');
                const customerPhoneFullInput = document.getElementById('customer_phone_full');
                const phoneErrorMsg = document.getElementById('phone-error-msg');
                const confirmInfo = document.getElementById('confirm_info');

                /* ---------- Helpers ---------- */
                const setSummary = (key, value) => {
                    document.querySelectorAll(`[data-summary="${key}"]`).forEach(el => { el.textContent = value; });
                };

                const showValidationError = (message) => {
                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        window.Swal.fire({ icon: 'warning', title: validationMessages.generalValidationError, text: message, confirmButtonText: 'OK' });
                    } else if (window.toastr && typeof window.toastr.warning === 'function') {
                        window.toastr.warning(message);
                    } else {
                        alert(message);
                    }
                };

                const flagError = (el) => {
                    if (!el) return;
                    el.classList.add('field-error');
                    const focusable = el.matches('input,select,textarea') ? el : el.querySelector('input,select,textarea');
                    (focusable || el).scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => { try { (focusable || el).focus({ preventScroll: true }); } catch (e) {} }, 300);
                };

                const clearError = (el) => { if (el) el.classList.remove('field-error'); };
                [customerNameInput, customerEmailInput, hotelPickupInput].forEach(el => {
                    if (el) el.addEventListener('input', () => clearError(el));
                });

                /* ---------- Country phone component ---------- */
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
                    ['+973','bh','Bahrain'],['+964','iq','Iraq'],['+962','jo','Jordan'],
                    ['+965','kw','Kuwait'],['+961','lb','Lebanon'],['+968','om','Oman'],
                    ['+974','qa','Qatar'],['+963','sy','Syria'],['+967','ye','Yemen'],
                    ['+970','ps','Palestine'],
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

                const renderDropdownItems = (filter = '') => {
                    phoneDropdownList.innerHTML = '';
                    const lowerFilter = filter.toLowerCase();
                    const filtered = countries.filter(([dialCode, iso2, name]) =>
                        !filter || name.toLowerCase().includes(lowerFilter) || dialCode.includes(filter) || iso2.includes(lowerFilter)
                    );
                    filtered.forEach(([dialCode, iso2, name]) => {
                        const item = document.createElement('div');
                        item.className = 'phone-dropdown-item' + (dialCode === selectedDialCode ? ' selected' : '');
                        item.setAttribute('role', 'option');
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
                phoneSearchInput.addEventListener('input', () => renderDropdownItems(phoneSearchInput.value));
                phoneSearchInput.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeDropdown(); phoneCountryBtn.focus(); } });
                document.addEventListener('click', (e) => {
                    if (!document.getElementById('phone-input-wrapper').contains(e.target)) closeDropdown();
                });

                const syncFullPhone = () => {
                    const number = phoneNumberInput.value.replace(/[^\d]/g, '');
                    const dialCode = phoneDialCodeEl.value.startsWith('+') ? phoneDialCodeEl.value : '+' + phoneDialCodeEl.value;
                    customerPhoneFullInput.value = number ? dialCode + number : '';
                };

                phoneDialCodeEl.addEventListener('input', () => {
                    let val = phoneDialCodeEl.value.replace(/[^\d+]/g, '');
                    if (!val.startsWith('+')) val = '+' + val;
                    phoneDialCodeEl.value = val;
                    selectedDialCode = val;
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
                phoneNumberInput.addEventListener('keypress', (e) => {
                    const char = String.fromCharCode(e.which || e.keyCode);
                    if (/[a-zA-Z]/.test(char)) e.preventDefault();
                });
                phoneNumberInput.addEventListener('input', () => {
                    let val = phoneNumberInput.value.replace(/[a-zA-Z]/g, '');
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
                    clearError(document.getElementById('phone-input-wrapper'));
                });
                phoneNumberInput.addEventListener('blur', syncFullPhone);

                const oldPhone = @json(old('customer_phone', request('customer_phone', $user->phone ?? '')));
                if (oldPhone) {
                    if (oldPhone.startsWith('+')) {
                        const sortedCountries = [...countries].sort((a, b) => b[0].length - a[0].length);
                        let matched = false;
                        for (const [dialCode, iso2] of sortedCountries) {
                            if (oldPhone.startsWith(dialCode)) {
                                selectCountry(dialCode, iso2, false);
                                phoneNumberInput.value = oldPhone.substring(dialCode.length);
                                matched = true;
                                break;
                            }
                        }
                        if (!matched) phoneNumberInput.value = oldPhone.substring(1);
                    } else {
                        phoneNumberInput.value = oldPhone;
                    }
                    syncFullPhone();
                }

                /* ---------- Quantity & summary ---------- */
                const quantityInput = document.getElementById('quantity');
                const decreaseBtn = document.getElementById('decrease-quantity');
                const increaseBtn = document.getElementById('increase-quantity');
                const maxQuantity = {{ (int) $availableSeats }};
                const finalUnitPrice = {{ $finalUnitPrice }};
                const totalPriceInput = document.getElementById('total-price-input');

                const updateSummary = () => {
                    const quantity = parseInt(quantityInput.value) || 0;
                    const totalPrice = quantity * finalUnitPrice;
                    setSummary('quantity', quantity);
                    setSummary('total', totalPrice > 0 ? totalPrice.toLocaleString('vi-VN') + 'đ' : '0đ');
                    totalPriceInput.value = totalPrice;
                    const disabled = quantity <= 0;
                    if (submitButton) submitButton.disabled = disabled;
                    if (submitButtonMobile) submitButtonMobile.disabled = disabled;
                };

                decreaseBtn.addEventListener('click', () => {
                    let v = parseInt(quantityInput.value) || 1;
                    if (v > 1) { quantityInput.value = v - 1; updateSummary(); }
                });
                increaseBtn.addEventListener('click', () => {
                    let v = parseInt(quantityInput.value) || 1;
                    if (v < maxQuantity) { quantityInput.value = v + 1; updateSummary(); }
                });

                /* ---------- Date picker (flatpickr global) ---------- */
                if (window.flatpickr) {
                    flatpickr('#booking_date', {
                        dateFormat: 'd/m/Y',
                        minDate: 'today',
                        defaultDate: @json($bookingDate->format('d/m/Y')),
                        disableMobile: true,
                        onChange: function(selectedDates) {
                            const d = selectedDates[0];
                            if (!d) return;
                            const iso = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
                            const url = new URL(window.location.href);
                            url.searchParams.set('date', iso);
                            window.location.href = url.toString();
                        },
                    });
                }

                /* ---------- Stop selection (real radios) ---------- */
                const stopInputs = document.querySelectorAll('.stop-input');
                const syncStopCard = (input) => {
                    const card = input.closest('.stop-card');
                    const type = input.dataset.type;
                    document.querySelectorAll(`.stop-input[data-type="${type}"]`).forEach(i => {
                        i.closest('.stop-card').classList.toggle('selected', i.checked);
                    });
                    if (input.checked) {
                        const name = card.dataset.stopName || '';
                        setSummary(type, name);
                        clearError(document.getElementById(type === 'pickup' ? 'pickup-stops-container' : 'dropoff-stops-container'));
                        if (type === 'pickup') {
                            const isHotel = input.value === 'hotel_pickup';
                            hotelPickupWrapper.classList.toggle('hidden', !isHotel);
                            hotelPickupInput.required = isHotel;
                            if (!isHotel) { hotelPickupInput.value = ''; clearError(hotelPickupInput); }
                        }
                    }
                    updateStepProgress();
                };
                stopInputs.forEach(input => {
                    input.addEventListener('change', () => syncStopCard(input));
                    if (input.checked) syncStopCard(input);
                });

                /* ---------- Stop search filter ---------- */
                const stopSearch = document.getElementById('stop-search');
                const noResult = document.getElementById('stop-no-result');
                if (stopSearch) {
                    stopSearch.addEventListener('input', () => {
                        const q = stopSearch.value.trim().toLowerCase();
                        let visible = 0;
                        document.querySelectorAll('.stop-card').forEach(card => {
                            const hay = card.dataset.search || card.dataset.stopName?.toLowerCase() || '';
                            const match = !q || hay.includes(q);
                            card.classList.toggle('hidden', !match);
                            if (match) visible++;
                        });
                        if (noResult) noResult.classList.toggle('hidden', visible !== 0);
                    });
                }

                /* ---------- Payment selection ---------- */
                const paymentLabels = document.querySelectorAll('.payment-method-label');
                const updatePaymentSelection = () => {
                    paymentLabels.forEach(label => {
                        const input = label.querySelector('.payment-method-input');
                        const radioIcon = label.querySelector('.radio-icon');
                        const innerDot = radioIcon.querySelector('span');
                        const checked = input.checked;
                        label.classList.toggle('selected', checked);
                        radioIcon.classList.toggle('border-primary-600', checked);
                        innerDot.classList.toggle('hidden', !checked);
                    });
                };
                paymentLabels.forEach(label => {
                    label.querySelector('.payment-method-input').addEventListener('change', updatePaymentSelection);
                });

                /* ---------- Step progress ---------- */
                function updateStepProgress() {
                    const hasPickup = !!document.querySelector('input[name="pickup_stop_id"]:checked');
                    const hasDropoff = !!document.querySelector('input[name="dropoff_stop_id"]:checked');
                    const c2 = document.getElementById('step-2-circle');
                    const l2 = document.getElementById('step-2-label');
                    if (hasPickup && hasDropoff) {
                        c2.classList.remove('pending'); c2.classList.add('completed');
                        c2.innerHTML = '<i class="fa-solid fa-check text-xs"></i>';
                        l2.classList.add('active');
                    } else {
                        c2.classList.add('pending'); c2.classList.remove('completed');
                        c2.textContent = '2'; l2.classList.remove('active');
                    }

                    const hasName = (customerNameInput.value || '').trim() !== '';
                    const hasPhone = (phoneNumberInput.value || '').trim() !== '';
                    const hasEmail = (customerEmailInput.value || '').trim() !== '';
                    const c3 = document.getElementById('step-3-circle');
                    const l3 = document.getElementById('step-3-label');
                    if (hasName && hasPhone && hasEmail) {
                        c3.classList.remove('pending'); c3.classList.add('completed');
                        c3.innerHTML = '<i class="fa-solid fa-check text-xs"></i>';
                        l3.classList.add('active');
                    } else {
                        c3.classList.add('pending'); c3.classList.remove('completed');
                        c3.textContent = '3'; l3.classList.remove('active');
                    }
                }
                [customerNameInput, customerEmailInput, phoneNumberInput].forEach(el => el.addEventListener('input', updateStepProgress));

                /* ---------- Mobile price sheet ---------- */
                const priceSheet = document.getElementById('price-sheet');
                const priceBackdrop = document.getElementById('price-sheet-backdrop');
                const openSheetBtn = document.getElementById('open-price-sheet');
                const closeSheetBtn = document.getElementById('close-price-sheet');
                const openSheet = () => { priceSheet.classList.add('open'); priceBackdrop.classList.add('open'); };
                const closeSheet = () => { priceSheet.classList.remove('open'); priceBackdrop.classList.remove('open'); };
                if (openSheetBtn) openSheetBtn.addEventListener('click', openSheet);
                if (closeSheetBtn) closeSheetBtn.addEventListener('click', closeSheet);
                if (priceBackdrop) priceBackdrop.addEventListener('click', closeSheet);
                document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeSheet(); });

                /* ---------- Validation & submit ---------- */
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const getFrontendValidationError = () => {
                    const pickup = document.querySelector('input[name="pickup_stop_id"]:checked');
                    const dropoff = document.querySelector('input[name="dropoff_stop_id"]:checked');

                    if (!pickup) return { message: validationMessages.pickupRequired, el: document.getElementById('pickup-stops-container') };
                    if (!dropoff) return { message: validationMessages.dropoffRequired, el: document.getElementById('dropoff-stops-container') };
                    if (pickup.value === 'hotel_pickup' && !(hotelPickupInput.value || '').trim()) {
                        return { message: validationMessages.hotelAddressRequired, el: hotelPickupInput };
                    }
                    if (!(customerNameInput.value || '').trim()) return { message: validationMessages.nameRequired, el: customerNameInput };

                    syncFullPhone();
                    if (!(phoneNumberInput.value || '').trim()) return { message: validationMessages.phoneRequired, el: document.getElementById('phone-input-wrapper') };

                    if (!(customerEmailInput.value || '').trim()) return { message: validationMessages.emailRequired, el: customerEmailInput };
                    if (!emailRegex.test(customerEmailInput.value.trim())) return { message: validationMessages.emailInvalid, el: customerEmailInput };

                    if (!confirmInfo.checked) return { message: validationMessages.confirmRequired, el: confirmInfo };

                    return null;
                };

                let isSubmitting = false;
                const setLoading = () => {
                    if (submitButton) { submitButton.disabled = true; if (submitText) submitText.classList.add('hidden'); if (submitSpinner) submitSpinner.classList.remove('hidden'); }
                    if (submitButtonMobile) submitButtonMobile.disabled = true;
                };

                bookingForm.addEventListener('submit', function(e) {
                    if (isSubmitting) return;
                    e.preventDefault();

                    document.querySelectorAll('.field-error').forEach(el => el.classList.remove('field-error'));

                    const error = getFrontendValidationError();
                    if (error) {
                        closeSheet();
                        showValidationError(error.message);
                        flagError(error.el);
                        return;
                    }
                    if ((parseInt(quantityInput.value) || 0) <= 0) {
                        showValidationError(validationMessages.generalValidationError);
                        return;
                    }

                    isSubmitting = true;
                    setLoading();
                    bookingForm.submit();
                });

                /* ---------- Init ---------- */
                updateSummary();
                updatePaymentSelection();
                updateStepProgress();
            });
        </script>
    @endpush
</x-client.layout>
