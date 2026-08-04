<?php

use App\Models\Booking;
use App\Models\District;
use App\Models\DistrictType;
use App\Models\Province;
use App\Models\Stop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// ─── Helpers ────────────────────────────────────────────────────────────────

function loc_admin(): User
{
    return User::factory()->admin()->create();
}

function loc_province(array $attrs = []): Province
{
    return Province::factory()->create(array_merge(['priority' => 0], $attrs));
}

function loc_district_type(array $attrs = []): DistrictType
{
    return DistrictType::factory()->create(array_merge(['priority' => 0], $attrs));
}

function loc_district(array $attrs = []): District
{
    $prov = $attrs['province_id'] ?? loc_province()->id;
    $dt   = $attrs['district_type_id'] ?? loc_district_type()->id;

    return District::factory()->create(array_merge([
        'province_id'      => $prov,
        'district_type_id' => $dt,
        'priority'         => 0,
    ], $attrs));
}

function loc_stop(array $attrs = []): Stop
{
    $dist = $attrs['district_id'] ?? loc_district()->id;

    return Stop::factory()->create(array_merge([
        'district_id' => $dist,
        'priority'    => 0,
    ], $attrs));
}

function loc_booking_for_stop(int $stopId): Booking
{
    return Booking::factory()->create([
        'pickup_stop_id'  => $stopId,
        'dropoff_stop_id' => $stopId,
    ]);
}

// ─── Section routing ─────────────────────────────────────────────────────────

it('index returns 200 for provinces section', function () {
    $this->actingAs(loc_admin())
        ->get(route('admin.locations.index', ['section' => 'provinces']))
        ->assertOk()
        ->assertViewIs('admin.locations.index');
});

it('index returns 200 for district-types section', function () {
    $this->actingAs(loc_admin())
        ->get(route('admin.locations.index', ['section' => 'district-types']))
        ->assertOk();
});

it('index returns 200 for districts section', function () {
    $this->actingAs(loc_admin())
        ->get(route('admin.locations.index', ['section' => 'districts']))
        ->assertOk();
});

it('index returns 200 for stops section', function () {
    $this->actingAs(loc_admin())
        ->get(route('admin.locations.index', ['section' => 'stops']))
        ->assertOk();
});

it('invalid section defaults to provinces without error', function () {
    $this->actingAs(loc_admin())
        ->get(route('admin.locations.index', ['section' => 'hack-section']))
        ->assertOk()
        ->assertViewHas('section', 'provinces');
});

it('missing section defaults to provinces', function () {
    $this->actingAs(loc_admin())
        ->get(route('admin.locations.index'))
        ->assertOk()
        ->assertViewHas('section', 'provinces');
});

// ─── Guest / role protection ─────────────────────────────────────────────────

it('guest cannot access location index', function () {
    $this->get(route('admin.locations.index'))
        ->assertRedirect(route('admin.login'));
});

it('customer role cannot access location index', function () {
    $this->actingAs(User::factory()->customer()->create())
        ->get(route('admin.locations.index'))
        ->assertForbidden();
});

// ─── Province CRUD ────────────────────────────────────────────────────────────

it('can create a province', function () {
    $admin = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.provinces.store'), [
            'name'     => 'Hà Nội',
            'slug'     => 'ha-noi',
            'priority' => 10,
        ])
        ->assertRedirect(route('admin.locations.index', ['section' => 'provinces']));

    expect(Province::where('slug', 'ha-noi')->exists())->toBeTrue();
});

it('province store auto-normalises slug via Str::slug', function () {
    $admin = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.provinces.store'), [
            'name'     => 'Hà Nội',
            'slug'     => 'Ha Noi With Spaces',
            'priority' => 0,
        ])
        ->assertRedirect();

    expect(Province::where('slug', 'ha-noi-with-spaces')->exists())->toBeTrue();
});

it('province store rejects duplicate slug with 422', function () {
    loc_province(['slug' => 'ha-noi']);
    $admin = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.provinces.store'), [
            'name'     => 'Another Province',
            'slug'     => 'ha-noi',
            'priority' => 0,
        ])
        ->assertSessionHasErrors('slug');
});

it('can update a province and same slug does not cause unique error', function () {
    $province = loc_province(['name' => 'Hà Nội', 'slug' => 'ha-noi']);
    $admin = loc_admin();

    $this->actingAs($admin)
        ->put(route('admin.locations.provinces.update', $province), [
            'name'     => 'Hà Nội Updated',
            'slug'     => 'ha-noi',
            'priority' => 5,
        ])
        ->assertRedirect(route('admin.locations.index', ['section' => 'provinces']));

    expect($province->fresh()->name)->toBe('Hà Nội Updated');
});

it('can delete a province with no bookings', function () {
    $province = loc_province();
    $admin    = loc_admin();

    $this->actingAs($admin)
        ->delete(route('admin.locations.provinces.destroy', $province))
        ->assertRedirect(route('admin.locations.index', ['section' => 'provinces']));

    expect(Province::find($province->id))->toBeNull();
});

