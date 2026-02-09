<?php

use App\Http\Controllers\System\CkFinderController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Import Middleware
use App\Http\Middleware\Roles\AdminAuthMiddleware;
use App\Http\Middleware\Roles\CustomerAuthMiddleware;

// Auth Controllers
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\AuthenticateController;
use App\Http\Controllers\Admin\Auth\LogoutController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\WebProfileController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\DistrictTypeController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\StopController;
use App\Http\Controllers\Admin\RouteController as AdminRouteController;
use App\Http\Controllers\Admin\BusController as AdminBusController;
use App\Http\Controllers\Admin\TripController as AdminTripController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\BusServiceController;

// Client Controllers
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\TripController as ClientTripController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\PageController;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\SearchController as ClientSearchController;

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES
| Homepage, search trips, booking, contact, pages, auth
|--------------------------------------------------------------------------
*/
Route::name('client.')->group(function () {
    // Locale switching
    Route::get('/locale/{locale}', function (string $locale, Request $request) {
        $availableLocales = ['en', 'vi'];

        if (!in_array($locale, $availableLocales, true)) {
            abort(404);
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return back(fallback: route('client.home'));
    })->name('locale.switch');

    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Search
    Route::get('/tim-kiem', [ClientSearchController::class, 'search'])->name('routes.search');

    // Routes/Trips
    Route::get('/tuyen-duong', [ClientTripController::class, 'index'])->name('routes.index');
    Route::get('/tuyen-duong/{slug}', [ClientTripController::class, 'show'])->name('routes.show');

    // Booking
    Route::get('/dat-ve', [ClientBookingController::class, 'create'])->name('booking.create');
    Route::post('/dat-ve', [ClientBookingController::class, 'store'])->name('booking.store');
    Route::get('/dat-ve/thanh-cong', [ClientBookingController::class, 'success'])->name('booking.success');

    // Static pages
    Route::get('/lien-he', [ContactController::class, 'index'])->name('contact');
    Route::get('/gioi-thieu', [PageController::class, 'about'])->name('about');
    Route::get('/trang/{slug}', [PageController::class, 'show'])->name('page.show');

    // Client Authentication
    Route::get('/dang-nhap', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/dang-nhap', [ClientAuthController::class, 'login'])->name('login.submit');
    Route::get('/dang-ky', [ClientAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/dang-ky', [ClientAuthController::class, 'register'])->name('register.submit');
    Route::get('/quen-mat-khau', [ClientAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/quen-mat-khau', [ClientAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/dat-lai-mat-khau', [ClientAuthController::class, 'resetPassword'])->name('password.update');
    Route::post('/dang-xuat', [ClientAuthController::class, 'logout'])->name('logout');

    // Protected client routes
    Route::middleware(CustomerAuthMiddleware::class)->group(function () {
        Route::get('/tai-khoan', [ClientProfileController::class, 'index'])->name('profile.index');
    });
});


/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES (Admin)
|--------------------------------------------------------------------------
*/
Route::get("/examples/{name}", [\App\Http\Controllers\Examples\ExamplesController::class, "index"]);
Route::get('login', [LoginController::class, 'login'])->name('login');
Route::post('authenticate', [AuthenticateController::class, 'authenticate'])->name('authenticate');
Route::get('logout', [LogoutController::class, 'logout'])->name('logout');
Route::get('/dat-lai-mat-khau/{token}', [ClientAuthController::class, 'showResetForm'])->name('password.reset');


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
| Centralized management for single-tenant system
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(AdminAuthMiddleware::class)
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.index');

        // Website Configuration
        Route::resource('web_profiles', WebProfileController::class);
        Route::patch('/web_profiles/{web_profile}/set-default', [WebProfileController::class, 'setDefault'])->name('web_profiles.setDefault');
        Route::resource('menus', MenuController::class)->except(['show']);
        Route::post('/menus/update-order', [MenuController::class, 'updateOrder'])->name('menus.updateOrder');
        Route::post('/menus/add-item', [MenuController::class, 'addItem'])->name('menus.addItem');

        // Location Management
        Route::resource('provinces', ProvinceController::class);
        Route::resource('district-types', DistrictTypeController::class);
        Route::resource('districts', DistrictController::class);
        Route::post('/districts/update-order', [DistrictController::class, 'updateOrder'])->name('districts.updateOrder');
        Route::resource('stops', StopController::class);
        Route::post('/stops/update-order', [StopController::class, 'updateOrder'])->name('stops.updateOrder');

        // Route Management (Tuyến đường)
        Route::resource('routes', AdminRouteController::class);
        Route::post('/routes/update-order', [AdminRouteController::class, 'updateOrder'])->name('routes.updateOrder');
        Route::get('/routes-all', [AdminRouteController::class, 'all'])->name('routes.all');

        // Bus Management (Quản lý đội xe)
        Route::resource('buses', AdminBusController::class);
        Route::get('/buses-list', [AdminBusController::class, 'list'])->name('buses.list');
        Route::get('/buses-all', [AdminBusController::class, 'all'])->name('buses.all');
        Route::resource('bus-services', BusServiceController::class);

        // Trip Management (Quản lý chuyến xe / Lịch chạy)
        Route::resource('trips', AdminTripController::class);
        Route::post('/trips/update-order', [AdminTripController::class, 'updateOrder'])->name('trips.updateOrder');
        Route::patch('/trips/{trip}/toggle-status', [AdminTripController::class, 'toggleStatus'])->name('trips.toggleStatus');

        // Booking Management (Quản lý đặt vé)
        Route::resource('bookings', AdminBookingController::class);
    });


/*
|--------------------------------------------------------------------------
| OTHER UTILITY ROUTES
|--------------------------------------------------------------------------
*/
Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
    ->name('ckfinder_connector')
    ->middleware('auth');

Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
    ->name('ckfinder_browser')
    ->middleware('auth');

Route::post('/ckfinder/delete-file', [CkFinderController::class, 'deleteFile'])
    ->name('ckfinder_delete_file')
    ->middleware('auth');

Route::post('/ckfinder/upload', [CkFinderController::class, 'upload'])
    ->name('ckfinder_upload')
    ->middleware('auth');
