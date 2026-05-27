<?php

namespace App\Filament\Resources\Menus\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin menu')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Tên menu')
                            ->weight('bold'),
                        TextEntry::make('type')
                            ->label('Loại menu')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::menuTypeLabel($state)),
                        TextEntry::make('url')
                            ->label('Đường dẫn')
                            ->placeholder('-'),
                        TextEntry::make('parent.name')
                            ->label('Menu cha')
                            ->placeholder('-'),
                        TextEntry::make('related_id')
                            ->label('ID liên kết')
                            ->numeric()
                            ->placeholder('-'),
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

    private static function menuTypeLabel(?string $state): string
    {
        return match ($state) {
            'custom_link' => 'Liên kết tùy chỉnh',
            'route' => 'Tuyến đường',
            'page' => 'Trang',
            'system_page' => 'Trang hệ thống',
            default => 'Không xác định',
        };
    }
}
