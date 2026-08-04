<?php

use App\Http\Controllers\Admin\HolidaySurchargeController;
use Illuminate\Support\Facades\Route;

Route::get('phu-thu', [HolidaySurchargeController::class, 'index'])->name('surcharges.index');
Route::get('phu-thu/them', [HolidaySurchargeController::class, 'create'])->name('surcharges.create');
Route::post('phu-thu', [HolidaySurchargeController::class, 'store'])->name('surcharges.store');
Route::get('phu-thu/{holidaySurcharge}/sua', [HolidaySurchargeController::class, 'edit'])->name('surcharges.edit');
Route::put('phu-thu/{holidaySurcharge}', [HolidaySurchargeController::class, 'update'])->name('surcharges.update');
Route::delete('phu-thu/{holidaySurcharge}', [HolidaySurchargeController::class, 'destroy'])->name('surcharges.destroy');
Route::delete('phu-thu', [HolidaySurchargeController::class, 'bulkDestroy'])->name('surcharges.bulk-destroy');
