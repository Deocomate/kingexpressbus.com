<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\WebProfile;
use App\Observers\MenuObserver;
use App\Observers\WebProfileObserver;
use App\Support\ClientWebProfileResolver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        WebProfile::observe(WebProfileObserver::class);
        Menu::observe(MenuObserver::class);

        Paginator::useBootstrap();

        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('booking', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('payment', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));

        View::composer(['client.*', 'components.client.*'], function ($view) {
            if (! $view->offsetExists('web_profile')) {
                $view->with('web_profile', app(ClientWebProfileResolver::class)->resolve());
            }
        });
    }
}
