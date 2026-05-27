<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class MonthlyRevenueChart extends ChartWidget
{
    protected ?string $heading = 'Doanh thu 12 tháng';

    protected function getData(): array
    {
        $labels = [];
        $values = [];

        for ($monthOffset = 11; $monthOffset >= 0; $monthOffset--) {
            $date = Carbon::now()->subMonths($monthOffset);
            $labels[] = $date->format('m/Y');
            $values[] = (int) DB::table('bookings')
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereYear('booking_date', $date->year)
                ->whereMonth('booking_date', $date->month)
                ->sum('total_price');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Doanh thu',
                    'data' => $values,
                    'backgroundColor' => '#FF9B00',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
