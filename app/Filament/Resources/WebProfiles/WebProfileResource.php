<?php

namespace App\Filament\Resources\WebProfiles;

use App\Filament\Resources\WebProfiles\Pages\ManageWebProfiles;
use App\Helpers\SystemHelper;
use App\Models\WebProfile;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebProfileResource extends Resource
{
    protected static ?string $model = WebProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Cấu hình website';

    protected static ?string $modelLabel = 'Hồ sơ web';

    protected static ?string $pluralModelLabel = 'Hồ sơ web';

    protected static ?string $navigationLabel = 'Cấu hình website';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin cơ bản')
                    ->schema([
                        TextInput::make('profile_name')
                            ->label('Tên cấu hình')
                            ->required()
                            ->maxLength(1000),
                        Toggle::make('is_default')
                            ->label('Cấu hình mặc định')
                            ->required(),
                        TextInput::make('title')
                            ->label('Tiêu đề website')
                            ->maxLength(1000),
                        TextInput::make('description')
                            ->label('Mô tả website')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Nhận diện')
                    ->schema([
                        FileUpload::make('logo_url')
                            ->label('Logo')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('website')
                            ->image()
                            ->imageEditor()
                            ->fetchFileInformation(false),
                        FileUpload::make('favicon_url')
                            ->label('Favicon')
                            ->disk('public')
                            ->visibility('public')
                            ->directory('website')
                            ->image()
                            ->fetchFileInformation(false),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Liên hệ')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(1000),
                        TextInput::make('phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->maxLength(1000),
                        TextInput::make('hotline')
                            ->label('Hotline')
                            ->tel()
                            ->maxLength(1000),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->maxLength(1000),
                        TextInput::make('address')
                            ->label('Địa chỉ')
                            ->maxLength(1000)
                            ->columnSpanFull(),
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->maxLength(1000),
                        TextInput::make('zalo_url')
                            ->label('Zalo')
                            ->url()
                            ->maxLength(1000),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Nội dung')
                    ->schema([
                        Textarea::make('map_embedded')
                            ->label('Mã nhúng bản đồ')
                            ->rows(4)
                            ->columnSpanFull(),
                        RichEditor::make('policy_content')
                            ->label('Chính sách')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsVisibility('public')
                            ->fileAttachmentsDirectory('website/content')
                            ->columnSpanFull(),
                        RichEditor::make('introduction_content')
                            ->label('Giới thiệu')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsVisibility('public')
                            ->fileAttachmentsDirectory('website/content')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin cơ bản')
                    ->schema([
                        TextEntry::make('profile_name')
                            ->label('Tên cấu hình')
                            ->weight('bold')
                            ->size('lg'),
                        IconEntry::make('is_default')
                            ->label('Cấu hình mặc định')
                            ->boolean(),
                        TextEntry::make('title')
                            ->label('Tiêu đề website')
                            ->placeholder('-'),
                        TextEntry::make('description')
                            ->label('Mô tả website')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Nhận diện')
                    ->schema([
                        ImageEntry::make('logo_url')
                            ->label('Logo')
                            ->getStateUsing(fn ($record): ?string => SystemHelper::mediaUrl($record->logo_url))
                            ->size(120),
                        ImageEntry::make('favicon_url')
                            ->label('Favicon')
                            ->getStateUsing(fn ($record): ?string => SystemHelper::mediaUrl($record->favicon_url))
                            ->size(64),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Liên hệ')
                    ->schema([
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label('Số điện thoại')
                            ->placeholder('-'),
                        TextEntry::make('hotline')
                            ->label('Hotline')
                            ->placeholder('-'),
                        TextEntry::make('whatsapp')
                            ->label('WhatsApp')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label('Địa chỉ')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('facebook_url')
                            ->label('Facebook')
                            ->placeholder('-'),
                        TextEntry::make('zalo_url')
                            ->label('Zalo')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Nội dung')
                    ->schema([
                        TextEntry::make('map_embedded')
                            ->label('Bản đồ nhúng')
                            ->html()
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('policy_content')
                            ->label('Chính sách')
                            ->html()
                            ->prose()
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('introduction_content')
                            ->label('Giới thiệu')
                            ->html()
                            ->prose()
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Cấu hình SEO')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Ngày tạo')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('-'),
                                TextEntry::make('updated_at')
                                    ->label('Ngày cập nhật')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('-'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->getStateUsing(fn ($record): ?string => SystemHelper::mediaUrl($record->logo_url))
                    ->size(48),
                TextColumn::make('profile_name')
                    ->label('Tên cấu hình')
                    ->searchable(),
                IconColumn::make('is_default')
                    ->label('Mặc định')
                    ->boolean(),
                TextColumn::make('title')
                    ->label('Tiêu đề website')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Số điện thoại')
                    ->searchable(),
                TextColumn::make('hotline')
                    ->label('Hotline')
                    ->searchable(),
                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Địa chỉ')
                    ->searchable(),
                TextColumn::make('facebook_url')
                    ->label('Facebook')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('zalo_url')
                    ->label('Zalo')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Action::make('setDefault')
                    ->label('Đặt mặc định')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (WebProfile $record): bool => ! $record->is_default)
                    ->action(function (WebProfile $record): void {
                        $record->update(['is_default' => true]);

                        Notification::make()
                            ->success()
                            ->title('Đã đặt cấu hình mặc định')
                            ->send();
                    }),
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
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWebProfiles::route('/'),
        ];
    }
}
