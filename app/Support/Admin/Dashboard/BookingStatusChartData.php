<?php

namespace App\Support\Admin\Dashboard;

use App\Enums\BookingStatus;
use Illuminate\Support\Facades\DB;

/**
 * Doughnut chart data: booking counts by status.
 * Mirrors BookingStatusChart widget.
 */
class BookingStatusChartData
{
    public readonly array $labels;
    public readonly array $values;
    public readonly array $colors;
    /** Chart.js payload consumed by the Blade chart component and tests. */
    public readonly array $data;

    private function __construct()
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

        $this->labels = collect($statuses)
            ->map(fn (BookingStatus $s): string => $s->getLabel())
            ->all();

        $this->values = collect($statuses)
            ->map(fn (BookingStatus $s): int => (int) ($stats[$s->value] ?? 0))
            ->all();

        $this->colors = ['#f59e0b', '#22c55e', '#ef4444', '#3b82f6'];
        $this->data = [
            'datasets' => [[
                'label'           => 'Đặt vé',
                'data'            => $this->values,
                'backgroundColor' => $this->colors,
            ]],
            'labels' => $this->labels,
        ];
    }

    public static function load(): self
    {
        return new self();
    }

    public function toChartJs(): array
    {
        return $this->data;
    }
}
