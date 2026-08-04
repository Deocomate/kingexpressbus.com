<?php

use App\Http\Controllers\Admin\BookingActionController;
use App\Http\Controllers\Admin\BookingController;
use Illuminate\Support\Facades\Route;

// Booking CRUD
Route::prefix('dat-ve')->name('bookings.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/bang', [BookingController::class, 'table'])->name('table');      // poll partial
    Route::get('/tao', [BookingController::class, 'create'])->name('create');
    Route::post('/', [BookingController::class, 'store'])->name('store');
    Route::get('/{booking}', [BookingController::class, 'show'])->name('show');   // detail slide-over
    Route::get('/{booking}/sua', [BookingController::class, 'edit'])->name('edit');
    Route::put('/{booking}', [BookingController::class, 'update'])->name('update');

    // Booking status transition actions
    Route::post('/{booking}/xac-nhan', [BookingActionController::class, 'confirm'])->name('confirm');
    Route::post('/{booking}/huy', [BookingActionController::class, 'cancel'])->name('cancel');
    Route::post('/{booking}/hoan-thanh', [BookingActionController::class, 'complete'])->name('complete');
});
