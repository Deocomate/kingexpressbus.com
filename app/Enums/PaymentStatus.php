<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasColor, HasLabel
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Unpaid => 'Chưa thanh toán',
            self::Paid => 'Đã thanh toán',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Unpaid => 'warning',
            self::Paid => 'success',
        };
    }
}
