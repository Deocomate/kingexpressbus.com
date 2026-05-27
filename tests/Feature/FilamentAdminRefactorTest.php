<?php

use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Buses\BusResource;
use App\Filament\Resources\Buses\Pages\CreateBus;
use App\Filament\Resources\BusServices\BusServiceResource;
use App\Filament\Resources\Districts\DistrictResource;
use App\Filament\Resources\DistrictTypes\DistrictTypeResource;
use App\Filament\Resources\HolidaySurcharges\HolidaySurchargeResource;
use App\Filament\Resources\Menus\MenuResource;
use App\Filament\Resources\Provinces\ProvinceResource;
use App\Filament\Resources\Routes\Pages\ListRoutes;
use App\Filament\Resources\Routes\RouteResource;
use App\Filament\Resources\Stops\StopResource;
use App\Filament\Resources\Trips\Pages\ListTrips;
use App\Filament\Resources\Trips\TripResource;
use App\Filament\Resources\WebProfiles\WebProfileResource;
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
use Filament\Actions\Testing\TestAction;
use Filament\Auth\Pages\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('only admin users can access the filament admin panel', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk();

    $this->actingAs($customer)
        ->get('/admin')
        ->assertForbidden();
});

test('admin can log in through the filament login page', function () {
    User::factory()->admin()->create([
        'email' => 'admin@example.com',
    ]);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => 'admin@example.com',
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect('/admin');
});

test('filament login shows validation for invalid credentials', function () {
    User::factory()->admin()->create([
        'email' => 'admin@example.com',
    ]);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors([
            'email',
        ]);
});

test('web profile default flag is unique', function () {
    $first = WebProfile::factory()->default()->create();
    $second = WebProfile::factory()->create();

    $second->update(['is_default' => true]);

    expect($first->refresh()->is_default)->toBeFalse()
        ->and($second->refresh()->is_default)->toBeTrue();
});

test('bus resource syncs services and recomputes seat count from seat map', function () {
    $this->actingAs(User::factory()->admin()->create());

    $service = BusService::factory()->create();

    Livewire::test(CreateBus::class)
        ->fillForm([
            'name' => 'Test Cabin',
            'model_name' => 'Cabin',
            'services' => [$service->id],
            'seat_map' => [
                ['seat_number' => 'A1', 'status' => 'available', 'deck' => 1],
                ['seat_number' => 'A2', 'status' => 'disabled', 'deck' => 1],
                ['seat_number' => 'B1', 'status' => 'available', 'deck' => 2],
            ],
            'image_list_url' => [],
            'priority' => 10,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $bus = Bus::query()->firstOrFail();

    expect($bus->seat_count)->toBe(2)
        ->and($bus->services()->pluck('bus_services.id')->all())->toBe([$service->id]);
});

test('booking row actions call booking service transitions', function () {
    Mail::fake();

    $this->actingAs(User::factory()->admin()->create());

    $booking = Booking::factory()->create();

    Livewire::test(ListBookings::class)
        ->callAction(TestAction::make('confirm')->table($booking));

    expect($booking->refresh()->status)->toBe('confirmed');

    Livewire::test(ListBookings::class)
        ->callAction(TestAction::make('complete')->table($booking));

    expect($booking->refresh()->status)->toBe('completed');
});

test('menu root sentinel is used for top level menu items', function () {
    $parent = Menu::factory()->create();
    $child = Menu::factory()->create(['parent_id' => $parent->id]);

    expect($parent->parent_id)->toBe(Menu::ROOT_PARENT_ID)
        ->and($child->parent_id)->toBe($parent->id);
});

test('filament resources expose Vietnamese labels', function () {
    $resources = [
        BookingResource::class => ['Đơn đặt vé', 'Đơn đặt vé', 'Quản lý đặt vé'],
        BusResource::class => ['Xe', 'Xe', 'Quản lý xe'],
        BusServiceResource::class => ['Dịch vụ xe', 'Dịch vụ xe', 'Dịch vụ xe'],
        DistrictResource::class => ['Địa điểm', 'Địa điểm', 'Địa điểm'],
        DistrictTypeResource::class => ['Loại địa điểm', 'Loại địa điểm', 'Loại địa điểm'],
        HolidaySurchargeResource::class => ['Phụ thu ngày lễ', 'Phụ thu ngày lễ', 'Phụ thu ngày lễ'],
        MenuResource::class => ['Menu', 'Menu', 'Quản lý menu'],
        ProvinceResource::class => ['Tỉnh/thành', 'Tỉnh/thành', 'Tỉnh/thành'],
        RouteResource::class => ['Tuyến đường', 'Tuyến đường', 'Quản lý tuyến đường'],
        StopResource::class => ['Điểm dừng', 'Điểm dừng', 'Điểm dừng'],
        TripResource::class => ['Chuyến xe', 'Chuyến xe', 'Quản lý chuyến xe'],
        WebProfileResource::class => ['Cấu hình website', 'Cấu hình website', 'Cấu hình website'],
    ];

    foreach ($resources as $resource => [$modelLabel, $pluralModelLabel, $navigationLabel]) {
        expect($resource::getModelLabel())->toBe($modelLabel)
            ->and($resource::getPluralModelLabel())->toBe($pluralModelLabel)
            ->and($resource::getNavigationLabel())->toBe($navigationLabel);
    }
});

test('trip list has Vietnamese tabs and filters active records', function () {
    $this->actingAs(User::factory()->admin()->create());

    $activeTrip = Trip::factory()->create(['is_active' => true]);
    $inactiveTrip = Trip::factory()->create(['is_active' => false]);

    Livewire::test(ListTrips::class)
        ->assertSuccessful()
        ->assertSee('Tất cả')
        ->assertSee('Đang hoạt động')
        ->assertSee('Tạm ngưng')
        ->set('activeTab', 'active')
        ->assertCanSeeTableRecords([$activeTrip])
        ->assertCanNotSeeTableRecords([$inactiveTrip])
        ->set('activeTab', 'inactive')
        ->assertCanSeeTableRecords([$inactiveTrip])
        ->assertCanNotSeeTableRecords([$activeTrip]);
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

    Livewire::test(ListRoutes::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$route])
        ->assertSee('Hà Nội');

    Livewire::test(ListTrips::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords([$trip])
        ->assertSee('Hà Nội - Sa Pa');
});

test('booking table formats status and payment states in Vietnamese', function () {
    $this->actingAs(User::factory()->admin()->create());

    Booking::factory()->create([
        'status' => 'confirmed',
        'payment_method' => 'cash_on_pickup',
        'payment_status' => 'unpaid',
    ]);

    Livewire::test(ListBookings::class)
        ->assertSuccessful()
        ->assertSee('Đã xác nhận')
        ->assertSee('Thanh toán khi đón')
        ->assertSee('Chưa thanh toán')
        ->assertDontSee('Confirmed')
        ->assertDontSee('Cash on pickup')
        ->assertDontSee('Unpaid');
});

test('admin media URLs support client asset paths', function () {
    expect(SystemHelper::mediaUrl('/client/images/demo.jpg'))
        ->toBe(asset('client/images/demo.jpg'));
});
