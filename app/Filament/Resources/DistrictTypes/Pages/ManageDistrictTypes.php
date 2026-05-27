<?php

namespace App\Filament\Resources\DistrictTypes\Pages;

use App\Filament\Resources\DistrictTypes\DistrictTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDistrictTypes extends ManageRecords
{
    protected static string $resource = DistrictTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm loại địa điểm')
                ->slideOver(),
        ];
    }
}
