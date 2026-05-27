<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class BookingStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Tổng quan';

    protected function getStats(): array
    {
        $totalRevenue = (int) DB::table('bookings')
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        return [
            Stat::make('Đặt vé', DB::table('bookings')->count()),
            Stat::make('Doanh thu', number_format($totalRevenue, 0, ',', '.').' VND'),
            Stat::make('Tuyến đường', DB::table('routes')->count()),
            Stat::make('Chuyến đang chạy', DB::table('trips')->where('is_active', true)->count()),
        ];
    }
}
