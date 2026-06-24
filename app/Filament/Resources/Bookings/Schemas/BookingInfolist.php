<?php

namespace App\Filament\Resources\Bookings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin đặt vé')
                    ->schema([
                        TextEntry::make('booking_code')
                            ->label('Mã đặt vé')
                            ->weight('bold')
                            ->size('lg'),
                        TextEntry::make('status')
                            ->label('Trạng thái')
                            ->badge(),
                        TextEntry::make('booking_date')
                            ->label('Ngày đi')
                            ->date('d/m/Y'),
                        TextEntry::make('trip.start_time')
                            ->label('Giờ khởi hành')
                            ->time('H:i')
                            ->placeholder('-'),
                        TextEntry::make('confirmed_at')
                            ->label('Thời gian xác nhận')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Khách hàng')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Tài khoản')
                            ->placeholder('-'),
                        TextEntry::make('customer_name')
                            ->label('Tên khách hàng'),
                        TextEntry::make('customer_email')
                            ->label('Email')
                            ->placeholder('-'),
                        TextEntry::make('customer_phone')
                            ->label('Số điện thoại'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Lịch trình')
                    ->schema([
                        TextEntry::make('trip.route.name')
                            ->label('Tuyến đường')
                            ->weight('bold')
                            ->placeholder('-'),
                        TextEntry::make('trip.bus.name')
                            ->label('Xe')
                            ->placeholder('-'),
                        TextEntry::make('pickupStop.name')
                            ->label('Điểm đón')
                            ->placeholder('-'),
                        TextEntry::make('dropoffStop.name')
                            ->label('Điểm trả')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Thanh toán')
                    ->schema([
                        TextEntry::make('quantity')
                            ->label('Số lượng vé')
                            ->numeric(),
                        TextEntry::make('total_price')
                            ->label('Tổng tiền')
                            ->money('VND')
                            ->color('success'),
                        TextEntry::make('payment_method')
                            ->label('Phương thức thanh toán')
                            ->badge(),
                        TextEntry::make('payment_status')
                            ->label('Trạng thái thanh toán')
                            ->badge(),
                        TextEntry::make('payment_transaction_id')
                            ->label('Mã giao dịch')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Chi tiết giá')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('base_unit_price')
                                    ->label('Giá gốc/vé')
                                    ->money('VND'),
                                TextEntry::make('global_surcharge_unit')
                                    ->label('Phụ thu chung/vé')
                                    ->money('VND'),
                                TextEntry::make('route_surcharge_unit')
                                    ->label('Phụ thu tuyến/vé')
                                    ->money('VND'),
                                TextEntry::make('final_unit_price')
                                    ->label('Giá cuối/vé')
                                    ->money('VND'),
                                TextEntry::make('total_surcharge_amount')
                                    ->label('Tổng phụ thu')
                                    ->money('VND'),
                            ]),
                        TextEntry::make('surcharge_reason_snapshot')
                            ->label('Lý do phụ thu')
                            ->placeholder('-')
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                Section::make('Ghi chú và thông tin bổ sung')
                    ->schema([
                        TextEntry::make('notes')
                            ->label('Ghi chú')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->label('Ngày tạo')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->label('Ngày cập nhật')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
