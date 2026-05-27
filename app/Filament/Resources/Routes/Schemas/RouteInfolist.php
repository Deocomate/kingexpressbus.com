<?php

namespace App\Filament\Resources\Routes\Schemas;

use App\Helpers\SystemHelper;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RouteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin tuyến đường')
                    ->schema([
                        Flex::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('startProvince.name')
                                        ->label('Điểm đầu'),
                                    TextEntry::make('endProvince.name')
                                        ->label('Điểm cuối'),
                                    TextEntry::make('name')
                                        ->label('Tên tuyến')
                                        ->weight('bold')
                                        ->columnSpanFull(),
                                    TextEntry::make('slug')
                                        ->label('Đường dẫn'),
                                    TextEntry::make('title')
                                        ->label('Tiêu đề SEO')
                                        ->placeholder('-'),
                                    TextEntry::make('description')
                                        ->label('Mô tả SEO')
                                        ->placeholder('-')
                                        ->columnSpanFull(),
                                ]),
                            ImageEntry::make('thumbnail_url')
                                ->label('Ảnh đại diện')
                                ->getStateUsing(fn ($record): ?string => SystemHelper::mediaUrl($record->thumbnail_url))
                                ->size(160)
                                ->grow(false),
                        ])->from('md'),
                    ])
                    ->columnSpanFull(),
                Section::make('Vận hành')
                    ->schema([
                        TextEntry::make('duration')
                            ->label('Thời gian di chuyển')
                            ->placeholder('-'),
                        TextEntry::make('distance_km')
                            ->label('Quãng đường')
                            ->suffix(' km')
                            ->numeric()
                            ->placeholder('-'),
                        TextEntry::make('price_default')
                            ->label('Giá mặc định')
                            ->money('VND'),
                        IconEntry::make('available_hotel_pickup')
                            ->label('Đón tại khách sạn')
                            ->boolean(),
                    ])
                    ->columns(2)
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
