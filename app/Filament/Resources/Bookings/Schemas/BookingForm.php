<?php

namespace App\Filament\Resources\Bookings\Schemas;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin đặt vé')
                    ->schema([
                        TextInput::make('booking_code')
                            ->label('Mã đặt vé')
                            ->required(),
                        Select::make('user_id')
                            ->label('Tài khoản')
                            ->relationship('user', 'name'),
                        Select::make('trip_id')
                            ->label('Chuyến xe')
                            ->relationship('trip', 'id')
                            ->required(),
                        DatePicker::make('booking_date')
                            ->label('Ngày đi')
                            ->required(),
                        TextInput::make('customer_name')
                            ->label('Tên khách hàng')
                            ->required(),
                        TextInput::make('customer_email')
                            ->label('Email')
                            ->email(),
                        TextInput::make('customer_phone')
                            ->label('Số điện thoại')
                            ->tel()
                            ->required(),
                        Select::make('pickup_stop_id')
                            ->label('Điểm đón')
                            ->relationship('pickupStop', 'name'),
                        Select::make('dropoff_stop_id')
                            ->label('Điểm trả')
                            ->relationship('dropoffStop', 'name')
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Số lượng vé')
                            ->required()
                            ->numeric()
                            ->default(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Thanh toán')
                    ->schema([
                        TextInput::make('total_price')
                            ->label('Tổng tiền')
                            ->required()
                            ->numeric()
                            ->suffix('VND'),
                        Select::make('status')
                            ->label('Trạng thái')
                            ->options(BookingStatus::class)
                            ->default(BookingStatus::Pending)
                            ->required(),
                        DateTimePicker::make('confirmed_at')
                            ->label('Thời gian xác nhận'),
                        Select::make('payment_method')
                            ->label('Phương thức thanh toán')
                            ->options(PaymentMethod::class)
                            ->default(PaymentMethod::CashOnPickup)
                            ->required(),
                        Select::make('payment_status')
                            ->label('Trạng thái thanh toán')
                            ->options(PaymentStatus::class)
                            ->default(PaymentStatus::Unpaid)
                            ->required(),
                        TextInput::make('payment_transaction_id')
                            ->label('Mã giao dịch'),
                        Textarea::make('notes')
                            ->label('Ghi chú')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Chi tiết giá')
                    ->schema([
                        TextInput::make('base_unit_price')
                            ->label('Giá gốc/vé')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('VND'),
                        TextInput::make('global_surcharge_unit')
                            ->label('Phụ thu chung/vé')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('VND'),
                        TextInput::make('route_surcharge_unit')
                            ->label('Phụ thu tuyến/vé')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('VND'),
                        TextInput::make('final_unit_price')
                            ->label('Giá cuối/vé')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('VND'),
                        TextInput::make('total_surcharge_amount')
                            ->label('Tổng phụ thu')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('VND'),
                        Textarea::make('surcharge_reason_snapshot')
                            ->label('Lý do phụ thu')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
