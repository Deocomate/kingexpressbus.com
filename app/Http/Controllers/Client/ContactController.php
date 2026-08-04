<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ContactController extends Controller
{
    public function index()
    {
        // Lấy web profile chứa thông tin liên hệ
        $webProfile = DB::table('web_profiles')->where('is_default', true)->first();

        // Lấy danh sách văn phòng/điểm đón trả
        $offices = DB::table('stops as s')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->select([
                's.id',
                's.name',
                's.address',
                'p.name as province_name',
                'd.name as district_name',
            ])
            ->orderByDesc('s.priority')
            ->orderByDesc('p.priority')
            ->limit(10)
            ->get();

        // Lấy thống kê cơ bản (Single-tenant: buses instead of companies)
        $stats = [
            'route_count' => DB::table('routes')->count(),
            'bus_count' => DB::table('buses')->count(),
            'trip_count' => DB::table('trips')->where('is_active', true)->count(),
        ];

        return view('client.contact.index', [
            'webProfile' => $webProfile,
            'offices' => $offices,
            'stats' => $stats,
            'title' => __('client.contact.meta.title'),
            'description' => __('client.contact.meta.description'),
        ]);
    }
}
