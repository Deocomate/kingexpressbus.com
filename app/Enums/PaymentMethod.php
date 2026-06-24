<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasLabel
{
    case OnlineBanking = 'online_banking';
    case CashOnPickup = 'cash_on_pickup';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OnlineBanking => 'Chuyển khoản online',
            self::CashOnPickup => 'Thanh toán khi đón',
        };
    }
}
