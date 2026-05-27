<?php

namespace App\Filament\Resources\Trips\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lịch chạy')
                    ->schema([
                        Select::make('route_id')
                            ->label('Tuyến đường')
                            ->relationship('route', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('bus_id')
                            ->label('Xe')
                            ->relationship('bus', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TimePicker::make('start_time')
                            ->label('Giờ xuất bến')
                            ->seconds(false)
                            ->required(),
                        TimePicker::make('end_time')
                            ->label('Giờ đến')
                            ->seconds(false)
                            ->required(),
                        TextInput::make('price')
                            ->label('Giá vé')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('VND'),
                        Toggle::make('is_active')
                            ->label('Đang hoạt động')
                            ->required(),
                        TextInput::make('priority')
                            ->label('Độ ưu tiên')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
