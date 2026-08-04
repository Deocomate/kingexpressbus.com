<?php

use App\Http\Controllers\Admin\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('dang-nhap', [AuthController::class, 'loginForm'])->name('login');
Route::post('dang-nhap', [AuthController::class, 'login'])->name('login.submit');
Route::post('dang-xuat', [AuthController::class, 'logout'])->name('logout');
