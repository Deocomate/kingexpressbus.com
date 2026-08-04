<?php

namespace App\Support\Admin\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Bar chart data: monthly revenue for the last 12 months.
 * Mirrors MonthlyRevenueChart widget.
 */
class MonthlyRevenueChartData
{
    public readonly array $labels;
    public readonly array $values;
    /** Chart.js payload consumed by the Blade chart component and tests. */
    public readonly array $data;

    private function __construct()
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $yearExpr = "CAST(strftime('%Y', booking_date) AS INTEGER)";
            $monthExpr = "CAST(strftime('%m', booking_date) AS INTEGER)";
        } else {
            $yearExpr = 'YEAR(booking_date)';
            $monthExpr = 'MONTH(booking_date)';
        }

        $rows = DB::table('bookings')
            ->selectRaw("{$yearExpr} as y, {$monthExpr} as m, SUM(total_price) as total")
            ->whereIn('status', ['confirmed', 'completed'])
            ->where('booking_date', '>=', now()->subMonths(11)->startOfMonth())
            ->groupByRaw("{$yearExpr}, {$monthExpr}")
            ->get()
            ->keyBy(fn ($r) => sprintf('%02d/%d', $r->m, $r->y));

        $labels = [];
        $values = [];

        for ($offset = 11; $offset >= 0; $offset--) {
            $date     = Carbon::now()->subMonths($offset);
            $key      = $date->format('m/Y');
            $labels[] = $key;
            $values[] = (int) ($rows[$key]->total ?? 0);
        }

        $this->labels = $labels;
        $this->values = $values;
        $this->data = [
            'datasets' => [[
                'label'           => 'Doanh thu',
                'data'            => $this->values,
                'backgroundColor' => '#FF9B00',
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
