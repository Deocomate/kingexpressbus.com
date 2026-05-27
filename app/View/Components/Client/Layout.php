<?php

namespace App\View\Components\Client;

use App\Models\Menu;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\Component;

class Layout extends Component
{
    public ?object $webProfile;

    public array $mainMenu;

    public ?string $title;

    public ?string $description;

    public ?string $favicon;

    public string $bodyClass;

    public $authUser;

    public array $customerLinks;

    // New SEO properties
    public ?string $keywords;

    public string $ogType;

    public ?string $ogImage;

    public ?string $ogImageAlt;

    public string $siteName;

    public string $locale;

    public ?string $canonicalUrl;

    public string $robots;

    public function __construct(
        ?string $title = null,
        ?string $description = null,
        ?string $favicon = null,
        ?string $bodyClass = null,
        ?string $keywords = null,
        ?string $ogType = null,
        ?string $ogImage = null,
        ?string $ogImageAlt = null,
        ?string $canonicalUrl = null,
        ?string $robots = null
    ) {
        $this->webProfile = $this->resolveWebProfile();

        // Basic properties
        $this->title = $title;
        $this->description = $description;
        $this->favicon = $favicon;
        $this->bodyClass = $bodyClass ?? 'bg-gray-50';

        // SEO properties with smart defaults
        $this->keywords = $keywords;
        $this->ogType = $ogType ?? 'website';
        $this->ogImage = $ogImage;
        $this->ogImageAlt = $ogImageAlt;
        $this->canonicalUrl = $canonicalUrl;
        $this->robots = $robots ?? 'index, follow';

        // Derived properties
        $this->siteName = data_get($this->webProfile, 'profile_name', config('app.name', 'King Express Bus'));
        $this->locale = str_replace('-', '_', app()->getLocale()).'_VN';

        $this->mainMenu = $this->resolveMainMenu();
        $this->authUser = auth()->user();
        $this->customerLinks = $this->resolveCustomerLinks();
    }

    protected function resolveWebProfile(): ?object
    {
        if (! Schema::hasTable('web_profiles')) {
            return null;
        }

        return DB::table('web_profiles')->where('is_default', true)->first();
    }

    protected function resolveCustomerLinks(): array
    {
        if ($this->authUser && ($this->authUser->role ?? null) === 'customer') {
            return [
                [
                    'label' => __('client.layout.profile'),
                    'url' => route('client.profile.index'),
                    'icon' => 'fa-solid fa-user',
                ],
                [
                    'label' => __('client.layout.my_bookings'),
                    'url' => route('client.profile.index').'#history',
                    'icon' => 'fa-solid fa-ticket',
                ],
            ];
        }

        return [];
    }

    protected function resolveMainMenu(): array
    {
        // Static menu items - prefix
        $staticPrefix = [
            (object) [
                'id' => 'static_home',
                'name' => __('client.menu.home'),
                'url' => url('/'),
                'parent_id' => null,
                'children' => [],
            ],
            (object) [
                'id' => 'static_about',
                'name' => __('client.menu.about'),
                'url' => url('/gioi-thieu'),
                'parent_id' => null,
                'children' => [],
            ],
            (object) [
                'id' => 'static_routes',
                'name' => __('client.menu.routes'),
                'url' => url('/tuyen-duong'),
                'parent_id' => null,
                'children' => [],
            ],
        ];

        // Static menu item - suffix
        $staticSuffix = [
            (object) [
                'id' => 'static_contact',
                'name' => __('client.menu.contact'),
                'url' => url('/lien-he'),
                'parent_id' => null,
                'children' => [],
            ],
        ];

        // Dynamic menu items from database
        $dynamicItems = [];
        if (Schema::hasTable('menus')) {
            $menus = DB::table('menus')
                ->orderByDesc('priority')
                ->get();

            if ($menus->isNotEmpty()) {
                $dynamicItems = $this->buildMenuTree($menus, Menu::ROOT_PARENT_ID);
            }
        }

        // Combine: Static prefix + Dynamic items + Static suffix
        return array_merge($staticPrefix, $dynamicItems, $staticSuffix);
    }

    /**
     * Resolve the URL for a menu item based on its type
     */
    protected function resolveMenuUrl(object $menu): string
    {
        switch ($menu->type) {
            case 'route':
                // Get route slug from routes table using related_id
                if ($menu->related_id && Schema::hasTable('routes')) {
                    $route = DB::table('routes')->where('id', $menu->related_id)->first();
                    if ($route) {
                        return url('/tuyen-duong/'.$route->slug);
                    }
                }

                return $menu->url ?? '#';

            case 'page':
                // Get page slug from pages table using related_id
                if ($menu->related_id && Schema::hasTable('pages')) {
                    $page = DB::table('pages')->where('id', $menu->related_id)->first();
                    if ($page) {
                        return url('/trang/'.$page->slug);
                    }
                }

                return $menu->url ?? '#';

            case 'custom_link':
            default:
                return $menu->url ?? '#';
        }
    }

    protected function buildMenuTree(Collection $menus, int $parentId = Menu::ROOT_PARENT_ID): array
    {
        $branch = [];
        $items = $menus->where('parent_id', $parentId)->sortByDesc('priority');

        foreach ($items as $item) {
            $item->name = __($item->name);
            $item->url = $this->resolveMenuUrl($item);
            $item->children = [];

            $children = $this->buildMenuTree($menus, $item->id);
            if (! empty($children)) {
                $item->children = $children;
            }
            $branch[] = $item;
        }

        return $branch;
    }

    public function render(): View|Closure|string
    {
        return view('components.client.layout');
    }
}
