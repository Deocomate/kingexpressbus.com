<?php

namespace App\Filament\Resources\Trips\Tables;

use App\Filament\Support\BookingDeleteGuard;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class TripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('route.name')
                    ->label('Tuyến đường')
                    ->searchable(),
                TextColumn::make('bus.name')
                    ->label('Xe')
                    ->searchable(),
                TextColumn::make('start_time')
                    ->label('Giờ xuất bến')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('Giờ đến')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Giá vé')
                    ->money('VND')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Đang hoạt động'),
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
                SelectFilter::make('route_id')
                    ->label('Tuyến đường')
                    ->relationship('route', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('bus_id')
                    ->label('Xe')
                    ->relationship('bus', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->groups([
                Group::make('route.name')
                    ->label('Tuyến đường')
                    ->collapsible(),
            ])
            ->defaultGroup('route.name')
            ->recordActions([
                ViewAction::make()
                    ->label('Xem'),
                EditAction::make()
                    ->label('Sửa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa đã chọn')
                        ->before(function ($records): void {
                            $count = collect($records)->sum(
                                fn ($record) => BookingDeleteGuard::tripBookingCount((int) $record->id),
                            );
                            BookingDeleteGuard::haltIfBookingsExist((int) $count);
                        }),
                ])
                    ->label('Thao tác hàng loạt'),
            ])
            ->reorderable('priority', direction: 'desc')
            ->defaultSort('priority', 'desc');
    }
}
