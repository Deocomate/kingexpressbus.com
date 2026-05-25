{{-- ===== resources\views\client\booking\success.blade.php ===== --}}
<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []" :title="$title ?? __('client.booking.success.meta_title')" :description="$description ?? ''" body-class="bg-[#F8FAFC]">
    @php
        $isPaid = $booking->payment_status === 'paid';
        $isOnlineBanking = $booking->payment_method === 'online_banking';
        $isAwaitingPaymentRequest = $isOnlineBanking && !$isPaid;
    @endphp

    <section class="border-b border-amber-100 bg-amber-50">
        <div class="mx-auto max-w-7xl px-4 py-12 lg:py-14">
            <div class="mx-auto max-w-3xl text-center">
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-green-200 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-[0.18em] text-green-700">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                    {{ __('client.booking.success.title') }}
                </span>

                <div class="mx-auto mt-5 flex h-20 w-20 items-center justify-center rounded-full bg-green-600 text-3xl text-white shadow-soft">
                    <i class="fa-solid fa-ticket" aria-hidden="true"></i>
                </div>

                <p class="mx-auto mt-5 max-w-2xl text-sm leading-relaxed text-gray-600 sm:text-base">
                    {!! __('client.booking.success.thank_you_message', [
                        'email' => $booking->customer_email ?? __('client.booking.success.your_email'),
                    ]) !!}
                </p>

                @if ($isPaid)
                    <div class="mx-auto mt-5 max-w-2xl rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-semibold leading-relaxed text-green-800">
                        <i class="fa-solid fa-circle-check mr-2" aria-hidden="true"></i>
                        {{ __('client.booking.success.online_payment_success_message') }}
                    </div>
                @elseif ($isAwaitingPaymentRequest)
                    <div class="mx-auto mt-5 max-w-2xl rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold leading-relaxed text-amber-800">
                        <i class="fa-solid fa-clock mr-2" aria-hidden="true"></i>
                        {{ __('client.booking.success.online_payment_pending_message') }}
                    </div>
                @endif
            </div>

            <div class="mx-auto mt-8 max-w-2xl rounded-3xl border border-amber-200 bg-white p-6 shadow-soft">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                            {{ __('client.booking.success.booking_code_label') }}
                        </p>
                        <p class="mt-2 text-2xl font-extrabold tracking-[0.16em] text-gray-900">
                            {{ $booking->booking_code ?? __('client.booking.common.updating') }}
                        </p>
                    </div>
                    <span
                        class="inline-flex items-center gap-2 rounded-full px-4 py-2 text-xs font-semibold {{ $isPaid ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                        <i class="fa-solid {{ $isPaid ? 'fa-check' : 'fa-clock' }}" aria-hidden="true"></i>
                        {{ $isPaid ? __('client.booking.success.paid') : __('client.booking.success.unpaid') }}
                    </span>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 lg:py-12">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-4 lg:grid-cols-3 lg:gap-7">
            <article class="space-y-6 lg:col-span-2">
                <section class="rounded-3xl border border-amber-100 bg-white p-5 shadow-soft sm:p-6">
                    <h2 class="mb-5 flex items-center gap-3 text-lg font-bold text-gray-900">
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 text-primary-700">
                            <i class="fa-solid fa-bus" aria-hidden="true"></i>
                        </span>
                        {{ __('client.booking.success.trip_info_title') }}
                    </h2>

                    <div class="mb-6 rounded-2xl border border-amber-100 bg-amber-50/60 p-4 sm:p-5">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="min-w-[72px] text-center">
                                <p class="text-2xl font-extrabold text-gray-900">
                                    {{ isset($booking->start_time) ? \Carbon\Carbon::parse($booking->start_time)->format('H:i') : '--:--' }}
                                </p>
                                <p class="mt-1 text-xs font-medium uppercase tracking-wider text-gray-500">
                                    {{ __('client.booking.success.departure_time') }}
                                </p>
                            </div>

                            <div class="relative flex-1 border-t-2 border-dashed border-primary-300">
                                <span
                                    class="absolute -left-1.5 top-1/2 h-3 w-3 -translate-y-1/2 rounded-full bg-primary-500"></span>
                                <span
                                    class="absolute -right-1.5 top-1/2 h-3 w-3 -translate-y-1/2 rounded-full bg-primary-500"></span>
                            </div>

                            <div class="min-w-[72px] text-center">
                                <p class="text-2xl font-extrabold text-gray-900">
                                    {{ isset($booking->end_time) ? \Carbon\Carbon::parse($booking->end_time)->format('H:i') : '--:--' }}
                                </p>
                                <p class="mt-1 text-xs font-medium uppercase tracking-wider text-gray-500">
                                    {{ __('client.booking.success.arrival_time') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3.5">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('client.booking.success.route') }}</p>
                            <p class="mt-2 font-semibold text-gray-900">
                                {{ $booking->route_name ?? __('client.booking.common.updating') }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3.5">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('client.booking.success.departure_date') }}</p>
                            <p class="mt-2 font-semibold text-gray-900">
                                {{ isset($booking->booking_date) ? \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') : __('client.booking.common.updating') }}
                            </p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3.5">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('client.booking.success.quantity') }}</p>
                            <p class="mt-2 font-semibold text-gray-900">{{ $booking->quantity ?? 1 }}
                                {{ __('client.booking.success.tickets') }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3.5">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('client.booking.success.bus', ['default' => 'Xe']) }}</p>
                            <p class="mt-2 font-semibold text-gray-900">{{ $booking->bus_name ?? 'King Express' }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-amber-100 bg-white p-5 shadow-soft sm:p-6">
                    <h2 class="mb-5 flex items-center gap-3 text-lg font-bold text-gray-900">
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 text-primary-700">
                            <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        </span>
                        {{ __('client.booking.success.locations_title') }}
                    </h2>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="rounded-2xl border border-green-100 bg-green-50/80 p-4">
                            <div class="mb-2 flex items-center gap-2">
                                <span
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-green-500 text-xs text-white">
                                    <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
                                </span>
                                <span class="text-xs font-semibold uppercase tracking-wider text-green-700">
                                    {{ __('client.booking.success.pickup_point') }}
                                </span>
                            </div>
                            <p class="font-semibold text-gray-900">
                                {{ $booking->pickup_name ?? __('client.booking.common.updating') }}</p>
                            <p class="mt-1 text-sm text-gray-600">{{ $booking->pickup_address ?? '' }}</p>
                        </div>

                        <div class="rounded-2xl border border-rose-100 bg-rose-50/80 p-4">
                            <div class="mb-2 flex items-center gap-2">
                                <span
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-rose-500 text-xs text-white">
                                    <i class="fa-solid fa-arrow-down" aria-hidden="true"></i>
                                </span>
                                <span class="text-xs font-semibold uppercase tracking-wider text-rose-700">
                                    {{ __('client.booking.success.dropoff_point') }}
                                </span>
                            </div>
                            <p class="font-semibold text-gray-900">
                                {{ $booking->dropoff_name ?? __('client.booking.common.updating') }}</p>
                            <p class="mt-1 text-sm text-gray-600">{{ $booking->dropoff_address ?? '' }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-amber-100 bg-white p-5 shadow-soft sm:p-6">
                    <h2 class="mb-5 flex items-center gap-3 text-lg font-bold text-gray-900">
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 text-primary-700">
                            <i class="fa-solid fa-user" aria-hidden="true"></i>
                        </span>
                        {{ __('client.booking.success.passenger_info_title') }}
                    </h2>

                    <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-3">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3.5">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('client.booking.success.passenger_name') }}</p>
                            <p class="mt-2 font-semibold text-gray-900">
                                {{ $booking->customer_name ?? __('client.booking.common.updating') }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3.5">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('client.booking.success.passenger_phone') }}</p>
                            <p class="mt-2 font-semibold text-gray-900">
                                {{ $booking->customer_phone ?? __('client.booking.common.updating') }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-3.5">
                            <p class="text-xs font-medium uppercase tracking-wider text-gray-500">
                                {{ __('client.booking.success.passenger_email') }}</p>
                            <p class="mt-2 break-all font-semibold text-gray-900">
                                {{ $booking->customer_email ?? __('client.booking.common.updating') }}</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-3xl border border-amber-100 bg-white p-5 shadow-soft sm:p-6">
                    <h2 class="mb-5 flex items-center gap-3 text-lg font-bold text-gray-900">
                        <span
                            class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-primary-100 text-primary-700">
                            <i class="fa-solid fa-list-check" aria-hidden="true"></i>
                        </span>
                        {{ __('client.booking.success.next_steps_title') }}
                    </h2>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span
                                class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">1</span>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $isAwaitingPaymentRequest ? __('client.booking.success.next_step_1_online_title') : __('client.booking.success.next_step_1_title') }}</p>
                                <p class="text-sm text-gray-600">{{ $isAwaitingPaymentRequest ? __('client.booking.success.next_step_1_online_desc') : __('client.booking.success.next_step_1_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span
                                class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">2</span>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $isAwaitingPaymentRequest ? __('client.booking.success.next_step_2_online_title') : __('client.booking.success.next_step_2_title') }}</p>
                                <p class="text-sm text-gray-600">{{ $isAwaitingPaymentRequest ? __('client.booking.success.next_step_2_online_desc') : __('client.booking.success.next_step_2_desc') }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span
                                class="mt-0.5 inline-flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 text-sm font-bold text-white">3</span>
                            <div>
                                <p class="font-semibold text-gray-900">{{ __('client.booking.success.next_step_3_title') }}</p>
                                <p class="text-sm text-gray-600">{!! __('client.booking.success.next_step_3_desc', ['hotline' => $web_profile->hotline ?? '0865 095 066']) !!}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </article>

            <aside class="space-y-5 lg:sticky lg:top-24 lg:self-start">
                <section class="rounded-3xl border border-amber-100 bg-white p-5 shadow-soft sm:p-6">
                    <h2 class="mb-4 text-lg font-bold text-gray-900">{{ __('client.booking.success.payment_info_title') }}</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-600">{{ __('client.booking.success.total_price') }}</span>
                            <span class="text-xl font-extrabold text-primary-700">
                                {{ $booking->total_price ? number_format($booking->total_price) . 'đ' : __('client.booking.create.summary_contact_price') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-600">{{ __('client.booking.success.payment_method') }}</span>
                            <span class="font-semibold text-gray-900">
                                {{ $booking->payment_method === 'online_banking' ? __('client.booking.success.payment_method_online') : __('client.booking.success.payment_method_cash') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="text-gray-600">{{ __('client.booking.success.payment_status') }}</span>
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold {{ $isPaid ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                <i class="fa-solid {{ $isPaid ? 'fa-check' : 'fa-clock' }}" aria-hidden="true"></i>
                                {{ $isPaid ? __('client.booking.success.paid') : __('client.booking.success.unpaid') }}
                            </span>
                        </div>
                    </div>

                    @if ($isAwaitingPaymentRequest)
                        <div class="mt-4 rounded-2xl border border-primary-100 bg-primary-50 p-3 text-xs text-primary-700">
                            <i class="fa-solid fa-info-circle mr-1" aria-hidden="true"></i>
                            {{ __('client.booking.success.online_payment_note') }}
                        </div>
                    @endif
                </section>

                <section
                    class="rounded-3xl bg-slate-900 p-5 text-white shadow-soft sm:p-6">
                    <h3 class="text-lg font-bold">{{ __('client.booking.success.support_title') }}</h3>
                    <p class="mt-2 text-sm text-white/75">{{ __('client.booking.success.support_description') }}</p>
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $web_profile->hotline ?? '0865095066') }}"
                        class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 py-3 font-semibold text-white transition-colors duration-200 hover:bg-primary-700">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i>
                        {{ __('client.booking.success.call_button') }}
                    </a>
                </section>

                <section class="rounded-3xl border border-amber-100 bg-white p-5 shadow-soft sm:p-6">
                    <h3 class="text-base font-bold text-gray-900">{{ __('client.booking.success.other_routes_title') }}</h3>
                    <ul class="mt-3 space-y-2.5 text-sm">
                        <li>
                            <a href="{{ route('client.routes.search', ['from' => 'ha-noi', 'to' => 'sapa']) }}"
                                class="inline-flex items-center gap-2 font-medium text-primary-700 transition-colors duration-200 hover:text-primary-800">
                                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i> Hà Nội ⇆ Sa Pa
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('client.routes.search', ['from' => 'ha-noi', 'to' => 'ninh-binh']) }}"
                                class="inline-flex items-center gap-2 font-medium text-primary-700 transition-colors duration-200 hover:text-primary-800">
                                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i> Hà Nội ⇆ Ninh Bình
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('client.routes.search', ['from' => 'hue', 'to' => 'hoi-an']) }}"
                                class="inline-flex items-center gap-2 font-medium text-primary-700 transition-colors duration-200 hover:text-primary-800">
                                <i class="fa-solid fa-arrow-right text-xs" aria-hidden="true"></i> Huế ⇆ Hội An
                            </a>
                        </li>
                    </ul>
                </section>
            </aside>
        </div>
    </section>
</x-client.layout>
