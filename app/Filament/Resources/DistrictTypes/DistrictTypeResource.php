<?php

namespace App\Filament\Resources\DistrictTypes;

use App\Filament\Resources\DistrictTypes\Pages\ManageDistrictTypes;
use App\Models\DistrictType;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DistrictTypeResource extends Resource
{
    protected static ?string $model = DistrictType::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Quản lý địa điểm';

    protected static ?string $modelLabel = 'Loại Quận/Huyện';

    protected static ?string $pluralModelLabel = 'Loại Quận/Huyện';

    protected static ?string $navigationLabel = 'Loại địa điểm';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Tên loại địa điểm')
                    ->required()
                    ->maxLength(1000),
                TextInput::make('priority')
                    ->label('Độ ưu tiên')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin loại địa điểm')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Tên loại địa điểm')
                            ->weight('bold'),
                        TextEntry::make('priority')
                            ->label('Độ ưu tiên')
                            ->numeric(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên loại địa điểm')
                    ->searchable(),
                TextColumn::make('priority')
                    ->label('Độ ưu tiên')
                    ->numeric()
                    ->sortable(),
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
            'index' => ManageDistrictTypes::route('/'),
        ];
    }
}
