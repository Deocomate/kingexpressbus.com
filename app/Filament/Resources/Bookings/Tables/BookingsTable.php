<?php

namespace App\Filament\Resources\Bookings\Tables;

use App\Models\Booking;
use App\Services\BookingService;
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
                    ->searchable(),
                TextColumn::make('trip.route.name')
                    ->label('Tuyến đường')
                    ->searchable(),
                TextColumn::make('booking_date')
                    ->label('Ngày đi')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('customer_phone')
                    ->label('Số điện thoại')
                    ->searchable(),
                TextColumn::make('pickupStop.name')
                    ->label('Điểm đón')
                    ->searchable(),
                TextColumn::make('dropoffStop.name')
                    ->label('Điểm trả')
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
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::bookingStatusLabel($state))
                    ->color(fn (?string $state): string => self::bookingStatusColor($state)),
                TextColumn::make('payment_method')
                    ->label('Phương thức')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::paymentMethodLabel($state)),
                TextColumn::make('payment_status')
                    ->label('Thanh toán')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::paymentStatusLabel($state))
                    ->color(fn (?string $state): string => $state === 'paid' ? 'success' : 'warning'),
                TextColumn::make('confirmed_at')
                    ->label('Thời gian xác nhận')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending' => 'Chờ xác nhận',
                        'confirmed' => 'Đã xác nhận',
                        'cancelled' => 'Đã hủy',
                        'completed' => 'Hoàn thành',
                    ]),
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
                            ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('booking_date', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('booking_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Xem')
                    ->slideOver(),
                Action::make('confirm')
                    ->label('Xác nhận')
                    ->color('success')
                    ->visible(fn (Booking $record): bool => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Booking $record): void {
                        self::notifyServiceResult(app(BookingService::class)->updateStatus($record->id, 'confirmed'));
                    }),
                Action::make('cancel')
                    ->label('Hủy vé')
                    ->color('danger')
                    ->visible(fn (Booking $record): bool => ! in_array($record->status, ['cancelled', 'completed'], true))
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
                    ->visible(fn (Booking $record): bool => $record->status === 'confirmed')
                    ->requiresConfirmation()
                    ->action(function (Booking $record): void {
                        self::notifyServiceResult(app(BookingService::class)->updateStatus($record->id, 'completed'));
                    }),
            ]);
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

    private static function bookingStatusColor(?string $state): string
    {
        return match ($state) {
            'pending' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
            'completed' => 'info',
            default => 'gray',
        };
    }

    private static function paymentMethodLabel(?string $state): string
    {
        return match ($state) {
            'online_banking' => 'Chuyển khoản online',
            'cash_on_pickup' => 'Thanh toán khi đón',
            default => 'Không xác định',
        };
    }

    private static function paymentStatusLabel(?string $state): string
    {
        return match ($state) {
            'unpaid' => 'Chưa thanh toán',
            'paid' => 'Đã thanh toán',
            default => 'Không xác định',
        };
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
