<?php

namespace App\Support\Admin\Tables;

use App\Support\Admin\TableConfig;

class DistrictTypeTableConfig
{
    public static function make(): TableConfig
    {
        return TableConfig::make()
            ->searchColumns(['name'])
            ->allowSort('name')
            ->allowSort('priority');
    }
}
