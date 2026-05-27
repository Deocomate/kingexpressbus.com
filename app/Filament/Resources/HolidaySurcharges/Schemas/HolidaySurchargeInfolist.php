<?php

namespace App\Filament\Resources\HolidaySurcharges\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HolidaySurchargeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin phụ thu')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Tên phụ thu')
                            ->weight('bold')
                            ->size('lg'),
                        IconEntry::make('is_active')
                            ->label('Đang áp dụng')
                            ->boolean(),
                        TextEntry::make('start_date')
                            ->label('Ngày bắt đầu')
                            ->date('d/m/Y'),
                        TextEntry::make('end_date')
                            ->label('Ngày kết thúc')
                            ->date('d/m/Y'),
                        TextEntry::make('global_surcharge_amount')
                            ->label('Phụ thu chung')
                            ->money('VND'),
                        TextEntry::make('reason')
                            ->label('Lý do')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Thông tin hệ thống')
                    ->schema([
                        TextEntry::make('priority')
                            ->label('Độ ưu tiên')
                            ->numeric(),
                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Ngày cập nhật')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