it('delete province blocked when bookings exist via cascade chain', function () {
    $province = loc_province();
    $district = loc_district(['province_id' => $province->id]);
    $stop     = loc_stop(['district_id' => $district->id]);
    loc_booking_for_stop($stop->id);

    $bookingsBefore = DB::table('bookings')->count();
    $admin          = loc_admin();

    $this->actingAs($admin)
        ->delete(route('admin.locations.provinces.destroy', $province))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(Province::find($province->id))->not->toBeNull();
    expect(DB::table('bookings')->count())->toBe($bookingsBefore);
});

it('bulk delete provinces blocked when any has cascade bookings', function () {
    $safeProvince     = loc_province();
    $blockedProvince  = loc_province();
    $district         = loc_district(['province_id' => $blockedProvince->id]);
    $stop             = loc_stop(['district_id' => $district->id]);
    loc_booking_for_stop($stop->id);

    $bookingsBefore = DB::table('bookings')->count();
    $admin          = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.provinces.bulk-destroy'), [
            'ids' => [$safeProvince->id, $blockedProvince->id],
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(DB::table('bookings')->count())->toBe($bookingsBefore);
});

// ─── DistrictType CRUD ────────────────────────────────────────────────────────

it('can create a district type', function () {
    $this->actingAs(loc_admin())
        ->post(route('admin.locations.district-types.store'), [
            'name'     => 'Quận',
            'priority' => 5,
        ])
        ->assertRedirect(route('admin.locations.index', ['section' => 'district-types']));

    expect(DistrictType::where('name', 'Quận')->exists())->toBeTrue();
});

it('can update a district type', function () {
    $dt    = loc_district_type(['name' => 'Quận', 'priority' => 5]);
    $admin = loc_admin();

    $this->actingAs($admin)
        ->put(route('admin.locations.district-types.update', $dt), [
            'name'     => 'Quận Updated',
            'priority' => 10,
        ])
        ->assertRedirect(route('admin.locations.index', ['section' => 'district-types']));

    expect($dt->fresh()->name)->toBe('Quận Updated');
});

it('can delete a district type', function () {
    $dt    = loc_district_type();
    $admin = loc_admin();

    $this->actingAs($admin)
        ->delete(route('admin.locations.district-types.destroy', $dt))
        ->assertRedirect(route('admin.locations.index', ['section' => 'district-types']));

    expect(DistrictType::find($dt->id))->toBeNull();
});

// ─── District CRUD ────────────────────────────────────────────────────────────

it('can create a district', function () {
    $province = loc_province();
    $dt       = loc_district_type();
    $admin    = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.districts.store'), [
            'province_id'      => $province->id,
            'district_type_id' => $dt->id,
            'name'             => 'Quận Hoàn Kiếm',
            'slug'             => 'quan-hoan-kiem',
            'priority'         => 5,
        ])
        ->assertRedirect(route('admin.locations.index', ['section' => 'districts']));

    expect(District::where('slug', 'quan-hoan-kiem')->exists())->toBeTrue();
});

it('district store rejects non-existent province_id with 422', function () {
    $dt    = loc_district_type();
    $admin = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.districts.store'), [
            'province_id'      => 99999,
            'district_type_id' => $dt->id,
            'name'             => 'Test',
            'slug'             => 'test',
            'priority'         => 0,
        ])
        ->assertSessionHasErrors('province_id');
});

it('district store rejects non-existent district_type_id with 422', function () {
    $province = loc_province();
    $admin    = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.districts.store'), [
            'province_id'      => $province->id,
            'district_type_id' => 99999,
            'name'             => 'Test',
            'slug'             => 'test',
            'priority'         => 0,
        ])
        ->assertSessionHasErrors('district_type_id');
});

it('district slug unique validation', function () {
    $province = loc_province();
    $dt       = loc_district_type();
    loc_district(['province_id' => $province->id, 'district_type_id' => $dt->id, 'slug' => 'dupe-slug']);

    $admin = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.districts.store'), [
            'province_id'      => $province->id,
            'district_type_id' => $dt->id,
            'name'             => 'Another',
            'slug'             => 'dupe-slug',
            'priority'         => 0,
        ])
        ->assertSessionHasErrors('slug');
});

it('district update allows same slug without unique error', function () {
    $province = loc_province();
    $dt       = loc_district_type();
    $district = loc_district(['province_id' => $province->id, 'district_type_id' => $dt->id, 'slug' => 'my-slug']);
    $admin    = loc_admin();

    $this->actingAs($admin)
        ->put(route('admin.locations.districts.update', $district), [
            'province_id'      => $province->id,
            'district_type_id' => $dt->id,
            'name'             => 'Updated Name',
            'slug'             => 'my-slug',
            'priority'         => 0,
        ])
        ->assertRedirect(route('admin.locations.index', ['section' => 'districts']));

    expect($district->fresh()->name)->toBe('Updated Name');
});

