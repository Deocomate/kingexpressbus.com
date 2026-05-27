<?php

namespace App\Filament\Resources\HolidaySurcharges\Pages;

use App\Filament\Resources\HolidaySurcharges\HolidaySurchargeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHolidaySurcharges extends ListRecords
{
    protected static string $resource = HolidaySurchargeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm phụ thu'),
        ];
    }
}
