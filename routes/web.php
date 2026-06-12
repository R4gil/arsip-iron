<?php

use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\ClassificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\CabinetController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('arsip', ArchiveController::class)->except(['index']);
    Route::get('/arsip', [ArchiveController::class, 'index'])->name('arsip.index');

    Route::resource('borrowings', BorrowingController::class);
    Route::put('/borrowings/{borrowing}/return', [BorrowingController::class, 'update'])->name('borrowings.return');

    Route::resource('users', UserController::class)->except(['show']);

    Route::middleware('role:Admin')->group(function () {
        Route::resource('locations', LocationController::class)->except(['show']);
        Route::resource('cabinets', CabinetController::class)->except(['show']);
        Route::resource('racks', RackController::class)->except(['show']);
        Route::resource('classifications', ClassificationController::class)->except(['show']);
    });

    Route::get('/ajax/cabinets', [AjaxController::class, 'cabinetsByLocation'])->name('ajax.cabinets');
    Route::get('/ajax/racks', [AjaxController::class, 'racksByCabinet'])->name('ajax.racks');
});
