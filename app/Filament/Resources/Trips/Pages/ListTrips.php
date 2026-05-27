<?php

namespace App\Filament\Resources\Trips\Pages;

use App\Filament\Resources\Trips\TripResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTrips extends ListRecords
{
    protected static string $resource = TripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Thêm chuyến xe'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Tất cả'),
            'active' => Tab::make('Đang hoạt động')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_active', true)),
            'inactive' => Tab::make('Tạm ngưng')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('is_active', false)),
        ];
    }
}
