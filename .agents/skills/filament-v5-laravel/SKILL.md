---
name: filament-v5-laravel
compatibility: PHP 8.2+, Laravel 11.28+, Tailwind CSS 4.1+, Livewire 3, Filament 5.x.
description: Use this skill whenever the user asks to build, debug, refactor, teach, or generate Laravel admin panels, dashboards, resources, forms, tables, actions, relation managers, tenancy, navigation, widgets, tests, or deployment workflows with Filament 5.x. Prefer this skill even if the user only says Laravel admin, CRUD panel, resource, form builder, table builder, dashboard, or Filament without mentioning version. Enforce Filament 5 APIs and avoid older v2/v3/v4 syntax.
---

# Filament v5 Laravel Skill

## Mission

Help the user produce correct, runnable, secure Filament **5.x** code for Laravel. Prioritize copy-paste-ready files, accurate imports, current v5 conventions, and practical examples over abstract explanation.

Filament v5 is a server-driven UI framework for Laravel. Build UI with PHP configuration objects, especially `Panel`, `Resource`, `Schema`, `Table`, `Action`, `Notification`, `Widget`, and relation manager classes.

## When this skill applies

Use this skill for any request involving:

- Installing or upgrading Filament in Laravel.
- Building admin panels, dashboards, user portals, back-office CRUD, CMS-like management screens, or internal tools.
- Creating or fixing Resources, Schemas, Tables, Infolists, Actions, Relation Managers, Widgets, Custom Pages, Navigation, Auth, MFA, Tenancy, Plugins, Tests, or Deployment.
- Migrating from older Filament versions to v5.
- Debugging import errors, missing methods, broken actions, relation manager behavior, route/panel problems, policies, authorization, or production asset issues.

If the user asks in Vietnamese, answer in Vietnamese while keeping PHP class names, namespaces, file paths, and method names exactly as code.

## Version guardrails for Filament 5.x

Always enforce these v5 conventions:

1. **Use v5 package constraints**:

    ```bash
    composer require filament/filament:"^5.0"
    php artisan filament:install --panels
    ```

2. **Base requirements**:
    - PHP `8.2+`
    - Laravel `11.28+`
    - Tailwind CSS `4.1+`
    - Livewire-compatible Laravel setup

3. **Use `Filament\Schemas\Schema` for resource forms and infolists**. Do not generate old `Filament\Forms\Form` examples for v5 resources.

4. **Use generated split classes when code is non-trivial**:

    ```txt
    app/Filament/Resources/Products/ProductResource.php
    app/Filament/Resources/Products/Schemas/ProductForm.php
    app/Filament/Resources/Products/Schemas/ProductInfolist.php
    app/Filament/Resources/Products/Tables/ProductsTable.php
    app/Filament/Resources/Products/Pages/ListProducts.php
    app/Filament/Resources/Products/Pages/CreateProduct.php
    app/Filament/Resources/Products/Pages/EditProduct.php
    ```

5. **Use v5 table action zones**:
    - `->headerActions([...])` for buttons above a table.
    - `->recordActions([...])` for row actions.
    - `->toolbarActions([BulkActionGroup::make([...])])` for bulk actions.
    - Avoid older-looking table examples that use `->actions()` or ungrouped `->bulkActions()` for resource tables unless the user is explicitly working in a legacy codebase.

6. **Use `Filament\Actions` for actions** in v5 examples:

    ```php
    use Filament\Actions\CreateAction;
    use Filament\Actions\EditAction;
    use Filament\Actions\DeleteAction;
    use Filament\Actions\BulkActionGroup;
    use Filament\Actions\DeleteBulkAction;
    ```

7. **Use Heroicon enum where possible**:

    ```php
    use Filament\Support\Icons\Heroicon;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;
    ```

8. **Do not silently mix v5 with v3/v4 snippets**. If a user's pasted code uses older APIs, explain the mismatch and rewrite to v5.

9. **Prefer policies and explicit authorization** for production. `canAccessPanel()` is required for real panel access control outside local development.

10. **Use secure defaults**: sanitize dynamic URLs, validate uploaded files, avoid unsafe HTML, authorize custom actions, and scope tenant data carefully.

## Response protocol

When generating a solution:

