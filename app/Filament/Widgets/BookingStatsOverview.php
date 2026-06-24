<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Bookings\BookingResource;
use App\Services\BookingService;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class BookingStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Tổng quan hôm nay';

    protected function getStats(): array
    {
        $todayStats = app(BookingService::class)->getAdminBookingStats();

        $totalRevenue = (int) DB::table('bookings')
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        return [
            Stat::make('Đặt vé hôm nay', $todayStats['totalToday'])
                ->description('Đơn mới trong ngày')
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->color('primary'),
            Stat::make('Chờ xác nhận', $todayStats['pendingTotal'])
                ->description('Cần xử lý ngay')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color('warning')
                ->url(BookingResource::getUrl('index', ['tab' => 'pending'])),
            Stat::make('Doanh thu hôm nay', number_format($todayStats['revenueToday'], 0, ',', '.').' ₫')
                ->description('Đã xác nhận & hoàn thành')
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->color('success'),
            Stat::make('Doanh thu tổng', number_format($totalRevenue, 0, ',', '.').' ₫')
                ->description('Tất cả thời gian')
                ->descriptionIcon(Heroicon::OutlinedChartBar)
                ->color('info'),
        ];
    }
}
