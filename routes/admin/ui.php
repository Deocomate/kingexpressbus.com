<?php

use App\Http\Controllers\Admin\Api\OptionsController;
use App\Http\Controllers\Admin\Api\UploadController;
use App\Http\Controllers\Admin\UiKitController;
use Illuminate\Support\Facades\Route;

// Options search endpoint (no DB transaction lock during search)
Route::get('api/options/{resource}', OptionsController::class)
    ->middleware('throttle:60,1')
    ->name('api.options');

// FilePond upload endpoints (no DB transaction — holds lock during file I/O)
Route::post('api/upload/process', [UploadController::class, 'process'])
    ->middleware('throttle:20,1')
    ->name('api.upload.process');

Route::delete('api/upload/revert', [UploadController::class, 'revert'])
    ->name('api.upload.revert');

// UI kit demo — local environment only
if (app()->environment('local')) {
    Route::get('ui-kit', [UiKitController::class, 'index'])->name('ui-kit');
}
