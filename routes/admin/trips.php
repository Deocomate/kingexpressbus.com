<?php

use App\Http\Controllers\Admin\TripBlockController;
use App\Http\Controllers\Admin\TripController;
use Illuminate\Support\Facades\Route;

// Trips section (index also handles ?section=blocks)
Route::get('chuyen-xe', [TripController::class, 'index'])->name('trips.index');
Route::get('chuyen-xe/them', [TripController::class, 'create'])->name('trips.create');
Route::post('chuyen-xe', [TripController::class, 'store'])->name('trips.store');
Route::get('chuyen-xe/{trip}/sua', [TripController::class, 'edit'])->name('trips.edit');
Route::put('chuyen-xe/{trip}', [TripController::class, 'update'])->name('trips.update');
Route::delete('chuyen-xe/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');
Route::delete('chuyen-xe', [TripController::class, 'bulkDestroy'])->name('trips.bulk-destroy');
Route::patch('chuyen-xe/{trip}/toggle-active', [TripController::class, 'toggleActive'])->name('trips.toggle-active');

// Inline JSON endpoint for dependent select (trips filtered by route)
Route::get('chuyen-xe/api/trips-for-route', [TripController::class, 'tripsForRoute'])->name('trips.api.trips-for-route');

// TripBlock sub-routes (create/edit pages are separate; index lives in trips.index?section=blocks)
Route::get('chuyen-xe/khoa/them', [TripBlockController::class, 'create'])->name('trips.blocks.create');
Route::post('chuyen-xe/khoa', [TripBlockController::class, 'store'])->name('trips.blocks.store');
Route::get('chuyen-xe/khoa/{tripBlock}/sua', [TripBlockController::class, 'edit'])->name('trips.blocks.edit');
Route::put('chuyen-xe/khoa/{tripBlock}', [TripBlockController::class, 'update'])->name('trips.blocks.update');
Route::delete('chuyen-xe/khoa/{tripBlock}', [TripBlockController::class, 'destroy'])->name('trips.blocks.destroy');
Route::delete('chuyen-xe/khoa', [TripBlockController::class, 'bulkDestroy'])->name('trips.blocks.bulk-destroy');