1. Identify the task category: install, panel, resource, schema/form, table, action, relation, widget, custom page, tenancy, testing, security, deployment, or migration.
2. State the assumptions only when needed, e.g. model names, relationships, database columns, panel ID, or Laravel version.
3. Provide commands first, then file tree, then code grouped by file path.
4. Keep code runnable and import-complete. Every class should include namespace and `use` statements.
5. Prefer incremental edits if the user pasted an existing file. Preserve their naming style unless it is invalid for v5.
6. For debugging, compare exact error text against imports, namespaces, generated paths, service provider registration, policies, and Composer versions.
7. End with a short verification checklist: route to open, artisan commands to run, tests or expected UI behavior.

## Official reference links to consult

Use these docs as the primary source when the user asks for current behavior or exact API details:

- Filament v5 overview: https://filamentphp.com/docs/5.x/introduction/overview
- Installation: https://filamentphp.com/docs/5.x/introduction/installation
- Getting started: https://filamentphp.com/docs/5.x/getting-started
- Panel configuration: https://filamentphp.com/docs/5.x/panel-configuration
- Resources overview: https://filamentphp.com/docs/5.x/resources/overview
- Listing records: https://filamentphp.com/docs/5.x/resources/listing-records
- Creating records: https://filamentphp.com/docs/5.x/resources/creating-records
- Editing records: https://filamentphp.com/docs/5.x/resources/editing-records
- Viewing records: https://filamentphp.com/docs/5.x/resources/viewing-records
- Managing relationships: https://filamentphp.com/docs/5.x/resources/managing-relationships
- Nesting resources: https://filamentphp.com/docs/5.x/resources/nesting
- Singular resources: https://filamentphp.com/docs/5.x/resources/singular
- Global search: https://filamentphp.com/docs/5.x/resources/global-search
- Custom pages: https://filamentphp.com/docs/5.x/navigation/custom-pages
- Navigation overview: https://filamentphp.com/docs/5.x/navigation/overview
- User access/auth: https://filamentphp.com/docs/5.x/users/overview
- MFA: https://filamentphp.com/docs/5.x/users/multi-factor-authentication
- Tenancy: https://filamentphp.com/docs/5.x/users/tenancy
- Styling: https://filamentphp.com/docs/5.x/styling/overview
- Icons: https://filamentphp.com/docs/5.x/styling/icons
- Render hooks: https://filamentphp.com/docs/5.x/advanced/render-hooks
- Assets: https://filamentphp.com/docs/5.x/advanced/assets
- Enums: https://filamentphp.com/docs/5.x/advanced/enums
- Security: https://filamentphp.com/docs/5.x/advanced/security
- Testing resources: https://filamentphp.com/docs/5.x/testing/testing-resources
- Testing tables: https://filamentphp.com/docs/5.x/testing/testing-tables
- Testing schemas: https://filamentphp.com/docs/5.x/testing/testing-schemas
- Testing actions: https://filamentphp.com/docs/5.x/testing/testing-actions
- Plugins: https://filamentphp.com/docs/5.x/plugins/getting-started
- Deployment: https://filamentphp.com/docs/5.x/deployment
- Upgrade guide: https://filamentphp.com/docs/5.x/upgrade-guide

## Quick install and setup examples

### New panel builder install

```bash
composer require filament/filament:"^5.0"
php artisan filament:install --panels
php artisan make:filament-user
php artisan serve
```

Open:

```txt
http://127.0.0.1:8000/admin
```

For Windows PowerShell, use `~5.0` if `^` is swallowed:

```bash
composer require filament/filament:"~5.0"
php artisan filament:install --panels
```

### Create a second panel

```bash
php artisan make:filament-panel app
```

This creates:

```txt
app/Providers/Filament/AppPanelProvider.php
```

Check registration in Laravel 11+:

```php
// bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\AdminPanelProvider::class,
    App\Providers\Filament\AppPanelProvider::class,
];
```

## Panel provider example

Use a Panel Provider for route path, auth, navigation, assets, middleware, render hooks, database transactions, and tenancy.

