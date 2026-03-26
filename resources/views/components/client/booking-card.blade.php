@props(['booking', 'type' => 'history'])

@php
    $statusColors = [
        'pending' => 'bg-amber-100 text-amber-700',
        'confirmed' => 'bg-sky-100 text-sky-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-rose-100 text-rose-700',
    ];
    $statusColor = $statusColors[$booking->status] ?? 'bg-slate-100 text-slate-700';

    $isUpcoming = $type === 'upcoming';
@endphp

<article
    class="rounded-2xl border border-amber-100 bg-white p-5 shadow-soft transition-all duration-300 hover:-translate-y-1 hover:shadow-xl {{ $isUpcoming ? 'ring-2 ring-primary-600/15' : '' }}">
    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
        <div class="min-w-0 flex-1 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-wide {{ $statusColor }}">
                    {{ $booking->status }}
                </span>
                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">
                    #{{ $booking->booking_code }}
                </span>
            </div>

            <div>
                <h3 class="line-clamp-1 text-lg font-extrabold text-slate-800 md:text-xl">{{ $booking->route_name }}</h3>
                <div class="mt-2 inline-flex items-center gap-2 rounded-xl bg-pastel/40 px-3 py-1.5 text-sm text-slate-600">
                    <i class="fa-regular fa-clock {{ $isUpcoming ? 'text-primary-600' : '' }}"></i>
                    <span>
                        Khởi hành:
                        <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($booking->booking_date)->format('H:i d/m/Y') }}</span>
                    </span>
                </div>
            </div>

            <div class="grid gap-3 border-t border-dashed border-amber-100 pt-4 md:grid-cols-2">
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-slate-400">Điểm đón</p>
                    <div class="flex items-start gap-2 text-sm text-slate-700">
                        <span class="mt-1 inline-flex h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                        <span class="line-clamp-2 font-semibold" title="{{ $booking->pickup_name ?? 'N/A' }}">{{ $booking->pickup_name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-slate-400">Điểm trả</p>
                    <div class="flex items-start gap-2 text-sm text-slate-700">
                        <span class="mt-1 inline-flex h-2.5 w-2.5 shrink-0 rounded-full bg-rose-500 ring-4 ring-rose-100"></span>
                        <span class="line-clamp-2 font-semibold" title="{{ $booking->dropoff_name ?? 'N/A' }}">{{ $booking->dropoff_name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex w-full flex-row items-center justify-between gap-4 border-t border-amber-100 pt-4 lg:w-auto lg:flex-col lg:items-end lg:justify-start lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0">
            <div class="text-left lg:text-right">
                <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400">Tổng thanh toán</p>
                <p class="text-xl font-extrabold text-primary-600 lg:text-2xl">{{ number_format($booking->total_price) }} đ</p>
            </div>

            <a href="{{ route('client.routes.show', ['slug' => $booking->route_slug]) }}"
                class="inline-flex items-center gap-2 rounded-xl border border-primary-600/20 bg-primary-50 px-4 py-2 text-sm font-bold text-primary-700 transition hover:bg-primary-600 hover:text-white active:scale-95">
                {{ $isUpcoming ? 'Chi tiết' : 'Đặt lại' }}
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</article>
