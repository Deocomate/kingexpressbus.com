<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BookingStatusChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Trạng thái đặt vé';

    protected function getData(): array
    {
        $stats = DB::table('bookings')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $statuses = [
            BookingStatus::Pending,
            BookingStatus::Confirmed,
            BookingStatus::Cancelled,
            BookingStatus::Completed,
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Đặt vé',
                    'data' => collect($statuses)->map(fn (BookingStatus $status): int => (int) ($stats[$status->value] ?? 0))->all(),
                    'backgroundColor' => ['#f59e0b', '#22c55e', '#ef4444', '#3b82f6'],
                ],
            ],
            'labels' => collect($statuses)->map(fn (BookingStatus $status): string => $status->getLabel())->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
