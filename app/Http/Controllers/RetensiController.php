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
            'Masuk Masa Retensi' => 'masuk_retensi',
            'Proses Retensi' => 'proses_retensi',
            'Selesai Retensi' => 'selesai_retensi',
        ];
        $filterQuery = $filterMap[$filter] ?? $filter;

        if (!$retensiTersedia) {
            return view('retensi.daftar', [
                'archives' => new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 15, 1),
                'filter' => $filter,
                'retensiTersedia' => false,
            ]);
        }

        try {
            $query = DB::table('arsip')
                ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
                ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
                ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
                ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
                ->select(
                    'arsip.*',
                    'klasifikasi.nama as nama_jenis',
                    'lokasi_simpan.ruangan as ruangan',
                    'lemari.lemari_nama as lemari_nama',
                    'rak.rak_nama as rak_nama'
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
                        ->orWhere('klasifikasi.nama', 'like', "%{$search}%");
                });
            }

            // Filter berdasarkan status_retensi dari database
            if ($filterQuery === 'masuk_retensi') {
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
                'archives' => new \Illuminate\Pagination\LengthAwarePaginator(collect(), 0, 15, 1),
                'filter' => $filter,
                'retensiTersedia' => false,
            ]);
        }
    }

    public function exportExcel(Request $request)
    {
        $retensiTersedia = RetensiService::kolomRetensiTersedia();

        if (!$retensiTersedia) {
            return redirect()->route('retensi.index')->with('error', 'Kolom retensi tidak tersedia.');
        }

        $filter = $request->get('status_retensi', 'semua');

        // Map filter display values to DB query values
        $filterMap = [
            'Masuk Masa Retensi' => 'masuk_retensi',
            'Proses Retensi' => 'proses_retensi',
            'Selesai Retensi' => 'selesai_retensi',
        ];
        $filterQuery = $filterMap[$filter] ?? $filter;

            $query = DB::table('arsip')
                ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
                ->select(
                    'arsip.*',
                    'klasifikasi.nama as nama_jenis'
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
                    ->orWhere('klasifikasi.nama', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status_retensi dari database
        if ($filterQuery === 'masuk_retensi') {
            $query->where('arsip.status_retensi', 'Masuk Masa Retensi');
        } elseif ($filterQuery === 'proses_retensi') {
            $query->where('arsip.status_retensi', 'Proses Retensi');
        } elseif ($filterQuery === 'selesai_retensi') {
            $query->where('arsip.status_retensi', 'Sudah Retensi');
        }

        $archives = $query->latest('arsip.id')->get();

        // Recalculate status_retensi
        $archives->transform(function ($archive) {
            if (in_array($archive->status_retensi, ['Proses Retensi', 'Sudah Retensi'])) {
                return $archive;
            }
            $archive->status_retensi = RetensiService::statusRetensi($archive->masa_retensi, $archive->tanggal_retensi);
            return $archive;
        });

        $filename = 'retensi_arsip_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, [
            'Nomor Surat',
            'Tanggal Arsip',
            'Nama Arsip',
            'Jenis',
            'Tanggal Retensi',
            'Masa Retensi',
            'Status Retensi'
        ]);

        // Data
        foreach ($archives as $archive) {
            $sr = $archive->status_retensi;
            $displayStatus = $sr;
            if ($sr === 'Sudah Retensi') $displayStatus = 'Selesai Retensi';

            fputcsv($handle, [
                $archive->nomor_surat ?? '—',
                $archive->tanggal_arsip ? Carbon::parse($archive->tanggal_arsip)->format('d-m-Y') : '—',
                $archive->nama_arsip ?? '—',
                $archive->nama_jenis ?? '—',
                $archive->tanggal_retensi ? Carbon::parse($archive->tanggal_retensi)->format('d-m-Y') : '—',
                $archive->masa_retensi ?? '—',
                $displayStatus ?? '—'
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportPDF(Request $request)
    {
        // For PDF export, redirect to index with print parameter
        return redirect()->route('retensi.index', $request->all());
    }
}
