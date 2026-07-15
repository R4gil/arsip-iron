<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\AutentikasiController;
use App\Http\Controllers\DasborController;
use App\Http\Controllers\KlasifikasiController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\LemariController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\RakController;
use App\Http\Controllers\RetensiController;
use Illuminate\Support\Facades\Route;

// ==========================================
// ROUTE GUEST (BELUM LOGIN)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AutentikasiController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AutentikasiController::class, 'login'])->name('login.perform');
});

// ==========================================
// ROUTE AUTH
// ==========================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AutentikasiController::class, 'logout'])->name('logout');
    Route::get('/', [DasborController::class, 'index'])->name('dashboard');
    Route::post('/profile/update', [AutentikasiController::class, 'updateProfile'])->name('profile.update');

    // 1. Route Arsip (Disatukan dalam resource agar store/update/destroy bekerja otomatis)
    Route::resource('arsip', ArsipController::class);
    Route::post('/arsip/retensi/ajukan', [ArsipController::class, 'ajukanRetensi'])->name('arsip.ajukanRetensi');
    Route::post('/arsip/retensi/selesai', [ArsipController::class, 'selesaiRetensi'])->name('arsip.selesaiRetensi');
    Route::post('/arsip/retensi/batal', [ArsipController::class, 'batalRetensi'])->name('arsip.batalRetensi');
    Route::post('/arsip/bulk-delete', [ArsipController::class, 'bulkDelete'])->name('arsip.bulkDelete');
    Route::get('/arsip/export/excel', [ArsipController::class, 'exportExcel'])->name('arsip.exportExcel');
    Route::get('/arsip/export/pdf', [ArsipController::class, 'exportPDF'])->name('arsip.exportPDF');
    Route::get('/arsip/print', [ArsipController::class, 'print'])->name('arsip.print');
    Route::get('/arsip/{id}/file-info', [ArsipController::class, 'getFileInfo'])->name('arsip.fileInfo');
    
    // 2. Route Peminjaman & User
    Route::resource('peminjaman', PeminjamanController::class)->except(['show', 'edit']);
    Route::patch('/peminjaman/{id}/return', [PeminjamanController::class, 'return'])->name('peminjaman.return');
    Route::post('/peminjaman/clear-history', [PeminjamanController::class, 'clearHistory'])->name('peminjaman.clearHistory');
    Route::resource('pengguna', PenggunaController::class)->except(['show']);
    Route::get('/profile', [AutentikasiController::class, 'profile'])->name('profile');
    Route::get('/profile-photos/{filename}', [AutentikasiController::class, 'viewProfilePhoto'])->name('profile.photo');
    Route::get('/arsip/view/{filename}', [ArsipController::class, 'viewFile'])->name('arsip.viewFile');
    Route::get('/retensi', [RetensiController::class, 'index'])->name('retensi.index');
    Route::post('/notifikasi/dismiss', [NotifikasiController::class, 'dismiss'])->name('notifikasi.dismiss');
    Route::post('/notifikasi/dismiss-all', [NotifikasiController::class, 'dismissAll'])->name('notifikasi.dismissAll');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::post('/activity-log/clear', [ActivityLogController::class, 'clear'])->name('activity-log.clear');

    // 4. KHUSUS ROLE ADMIN
    Route::middleware('role:Admin')->group(function () {
          // Explicit route model binding untuk parameter names yang tidak match model names
          Route::bind('lokasi', function ($value) {
              return \App\Models\Location::findOrFail($value);
          });
          Route::bind('lemari', function ($value) {
              return \App\Models\Cabinet::findOrFail($value);
          });
          Route::bind('rak', function ($value) {
              return \App\Models\Rack::findOrFail($value);
          });
          Route::bind('klasifikasi', function ($value) {
              return \App\Models\Classification::findOrFail($value);
          });
          
          Route::resource('lokasi', LokasiController::class)->except(['show']);
          Route::resource('lemari', LemariController::class)->except(['show']);
          Route::resource('rak', RakController::class)->except(['show']);
          Route::post('/klasifikasi/import', [KlasifikasiController::class, 'import'])->name('klasifikasi.import');
          Route::resource('klasifikasi', KlasifikasiController::class)->except(['show']);
    });

    // 5. AJAX
    Route::get('/ajax/cabinets', [AjaxController::class, 'cabinetsByLocation'])->name('ajax.cabinets');
    Route::get('/ajax/racks', [AjaxController::class, 'racksByCabinet'])->name('ajax.racks');
    Route::get('/ajax/klasifikasi', [AjaxController::class, 'searchClassifications'])->name('ajax.klasifikasi');
});


// ==========================================
// ROUTE API DROPDOWN BERTINGKAT (DI LUAR AUTH)
// ==========================================