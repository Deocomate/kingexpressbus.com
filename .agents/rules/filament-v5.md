# codebase | 5 files | 2026-05-27 06:33:12

// 01_Panel_Provider.md
# Filament Panel & Navigation Configuration Cheatsheet
Reference guide for configuring Filament panels (`PanelProvider`), custom pages, navigation structure, user authentication, and basic tenancy.
---
## 1. Panel Configuration Reference (`AdminPanelProvider.php`)
```php
namespace App\Providers\Filament;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\AvatarProviders\BoringAvatarsProvider;
use Filament\Http\Middleware\Authenticate;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin') // URL prefix (empty string '' for root)
            ->domain('admin.example.com') // Subdomain routing
            // Authentication & Profile
            ->login() // Enable default login
            ->registration() // Enable registration
            ->passwordReset() // Enable password reset
            ->emailVerification() // Enable email verification
            ->profile(EditProfile::class) // Custom profile page class
            ->profile(isSimple: false) // Use standard sidebar layout for profile
            ->authGuard('web') // Custom auth guard
            ->authPasswordBroker('users') // Custom password broker
            ->revealablePasswords(false) // Disable password reveal button
            // Registration slugs
            ->loginRouteSlug('login')
            ->registrationRouteSlug('register')
            // User Avatars
            ->defaultAvatarProvider(BoringAvatarsProvider::class)
            // Layout & Navigation
            ->sidebarCollapsibleOnDesktop() // Collapsible sidebar
            ->sidebarFullyCollapsibleOnDesktop() // Hides sidebar completely on collapse
            ->sidebarWidth('20rem') // Custom sidebar width
            ->collapsedSidebarWidth('9rem') // Collapsed sidebar width
            ->topNavigation() // Switch to horizontal top navigation
            ->breadcrumbs(false) // Disable breadcrumbs
            ->maxContentWidth(Width::Full) // Max width: Width::[ExtraSmall|Small|Medium|Large|Full|...]
            ->simplePageMaxContentWidth(Width::Small) // Simple page max width
            ->subNavigationPosition(SubNavigationPosition::End) // Position: Start, End, Top (as tabs)
            // Database & Transactions
            ->databaseTransactions() // Enable DB transactions globally for mutations
            ->strictAuthorization() // Throw exceptions if policy/method is missing
            // Echo & Broadcasting
            ->broadcasting(false) // Disable automatic Laravel Echo connections
            // Error Notifications (Flash replacement for Livewire fullscreen errors)
            ->errorNotifications(true)
            ->registerErrorNotification(title: 'Error', body: 'Try again.', statusCode: 404)
            ->hiddenErrorNotification(403) // Silent fail
            ->disabledErrorNotification(503) // Fall back to Livewire default handler
            // Middleware Registration
            ->middleware([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                // ...
            ], isPersistent: true) // persistent = runs on Livewire AJAX requests too
            ->authMiddleware([
                Authenticate::class,
            ], isPersistent: true)
            // Custom Assets
            ->assets([
                Css::make('custom-stylesheet', resource_path('css/custom.css')),
                Js::make('custom-script', resource_path('js/custom.js')),
            ])
            // Render Hooks
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_START,
                fn (): string => \Illuminate\Support\Facades\Blade::render('@livewire(\'livewire-ui-modal\')')
            )
            // Boot callback (executed on every request within this panel)
            ->bootUsing(function (Panel $panel) {
                // Custom boot logic
            });
    }
}
```
---
## 2. Navigation Customization
Configure navigation behavior inside Resources, Custom Pages, or the Panel Provider.
### Navigation Properties in Resource/Page Classes
```php
namespace App\Filament\Resources;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
class PostResource extends Resource
{
    protected static ?string $navigationLabel = 'Custom Label';
    protected static ?int $navigationSort = 1; // Ascending order
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;
    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::SolidDocumentText; // Active state
    // Grouping
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationParentItem = 'Posts'; // Nested under another nav item
    // Disable showing in sidebar (still permits direct URL access)
    protected static bool $shouldRegisterNavigation = false;
    // Dynamic Overrides
    public static function getNavigationLabel(): string => __('custom.posts');
    public static function getNavigationParentItem(): ?string => __('custom.parent');
    public static function shouldRegisterNavigation(): bool => auth()->user()->isAdmin();
    // Badges
    public static function getNavigationBadge(): ?string => (string) static::getModel()::count();
    public static function getNavigationBadgeColor(): ?string => static::getModel()::count() > 10 ? 'warning' : 'success';
    public static function getNavigationBadgeTooltip(): ?string => 'Total posts count';
}
```
### Custom Navigation Groups (Panel Configuration)
```php
use Filament\Navigation\NavigationGroup;
$panel
    ->collapsibleNavigationGroups(false) // Disable collapsibility globally
    ->navigationGroups([
        // Order and style groups
        NavigationGroup::make()
            ->label('Shop')
            ->icon(Heroicon::OutlinedShoppingCart)
            ->collapsed(),
        NavigationGroup::make()
            ->label('Settings')
            ->collapsible(false)
            ->extraSidebarAttributes(['class' => 'custom-sidebar-class'])
            ->extraTopbarAttributes(['class' => 'custom-topbar-class']),
    ]);
```
### Custom Navigation Builder (Fully Manual Routing)
```php
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use App\Filament\Pages\Dashboard;
$panel->navigation(function (NavigationBuilder $builder): NavigationBuilder {
    return $builder
        ->items([
            NavigationItem::make('Dashboard')
                ->icon(Heroicon::OutlinedHome)
                ->url(fn (): string => Dashboard::getUrl())
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard')),
        ])
        ->groups([
            NavigationGroup::make('Blog')
                ->items([
                    ...PostResource::getNavigationItems(),
                ]),
        ]);
});
// Disable navigation sidebar dynamically
$panel->navigation(fn (): bool => auth()->user()->hasCompletedOnboarding());
```
### Sidebar & Topbar Reloading (Via Livewire / Alpine.js)
```php
// PHP (Livewire component, Action, Widget)
$this->dispatch('refresh-sidebar');
$this->dispatch('refresh-topbar');
// JS / Alpine.js
window.dispatchEvent(new CustomEvent('refresh-sidebar'));
```
---
## 3. Clusters (Hierarchical Navigation Grouping)
Group resources and pages into a cluster to reduce sidebar clutter and auto-apply sub-navigation and URL prefixes.
### Registering Clusters in Panel
```php
$panel->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters');
```
### Defining a Cluster Class
```php
namespace App\Filament\Clusters;
use Filament\Clusters\Cluster;
use Filament\Support\Icons\Heroicon;
use Filament\Pages\Enums\SubNavigationPosition;
class SettingsCluster extends Cluster
{
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSquares2x2;
    protected static ?string $clusterBreadcrumb = 'System Settings';
    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::End; // Start, End, Top
    protected static bool $shouldRegisterSubNavigation = true; // Set to false to disable sub-navigation
}
```
### Assigning Resource/Page to Cluster
```php
protected static ?string $cluster = \App\Filament\Clusters\SettingsCluster::class;
```
---
## 4. User Model Contracts & Customizing Auth
Implement Filament contracts in `App\Models\User` to handle access, avatars, and names.
### Core Contracts & Avatars
```php
namespace App\Models;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable implements FilamentUser, HasAvatar, HasName
{
    // Access control (Required for production environments)
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return str_ends_with($this->email, '@admin.com');
        }
        return true;
    }
    // Custom name rendering
    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
    // Custom avatar retrieval
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url; // Returns null to fallback to ui-avatars.com
    }
}
```
### Custom Avatar Provider
```php
namespace App\Filament\AvatarProviders;
use Filament\AvatarProviders\Contracts\AvatarProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
class BoringAvatarsProvider implements AvatarProvider
{
    public function get(Model | Authenticatable $record): string
    {
        return 'https://source.boringavatars.com/beam/120/' . urlencode($record->name);
    }
}
```
### Customizing Authentication Page Forms
Extend page classes and override component getters to modify fields without replacing the entire page.
```php
namespace App\Filament\Pages\Auth;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('username')->required(),
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent()->revealable(false), // chain customization
        ]);
    }
}
```
---
## 5. User Menu Customization
Custom actions and settings dropdown located in the panel topbar/sidebar.
```php
use Filament\Actions\Action;
use Filament\Enums\UserMenuPosition;
$panel
    ->userMenu(position: UserMenuPosition::Sidebar) // Always sidebar (default: Topbar)
    ->userMenu(false) // Disable user menu completely
    ->userMenuItems([
        'profile' => fn (Action $action) => $action->label('My Settings'), // Customize profile link
        'logout' => fn (Action $action) => $action->label('Sign Out'), // Customize logout link
        // Custom menu items
        Action::make('billing')
            ->label('Billing Info')
            ->icon('heroicon-o-credit-card')
            ->url(fn (): string => route('billing'))
            ->visible(fn (): bool => auth()->user()->can('view-billing'))
            ->postToUrl(), // Executes POST request instead of GET
    ]);
```

