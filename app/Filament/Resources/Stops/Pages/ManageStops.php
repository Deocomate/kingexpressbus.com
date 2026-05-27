<?php

namespace App\Filament\Resources\Stops\Pages;

use App\Filament\Resources\Stops\StopResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageStops extends ManageRecords
{
    protected static string $resource = StopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm điểm dừng')
                ->slideOver(),
        ];
    }
}
