<?php

namespace App\Filament\Resources\HolidaySurcharges\Pages;

use App\Filament\Resources\HolidaySurcharges\HolidaySurchargeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHolidaySurcharge extends ViewRecord
{
    protected static string $resource = HolidaySurchargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Sửa'),
        ];
    }
}
