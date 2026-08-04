<?php

namespace App\Support\Admin;

use Closure;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Reusable table engine: tabs, search, sort whitelist, filters, pagination.
 * Controllers call TableQuery::make($baseQuery, $config)->process($request)->paginator().
 */
class TableQuery
{
    private EloquentBuilder|QueryBuilder $query;
    private TableConfig $config;

    /** Active tab key after processing. */
    private string $activeTab = '';

    /** @var array<string, mixed> Active filter values. */
    private array $activeFilters = [];

    private string $activeSearch = '';
    private string $activeSortKey = '';
    private string $activeSortDir = 'asc';
    private int $perPage = 15;

    private LengthAwarePaginator $paginator;

    private function __construct(EloquentBuilder|QueryBuilder $query, TableConfig $config)
    {
        $this->query = clone $query;
        $this->config = $config;
    }

    public static function make(EloquentBuilder|QueryBuilder $query, TableConfig $config): self
    {
        return new self($query, $config);
    }

    /** Apply all request parameters and run the query. */
    public function process(Request $request): self
    {
        $this->resolvePerPage($request);
        $this->applyTab($request);
        $this->applySearch($request);
        $this->applyFilters($request);
        $this->applySort($request);

        $this->paginator = $this->query->paginate($this->perPage)
            ->withQueryString();

        return $this;
    }

    public function paginator(): LengthAwarePaginator
    {
        return $this->paginator;
    }

    public function activeTab(): string
    {
        return $this->activeTab;
    }

    public function activeSearch(): string
    {
        return $this->activeSearch;
    }

    public function activeSortKey(): string
    {
        return $this->activeSortKey;
    }

    public function activeSortDir(): string
    {
        return $this->activeSortDir;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    /** Resolve tab badge counts without executing main query again. */
    public function tabBadges(): array
    {
        $badges = [];
        foreach ($this->config->getTabs() as $tab) {
            $badges[$tab->key] = $tab->getBadgeCount();
        }
        return $badges;
    }

    // -------------------------------------------------------------------------

    private function resolvePerPage(Request $request): void
    {
        $allowed = $this->config->getPerPageOptions();
        $requested = (int) $request->input('per_page', $this->config->getDefaultPerPage());
        $this->perPage = in_array($requested, $allowed) ? $requested : $this->config->getDefaultPerPage();
    }

    private function applyTab(Request $request): void
    {
        $key = $request->input('tab', '');
        $tabs = $this->config->getTabs();

        if (empty($tabs)) {
            $this->activeTab = '';
            return;
        }

        $tab = collect($tabs)->firstWhere('key', $key) ?? $tabs[0];
        $this->activeTab = $tab->key;
        $this->query = $tab->applyTo($this->query);
    }

    private function applySearch(Request $request): void
    {
        $search = trim((string) $request->input('search', ''));
        $this->activeSearch = $search;
        $cols = $this->config->getSearchColumns();

        if ($search === '' || empty($cols)) {
            return;
        }

        $this->query->where(function (EloquentBuilder|QueryBuilder $q) use ($search, $cols) {
            foreach ($cols as $col) {
                if ($col instanceof Closure) {
                    $q->orWhere(fn ($sub) => $col($sub, $search));
                } else {
                    $q->orWhere($col, 'like', '%' . $search . '%');
                }
            }
        });
    }

    private function applyFilters(Request $request): void
    {
        $filters = $request->input('filter', []);
        if (! is_array($filters)) {
            return;
        }
        $this->activeFilters = $filters;
        // Concrete filter logic is applied by the controller via callbacks registered on config.
        // This method is a hook; the config-based filters extension can be added in a subclass.
    }

    private function applySort(Request $request): void
    {
        $key = (string) $request->input('sort', '');
        $dir = strtolower((string) $request->input('direction', 'asc'));
        $dir = in_array($dir, ['asc', 'desc']) ? $dir : 'asc';

        $whitelist = $this->config->getSortWhitelist();

        if ($key === '' || ! array_key_exists($key, $whitelist)) {
            return;
        }

        $this->activeSortKey = $key;
        $this->activeSortDir = $dir;

        $resolver = $whitelist[$key];

        if ($resolver instanceof Closure) {
            $this->query = $resolver($this->query, $dir);
        } else {
            $this->query->orderBy($resolver, $dir);
        }
    }
}
