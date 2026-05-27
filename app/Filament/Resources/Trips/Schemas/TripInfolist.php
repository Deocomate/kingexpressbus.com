<?php

namespace App\Filament\Resources\Trips\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TripInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin lịch trình')
                    ->schema([
                        TextEntry::make('route.name')
                            ->label('Tuyến đường')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('bus.name')
                            ->label('Xe được gán')
                            ->placeholder('-'),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('start_time')
                                    ->label('Giờ xuất bến')
                                    ->time('H:i'),
                                TextEntry::make('end_time')
                                    ->label('Giờ đến')
                                    ->time('H:i'),
                            ]),
                        TextEntry::make('price')
                            ->label('Giá vé')
                            ->money('VND')
                            ->color('success'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Trạng thái và thông tin hệ thống')
                    ->schema([
                        IconEntry::make('is_active')
                            ->label('Trạng thái')
                            ->boolean(),
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
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
