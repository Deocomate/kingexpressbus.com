<?php

namespace App\Support\Admin;

use App\Filament\Resources\Districts\DistrictResource;
use App\Filament\Resources\DistrictTypes\DistrictTypeResource;
use App\Filament\Resources\Provinces\ProvinceResource;
use App\Filament\Resources\Stops\StopResource;
use App\Models\District;
use App\Models\DistrictType;
use App\Models\Province;
use App\Models\Stop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Canonical list queries mirroring Filament location resources:
 * defaultSort + reorderable priority DESC, same eager loads as Resource::getEloquentQuery().
 */
class LocationFilamentParity
{
    /** @return Collection<int, Province> */
    public static function filamentProvinces(): Collection
    {
        return self::filamentQuery(Province::class, ProvinceResource::class)->get();
    }

    /** @return Collection<int, DistrictType> */
    public static function filamentDistrictTypes(): Collection
    {
        return self::filamentQuery(DistrictType::class, DistrictTypeResource::class)->get();
    }

    /** @return Collection<int, District> */
    public static function filamentDistricts(): Collection
    {
        return self::filamentQuery(District::class, DistrictResource::class)->get();
    }

    /** @return Collection<int, Stop> */
    public static function filamentStops(): Collection
    {
        return self::filamentQuery(Stop::class, StopResource::class)->get();
    }

  /**
   * @param class-string $modelClass
   * @param class-string $resourceClass
   */
    private static function filamentQuery(string $modelClass, string $resourceClass): Builder
    {
        /** @var Builder $query */
        $query = $resourceClass::getEloquentQuery();

        return $query->orderBy('priority', 'desc');
    }
}
