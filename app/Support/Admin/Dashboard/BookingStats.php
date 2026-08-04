<?php

namespace App\Support\Admin\Dashboard;

use App\Services\BookingService;
use App\Support\ClientCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Cached dashboard stats: today bookings, pending count, today revenue, total revenue.
 * Cache key ClientCache::ADMIN_DASHBOARD_STATS, TTL 60s — mirrors BookingStatsOverview widget.
 */
class BookingStats
{
    public readonly int $totalToday;
    public readonly int $pendingTotal;
    public readonly int $revenueToday;
    public readonly int $totalRevenue;

    private function __construct(array $data)
    {
        $this->totalToday   = (int) $data['todayStats']['totalToday'];
        $this->pendingTotal = (int) $data['todayStats']['pendingTotal'];
        $this->revenueToday = (int) $data['todayStats']['revenueToday'];
        $this->totalRevenue = (int) $data['totalRevenue'];
    }

    public static function load(): self
    {
        $data = Cache::remember(ClientCache::ADMIN_DASHBOARD_STATS, 60, function () {
            return [
                'todayStats' => app(BookingService::class)->getAdminBookingStats(),
                'totalRevenue' => (int) DB::table('bookings')
                    ->whereIn('status', ['confirmed', 'completed'])
                    ->sum('total_price'),
            ];
        });

        return new self($data);
    }

    public function revenueTodayFormatted(): string
    {
        return number_format($this->revenueToday, 0, ',', '.').' ₫';
    }

    public function totalRevenueFormatted(): string
    {
        return number_format($this->totalRevenue, 0, ',', '.').' ₫';
    }
}
