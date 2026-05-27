<?php

namespace App\Filament\Resources\Menus\Schemas;

use App\Models\Route;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MenuForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin menu')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên menu')
                            ->required()
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label('Loại menu')
                            ->required()
                            ->options([
                                'custom_link' => 'Liên kết tùy chỉnh',
                                'route' => 'Tuyến đường',
                                'page' => 'Trang',
                                'system_page' => 'Trang hệ thống',
                            ])
                            ->default('custom_link')
                            ->live(),

                        // 1. Nhập URL nếu là Custom Link, Page hoặc System Page
                        TextInput::make('url')
                            ->label(fn (Get $get) => match ($get('type')) {
                                'page' => 'Đường dẫn trang (Slug)',
                                'system_page' => 'Đường dẫn hệ thống',
                                default => 'Đường dẫn (URL)',
                            })
                            ->placeholder(fn (Get $get) => match ($get('type')) {
                                'page' => 'vd: gioi-thieu',
                                'system_page' => 'vd: /dat-ve',
                                default => 'https://...',
                            })
                            ->maxLength(1000)
                            ->required(fn (Get $get) => in_array($get('type'), ['custom_link', 'page', 'system_page']))
                            ->visible(fn (Get $get) => in_array($get('type'), ['custom_link', 'page', 'system_page'])),

                        // 2. Chọn Tuyến đường nếu loại là Route
                        Select::make('related_id')
                            ->label('Chọn Tuyến đường')
                            ->options(fn (): array => Route::pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get) => $get('type') === 'route')
                            ->visible(fn (Get $get) => $get('type') === 'route'),

                        TextInput::make('priority')
                            ->label('Độ ưu tiên')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->helperText('Hệ thống tự động xếp khi bạn kéo thả ở ngoài màn hình cây Menu.'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
