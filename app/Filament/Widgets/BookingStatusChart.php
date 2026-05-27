<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BookingStatusChart extends ChartWidget
{
    protected ?string $heading = 'Trạng thái đặt vé';

    protected function getData(): array
    {
        $stats = DB::table('bookings')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
            'completed' => 'Hoàn thành',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Đặt vé',
                    'data' => collect(array_keys($labels))->map(fn (string $status): int => (int) ($stats[$status] ?? 0))->all(),
                    'backgroundColor' => ['#f59e0b', '#22c55e', '#ef4444', '#3b82f6'],
                ],
            ],
            'labels' => array_values($labels),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