// 02_Resources_Lifecycle.md
# Filament Resources & Lifecycle Cheatsheet
Reference guide for Filament Resources, resource page lifecycles, Eloquent query customization, global search, and core UI schemas (Forms, Tables, Actions, Infolists, Notifications).
---
## 1. Resource Class Template
```php
namespace App\Filament\Resources;
use App\Models\Customer;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\Builder;
class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $slug = 'customers'; // URL slug (default: model plural)
    // Labels & Customization
    protected static ?string $modelLabel = 'customer';
    protected static ?string $pluralModelLabel = 'customers';
    protected static bool $hasTitleCaseModelLabel = true;
    // Navigation & Icons (See 01_Panel_Provider.md)
    protected static ?string $navigationLabel = 'Customers';
    protected static ?string $navigationGroup = 'Users';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';
    // Global Search Configuration
    protected static ?string $recordTitleAttribute = 'name'; // Required for global search
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone']; // Fields to search
    }
    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->name; // Custom title
    }
    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'Email' => $record->email, // Key-value details under search title
        ];
    }
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['category']); // Eager load
    }
    public static function getGlobalSearchResultUrl(\Illuminate\Database\Eloquent\Model $record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }
    // Eloquent Query Customization
    public static function getEloquentQuery(): Builder
    {
        // Custom scopes, removing global scopes (e.g. Soft Deletes)
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
    // Core Schemas
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Forms\Components\TextInput::make('name')->required(),
        ]);
    }
    public static function table(Table $table): Table
    {
        return $table->columns([
            \Filament\Tables\Columns\TextColumn::make('name')->searchable(),
        ]);
    }
    public static function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema->components([
            \Filament\Infolists\Components\TextEntry::make('name'),
        ]);
    }
    // Sub-Navigation (Switching between pages of a specific record)
    protected static ?\Filament\Pages\Enums\SubNavigationPosition $subNavigationPosition = \Filament\Pages\Enums\SubNavigationPosition::End;
    public static function getRecordSubNavigation(\Filament\Resources\Pages\Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewCustomer::class,
            Pages\EditCustomer::class,
        ]);
    }
    // Page Routes
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
    // Relation Managers
    public static function getRelations(): array
    {
        return [
            // RelationsManager::class
        ];
    }
}
```
---
## 2. Resource Page Lifecycles & Hooks
All resource pages are Livewire components. They offer hooks executed before/after standard actions. Halting is supported using `$this->halt()` or throwing validation exceptions.
### A. List Page (`ListRecords`)
```php
namespace App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;
    // Custom tabs with query filters
    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'active' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('active', true))
                ->badge(Customer::query()->where('active', true)->count()),
        ];
    }
    public function getDefaultActiveTab(): string | int | null
    {
        return 'active';
    }
    // Page widgets
    protected function getHeaderWidgets(): array => [ /* StatsWidget::class */ ];
}
```
### B. Create Page (`CreateRecord`)
**Execution Order:** `beforeFill` $\rightarrow$ `afterFill` $\rightarrow$ `beforeValidate` $\rightarrow$ `afterValidate` $\rightarrow$ `beforeCreate` $\rightarrow$ `handleRecordCreation` $\rightarrow$ `afterCreate`
```php
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
    protected ?bool $hasDatabaseTransactions = true;
    // 1. Mutate form inputs prior to saving/creating
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }
    // 2. Custom creation logic (Optional override)
    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }
    // 3. Before/After callbacks
    protected function beforeCreate(): void
    {
        // Custom validations / checks. Call $this->halt() to cancel operation.
    }
    protected function afterCreate(): void
    {
        // Run after DB write (e.g., dispatch job, log audits)
    }
    // 4. Redirect Url after creation
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    // 5. Notifications
    protected function getCreatedNotificationTitle(): ?string => 'Customer Registered!';
}
```
### C. Edit Page (`EditRecord`)
**Execution Order:** `beforeFill` $\rightarrow$ `afterFill` $\rightarrow$ `beforeValidate` $\rightarrow$ `afterValidate` $\rightarrow$ `beforeSave` $\rightarrow$ `handleRecordUpdate` $\rightarrow$ `afterSave`
```php
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;
    // Mutate record prior to filling the form
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['filled_at'] = now();
        return $data;
    }
    // Mutate data before saving
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['editor_id'] = auth()->id();
        return $data;
    }
    // Custom update logic (Optional override)
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        return $record;
    }
    protected function beforeSave(): void { /* ... */ }
    protected function afterSave(): void { /* ... */ }
    protected function getRedirectUrl(): string => $this->getResource()::getUrl('index');
    protected function getSavedNotificationTitle(): ?string => 'Customer settings updated';
}
```
---
## 3. Core UI Schemas Reference
Filament is Server-Driven UI configured through PHP arrays of components.
### Form Configuration (Fields)
```php
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Operation;
TextInput::make('password')
    ->password()
    ->required()
    ->hiddenOn(Operation::Edit) // Visible on Create only
    ->visibleOn(Operation::Create)
    ->disabled();
```
### Table Columns, Filters, & Actions
```php
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;
$table
    ->columns([
        TextColumn::make('name')
            ->searchable()
            ->sortable(),
    ])
    ->filters([
        Filter::make('active')
            ->query(fn ($query) => $query->where('is_active', true)),
    ])
    ->recordActions([
        EditAction::make(),
    ])
    ->toolbarActions([
        DeleteBulkAction::make(),
    ])
    ->reorderable('sort_order'); // Drag-drop reordering
```
### Notifications Fluent Builder
```php
use Filament\Notifications\Notification;
Notification::make()
    ->title('Order Completed')
    ->body('The order has been shipped successfully.')
    ->success() // success, warning, danger, info
    ->duration(5000)
    ->send();
```
### Actions API
```php
use Filament\Actions\Action;
Action::make('approve')
    ->action(function ($record) {
        $record->approve();
    })
    ->requiresConfirmation()
    ->modalHeading('Approve Item')
    ->modalDescription('Are you sure?')
    ->modalSubmitActionLabel('Confirm Approval')
    ->databaseTransaction(true); // Wrap execution in a database transaction
```
---
## 4. Singular Resources & Custom Pages
If you want a page dedicated to a single record (e.g., General Settings, Site Homepage), implement it as a Custom Page.
```php
namespace App\Filament\Pages;
use App\Models\WebsitePage;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
class ManageHomepage extends Page
{
    protected string $view = 'filament.pages.manage-homepage';
    public ?array $data = [];
    public function mount(): void
    {
        // Populate the form state
        $this->form->fill($this->getRecord()?->attributesToArray());
    }
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required(),
            ])
            ->record($this->getRecord())
            ->statePath('data');
    }
    public function save(): void
    {
        $data = $this->form->getState();
        $record = $this->getRecord() ?? new WebsitePage(['is_homepage' => true]);
        $record->fill($data)->save();
        if ($record->wasRecentlyCreated) {
            $this->form->record($record)->saveRelationships();
        }
        Notification::make()->success()->title('Settings saved.')->send();
    }
    public function getRecord(): ?WebsitePage
    {
        return WebsitePage::where('is_homepage', true)->first();
    }
}
```
---
## 5. Nesting Resources (Parent-Child Routing)
Generate child resource views that exist scoped strictly inside a parent resource scope.
```php
namespace App\Filament\Resources\OrderResource\Pages;
use Filament\Resources\Pages\ParentResourceRegistration;
use App\Filament\Resources\CustomerResource;
// On Child Resource (e.g. OrderResource)
class ManageOrders extends \Filament\Resources\Pages\ManageRelatedRecords
{
    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return ParentResourceRegistration::make(CustomerResource::class);
    }
}
```

