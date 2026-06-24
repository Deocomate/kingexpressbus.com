<?php

namespace App\Observers;

use App\Models\Menu;
use App\Support\ClientCache;

class MenuObserver
{
    public function saved(Menu $menu): void
    {
        ClientCache::forgetMenus();
    }

    public function deleted(Menu $menu): void
    {
        ClientCache::forgetMenus();
    }
}
