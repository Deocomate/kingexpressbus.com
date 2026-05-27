<?php

namespace App\Filament\Resources\TripBlocks\Pages;

use App\Filament\Resources\TripBlocks\TripBlockResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTripBlock extends EditRecord
{
    protected static string $resource = TripBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