```php
<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Dashboard;
use App\Models\Team;
use Filament\Http\Middleware\Authenticate;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile(EditProfile::class, isSimple: false)
            ->authGuard('web')
            ->authMiddleware([
                Authenticate::class,
            ], isPersistent: true)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('20rem')
            ->collapsedSidebarWidth('5rem')
            ->breadcrumbs(true)
            ->maxContentWidth(Width::Full)
            ->subNavigationPosition(SubNavigationPosition::End)
            ->databaseTransactions()
            ->strictAuthorization()
            ->spa(hasPrefetching: true)
            ->unsavedChangesAlerts()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Shop')
                    ->icon(Heroicon::OutlinedShoppingCart),
                NavigationGroup::make()
                    ->label('Settings')
                    ->icon(Heroicon::OutlinedCog6Tooth)
                    ->collapsed(),
            ])
            ->assets([
                Css::make('admin-theme', resource_path('css/filament/admin/theme.css')),
                Js::make('admin-scripts', resource_path('js/filament/admin/app.js')),
            ])
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): string => Blade::render('@livewire(\'livewire-ui-modal\')')
            )
            ->bootUsing(function (Panel $panel): void {
                // Runs for requests inside this panel.
            });
    }
}
```

### Fully custom navigation builder

Use this only when automatic resource/page navigation is not enough.

```php
use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Products\ProductResource;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;

$panel->navigation(function (NavigationBuilder $builder): NavigationBuilder {
    return $builder
        ->items([
            NavigationItem::make('Dashboard')
                ->icon(Heroicon::OutlinedHome)
                ->url(fn (): string => Dashboard::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard')),
        ])
        ->groups([
            NavigationGroup::make('Catalog')
                ->items([
                    ...ProductResource::getNavigationItems(),
                ]),
        ]);
});
```

## Production user access example

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return str_ends_with($this->email, '@example.com')
                && $this->hasVerifiedEmail()
                && $this->is_admin;
        }

        return true;
    }

    public function getFilamentName(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: $this->email;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
}
```

## Resource workflow

### Generate resources

```bash
php artisan make:model Product -mf
php artisan make:filament-resource Product --generate --view --soft-deletes
php artisan make:filament-resource Category --simple --generate
php artisan make:filament-relation-manager CategoryResource products name --attach
```

Resource types:

- Standard resource: List, Create, Edit pages; optional View page.
- Simple resource: one Manage page using modals.
- Singular resource or custom page: use for app settings, homepage settings, or a single record.

## Practical example: Product resource

Assumed models:

```txt
Product belongsTo Category
Product belongsTo User as creator
Product hasMany ProductVariant
Product has soft deletes
```

### `app/Filament/Resources/Products/ProductResource.php`

```php
<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages;
use App\Filament\Resources\Products\RelationManagers\VariantsRelationManager;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Schemas\ProductInfolist;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End;

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku', 'category.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'SKU' => $record->sku,
            'Category' => $record->category?->name,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['category']);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
```

### `app/Filament/Resources/Products/Schemas/ProductForm.php`

```php
<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ProductStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (string $operation, $state, callable $set) =>
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                    ),

                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(64),

                                Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),

                        RichEditor::make('description')
                            ->columnSpanFull(),
                    ]),

                Section::make('Pricing & publishing')
                    ->columns(3)
                    ->schema([
                        TextInput::make('price')
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->required(),

                        Select::make('status')
                            ->options(ProductStatus::class)
                            ->required(),

                        DateTimePicker::make('published_at')
                            ->seconds(false)
                            ->visibleOn(Operation::Edit),

                        Toggle::make('is_featured')
                            ->label('Featured')
                            ->default(false),
                    ]),

                Section::make('Media')
                    ->schema([
                        FileUpload::make('thumbnail_path')
                            ->image()
                            ->disk('public')
                            ->directory('products/thumbnails')
                            ->visibility('public')
                            ->imageEditor()
                            ->maxSize(2048),
                    ]),
            ]);
    }
}
```

### `app/Filament/Resources/Products/Tables/ProductsTable.php`

```php
<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ProductStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail_path')
                    ->label('Image')
                    ->disk('public')
                    ->square(),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): ?string => $record->sku),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge(),

                IconColumn::make('is_featured')
                    ->boolean()
                    ->label('Featured'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProductStatus::class),

                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_featured'),

                TrashedFilter::make(),
            ])
            ->recordUrl(fn ($record): string => route('filament.admin.resources.products.view', $record))
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No products yet')
            ->emptyStateDescription('Create a product to start managing your catalog.');
    }
}
```

### `app/Enums/ProductStatus.php`

```php
<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;

/**
 * Cast on model: protected $casts = ['status' => ProductStatus::class];
 */
