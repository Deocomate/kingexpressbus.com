<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $normalizedSlug = Str::slug($slug);
        $page = null;

        if (Schema::hasTable('pages')) {
            $page = DB::table('pages')->where('slug', $normalizedSlug)->first();
        }

        if (!$page) {
            $page = $this->resolveFallbackPage($normalizedSlug);
        }

        abort_if(!$page, 404);

        $description = $page->description ?? $this->excerptFromContent($page->content ?? '');

        return view('client.page.show', [
            'page' => $page,
            'title' => $page->title ?: 'Trang nội dung',
            'description' => Str::limit($description, 155),
        ]);
    }

    private function resolveFallbackPage(string $slug): ?object
    {
        $webProfile = DB::table('web_profiles')->where('is_default', true)->first();
        if (!$webProfile) {
            return null;
        }

        $fallbacks = [
            'gioi-thieu' => [
                'title' => __('client.about.meta.title'),
                'description' => __('client.about.meta.description'),
                'content' => $webProfile->introduction_content,
                'updated_at' => $webProfile->updated_at ?? null,
            ],
            'chinh-sach' => [
                'title' => 'Chính sách hỗ trợ khách hàng',
                'description' => 'Tổng hợp chính sách đặt vé, thanh toán và hỗ trợ khách hàng của King Express Bus.',
                'content' => $webProfile->policy_content,
                'updated_at' => $webProfile->updated_at ?? null,
            ],
        ];

        if (!isset($fallbacks[$slug])) {
            return null;
        }

        $config = $fallbacks[$slug];
        if (empty($config['content'])) {
            return null;
        }

        return (object) [
            'title' => $config['title'],
            'slug' => $slug,
            'description' => $config['description'],
            'content' => $config['content'],
            'updated_at' => $config['updated_at'],
        ];
    }

    private function excerptFromContent(string $content): string
    {
        $plain = trim(strip_tags($content));
        return $plain === '' ? 'Nội dung tổng hợp của King Express Bus.' : $plain;
    }

    public function about()
    {
        // Lấy các số liệu thống kê động từ DB (Single-tenant: buses instead of companies)
        $stats = [
            'route_count' => DB::table('routes')->count(),
            'bus_count' => DB::table('buses')->count(),
            'trip_count' => DB::table('trips')->where('is_active', true)->count(),
            'customer_count' => DB::table('bookings')->distinct('customer_email')->count('customer_email'),
            'booking_count' => DB::table('bookings')->count(),
            'years_experience' => now()->year - 2017,
        ];

        // Lấy 4 popular routes với thống kê từ trips table
        $popularRoutes = DB::table('routes')
            ->leftJoin('trips', 'routes.id', '=', 'trips.route_id')
            ->select(
                'routes.id',
                'routes.name',
                'routes.slug',
                'routes.thumbnail_url',
                'routes.duration'
            )
            ->selectRaw('COUNT(trips.id) as trip_count')
            ->selectRaw('COALESCE(MIN(NULLIF(trips.price, 0)), routes.price_default, 0) as min_price')
            ->groupBy('routes.id', 'routes.name', 'routes.slug', 'routes.thumbnail_url', 'routes.duration', 'routes.price_default')
            ->orderByDesc('trip_count')
            ->limit(4)
            ->get();

        return view('client.about.index', [
            'stats' => $stats,
            'popularRoutes' => $popularRoutes,
            'title' => __('client.about.meta.title'),
            'description' => __('client.about.meta.description'),
        ]);
    }
}
