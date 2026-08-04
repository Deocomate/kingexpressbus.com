<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

// Filament 5 resolves enum labels via the HasLabel interface.
// This interface must remain until Phase 10 when Filament is removed.
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
