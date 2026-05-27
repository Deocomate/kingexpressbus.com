<?php

namespace App\Filament\Resources\HolidaySurcharges\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HolidaySurchargeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cấu hình phụ thu')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên phụ thu')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label('Đang áp dụng')
                            ->required(),
                        DatePicker::make('start_date')
                            ->label('Ngày bắt đầu')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->native(false),
                        DatePicker::make('end_date')
                            ->label('Ngày kết thúc')
                            ->required()
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->afterOrEqual('start_date'),
                        TextInput::make('global_surcharge_amount')
                            ->label('Phụ thu chung')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('VND'),
                        TextInput::make('priority')
                            ->label('Độ ưu tiên')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Textarea::make('reason')
                            ->label('Lý do')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Ngoại lệ theo tuyến')
                    ->schema([
                        Repeater::make('routeAdjustments')
                            ->label('Phụ thu theo tuyến')
                            ->relationship()
                            ->schema([
                                Select::make('route_id')
                                    ->label('Tuyến đường')
                                    ->relationship('route', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('route_surcharge_amount')
                                    ->label('Phụ thu tuyến')
                                    ->numeric()
                                    ->required()
                                    ->default(0)
                                    ->suffix('VND'),
                            ])
                            ->columns(2)
                            ->reorderable(false)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
