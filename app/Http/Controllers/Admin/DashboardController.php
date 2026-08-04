<?php

namespace App\Http\Controllers\Admin;

use App\Support\Admin\Dashboard\BookingStats;
use App\Support\Admin\Dashboard\BookingStatusChartData;
use App\Support\Admin\Dashboard\LatestBookingsData;
use App\Support\Admin\Dashboard\MonthlyRevenueChartData;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends \App\Http\Controllers\Controller
{
    public function index(): View
    {
        return view('admin.dashboard.index', [
            'stats'            => BookingStats::load(),
            'statusChartData'  => BookingStatusChartData::load(),
            'revenueChartData' => MonthlyRevenueChartData::load(),
            'latestBookings'   => LatestBookingsData::load(),
        ]);
    }

    /**
     * Generic placeholder used by stub module routes until each phase implements its own controller.
     */
    public function placeholder(Request $request): View
    {
        return view('admin.placeholder', [
            'title' => $request->route()->defaults['title'] ?? 'Chức năng đang phát triển',
        ]);
    }
}
