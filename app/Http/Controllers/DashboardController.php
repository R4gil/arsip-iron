<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Borrowing;
use App\Models\Location;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
    $totalArchives = \DB::table('arsip')->count();
    $activeArchives = \DB::table('arsip')->where('status', 'tersedia')->count();
    $inactiveArchives = \DB::table('arsip')->where('status', 'inaktif')->count();
    $borrowedArchives = \DB::table('arsip')->where('status', 'dipinjam')->count();
    $returnedArchives = \DB::table('peminjaman_arsip')->where('status_pinjam', 'dikembalikan')->count();
    $locationsCount = \DB::table('lokasi_simpan')->count();

$archivesPerLocation = \DB::table('arsip') // <-- Diganti dari Archive:: menjadi \DB::table('arsip')
            ->selectRaw("CONCAT('R.', lokasi_simpan.ruangan, ' - L.', lokasi_simpan.lemari, ' - Rak ', lokasi_simpan.rak) as nama_lokasi, count(arsip.id) as total")
            ->join('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->groupBy('lokasi_simpan.ruangan', 'lokasi_simpan.lemari', 'lokasi_simpan.rak')
            ->orderByDesc('total')
            ->get();

        $archivesPerYear = \DB::table('arsip')->selectRaw('tahun, count(id) as total')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        $borrowingsMonthly = \DB::table('peminjaman_arsip')->selectRaw('MONTH(tanggal_keluar) as month, count(id) as total')
            ->whereYear('tanggal_keluar', now()->year)
            ->groupByRaw('MONTH(tanggal_keluar)')
            ->orderBy('month')
            ->get();

        $recentArchives = \DB::table('arsip')->latest()->limit(5)->get();
        $recentActivities = \App\Models\ActivityLog::with('user')
            ->latest('timestamp') // <-- Sebutkan nama kolom waktunya di dalam kurung
            ->limit(6)
            ->get();
        $borrowedNow = \App\Models\Borrowing::where('status_pinjam', 'dipinjam') // <-- Ganti 'status_peminjaman' dengan nama kolom asli di phpMyAdmin kamu
            ->with('archive')
            ->latest() // <-- Ini sudah aman digunakan sekarang karena created_at ada
            ->limit(6)
            ->get();

        return view('dashboard.index', compact(
            'totalArchives',
            'activeArchives',
            'inactiveArchives',
            'borrowedArchives',
            'returnedArchives',
            'locationsCount',
            'archivesPerLocation',
            'archivesPerYear',
            'borrowingsMonthly',
            'recentArchives',
            'recentActivities',
            'borrowedNow'
        ));
    }
}
