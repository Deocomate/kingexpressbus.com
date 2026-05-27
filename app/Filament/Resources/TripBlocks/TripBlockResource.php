<?php

namespace App\Filament\Resources\TripBlocks;

use App\Filament\Resources\TripBlocks\Pages\CreateTripBlock;
use App\Filament\Resources\TripBlocks\Pages\EditTripBlock;
use App\Filament\Resources\TripBlocks\Pages\ListTripBlocks;
use App\Filament\Resources\TripBlocks\Schemas\TripBlockForm;
use App\Filament\Resources\TripBlocks\Tables\TripBlocksTable;
use App\Models\TripBlock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TripBlockResource extends Resource
{
    protected static ?string $model = TripBlock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNoSymbol;

    protected static string|\UnitEnum|null $navigationGroup = 'Vận hành';

    protected static ?string $modelLabel = 'Khóa chuyến';

    protected static ?string $pluralModelLabel = 'Khóa chuyến';

    public static function form(Schema $schema): Schema
    {
        return TripBlockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TripBlocksTable::configure($table);
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
            'index' => ListTripBlocks::route('/'),
            'create' => CreateTripBlock::route('/create'),
            'edit' => EditTripBlock::route('/{record}/edit'),
        ];
    }
}
