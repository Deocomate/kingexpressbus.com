<?php

use App\Support\Admin\TableColumn;
use App\Support\Admin\TableConfig;
use App\Support\Admin\TableQuery;
use App\Support\Admin\TableTab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// We test TableQuery using the 'users' table which is always present.

function makeConfig(): TableConfig
{
    return TableConfig::make()
        ->columns([
            TableColumn::make('id', 'ID')->sortable()->hideable(false),
            TableColumn::make('name', 'Name')->sortable(),
        ])
        ->tabs([
            TableTab::make('all', 'Tất cả')
                ->query(fn ($q) => $q)
                ->badge(fn () => DB::table('users')->count()),
            TableTab::make('active', 'Hoạt động')
                ->query(fn ($q) => $q->whereNotNull('email_verified_at'))
                ->badge(fn () => DB::table('users')->whereNotNull('email_verified_at')->count()),
        ])
        ->searchColumns(['name', 'email'])
        ->allowSort('id')
        ->allowSort('name')
        ->perPageOptions([10, 25, 50], 10);
}

function makeRequest(array $params = []): Request
{
    return Request::create('/test?' . http_build_query($params));
}

beforeEach(function () {
    DB::table('users')->insert([
        ['name' => 'Alice', 'email' => 'alice@test.com', 'password' => 'x', 'email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Bob', 'email' => 'bob@test.com', 'password' => 'x', 'email_verified_at' => null, 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'Carol', 'email' => 'carol@test.com', 'password' => 'x', 'email_verified_at' => now(), 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('returns all records on default tab', function () {
    $config = makeConfig();
    $result = TableQuery::make(DB::table('users'), $config)
        ->process(makeRequest());
    expect($result->paginator()->total())->toBe(3);
    expect($result->activeTab())->toBe('all');
});

it('tab filter restricts to verified users', function () {
    $config = makeConfig();
    $result = TableQuery::make(DB::table('users'), $config)
        ->process(makeRequest(['tab' => 'active']));
    expect($result->paginator()->total())->toBe(2);
    expect($result->activeTab())->toBe('active');
});

it('tab badge count matches filtered query', function () {
    $config = makeConfig();
    $tq = TableQuery::make(DB::table('users'), $config)->process(makeRequest());
    $badges = $tq->tabBadges();
    expect($badges['all'])->toBe(3);
    expect($badges['active'])->toBe(2);
});

it('search filters by column', function () {
    $config = makeConfig();
    $result = TableQuery::make(DB::table('users'), $config)
        ->process(makeRequest(['search' => 'Alice']));
    expect($result->paginator()->total())->toBe(1);
    expect($result->activeSearch())->toBe('Alice');
});

it('unknown tab falls back to first tab', function () {
    $config = makeConfig();
    $result = TableQuery::make(DB::table('users'), $config)
        ->process(makeRequest(['tab' => 'nonexistent']));
    expect($result->activeTab())->toBe('all');
});

it('sort whitelist rejects unknown column', function () {
    $config = makeConfig();
    $result = TableQuery::make(DB::table('users'), $config)
        ->process(makeRequest(['sort' => 'email', 'direction' => 'asc']));
    // email is not in whitelist — sort key should be empty
    expect($result->activeSortKey())->toBe('');
});

it('sort on whitelisted column works', function () {
    $config = makeConfig();
    $result = TableQuery::make(DB::table('users'), $config)
        ->process(makeRequest(['sort' => 'name', 'direction' => 'asc']));
    expect($result->activeSortKey())->toBe('name');
    expect($result->activeSortDir())->toBe('asc');
});

it('sort closure is applied when registered', function () {
    $config = TableConfig::make()
        ->allowSort('name_length', fn ($q, $dir) => $q->orderByRaw("LENGTH(name) {$dir}"))
        ->perPageOptions([10], 10);

    $result = TableQuery::make(DB::table('users'), $config)
        ->process(makeRequest(['sort' => 'name_length', 'direction' => 'desc']));
    expect($result->activeSortKey())->toBe('name_length');
});

it('per_page whitelist enforces allowed values', function () {
    $config = makeConfig();
    $result = TableQuery::make(DB::table('users'), $config)
        ->process(makeRequest(['per_page' => 99]));
    expect($result->perPage())->toBe(10); // fallback to default
});

it('per_page allowed value is respected', function () {
    $config = makeConfig();
    $result = TableQuery::make(DB::table('users'), $config)
        ->process(makeRequest(['per_page' => 25]));
    expect($result->perPage())->toBe(25);
});
