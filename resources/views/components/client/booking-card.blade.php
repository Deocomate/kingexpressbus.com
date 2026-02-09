@props(['booking', 'type' => 'history'])

@php
    $statusColors = [
        'pending' => 'bg-yellow-100 text-yellow-700',
        'confirmed' => 'bg-blue-100 text-blue-700',
        'completed' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-red-100 text-red-700',
    ];
    $statusColor = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-700';

    $isUpcoming = $type === 'upcoming';
@endphp

<article
    class="bg-white rounded-lg p-6 border border-neutral-200 shadow-soft hover:shadow-card transition-shadow duration-200 {{ $isUpcoming ? 'ring-1 ring-primary-500/10' : '' }}">
    <div class="flex flex-col md:flex-row gap-6 justify-between items-start">
        <div class="space-y-3 flex-1">
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider {{ $statusColor }}">
                    {{ $booking->status }}
                </span>
                <span class="text-sm text-slate-400 font-mono">#{{ $booking->booking_code }}</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">{{ $booking->route_name }}</h3>
                <div class="flex items-center gap-2 text-sm text-slate-500 mt-1">
                    <i class="fa-regular fa-clock {{ $isUpcoming ? 'text-blue-500' : '' }}"></i>
                    <span>Khởi hành: <span
                            class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($booking->booking_date)->format('H:i d/m/Y') }}</span></span>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 text-sm mt-3 pt-3 border-t border-slate-50 border-dashed">
                <div class="flex items-start gap-2 text-slate-600 min-w-[150px]">
                    <div class="mt-1.5 w-2 h-2 rounded-full bg-blue-500 flex-shrink-0 ring-2 ring-blue-100"></div>
                    <div>
                        <span class="block text-xs text-slate-400 uppercase tracking-wide">Điểm đón</span>
                        <span class="font-medium block truncate max-w-[200px]"
                            title="{{ $booking->pickup_name ?? 'N/A' }}">{{ $booking->pickup_name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="flex items-start gap-2 text-slate-600">
                    <div class="mt-1.5 w-2 h-2 rounded-full bg-pink-500 flex-shrink-0 ring-2 ring-pink-100"></div>
                    <div>
                        <span class="block text-xs text-slate-400 uppercase tracking-wide">Điểm trả</span>
                        <span class="font-medium block truncate max-w-[200px]"
                            title="{{ $booking->dropoff_name ?? 'N/A' }}">{{ $booking->dropoff_name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="text-right flex flex-row md:flex-col items-center md:items-end justify-between w-full md:w-auto gap-3 pl-0 md:pl-6 border-t md:border-t-0 md:border-l border-slate-100 pt-4 md:pt-0 mt-2 md:mt-0">
            <div>
                <span class="block text-xs text-slate-400 uppercase">Tổng thanh toán</span>
                <span
                    class="text-xl font-bold text-blue-600 whitespace-nowrap">{{ number_format($booking->total_price) }}
                    đ</span>
            </div>
            <a href="{{ route('client.routes.show', ['slug' => $booking->route_slug]) }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-md bg-neutral-50 text-neutral-600 hover:bg-primary-600 hover:text-white transition-colors duration-200 text-sm font-semibold">
                {{ $isUpcoming ? 'Chi tiết' : 'Đặt lại' }}
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</article>
