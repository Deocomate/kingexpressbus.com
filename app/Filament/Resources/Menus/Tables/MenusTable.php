<?php

namespace App\Filament\Resources\Menus\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MenusTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên menu')
                    ->searchable(),
                TextColumn::make('url')
                    ->label('Đường dẫn')
                    ->searchable(),
                TextColumn::make('parent.name')
                    ->label('Menu cha')
                    ->searchable(),
                TextColumn::make('priority')
                    ->label('Độ ưu tiên')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Loại menu')
                    ->formatStateUsing(fn (?string $state): string => self::menuTypeLabel($state))
                    ->searchable(),
                TextColumn::make('related_id')
                    ->label('ID liên kết')
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
                SelectFilter::make('type')
                    ->label('Loại menu')
                    ->options([
                        'custom_link' => 'Liên kết tùy chỉnh',
                        'route' => 'Tuyến đường',
                        'page' => 'Trang',
                        'system_page' => 'Trang hệ thống',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Xem')
                    ->slideOver(),
                EditAction::make()
                    ->label('Sửa')
                    ->slideOver(),
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

    private static function menuTypeLabel(?string $state): string
    {
        return match ($state) {
            'custom_link' => 'Liên kết tùy chỉnh',
            'route' => 'Tuyến đường',
            'page' => 'Trang',
            'system_page' => 'Trang hệ thống',
            default => 'Không xác định',
        };
    }
}