it('delete district blocked when bookings exist', function () {
    $province = loc_province();
    $dt       = loc_district_type();
    $district = loc_district(['province_id' => $province->id, 'district_type_id' => $dt->id]);
    $stop     = loc_stop(['district_id' => $district->id]);
    loc_booking_for_stop($stop->id);

    $bookingsBefore = DB::table('bookings')->count();
    $admin          = loc_admin();

    $this->actingAs($admin)
        ->delete(route('admin.locations.districts.destroy', $district))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(District::find($district->id))->not->toBeNull();
    expect(DB::table('bookings')->count())->toBe($bookingsBefore);
});

// ─── Stop CRUD ────────────────────────────────────────────────────────────────

it('can create a stop', function () {
    $district = loc_district();
    $admin    = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.stops.store'), [
            'district_id' => $district->id,
            'name'        => 'Bến xe Mỹ Đình',
            'address'     => '20 Phạm Hùng, Mỹ Đình',
            'priority'    => 5,
        ])
        ->assertRedirect(route('admin.locations.index', ['section' => 'stops']));

    expect(Stop::where('name', 'Bến xe Mỹ Đình')->exists())->toBeTrue();
});

it('can update a stop', function () {
    $stop  = loc_stop(['name' => 'Old Name', 'address' => 'Old Address']);
    $admin = loc_admin();

    $this->actingAs($admin)
        ->put(route('admin.locations.stops.update', $stop), [
            'district_id' => $stop->district_id,
            'name'        => 'New Name',
            'address'     => 'New Address',
            'priority'    => 0,
        ])
        ->assertRedirect(route('admin.locations.index', ['section' => 'stops']));

    expect($stop->fresh()->name)->toBe('New Name');
});

it('delete stop with no bookings succeeds', function () {
    $stop  = loc_stop();
    $admin = loc_admin();

    $this->actingAs($admin)
        ->delete(route('admin.locations.stops.destroy', $stop))
        ->assertRedirect(route('admin.locations.index', ['section' => 'stops']));

    expect(Stop::find($stop->id))->toBeNull();
});

it('delete stop with bookings is blocked and booking count unchanged', function () {
    $stop = loc_stop();
    loc_booking_for_stop($stop->id);

    $bookingsBefore = DB::table('bookings')->count();
    $admin          = loc_admin();

    $this->actingAs($admin)
        ->delete(route('admin.locations.stops.destroy', $stop))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(Stop::find($stop->id))->not->toBeNull();
    expect(DB::table('bookings')->count())->toBe($bookingsBefore);
});

it('bulk delete stops blocked when any stop has bookings and booking count unchanged', function () {
    $safeStop    = loc_stop();
    $blockedStop = loc_stop();
    loc_booking_for_stop($blockedStop->id);

    $bookingsBefore = DB::table('bookings')->count();
    $admin          = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.stops.bulk-destroy'), [
            'ids' => [$safeStop->id, $blockedStop->id],
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(DB::table('bookings')->count())->toBe($bookingsBefore);
    expect(Stop::find($safeStop->id))->not->toBeNull();
});

// ─── Reorder ─────────────────────────────────────────────────────────────────

it('reorder provinces assigns priority descending', function () {
    $p1    = loc_province(['name' => 'P1', 'priority' => 0]);
    $p2    = loc_province(['name' => 'P2', 'priority' => 0]);
    $p3    = loc_province(['name' => 'P3', 'priority' => 0]);
    $admin = loc_admin();

    // Send order: p3 first (highest priority), p1 second, p2 last
    $this->actingAs($admin)
        ->post(route('admin.locations.provinces.reorder'), [
            'ids' => [$p3->id, $p1->id, $p2->id],
        ])
        ->assertRedirect();

    expect($p3->fresh()->priority)->toBe(3);
    expect($p1->fresh()->priority)->toBe(2);
    expect($p2->fresh()->priority)->toBe(1);
});

it('reorder stops assigns priority descending', function () {
    $district = loc_district();
    $s1       = loc_stop(['district_id' => $district->id, 'priority' => 0]);
    $s2       = loc_stop(['district_id' => $district->id, 'priority' => 0]);
    $admin    = loc_admin();

    $this->actingAs($admin)
        ->post(route('admin.locations.stops.reorder'), [
            'ids' => [$s2->id, $s1->id],
        ])
        ->assertRedirect();

    expect($s2->fresh()->priority)->toBe(2);
    expect($s1->fresh()->priority)->toBe(1);
});

// ─── Province create / edit page accessible ──────────────────────────────────

it('province create page returns 200', function () {
    $this->actingAs(loc_admin())
        ->get(route('admin.locations.provinces.create'))
        ->assertOk()
        ->assertViewIs('admin.locations.form-province');
});

it('province edit page returns 200', function () {
    $province = loc_province();

    $this->actingAs(loc_admin())
        ->get(route('admin.locations.provinces.edit', $province))
        ->assertOk()
        ->assertViewIs('admin.locations.form-province');
});

it('district create page returns 200', function () {
    loc_district_type();

    $this->actingAs(loc_admin())
        ->get(route('admin.locations.districts.create'))
        ->assertOk()
        ->assertViewIs('admin.locations.form-district');
});
