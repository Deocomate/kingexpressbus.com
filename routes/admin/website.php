<?php

use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\MenuTreeController;
use App\Http\Controllers\Admin\WebProfileController;
use Illuminate\Support\Facades\Route;

// Main settings page — ?section=profile|menus
Route::get('cau-hinh-website', [WebProfileController::class, 'index'])->name('website.index');

// WebProfile CRUD
Route::prefix('cau-hinh-website/profiles')->name('website.profiles.')->group(function () {
    Route::get('create', [WebProfileController::class, 'create'])->name('create');
    Route::post('/', [WebProfileController::class, 'store'])->name('store');
    Route::get('{profile}/edit', [WebProfileController::class, 'edit'])->name('edit');
    Route::put('{profile}', [WebProfileController::class, 'update'])->name('update');
    Route::delete('{profile}', [WebProfileController::class, 'destroy'])->name('destroy');
    Route::post('{profile}/set-default', [WebProfileController::class, 'setDefault'])->name('setDefault');
});

// Menu CRUD + tree reorder
Route::prefix('cau-hinh-website/menus')->name('website.menus.')->group(function () {
    // Static route before wildcard
    Route::post('reorder', [MenuTreeController::class, 'reorder'])->name('reorder');
    Route::post('/', [MenuController::class, 'store'])->name('store');
    Route::put('{menu}', [MenuController::class, 'update'])->name('update');
    Route::delete('{menu}', [MenuController::class, 'destroy'])->name('destroy');
});
