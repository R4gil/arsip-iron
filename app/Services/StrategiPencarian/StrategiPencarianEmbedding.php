<?php

namespace App\Services\StrategiPencarian;

use App\Services\LayananEmbedding;
use Illuminate\Support\Facades\DB;

class StrategiPencarianEmbedding implements StrategiPencarianInterface
{
    protected LayananEmbedding $layananEmbedding;

    public function __construct(LayananEmbedding $layananEmbedding)
    {
        $this->layananEmbedding = $layananEmbedding;
    }

    /**
     * Cari arsip berdasarkan embedding vector similarity
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

        // Jika search strategy adalah keyword, langsung fallback tanpa cek embedding
        $searchStrategy = config('ai.search_strategy', 'keyword');
        if ($searchStrategy === 'keyword') {
            \Log::info('Search strategy adalah keyword, skip embedding search');
            return (new \App\Services\StrategiPencarian\StrategiPencarianKataKunci())->cari($query, $options);
        }

        // Cek ketersediaan embedding service
        if (!$this->layananEmbedding->cekKetersediaan()) {
            \Log::warning('Embedding service tidak tersedia, fallback ke keyword search');
            // Fallback ke keyword search
            return (new \App\Services\StrategiPencarian\StrategiPencarianKataKunci())->cari($query, $options);
        }

        try {
            // Generate embedding untuk query
            $queryEmbedding = $this->layananEmbedding->generateEmbedding($query);
            
            if (!$queryEmbedding) {
                \Log::warning('Gagal generate embedding untuk query');
                return collect();
            }

            // Ambil semua embeddings dari database
            $embeddings = DB::table('arsip_embeddings')
                ->select('arsip_id', 'embedding', 'model', 'dimension')
                ->where('model', $this->layananEmbedding->dapatkanModel())
                ->get();

            if ($embeddings->isEmpty()) {
                \Log::info('Tidak ada embeddings di database, fallback ke keyword search');
                // Fallback ke keyword search
                return (new \App\Services\StrategiPencarian\StrategiPencarianKataKunci())->cari($query, $options);
            }

            // Hitung similarity untuk setiap embedding
            $results = [];
            foreach ($embeddings as $embedding) {
                $embeddingVector = json_decode($embedding->embedding, true);
                
                if (!is_array($embeddingVector)) {
                    continue;
                }

                $similarity = $this->layananEmbedding->cosineSimilarity($queryEmbedding, $embeddingVector);
                
                // Filter hanya yang memiliki similarity > 0.3 (threshold)
                if ($similarity > 0.3) {
                    $results[] = [
                        'arsip_id' => $embedding->arsip_id,
                        'similarity' => $similarity,
                    ];
                }
            }

            // Urutkan berdasarkan similarity descending
            usort($results, function ($a, $b) {
                return $b['similarity'] <=> $a['similarity'];
            });

            // Ambil detail arsip berdasarkan ID
            if (empty($results)) {
                return collect();
            }

            $arsipIds = array_column($results, 'arsip_id');
            
            $arsipDetails = DB::table('arsip')
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
                    'klasifikasi.nama',
                    'lokasi_simpan.ruangan',
                    'lemari.lemari_nama',
                    'rak.rak_nama'
                )
                ->whereIn('arsip.id', $arsipIds)
                ->get();

            // Gabungkan dengan similarity score
            $similarityMap = [];
            foreach ($results as $result) {
                $similarityMap[$result['arsip_id']] = $result['similarity'];
            }

            $finalResults = $arsipDetails->map(function ($arsip) use ($similarityMap) {
                $arsip->relevansi_score = $similarityMap[$arsip->id] ?? 0;
                return $arsip;
            });

            // Urutkan berdasarkan similarity score
            return $finalResults->sortByDesc('relevansi_score')->values();
        } catch (\Exception $e) {
            \Log::error('Gagal mencari arsip dengan embedding: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Dapatkan nama strategi
     *
     * @return string
     */
    public function dapatkanNama(): string
    {
        return 'embedding';
    }

    /**
     * Cek apakah strategi tersedia
     *
     * @return bool
     */
    public function cekKetersediaan(): bool
    {
        return $this->layananEmbedding->cekKetersediaan();
    }
}