// 03_Relation_Managers.md
# Filament Relation Managers Cheatsheet
Reference guide for creating, configuring, and optimizing Filament Relation Managers (interactive child tables underneath resource forms).
---
## 1. Basic Relation Manager Class
Create using: `php artisan make:filament-relation-manager CategoryResource posts title`
```php
namespace App\Filament\Resources\CategoryResource\RelationManagers;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
class PostsRelationManager extends RelationManager
{
    // Eloquent relationship name on the owner model
    protected static string $relationship = 'posts'; 
    // Custom label for pages/tab (defaults to relationship name)
    protected static ?string $title = 'Blog Posts';
    // Access the owner record instance in standard methods
    // $this->getOwnerRecord();
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            // Access owner inside closure:
            TextInput::make('code')
                ->default(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->code),
        ]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title') // Attribute representing the record
            ->inverseRelationship('category') // Unconventional inverse relationship name
            ->columns([
                TextColumn::make('title'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
    // Disable read-only mode on Resource View Pages (where actions are hidden by default)
    public function isReadOnly(): bool => false;
    // Conditionally show/hide this Relation Manager
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->is_active; 
    }
}
```
### Registering in Parent Resource
```php
use App\Filament\Resources\CategoryResource\RelationManagers\PostsRelationManager;
public static function getRelations(): array
{
    return [
        'posts' => PostsRelationManager::class, // Named index scopes URL parameter: ?relation=posts
    ];
}
```
---
## 2. Pivot Table Attributes (`BelongsToMany` / `MorphToMany`)
Manage extra columns in pivot tables. Ensure pivot attributes are defined in `withPivot()` on Laravel model relations.
```php
// Inside RelationManager table()
$table
    ->columns([
        TextColumn::make('name'),
        TextColumn::make('role'), // Pivot column
    ])
    ->allowDuplicates(); // Allow attaching the same record multiple times (requires primary id on pivot table)
```
---
## 3. Relationship Actions (Attach, Associate, Detach, Dissociate)
Use specialized actions to connect existing records in DB without recreating them.
### A. Attach / Detach Actions (`BelongsToMany` / `MorphToMany`)
```php
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use App\Filament\Resources\Products\Tables\ProductsTable;
$table
    ->headerActions([
        AttachAction::make()
            ->preloadRecordSelect() // Preload options on load instead of query via AJAX
            ->multiple() // Allow selecting multiple records to attach
            // Limit options to choose from
            ->recordSelectOptionsQuery(fn ($query) => $query->where('active', true))
            // Search across multiple columns
            ->recordSelectSearchColumns(['title', 'sku'])
            // Customize Select input object
            ->recordSelect(fn (Select $select) => $select->placeholder('Choose items'))
            // Form schema to record extra pivot attributes
            ->schema(fn (AttachAction $action): array => [
                $action->getRecordSelect(), // Default picker
                TextInput::make('role')->required(), // Extra pivot attribute
            ])
            // Use full Filament Table instead of simple Select dropdown to pick attachments
            ->tableSelect(ProductsTable::class),
    ])
    ->recordActions([
        DetachAction::make(),
    ])
    ->bulkActions([
        DetachBulkAction::make()
            ->chunkSelectedRecords(250) // Optimization for large datasets
            ->fetchSelectedRecords(false), // Disable loading models into memory (no observer events / auth checks)
    ]);
```
### B. Associate / Dissociate Actions (`HasMany` / `MorphMany`)
Moves child records into/out of parent relationship constraints (sets/nullifies foreign key column).
```php
use Filament\Actions\AssociateAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
$table
    ->headerActions([
        AssociateAction::make()
            ->preloadRecordSelect()
            ->multiple()
            ->recordSelectSearchColumns(['title']),
    ])
    ->recordActions([
        DissociateAction::make(),
    ])
    ->bulkActions([
        DissociateBulkAction::make()
            ->fetchSelectedRecords(false), // Skips memory load for high performance
    ]);
```
---
## 4. Soft-Deletes Configuration
If managing soft-deletable records inside child tables:
```php
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
$table
    // Override default Eloquent scope to include trashed records
    ->modifyQueryUsing(fn ($query) => $query->withoutGlobalScopes([SoftDeletingScope::class]))
    ->filters([
        TrashedFilter::make(),
    ])
    ->recordActions([
        RestoreAction::make(),
        ForceDeleteAction::make(),
    ])
    ->bulkActions([
        RestoreBulkAction::make(),
        ForceDeleteBulkAction::make(),
    ]);
```
---
## 5. Relation Managers Grouping & Tab Layout
Consolidate multiple child tables under a single tab or combine them directly with the parent resource form.
### Grouping Multiple Managers in Tabs
```php
use Filament\Resources\RelationManagers\RelationGroup;
public static function getRelations(): array
{
    return [
        RelationGroup::make('Audits & Logs', [
            RelationManagers\CommentsRelationManager::class,
            RelationManagers\ActivityLogsRelationManager::class,
        ]),
    ];
}
```
### Combining Relation Tabs with Edit/View Form
By default, relation managers are placed below the main form. Override this on the `EditRecord` or `ViewRecord` page class to display them in unified tabs alongside the main form.
```php
// Inside App\Filament\Resources\CustomerResource\Pages\EditCustomer.php
class EditCustomer extends EditRecord
{
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true; // Merges form and relation managers into one tabbed system
    }
    public function getContentTabPosition(): ?\Filament\Resources\Pages\ContentTabPosition
    {
        return \Filament\Resources\Pages\ContentTabPosition::After; // Or Before
    }
}
```

