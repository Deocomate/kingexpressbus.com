<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrap();

        // Logic tạo menu đã được chuyển đi, chỉ cần giữ lại logic lấy web_profile nếu cần ở nơi khác
        // if (Schema::hasTable('web_profiles')) {
        //     View::composer(['layouts.client.*', 'client.*'], function ($view) {
        //         if (!isset($view->web_profile)) {
        //             $web_profile = DB::table('web_profiles')->where('is_default', true)->first();
        //             $view->with(compact('web_profile'));
        //         }
        //     });
        // }
    }
}
