<?php

use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\BusServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('doi-xe')->name('buses.')->group(function () {
    Route::get('/', [BusController::class, 'index'])->name('index');
    Route::get('/them', [BusController::class, 'create'])->name('create');
    Route::post('/', [BusController::class, 'store'])->name('store');
    Route::get('/{bus}/sua', [BusController::class, 'edit'])->name('edit');
    Route::put('/{bus}', [BusController::class, 'update'])->name('update');
    Route::delete('/{bus}', [BusController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete', [BusController::class, 'bulkDestroy'])->name('bulk-destroy');
    Route::post('/reorder', [BusController::class, 'reorder'])->name('reorder');

    // Bus services (?section=services) — CRUD via slide-over
    Route::prefix('dich-vu')->name('services.')->group(function () {
        Route::post('/', [BusServiceController::class, 'store'])->name('store');
        Route::put('/{service}', [BusServiceController::class, 'update'])->name('update');
        Route::delete('/{service}', [BusServiceController::class, 'destroy'])->name('destroy');
    });
});
