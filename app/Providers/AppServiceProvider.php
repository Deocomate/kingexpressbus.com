<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\WebProfile;
use App\Observers\MenuObserver;
use App\Observers\WebProfileObserver;
use App\Support\Admin\OptionSources\Buses;
use App\Support\Admin\OptionSources\Districts;
use App\Support\Admin\OptionSources\LocationOptionSource;
use App\Support\Admin\OptionSources\OptionSourceRegistry;
use App\Support\Admin\OptionSources\Provinces;
use App\Support\Admin\OptionSources\Routes;
use App\Support\Admin\OptionSources\Stops;
use App\Support\Admin\OptionSources\Trips;
use App\Support\Admin\OptionSources\Users;
use App\Support\ClientWebProfileResolver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Blade;
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
        // Direct AuthenticateSession to the correct login route per area.
        // Without this, Laravel defaults to route('login') which doesn't exist.
        AuthenticateSession::redirectUsing(function (Request $request): string {
            if ($request->is('quan-tri*')) {
                return route('admin.login');
            }

            return route('client.login');
        });

        WebProfile::observe(WebProfileObserver::class);
        Menu::observe(MenuObserver::class);

        Paginator::useBootstrap();

        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('booking', fn (Request $request) => Limit::perMinute(10)->by($request->ip()));
        RateLimiter::for('payment', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));

        // Admin auth limiter is independent from the public portal limiter above.
        // Dual keys prevent both IP-only exhaustion (shared NAT) and account enumeration.
        RateLimiter::for('admin-auth', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip().'|'.\Illuminate\Support\Str::lower($request->input('email', ''))),
            Limit::perMinute(20)->by($request->ip()),
        ]);

        // Register x-admin::* anonymous components (Laravel requires :: with path prefix)
        Blade::anonymousComponentPath(resource_path('views/admin/components'), 'admin');

        // Register admin option sources (P3 users + P4–P8 module sources)
        OptionSourceRegistry::register('users', Users::class);
        OptionSourceRegistry::register('trips', Trips::class);
        OptionSourceRegistry::register('routes', Routes::class);
        OptionSourceRegistry::register('buses', Buses::class);
        OptionSourceRegistry::register('stops', Stops::class);
        OptionSourceRegistry::register('provinces', Provinces::class);
        OptionSourceRegistry::register('districts', Districts::class);
        OptionSourceRegistry::register('district-types', LocationOptionSource::class);

        View::composer(['client.*', 'components.client.*'], function ($view) {
            if (! $view->offsetExists('web_profile')) {
                $view->with('web_profile', app(ClientWebProfileResolver::class)->resolve());
            }
        });
    }
}
