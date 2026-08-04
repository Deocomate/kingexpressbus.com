<?php

namespace App\Support\Admin\Tables;

use App\Support\Admin\TableColumn;
use App\Support\Admin\TableConfig;

class HolidaySurchargeTableConfig
{
    public static function make(): TableConfig
    {
        return TableConfig::make()
            ->columns([
                TableColumn::make('name', 'Tên phụ thu')->sortable(),
                TableColumn::make('start_date', 'Ngày bắt đầu')->sortable(),
                TableColumn::make('end_date', 'Ngày kết thúc')->sortable(),
                TableColumn::make('global_surcharge_amount', 'Phụ thu chung')->sortable(),
                TableColumn::make('is_active', 'Đang áp dụng'),
                TableColumn::make('priority', 'Độ ưu tiên')->sortable(),
                TableColumn::make('created_at', 'Ngày tạo')->sortable()->defaultHidden(),
                TableColumn::make('updated_at', 'Cập nhật')->sortable()->defaultHidden(),
            ])
            ->allowSort('name')
            ->allowSort('start_date')
            ->allowSort('end_date')
            ->allowSort('global_surcharge_amount')
            ->allowSort('priority')
            ->searchColumns(['name', 'reason'])
            ->perPageOptions([15, 30, 50], 15);
    }
}
