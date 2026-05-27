<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestBookings extends TableWidget
{
    protected static ?string $heading = 'Đặt vé mới nhất';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Booking::query()->with('trip.route')->latest()->limit(10))
            ->columns([
                TextColumn::make('booking_code')
                    ->label('Mã đặt vé')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable(),
                TextColumn::make('trip.route.name')
                    ->label('Tuyến đường'),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::bookingStatusLabel($state))
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'completed' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('total_price')
                    ->label('Tổng tiền')
                    ->money('VND'),
            ])
            ->paginated(false);
    }

    private static function bookingStatusLabel(?string $state): string
    {
        return match ($state) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã hủy',
            'completed' => 'Hoàn thành',
            default => 'Không xác định',
        };
    }
}
