<?php

namespace App\Filament\Resources\Routes\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RouteStopsRelationManager extends RelationManager
{
    protected static string $relationship = 'routeStops';

    protected static ?string $title = 'Danh sách Điểm dừng';

    protected static ?string $modelLabel = 'Điểm dừng tuyến';

    protected static ?string $pluralModelLabel = 'Điểm dừng tuyến';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('stop_id')
                    ->label('Điểm dừng')
                    ->relationship('stop', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('stop_type')
                    ->label('Kiểu điểm dừng')
                    ->options([
                        'pickup' => 'Đón',
                        'dropoff' => 'Trả',
                        'both' => 'Đón và trả',
                    ])
                    ->default('both')
                    ->required(),
                TextInput::make('priority')
                    ->label('Độ ưu tiên')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin điểm dừng tuyến')
                    ->schema([
                        TextEntry::make('stop.name')
                            ->label('Điểm dừng'),
                        TextEntry::make('stop_type')
                            ->label('Kiểu điểm dừng')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => self::stopTypeLabel($state)),
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
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('stop.name')
            ->columns([
                TextColumn::make('stop.name')
                    ->label('Điểm dừng')
                    ->searchable(),
                TextColumn::make('stop_type')
                    ->label('Kiểu điểm dừng')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::stopTypeLabel($state)),
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
            ->headerActions([
                CreateAction::make()
                    ->label('Thêm điểm dừng')
                    ->slideOver(),
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
            ->reorderable('priority')
            ->defaultSort('priority');
    }

    private static function stopTypeLabel(?string $state): string
    {
        return match ($state) {
            'pickup' => 'Đón',
            'dropoff' => 'Trả',
            'both' => 'Đón và trả',
            default => 'Không xác định',
        };
    }
}
