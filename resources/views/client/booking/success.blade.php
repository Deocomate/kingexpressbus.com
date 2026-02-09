{{-- ===== resources\views\client\booking\success.blade.php ===== --}}
<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.booking.success.meta_title')" :description="$description ?? ''">
    {{-- Success Banner --}}
    <section class="bg-green-50 border-b border-green-100">
        <div class="container mx-auto px-4 py-12 text-center space-y-5">
            <div
                class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-green-600 text-white text-3xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="text-2xl md:text-3xl font-semibold text-green-700">{{ __('client.booking.success.title') }}</h1>
            <p class="text-gray-600 max-w-xl mx-auto text-sm">{!! __('client.booking.success.thank_you_message', [
                'email' => $booking->customer_email ?? __('client.booking.success.your_email'),
            ]) !!}</p>

            {{-- Booking Code Card --}}
            <div
                class="inline-flex items-center gap-4 bg-white border-2 border-green-200 rounded-lg px-6 py-4 shadow-soft">
                <div class="w-12 h-12 bg-green-100 rounded-md flex items-center justify-center">
                    <i class="fa-solid fa-ticket text-green-600 text-xl"></i>
                </div>
                <div class="text-left">
                    <span
                        class="text-xs text-gray-500 uppercase tracking-wide">{{ __('client.booking.success.booking_code_label') }}</span>
                    <p class="text-xl font-bold text-gray-900 tracking-wider">
                        {{ $booking->booking_code ?? __('client.booking.common.updating') }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="py-10 bg-gray-50">
        <div class="container mx-auto px-4 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <article class="lg:col-span-2 space-y-6">
                {{-- Trip Info Card --}}
                <section class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-bus text-blue-600"></i>
                        {{ __('client.booking.success.trip_info_title') }}
                    </h2>

                    {{-- Route Timeline --}}
                    <div class="flex items-center gap-4 mb-6 p-4 bg-neutral-50 rounded-md">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">
                                {{ isset($booking->start_time) ? \Carbon\Carbon::parse($booking->start_time)->format('H:i') : '--:--' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ __('client.booking.success.departure_time') }}</p>
                        </div>
                        <div class="flex-1 border-t-2 border-dashed border-blue-300 relative">
                            <div class="absolute left-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-blue-500 rounded-full">
                            </div>
                            <div class="absolute right-0 top-1/2 -translate-y-1/2 w-3 h-3 bg-blue-500 rounded-full">
                            </div>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-900">
                                {{ isset($booking->end_time) ? \Carbon\Carbon::parse($booking->end_time)->format('H:i') : '--:--' }}
                            </p>
                            <p class="text-xs text-gray-500">{{ __('client.booking.success.arrival_time') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">{{ __('client.booking.success.route') }}</p>
                            <p class="text-gray-900 font-semibold">
                                {{ $booking->route_name ?? __('client.booking.common.updating') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">{{ __('client.booking.success.departure_date') }}</p>
                            <p class="text-gray-900 font-semibold">
                                {{ isset($booking->booking_date) ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : __('client.booking.common.updating') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">{{ __('client.booking.success.quantity') }}</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->quantity ?? 1 }}
                                {{ __('client.booking.success.tickets') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">
                                {{ __('client.booking.success.bus', ['default' => 'Xe']) }}</p>
                            <p class="text-gray-900 font-semibold">{{ $booking->bus_name ?? 'King Express' }}</p>
                        </div>
                    </div>
                </section>

                {{-- Pickup & Dropoff --}}
                <section class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-green-600"></i>
                        {{ __('client.booking.success.locations_title') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-arrow-up"></i>
                                </span>
                                <span
                                    class="text-xs font-semibold text-green-700 uppercase">{{ __('client.booking.success.pickup_point') }}</span>
                            </div>
                            <p class="font-semibold text-gray-900">
                                {{ $booking->pickup_name ?? __('client.booking.common.updating') }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $booking->pickup_address ?? '' }}</p>
                        </div>
                        <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-arrow-down"></i>
                                </span>
                                <span
                                    class="text-xs font-semibold text-red-700 uppercase">{{ __('client.booking.success.dropoff_point') }}</span>
                            </div>
                            <p class="font-semibold text-gray-900">
                                {{ $booking->dropoff_name ?? __('client.booking.common.updating') }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $booking->dropoff_address ?? '' }}</p>
                        </div>
                    </div>
                </section>

                {{-- Passenger Info --}}
                <section class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-user text-purple-600"></i>
                        {{ __('client.booking.success.passenger_info_title') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">{{ __('client.booking.success.passenger_name') }}</p>
                            <p class="text-gray-900 font-semibold">
                                {{ $booking->customer_name ?? __('client.booking.common.updating') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">{{ __('client.booking.success.passenger_phone') }}</p>
                            <p class="text-gray-900 font-semibold">
                                {{ $booking->customer_phone ?? __('client.booking.common.updating') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">{{ __('client.booking.success.passenger_email') }}</p>
                            <p class="text-gray-900 font-semibold">
                                {{ $booking->customer_email ?? __('client.booking.common.updating') }}</p>
                        </div>
                    </div>
                </section>

                {{-- Next Steps --}}
                <section class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-amber-600"></i>
                        {{ __('client.booking.success.next_steps_title') }}
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm">1</span>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ __('client.booking.success.next_step_1_title') }}</p>
                                <p class="text-sm text-gray-600">{{ __('client.booking.success.next_step_1_desc') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm">2</span>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ __('client.booking.success.next_step_2_title') }}</p>
                                <p class="text-sm text-gray-600">{{ __('client.booking.success.next_step_2_desc') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm">3</span>
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ __('client.booking.success.next_step_3_title') }}</p>
                                <p class="text-sm text-gray-600">{!! __('client.booking.success.next_step_3_desc', ['hotline' => $web_profile->hotline ?? '0865 095 066']) !!}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </article>

            {{-- Sidebar --}}
            <aside class="space-y-5">
                {{-- Payment Summary --}}
                <div class="bg-white border border-gray-100 rounded-lg p-5 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        {{ __('client.booking.success.payment_info_title') }}</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('client.booking.success.total_price') }}</span>
                            <span
                                class="text-xl font-bold text-blue-600">{{ $booking->total_price ? number_format($booking->total_price) . 'đ' : __('client.booking.create.summary_contact_price') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('client.booking.success.payment_method') }}</span>
                            <span
                                class="font-semibold text-gray-900">{{ $booking->payment_method === 'online_banking' ? __('client.booking.success.payment_method_online') : __('client.booking.success.payment_method_cash') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">{{ __('client.booking.success.payment_status') }}</span>
                            <span
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-semibold {{ $booking->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                <i
                                    class="fa-solid {{ $booking->payment_status === 'paid' ? 'fa-check' : 'fa-clock' }}"></i>
                                {{ $booking->payment_status === 'paid' ? __('client.booking.success.paid') : __('client.booking.success.unpaid') }}
                            </span>
                        </div>
                    </div>
                    @if ($booking->payment_method === 'online_banking' && $booking->payment_status !== 'paid')
                        <div class="mt-4 rounded-xl bg-blue-50 border border-blue-100 p-3 text-xs text-blue-700">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            {{ __('client.booking.success.online_payment_note') }}
                        </div>
                    @endif
                </div>

                {{-- Support Card --}}
                <div class="bg-neutral-800 text-white rounded-lg p-5 space-y-4">
                    <h3 class="text-lg font-bold">{{ __('client.booking.success.support_title') }}</h3>
                    <p class="text-sm text-white/70">{{ __('client.booking.success.support_description') }}</p>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $web_profile->hotline ?? '0865095066') }}"
                        class="inline-flex items-center gap-2 w-full justify-center px-4 py-3 bg-accent-500 text-white font-semibold rounded-md hover:bg-accent-600 transition-colors duration-200">
                        <i class="fa-solid fa-phone"></i>
                        {{ __('client.booking.success.call_button') }}
                    </a>
                </div>

                {{-- Other Routes --}}
                <div class="bg-white border border-gray-100 rounded-lg p-5 space-y-3 text-sm">
                    <h3 class="font-bold text-gray-900">{{ __('client.booking.success.other_routes_title') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('client.routes.search', ['from' => 'ha-noi', 'to' => 'sapa']) }}"
                                class="text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-2"><i
                                    class="fa-solid fa-arrow-right text-xs"></i> Hà Nội ⇆ Sa Pa</a></li>
                        <li><a href="{{ route('client.routes.search', ['from' => 'ha-noi', 'to' => 'ninh-binh']) }}"
                                class="text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-2"><i
                                    class="fa-solid fa-arrow-right text-xs"></i> Hà Nội ⇆ Ninh Bình</a></li>
                        <li><a href="{{ route('client.routes.search', ['from' => 'hue', 'to' => 'hoi-an']) }}"
                                class="text-blue-600 hover:text-blue-700 hover:underline flex items-center gap-2"><i
                                    class="fa-solid fa-arrow-right text-xs"></i> Huế ⇆ Hội An</a></li>
                    </ul>
                </div>
            </aside>
        </div>
    </section>
</x-client.layout>