// 04_Advanced_Architecture.md
# Filament Advanced Architecture Cheatsheet
Reference guide for Advanced Multi-Tenancy, Security Rules (XSS/Policy limits), PHP Enums integrations, Domain-Driven Design (Modular Architecture), and Custom Plugin development.
---
## 1. Advanced Multi-Tenancy
Implement many-to-many tenancy where users can register, manage, and switch between multiple tenant boundaries.
### A. User Tenancy Configuration (`User` model)
```php
namespace App\Models;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\HasDefaultTenant;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
class User extends Model implements FilamentUser, HasTenants, HasDefaultTenant
{
    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }
    // Get list of tenants the user belongs to
    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }
    // Authorize user access to a specific tenant (stops URL tampering)
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }
    // Default tenant to redirect to after login
    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->teams()->first();
    }
}
```
### B. Tenant Configuration & Custom Swapping (Panel Provider)
```php
use App\Models\Team;
use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Filament\Pages\Tenancy\EditTeamProfile;
use Filament\Billing\Providers\SparkBillingProvider;
$panel
    // Register Tenant Model & Custom Routing
    ->tenant(Team::class, slugAttribute: 'slug', ownershipRelationship: 'owner')
    ->tenantRoutePrefix('team') // URL structure: /admin/team/{tenant_slug}/...
    ->tenantDomain('{tenant:slug}.example.com') // Subdomain routing
    // Switcher UI
    ->searchableTenantMenu() // Enable search box in tenant menu
    ->tenantSwitcher(false) // Keep menu showing tenant name but disable switcher list
    ->tenantMenu(false) // Hide tenant menu completely (use if user has exactly 1 tenant)
    // Tenancy Lifecycle pages
    ->tenantRegistration(RegisterTeam::class)
    ->tenantProfile(EditTeamProfile::class)
    ->tenantMiddleware([
        \App\Http\Middleware\TenantSetupMiddleware::class,
    ], isPersistent: true) //persistent runs on Livewire AJAX requests too
    // Billing Integration (Laravel Spark Example)
    ->tenantBillingProvider(new SparkBillingProvider())
    ->requiresTenantSubscription() // Require billing subscription globally
    ->tenantBillingRouteSlug('billing');
```
*Inside Child Resources: Disable tenancy scoping if resources are shared globally:*
```php
// Inside shared Resource class (e.g. CategoryResource)
protected static bool $isScopedToTenant = false;
```
---
## 2. Security, Policies, & XSS Sanitization
### A. Authorization Hooks Order (Custom Livewire Components)
Filament re-runs policy checks on every Livewire request. Be aware of the lifecycle execution order:
1. Public properties are deserialized from payload.
2. `boot()` & `boot{TraitName}()` fire **prior to authorization**.
3. User `mount()` body runs **before trait-level `mount` hooks**.
4. Property `hydrate{PropertyName}()` hooks fire **after authorization** but before action updates.
> [!CAUTION]
> Always execute sensitive database mutations inside actions (e.g. `wire:click`) or explicitly invoke `$this->authorizeAccess()` inside `mount()` prior to running side-effects.
### B. URL Schema Sanitization (Preventing `javascript:` XSS)
Always sanitize dynamic URL inputs mapped to link fields using the `Str::sanitizeUrl()` helper:
```php
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;
TextColumn::make('website')
    ->url(fn (string $state): ?string => Str::sanitizeUrl($state))
    // Or specify an allowed list of schemas:
    ->url(fn (string $state): ?string => Str::sanitizeUrl(
        $state,
        allowedSchemes: ['http', 'https', 'mailto', 'tel']
    ));
```
### C. Container Scoped HTML Sanitizer (Symfony)
Filament strips dangerous HTML tags inside `html()` or `markdown()` renderers using Symfony's `HtmlSanitizer`. Extend or restrict attributes in a service provider:
```php
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
public function register(): void
{
    $this->app->extend(HtmlSanitizerConfig::class, function (HtmlSanitizerConfig $config) {
        return $config
            ->allowAttribute('data-custom', allowedElements: '*')
            ->dropAttribute('style', '*'); // Drop style attributes globally
    });
}
```
### D. RESTRICT Livewire File Uploads
Ensure users cannot upload files via tampered Livewire RPC requests on pages without upload fields:
```php
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Livewire\Component;
class ViewProduct extends Component implements \Filament\Schemas\Contracts\HasSchemas
{
    use InteractsWithSchemas;
    use RestrictsFileUploadsToSchemaComponents; // Rejects uploads not targeting active FileUpload schema fields
}
```
---
## 3. PHP Enums Integration
Add Filament interfaces to Enums to automate labels, colors, icons, and descriptions in Tables, Forms, and Infolists.
```php
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasDescription;
enum OrderStatus: string implements HasLabel, HasColor, HasIcon, HasDescription
{
    case Pending = 'pending';
    case Shipped = 'shipped';
    public function getLabel(): ?string => $this->name;
    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Shipped => 'success',
        };
    }
    public function getIcon(): ?string
    {
        return match ($this) {
            self::Pending => 'heroicon-o-clock',
            self::Shipped => 'heroicon-o-truck',
        };
    }
    public function getDescription(): ?string
    {
        return match ($this) {
            self::Pending => 'Order is awaiting payment.',
            self::Shipped => 'Order is on its way.',
        };
    }
}
```
*Automated usage in Schemas:*
```php
// Dropdowns, radios, and checkbox lists auto-extract values and descriptions
Select::make('status')->options(OrderStatus::class);
Radio::make('status')->options(OrderStatus::class);
// Table columns & infolists auto-render labels, icons, and colors
TextColumn::make('status')->badge(); 
TextEntry::make('status')->badge();
```
---
## 4. Modular Architecture (DDD) & Plugins
Organize application components inside Domain Modules (using packages like `internachi/modular`).
### A. Creating a Panel Plugin for a Domain Module
Defines resources and configurations that register automatically inside a panel lifecycle.
```php
namespace Modules\Billing;
use Filament\Contracts\Plugin;
use Filament\Panel;
class BillingPlugin implements Plugin
{
    public function getId(): string => 'billing-domain';
    public static function make(): static => app(static::class);
    public function register(Panel $panel): void
    {
        // Auto-discover components inside module package directories
        $panel
            ->discoverResources(in: __DIR__ . '/Filament/Resources', for: 'Modules\\Billing\\Filament\\Resources')
            ->discoverPages(in: __DIR__ . '/Filament/Pages', for: 'Modules\\Billing\\Filament\\Pages')
            ->discoverWidgets(in: __DIR__ . '/Filament/Widgets', for: 'Modules\\Billing\\Filament\\Widgets');
    }
    public function boot(Panel $panel): void
    {
        // Register Livewire components utilized by this module's pages
        \Livewire\Livewire::component('custom-billing-chart', \Modules\Billing\Livewire\BillingChart::class);
    }
}
```
### B. Conditional Module Activation (Module Service Provider)
Intercept panel setups and register plugins without editing the core `PanelProvider` files:
```php
namespace Modules\Billing\Providers;
use Illuminate\Support\ServiceProvider;
use Filament\Panel;
use Modules\Billing\BillingPlugin;
class BillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            // Register plugin conditionally based on panel ID
            match ($panel->getId()) {
                'admin' => $panel->plugin(BillingPlugin::make()->enableAdminFeatures()),
                'portal' => $panel->plugin(BillingPlugin::make()),
                default => null,
            };
        });
    }
}
```

