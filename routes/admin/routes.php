<?php

use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\RouteStopController;
use Illuminate\Support\Facades\Route;

Route::prefix('tuyen-duong')->name('routes.')->group(function () {
    Route::get('/', [RouteController::class, 'index'])->name('index');
    Route::get('/them', [RouteController::class, 'create'])->name('create');
    Route::post('/', [RouteController::class, 'store'])->name('store');
    Route::get('/{route}/sua', [RouteController::class, 'edit'])->name('edit');
    Route::put('/{route}', [RouteController::class, 'update'])->name('update');
    Route::delete('/{route}', [RouteController::class, 'destroy'])->name('destroy');
    Route::post('/bulk-delete', [RouteController::class, 'bulkDestroy'])->name('bulk-destroy');
    Route::post('/reorder', [RouteController::class, 'reorder'])->name('reorder');

    // Route stops — independent fetch CRUD (only active on edit page)
    Route::prefix('{route}/diem-dung')->name('stops.')->group(function () {
        Route::post('/', [RouteStopController::class, 'store'])->name('store');
        Route::put('/{stop}', [RouteStopController::class, 'update'])->name('update');
        Route::delete('/{stop}', [RouteStopController::class, 'destroy'])->name('destroy');
        Route::post('/reorder', [RouteStopController::class, 'reorder'])->name('reorder');
    });
});
