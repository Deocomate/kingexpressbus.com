<?php

namespace App\Filament\Resources\TripBlocks\Schemas;

use App\Models\Route;
use App\Models\Trip;
use App\Models\TripBlock;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TripBlockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin khóa chuyến')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('route_id')
                                ->label('Tuyến đường')
                                ->options(Route::pluck('name', 'id'))
                                ->searchable()
                                ->live()
                                ->dehydrated(false)
                                ->afterStateUpdated(fn (\Filament\Schemas\Components\Utilities\Set $set) => $set('trip_id', null))
                                ->afterStateHydrated(function ($component, $state, ?TripBlock $record) {
                                    if ($record && $record->trip_id) {
                                        $trip = Trip::find($record->trip_id);
                                        if ($trip) {
                                            $component->state($trip->route_id);
                                        }
                                    }
                                }),

                            Select::make('trip_id')
                                ->label('Chuyến xe')
                                ->disabled(fn (Get $get) => ! $get('route_id'))
                                ->options(function (Get $get) {
                                    $routeId = $get('route_id');
                                    if (! $routeId) {
                                        return [];
                                    }

                                    return Trip::with('bus')
                                        ->where('route_id', $routeId)
                                        ->get()
                                        ->mapWithKeys(function ($trip) {
                                            $time = Carbon::parse($trip->start_time)->format('H:i');
                                            $busName = $trip->bus ? $trip->bus->name : 'N/A';
                                            $busModel = $trip->bus ? $trip->bus->model_name : 'N/A';

                                            return [$trip->id => "{$time} - {$busName} ({$busModel})"];
                                        })
                                        ->toArray();
                                })
                                ->searchable()
                                ->required()
                                ->live(),

                            DatePicker::make('start_date')
                                ->label('Từ ngày')
                                ->required()
                                ->displayFormat('d/m/Y')
                                ->native(false),

                            DatePicker::make('end_date')
                                ->label('Đến ngày')
                                ->required()
                                ->displayFormat('d/m/Y')
                                ->native(false)
                                ->afterOrEqual('start_date'),

                            Radio::make('block_type')
                                ->label('Loại khóa')
                                ->options([
                                    'off_day' => 'Ngừng chạy (Tài xế nghỉ, lễ tết)',
                                    'sold_out' => 'Khóa hết chỗ (Đã bao xe hoặc không nhận thêm khách)',
                                ])
                                ->required()
                                ->columnSpanFull(),

                            Textarea::make('note')
                                ->label('Ghi chú')
                                ->maxLength(65535)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
