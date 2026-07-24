<?php

namespace App\Services\StrategiPencarian;

use Illuminate\Support\Facades\DB;

class StrategiPencarianKataKunci implements StrategiPencarianInterface
{
    /**
     * Cari arsip berdasarkan kata kunci dengan scoring relevansi
     *
     * @param string $query
     * @param array $options
     * @return \Illuminate\Support\Collection
     */
    public function cari(string $query, array $options = []): \Illuminate\Support\Collection
    {
        if (empty($query)) {
            return collect();
        }

        try {
            $queryBuilder = DB::table('arsip')
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
                    'arsip.file_arsip',
                    'arsip.tanggal_arsip',
                    'arsip.status',
                    'arsip.status_ketersediaan',
                    'klasifikasi.nama as nama_jenis',
                    'lokasi_simpan.ruangan',
                    'lemari.lemari_nama',
                    'rak.rak_nama'
                );

            $queryLower = strtolower($query);

            // Filter di database dulu (bukan full table scan)
            $queryBuilder->where(function ($q) use ($queryLower) {
                $kata = explode(' ', $queryLower);
                foreach ($kata as $k) {
                    $k = trim($k);
                    if (strlen($k) < 2) continue;
                    $q->orWhere('arsip.nama_arsip', 'like', "%{$k}%")
                      ->orWhere('arsip.nomor_surat', 'like', "%{$k}%")
                      ->orWhere('arsip.perihal_surat', 'like', "%{$k}%")
                      ->orWhere('arsip.isi_dokumen', 'like', "%{$k}%")
                      ->orWhere('klasifikasi.nama', 'like', "%{$k}%");
                }
            });

            \Log::info('[AI Pencarian] Query ke database', [
                'query_string' => $query,
                'sql' => $queryBuilder->toSql(),
            ]);

            $results = $queryBuilder->get();

            \Log::info('[AI Pencarian] Hasil dari database', ['jumlah_baris' => $results->count()]);

            // Hitung skor relevansi untuk setiap arsip
            $scoredResults = $results->map(function ($arsip) use ($queryLower) {
                $score = $this->hitungSkorRelevansi($arsip, $queryLower);
                $arsip->relevansi_score = $score;
                return $arsip;
            });

            // Semua hasil dari DB sudah relevan (sudah difilter WHERE), tapi tetap sort by score
            $relevantResults = $scoredResults->filter(fn($a) => $a->relevansi_score > 0);

            // Jika scoring menghasilkan 0 semua (edge case), kembalikan semua hasil DB
            if ($relevantResults->isEmpty() && $results->isNotEmpty()) {
                $relevantResults = $scoredResults->map(function ($a) { $a->relevansi_score = 1; return $a; });
            }

            \Log::info('[AI Pencarian] Setelah scoring', ['jumlah_relevan' => $relevantResults->count()]);

            return $relevantResults->sortByDesc('relevansi_score')->values();
        } catch (\Exception $e) {
            \Log::error('Gagal mencari arsip dengan kata kunci: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Hitung skor relevansi untuk arsip
     *
     * @param object $arsip
     * @param string $query
     * @return float
     */
    private function hitungSkorRelevansi($arsip, string $query): float
    {
        $score = 0;
        $queryArray = explode(' ', $query);

        // Bobot untuk setiap field
        $weights = [
            'nama_arsip' => 3.0,
            'nomor_surat' => 2.5,
            'perihal_surat' => 2.0,
            'isi_dokumen' => 1.0,
            'nama_jenis' => 1.5,
        ];

        foreach ($queryArray as $kata) {
            if (empty($kata)) continue;

            $kata = trim($kata);

            // Cek di nama arsip
            if (stripos($arsip->nama_arsip ?? '', $kata) !== false) {
                $score += $weights['nama_arsip'];
                // Bonus untuk exact match
                if (strtolower($arsip->nama_arsip) === $kata) {
                    $score += $weights['nama_arsip'] * 0.5;
                }
            }

            // Cek di nomor surat
            if (stripos($arsip->nomor_surat ?? '', $kata) !== false) {
                $score += $weights['nomor_surat'];
            }

            // Cek di perihal
            if (stripos($arsip->perihal_surat ?? '', $kata) !== false) {
                $score += $weights['perihal_surat'];
            }

            // Cek di isi dokumen (hitung frekuensi)
            if (stripos($arsip->isi_dokumen ?? '', $kata) !== false) {
                $count = substr_count(strtolower($arsip->isi_dokumen), $kata);
                $score += $weights['isi_dokumen'] * min($count, 5); // Max 5x weight
            }

            // Cek di jenis arsip
            if (stripos($arsip->nama_jenis ?? '', $kata) !== false) {
                $score += $weights['nama_jenis'];
            }
        }

        return round($score, 2);
    }

    /**
     * Dapatkan nama strategi
     *
     * @return string
     */
    public function dapatkanNama(): string
    {
        return 'keyword';
    }

    /**
     * Cek apakah strategi tersedia
     *
     * @return bool
     */
    public function cekKetersediaan(): bool
    {
        return true; // Keyword search selalu tersedia
    }
}
