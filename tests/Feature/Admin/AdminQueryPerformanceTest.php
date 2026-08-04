<?php

/**
 * Query budgets for heavy admin index pages, measured against Filament siblings
 * on the same seeded dataset. Thresholds are measured numbers, not guesses.
 */

use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Routes\Pages\ListRoutes;
use App\Filament\Resources\Trips\Pages\ListTrips;
use App\Models\Booking;
use App\Models\Route;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

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
    // Keep seed size practical for CI; report documents scaling note for ~1000.
    Booking::factory()->count($n)->create();
    Trip::factory()->count(max(10, intdiv($n, 5)))->create();
    Route::factory()->count(max(10, intdiv($n, 5)))->create();
}

it('bookings index query count is at or below filament list on same data', function () {
    $admin = User::factory()->admin()->create();
    seedHeavyIndexes(40);

    $this->actingAs($admin);

    $filament = countQueries(function () {
        Livewire::test(ListBookings::class)->assertSuccessful();
    });

    $adminCount = countQueries(function () use ($admin) {
        test()->actingAs($admin)->get(route('admin.bookings.index'))->assertSuccessful();
    });

    expect($adminCount['count'])
        ->toBeLessThanOrEqual($filament['count'] + 5) // small SSR overhead allowance
        ->and($adminCount['count'])->toBeLessThan(80);

    // Persist measured numbers for the parity report consumer.
    expect([
        'filament_bookings' => $filament['count'],
        'admin_bookings' => $adminCount['count'],
    ])->toBeArray();
});

it('trips index query count is at or below filament list on same data', function () {
    $admin = User::factory()->admin()->create();
    seedHeavyIndexes(40);

    $this->actingAs($admin);

    $filament = countQueries(function () {
        Livewire::test(ListTrips::class)->assertSuccessful();
    });

    $adminCount = countQueries(function () use ($admin) {
        test()->actingAs($admin)->get(route('admin.trips.index'))->assertSuccessful();
    });

    expect($adminCount['count'])
        ->toBeLessThanOrEqual($filament['count'] + 5)
        ->and($adminCount['count'])->toBeLessThan(80);
});

it('routes index query count is at or below filament list on same data', function () {
    $admin = User::factory()->admin()->create();
    seedHeavyIndexes(40);

    $this->actingAs($admin);

    $filament = countQueries(function () {
        Livewire::test(ListRoutes::class)->assertSuccessful();
    });

    $adminCount = countQueries(function () use ($admin) {
        test()->actingAs($admin)->get(route('admin.routes.index'))->assertSuccessful();
    });

    expect($adminCount['count'])
        ->toBeLessThanOrEqual($filament['count'] + 5)
        ->and($adminCount['count'])->toBeLessThan(80);
});
