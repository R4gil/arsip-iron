<?php

namespace App\Http\Controllers;

use App\Services\RetensiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RetensiController extends Controller
{
    public function index(Request $request)
    {
        // Mark all retensi notifications as read when user visits this page
        RetensiService::markNotifikasiDibaca();

        $retensiTersedia = RetensiService::kolomRetensiTersedia();

        $filter = $request->get('status_retensi', 'semua');

        // Map filter display values to DB query values
        $filterMap = [
            'Belum Masuk Masa Retensi' => 'belum_retensi',
            'Masuk Masa Retensi' => 'masuk_retensi',
            'Proses Retensi' => 'proses_retensi',
            'Selesai Retensi' => 'selesai_retensi',
        ];
        $filterQuery = $filterMap[$filter] ?? $filter;

        if (!$retensiTersedia) {
            return view('retensi.daftar', [
                'archives' => \Illuminate\Pagination\LengthAwarePaginator::make(collect(), 0, 15),
                'filter' => $filter,
                'retensiTersedia' => false,
            ]);
        }

        try {
            $query = DB::table('arsip')
                ->leftJoin('jenis_arsip', 'arsip.jenis_arsip_id', '=', 'jenis_arsip.id')
                ->select(
                    'arsip.*',
                    'jenis_arsip.nama_jenis as nama_jenis'
                );

            // Default filter: hanya arsip yang sudah memasuki masa retensi
            if ($filter === 'semua' || $filterQuery === 'semua') {
                $query->whereNotNull('arsip.masa_retensi')
                    ->where('arsip.masa_retensi', '!=', 'Permanen')
                    ->whereNotNull('arsip.tanggal_retensi')
                    ->whereDate('arsip.tanggal_retensi', '<=', Carbon::today());
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('arsip.nomor_surat', 'like', "%{$search}%")
                        ->orWhere('arsip.nama_arsip', 'like', "%{$search}%")
                        ->orWhere('jenis_arsip.nama_jenis', 'like', "%{$search}%");
                });
            }

            // Filter berdasarkan status_retensi dari database
            if ($filterQuery === 'belum_retensi') {
                $query->where(function ($q) {
                    $q->whereNull('arsip.status_retensi')
                        ->orWhere('arsip.status_retensi', 'Belum Memasuki Masa Retensi');
                });
            } elseif ($filterQuery === 'masuk_retensi') {
                $query->where('arsip.status_retensi', 'Masuk Masa Retensi');
            } elseif ($filterQuery === 'proses_retensi') {
                $query->where('arsip.status_retensi', 'Proses Retensi');
            } elseif ($filterQuery === 'selesai_retensi') {
                $query->where('arsip.status_retensi', 'Sudah Retensi');
            }

            $perPage = $request->get('per_page', 15);
            $archives = $query->latest('arsip.id')->simplePaginate($perPage)->withQueryString();

            // Recalculate status_retensi but preserve Proses Retensi and Sudah Retensi
            $archives->getCollection()->transform(function ($archive) {
                // If already has explicit status (Proses Retensi or Sudah Retensi), keep it
                if (in_array($archive->status_retensi, ['Proses Retensi', 'Sudah Retensi'])) {
                    return $archive;
                }
                // Otherwise calculate from masa_retensi and tanggal_retensi
                $archive->status_retensi = RetensiService::statusRetensi($archive->masa_retensi, $archive->tanggal_retensi);
                return $archive;
            });

            return view('retensi.daftar', compact('archives', 'filter'))->with('retensiTersedia', true);
        } catch (\Exception $e) {
            return view('retensi.daftar', [
                'archives' => \Illuminate\Pagination\LengthAwarePaginator::make(collect(), 0, 15),
                'filter' => $filter,
                'retensiTersedia' => false,
            ]);
        }
    }
}
