<?php

namespace Database\Seeders\Concerns;

use App\Helpers\SystemHelper;

trait UsesClientAssets
{
    protected function clientImage(string $path): string
    {
        return SystemHelper::clientAsset('images/'.$path);
    }

    protected function clientIcon(string $path): string
    {
        return SystemHelper::clientAsset('icons/'.$path);
    }
}