// 05_Testing_Snippets.md
# Filament Testing Snippets Cheatsheet
Reference guide for testing Filament panels, resources, tables, forms, custom actions, and wizards using Pest and Livewire.
---
## 1. Setup & Environment Authentication
### Authentication Setup (Pest)
```php
use App\Models\User;
beforeEach(function () {
    $user = User::factory()->create();
    actingAs($user); // Authenticate for Pest tests
});
```
### Multi-Panel & Multi-Tenant Setup
If running direct Livewire component tests (without HTTP requests bootstrapping the panel provider middleware), set panels and tenants manually:
```php
use Filament\Facades\Filament;
it('tests multi-tenant panel', function () {
    $team = Team::factory()->create();
    Filament::setCurrentPanel('admin'); // Set active panel ID
    Filament::setTenant($team); // Set active tenant
    Filament::bootCurrentPanel(); // Re-run scopes and observers
    // ... test logic
});
```
---
## 2. Table Assertions (`ListRecords` / `RelationManager`)
```php
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
it('tests table interactions', function () {
    $users = User::factory()->count(5)->create();
    $targetUser = $users->first();
    livewire(ListUsers::class)
        ->assertOk()
        // Visibility
        ->assertCanSeeTableRecords($users)
        ->assertCanNotSeeTableRecords($users->skip(5))
        // Search
        ->searchTable($targetUser->name)
        ->assertCanSeeTableRecords([$targetUser])
        // Sorting
        ->sortTable('name', 'desc')
        ->assertCanSeeTableRecords($users->sortByDesc('name'), inOrder: true)
        // Filtering
        ->filterTable('locale', 'en')
        ->assertCanSeeTableRecords($users->where('locale', 'en'))
        // Bulk Actions
        ->selectTableRecords($users->pluck('id')->toArray())
        ->callAction(TestAction::make(DeleteBulkAction::class)->table()->bulk())
        ->assertNotified();
});
```
---
## 3. Schema & Form Assertions (`HasSchemas`)
### A. Core Form Assertions
```php
use App\Filament\Resources\Users\Pages\CreateUser;
it('fills and validates form', function () {
    livewire(CreateUser::class)
        ->assertSchemaExists('form') // Assert form exists
        // Fill form fields (specify form name if multiple e.g. 'createPostForm')
        ->fillForm([
            'name' => 'John Doe',
            'email' => 'invalid-email',
        ])
        ->call('create')
        // Validation Assertions
        ->assertHasFormErrors(['email' => 'email']) // Checks specific rules
        ->assertNotNotified()
        ->assertNoRedirect()
        // Check state value
        ->assertSchemaStateSet([
            'name' => 'John Doe',
        ]);
});
```
### B. Custom State Validation (Closure)
Pass a callback to run custom assertions on the form's state:
```php
livewire(CreateUser::class)
    ->fillForm(['name' => 'John Doe'])
    ->assertSchemaStateSet(function (array $state): array {
        expect($state['name'])->not->toContain(' ');
        return ['name' => 'John Doe']; // Return state to continue validation chain
    });
```
### C. Field & Component State Assertions
Check if form fields exist, are visible, hidden, enabled, or disabled.
```php
// Fields (By Name)
livewire(CreateUser::class)
    ->assertFormFieldExists('title', fn ($field) => $field->isDisabled())
    ->assertFormFieldDoesNotExist('password_hash')
    ->assertFormFieldVisible('title')
    ->assertFormFieldHidden('hidden_field')
    ->assertFormFieldEnabled('title')
    ->assertFormFieldDisabled('readonly_field');
// Non-field Layout Components (By Key)
livewire(CreateUser::class)
    ->assertSchemaComponentExists('comments-section', fn ($section) => $section->getHeading() === 'Comments')
    ->assertSchemaComponentDoesNotExist('debug-section')
    ->assertSchemaComponentVisible('comments-section')
    ->assertSchemaComponentHidden('comments-section');
```
### D. Repeaters & Builders (Disabling UUID Fakes)
Repeaters/builders generate UUIDs for tracking layout items. Use `fake()` to swap UUIDs with simple numeric array keys during testing:
```php
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Builder;
$undoRepeaterFake = Repeater::fake();
$undoBuilderFake = Builder::fake();
livewire(EditPost::class, ['record' => $post])
    ->assertSchemaStateSet([
        'quotes' => [
            ['content' => 'First quote'], // Keyed by integer 0 rather than UUID
        ]
    ]);
$undoRepeaterFake();
$undoBuilderFake();
```
*Interacting with repeater component actions:*
```php
// Target action on specific relationship record item: prefix id with 'record-'
livewire(EditPost::class, ['record' => $post])
    ->callAction(TestAction::make('sendQuote')->schemaComponent('quotes')->arguments([
        'item' => "record-{$quoteId}",
    ]))
    ->assertNotified();
```
### E. Wizards
```php
livewire(CreatePost::class)
    ->goToNextWizardStep() // Step validation executes here
    ->assertHasFormErrors(['title'])
    ->goToPreviousWizardStep()
    ->goToWizardStep(2) // Jump to step
    ->assertWizardCurrentStep(2);
```
---
## 4. Actions & Modals Assertions
Test custom actions embedded in tables, headers, forms, or infolists.
### A. Calling Actions
```php
use App\Models\Invoice;
use Filament\Actions\Testing\TestAction;
// 1. Page Header Action
livewire(EditInvoice::class, ['record' => $invoice])
    ->callAction('send');
// 2. Table Row Action (Requires record instance)
livewire(ListInvoices::class)
    ->callAction(TestAction::make('send')->table($invoice));
// 3. Table Header Action (Create actions, etc.)
livewire(ListInvoices::class)
    ->callAction(TestAction::make('create')->table());
// 4. Infolist Entry Action (Action inside belowContent, etc.)
livewire(EditInvoice::class)
    ->callAction(TestAction::make('send')->schemaComponent('customer_id'));
// 5. Nested Actions (Action opening inside another action's modal/form schema)
livewire(ManageInvoices::class)
    ->callAction([
        TestAction::make('view')->table($invoice),
        TestAction::make('send')->schemaComponent('customer.name'),
    ]);
```
### B. Action Forms, Modals & Validation
For actions that open modal forms:
```php
livewire(EditInvoice::class, ['invoice' => $invoice])
    ->mountAction('send') // Open modal (doesn't submit)
    ->assertMountedActionModalSee($invoice->recipient_email) // Check text in modal HTML
    ->fillForm([
        'email' => 'invalid-email',
    ])
    ->callMountedAction() // Submit modal
    ->assertHasFormErrors(['email' => ['email']])
    ->assertActionHalted('send'); // Assert execution was halted by validation/callback
```
### C. Action Property Assertions
```php
livewire(EditInvoice::class, ['invoice' => $invoice])
    ->assertActionExists('send')
    ->assertActionDoesNotExist('unsend')
    ->assertActionVisible('send')
    ->assertActionHidden('unsend')
    ->assertActionEnabled('send')
    ->assertActionDisabled('print')
    ->assertActionListInOrder(['send', 'export'])
    ->assertActionHasLabel('send', 'Email Invoice')
    ->assertActionHasIcon('send', 'envelope-open')
    ->assertActionHasColor('delete', 'danger')
    ->assertActionHasUrl('filament', 'https://filamentphp.com/')
    ->assertActionShouldOpenUrlInNewTab('filament');
```
### D. Testing Custom Action Classes
If using custom action classes, call them via class names. Ensure they implement `getDefaultName()` or the `#[ActionName]` attribute.
```php
use App\Filament\Resources\Invoices\Actions\SendInvoiceAction;
livewire(ManageInvoices::class)
    ->callAction(TestAction::make(SendInvoiceAction::class)->table($invoice));
```

