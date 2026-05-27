<?php

namespace App\Filament\Resources\Buses\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BusForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin xe')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên xe')
                            ->required()
                            ->maxLength(1000),
                        TextInput::make('model_name')
                            ->label('Dòng xe')
                            ->maxLength(1000),
                        Select::make('services')
                            ->label('Dịch vụ')
                            ->relationship('services', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload(),
                        TextInput::make('seat_count')
                            ->label('Số ghế khả dụng')
                            ->numeric()
                            ->required()
                            ->minValue(1),
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
                            ->directory('buses/thumbnails')
                            ->image()
                            ->imageEditor()
                            ->fetchFileInformation(false),
                        FileUpload::make('image_list_url')
                            ->label('Album ảnh')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('buses/albums')
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->image()
                            ->imageEditor()
                            ->panelLayout('grid')
                            ->maxFiles(10)
                            ->fetchFileInformation(false),
                        RichEditor::make('content')
                            ->label('Nội dung')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsVisibility('public')
                            ->fileAttachmentsDirectory('buses/content')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
