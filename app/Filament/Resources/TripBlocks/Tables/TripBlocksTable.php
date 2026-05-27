<?php

namespace App\Filament\Resources\TripBlocks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TripBlocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trip.route.name')
                    ->label('Tuyến đường')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('trip.start_time')
                    ->label('Giờ chạy')
                    ->time('H:i')
                    ->sortable(),
                TextColumn::make('trip.bus.model_name')
                    ->label('Loại xe'),
                TextColumn::make('trip.bus.name')
                    ->label('Tên xe')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Từ ngày')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Đến ngày')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('block_type')
                    ->label('Loại khóa')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'off_day' => 'warning',
                        'sold_out' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'off_day' => 'Ngừng chạy',
                        'sold_out' => 'Hết chỗ',
                        default => $state,
                    }),
                TextColumn::make('note')
                    ->label('Ghi chú')
                    ->limit(30)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 30 ? $state : null;
                    }),
            ])
            ->filters([
                Filter::make('block_date')
                    ->form([
                        DatePicker::make('date')
                            ->label('Ngày'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date),
                            );
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_date', 'desc');
    }
}
