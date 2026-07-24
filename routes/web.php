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
use App\Http\Controllers\AIArsipController;
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
    Route::get('/profile', [AutentikasiController::class, 'profile'])->name('profile');
    Route::get('/profile-photos/{filename}', [AutentikasiController::class, 'viewProfilePhoto'])->name('profile.photo');

    // ==========================================
    // AKSES SEMUA ROLE: Dashboard + Arsip (untuk pengguna, petugas, admin)
    // ==========================================
    // Static routes HARUS sebelum resource agar tidak di-intercept oleh route show
    Route::get('/arsip/export/excel', [ArsipController::class, 'exportExcel'])->name('arsip.exportExcel');
    Route::get('/arsip/export/pdf', [ArsipController::class, 'exportPDF'])->name('arsip.exportPDF');
    Route::get('/arsip/print', [ArsipController::class, 'print'])->name('arsip.print');
    Route::get('/arsip/view/{filename}', [ArsipController::class, 'viewFile'])->name('arsip.viewFile');
    Route::post('/arsip/retensi/ajukan', [ArsipController::class, 'ajukanRetensi'])->name('arsip.ajukanRetensi');
    Route::post('/arsip/retensi/selesai', [ArsipController::class, 'selesaiRetensi'])->name('arsip.selesaiRetensi');
    Route::post('/arsip/retensi/batal', [ArsipController::class, 'batalRetensi'])->name('arsip.batalRetensi');
    Route::post('/arsip/bulk-delete', [ArsipController::class, 'bulkDelete'])->name('arsip.bulkDelete');
    Route::get('/arsip/{id}/file-info', [ArsipController::class, 'getFileInfo'])->name('arsip.fileInfo');
    Route::resource('arsip', ArsipController::class);
    Route::get('/retensi', [RetensiController::class, 'index'])->name('retensi.index');
    Route::get('/retensi/export/excel', [RetensiController::class, 'exportExcel'])->name('retensi.exportExcel');
    Route::get('/retensi/export/pdf', [RetensiController::class, 'exportPDF'])->name('retensi.exportPDF');
    Route::post('/notifikasi/dismiss', [NotifikasiController::class, 'dismiss'])->name('notifikasi.dismiss');
    Route::post('/notifikasi/dismiss-all', [NotifikasiController::class, 'dismissAll'])->name('notifikasi.dismissAll');

    // ==========================================
    // AI ARSIP (AKSES SEMUA ROLE)
    // ==========================================
    Route::prefix('ai-arsip')->group(function () {
        Route::get('/', [AIArsipController::class, 'index'])->name('ai-arsip.index');
        Route::post('/tanya', [AIArsipController::class, 'tanyaAI'])->name('ai-arsip.tanya');
        Route::post('/export-excel', [AIArsipController::class, 'exportExcel'])->name('ai-arsip.exportExcel');
        Route::post('/export-jawaban', [AIArsipController::class, 'exportJawabanExcel'])->name('ai-arsip.exportJawaban');
        Route::get('/jenis-arsip', [AIArsipController::class, 'getJenisArsip'])->name('ai-arsip.getJenisArsip');
        Route::get('/lokasi', [AIArsipController::class, 'getLokasi'])->name('ai-arsip.getLokasi');
    });

    // ==========================================
    // AKSES PETUGAS & ADMIN: Peminjaman
    // ==========================================
    Route::middleware('role:Petugas,Admin')->group(function () {
        Route::resource('peminjaman', PeminjamanController::class)->except(['show', 'edit']);
        Route::patch('/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
        Route::post('/peminjaman/clear-history', [PeminjamanController::class, 'clearHistory'])->name('peminjaman.clearHistory');
        Route::get('/peminjaman/export/excel', [PeminjamanController::class, 'exportExcel'])->name('peminjaman.exportExcel');
        Route::get('/peminjaman/export/pdf', [PeminjamanController::class, 'exportPDF'])->name('peminjaman.exportPDF');
    });

    // ==========================================
    // KHUSUS ADMIN: Manajemen Pengguna + Log Aktivitas + Pengaturan Arsip
    // ==========================================
    Route::middleware('role:Admin')->group(function () {
        // Manajemen Pengguna + Log Aktivitas (satu kesatuan)
        Route::resource('pengguna', PenggunaController::class)->except(['show']);
        Route::get('/pengguna/export/excel', [PenggunaController::class, 'exportExcel'])->name('pengguna.exportExcel');
        Route::get('/pengguna/export/pdf', [PenggunaController::class, 'exportPDF'])->name('pengguna.exportPDF');
        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::post('/activity-log/clear', [ActivityLogController::class, 'clear'])->name('activity-log.clear');
        Route::get('/activity-log/export/excel', [ActivityLogController::class, 'exportExcel'])->name('activity-log.exportExcel');
        Route::get('/activity-log/export/pdf', [ActivityLogController::class, 'exportPDF'])->name('activity-log.exportPDF');
        
        // Pengaturan Arsip (Lokasi, Lemari, Rak, Klasifikasi)
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
          Route::get('/lokasi/export/excel', [LokasiController::class, 'exportExcel'])->name('lokasi.exportExcel');
          Route::get('/lokasi/export/pdf', [LokasiController::class, 'exportPDF'])->name('lokasi.exportPDF');
          Route::resource('lemari', LemariController::class)->except(['show']);
          Route::get('/lemari/export/excel', [LemariController::class, 'exportExcel'])->name('lemari.exportExcel');
          Route::get('/lemari/export/pdf', [LemariController::class, 'exportPDF'])->name('lemari.exportPDF');
          Route::resource('rak', RakController::class)->except(['show']);
          Route::get('/rak/export/excel', [RakController::class, 'exportExcel'])->name('rak.exportExcel');
          Route::get('/rak/export/pdf', [RakController::class, 'exportPDF'])->name('rak.exportPDF');
          Route::post('/klasifikasi/import', [KlasifikasiController::class, 'import'])->name('klasifikasi.import');
          Route::resource('klasifikasi', KlasifikasiController::class)->except(['show']);
          Route::get('/klasifikasi/export/excel', [KlasifikasiController::class, 'exportExcel'])->name('klasifikasi.exportExcel');
          Route::get('/klasifikasi/export/pdf', [KlasifikasiController::class, 'exportPDF'])->name('klasifikasi.exportPDF');
    });

    // 5. AJAX
    Route::get('/ajax/cabinets', [AjaxController::class, 'cabinetsByLocation'])->name('ajax.cabinets');
    Route::get('/ajax/racks', [AjaxController::class, 'racksByCabinet'])->name('ajax.racks');
    Route::get('/ajax/klasifikasi', [AjaxController::class, 'searchClassifications'])->name('ajax.klasifikasi');
});


// ==========================================
// ROUTE API DROPDOWN BERTINGKAT (DI LUAR AUTH)
// ==========================================