enum ProductStatus: string implements HasLabel, HasColor, HasIcon, HasDescription
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Archived => 'Archived',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'success',
            self::Archived => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => Heroicon::Pencil,
            self::Published => Heroicon::Check,
            self::Archived => Heroicon::ArchiveBox,
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::Draft => 'Hidden from customers.',
            self::Published => 'Visible in the storefront.',
            self::Archived => 'Kept for history but not actively sold.',
        };
    }
}
```

### `app/Filament/Resources/Products/Schemas/ProductInfolist.php`

```php
<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product')
                    ->columns(2)
                    ->schema([
                        ImageEntry::make('thumbnail_path')
                            ->disk('public')
                            ->square(),

                        TextEntry::make('name')
                            ->size('lg')
                            ->weight('bold'),

                        TextEntry::make('sku')
                            ->label('SKU'),

                        TextEntry::make('category.name'),

                        TextEntry::make('price')
                            ->money('USD'),

                        TextEntry::make('status')
                            ->badge(),

                        IconEntry::make('is_featured')
                            ->boolean(),
                    ]),
            ]);
    }
}
```

## Resource page lifecycle examples

### `CreateProduct.php`

```php
<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_id'] = auth()->id();

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('Product created')
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
```

### `EditProduct.php`

```php
<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected ?bool $hasDatabaseTransactions = true;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('publish')
                ->label('Publish')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status?->value !== 'published')
                ->action(function (): void {
                    $this->record->update(['status' => 'published']);

                    $this->refreshFormData(['status']);

                    Notification::make()
                        ->title('Product published')
                        ->success()
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by_id'] = auth()->id();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }
}
```

### List page tabs

```php
<?php

namespace App\Filament\Resources\Products\Pages;

use App\Enums\ProductStatus;
use App\Filament\Resources\Products\ProductResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'published' => Tab::make('Published')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ProductStatus::Published)),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ProductStatus::Draft)),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'all';
    }
}
```

## Relation manager examples

Choose the right relationship UI:

- `BelongsTo` / `MorphTo`: use `Select::make()->relationship()`.
- `BelongsToMany`: use a multi-select, checkbox list, or a relation manager with attach/detach actions.
- `HasMany` / `MorphMany`: use a relation manager for large child tables, or `Repeater::make()->relationship()` for small inline child records.
- `HasOne` / `MorphOne`: use a layout component with `->relationship()`.

### `VariantsRelationManager.php`

```php
<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    protected static ?string $title = 'Variants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(64),

                TextInput::make('stock')
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('stock')->sortable(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return ! $ownerRecord->trashed();
    }
}
```

### BelongsToMany attach/detach with pivot attributes

Model relationship must include pivot attributes on both sides:

```php
public function products()
{
    return $this->belongsToMany(Product::class)
        ->withPivot(['position', 'is_primary'])
        ->withTimestamps();
}
```

Relation manager table:

```php
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

$table
    ->columns([
        TextColumn::make('name'),
        TextColumn::make('position'),
        IconColumn::make('is_primary')->boolean(),
    ])
    ->headerActions([
        AttachAction::make()
            ->preloadRecordSelect()
            ->recordSelectSearchColumns(['name', 'sku'])
            ->schema(fn (AttachAction $action): array => [
                $action->getRecordSelect(),
                TextInput::make('position')
                    ->numeric()
                    ->default(1),
            ]),
    ])
    ->recordActions([
        DetachAction::make(),
    ])
    ->toolbarActions([
        BulkActionGroup::make([
            DetachBulkAction::make(),
        ]),
    ]);
