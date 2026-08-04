<?php

/**
 * Booking/admin refactor coverage against /quan-tri.
 */

use App\Enums\BookingStatus;
use App\Helpers\SystemHelper;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\BusService;
use App\Models\Menu;
use App\Models\Province;
use App\Models\Route;
use App\Models\Trip;
use App\Models\User;
use App\Models\WebProfile;
use App\Support\Admin\Navigation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('only admin users can access the blade admin panel', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();

    $this->actingAs($customer)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('admin can log in through the blade login page', function () {
    User::factory()->admin()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->from(route('admin.login'))
        ->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticated();
});

test('blade login shows validation for invalid credentials', function () {
    User::factory()->admin()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
    ]);

    $this->from(route('admin.login'))
        ->post(route('admin.login.submit'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors('email');
});

test('web profile default flag is unique', function () {
    $first = WebProfile::factory()->default()->create();
    $second = WebProfile::factory()->create();

    $second->update(['is_default' => true]);

    expect($first->refresh()->is_default)->toBeFalse()
        ->and($second->refresh()->is_default)->toBeTrue();
});

test('bus form syncs services and persists manual seat count', function () {
    $this->actingAs(User::factory()->admin()->create());

    $service = BusService::factory()->create();

    $this->post(route('admin.buses.store'), [
        'name' => 'Test Cabin',
        'model_name' => 'Cabin',
        'services' => [$service->id],
        'seat_count' => 22,
        'priority' => 10,
        'content' => '',
    ])->assertRedirect();

    $bus = Bus::query()->firstOrFail();

    expect($bus->seat_count)->toBe(22)
        ->and($bus->services()->pluck('bus_services.id')->all())->toBe([$service->id]);
});

test('admin can view a booking detail page', function () {
    $this->actingAs(User::factory()->admin()->create());

    $booking = Booking::factory()->create();

    $this->get(route('admin.bookings.show', $booking))
        ->assertSuccessful()
        ->assertSee($booking->booking_code);
});

test('booking row actions call booking service transitions', function () {
    Mail::fake();

    $this->actingAs(User::factory()->admin()->create());

    $booking = Booking::factory()->create(['status' => 'pending']);

    $this->post(route('admin.bookings.confirm', $booking))->assertRedirect();
    expect($booking->refresh()->status)->toBe(BookingStatus::Confirmed);

    $this->post(route('admin.bookings.complete', $booking))->assertRedirect();
    expect($booking->refresh()->status)->toBe(BookingStatus::Completed);
});

test('menu root sentinel is used for top level menu items', function () {
    $parent = Menu::factory()->create();
    $child = Menu::factory()->create(['parent_id' => $parent->id]);

    expect($parent->parent_id)->toBe(Menu::ROOT_PARENT_ID)
        ->and($child->parent_id)->toBe($parent->id);
});

test('admin navigation exposes Vietnamese labels for grouped modules', function () {
    // Intentional navigation merge (13 resources → 8 menu items).
    $labels = collect(Navigation::items())->pluck('label')->all();

    expect($labels)->toBe([
        'Tổng quan',
        'Đặt vé',
        'Chuyến xe',
        'Tuyến đường',
        'Đội xe',
        'Địa điểm',
        'Phụ thu',
        'Cấu hình website',
    ]);
});

test('trip list has Vietnamese tabs and filters active records', function () {
    $this->actingAs(User::factory()->admin()->create());

    $activeTrip = Trip::factory()->create(['is_active' => true]);
    $inactiveTrip = Trip::factory()->create(['is_active' => false]);

    $all = $this->get(route('admin.trips.index'))
        ->assertSuccessful()
        ->assertSee('Tất cả')
        ->assertSee('Đang hoạt động')
        ->assertSee('Tạm ngưng');

    expect($all->viewData('activeTab'))->toBe('all');

    $activeIds = $this->get(route('admin.trips.index', ['tab' => 'active']))
        ->viewData('paginator')
        ->pluck('id')
        ->all();

    expect($activeIds)->toContain($activeTrip->id)
        ->and($activeIds)->not->toContain($inactiveTrip->id);

    $inactiveIds = $this->get(route('admin.trips.index', ['tab' => 'inactive']))
        ->viewData('paginator')
        ->pluck('id')
        ->all();

    expect($inactiveIds)->toContain($inactiveTrip->id)
        ->and($inactiveIds)->not->toContain($activeTrip->id);
});

test('route and trip tables render relationship grouping data', function () {
    $this->actingAs(User::factory()->admin()->create());

    $startProvince = Province::factory()->create(['name' => 'Hà Nội']);
    $endProvince = Province::factory()->create(['name' => 'Sa Pa']);
    $route = Route::factory()->create([
        'province_start_id' => $startProvince->id,
        'province_end_id' => $endProvince->id,
        'name' => 'Hà Nội - Sa Pa',
    ]);
    $trip = Trip::factory()->create(['route_id' => $route->id]);

    $this->get(route('admin.routes.index'))
        ->assertSuccessful()
        ->assertSee('Hà Nội')
        ->assertSee($route->name);

    $this->get(route('admin.trips.index'))
        ->assertSuccessful()
        ->assertSee('Hà Nội - Sa Pa');
});

test('booking table formats status and payment states in Vietnamese', function () {
    $this->actingAs(User::factory()->admin()->create());

    Booking::factory()->create([
        'status' => 'confirmed',
        'payment_method' => 'cash_on_pickup',
        'payment_status' => 'unpaid',
        'booking_date' => now()->addDay()->toDateString(),
    ]);

    $this->get(route('admin.bookings.index'))
        ->assertSuccessful()
        ->assertSee('Đã xác nhận')
        ->assertSee('Chưa thanh toán')
        ->assertSee('Ngày đặt vé')
        ->assertSee('Ngày đi')
        ->assertDontSee('Confirmed')
        ->assertDontSee('Unpaid');
});

test('admin media URLs support client asset paths', function () {
    expect(SystemHelper::mediaUrl('/assets/client/images/demo.jpg'))
        ->toBe(asset('assets/client/images/demo.jpg'));
});

test('resolves legacy client image paths via mediaUrl', function () {
    expect(SystemHelper::mediaUrl('/client/images/city_imgs/ha-noi.jpg'))
        ->toBe(asset('assets/client/images/city_imgs/ha-noi.jpg'));
});

test('resolves legacy userfiles paths via mediaUrl', function () {
    expect(SystemHelper::mediaUrl('/userfiles/files/kingexpressbus/cabin/1.jpg'))
        ->toBe(Storage::disk('public')->url('media/files/kingexpressbus/cabin/1.jpg'));
});
