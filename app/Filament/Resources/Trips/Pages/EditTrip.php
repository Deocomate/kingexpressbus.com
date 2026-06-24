<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Concerns\GuardsDeleteWhenBookingsExist;
use App\Filament\Resources\Trips\TripResource;
use App\Filament\Support\BookingDeleteGuard;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTrip extends EditRecord
{
    use GuardsDeleteWhenBookingsExist;

    protected static string $resource = TripResource::class;

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
        return BookingDeleteGuard::tripBookingCount((int) $this->record->id);
    }
}