```

## Singular settings page example

Use a custom page when the UI edits one logical record, such as site settings.

```bash
php artisan make:filament-page ManageSiteSettings
```

### `app/Filament/Pages/ManageSiteSettings.php`

```php
<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSiteSettings extends Page
{
    protected string $view = 'filament.pages.manage-site-settings';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationGroup = 'Settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getRecord()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('site_name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('support_email')
                    ->email()
                    ->required(),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save settings')
                ->action('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $record = $this->getRecord();
        $record->fill($data)->save();

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    protected function getRecord(): SiteSetting
    {
        return SiteSetting::query()->firstOrCreate([]);
    }
}
```

### `resources/views/filament/pages/manage-site-settings.blade.php`

```blade
<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit">
            Save
        </x-filament::button>
    </form>
</x-filament-panels::page>
```

## Standalone Filament components in Livewire

Use this when the user does not want a panel, but wants Filament forms/tables inside Blade/Livewire.

### Standalone form

```php
<?php

namespace App\Livewire;

use App\Models\Post;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CreatePost extends Component implements HasSchemas
{
    use InteractsWithSchemas;
    use RestrictsFileUploadsToSchemaComponents;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required(),
                MarkdownEditor::make('content'),
            ])
            ->statePath('data')
            ->model(Post::class);
    }

    public function create(): void
    {
        Post::create($this->form->getState());

        $this->form->fill();
    }

    public function render(): View
    {
        return view('livewire.create-post');
    }
}
```

```blade
<div>
    <form wire:submit="create">
        {{ $this->form }}

        <x-filament::button type="submit">
            Create
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>
```

### Standalone table

```php
<?php

namespace App\Livewire;

use App\Models\Product;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\EditAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ListProducts extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;
    use RestrictsFileUploadsToSchemaComponents;

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('price')->money('USD')->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public function render(): View
    {
        return view('livewire.list-products');
    }
}
```

```blade
<div>
    {{ $this->table }}

    <x-filament-actions::modals />
</div>
```

## Multi-tenancy example

Use Filament tenancy when users belong to many tenant records and can switch tenants. For simple one-user-one-team scoping, use Eloquent global scopes and observers instead.

### `User.php`

```php
<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;

class User extends Authenticatable implements FilamentUser, HasTenants, HasDefaultTenant
{
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->teams()->first();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail();
    }
}
```

### Panel tenancy config

```php
use App\Filament\Pages\Tenancy\EditTeamProfile;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Models\Team;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->tenant(Team::class, slugAttribute: 'slug', ownershipRelationship: 'owner')
        ->tenantRoutePrefix('team')
        ->searchableTenantMenu()
        ->tenantRegistration(RegisterTeam::class)
        ->tenantProfile(EditTeamProfile::class)
        ->tenantMiddleware([
            \App\Http\Middleware\SetTenantContext::class,
        ], isPersistent: true);
}
```

Disable tenancy scoping for global shared resources:

```php
protected static bool $isScopedToTenant = false;
```

## Widget example on a resource list page

Generate:

```bash
php artisan make:filament-widget ProductOverview --resource=ProductResource
```

Register in resource:

```php
use App\Filament\Resources\Products\Widgets\ProductOverview;

public static function getWidgets(): array
{
    return [
        ProductOverview::class,
    ];
}
```

Display on list page:

```php
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = ProductResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ProductResource\Widgets\ProductOverview::class,
        ];
    }
}
```

Widget reads list table query:

```php
<?php

namespace App\Filament\Resources\Products\Widgets;

use App\Filament\Resources\Products\Pages\ListProducts;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProductOverview extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListProducts::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Visible products', $this->getPageTableQuery()->count()),
            Stat::make('Rows on this page', $this->getPageTableRecords()->count()),
            Stat::make('Total rows', $this->tableRecordsCount),
        ];
    }
}
```

## Custom action examples

### Row action with modal form

```php
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

Action::make('markShipped')
    ->label('Mark shipped')
    ->icon(Heroicon::Truck)
    ->schema([
        Textarea::make('note')
            ->label('Shipping note')
            ->maxLength(500),
    ])
    ->requiresConfirmation()
    ->modalHeading('Mark order as shipped?')
    ->action(function (Order $record, array $data): void {
        abort_unless(auth()->user()->can('update', $record), 403);

        $record->update([
            'status' => 'shipped',
            'shipping_note' => $data['note'] ?? null,
            'shipped_at' => now(),
        ]);

        Notification::make()
            ->title('Order marked as shipped')
            ->success()
            ->send();
    });
```

### Bulk action with per-record authorization

```php
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

BulkAction::make('archive')
    ->requiresConfirmation()
    ->authorizeIndividualRecords('update')
    ->action(function (Collection $records): void {
        $records->each->update(['archived_at' => now()]);

        Notification::make()
            ->title('Selected records archived')
            ->success()
            ->send();
    });
