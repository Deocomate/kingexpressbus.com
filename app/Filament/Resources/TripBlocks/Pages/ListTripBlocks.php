<?php

namespace App\Filament\Resources\TripBlocks\Pages;

use App\Filament\Resources\TripBlocks\TripBlockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTripBlocks extends ListRecords
{
    protected static string $resource = TripBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
