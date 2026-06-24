<?php

namespace App\Filament\Resources\Buses\Pages;

use App\Filament\Concerns\GuardsDeleteWhenBookingsExist;
use App\Filament\Resources\Buses\BusResource;
use App\Filament\Support\BookingDeleteGuard;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditBus extends EditRecord
{
    use GuardsDeleteWhenBookingsExist;

    protected static string $resource = BusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Xem'),
            $this->guardedDeleteAction(),
        ];
    }

    protected function relatedBookingCount(): int
    {
        return BookingDeleteGuard::busBookingCount((int) $this->record->id);
    }
}
