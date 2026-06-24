<?php

namespace App\Filament\Resources\Stops;

use App\Filament\Resources\Stops\Pages\ManageStops;
use App\Filament\Support\BookingDeleteGuard;
use App\Models\Stop;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StopResource extends Resource
{
    protected static ?string $model = Stop::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Quản lý địa điểm';

    protected static ?string $modelLabel = 'Điểm dừng';

    protected static ?string $pluralModelLabel = 'Điểm dừng';

    protected static ?string $navigationLabel = 'Điểm dừng';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['district']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'address'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin điểm dừng')
                    ->schema([
                        Select::make('district_id')
                            ->label('Địa điểm')
                            ->relationship('district', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Tên điểm dừng')
                            ->required()
                            ->maxLength(1000),
                        TextInput::make('address')
                            ->label('Địa chỉ')
                            ->required()
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
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin điểm dừng')
                    ->schema([
                        TextEntry::make('district.name')
                            ->label('Địa điểm'),
                        TextEntry::make('name')
                            ->label('Tên điểm dừng')
                            ->weight('bold'),
                        TextEntry::make('address')
                            ->label('Địa chỉ')
                            ->columnSpanFull(),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('district.name')
                    ->label('Địa điểm')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Tên điểm dừng')
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Địa chỉ')
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
                SelectFilter::make('district_id')
                    ->label('Địa điểm')
                    ->relationship('district', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Xem')
                    ->slideOver(),
                EditAction::make()
                    ->label('Sửa')
                    ->slideOver(),
                DeleteAction::make()
                    ->label('Xóa')
                    ->before(function (Stop $record): void {
                        BookingDeleteGuard::haltIfBookingsExist(
                            BookingDeleteGuard::stopBookingCount((int) $record->id),
                        );
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa đã chọn')
                        ->before(function ($records): void {
                            $count = collect($records)->sum(
                                fn ($record) => BookingDeleteGuard::stopBookingCount((int) $record->id),
                            );
                            BookingDeleteGuard::haltIfBookingsExist((int) $count);
                        }),
                ])
                    ->label('Thao tác hàng loạt'),
            ])
            ->reorderable('priority', direction: 'desc')
            ->defaultSort('priority', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStops::route('/'),
        ];
    }
}
