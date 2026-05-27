<?php

namespace App\Filament\Resources\Buses\Tables;

use App\Helpers\SystemHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BusesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Ảnh')
                    ->getStateUsing(fn ($record): ?string => SystemHelper::mediaUrl($record->thumbnail_url))
                    ->size(56),
                TextColumn::make('name')
                    ->label('Tên xe')
                    ->searchable(),
                TextColumn::make('model_name')
                    ->label('Dòng xe')
                    ->searchable(),
                TextColumn::make('services.name')
                    ->label('Dịch vụ')
                    ->badge()
                    ->separator(',')
                    ->toggleable(),
                TextColumn::make('seat_count')
                    ->label('Số ghế')
                    ->numeric()
                    ->sortable(),
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
                    ->label('Xem'),
                EditAction::make()
                    ->label('Sửa'),
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
}
