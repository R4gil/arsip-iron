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

// ==========================================
// ROUTE GUEST (BELUM LOGIN)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
});

// ==========================================
// ROUTE AUTH
// ==========================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 1. Route Arsip (Disatukan dalam resource agar store/update/destroy bekerja otomatis)
    Route::resource('arsip', ArchiveController::class);
    
    // 2. Route Tambahan untuk Klasifikasi
    Route::get('/get-sub-klasifikasi/{parent_id}', [ArchiveController::class, 'getSubKlasifikasi'])->name('sub-klasifikasi');

    // 3. Route Peminjaman & User
    Route::resource('borrowings', BorrowingController::class);
    Route::put('/borrowings/{borrowing}/return', [BorrowingController::class, 'update'])->name('borrowings.return');
    Route::resource('users', UserController::class)->except(['show']);

    // 4. KHUSUS ROLE ADMIN
    Route::middleware('role:Admin')->group(function () {
        Route::resource('locations', LocationController::class)->except(['show']);
        Route::resource('cabinets', CabinetController::class)->except(['show']);
        Route::resource('racks', RackController::class)->except(['show']);
        Route::resource('classifications', ClassificationController::class)->except(['show']);
    });

    // 5. AJAX
    Route::get('/ajax/cabinets', [AjaxController::class, 'cabinetsByLocation'])->name('ajax.cabinets');
    Route::get('/ajax/racks', [AjaxController::class, 'racksByCabinet'])->name('ajax.racks');
});


// ==========================================
// ROUTE API DROPDOWN BERTINGKAT (DI LUAR AUTH)
// ==========================================
Route::get('/api/get-sub-klasifikasi/{parent_id}', [ArchiveController::class, 'getSubKlasifikasi'])->name('api.sub-klasifikasi');

