<?php

namespace App\Support\Admin\Tables;

use App\Support\Admin\TableConfig;

class ProvinceTableConfig
{
    public static function make(): TableConfig
    {
        return TableConfig::make()
            ->searchColumns(['name', 'slug', 'title'])
            ->allowSort('name')
            ->allowSort('priority')
            ->allowSort('created_at');
    }
}
