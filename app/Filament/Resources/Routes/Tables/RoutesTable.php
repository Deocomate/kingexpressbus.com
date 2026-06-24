<?php

namespace App\Filament\Resources\Routes\Tables;

use App\Filament\Support\BookingDeleteGuard;
use App\Helpers\SystemHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class RoutesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Ảnh')
                    ->getStateUsing(fn ($record): ?string => SystemHelper::mediaUrl($record->thumbnail_url))
                    ->size(48),
                TextColumn::make('startProvince.name')
                    ->label('Điểm đầu')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('endProvince.name')
                    ->label('Điểm cuối')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Tên tuyến')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Đường dẫn')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title')
                    ->label('Tiêu đề SEO')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('duration')
                    ->label('Thời gian')
                    ->searchable(),
                TextColumn::make('distance_km')
                    ->label('Quãng đường')
                    ->suffix(' km')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('price_default')
                    ->label('Giá mặc định')
                    ->money('VND')
                    ->sortable(),
                IconColumn::make('available_hotel_pickup')
                    ->label('Đón khách sạn')
                    ->boolean(),
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
                SelectFilter::make('province_start_id')
                    ->label('Điểm đầu')
                    ->relationship('startProvince', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('province_end_id')
                    ->label('Điểm cuối')
                    ->relationship('endProvince', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->groups([
                Group::make('startProvince.name')
                    ->label('Tỉnh/thành đi')
                    ->collapsible(),
            ])
            ->defaultGroup('startProvince.name')
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
                                fn ($record) => BookingDeleteGuard::routeBookingCount((int) $record->id),
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
