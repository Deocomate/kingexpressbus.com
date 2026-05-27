<?php

namespace App\Filament\Resources\HolidaySurcharges\Pages;

use App\Filament\Resources\HolidaySurcharges\HolidaySurchargeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHolidaySurcharge extends EditRecord
{
    protected static string $resource = HolidaySurchargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Xem'),
            DeleteAction::make()
                ->label('Xóa'),
        ];
    }
}
