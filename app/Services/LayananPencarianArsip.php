<?php

namespace App\Services;

use App\Services\StrategiPencarian\StrategiPencarianInterface;
use App\Services\StrategiPencarian\StrategiPencarianKataKunci;
use App\Services\LayananEmbedding;
use Illuminate\Support\Facades\DB;

class LayananPencarianArsip
{
    protected StrategiPencarianInterface $strategiPencarian;

    public function __construct()
    {
        // Gunakan strategi berdasarkan konfigurasi
        $strategi = config('ai.search_strategy', 'keyword');
        $layananEmbedding = app(LayananEmbedding::class);
        
        $this->strategiPencarian = match ($strategi) {
            'embedding' => new \App\Services\StrategiPencarian\StrategiPencarianEmbedding($layananEmbedding),
            default => new StrategiPencarianKataKunci(),
        };
    }

    /**
     * Cari arsip berdasarkan kata kunci dengan scoring relevansi
     *
     * @param string $kataKunci Kata kunci pencarian
     * @return \Illuminate\Support\Collection
     */
    public function cariArsip(string $kataKunci): \Illuminate\Support\Collection
    {
        return $this->strategiPencarian->cari($kataKunci);
    }

    /**
     * Cari arsip berdasarkan array kata kunci (multi-kolom search)
     *
     * @param array $kataKunci Array kata kunci pencarian
     * @param array $filter Filter tambahan (opsional)
     * @param int $limit Batas hasil
     * @return \Illuminate\Support\Collection
     */
    public function cariArsipMultiKunci(array $kataKunci, array $filter = [], int $limit = 10): \Illuminate\Support\Collection
    {
        try {
            $query = DB::table('arsip')
                ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
                ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
                ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
                ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
                ->select(
                    'arsip.id',
                    'arsip.nomor_surat',
                    'arsip.nama_arsip',
                    'arsip.perihal_surat',
                    'arsip.isi_dokumen',
                    'arsip.tanggal_arsip',
                    'arsip.status',
                    'arsip.status_ketersediaan',
                    'arsip.file_arsip',
                    'klasifikasi.nama as nama_jenis',
                    'lokasi_simpan.ruangan',
                    'lemari.lemari_nama',
                    'rak.rak_nama'
                );

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

            return $query->limit($limit)->orderBy('arsip.created_at', 'desc')->get();
        } catch (\Exception $e) {
            \Log::error('Gagal mencari arsip multi-kunci: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Hitung jumlah arsip berdasarkan kata kunci (untuk statistik)
     *
     * @param array $kataKunci Array kata kunci pencarian
     * @param array $filter Filter tambahan (opsional)
     * @return int Jumlah arsip yang ditemukan
     */
    public function hitungArsipMultiKunci(array $kataKunci, array $filter = []): int
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

            return $query->count();
        } catch (\Exception $e) {
            \Log::error('Gagal menghitung arsip multi-kunci: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Ganti strategi pencarian
     *
     * @param string $strategi
     * @return void
     */
    public function gantiStrategi(string $strategi): void
    {
        $layananEmbedding = app(LayananEmbedding::class);
        
        $this->strategiPencarian = match ($strategi) {
            'embedding' => new \App\Services\StrategiPencarian\StrategiPencarianEmbedding($layananEmbedding),
            default => new StrategiPencarianKataKunci(),
        };
    }

    /**
     * Dapatkan strategi yang sedang digunakan
     *
     * @return string
     */
    public function dapatkanStrategi(): string
    {
        return $this->strategiPencarian->dapatkanNama();
    }

    /**
     * Cari arsip dengan filter tambahan
     *
     * @param array $filter Filter pencarian
     * @param int $limit Batas hasil
     * @return \Illuminate\Support\Collection
     */
    public function cariArsipDenganFilter(array $filter, int $limit = 10): \Illuminate\Support\Collection
    {
        try {
            $query = DB::table('arsip')
                ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
                ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
                ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
                ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
                ->select(
                    'arsip.id',
                    'arsip.nomor_surat',
                    'arsip.nama_arsip',
                    'arsip.perihal_surat',
                    'arsip.isi_dokumen',
                    'arsip.tanggal_arsip',
                    'arsip.status',
                    'arsip.status_ketersediaan',
                    'klasifikasi.nama',
                    'lokasi_simpan.ruangan',
                    'lemari.lemari_nama',
                    'rak.rak_nama'
                );

            // Filter kata kunci
            if (!empty($filter['kata_kunci'])) {
                $kataKunci = $filter['kata_kunci'];
                $query->where(function ($q) use ($kataKunci) {
                    $q->where('arsip.nama_arsip', 'like', "%{$kataKunci}%")
                      ->orWhere('arsip.nomor_surat', 'like', "%{$kataKunci}%")
                      ->orWhere('arsip.perihal_surat', 'like', "%{$kataKunci}%")
                      ->orWhere('arsip.isi_dokumen', 'like', "%{$kataKunci}%");
                });
            }

            // Filter jenis arsip
            if (!empty($filter['jenis_arsip_id'])) {
                $query->where('arsip.jenis_arsip_id', $filter['jenis_arsip_id']);
            }

            // Filter lokasi
            if (!empty($filter['lokasi_id'])) {
                $query->where('arsip.lokasi_id', $filter['lokasi_id']);
            }

            // Filter status
            if (!empty($filter['status'])) {
                $query->where('arsip.status', $filter['status']);
            }

            // Filter tanggal
            if (!empty($filter['tanggal_mulai'])) {
                $query->whereDate('arsip.tanggal_arsip', '>=', $filter['tanggal_mulai']);
            }
            if (!empty($filter['tanggal_selesai'])) {
                $query->whereDate('arsip.tanggal_arsip', '<=', $filter['tanggal_selesai']);
            }

            return $query->limit($limit)->orderBy('arsip.created_at', 'desc')->get();
        } catch (\Exception $e) {
            \Log::error('Gagal mencari arsip dengan filter: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Hitung relevansi kata kunci dalam teks
     *
     * @param string $teks
     * @param string $kataKunci
     * @return int Score relevansi
     */
    public function hitungRelevansi(string $teks, string $kataKunci): int
    {
        if (empty($teks) || empty($kataKunci)) {
            return 0;
        }

        $teksLower = strtolower($teks);
        $kataKunciLower = strtolower($kataKunci);
        
        // Hitung jumlah kemunculan kata kunci
        $count = substr_count($teksLower, $kataKunciLower);
        
        // Beri bobot berdasarkan posisi (judul lebih penting)
        return $count;
    }

    /**
     * Ambil excerpt dari teks dengan highlight kata kunci
     *
     * @param string $teks
     * @param string $kataKunci
     * @param int $panjang
     * @return string
     */
    public function ambilExcerptDenganHighlight(string $teks, string $kataKunci, int $panjang = 300): string
    {
        if (empty($teks)) {
            return '';
        }

        $teksLower = strtolower($teks);
        $kataKunciLower = strtolower($kataKunci);
        $posisi = strpos($teksLower, $kataKunciLower);

        if ($posisi === false) {
            return substr($teks, 0, $panjang) . '...';
        }

        // Ambil teks di sekitar kata kunci
        $mulai = max(0, $posisi - 100);
        $excerpt = substr($teks, $mulai, $panjang);

        // Tambah ellipsis jika perlu
        if ($mulai > 0) {
            $excerpt = '...' . $excerpt;
        }
        if (strlen($teks) > $mulai + $panjang) {
            $excerpt = $excerpt . '...';
        }

        // Highlight kata kunci
        $excerpt = str_ireplace($kataKunci, "<strong>{$kataKunci}</strong>", $excerpt);

        return $excerpt;
    }

    /**
     * Dapatkan statistik pencarian
     *
     * @return array
     */
    public function dapatkanStatistikPencarian(): array
    {
        try {
            $totalArsip = DB::table('arsip')->count();
            $arsipDenganIsi = DB::table('arsip')
                ->whereNotNull('isi_dokumen')
                ->where('isi_dokumen', '!=', '')
                ->count();

            return [
                'total_arsip' => $totalArsip,
                'arsip_dengan_isi' => $arsipDenganIsi,
                'arsip_tanpa_isi' => $totalArsip - $arsipDenganIsi,
                'persentase_dengan_isi' => $totalArsip > 0 
                    ? round(($arsipDenganIsi / $totalArsip) * 100, 2) 
                    : 0,
            ];
        } catch (\Exception $e) {
            \Log::error('Gagal mendapatkan statistik pencarian: ' . $e->getMessage());
            return [
                'total_arsip' => 0,
                'arsip_dengan_isi' => 0,
                'arsip_tanpa_isi' => 0,
                'persentase_dengan_isi' => 0,
            ];
        }
    }
}
