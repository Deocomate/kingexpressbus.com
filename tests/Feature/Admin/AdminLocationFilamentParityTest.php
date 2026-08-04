<?php

use App\Filament\Resources\Districts\Pages\ManageDistricts;
use App\Filament\Resources\DistrictTypes\Pages\ManageDistrictTypes;
use App\Filament\Resources\Provinces\Pages\ManageProvinces;
use App\Filament\Resources\Stops\Pages\ManageStops;
use App\Models\District;
use App\Models\DistrictType;
use App\Models\Province;
use App\Models\Stop;
use App\Models\User;
use App\Support\Admin\LocationFilamentParity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function parity_admin(): User
{
    return User::factory()->admin()->create();
}

/**
 * @return array<int, int>
 */
function admin_location_ids(User $admin, string $section): array
{
    $response = test()
        ->actingAs($admin)
        ->get(route('admin.locations.index', [
            'section'  => $section,
            'per_page' => 100,
        ]))
        ->assertOk();

    /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
    $paginator = $response->viewData('paginator');

    return $paginator->pluck('id')->values()->all();
}

/**
 * @param class-string $pageClass
 * @return array<int, int>
 */
function filament_livewire_ids(string $pageClass): array
{
    $component = Livewire::test($pageClass);

    return $component->instance()
        ->getTableRecords()
        ->pluck('id')
        ->values()
        ->all();
}

it('provinces: admin list matches Filament order and core fields', function () {
    $admin = parity_admin();

    Province::factory()->create(['name' => 'P-low', 'slug' => 'p-low', 'priority' => 1]);
    Province::factory()->create(['name' => 'P-high', 'slug' => 'p-high', 'priority' => 50]);
    Province::factory()->create(['name' => 'P-mid', 'slug' => 'p-mid', 'priority' => 20]);

    $canonical = LocationFilamentParity::filamentProvinces();
    $adminIds = admin_location_ids($admin, 'provinces');
    $filamentIds = filament_livewire_ids(ManageProvinces::class);

    expect($adminIds)->toBe($canonical->pluck('id')->all())
        ->and($filamentIds)->toBe($canonical->pluck('id')->all());

    $adminPaginator = test()->actingAs($admin)->get(route('admin.locations.index', [
        'section' => 'provinces',
        'per_page' => 100,
    ]))->viewData('paginator');

    foreach ($canonical as $index => $filamentRow) {
        $adminRow = $adminPaginator->getCollection()->firstWhere('id', $filamentRow->id);
        expect($adminRow)->not->toBeNull()
            ->and($adminRow->name)->toBe($filamentRow->name)
            ->and($adminRow->slug)->toBe($filamentRow->slug)
            ->and($adminRow->priority)->toBe($filamentRow->priority)
            ->and($adminRow->title)->toBe($filamentRow->title);
    }
});

it('district types: admin list matches Filament order and fields', function () {
    $admin = parity_admin();

    DistrictType::factory()->create(['name' => 'DT-a', 'priority' => 2]);
    DistrictType::factory()->create(['name' => 'DT-b', 'priority' => 8]);
    DistrictType::factory()->create(['name' => 'DT-c', 'priority' => 5]);

    $canonical = LocationFilamentParity::filamentDistrictTypes();
    $adminIds = admin_location_ids($admin, 'district-types');
    $filamentIds = filament_livewire_ids(ManageDistrictTypes::class);

    expect($adminIds)->toBe($canonical->pluck('id')->all())
        ->and($filamentIds)->toBe($canonical->pluck('id')->all());

    $adminPaginator = test()->actingAs($admin)->get(route('admin.locations.index', [
        'section' => 'district-types',
        'per_page' => 100,
    ]))->viewData('paginator');

    foreach ($canonical as $filamentRow) {
        $adminRow = $adminPaginator->getCollection()->firstWhere('id', $filamentRow->id);
        expect($adminRow->name)->toBe($filamentRow->name)
            ->and($adminRow->priority)->toBe($filamentRow->priority);
    }
});

it('districts: admin list matches Filament order and core fields', function () {
    $admin = parity_admin();

    $province = Province::factory()->create();
    $typeA = DistrictType::factory()->create(['name' => 'Type A']);
    $typeB = DistrictType::factory()->create(['name' => 'Type B']);

    District::factory()->create([
        'province_id' => $province->id,
        'district_type_id' => $typeA->id,
        'name' => 'D-low',
        'slug' => 'd-low',
        'priority' => 3,
    ]);
    District::factory()->create([
        'province_id' => $province->id,
        'district_type_id' => $typeB->id,
        'name' => 'D-high',
        'slug' => 'd-high',
        'priority' => 30,
    ]);

    $canonical = LocationFilamentParity::filamentDistricts();
    $adminIds = admin_location_ids($admin, 'districts');
    $filamentIds = filament_livewire_ids(ManageDistricts::class);

    expect($adminIds)->toBe($canonical->pluck('id')->all())
        ->and($filamentIds)->toBe($canonical->pluck('id')->all());

    $adminPaginator = test()->actingAs($admin)->get(route('admin.locations.index', [
        'section' => 'districts',
        'per_page' => 100,
    ]))->viewData('paginator');

    foreach ($canonical as $filamentRow) {
        $adminRow = $adminPaginator->getCollection()->firstWhere('id', $filamentRow->id);
        expect($adminRow->name)->toBe($filamentRow->name)
            ->and($adminRow->slug)->toBe($filamentRow->slug)
            ->and($adminRow->priority)->toBe($filamentRow->priority)
            ->and($adminRow->province_id)->toBe($filamentRow->province_id)
            ->and($adminRow->district_type_id)->toBe($filamentRow->district_type_id);
    }
});

it('stops: admin list matches Filament order and core fields', function () {
    $admin = parity_admin();

    $districtA = District::factory()->create(['name' => 'Dist A']);
    $districtB = District::factory()->create(['name' => 'Dist B']);

    Stop::factory()->create([
        'district_id' => $districtA->id,
        'name' => 'Stop-low',
        'address' => 'Addr low',
        'priority' => 4,
    ]);
    Stop::factory()->create([
        'district_id' => $districtB->id,
        'name' => 'Stop-high',
        'address' => 'Addr high',
        'priority' => 40,
    ]);

    $canonical = LocationFilamentParity::filamentStops();
    $adminIds = admin_location_ids($admin, 'stops');
    $filamentIds = filament_livewire_ids(ManageStops::class);

    expect($adminIds)->toBe($canonical->pluck('id')->all())
        ->and($filamentIds)->toBe($canonical->pluck('id')->all());

    $adminPaginator = test()->actingAs($admin)->get(route('admin.locations.index', [
        'section' => 'stops',
        'per_page' => 100,
    ]))->viewData('paginator');

    foreach ($canonical as $filamentRow) {
        $adminRow = $adminPaginator->getCollection()->firstWhere('id', $filamentRow->id);
        expect($adminRow->name)->toBe($filamentRow->name)
            ->and($adminRow->address)->toBe($filamentRow->address)
            ->and($adminRow->priority)->toBe($filamentRow->priority)
            ->and($adminRow->district_id)->toBe($filamentRow->district_id);
    }
});
