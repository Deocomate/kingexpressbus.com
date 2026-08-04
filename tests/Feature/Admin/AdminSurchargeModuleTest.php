<?php

use App\Models\HolidaySurcharge;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// ─── helpers ────────────────────────────────────────────────────────────────

function adminUserForSurcharges(): User
{
    return User::factory()->admin()->create();
}

// ─── index ──────────────────────────────────────────────────────────────────

it('surcharge index page returns 200', function () {
    $this->actingAs(adminUserForSurcharges())
        ->get(route('admin.surcharges.index'))
        ->assertOk()
        ->assertViewIs('admin.surcharges.index');
});

// ─── create / store ─────────────────────────────────────────────────────────

it('create page returns 200', function () {
    $this->actingAs(adminUserForSurcharges())
        ->get(route('admin.surcharges.create'))
        ->assertOk();
});

it('store creates surcharge and redirects', function () {
    $this->actingAs(adminUserForSurcharges())
        ->post(route('admin.surcharges.store'), [
            'name'                    => 'Tết Nguyên Đán 2027',
            'reason'                  => 'Cao điểm lễ tết',
            'start_date'              => '25/01/2027',
            'end_date'                => '05/02/2027',
            'global_surcharge_amount' => '50000',
            'is_active'               => '1',
            'priority'                => '10',
        ])
        ->assertRedirect(route('admin.surcharges.index'))
        ->assertSessionHas('success');

    expect(DB::table('holiday_surcharges')->count())->toBe(1);
    expect(DB::table('holiday_surcharges')->first()->name)->toBe('Tết Nguyên Đán 2027');
});

it('store validates required fields', function () {
    $this->actingAs(adminUserForSurcharges())
        ->post(route('admin.surcharges.store'), [])
        ->assertSessionHasErrors(['name', 'start_date', 'end_date', 'global_surcharge_amount', 'priority']);
});

it('store rejects end_date before start_date', function () {
    $this->actingAs(adminUserForSurcharges())
        ->post(route('admin.surcharges.store'), [
            'name'                    => 'Test',
            'start_date'              => '10/02/2027',
            'end_date'                => '01/02/2027',
            'global_surcharge_amount' => '10000',
            'priority'                => '0',
        ])
        ->assertSessionHasErrors('end_date');
});

// ─── route adjustments ───────────────────────────────────────────────────────

it('store syncs route adjustments correctly', function () {
    $route1 = Route::factory()->create();
    $route2 = Route::factory()->create();

    $this->actingAs(adminUserForSurcharges())
        ->post(route('admin.surcharges.store'), [
            'name'                    => 'Test Surcharge',
            'start_date'              => '01/02/2027',
            'end_date'                => '07/02/2027',
            'global_surcharge_amount' => '30000',
            'is_active'               => '1',
            'priority'                => '5',
            'route_adjustments'       => [
                ['route_id' => $route1->id, 'route_surcharge_amount' => '20000'],
                ['route_id' => $route2->id, 'route_surcharge_amount' => '15000'],
            ],
        ])
        ->assertSessionHas('success');

    $surchargeId = DB::table('holiday_surcharges')->first()->id;
    $adjustments = DB::table('holiday_surcharge_routes')
        ->where('holiday_surcharge_id', $surchargeId)
        ->orderBy('route_id')
        ->get();

    expect($adjustments)->toHaveCount(2);
});

// ─── edit / update ───────────────────────────────────────────────────────────

it('edit page returns 200 for existing surcharge', function () {
    $surcharge = HolidaySurcharge::factory()->create();

    $this->actingAs(adminUserForSurcharges())
        ->get(route('admin.surcharges.edit', $surcharge->id))
        ->assertOk();
});

it('update changes surcharge data and syncs adjustments', function () {
    $surcharge = HolidaySurcharge::factory()->create([
        'name'     => 'Old Name',
        'is_active' => true,
    ]);
    $route = Route::factory()->create();

    $this->actingAs(adminUserForSurcharges())
        ->put(route('admin.surcharges.update', $surcharge->id), [
            'name'                    => 'New Name',
            'start_date'              => '01/02/2027',
            'end_date'                => '07/02/2027',
            'global_surcharge_amount' => '60000',
            'is_active'               => '1',
            'priority'                => '20',
            'route_adjustments'       => [
                ['route_id' => $route->id, 'route_surcharge_amount' => '25000'],
            ],
        ])
        ->assertRedirect(route('admin.surcharges.index'))
        ->assertSessionHas('success');

    expect($surcharge->fresh()->name)->toBe('New Name');
    expect($surcharge->fresh()->global_surcharge_amount)->toBe(60000);
    expect(DB::table('holiday_surcharge_routes')
        ->where('holiday_surcharge_id', $surcharge->id)
        ->count())->toBe(1);
});

// ─── delete ──────────────────────────────────────────────────────────────────

it('delete removes surcharge and its route adjustments', function () {
    $surcharge = HolidaySurcharge::factory()->create();
    $route = Route::factory()->create();
    DB::table('holiday_surcharge_routes')->insert([
        'holiday_surcharge_id'   => $surcharge->id,
        'route_id'               => $route->id,
        'route_surcharge_amount' => 10000,
        'created_at'             => now(),
        'updated_at'             => now(),
    ]);

    $this->actingAs(adminUserForSurcharges())
        ->delete(route('admin.surcharges.destroy', $surcharge->id))
        ->assertRedirect(route('admin.surcharges.index'))
        ->assertSessionHas('success');

    expect(DB::table('holiday_surcharges')->count())->toBe(0);
    expect(DB::table('holiday_surcharge_routes')->count())->toBe(0);
});

// ─── search ──────────────────────────────────────────────────────────────────

it('search filters surcharges by name', function () {
    HolidaySurcharge::factory()->create(['name' => 'Tết Nguyên Đán']);
    HolidaySurcharge::factory()->create(['name' => 'Giỗ Tổ Hùng Vương']);

    $response = $this->actingAs(adminUserForSurcharges())
        ->get(route('admin.surcharges.index', ['search' => 'Tết']));

    $response->assertOk();
    $paginator = $response->viewData('paginator');
    expect($paginator->total())->toBe(1);
});

// ─── stacking behavior preserved ─────────────────────────────────────────────

it('stacking behavior: two overlapping rules sum additively (HolidaySurchargeService unchanged)', function () {
    $route = Route::factory()->create();
    $now   = now();

    DB::table('holiday_surcharges')->insert([
        [
            'name'                    => 'Rule A',
            'reason'                  => 'A',
            'start_date'              => '2027-02-01',
            'end_date'                => '2027-02-07',
            'global_surcharge_amount' => 30000,
            'is_active'               => true,
            'priority'                => 10,
            'created_at'              => $now,
            'updated_at'              => $now,
        ],
        [
            'name'                    => 'Rule B',
            'reason'                  => 'B',
            'start_date'              => '2027-02-03',
            'end_date'                => '2027-02-05',
            'global_surcharge_amount' => 20000,
            'is_active'               => true,
            'priority'                => 8,
            'created_at'              => $now,
            'updated_at'              => $now,
        ],
    ]);

    $service = app(\App\Services\HolidaySurchargeService::class);

    // On 2027-02-03 both rules apply → 50 000 total
    // Route has no specific adjustment
    $breakdown = $service->calculateBreakdownByRouteAndBasePrice($route->id, 300000, '2027-02-03');

    expect($breakdown['global_surcharge_unit'])->toBe(50000)
        ->and($breakdown['has_surcharge'])->toBeTrue();
});
