<?php

use App\Support\Admin\LegacyAdminRedirect;
use Illuminate\Support\Facades\Route;

/*
| Legacy Filament panel lived at /admin. After cutover, permanently redirect
| familiar bookmarks to the Blade admin at /quan-tri.
*/
Route::get('/admin/{any?}', function (?string $any = null) {
    return redirect(LegacyAdminRedirect::target($any), 301);
})->where('any', '.*')->name('admin.legacy.redirect');
