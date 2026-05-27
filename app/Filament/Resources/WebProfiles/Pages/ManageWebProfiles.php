<?php

namespace App\Filament\Resources\WebProfiles\Pages;

use App\Filament\Resources\WebProfiles\WebProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageWebProfiles extends ManageRecords
{
    protected static string $resource = WebProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm cấu hình')
                ->slideOver(),
        ];
    }
}
