<?php

namespace App\Filament\Resources\Provinces;

use App\Filament\Resources\Provinces\Pages\ManageProvinces;
use App\Helpers\SystemHelper;
use App\Models\Province;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProvinceResource extends Resource
{
    protected static ?string $model = Province::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Quản lý địa điểm';

    protected static ?string $modelLabel = 'Tỉnh/Thành phố';

    protected static ?string $pluralModelLabel = 'Tỉnh/Thành phố';

    protected static ?string $navigationLabel = 'Tỉnh/thành';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tên tỉnh/thành')
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
                            ->maxLength(1000)
                            ->columnSpanFull(),
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
                            ->directory('provinces/thumbnails')
                            ->image()
                            ->imageEditor()
                            ->fetchFileInformation(false),
                        FileUpload::make('image_list_url')
                            ->label('Album ảnh')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('provinces/albums')
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
                            ->fileAttachmentsDirectory('provinces/content')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin tỉnh/thành')
                    ->schema([
                        Flex::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Tên tỉnh/thành')
                                        ->weight('bold')
                                        ->size('lg'),
                                    TextEntry::make('slug')
                                        ->label('Đường dẫn'),
                                    TextEntry::make('title')
                                        ->label('Tiêu đề SEO')
                                        ->placeholder('-'),
                                    TextEntry::make('description')
                                        ->label('Mô tả SEO')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Ảnh')
                    ->getStateUsing(fn ($record): ?string => SystemHelper::mediaUrl($record->thumbnail_url))
                    ->size(48),
                TextColumn::make('name')
                    ->label('Tên tỉnh/thành')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Đường dẫn')
                    ->searchable(),
                TextColumn::make('title')
                    ->label('Tiêu đề SEO')
                    ->searchable(),
                TextColumn::make('priority')
                    ->label('Độ ưu tiên')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Xem')
                    ->slideOver(),
                EditAction::make()
                    ->label('Sửa')
                    ->slideOver(),
                DeleteAction::make()
                    ->label('Xóa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                ])
                    ->label('Thao tác hàng loạt'),
            ])
            ->reorderable('priority', direction: 'desc')
            ->defaultSort('priority', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProvinces::route('/'),
        ];
    }
}
