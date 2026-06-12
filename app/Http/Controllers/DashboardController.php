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
        $totalArchives = Archive::count();
        $activeArchives = Archive::where('status', 'tersedia')->count();
        $inactiveArchives = Archive::where('status', 'inaktif')->count();
        $borrowedArchives = Archive::where('status', 'dipinjam')->count();
        $returnedArchives = Borrowing::where('status', 'dikembalikan')->count();
        $locationsCount = Location::count();

        $archivesPerLocation = Archive::selectRaw('locations.nama_lokasi, count(archives.id) as total')
            ->join('locations', 'archives.location_id', '=', 'locations.id')
            ->groupBy('locations.nama_lokasi')
            ->orderByDesc('total')
            ->get();

        $archivesPerYear = Archive::selectRaw('tahun, count(id) as total')
            ->groupBy('tahun')
            ->orderBy('tahun')
            ->get();

        $borrowingsMonthly = Borrowing::selectRaw('MONTH(tanggal_pinjam) as month, count(id) as total')
            ->whereYear('tanggal_pinjam', now()->year)
            ->groupByRaw('MONTH(tanggal_pinjam)')
            ->orderBy('month')
            ->get();

        $recentArchives = Archive::latest()->limit(5)->get();
        $recentActivities = ActivityLog::with('user')->latest()->limit(6)->get();
        $borrowedNow = Borrowing::where('status', 'dipinjam')->with('archive')->latest()->limit(6)->get();

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
