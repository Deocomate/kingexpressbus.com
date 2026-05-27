<?php

namespace App\Filament\Resources\BusServices;

use App\Filament\Resources\BusServices\Pages\ManageBusServices;
use App\Models\BusService;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BusServiceResource extends Resource
{
    protected static ?string $model = BusService::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Quản lý đội xe';

    protected static ?string $modelLabel = 'Dịch vụ xe';

    protected static ?string $pluralModelLabel = 'Dịch vụ xe';

    protected static ?string $navigationLabel = 'Dịch vụ xe';

    public static function form(Schema $schema): Schema
    {
        // Khai báo danh sách các icon gợi ý phổ biến cho xe khách
        $icons = [
            'fa-solid fa-wifi' => 'Wifi',
            'fa-solid fa-bottle-water' => 'Nước uống',
            'fa-solid fa-plug' => 'Ổ cắm điện',
            'fa-solid fa-tv' => 'TV',
            'fa-solid fa-snowflake' => 'Điều hòa',
            'fa-solid fa-blanket' => 'Chăn đắp',
            'fa-solid fa-shield-heart' => 'Bảo hiểm/An toàn',
            'fa-solid fa-utensils' => 'Đồ ăn nhẹ',
            'fa-solid fa-bed' => 'Giường nằm',
            'fa-solid fa-toilet' => 'Nhà vệ sinh',
            'fa-solid fa-usb' => 'Cổng USB',
            'fa-solid fa-headphones' => 'Tai nghe',
            'fa-solid fa-mug-hot' => 'Trà / Cafe',
            'fa-solid fa-suitcase-rolling' => 'Chỗ để hành lý',
            'fa-solid fa-wheelchair' => 'Hỗ trợ xe lăn',
        ];

        // Format lại array để render cả thẻ <i> và Text trong Select dropdown
        $iconOptions = collect($icons)->mapWithKeys(function ($label, $class) {
            return [$class => "<div class='flex items-center gap-3'><i class='{$class} text-primary-600 w-5 text-center text-lg'></i> <span>{$label} ({$class})</span></div>"];
        })->toArray();

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Tên dịch vụ')
                    ->required()
                    ->maxLength(255),

                Select::make('icon')
                    ->label('Biểu tượng')
                    ->options($iconOptions)
                    ->allowHtml() // Cho phép render HTML bên trong Dropdown
                    ->searchable()
                    ->preload()
                    ->helperText('Chọn biểu tượng Font Awesome phù hợp.')
                    ->required(),

                TextInput::make('priority')
                    ->label('Độ ưu tiên')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Thông tin dịch vụ xe')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Tên dịch vụ')
                            ->weight('bold'),

                        TextEntry::make('icon')
                            ->label('Biểu tượng')
                            ->formatStateUsing(fn (?string $state): string => $state ? "<i class='{$state} text-2xl text-primary-600'></i>" : '-')
                            ->html(),

                        TextEntry::make('priority')
                            ->label('Độ ưu tiên')
                            ->numeric(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên dịch vụ')
                    ->searchable(),

                TextColumn::make('icon')
                    ->label('Biểu tượng')
                    ->formatStateUsing(fn (?string $state): string => $state ? "<i class='{$state} text-xl text-primary-600'></i>" : '')
                    ->html()
                    ->alignCenter(),

                TextColumn::make('priority')
                    ->label('Độ ưu tiên')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Xem')
                    ->slideOver(),
                EditAction::make()
                    ->label('Sửa')
                    ->slideOver(),
                DeleteAction::make()
                    ->label('Xóa'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Xóa đã chọn'),
                ])
                    ->label('Thao tác hàng loạt'),
            ])
            ->reorderable('priority', direction: 'desc')
            ->defaultSort('priority', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBusServices::route('/'),
        ];
    }
}
