<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.booking.create.meta_title')" :description="$description ?? ''">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
        <style>
            .litepicker {
                font-family: 'Inter', sans-serif;
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
                border-color: #3b82f6;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.4);
            }

            /* Stop Selection Cards */
            .stop-card {
                transition: all 0.2s ease-in-out;
                cursor: pointer;
            }

            .stop-card:hover {
                border-color: #3b82f6;
                background-color: #f0f9ff;
            }

            .stop-card.selected {
                border-color: #3b82f6;
                background-color: #eff6ff;
                box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
            }

            .stop-card.selected .stop-radio {
                border-color: #3b82f6;
                background-color: #3b82f6;
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
                background: #3b82f6;
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
                color: #3b82f6;
                font-weight: 600;
            }

            /* Sticky Summary */
            @media (min-width: 1280px) {
                .sticky-summary {
                    position: sticky;
                    top: 100px;
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
        $busImage = $busImages[0] ?? ($trip->bus_thumbnail ?? '/userfiles/files/kingexpressbus/cabin/1.jpg');
        $seatPrice = (int) ($trip->price ?? 0);
        $pickupStops = $stops->where('stop_type', '!=', 'dropoff');
        $dropoffStops = $stops->where('stop_type', '!=', 'pickup');
    @endphp

    {{-- Hero Section --}}
    <section class="bg-neutral-800 text-white">
        <div class="container mx-auto px-4 py-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
                <div class="space-y-4 lg:col-span-2">
                    <span class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-accent-300">
                        <i class="fa-solid fa-ticket"></i>
                        {{ __('client.booking.create.header_subtitle') }}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-semibold leading-tight">
                        {{ $trip->route_name }}
                    </h1>
                    <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm text-white/80">
                        <span class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-clock w-4 text-center text-blue-400"></i>
                            {{ \Carbon\Carbon::parse($trip->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($trip->end_time)->format('H:i') }}
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-calendar-day w-4 text-center text-green-400"></i>
                            {{ $bookingDate->format('d/m/Y') }}
                        </span>
                        <span class="inline-flex items-center gap-2">
                            <i class="fa-solid fa-bus w-4 text-center text-amber-400"></i>
                            {{ $trip->bus_name }}
                        </span>
                    </div>
                </div>
                <div class="relative h-40 lg:h-48 rounded-lg overflow-hidden shadow-card">
                    <img src="{{ $busImage }}" alt="{{ $trip->bus_name }}" class="h-full w-full object-cover"
                        loading="lazy">
                    <span
                        class="absolute bottom-3 left-3 inline-flex items-center gap-2 bg-white/95 text-neutral-900 px-3 py-1.5 rounded-md text-xs font-semibold">
                        <i class="fa-solid fa-shield-heart text-blue-600"></i>
                        {{ __('client.booking.create.insurance_badge') }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- Step Progress Indicator --}}
    <section class="bg-white border-b border-gray-100">
        <div class="container mx-auto px-4 py-6">
            <div class="step-progress max-w-lg mx-auto">
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
    <section class="py-8 bg-gray-50">
        <div class="container mx-auto px-4 grid grid-cols-1 xl:grid-cols-3 gap-8">
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
                <section class="bg-white border border-gray-100 rounded-lg p-5 space-y-5" id="section-step-1">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span
                                class="w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">1</span>
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
                                class="w-full rounded-md border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-blue-400 focus:ring-0"
                                required>
                        </div>
                        <div>
                            <label for="quantity" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('client.booking.create.quantity_label') }}
                            </label>
                            <div class="flex items-center gap-2">
                                {{-- Quantity Control Card --}}
                                <div
                                    class="inline-flex items-center bg-neutral-50 border border-neutral-200 rounded-md p-1">
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
                                        class="w-10 h-10 flex items-center justify-center rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-all shadow-sm">
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
                <section class="bg-white border border-gray-100 rounded-lg p-5 space-y-5" id="section-step-2">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span
                                class="w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">2</span>
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
                                <div class="stop-card hotel-pickup-badge rounded-md p-4 flex items-start gap-3 {{ old('pickup_stop_id', request('pickup_stop_id')) == 'hotel_pickup' ? 'selected' : '' }}"
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
                                <div class="stop-card border border-gray-200 rounded-md p-4 flex items-start gap-3 {{ old('pickup_stop_id', request('pickup_stop_id')) == $stop->id ? 'selected' : '' }}"
                                    data-stop-id="{{ $stop->id }}" data-type="pickup">
                                    <div class="stop-radio"></div>
                                    <div class="flex-1 min-w-0">
                                        <span
                                            class="font-semibold text-gray-900 block truncate">{{ $stop->name }}</span>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $stop->address }}</p>
                                        <p class="text-xs text-blue-600 mt-1">{{ $stop->district_name }},
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
                            class="w-full rounded-md border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-blue-400 focus:ring-0"
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
                                <div class="stop-card border border-gray-200 rounded-md p-4 flex items-start gap-3 {{ old('dropoff_stop_id', request('dropoff_stop_id')) == $stop->id ? 'selected' : '' }}"
                                    data-stop-id="{{ $stop->id }}" data-type="dropoff">
                                    <div class="stop-radio"></div>
                                    <div class="flex-1 min-w-0">
                                        <span
                                            class="font-semibold text-gray-900 block truncate">{{ $stop->name }}</span>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $stop->address }}</p>
                                        <p class="text-xs text-blue-600 mt-1">{{ $stop->district_name }},
                                            {{ $stop->province_name }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- Step 3: Passenger Info --}}
                <section class="bg-white border border-gray-100 rounded-lg p-5 space-y-5" id="section-step-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span
                                class="w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">3</span>
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
                                class="w-full rounded-md border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-blue-400 focus:ring-0"
                                required placeholder="{{ __('client.booking.create.name_placeholder') }}">
                        </div>
                        <div>
                            <label for="customer_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('client.booking.create.phone_label') }}
                            </label>
                            <input type="tel" id="customer_phone" name="customer_phone"
                                value="{{ old('customer_phone', request('customer_phone', $user->phone ?? '')) }}"
                                class="w-full rounded-md border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-blue-400 focus:ring-0"
                                pattern="(0|\+84)[0-9]{9,10}" inputmode="tel"
                                required placeholder="{{ __('client.booking.create.phone_placeholder') }}">
                        </div>
                        <div>
                            <label for="customer_email" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('client.booking.create.email_label') }}
                            </label>
                            <input type="email" id="customer_email" name="customer_email"
                                value="{{ old('customer_email', request('customer_email', $user->email ?? '')) }}"
                                class="w-full rounded-md border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-blue-400 focus:ring-0"
                                required placeholder="{{ __('client.booking.create.email_placeholder') }}">
                        </div>
                        <div>
                            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ __('client.booking.create.notes_label') }}
                            </label>
                            <textarea id="notes" name="notes" rows="1"
                                class="w-full rounded-md border-neutral-200 bg-neutral-50 p-3 text-base shadow-sm focus:bg-white focus:border-blue-400 focus:ring-0"
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
                                    class="payment-method-label block border border-gray-200 rounded-md p-4 {{ old('payment_method', request('payment_method', 'cash_on_pickup')) === $method['key'] ? 'selected' : '' }}">
                                    <input type="radio" name="payment_method" value="{{ $method['key'] }}"
                                        class="hidden payment-method-input" @checked(old('payment_method', request('payment_method', 'cash_on_pickup')) === $method['key'])>
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="mt-0.5 w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center radio-icon">
                                            <div class="w-2.5 h-2.5 rounded-full bg-blue-600 hidden"></div>
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
                        class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-accent-500 text-white font-semibold rounded-md hover:bg-accent-600 transition-colors duration-200 disabled:opacity-60 disabled:cursor-not-allowed"
                        id="submit-booking">
                        <span id="submit-text">{{ __('client.booking.create.submit_button') }}</span>
                        <i id="submit-spinner" class="fa-solid fa-spinner animate-spin hidden"></i>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </form>

            {{-- Sidebar Summary --}}
            <aside class="space-y-5 sticky-summary">
                <div class="bg-white border border-gray-100 rounded-lg p-5 space-y-4">
                    <h2 class="text-lg font-bold text-gray-900">{{ __('client.booking.create.summary_title') }}</h2>

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
                            <span>{{ __('client.booking.create.summary_price_per_ticket') }}</span>
                            <span class="font-semibold text-gray-900">
                                {{ $seatPrice > 0 ? number_format($seatPrice) . 'đ' : __('client.booking.create.summary_contact_price') }}
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
                    </div>

                    <div
                        class="border-t border-gray-200 pt-4 mt-4 flex items-center justify-between text-lg font-bold text-gray-900">
                        <span>{{ __('client.booking.create.summary_total') }}</span>
                        <span class="text-blue-600" id="summary-total-price">0đ</span>
                    </div>
                </div>

                {{-- Amenities --}}
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-5 space-y-3 text-sm text-blue-800">
                    <h3 class="text-sm font-semibold text-blue-900">{{ __('client.booking.create.amenities_title') }}
                    </h3>
                    <ul class="grid grid-cols-2 gap-x-3 gap-y-2">
                        @forelse ($services as $service)
                            <li class="flex items-center gap-2 text-xs">
                                <i class="fa-solid fa-circle-check text-blue-500"></i>
                                {{ $service }}
                            </li>
                        @empty
                            <li class="flex items-center gap-2 text-xs"><i
                                    class="fa-solid fa-circle-check text-blue-500"></i>{{ __('client.booking.create.amenity_ac') }}
                            </li>
                            <li class="flex items-center gap-2 text-xs"><i
                                    class="fa-solid fa-circle-check text-blue-500"></i>{{ __('client.booking.create.amenity_blanket') }}
                            </li>
                            <li class="flex items-center gap-2 text-xs"><i
                                    class="fa-solid fa-circle-check text-blue-500"></i>{{ __('client.booking.create.amenity_water') }}
                            </li>
                            <li class="flex items-center gap-2 text-xs"><i
                                    class="fa-solid fa-circle-check text-blue-500"></i>{{ __('client.booking.create.amenity_wifi') }}
                            </li>
                        @endforelse
                    </ul>
                </div>

                {{-- Support --}}
                <div class="bg-white border border-gray-100 rounded-lg p-5 space-y-3">
                    <h3 class="text-sm font-semibold text-gray-900">{{ __('client.booking.create.support_title') }}
                    </h3>
                    <div class="space-y-2">
                        @if (!empty($web_profile->hotline))
                            <a href="tel:{{ preg_replace('/[^\d+]/', '', $web_profile->hotline) }}"
                                class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition text-sm">
                                <i class="fa-solid fa-phone text-blue-600"></i>
                                <span class="font-medium text-gray-800">{{ $web_profile->hotline }}</span>
                            </a>
                        @endif
                        @if (!empty($web_profile->zalo_url))
                            <a href="{{ $web_profile->zalo_url }}" target="_blank"
                                class="flex items-center gap-2 p-2 rounded-lg bg-gray-50 hover:bg-gray-100 transition text-sm">
                                <i class="fa-solid fa-comment-dots text-blue-600"></i>
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
                const customerPhoneInput = document.getElementById('customer_phone');
                const customerEmailInput = document.getElementById('customer_email');

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
                    const customerPhone = (customerPhoneInput?.value || '').trim();
                    const customerEmail = (customerEmailInput?.value || '').trim();
                    const hotelAddress = (hotelPickupInput?.value || '').trim();
                    const phoneRegex = /^(0|\+84)[0-9]{9,10}$/;
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

                    if (!phoneRegex.test(customerPhone)) {
                        return validationMessages.phoneInvalid;
                    }

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
                const seatPrice = {{ $seatPrice }};
                const summaryQuantity = document.getElementById('summary-quantity');
                const summaryTotalPrice = document.getElementById('summary-total-price');
                const totalPriceInput = document.getElementById('total-price-input');

                const updateSummary = () => {
                    const quantity = parseInt(quantityInput.value);
                    const totalPrice = quantity * seatPrice;
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
                            radioIcon.classList.add('border-blue-600');
                            innerDot.classList.remove('hidden');
                        } else {
                            label.classList.remove('selected');
                            radioIcon.classList.remove('border-blue-600');
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
