<?php

namespace App\Filament\Resources\Routes\Pages;

use App\Filament\Concerns\GuardsDeleteWhenBookingsExist;
use App\Filament\Resources\Routes\RouteResource;
use App\Filament\Support\BookingDeleteGuard;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRoute extends EditRecord
{
    use GuardsDeleteWhenBookingsExist;

    protected static string $resource = RouteResource::class;

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
        return BookingDeleteGuard::routeBookingCount((int) $this->record->id);
    }
}
