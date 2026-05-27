<?php

namespace App\Filament\Resources\HolidaySurcharges;

use App\Filament\Resources\HolidaySurcharges\Pages\CreateHolidaySurcharge;
use App\Filament\Resources\HolidaySurcharges\Pages\EditHolidaySurcharge;
use App\Filament\Resources\HolidaySurcharges\Pages\ListHolidaySurcharges;
use App\Filament\Resources\HolidaySurcharges\Pages\ViewHolidaySurcharge;
use App\Filament\Resources\HolidaySurcharges\Schemas\HolidaySurchargeForm;
use App\Filament\Resources\HolidaySurcharges\Schemas\HolidaySurchargeInfolist;
use App\Filament\Resources\HolidaySurcharges\Tables\HolidaySurchargesTable;
use App\Models\HolidaySurcharge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class HolidaySurchargeResource extends Resource
{
    protected static ?string $model = HolidaySurcharge::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Vận hành';

    protected static ?string $modelLabel = 'Phụ thu lễ';

    protected static ?string $pluralModelLabel = 'Phụ thu lễ';

    protected static ?string $navigationLabel = 'Phụ thu ngày lễ';

    public static function form(Schema $schema): Schema
    {
        return HolidaySurchargeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HolidaySurchargeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HolidaySurchargesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHolidaySurcharges::route('/'),
            'create' => CreateHolidaySurcharge::route('/create'),
            'view' => ViewHolidaySurcharge::route('/{record}'),
            'edit' => EditHolidaySurcharge::route('/{record}/edit'),
        ];
    }
}
