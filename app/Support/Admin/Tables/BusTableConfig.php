<?php

namespace App\Support\Admin\Tables;

use App\Support\Admin\TableColumn;
use App\Support\Admin\TableConfig;
use Illuminate\Support\Facades\DB;

class BusTableConfig
{
    public static function make(): TableConfig
    {
        return TableConfig::make()
            ->columns([
                TableColumn::make('thumbnail', 'Ảnh'),
                TableColumn::make('name', 'Tên xe')->sortable(),
                TableColumn::make('model_name', 'Dòng xe')->sortable(),
                TableColumn::make('seat_count', 'Số ghế')->sortable(),
                TableColumn::make('services', 'Dịch vụ'),
                TableColumn::make('priority', 'Ưu tiên')->sortable(),
            ])
            ->allowSort('name')
            ->allowSort('model_name')
            ->allowSort('seat_count')
            ->allowSort('priority')
            ->searchColumns(['b.name', 'b.model_name'])
            ->perPageOptions([15, 30, 50, 100]);
    }

    public static function baseQuery(): \Illuminate\Database\Query\Builder
    {
        return DB::table('buses as b')
            ->select([
                'b.id',
                'b.name',
                'b.model_name',
                'b.seat_count',
                'b.thumbnail_url',
                'b.priority',
            ])
            ->orderByDesc('b.priority');
    }
}
