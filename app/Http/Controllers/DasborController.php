<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\RetensiService;
use Illuminate\Support\Facades\DB;

class DasborController extends Controller
{
    public function index()
    {
        try {
            $query = DB::table('arsip');

            $totalArsip = $query->count();
            $tersedia = (clone $query)->where('status_ketersediaan', 'Tersedia')->count();
            $dipinjam = (clone $query)->where('status_ketersediaan', 'Dipinjam')->count();
            $aktif = (clone $query)->where('status', 'Aktif')->count();
            $inaktif = (clone $query)->where('status', 'Inaktif')->count();
            $arsipMasukRetensi = RetensiService::countSudahRetensi();
            $arsipPermanen = RetensiService::countPermanen();

            $statusData = DB::table('arsip')
                ->select('status_ketersediaan as label', DB::raw('count(*) as total'))
                ->groupBy('status_ketersediaan')
                ->get();

            $lokasiData = DB::table('arsip')
                ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
                ->select(
                    DB::raw(Location::labelSql('lokasi_simpan') . ' as label'),
                    DB::raw('SUM(CASE WHEN arsip.status = "Aktif" THEN 1 ELSE 0 END) as aktif'),
                    DB::raw('SUM(CASE WHEN arsip.status = "Inaktif" THEN 1 ELSE 0 END) as inaktif'),
                    DB::raw('count(*) as total')
                )
                ->groupBy('lokasi_simpan.ruangan', 'lokasi_simpan.keterangan')
                ->get();

            $jenisData = DB::table('arsip')
                ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
                ->select('klasifikasi.nama as label', DB::raw('count(*) as total'))
                ->groupBy('klasifikasi.nama')
                ->get();

            $arsipPerBulan = DB::table('arsip')
                ->selectRaw('MONTH(tanggal_arsip) as bulan, count(*) as total')
                ->whereYear('tanggal_arsip', date('Y'))
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();

            $arsipTerbaru = DB::table('arsip')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            $retensiNotifBaru = RetensiService::countNotifikasiBaru();

            return view('dashboard.index', compact(
                'totalArsip',
                'tersedia',
                'dipinjam',
                'aktif',
                'inaktif',
                'arsipMasukRetensi',
                'arsipPermanen',
                'statusData',
                'lokasiData',
                'jenisData',
                'arsipPerBulan',
                'arsipTerbaru',
                'retensiNotifBaru'
            ));
        } catch (\Exception $e) {
            return view('dashboard.index', [
                'totalArsip' => 0,
                'tersedia' => 0,
                'dipinjam' => 0,
                'aktif' => 0,
                'inaktif' => 0,
                'arsipMasukRetensi' => 0,
                'arsipPermanen' => 0,
                'statusData' => collect(),
                'lokasiData' => collect(),
                'jenisData' => collect(),
                'arsipPerBulan' => collect(),
                'arsipTerbaru' => collect(),
                'retensiNotifBaru' => 0,
            ]);
        }
    }
}


