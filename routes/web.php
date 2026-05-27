<?php

use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\BookingController as ClientBookingController;
use App\Http\Controllers\Client\ContactController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\PageController;
use App\Http\Controllers\Client\ProfileController as ClientProfileController;
use App\Http\Controllers\Client\SearchController as ClientSearchController;
use App\Http\Controllers\Client\SePayController as ClientSePayController;
use App\Http\Controllers\Client\TripController as ClientTripController;
use App\Http\Middleware\Roles\CustomerAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('client.')->group(function () {
    Route::get('/locale/{locale}', function (string $locale, Request $request) {
        $availableLocales = config('app.supported_locales', ['en', 'vi']);

        if (! in_array($locale, $availableLocales, true)) {
            abort(404);
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        $redirect = $request->query('redirect');
        if (is_string($redirect) && $redirect !== '') {
            if (str_starts_with($redirect, '/') && ! str_starts_with($redirect, '//')) {
                return redirect($redirect);
            }
        }

        return redirect()->route('client.home');
    })->name('locale.switch');

    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/tim-kiem', [ClientSearchController::class, 'search'])->name('routes.search');

    Route::get('/tuyen-duong', [ClientTripController::class, 'index'])->name('routes.index');
    Route::get('/tuyen-duong/{slug}', [ClientTripController::class, 'show'])->name('routes.show');

    Route::get('/dat-ve', [ClientBookingController::class, 'create'])->name('booking.create');
    Route::post('/dat-ve', [ClientBookingController::class, 'store'])->name('booking.store');
    Route::get('/dat-ve/thanh-cong', [ClientBookingController::class, 'success'])->name('booking.success');
    Route::get('/dat-ve/trang-thai-thanh-toan/{code}', [ClientBookingController::class, 'paymentStatus'])->name('booking.payment_status');

    Route::get('/dat-ve/chuyen-huong-sepay/{code}', [ClientSePayController::class, 'redirect'])->name('sepay.redirect');
    Route::get('/dat-ve/sepay/thanh-cong/{code}', [ClientSePayController::class, 'success'])->name('sepay.success');
    Route::get('/dat-ve/sepay/that-bai/{code}', [ClientSePayController::class, 'error'])->name('sepay.error');
    Route::get('/dat-ve/sepay/huy/{code}', [ClientSePayController::class, 'cancel'])->name('sepay.cancel');
    Route::post('/checkout/sepay/ipn', [ClientSePayController::class, 'ipn'])->name('sepay.ipn');

    Route::get('/lien-he', [ContactController::class, 'index'])->name('contact');
    Route::get('/gioi-thieu', [PageController::class, 'about'])->name('about');
    Route::get('/trang/{slug}', [PageController::class, 'show'])->name('page.show');

    Route::get('/dang-nhap', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/dang-nhap', [ClientAuthController::class, 'login'])->name('login.submit');
    Route::get('/dang-ky', [ClientAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/dang-ky', [ClientAuthController::class, 'register'])->name('register.submit');
    Route::get('/quen-mat-khau', [ClientAuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/quen-mat-khau', [ClientAuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::post('/dat-lai-mat-khau', [ClientAuthController::class, 'resetPassword'])->name('password.update');
    Route::post('/dang-xuat', [ClientAuthController::class, 'logout'])->name('logout');

    Route::middleware(CustomerAuthMiddleware::class)->group(function () {
        Route::get('/tai-khoan', [ClientProfileController::class, 'index'])->name('profile.index');
    });
});

Route::get('/dat-lai-mat-khau/{token}', [ClientAuthController::class, 'showResetForm'])
    ->name('password.reset');
