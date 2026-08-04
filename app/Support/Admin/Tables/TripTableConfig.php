<?php

namespace App\Support\Admin\Tables;

use App\Support\Admin\TableColumn;
use App\Support\Admin\TableConfig;
use App\Support\Admin\TableTab;
use Illuminate\Support\Facades\DB;

class TripTableConfig
{
    public static function make(): TableConfig
    {
        return TableConfig::make()
            ->columns([
                TableColumn::make('route_name', 'Tuyến đường')->sortable(),
                TableColumn::make('bus_name', 'Xe')->sortable(),
                TableColumn::make('start_time', 'Giờ xuất bến')->sortable(),
                TableColumn::make('end_time', 'Giờ đến')->sortable(),
                TableColumn::make('price', 'Giá vé')->sortable(),
                TableColumn::make('is_active', 'Trạng thái'),
                TableColumn::make('priority', 'Độ ưu tiên')->sortable(),
                TableColumn::make('created_at', 'Ngày tạo')->sortable()->defaultHidden(),
                TableColumn::make('updated_at', 'Cập nhật')->sortable()->defaultHidden(),
            ])
            ->tabs([
                TableTab::make('all', 'Tất cả')
                    ->query(fn ($q) => $q)
                    ->badge(fn () => DB::table('trips')->count()),

                TableTab::make('active', 'Đang hoạt động')
                    ->query(fn ($q) => $q->where('trips.is_active', true))
                    ->badge(fn () => DB::table('trips')->where('is_active', true)->count()),

                TableTab::make('inactive', 'Tạm ngưng')
                    ->query(fn ($q) => $q->where('trips.is_active', false))
                    ->badge(fn () => DB::table('trips')->where('is_active', false)->count()),
            ])
            ->allowSort('start_time', 'trips.start_time')
            ->allowSort('end_time', 'trips.end_time')
            ->allowSort('price', 'trips.price')
            ->allowSort('priority', 'trips.priority')
            ->allowSort('route_name', fn ($q, $dir) => $q->orderBy('routes.name', $dir))
            ->allowSort('bus_name', fn ($q, $dir) => $q->orderBy('buses.name', $dir))
            ->searchColumns([
                fn ($q, $search) => $q->where(function ($sub) use ($search) {
                    $sub->where('routes.name', 'like', '%'.$search.'%')
                        ->orWhere('buses.name', 'like', '%'.$search.'%');
                }),
            ])
            ->perPageOptions([15, 30, 50], 15);
    }
}
