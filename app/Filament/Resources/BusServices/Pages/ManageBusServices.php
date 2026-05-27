<?php

namespace App\Filament\Resources\BusServices\Pages;

use App\Filament\Resources\BusServices\BusServiceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBusServices extends ManageRecords
{
    protected static string $resource = BusServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm dịch vụ')
                ->slideOver(),
        ];
    }
}
