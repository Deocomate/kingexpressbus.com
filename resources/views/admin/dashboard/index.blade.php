@extends('admin.layouts.app')

@section('title', 'Tổng quan')

@section('breadcrumb')
    <span class="text-gray-700 font-medium">Tổng quan</span>
@endsection

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Tổng quan</h1>
        <p class="mt-1 text-sm text-gray-500">Chào mừng, {{ auth()->user()->name ?: auth()->user()->email }}.</p>
    </div>

    {{-- 4 Stat cards — mirrors BookingStatsOverview widget --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-admin::display.stat-card
            title="Đặt vé hôm nay"
            :value="$stats->totalToday"
            description="Đơn mới trong ngày"
            color="primary"
            icon="📅"
        />
        <a href="{{ route('admin.bookings.index', ['tab' => 'pending']) }}" class="block">
            <x-admin::display.stat-card
                title="Chờ xác nhận"
                :value="$stats->pendingTotal"
                description="Cần xử lý ngay"
                color="warning"
                icon="⏰"
            />
        </a>
        <x-admin::display.stat-card
            title="Doanh thu hôm nay"
            :value="$stats->revenueTodayFormatted()"
            description="Đã xác nhận & hoàn thành"
            color="success"
            icon="💰"
        />
        <x-admin::display.stat-card
            title="Doanh thu tổng"
            :value="$stats->totalRevenueFormatted()"
            description="Tất cả thời gian"
            color="info"
            icon="📊"
        />
    </div>

    {{-- Charts row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Status doughnut chart --}}
        <div class="bg-white rounded border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Trạng thái đặt vé</h3>
            <x-admin::display.chart
                id="chart-booking-status"
                type="doughnut"
                :data="$statusChartData->data"
                height="260px"
            />
        </div>

        {{-- Monthly revenue bar chart --}}
        <div class="bg-white rounded border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Doanh thu 12 tháng</h3>
            <x-admin::display.chart
                id="chart-monthly-revenue"
                type="bar"
                :data="$revenueChartData->data"
                height="260px"
            />
        </div>
    </div>

    {{-- Latest upcoming bookings table — mirrors LatestBookings widget --}}
    <div class="bg-white rounded border border-gray-200 shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">Vé sắp khởi hành</h3>
            <p class="mt-0.5 text-xs text-gray-500">Tối đa 10 vé upcoming (pending/confirmed) theo thứ tự giờ đi.</p>
        </div>
        @if($latestBookings->bookings->isEmpty())
        <div class="px-5 py-8 text-center text-sm text-gray-400">Không có vé sắp khởi hành.</div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Mã vé</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Ngày đặt</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Ngày đi</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Khách hàng</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Tuyến đường</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Trạng thái</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Tổng tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($latestBookings->bookings as $booking)
                    @php
                        $statusClasses = match($booking->status->value) {
                            'pending'   => 'bg-yellow-100 text-yellow-800',
                            'confirmed' => 'bg-green-100 text-green-800',
                            default     => 'bg-gray-100 text-gray-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2.5 font-mono text-xs">{{ $booking->booking_code }}</td>
                        <td class="px-4 py-2.5 text-gray-600">{{ $booking->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2.5 text-gray-900 whitespace-nowrap">
                            {{ $booking->departure_at ? \Carbon\Carbon::parse($booking->departure_at)->format('d/m/Y H:i') : '—' }}
                        </td>
                        <td class="px-4 py-2.5 text-gray-900">{{ $booking->customer_name }}</td>
                        <td class="px-4 py-2.5 text-gray-700">{{ $booking->trip?->route?->name ?? '—' }}</td>
                        <td class="px-4 py-2.5">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $statusClasses }}">
                                {{ $booking->status->getLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-right font-medium text-gray-900">
                            {{ number_format($booking->total_price, 0, ',', '.') }} ₫
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        <div class="px-5 py-3 border-t border-gray-100">
            <a href="{{ route('admin.bookings.index', ['tab' => 'upcoming']) }}"
               class="text-sm text-brand-600 hover:text-brand-700 font-medium transition-colors">
                Xem tất cả vé sắp đi →
            </a>
        </div>
    </div>
</div>
@endsection
