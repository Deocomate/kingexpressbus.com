<?php

namespace App\Support\Admin\Tables;

use App\Support\Admin\TableColumn;
use App\Support\Admin\TableConfig;
use Illuminate\Support\Facades\DB;

class BusServiceTableConfig
{
    public static function make(): TableConfig
    {
        return TableConfig::make()
            ->columns([
                TableColumn::make('name', 'Tên dịch vụ')->sortable(),
                TableColumn::make('icon', 'Icon'),
                TableColumn::make('bus_count', 'Số xe gắn'),
            ])
            ->allowSort('name')
            ->searchColumns(['name'])
            ->perPageOptions([15, 30, 50]);
    }

    public static function baseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('bus_services as bs')
            ->select([
                'bs.id',
                'bs.name',
                'bs.icon',
                DB::raw('(SELECT COUNT(*) FROM bus_bus_service WHERE bus_service_id = bs.id) as bus_count'),
            ])
            ->orderBy('bs.name');
    }
}
