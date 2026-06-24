<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ClientWebProfileResolver
{
    public function resolve(): ?object
    {
        try {
            return Cache::remember(ClientCache::WEB_PROFILE, 3600, function () {
                return DB::table('web_profiles')->where('is_default', true)->first();
            });
        } catch (\Throwable) {
            return null;
        }
    }
}
