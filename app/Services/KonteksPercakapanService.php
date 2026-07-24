<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class KonteksPercakapanService
{
    protected string $cachePrefix = 'ai_chat_context_';
    protected int $cacheDuration = 3600; // 1 jam

    /**
     * Simpan konteks percakapan untuk user
     *
     * @param string $userId
     * @param array $data
     * @return void
     */
    public function simpanKonteks(string $userId, array $data): void
    {
        try {
            $cacheKey = $this->cachePrefix . $userId;
            Cache::put($cacheKey, $data, $this->cacheDuration);
            Log::info('KonteksPercakapanService: Konteks disimpan', ['user_id' => $userId, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('KonteksPercakapanService: Gagal simpan konteks', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Ambil konteks percakapan untuk user
     *
     * @param string $userId
     * @return array
     */
    public function ambilKonteks(string $userId): array
    {
        try {
            $cacheKey = $this->cachePrefix . $userId;
            $konteks = Cache::get($cacheKey, []);
            Log::info('KonteksPercakapanService: Konteks diambil', ['user_id' => $userId, 'konteks' => $konteks]);
            return $konteks;
        } catch (\Exception $e) {
            Log::error('KonteksPercakapanService: Gagal ambil konteks', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Hapus konteks percakapan untuk user
     *
     * @param string $userId
     * @return void
     */
    public function hapusKonteks(string $userId): void
    {
        try {
            $cacheKey = $this->cachePrefix . $userId;
            Cache::forget($cacheKey);
            Log::info('KonteksPercakapanService: Konteks dihapus', ['user_id' => $userId]);
        } catch (\Exception $e) {
            Log::error('KonteksPercakapanService: Gagal hapus konteks', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Simpan hasil pencarian terakhir untuk referensi
     *
     * @param string $userId
     * @param array $hasilPencarian
     * @return void
     */
    public function simpanHasilPencarianTerakhir(string $userId, array $hasilPencarian): void
    {
        try {
            $cacheKey = $this->cachePrefix . $userId;
            $konteks = $this->ambilKonteks($userId);
            $konteks['hasil_pencarian_terakhir'] = $hasilPencarian;
            $konteks['timestamp_pencarian'] = now()->toIso8601String();
            Cache::put($cacheKey, $konteks, $this->cacheDuration);
            Log::info('KonteksPercakapanService: Hasil pencarian disimpan', ['user_id' => $userId, 'jumlah' => count($hasilPencarian)]);
        } catch (\Exception $e) {
            Log::error('KonteksPercakapanService: Gagal simpan hasil pencarian', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Ambil hasil pencarian terakhir
     *
     * @param string $userId
     * @return array
     */
    public function ambilHasilPencarianTerakhir(string $userId): array
    {
        try {
            $konteks = $this->ambilKonteks($userId);
            return $konteks['hasil_pencarian_terakhir'] ?? [];
        } catch (\Exception $e) {
            Log::error('KonteksPercakapanService: Gagal ambil hasil pencarian', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Cek apakah ada hasil pencarian terakhir yang valid
     *
     * @param string $userId
     * @return bool
     */
    public function adaHasilPencarianTerakhir(string $userId): bool
    {
        try {
            $konteks = $this->ambilKonteks($userId);
            if (!isset($konteks['hasil_pencarian_terakhir']) || empty($konteks['hasil_pencarian_terakhir'])) {
                return false;
            }

            // Cek apakah hasil pencarian masih valid (maksimal 30 menit)
            if (isset($konteks['timestamp_pencarian'])) {
                $timestamp = \Carbon\Carbon::parse($konteks['timestamp_pencarian']);
                if ($timestamp->diffInMinutes(now()) > 30) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('KonteksPercakapanService: Gagal cek hasil pencarian', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Simpan kata kunci terakhir untuk referensi
     *
     * @param string $userId
     * @param array $kataKunci
     * @return void
     */
    public function simpanKataKunciTerakhir(string $userId, array $kataKunci): void
    {
        try {
            $cacheKey = $this->cachePrefix . $userId;
            $konteks = $this->ambilKonteks($userId);
            $konteks['kata_kunci_terakhir'] = $kataKunci;
            $konteks['timestamp_kata_kunci'] = now()->toIso8601String();
            Cache::put($cacheKey, $konteks, $this->cacheDuration);
            Log::info('KonteksPercakapanService: Kata kunci disimpan', ['user_id' => $userId, 'kata_kunci' => $kataKunci]);
        } catch (\Exception $e) {
            Log::error('KonteksPercakapanService: Gagal simpan kata kunci', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Ambil kata kunci terakhir
     *
     * @param string $userId
     * @return array
     */
    public function ambilKataKunciTerakhir(string $userId): array
    {
        try {
            $konteks = $this->ambilKonteks($userId);
            return $konteks['kata_kunci_terakhir'] ?? [];
        } catch (\Exception $e) {
            Log::error('KonteksPercakapanService: Gagal ambil kata kunci', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Deteksi referensi ke hasil pencarian sebelumnya
     *
     * @param string $pertanyaan
     * @return bool
     */
    public function deteksiReferensiKonteks(string $pertanyaan): bool
    {
        $pertanyaanLower = strtolower($pertanyaan);
        
        // Kata kunci yang menunjukkan referensi ke konteks sebelumnya
        $kataKunciReferensi = [
            'itu', 'tersebut', 'tadi', 'yang tadi', 'tadi itu',
            'yang itu', 'terakhir', 'sebelumnya', 'lagi', 'sekali lagi'
        ];
        
        foreach ($kataKunciReferensi as $kata) {
            if (strpos($pertanyaanLower, $kata) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Simpan riwayat percakapan untuk konteks yang lebih lengkap
     *
     * @param string $userId
     * @param string $pertanyaan
     * @param string $jawaban
     * @param array $metadata
     * @return void
     */
    public function simpanRiwayatPercakapan(string $userId, string $pertanyaan, string $jawaban, array $metadata = []): void
    {
        try {
            $cacheKey = $this->cachePrefix . $userId;
            $konteks = $this->ambilKonteks($userId);
            
            if (!isset($konteks['riwayat'])) {
                $konteks['riwayat'] = [];
            }
            
            // Tambah percakapan baru ke riwayat
            $konteks['riwayat'][] = [
                'pertanyaan' => $pertanyaan,
                'jawaban' => $jawaban,
                'metadata' => $metadata,
                'timestamp' => now()->toIso8601String(),
            ];
            
            // Batasi riwayat maksimal 10 percakapan terakhir
            if (count($konteks['riwayat']) > 10) {
                $konteks['riwayat'] = array_slice($konteks['riwayat'], -10);
            }
            
            Cache::put($cacheKey, $konteks, $this->cacheDuration);
            Log::info('KonteksPercakapanService: Riwayat percakapan disimpan', ['user_id' => $userId]);
        } catch (\Exception $e) {
            Log::error('KonteksPercakapanService: Gagal simpan riwayat percakapan', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Ambil riwayat percakapan
     *
     * @param string $userId
     * @return array
     */
    public function ambilRiwayatPercakapan(string $userId): array
    {
        try {
            $konteks = $this->ambilKonteks($userId);
            return $konteks['riwayat'] ?? [];
        } catch (\Exception $e) {
            Log::error('KonteksPercakapanService: Gagal ambil riwayat percakapan', ['error' => $e->getMessage()]);
            return [];
        }
    }
}
