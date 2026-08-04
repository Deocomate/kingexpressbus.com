<?php

namespace App\Support\Admin\Tables;

use App\Support\Admin\TableColumn;
use App\Support\Admin\TableConfig;
use Illuminate\Support\Facades\DB;

class RouteTableConfig
{
    public static function make(): TableConfig
    {
        return TableConfig::make()
            ->columns([
                TableColumn::make('thumbnail', 'Ảnh')->hideable()->defaultHidden(false),
                TableColumn::make('name', 'Tên tuyến')->sortable(),
                TableColumn::make('provinces', 'Tỉnh đầu → Tỉnh cuối'),
                TableColumn::make('price_default', 'Giá mặc định')->sortable(),
                TableColumn::make('distance_km', 'Km')->sortable(),
                TableColumn::make('available_hotel_pickup', 'Đón khách sạn'),
                TableColumn::make('priority', 'Ưu tiên')->sortable(),
            ])
            ->allowSort('name')
            ->allowSort('price_default')
            ->allowSort('distance_km')
            ->allowSort('priority')
            ->searchColumns(['r.name', 'ps.name', 'pe.name'])
            ->perPageOptions([15, 30, 50, 100]);
    }

    public static function baseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('routes as r')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->select([
                'r.id',
                'r.name',
                'r.slug',
                'r.thumbnail_url',
                'r.price_default',
                'r.distance_km',
                'r.available_hotel_pickup',
                'r.priority',
                'r.province_start_id',
                'r.province_end_id',
                'ps.name as start_province_name',
                'pe.name as end_province_name',
            ])
            ->orderByDesc('r.priority');
    }
}
