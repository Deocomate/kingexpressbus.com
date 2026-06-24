<?php

namespace App\Filament\Concerns;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\DB;

trait GuardsDeleteWhenBookingsExist
{
    protected function guardedDeleteAction(): DeleteAction
    {
        return DeleteAction::make()
            ->label('Xóa')
            ->before(function (): void {
                $count = $this->relatedBookingCount();

                if ($count > 0) {
                    Notification::make()
                        ->danger()
                        ->title("Không thể xóa: còn {$count} đơn đặt vé liên quan.")
                        ->send();

                    throw new Halt();
                }
            });
    }

    abstract protected function relatedBookingCount(): int;
}
