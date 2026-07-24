<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LayananEmbedding
{
    protected string $baseUrl;
    protected string $model;
    protected int $dimension;

    public function __construct()
    {
        $this->baseUrl = env('OLLAMA_URL', 'http://127.0.0.1:11434');
        $this->model = env('OLLAMA_EMBEDDING_MODEL', 'gemma3:4b');
        $this->dimension = env('OLLAMA_EMBEDDING_DIMENSION', 768);
    }

    /**
     * Generate embedding untuk teks menggunakan Ollama API
     *
     * @param string $text
     * @return array|null
     */
    public function generateEmbedding(string $text): ?array
    {
        if (empty($text)) {
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(60)->post("{$this->baseUrl}/api/embeddings", [
                'model' => $this->model,
                'prompt' => $text,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['embedding'] ?? null;
            }

            Log::error('Ollama embedding API error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('Exception saat generate embedding: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate embedding untuk arsip
     *
     * @param object $arsip
     * @return array|null
     */
    public function generateEmbeddingArsip($arsip): ?array
    {
        // Gabungkan informasi penting dari arsip
        $text = $this->siapkanTeksArsip($arsip);
        return $this->generateEmbedding($text);
    }

    /**
     * Siapkan teks dari arsip untuk embedding
     *
     * @param object $arsip
     * @return string
     */
    private function siapkanTeksArsip($arsip): string
    {
        $parts = [];

        if (!empty($arsip->nama_arsip)) {
            $parts[] = $arsip->nama_arsip;
        }

        if (!empty($arsip->nomor_surat)) {
            $parts[] = $arsip->nomor_surat;
        }

        if (!empty($arsip->perihal_surat)) {
            $parts[] = $arsip->perihal_surat;
        }

        if (!empty($arsip->isi_dokumen)) {
            // Batasi panjang isi dokumen untuk embedding
            $isiDokumen = substr($arsip->isi_dokumen, 0, 3000);
            $parts[] = $isiDokumen;
        }

        return implode(' ', $parts);
    }

    /**
     * Hitung cosine similarity antara dua vektor
     *
     * @param array $vector1
     * @param array $vector2
     * @return float
     */
    public function cosineSimilarity(array $vector1, array $vector2): float
    {
        if (count($vector1) !== count($vector2)) {
            return 0.0;
        }

        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        for ($i = 0; $i < count($vector1); $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $magnitude1 += $vector1[$i] * $vector1[$i];
            $magnitude2 += $vector2[$i] * $vector2[$i];
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);

        if ($magnitude1 == 0 || $magnitude2 == 0) {
            return 0.0;
        }

        return $dotProduct / ($magnitude1 * $magnitude2);
    }

    /**
     * Cek apakah embedding service tersedia
     *
     * @return bool
     */
    public function cekKetersediaan(): bool
    {
        // Jika search strategy adalah keyword, langsung return false tanpa cek
        $searchStrategy = config('ai.search_strategy', 'keyword');
        if ($searchStrategy === 'keyword') {
            return false;
        }

        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/tags");
            if (!$response->successful()) {
                return false;
            }

            // Cek apakah model mendukung embeddings
            $data = $response->json();
            if (isset($data['models'])) {
                foreach ($data['models'] as $model) {
                    if (isset($model['name']) && strpos($model['name'], $this->model) !== false) {
                        // Test embedding endpoint
                        $testResponse = Http::timeout(10)->post("{$this->baseUrl}/api/embeddings", [
                            'model' => $this->model,
                            'prompt' => 'test',
                        ]);
                        
                        if ($testResponse->successful()) {
                            return true;
                        }
                        
                        // Jika error "does not support embeddings", log dan return false
                        $error = $testResponse->json('error', '');
                        if (strpos($error, 'does not support embeddings') !== false) {
                            Log::warning('Ollama server tidak mendukung embeddings. Restart dengan flag --embeddings');
                            return false;
                        }
                    }
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Exception saat cek ketersediaan embedding: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Dapatkan model yang digunakan
     *
     * @return string
     */
    public function dapatkanModel(): string
    {
        return $this->model;
    }

    /**
     * Dapatkan dimensi vektor
     *
     * @return int
     */
    public function dapatkanDimensi(): int
    {
        return $this->dimension;
    }
}
