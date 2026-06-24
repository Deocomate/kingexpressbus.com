<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

final class ClientCache
{
    public const WEB_PROFILE = 'client.web_profile.default';

    public const MENUS = 'client.menus.tree';

    public const SEARCH_LOCATIONS = 'client.search.locations';

    public const ADMIN_DASHBOARD_STATS = 'admin.dashboard.stats';

    public static function forgetWebProfile(): void
    {
        Cache::forget(self::WEB_PROFILE);
    }

    public static function forgetMenus(): void
    {
        Cache::forget(self::MENUS);
    }

    public static function forgetSearchLocations(): void
    {
        Cache::forget(self::SEARCH_LOCATIONS);
    }

    public static function forgetAdminDashboardStats(): void
    {
        Cache::forget(self::ADMIN_DASHBOARD_STATS);
    }
}
