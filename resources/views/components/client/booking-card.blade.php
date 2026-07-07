@props(['booking', 'type' => 'history'])

@php
    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-700',
        'confirmed' => 'bg-sky-100 text-sky-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-rose-100 text-rose-700',
    ];
    $statusColor = $statusColors[$booking->status] ?? 'bg-panel text-muted';

    $isUpcoming = $type === 'upcoming';
    $pickupName = $booking->pickup_name ?? __('client.booking.service.not_available');
    $dropoffName = $booking->dropoff_name ?? __('client.booking.service.not_available');
@endphp

<article class="kx-card p-5 {{ $isUpcoming ? 'ring-2 ring-brand-600/15' : '' }}">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="min-w-0 flex-1 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-sm px-3 py-1 text-[11px] font-bold uppercase tracking-wide {{ $statusColor }}">
                    {{ $booking->status }}
                </span>
                <span class="rounded-sm bg-panel px-3 py-1 text-xs font-semibold text-muted">
                    #{{ $booking->booking_code }}
                </span>
            </div>

            <div>
                <h3 class="line-clamp-1 text-lg font-extrabold text-ink md:text-xl">{{ $booking->route_name }}</h3>
                <div class="mt-2 inline-flex items-center gap-2 rounded-sm bg-brand-50 px-3 py-1.5 text-sm text-muted">
                    <i class="fa-regular fa-clock {{ $isUpcoming ? 'text-brand-600' : '' }}"></i>
                    <span>
                        {{ __('client.booking_card.departure') }}:
                        <span class="font-bold text-ink">{{ \Carbon\Carbon::parse($booking->booking_date)->format('H:i d/m/Y') }}</span>
                    </span>
                </div>
            </div>

            <div class="grid gap-3 border-t border-dashed border-line-strong pt-4 md:grid-cols-2">
                <div class="rounded-sm bg-panel p-3">
                    <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-muted">{{ __('client.booking_card.pickup') }}</p>
                    <div class="flex items-start gap-2 text-sm text-ink">
                        <span class="mt-1 inline-flex h-2.5 w-2.5 shrink-0 rounded-sm bg-pickup"></span>
                        <span class="line-clamp-2 font-semibold" title="{{ $pickupName }}">{{ $pickupName }}</span>
                    </div>
                </div>
                <div class="rounded-sm bg-panel p-3">
                    <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-muted">{{ __('client.booking_card.dropoff') }}</p>
                    <div class="flex items-start gap-2 text-sm text-ink">
                        <span class="mt-1 inline-flex h-2.5 w-2.5 shrink-0 rounded-sm bg-dropoff"></span>
                        <span class="line-clamp-2 font-semibold" title="{{ $dropoffName }}">{{ $dropoffName }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex w-full flex-row items-center justify-between gap-4 border-t border-line-strong pt-4 lg:w-auto lg:flex-col lg:items-end lg:justify-start lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">
            <div class="text-left lg:text-right">
                <p class="text-[11px] font-bold uppercase tracking-wide text-muted">{{ __('client.booking_card.total_payment') }}</p>
                <p class="kx-price text-xl font-extrabold lg:text-2xl">{{ number_format($booking->total_price) }} đ</p>
            </div>

            <a href="{{ route('client.routes.show', ['slug' => $booking->route_slug]) }}"
                class="kx-btn-secondary px-4 py-2 text-sm">
                {{ $isUpcoming ? __('client.booking_card.actions.details') : __('client.booking_card.actions.rebook') }}
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</article>
