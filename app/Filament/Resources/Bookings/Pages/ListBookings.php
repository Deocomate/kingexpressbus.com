<?php

namespace App\Filament\Resources\Bookings\Pages;

use App\Enums\BookingStatus;
use App\Filament\Resources\Bookings\BookingResource;
use App\Models\Booking;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'upcoming' => Tab::make('Sắp đi')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->whereDate('bookings.booking_date', '>=', now()->toDateString())
                    ->whereIn('bookings.status', [
                        BookingStatus::Pending->value,
                        BookingStatus::Confirmed->value,
                    ]))
                ->badge(fn (): int => Booking::query()
                    ->whereDate('booking_date', '>=', now()->toDateString())
                    ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
                    ->count())
                ->badgeColor('info'),
            'pending' => Tab::make('Chờ xác nhận')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('bookings.status', BookingStatus::Pending->value))
                ->badge(fn (): int => Booking::query()->where('status', BookingStatus::Pending)->count())
                ->badgeColor('warning'),
            'completed' => Tab::make('Đã hoàn thành')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('bookings.status', BookingStatus::Completed->value)),
            'cancelled' => Tab::make('Đã hủy')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query
                    ->where('bookings.status', BookingStatus::Cancelled->value)),
            'all' => Tab::make('Tất cả'),
        ];
    }
}
