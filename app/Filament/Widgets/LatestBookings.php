<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Support\DepartureAtExpression;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LatestBookings extends TableWidget
{
    protected static ?string $heading = 'Vé sắp khởi hành';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Booking::query()
                ->with(['trip.route'])
                ->leftJoin('trips', 'trips.id', '=', 'bookings.trip_id')
                ->select('bookings.*')
                ->addSelect(DB::raw(DepartureAtExpression::asSelect()))
                ->whereDate('bookings.booking_date', '>=', now()->toDateString())
                ->whereIn('bookings.status', [BookingStatus::Pending, BookingStatus::Confirmed])
                ->orderBy('bookings.booking_date')
                ->orderBy('trips.start_time')
                ->limit(10))
            ->columns([
                TextColumn::make('booking_code')
                    ->label('Mã đặt vé'),
                TextColumn::make('departure_at')
                    ->label('Giờ khởi hành')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('customer_name')
                    ->label('Khách hàng'),
                TextColumn::make('trip.route.name')
                    ->label('Tuyến đường'),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge(),
                TextColumn::make('total_price')
                    ->label('Tổng tiền')
                    ->money('VND'),
            ])
            ->paginated(false);
    }
}
