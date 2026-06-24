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
        $rows = DB::table('bookings')
            ->selectRaw('YEAR(booking_date) as y, MONTH(booking_date) as m, SUM(total_price) as total')
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('booking_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupByRaw('YEAR(booking_date), MONTH(booking_date)')
            ->get()
            ->keyBy(fn ($r) => sprintf('%02d/%d', $r->m, $r->y));

        $labels = [];
        $values = [];

        for ($monthOffset = 11; $monthOffset >= 0; $monthOffset--) {
            $date = Carbon::now()->subMonths($monthOffset);
            $key = $date->format('m/Y');
            $labels[] = $key;
            $values[] = (int) ($rows[$key]->total ?? 0);
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
