<?php

namespace App\Filament\Resources\Buses\Schemas;

use App\Helpers\SystemHelper;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BusInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin xe')
                    ->schema([
                        Flex::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Tên xe')
                                        ->weight('bold')
                                        ->size('lg'),
                                    TextEntry::make('model_name')
                                        ->label('Dòng xe')
                                        ->placeholder('-'),
                                    TextEntry::make('seat_count')
                                        ->label('Số ghế khả dụng')
                                        ->numeric(),
                                    TextEntry::make('services.name')
                                        ->label('Dịch vụ')
                                        ->badge()
                                        ->separator(',')
                                        ->placeholder('-'),
                                ]),
                            ImageEntry::make('thumbnail_url')
                                ->label('Ảnh đại diện')
                                ->getStateUsing(fn ($record): ?string => SystemHelper::mediaUrl($record->thumbnail_url))
                                ->size(160)
                                ->grow(false),
                        ])->from('md'),
                    ])
                    ->columnSpanFull(),
                Section::make('Nội dung')
                    ->schema([
                        TextEntry::make('content')
                            ->label('Nội dung chi tiết')
                            ->html()
                            ->prose()
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
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
