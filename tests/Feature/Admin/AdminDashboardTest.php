<?php

use App\Models\Booking;
use App\Models\User;
use App\Services\BookingService;
use App\Support\ClientCache;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::forget(ClientCache::ADMIN_DASHBOARD_STATS);
    $this->admin = User::factory()->admin()->create();
});

// ─── Dashboard access ─────────────────────────────────────────────────────────

it('admin can access dashboard', function () {
    $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk();
});

it('guest is redirected from dashboard', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
});

// ─── Stat values match BookingService ────────────────────────────────────────

it('dashboard totalToday stat matches BookingService on same dataset', function () {
    Booking::factory()->count(3)->create(['status' => 'pending']);
    Booking::factory()->count(2)->create([
        'status'     => 'confirmed',
        'created_at' => now()->subDay(),
    ]);

    $serviceStats = app(BookingService::class)->getAdminBookingStats();

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $stats    = $response->viewData('stats');

    expect($stats->totalToday)->toBe($serviceStats['totalToday']);
});

it('dashboard pendingTotal stat matches BookingService on same dataset', function () {
    Booking::factory()->count(4)->create(['status' => 'pending']);
    Booking::factory()->count(2)->create(['status' => 'confirmed']);

    $serviceStats = app(BookingService::class)->getAdminBookingStats();

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $stats    = $response->viewData('stats');

    expect($stats->pendingTotal)->toBe($serviceStats['pendingTotal']);
});

it('dashboard revenueToday stat matches BookingService on same dataset', function () {
    Booking::factory()->count(2)->create([
        'status'      => 'confirmed',
        'total_price' => 200000,
        'created_at'  => now(),
    ]);

    $serviceStats = app(BookingService::class)->getAdminBookingStats();

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $stats    = $response->viewData('stats');

    expect($stats->revenueToday)->toBe($serviceStats['revenueToday']);
});

it('dashboard totalRevenue includes all confirmed and completed bookings', function () {
    Booking::factory()->create(['status' => 'confirmed', 'total_price' => 300000]);
    Booking::factory()->create(['status' => 'completed', 'total_price' => 150000]);
    Booking::factory()->create(['status' => 'pending',   'total_price' => 100000]);

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $stats    = $response->viewData('stats');

    expect($stats->totalRevenue)->toBe(450000);
});

// ─── Cache 60s ────────────────────────────────────────────────────────────────

it('dashboard stats are cached under the ADMIN_DASHBOARD_STATS key', function () {
    Booking::factory()->count(2)->create(['status' => 'pending']);

    // First request populates cache
    $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk();

    expect(Cache::has(ClientCache::ADMIN_DASHBOARD_STATS))->toBeTrue();
});

it('dashboard stats return cached value when cache is warm', function () {
    Cache::put(ClientCache::ADMIN_DASHBOARD_STATS, [
        'todayStats'   => ['totalToday' => 99, 'pendingTotal' => 88, 'revenueToday' => 77777],
        'totalRevenue' => 999999,
    ], 60);

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $stats    = $response->viewData('stats');

    expect($stats->totalToday)->toBe(99)
        ->and($stats->pendingTotal)->toBe(88)
        ->and($stats->revenueToday)->toBe(77777)
        ->and($stats->totalRevenue)->toBe(999999);
});

// ─── Chart data present ───────────────────────────────────────────────────────

it('dashboard passes statusChartData with labels and datasets', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $data     = $response->viewData('statusChartData')->data;

    expect($data)->toHaveKeys(['datasets', 'labels'])
        ->and($data['labels'])->toHaveCount(4);
});

it('dashboard passes revenueChartData with 12 months of labels', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $data     = $response->viewData('revenueChartData')->data;

    expect($data)->toHaveKeys(['datasets', 'labels'])
        ->and($data['labels'])->toHaveCount(12);
});

// ─── Latest bookings widget ───────────────────────────────────────────────────

it('dashboard latestBookings contains only upcoming pending/confirmed', function () {
    $future   = now()->addDay()->toDateString();
    $upcoming = Booking::factory()->create(['status' => 'pending',   'booking_date' => $future]);
    $cancelled = Booking::factory()->create(['status' => 'cancelled', 'booking_date' => $future]);
    $past     = Booking::factory()->create(['status' => 'pending',   'booking_date' => now()->subDay()->toDateString()]);

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
    $bookings = $response->viewData('latestBookings')->bookings;

    $codes = $bookings->pluck('booking_code');
    expect($codes)->toContain($upcoming->booking_code)
        ->and($codes)->not->toContain($cancelled->booking_code)
        ->and($codes)->not->toContain($past->booking_code);
});
