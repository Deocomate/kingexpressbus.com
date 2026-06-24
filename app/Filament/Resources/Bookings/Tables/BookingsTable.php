<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Services\BookingService;
use App\Support\DepartureAtExpression;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_code')
                    ->label('Mã đặt vé')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('created_at')
                    ->label('Ngày đặt vé')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('bookings.created_at', $direction);
                    }),
                TextColumn::make('departure_at')
                    ->label('Ngày đi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->orderBy('bookings.booking_date', $direction)
                            ->orderByRaw(DepartureAtExpression::tripStartTimeSubquery().' '.$direction);
                    })
                    ->description(fn (Booking $record): ?string => match (true) {
                        $record->booking_date?->isToday() => 'Đi hôm nay',
                        $record->booking_date?->isTomorrow() => 'Đi ngày mai',
                        default => null,
                    }),
                TextColumn::make('trip.route.name')
                    ->label('Tuyến đường')
                    ->searchable(),
                TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->description(fn (Booking $record): ?string => $record->customer_phone)
                    ->searchable(['customer_name', 'customer_phone']),
                TextColumn::make('pickupStop.name')
                    ->label('Đón → Trả')
                    ->description(fn (Booking $record): ?string => $record->dropoffStop?->name)
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Số vé')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Tổng tiền')
                    ->money('VND')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge(),
                TextColumn::make('payment_status')
                    ->label('Thanh toán')
                    ->badge(),
                TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payment_method')
                    ->label('Phương thức')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('booking_date')
                    ->label('Ngày đi')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('confirmed_at')
                    ->label('Thời gian xác nhận')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Ngày cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort(fn (Builder $query, string $direction): Builder => $query->orderBy('bookings.created_at', $direction), 'desc')
            ->poll('60s')
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options(BookingStatus::class),
                SelectFilter::make('payment_status')
                    ->label('Thanh toán')
                    ->options([
                        'unpaid' => 'Chưa thanh toán',
                        'paid' => 'Đã thanh toán',
                    ]),
                Filter::make('booking_date')
                    ->label('Ngày đi')
                    ->schema([
                        DatePicker::make('from')
                            ->label('Từ ngày'),
                        DatePicker::make('until')
                            ->label('Đến ngày'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('bookings.booking_date', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('bookings.booking_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Xem')
                    ->slideOver(),
                Action::make('confirm')
                    ->label('Xác nhận')
                    ->color('success')
                    ->visible(fn (Booking $record): bool => $record->status === BookingStatus::Pending)
                    ->requiresConfirmation()
                    ->action(function (Booking $record): void {
                        self::notifyServiceResult(app(BookingService::class)->updateStatus($record->id, 'confirmed'));
                    }),
                Action::make('cancel')
                    ->label('Hủy vé')
                    ->color('danger')
                    ->visible(fn (Booking $record): bool => ! in_array($record->status, [BookingStatus::Cancelled, BookingStatus::Completed], true))
                    ->schema([
                        Select::make('cancel_reason')
                            ->label('Lý do hủy')
                            ->options([
                                'Hết chỗ trống cho chuyến xe này' => 'Hết chỗ',
                                'Chuyến xe tạm ngưng hoạt động trong dịp lễ tết' => 'Lễ tết / Ngày lễ',
                                'custom' => 'Lý do khác',
                            ])
                            ->required()
                            ->live(),
                        Textarea::make('custom_reason')
                            ->label('Nhập lý do cụ thể')
                            ->required()
                            ->visible(fn (Get $get): bool => $get('cancel_reason') === 'custom'),
                    ])
                    ->requiresConfirmation()
                    ->action(function (Booking $record, array $data): void {
                        $reason = $data['cancel_reason'] === 'custom'
                            ? $data['custom_reason']
                            : $data['cancel_reason'];

                        self::notifyServiceResult(app(BookingService::class)->cancelBooking($record->id, $reason, auth()->id()));
                    }),
                Action::make('complete')
                    ->label('Hoàn thành')
                    ->color('info')
                    ->visible(fn (Booking $record): bool => $record->status === BookingStatus::Confirmed)
                    ->requiresConfirmation()
                    ->action(function (Booking $record): void {
                        self::notifyServiceResult(app(BookingService::class)->updateStatus($record->id, 'completed'));
                    }),
            ]);
    }

    private static function notifyServiceResult(array $result): void
    {
        $notification = Notification::make()
            ->title($result['message'] ?? 'Đã cập nhật đặt vé');

        ($result['success'] ?? false)
            ? $notification->success()->send()
            : $notification->danger()->send();
    }
}