```

## Security checklist

Apply these rules when generating code:

- Add `FilamentUser::canAccessPanel()` for production panels.
- Use Laravel model policies for Resource CRUD. Make sure `viewAny()` returns true when the resource should appear in navigation.
- Authorize custom actions manually or via `->authorize()` / policy checks.
- Never trust user-controlled URLs. Sanitize before using `->url()`:

    ```php
    use Illuminate\Support\Str;

    TextColumn::make('website')
        ->url(fn (?string $state): ?string => $state ? Str::sanitizeUrl($state) : null);
    ```

- Avoid `->html()` with raw user content unless sanitized.
- For custom Livewire components that use schemas, include `RestrictsFileUploadsToSchemaComponents`.
- In tenancy, implement `canAccessTenant()` to prevent URL tampering.
- Scope queries with policies, tenancy, or explicit `modifyQueryUsing()` when sensitive data is involved.
- Prefer `databaseTransactions()` in the panel or `->databaseTransaction(true)` on sensitive actions.

## Testing examples with Pest

### Resource list test

```php
<?php

use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    actingAs(User::factory()->create(['is_admin' => true]));
});

it('can list products', function (): void {
    $products = Product::factory()->count(5)->create();

    livewire(ListProducts::class)
        ->assertOk()
        ->assertCanSeeTableRecords($products);
});
```

### Table search/filter/sort test

```php
use App\Enums\ProductStatus;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;

use function Pest\Livewire\livewire;

it('can search filter and sort products', function (): void {
    $target = Product::factory()->create([
        'name' => 'Blue T-Shirt',
        'status' => ProductStatus::Published,
    ]);

    Product::factory()->count(3)->create([
        'status' => ProductStatus::Draft,
    ]);

    livewire(ListProducts::class)
        ->searchTable('Blue')
        ->assertCanSeeTableRecords([$target])
        ->filterTable('status', ProductStatus::Published->value)
        ->assertCanSeeTableRecords([$target])
        ->sortTable('name', 'desc')
        ->assertOk();
});
```

### Create page validation test

```php
use App\Filament\Resources\Products\Pages\CreateProduct;

use function Pest\Livewire\livewire;

it('validates required product fields', function (): void {
    livewire(CreateProduct::class)
        ->fillForm([
            'name' => null,
            'sku' => null,
            'price' => -10,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'sku' => 'required',
            'price' => 'min',
        ])
        ->assertNoRedirect();
});
```

### Action test

```php
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Models\Product;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

it('can publish a product', function (): void {
    $product = Product::factory()->draft()->create();

    livewire(EditProduct::class, ['record' => $product->getKey()])
        ->callAction('publish')
        ->assertNotified();

    expect($product->refresh()->status->value)->toBe('published');
});
```

## Troubleshooting matrix

### Resource not visible in sidebar

Check:

```php
// App\Policies\ProductPolicy.php
public function viewAny(User $user): bool
{
    return $user->is_admin;
}
```

Also check:

```php
protected static bool $shouldRegisterNavigation = true;
```

### Panel returns 404

Check:

- `AdminPanelProvider` registered in `bootstrap/providers.php`.
- Panel path: `->path('admin')` means `/admin`.
- No conflicting route in `routes/web.php`, especially if `->path('')`.
- `php artisan route:list | grep filament`.

### Form field does not save relationship data

Check:

- `Select::make('category_id')->relationship('category', 'name')` for `belongsTo`.
- Layout relationship fields use `Section::make()->relationship('metadata')` for `hasOne` / `belongsTo` style record data.
- Relation manager is registered in `getRelations()`.
- Pivot columns are declared in `withPivot()` on both related models.

### File upload fails in production

Check:

```bash
php artisan storage:link
php artisan filament:upgrade
php artisan optimize:clear
```

For production deploys, keep the Composer `post-autoload-dump` hook that runs `php artisan filament:upgrade`, or run it explicitly after `composer install`.

### Method or class not found after copying old code

Likely using v3/v4 snippets. Rewrite to v5:

- `Filament\Schemas\Schema` for resource forms/infolists.
- `->recordActions()` instead of old table row `->actions()` style.
- `->toolbarActions([BulkActionGroup::make([...])])` for bulk table actions.
- Resource subdirectories are pluralized in v5 generated code, e.g. `Resources/Products`.

## Upgrade from older Filament to v5

Use the official upgrade helper first:

```bash
composer require filament/upgrade:"^5.0" -W --dev
vendor/bin/filament-v5
composer require filament/filament:"^5.0" -W --no-update
composer update
php artisan filament:upgrade
php artisan optimize:clear
```

Then manually review:

- Namespaces and imports.
- Resource directory structure.
- Form and table builders.
- Custom actions and modal schemas.
- Policies and `canAccessPanel()`.
- Published assets and custom themes.
- Third-party plugins for v5 support.

## Navigation, clusters, and badges

### Resource navigation metadata

```php
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;

