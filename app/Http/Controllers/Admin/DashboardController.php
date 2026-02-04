<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Hiển thị trang dashboard của admin với các số liệu thống kê.
     * Updated for single-tenant schema.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // 1. Thống kê tổng quan (Single-tenant: buses/trips instead of companies)
        $totalBookings = DB::table('bookings')->count();
        $totalRevenue = DB::table('bookings')->whereIn('status', ['confirmed', 'completed'])->sum('total_price');
        $totalBuses = DB::table('buses')->count();
        $totalTrips = DB::table('trips')->where('is_active', true)->count();
        $totalRoutes = DB::table('routes')->count();
        $totalCustomers = DB::table('users')->where('role', 'customer')->count();

        // 2. Dữ liệu cho biểu đồ trạng thái đặt vé (Doughnut Chart)
        $bookingStatusStats = DB::table('bookings')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $bookingStatusLabels = $bookingStatusStats->pluck('status')->map(function ($status) {
            $statusMap = [
                'pending' => 'Chờ xác nhận',
                'confirmed' => 'Đã xác nhận',
                'cancelled' => 'Đã hủy',
                'completed' => 'Hoàn thành',
            ];
            return $statusMap[$status] ?? ucfirst($status);
        });
        $bookingStatusData = $bookingStatusStats->pluck('count');

        // 3. Dữ liệu cho biểu đồ doanh thu 12 tháng gần nhất (Bar Chart)
        $monthlyRevenueLabels = [];
        $monthlyRevenueData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyRevenueLabels[] = 'Tháng ' . $date->format('m/Y');
            $revenue = DB::table('bookings')
                ->whereIn('status', ['confirmed', 'completed'])
                ->whereYear('booking_date', $date->year)
                ->whereMonth('booking_date', $date->month)
                ->sum('total_price');
            $monthlyRevenueData[] = $revenue;
        }

        // 4. Lấy 10 đặt vé mới nhất (Updated for single-tenant schema)
        $latestBookings = DB::table('bookings as b')
            ->join('trips as t', 'b.trip_id', '=', 't.id')
            ->join('routes as r', 't.route_id', '=', 'r.id')
            ->select(
                'b.booking_code',
                'b.customer_name',
                'b.status',
                'b.booking_date',
                'b.total_price',
                'r.name as route_name'
            )
            ->orderByDesc('b.created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalBookings',
            'totalRevenue',
            'totalBuses',
            'totalTrips',
            'totalRoutes',
            'totalCustomers',
            'bookingStatusLabels',
            'bookingStatusData',
            'monthlyRevenueLabels',
            'monthlyRevenueData',
            'latestBookings'
        ));
    }
}
