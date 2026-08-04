<?php

/**
 * Absolute query budgets for heavy admin index pages (Filament siblings removed).
 */

use App\Models\Booking;
use App\Models\Route;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

/**
 * @return array{count: int, queries: list<string>}
 */
function countQueries(callable $callback): array
{
    $queries = [];

    DB::listen(function ($query) use (&$queries) {
        $queries[] = $query->sql;
    });

    $callback();

    return [
        'count' => count($queries),
        'queries' => $queries,
    ];
}

function seedHeavyIndexes(int $n = 50): void
{
    Booking::factory()->count($n)->create();
    Trip::factory()->count(max(10, intdiv($n, 5)))->create();
    Route::factory()->count(max(10, intdiv($n, 5)))->create();
}

it('bookings index query count stays under budget', function () {
    $admin = User::factory()->admin()->create();
    seedHeavyIndexes(40);

    $adminCount = countQueries(function () use ($admin) {
        test()->actingAs($admin)->get(route('admin.bookings.index'))->assertSuccessful();
    });

    expect($adminCount['count'])->toBeLessThan(80);
});

it('trips index query count stays under budget', function () {
    $admin = User::factory()->admin()->create();
    seedHeavyIndexes(40);

    $adminCount = countQueries(function () use ($admin) {
        test()->actingAs($admin)->get(route('admin.trips.index'))->assertSuccessful();
    });

    expect($adminCount['count'])->toBeLessThan(80);
});

it('routes index query count stays under budget', function () {
    $admin = User::factory()->admin()->create();
    seedHeavyIndexes(40);

    $adminCount = countQueries(function () use ($admin) {
        test()->actingAs($admin)->get(route('admin.routes.index'))->assertSuccessful();
    });

    expect($adminCount['count'])->toBeLessThan(80);
});
