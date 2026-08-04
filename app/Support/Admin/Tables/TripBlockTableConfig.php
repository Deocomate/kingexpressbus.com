<?php

namespace App\Support\Admin\Tables;

use App\Support\Admin\TableColumn;
use App\Support\Admin\TableConfig;

class TripBlockTableConfig
{
    public static function make(): TableConfig
    {
        return TableConfig::make()
            ->columns([
                TableColumn::make('route_name', 'Tuyến đường')->sortable(),
                TableColumn::make('trip_label', 'Chuyến xe'),
                TableColumn::make('start_date', 'Từ ngày')->sortable(),
                TableColumn::make('end_date', 'Đến ngày')->sortable(),
                TableColumn::make('block_type', 'Loại khóa'),
                TableColumn::make('note', 'Ghi chú'),
            ])
            ->allowSort('start_date', 'trip_blocks.start_date')
            ->allowSort('end_date', 'trip_blocks.end_date')
            ->allowSort('route_name', fn ($q, $dir) => $q->orderBy('routes.name', $dir))
            ->searchColumns([
                fn ($q, $search) => $q->where(function ($sub) use ($search) {
                    $sub->where('routes.name', 'like', '%'.$search.'%')
                        ->orWhere('buses.name', 'like', '%'.$search.'%');
                }),
            ])
            ->perPageOptions([15, 30, 50], 15);
    }
}
