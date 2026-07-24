<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatistikService
{
    /**
     * Hitung jumlah arsip berdasarkan kata kunci dan filter
     *
     * @param array $kataKunci
     * @param array $filter
     * @return int
     */
    public function hitungJumlahArsip(array $kataKunci, array $filter = []): int
    {
        try {
            $query = DB::table('arsip')
                ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id');

            // Filter dengan multi-kata kunci di multi-kolom
            if (!empty($kataKunci)) {
                $query->where(function ($q) use ($kataKunci) {
                    foreach ($kataKunci as $kata) {
                        $q->orWhere('arsip.nomor_surat', 'like', "%{$kata}%")
                          ->orWhere('arsip.nama_arsip', 'like', "%{$kata}%")
                          ->orWhere('arsip.perihal_surat', 'like', "%{$kata}%")
                          ->orWhere('arsip.isi_dokumen', 'like', "%{$kata}%")
                          ->orWhere('klasifikasi.nama', 'like', "%{$kata}%");
                    }
                });
            }

            // Filter tambahan
            if (!empty($filter['jenis_arsip_id'])) {
                $query->where('arsip.jenis_arsip_id', $filter['jenis_arsip_id']);
            }

            if (!empty($filter['lokasi_id'])) {
                $query->where('arsip.lokasi_id', $filter['lokasi_id']);
            }

            if (!empty($filter['status'])) {
                $query->where('arsip.status', $filter['status']);
            }

            if (!empty($filter['tahun'])) {
                $query->whereYear('arsip.tanggal_arsip', $filter['tahun']);
            }

            if (!empty($filter['bulan'])) {
                $query->whereMonth('arsip.tanggal_arsip', $filter['bulan']);
            }

            $count = $query->count();
            Log::info('StatistikService: hitungJumlahArsip', ['count' => $count, 'kata_kunci' => $kataKunci, 'filter' => $filter]);
            
            return $count;
        } catch (\Exception $e) {
            Log::error('StatistikService: Gagal hitungJumlahArsip', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Hitung statistik lengkap untuk arsip
     *
     * @param array $kataKunci
     * @param array $filter
     * @return array
     */
    public function hitungStatistikLengkap(array $kataKunci, array $filter = []): array
    {
        try {
            $query = DB::table('arsip')
                ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id');

            // Filter dengan multi-kata kunci di multi-kolom
            if (!empty($kataKunci)) {
                $query->where(function ($q) use ($kataKunci) {
                    foreach ($kataKunci as $kata) {
                        $q->orWhere('arsip.nomor_surat', 'like', "%{$kata}%")
                          ->orWhere('arsip.nama_arsip', 'like', "%{$kata}%")
                          ->orWhere('arsip.perihal_surat', 'like', "%{$kata}%")
                          ->orWhere('arsip.isi_dokumen', 'like', "%{$kata}%")
                          ->orWhere('klasifikasi.nama', 'like', "%{$kata}%");
                    }
                });
            }

            // Filter tambahan
            if (!empty($filter['jenis_arsip_id'])) {
                $query->where('arsip.jenis_arsip_id', $filter['jenis_arsip_id']);
            }

            if (!empty($filter['lokasi_id'])) {
                $query->where('arsip.lokasi_id', $filter['lokasi_id']);
            }

            if (!empty($filter['status'])) {
                $query->where('arsip.status', $filter['status']);
            }

            if (!empty($filter['tahun'])) {
                $query->whereYear('arsip.tanggal_arsip', $filter['tahun']);
            }

            if (!empty($filter['bulan'])) {
                $query->whereMonth('arsip.tanggal_arsip', $filter['bulan']);
            }

            // Hitung berbagai statistik
            $total = (clone $query)->count();
            $tersedia = (clone $query)->where('arsip.status_ketersediaan', 'Tersedia')->count();
            $dipinjam = (clone $query)->where('arsip.status_ketersediaan', 'Dipinjam')->count();
            $aktif = (clone $query)->where('arsip.status', 'Aktif')->count();
            $inaktif = (clone $query)->where('arsip.status', 'Inaktif')->count();

            $statistik = [
                'Total Arsip' => $total,
                'Arsip Tersedia' => $tersedia,
                'Arsip Dipinjam' => $dipinjam,
                'Arsip Aktif' => $aktif,
                'Arsip Inaktif' => $inaktif,
            ];

            Log::info('StatistikService: hitungStatistikLengkap', ['statistik' => $statistik, 'kata_kunci' => $kataKunci, 'filter' => $filter]);
            
            return $statistik;
        } catch (\Exception $e) {
            Log::error('StatistikService: Gagal hitungStatistikLengkap', ['error' => $e->getMessage()]);
            return [
                'Total Arsip' => 0,
                'Arsip Tersedia' => 0,
                'Arsip Dipinjam' => 0,
                'Arsip Aktif' => 0,
                'Arsip Inaktif' => 0,
            ];
        }
    }

    /**
     * Hitung statistik berdasarkan kategori (GROUP BY)
     *
     * @param string $kolom Kolom untuk grouping (jenis_arsip_id, status, dll)
     * @param array $kataKunci
     * @param array $filter
     * @return array
     */
    public function hitungStatistikPerKategori(string $kolom, array $kataKunci = [], array $filter = []): array
    {
        try {
            $query = DB::table('arsip')
                ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id');

            // Filter dengan multi-kata kunci di multi-kolom
            if (!empty($kataKunci)) {
                $query->where(function ($q) use ($kataKunci) {
                    foreach ($kataKunci as $kata) {
                        $q->orWhere('arsip.nomor_surat', 'like', "%{$kata}%")
                          ->orWhere('arsip.nama_arsip', 'like', "%{$kata}%")
                          ->orWhere('arsip.perihal_surat', 'like', "%{$kata}%")
                          ->orWhere('arsip.isi_dokumen', 'like', "%{$kata}%")
                          ->orWhere('klasifikasi.nama', 'like', "%{$kata}%");
                    }
                });
            }

            // Filter tambahan
            if (!empty($filter['jenis_arsip_id'])) {
                $query->where('arsip.jenis_arsip_id', $filter['jenis_arsip_id']);
            }

            if (!empty($filter['lokasi_id'])) {
                $query->where('arsip.lokasi_id', $filter['lokasi_id']);
            }

            if (!empty($filter['status'])) {
                $query->where('arsip.status', $filter['status']);
            }

            if (!empty($filter['tahun'])) {
                $query->whereYear('arsip.tanggal_arsip', $filter['tahun']);
            }

            // Group by kolom yang diminta
            $results = $query->select($kolom, DB::raw('COUNT(*) as jumlah'))
                ->groupBy($kolom)
                ->get()
                ->toArray();

            Log::info('StatistikService: hitungStatistikPerKategori', ['kolom' => $kolom, 'results' => $results]);
            
            return $results;
        } catch (\Exception $e) {
            Log::error('StatistikService: Gagal hitungStatistikPerKategori', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Ekstrak tipe statistik dari pertanyaan (COUNT, SUM, MAX, MIN, AVG)
     *
     * @param string $pertanyaan
     * @return string
     */
    public function ekstrakTipeStatistik(string $pertanyaan): string
    {
        $pertanyaanLower = strtolower($pertanyaan);
        
        if (strpos($pertanyaanLower, 'terbanyak') !== false || strpos($pertanyaanLower, 'terbesar') !== false) {
            return 'MAX';
        }
        
        if (strpos($pertanyaanLower, 'terkecil') !== false || strpos($pertanyaanLower, 'paling sedikit') !== false) {
            return 'MIN';
        }
        
        if (strpos($pertanyaanLower, 'rata-rata') !== false || strpos($pertanyaanLower, 'average') !== false) {
            return 'AVG';
        }
        
        if (strpos($pertanyaanLower, 'total') !== false || strpos($pertanyaanLower, 'sum') !== false) {
            return 'SUM';
        }
        
        // Default: COUNT
        return 'COUNT';
    }
}
