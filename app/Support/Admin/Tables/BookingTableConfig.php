<?php

namespace App\Support\Admin\Tables;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Support\Admin\TableColumn;
use App\Support\Admin\TableConfig;
use App\Support\Admin\TableTab;
use App\Support\DepartureAtExpression;

/**
 * Table configuration for the admin booking list.
 * 5 tabs: upcoming (default), pending, completed, cancelled, all.
 * Badge counts match Filament ListBookings implementation.
 */
class BookingTableConfig
{
    public static function make(): TableConfig
    {
        return TableConfig::make()
            ->tabs(self::tabs())
            ->columns(self::columns())
            ->searchColumns(self::searchColumns())
            ->allowSort('created_at', fn ($q, $dir) => $q->reorder()->orderBy('bookings.created_at', $dir))
            ->allowSort('departure_at', fn ($q, $dir) => $q
                ->reorder()
                ->orderBy('bookings.booking_date', $dir)
                ->orderByRaw(DepartureAtExpression::tripStartTimeSubquery().' '.$dir)
            )
            ->allowSort('total_price', fn ($q, $dir) => $q->reorder()->orderBy('bookings.total_price', $dir))
            ->allowSort('quantity', fn ($q, $dir) => $q->reorder()->orderBy('bookings.quantity', $dir))
            ->allowSort('booking_date', fn ($q, $dir) => $q->reorder()->orderBy('bookings.booking_date', $dir))
            ->allowSort('confirmed_at', fn ($q, $dir) => $q->reorder()->orderBy('bookings.confirmed_at', $dir))
            ->allowSort('updated_at', fn ($q, $dir) => $q->reorder()->orderBy('bookings.updated_at', $dir));
    }

    /** @return TableTab[] — port from ListBookings::getTabs() */
    private static function tabs(): array
    {
        return [
            TableTab::make('upcoming', 'Sắp đi')
                ->query(fn ($q) => $q
                    ->whereDate('bookings.booking_date', '>=', now()->toDateString())
                    ->whereIn('bookings.status', [
                        BookingStatus::Pending->value,
                        BookingStatus::Confirmed->value,
                    ]))
                ->badge(fn () => Booking::query()
                    ->whereDate('booking_date', '>=', now()->toDateString())
                    ->whereIn('status', [BookingStatus::Pending, BookingStatus::Confirmed])
                    ->count()),

            TableTab::make('pending', 'Chờ xác nhận')
                ->query(fn ($q) => $q->where('bookings.status', BookingStatus::Pending->value))
                ->badge(fn () => Booking::query()->where('status', BookingStatus::Pending)->count()),

            TableTab::make('completed', 'Đã hoàn thành')
                ->query(fn ($q) => $q->where('bookings.status', BookingStatus::Completed->value)),

            TableTab::make('cancelled', 'Đã hủy')
                ->query(fn ($q) => $q->where('bookings.status', BookingStatus::Cancelled->value)),

            TableTab::make('all', 'Tất cả'),
        ];
    }

    /** @return TableColumn[] — 15 columns, 10 visible + 5 hidden by default */
    private static function columns(): array
    {
        return [
            TableColumn::make('booking_code', 'Mã đặt vé')->hideable(false),
            TableColumn::make('created_at', 'Ngày đặt vé')->sortable(),
            TableColumn::make('departure_at', 'Ngày đi')->sortable(),
            TableColumn::make('trip_route', 'Tuyến đường'),
            TableColumn::make('customer_name', 'Khách hàng'),
            TableColumn::make('pickup_dropoff', 'Đón → Trả'),
            TableColumn::make('quantity', 'Số vé')->sortable(),
            TableColumn::make('total_price', 'Tổng tiền')->sortable(),
            TableColumn::make('status', 'Trạng thái'),
            TableColumn::make('payment_status', 'Thanh toán'),
            // Hidden by default
            TableColumn::make('customer_email', 'Email')->defaultHidden(),
            TableColumn::make('payment_method', 'Phương thức')->defaultHidden(),
            TableColumn::make('booking_date', 'Ngày đi (raw)')->defaultHidden()->sortable(),
            TableColumn::make('confirmed_at', 'Thời gian xác nhận')->defaultHidden()->sortable(),
            TableColumn::make('updated_at', 'Cập nhật')->defaultHidden()->sortable(),
        ];
    }

    /** Search closures for cross-table search. */
    private static function searchColumns(): array
    {
        return [
            'bookings.booking_code',
            'bookings.customer_name',
            'bookings.customer_phone',
            'bookings.customer_email',
            fn ($q, $s) => $q->whereHas('trip.route', fn ($r) => $r->where('name', 'like', "%{$s}%")),
            fn ($q, $s) => $q->whereHas('pickupStop', fn ($r) => $r->where('name', 'like', "%{$s}%")),
        ];
    }
}
