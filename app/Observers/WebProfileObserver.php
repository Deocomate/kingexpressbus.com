<?php

namespace App\Observers;

use App\Models\WebProfile;
use App\Support\ClientCache;

class WebProfileObserver
{
    public function saved(WebProfile $webProfile): void
    {
        ClientCache::forgetWebProfile();
    }

    public function deleted(WebProfile $webProfile): void
    {
        ClientCache::forgetWebProfile();
    }
}