class ProductResource extends Resource
{
    protected static ?string $navigationLabel = 'Products';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 10;
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::RectangleStack;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::query()->where('stock', '<=', 5)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('viewAny', static::getModel()) ?? false;
    }
}
```

### Cluster example

```bash
php artisan make:filament-cluster Settings
```

```php
<?php

namespace App\Filament\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Icons\Heroicon;

class Settings extends Cluster
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationGroup = 'System';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;
}
```

Assign a resource or page to the cluster:

```php
protected static ?string $cluster = \App\Filament\Clusters\Settings::class;
```

## MFA example

For app-based TOTP MFA, add columns and enable it in the panel.

```php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

Schema::table('users', function (Blueprint $table): void {
    $table->text('app_authentication_secret')->nullable();
    $table->text('app_authentication_recovery_codes')->nullable();
});
```

```php
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    use InteractsWithAppAuthentication;
    use InteractsWithAppAuthenticationRecovery;
}
```

```php
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->profile()
        ->multiFactorAuthentication([
            AppAuthentication::make()
                ->recoverable()
                ->recoveryCodeCount(10),
        ], isRequired: true);
}
```

## Styling and theme assets

When the user asks for visual customization, prefer a panel theme file over inline CSS.

```php
use Filament\Panel;
use Filament\Support\Assets\Css;

public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->colors([
            'primary' => '#2563eb',
        ])
        ->assets([
            Css::make('admin-theme', resource_path('css/filament/admin/theme.css')),
        ]);
}
```

Example `resources/css/filament/admin/theme.css`:

```css
@import "tailwindcss";

.fi-btn {
    @apply rounded-xl;
}

.fi-sidebar {
    @apply border-r border-gray-200;
}
```

## Plugin and modular architecture example

Use a panel plugin when a domain module should register its own resources, pages, widgets, assets, or Livewire components without bloating the main `AdminPanelProvider`.

```php
<?php

namespace Modules\Billing;

use Filament\Contracts\Plugin;
use Filament\Panel;

class BillingPlugin implements Plugin
{
    public function getId(): string
    {
        return 'billing';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(in: __DIR__ . '/Filament/Resources', for: 'Modules\\Billing\\Filament\\Resources')
            ->discoverPages(in: __DIR__ . '/Filament/Pages', for: 'Modules\\Billing\\Filament\\Pages')
            ->discoverWidgets(in: __DIR__ . '/Filament/Widgets', for: 'Modules\\Billing\\Filament\\Widgets');
    }

    public function boot(Panel $panel): void
    {
        // Register Livewire components, macros, or render hooks here.
    }
}
```

Register it in a panel:

```php
use Modules\Billing\BillingPlugin;

$panel->plugin(BillingPlugin::make());
```

Or register conditionally from a service provider:

```php
use Filament\Panel;
use Modules\Billing\BillingPlugin;

Panel::configureUsing(function (Panel $panel): void {
    if ($panel->getId() === 'admin') {
        $panel->plugin(BillingPlugin::make());
    }
});
```

## Deployment checklist

For production deploys, include:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan filament:upgrade
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Verify:

- `APP_URL` matches the real domain and scheme.
- Storage link exists if using public uploads: `php artisan storage:link`.
- `php artisan filament:upgrade` runs after Composer updates.
- Queue worker is running if imports, exports, notifications, or jobs are queued.
- Policies and `canAccessPanel()` are production-safe.

## Quality gate before final answer

Before responding to a user with Filament code, verify:

- Does every PHP file have the correct namespace and imports?
- Is the answer using Filament 5.x syntax, not older snippets?
- Are commands compatible with Laravel 11+ and v5 package constraints?
- Are relationships named according to the Eloquent model methods?
- Are table actions placed in `headerActions`, `recordActions`, or `toolbarActions` correctly?
- Are resource policies and panel access considered?
- Are database transactions or explicit validation used for sensitive mutations?
- Is there a verification step the user can run immediately?
