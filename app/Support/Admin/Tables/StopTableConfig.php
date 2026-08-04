<?php

namespace App\Support\Admin\Tables;

use App\Support\Admin\TableConfig;

class StopTableConfig
{
    public static function make(): TableConfig
    {
        return TableConfig::make()
            ->searchColumns(['name', 'address', fn ($q, $s) => $q->whereHas('district', fn ($d) => $d->where('name', 'like', "%{$s}%"))])
            ->allowSort('name')
            ->allowSort('priority')
            ->allowSort('created_at');
    }
}
