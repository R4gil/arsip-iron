<?php

namespace App\Providers;

use App\Models\Peminjaman;
use App\Services\NotifikasiService;
use App\Services\RetensiService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Konfigurasi Pagination Bootstrap
        Paginator::useBootstrap();

        // View Composer untuk layout dashboard
        View::composer('layouts.dashboard', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                $notifikasiList = NotifikasiService::getForUser($user);

                $view->with('retensiCount', RetensiService::countSudahRetensi());
                $view->with('retensiNotifCount', RetensiService::countNotifikasiBaru());
                $view->with('peminjamanAktifCount', Peminjaman::where('status_pinjam', 'Dipinjam')->count());
                $view->with('notifikasiList', $notifikasiList);
                $view->with('notifikasiCount', $notifikasiList->count());
            }
        });

    }
}
