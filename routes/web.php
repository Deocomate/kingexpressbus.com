<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KingExpressBus\Admin\AdminController;
use App\Http\Controllers\KingExpressBus\Admin\ProvinceController;
use App\Http\Controllers\KingExpressBus\Admin\DistrictController;
use App\Http\Controllers\KingExpressBus\Admin\RouteController;
use App\Http\Controllers\KingExpressBus\Admin\BusController;
use App\Http\Controllers\KingExpressBus\Admin\BusRouteController;
use App\Http\Controllers\KingExpressBus\Admin\BookingController;
use App\Http\Controllers\KingExpressBus\Admin\MenuController;

// [cite: 3141]
use App\Http\Controllers\KingExpressBus\Auth\AuthenticationController;
use App\Http\Middleware\AuthenticationMiddleware;

use App\Http\Controllers\KingExpressBus\Client\HomePageController;
use App\Http\Controllers\KingExpressBus\Client\SearchController;
use App\Http\Controllers\KingExpressBus\Client\BusListPageController;
use App\Http\Controllers\KingExpressBus\Client\BusDetailPageController;
use App\Http\Controllers\KingExpressBus\Client\BookingPageController;
use App\Http\Controllers\KingExpressBus\Client\AuthClientController;
use App\Http\Controllers\KingExpressBus\Client\UserInformationController;

// Removed: use App\Http\Controllers\KingExpressBus\Client\PaymentController; // [cite: 3142]


// Client Pages
Route::get("/", [HomePageController::class, "index"])->name("homepage");
Route::post("/search", [SearchController::class, "search_route"])->name("client.search");
Route::get("/tuyen-duong/{route_slug}", [BusListPageController::class, "index"])->name("client.bus_list");
Route::get("/chi-tiet-xe/{bus_route_slug}", [BusDetailPageController::class, "index"])->name("client.bus_detail");
Route::get("/dat-ve/{bus_route_slug}", [BookingPageController::class, "index"])->name("client.booking_page");
Route::post("/dat-ve/{bus_route_slug}", [BookingPageController::class, "booking"])->name("client.booking"); // [cite: 3143]

Route::get("/dang-nhap", [AuthClientController::class, "login_page"])->name("client.login_page");
Route::post("/dang-nhap", [AuthClientController::class, "login"])->name("client.login");
Route::get("/dang-ky", [AuthClientController::class, "register_page"])->name("client.register_page");
Route::post("/dang-ky", [AuthClientController::class, "register"])->name("client.register");

Route::middleware([\App\Http\Middleware\KingExpressBus\ClientAuthMiddleware::class])->group(function () { // [cite: 3144]
    Route::get("/tai-khoan", [UserInformationController::class, "index"])->name("client.user_information_page");
    Route::post("/tai-khoan", [UserInformationController::class, "update"])->name("client.user_information.update");
    Route::get("/dang-xuat", [AuthClientController::class, "logout"])->name("client.logout");
});

// Removed VNPAY payment routes [cite: 3145]

// Auth
Route::get('/admin/login', [AuthenticationController::class, "login"])->name("admin.login"); // [cite: 3146]
Route::get('/admin/logout', [AuthenticationController::class, "logout"])->name("admin.logout"); // [cite: 3146]
Route::post('/admin/authenticate', [AuthenticationController::class, "authenticate"])->name("admin.authenticate"); // [cite: 3146]

Route::prefix('admin')->name("admin.")->middleware(AuthenticationMiddleware::class)->group(function () { // [cite: 3147]
    Route::get("/dashboard", [AdminController::class, "index"])->name("dashboard.index");
    Route::post("/dashboard", [AdminController::class, "update"])->name("dashboard.update");

    Route::resource("provinces", ProvinceController::class);
    Route::resource("districts", DistrictController::class);
    Route::resource("routes", RouteController::class);
    Route::resource("buses", BusController::class);
    Route::resource("bus_routes", BusRouteController::class);
    Route::resource("bookings", BookingController::class);
    Route::resource("menus", MenuController::class);
    Route::post('/menus/update-order', [MenuController::class, 'updateOrder'])->name('menus.updateOrder');
});

// CK Plugin
Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction') // [cite: 3148]
->name('ckfinder_connector'); // [cite: 3148]

Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction') // [cite: 3148]
->name('ckfinder_browser'); // [cite: 3148]
