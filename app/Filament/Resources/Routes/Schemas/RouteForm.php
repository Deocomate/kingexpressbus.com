<?php

namespace App\Filament\Resources\Routes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RouteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tuyến đường')
                    ->schema([
                        Select::make('province_start_id')
                            ->label('Điểm đầu')
                            ->relationship('startProvince', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('province_end_id')
                            ->label('Điểm cuối')
                            ->relationship('endProvince', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Tên tuyến')
                            ->required()
                            ->maxLength(1000)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state): mixed => $set('slug', Str::slug($state ?? ''))),
                        TextInput::make('slug')
                            ->label('Đường dẫn')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('title')
                            ->label('Tiêu đề SEO')
                            ->maxLength(1000),
                        TextInput::make('description')
                            ->label('Mô tả SEO')
                            ->maxLength(1000),
                        TextInput::make('duration')
                            ->label('Thời gian di chuyển')
                            ->maxLength(1000),
                        TextInput::make('distance_km')
                            ->label('Quãng đường')
                            ->numeric()
                            ->suffix('km'),
                        TextInput::make('price_default')
                            ->label('Giá mặc định')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('VND'),
                        Toggle::make('available_hotel_pickup')
                            ->label('Đón tại khách sạn')
                            ->required(),
                        TextInput::make('priority')
                            ->label('Độ ưu tiên')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Hình ảnh & Video')
                    ->schema([
                        FileUpload::make('thumbnail_url')
                            ->label('Ảnh đại diện')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('routes/thumbnails')
                            ->image()
                            ->imageEditor()
                            ->fetchFileInformation(false),
                        FileUpload::make('image_list_url')
                            ->label('Album ảnh')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('routes/albums')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->panelLayout('grid')
                            ->fetchFileInformation(false),
                        RichEditor::make('content')
                            ->label('Nội dung')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsVisibility('public')
                            ->fileAttachmentsDirectory('routes/content')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
