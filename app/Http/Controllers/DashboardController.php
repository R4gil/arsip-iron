<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index() {
    $totalArsip = DB::table('arsip')->count();

    // Data untuk Donut Chart (Status)
    $statusData = DB::table('arsip')
        ->select('status_ketersediaan', DB::raw('count(*) as total'))
        ->groupBy('status_ketersediaan')->get();
        
    // Data untuk Bar Chart (Distribusi Lokasi)
    $lokasiData = DB::table('arsip')
        ->join('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
        ->select('lokasi_simpan.ruangan', DB::raw('count(*) as total'))
        ->groupBy('lokasi_simpan.ruangan')->get();

    // Data untuk Line Chart (Tren Bulanan)
    $arsipPerBulan = DB::table('arsip')
        ->selectRaw('MONTH(tanggal_arsip) as bulan, count(*) as total')
        ->whereYear('tanggal_arsip', date('Y'))
        ->groupBy('bulan')->orderBy('bulan')->get();

    return view('dashboard.index', compact('totalArsip', 'statusData', 'lokasiData', 'arsipPerBulan'));
    }
}
