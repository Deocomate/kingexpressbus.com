<?php

use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\DistrictTypeController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\StopController;
use Illuminate\Support\Facades\Route;

// Index (section tabs: provinces | district-types | districts | stops)
Route::get('dia-diem', [LocationController::class, 'index'])->name('locations.index');

// ── Province ────────────────────────────────────────────────────────────────
// Specific routes BEFORE {province} wildcard
Route::post('dia-diem/tinh-thanh/xoa-nhieu', [ProvinceController::class, 'bulkDestroy'])->name('locations.provinces.bulk-destroy');
Route::post('dia-diem/tinh-thanh/sap-xep', [ProvinceController::class, 'reorder'])->name('locations.provinces.reorder');
Route::get('dia-diem/tinh-thanh/tao', [ProvinceController::class, 'create'])->name('locations.provinces.create');

Route::post('dia-diem/tinh-thanh', [ProvinceController::class, 'store'])->name('locations.provinces.store');
Route::get('dia-diem/tinh-thanh/{province}/sua', [ProvinceController::class, 'edit'])->name('locations.provinces.edit');
Route::put('dia-diem/tinh-thanh/{province}', [ProvinceController::class, 'update'])->name('locations.provinces.update');
Route::delete('dia-diem/tinh-thanh/{province}', [ProvinceController::class, 'destroy'])->name('locations.provinces.destroy');

// ── DistrictType ─────────────────────────────────────────────────────────────
Route::post('dia-diem/loai-dia-diem/xoa-nhieu', [DistrictTypeController::class, 'bulkDestroy'])->name('locations.district-types.bulk-destroy');
Route::post('dia-diem/loai-dia-diem/sap-xep', [DistrictTypeController::class, 'reorder'])->name('locations.district-types.reorder');

Route::post('dia-diem/loai-dia-diem', [DistrictTypeController::class, 'store'])->name('locations.district-types.store');
Route::put('dia-diem/loai-dia-diem/{districtType}', [DistrictTypeController::class, 'update'])->name('locations.district-types.update');
Route::delete('dia-diem/loai-dia-diem/{districtType}', [DistrictTypeController::class, 'destroy'])->name('locations.district-types.destroy');

// ── District ─────────────────────────────────────────────────────────────────
Route::post('dia-diem/dia-diem/xoa-nhieu', [DistrictController::class, 'bulkDestroy'])->name('locations.districts.bulk-destroy');
Route::post('dia-diem/dia-diem/sap-xep', [DistrictController::class, 'reorder'])->name('locations.districts.reorder');
Route::get('dia-diem/dia-diem/tao', [DistrictController::class, 'create'])->name('locations.districts.create');

Route::post('dia-diem/dia-diem', [DistrictController::class, 'store'])->name('locations.districts.store');
Route::get('dia-diem/dia-diem/{district}/sua', [DistrictController::class, 'edit'])->name('locations.districts.edit');
Route::put('dia-diem/dia-diem/{district}', [DistrictController::class, 'update'])->name('locations.districts.update');
Route::delete('dia-diem/dia-diem/{district}', [DistrictController::class, 'destroy'])->name('locations.districts.destroy');

// ── Stop ─────────────────────────────────────────────────────────────────────
Route::post('dia-diem/diem-dung/xoa-nhieu', [StopController::class, 'bulkDestroy'])->name('locations.stops.bulk-destroy');
Route::post('dia-diem/diem-dung/sap-xep', [StopController::class, 'reorder'])->name('locations.stops.reorder');

Route::post('dia-diem/diem-dung', [StopController::class, 'store'])->name('locations.stops.store');
Route::put('dia-diem/diem-dung/{stop}', [StopController::class, 'update'])->name('locations.stops.update');
Route::delete('dia-diem/diem-dung/{stop}', [StopController::class, 'destroy'])->name('locations.stops.destroy');
