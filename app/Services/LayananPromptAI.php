<?php

namespace App\Services;

class LayananPromptAI
{
    protected int $maxContextTokens;
    protected int $maxTokensPerArchive;

    public function __construct()
    {
        $this->maxContextTokens = config('ai.settings.max_context_length', 10000);
        $this->maxTokensPerArchive = 1000; // Max tokens per archive in context
    }

    /**
     * Buat prompt untuk AI berdasarkan pertanyaan user dan konteks arsip
     *
     * @param string $pertanyaan Pertanyaan user
     * @param \Illuminate\Support\Collection $arsipRelevan Daftar arsip yang relevan
     * @param string $intent Intent pertanyaan (statistik, daftar, isi_dokumen, umum)
     * @param array $dataDatabase Data dari database (untuk statistik, dll)
     * @return string Prompt yang sudah disiapkan
     */
    public function buatPrompt(string $pertanyaan, \Illuminate\Support\Collection $arsipRelevan, string $intent = 'pencarian', array $dataDatabase = []): string
    {
        // Handle berdasarkan intent
        return match ($intent) {
            'statistik' => $this->buatPromptStatistik($pertanyaan, $dataDatabase),
            'daftar' => $this->buatPromptDaftar($pertanyaan, $arsipRelevan),
            'isi_dokumen' => $this->buatPromptRingkasanDokumen($pertanyaan, $arsipRelevan),
            'umum' => $this->buatPromptUmum($pertanyaan),
            default => $this->buatPromptUmum($pertanyaan),
        };
    }

    /**
     * Buat prompt untuk pertanyaan statistik (database-first)
     *
     * @param string $pertanyaan
     * @param array $dataDatabase
     * @return string
     */
    private function buatPromptStatistik(string $pertanyaan, array $dataDatabase): string
    {
        $dataText = $this->formatDataDatabase($dataDatabase);
        
        $prompt = <<<PROMPT
DATA STATISTIK DARI DATABASE (HARUS DIGUNAKAN):
{$dataText}

PERTANYAAN USER:
{$pertanyaan}

INSTRUKSI PENTING:
1. Jawab HANYA menggunakan DATA STATISTIK di atas
2. JANGAN mengarang angka atau data
3. JANGAN menggunakan pengetahuan umum untuk pertanyaan statistik
4. Jika data tidak tersedia, sampaikan dengan jelas bahwa data tidak ditemukan
5. Susun jawaban dengan bahasa yang natural dan mudah dipahami

JAWABAN:
PROMPT;

        return $prompt;
    }

    /**
     * Buat prompt untuk daftar arsip (database-first)
     *
     * @param string $pertanyaan
     * @param \Illuminate\Support\Collection $arsipRelevan
     * @return string
     */
    private function buatPromptDaftar(string $pertanyaan, \Illuminate\Support\Collection $arsipRelevan): string
    {
        if ($arsipRelevan->isEmpty()) {
            return $this->buatPromptDataTidakDitemukan($pertanyaan);
        }

        $konteks = $this->siapkanKonteksArsipDenganTokenLimit($arsipRelevan);
        
        $prompt = <<<PROMPT
DATA ARSIP DARI DATABASE:
{$konteks}

PERTANYAAN USER:
{$pertanyaan}

INSTRUKSI PENTING:
1. Jawab HANYA berdasarkan DATA ARSIP di atas
2. JANGAN mengarang informasi arsip yang tidak ada
3. Tampilkan daftar arsip dengan format yang jelas dan mudah dibaca
4. Jika arsip tidak ditemukan, sampaikan dengan jelas
5. Susun jawaban dengan bahasa yang natural dan mudah dipahami

JAWABAN:
PROMPT;

        return $prompt;
    }

    /**
     * Buat prompt untuk ringkasan dokumen (database-first)
     *
     * @param string $pertanyaan
     * @param \Illuminate\Support\Collection $arsipRelevan
     * @return string
     */
    private function buatPromptRingkasanDokumen(string $pertanyaan, \Illuminate\Support\Collection $arsipRelevan): string
    {
        if ($arsipRelevan->isEmpty()) {
            return $this->buatPromptDataTidakDitemukan($pertanyaan);
        }

        $konteks = $this->siapkanKonteksArsipDenganTokenLimit($arsipRelevan);
        
        $prompt = <<<PROMPT
DATA DOKUMEN DARI DATABASE:
{$konteks}

PERTANYAAN USER:
{$pertanyaan}

INSTRUKSI PENTING:
1. Jawab HANYA berdasarkan DATA DOKUMEN di atas
2. JANGAN mengarang informasi yang tidak ada di dokumen
3. Jika informasi tidak ditemukan di dokumen, sampaikan dengan jelas
4. Susun jawaban dengan bahasa yang natural dan mudah dipahami
5. Berikan ringkasan atau penjelasan sesuai yang diminta

JAWABAN:
PROMPT;

        return $prompt;
    }

    /**
     * Buat prompt untuk pencarian arsip (database-first)
     *
     * @param string $pertanyaan
     * @param \Illuminate\Support\Collection $arsipRelevan
     * @return string
     */
    private function buatPromptPencarian(string $pertanyaan, \Illuminate\Support\Collection $arsipRelevan): string
    {
        if ($arsipRelevan->isEmpty()) {
            return $this->buatPromptDataTidakDitemukan($pertanyaan);
        }

        $konteks = $this->siapkanKonteksArsipDenganTokenLimit($arsipRelevan);
        
        $prompt = <<<PROMPT
DATA ARSIP DARI DATABASE:
{$konteks}

PERTANYAAN USER:
{$pertanyaan}

INSTRUKSI PENTING:
1. Jawab HANYA berdasarkan DATA ARSIP di atas
2. JANGAN mengarang informasi arsip yang tidak ada
3. Jika arsip tidak ditemukan, sampaikan dengan jelas
4. Susun jawaban dengan bahasa yang natural dan mudah dipahami
5. Di akhir jawaban, sertakan referensi arsip yang digunakan

JAWABAN:
PROMPT;

        return $prompt;
    }

    /**
     * Buat prompt untuk pertanyaan umum
     *
     * @param string $pertanyaan
     * @return string
     */
    private function buatPromptUmum(string $pertanyaan): string
    {
        $prompt = <<<PROMPT
PERTANYAAN USER:
{$pertanyaan}

INSTRUKSI:
1. Jawab pertanyaan umum dengan pengetahuan umum Anda
2. Jangan mengarang data arsip atau statistik
3. Jika pertanyaan terkait arsip tetapi data tidak tersedia, sampaikan bahwa data tidak ditemukan

JAWABAN:
PROMPT;

        return $prompt;
    }

    /**
     * Buat prompt ketika data tidak ditemukan
     *
     * @param string $pertanyaan
     * @return string
     */
    private function buatPromptDataTidakDitemukan(string $pertanyaan): string
    {
        $prompt = <<<PROMPT
PERTANYAAN USER:
{$pertanyaan}

STATUS: Data tidak ditemukan di database

INSTRUKSI:
1. Sampaikan dengan sopan bahwa data yang dicari tidak ditemukan
2. Jangan mengarang data atau informasi
3. Berikan saran untuk mencari dengan kata kunci lain jika relevan

JAWABAN:
PROMPT;

        return $prompt;
    }

    /**
     * Format data database menjadi teks untuk prompt
     *
     * @param array $dataDatabase
     * @return string
     */
    private function formatDataDatabase(array $dataDatabase): string
    {
        $text = "";
        foreach ($dataDatabase as $key => $value) {
            $text .= "- {$key}: {$value}\n";
        }
        return $text;
    }

    /**
     * Deteksi apakah pertanyaan bersifat umum (bukan terkait arsip)
     *
     * @param string $pertanyaan
     * @return bool
     */
    private function adalahPertanyaanUmum(string $pertanyaan): bool
    {
        $pertanyaan = strtolower(trim($pertanyaan));
        
        // Kata kunci yang menunjukkan pertanyaan tentang statistik/arsip (bukan umum)
        $kataKunciArsip = [
            'berapa', 'jumlah', 'total', 'banyak', 'sebanyak',
            'arsip', 'dokumen', 'surat', 'file',
            'masuk', 'tersedia', 'ada',
        ];

        // Jika mengandung kata kunci arsip, bukan pertanyaan umum
        foreach ($kataKunciArsip as $kata) {
            if (strpos($pertanyaan, $kata) !== false) {
                return false;
            }
        }
        
        // Kata kunci yang menunjukkan pertanyaan umum
        $kataKunciUmum = [
            'halo', 'hai', 'selamat', 'siapa', 'apa itu', 'mengapa', 'bagaimana',
            'buatkan', 'tulis', 'jelaskan', 'definisi', 'pengertian', 'arti',
            'contoh', 'tips', 'cara', 'tutorial', 'panduan', 'bantu',
            'terima kasih', 'makasih', 'thanks', 'sampai jumpa',
        ];

        foreach ($kataKunciUmum as $kata) {
            if (strpos($pertanyaan, $kata) !== false) {
                return true;
            }
        }

        // Jika pertanyaan sangat pendek (< 5 kata), kemungkinan pertanyaan umum
        $jumlahKata = str_word_count($pertanyaan);
        if ($jumlahKata < 5) {
            return true;
        }

        return false;
    }

    /**
     * Siapkan konteks dari arsip yang relevan dengan token limit
     *
     * @param \Illuminate\Support\Collection $arsipRelevan
     * @return string
     */
    private function siapkanKonteksArsipDenganTokenLimit(\Illuminate\Support\Collection $arsipRelevan): string
    {
        if ($arsipRelevan->isEmpty()) {
            return "TIDAK ADA KONTEKS ARSIP. Jawab berdasarkan pengetahuan umum Anda.";
        }

        $konteks = "";
        $usedTokens = 0;
        $arsipCount = 0;

        foreach ($arsipRelevan as $arsip) {
            $arsipKonteks = $this->formatArsipKonteks($arsip);
            $arsipTokens = $this->hitungToken($arsipKonteks);

            // Cek jika menambahkan arsip ini akan melebihi limit
            if ($usedTokens + $arsipTokens > $this->maxContextTokens) {
                // Coba ringkas jika masih ada ruang (minimal 10% tersisa)
                if ($usedTokens < $this->maxContextTokens * 0.9) {
                    $ringkasan = $this->ringkasArsipKonteks($arsip, $this->maxContextTokens - $usedTokens);
                    $ringkasanTokens = $this->hitungToken($ringkasan);
                    
                    if ($usedTokens + $ringkasanTokens <= $this->maxContextTokens) {
                        $konteks .= $ringkasan;
                        $usedTokens += $ringkasanTokens;
                        $arsipCount++;
                    }
                }
                // Berhenti jika sudah mendekati batas token
                break;
            }

            $konteks .= $arsipKonteks;
            $usedTokens += $arsipTokens;
            $arsipCount++;
        }

        if ($arsipCount === 0) {
            return "Konteks terlalu panjang untuk ditampilkan secara lengkap.";
        }

        return $konteks;
    }

    /**
     * Format konteks arsip
     *
     * @param object $arsip
     * @return string
     */
    private function formatArsipKonteks($arsip): string
    {
        $isiDokumen = $arsip->isi_dokumen ?? '';
        
        // Batasi panjang isi dokumen per arsip
        if (strlen($isiDokumen) > 800) {
            $isiDokumen = substr($isiDokumen, 0, 800) . '...';
        }

        $konteks = "\n--- ARSIP ---\n";
        $konteks .= "Nomor Surat: " . ($arsip->nomor_surat ?? '—') . "\n";
        $konteks .= "Nama Arsip: " . ($arsip->nama_arsip ?? '—') . "\n";
        $konteks .= "Perihal: " . ($arsip->perihal_surat ?? '—') . "\n";
        $konteks .= "Tanggal: " . ($arsip->tanggal_arsip ?? '—') . "\n";
        $konteks .= "Jenis: " . ($arsip->nama_jenis ?? '—') . "\n";
        $konteks .= "Isi Dokumen:\n" . $isiDokumen . "\n";
        $konteks .= "---\n";

        return $konteks;
    }

    /**
     * Ringkas konteks arsip jika terlalu panjang
     *
     * @param object $arsip
     * @param int $maxTokens
     * @return string
     */
    private function ringkasArsipKonteks($arsip, int $maxTokens): string
    {
        $konteks = "\n--- ARSIP (DIRINGKAS) ---\n";
        $konteks .= "Nomor Surat: " . ($arsip->nomor_surat ?? '—') . "\n";
        $konteks .= "Nama Arsip: " . ($arsip->nama_arsip ?? '—') . "\n";
        $konteks .= "Perihal: " . ($arsip->perihal_surat ?? '—') . "\n";
        
        // Ambil excerpt dari isi dokumen
        $isiDokumen = $arsip->isi_dokumen ?? '';
        $excerptLength = min(strlen($isiDokumen), 500);
        $konteks .= "Isi Dokumen (excerpt): " . substr($isiDokumen, 0, $excerptLength) . "...\n";
        $konteks .= "---\n";

        return $konteks;
    }

    /**
     * Hitung estimasi token dari teks (perkiraan sederhana)
     * 1 token ≈ 4 karakter untuk bahasa Indonesia
     *
     * @param string $text
     * @return int
     */
    private function hitungToken(string $text): int
    {
        if (empty($text)) {
            return 0;
        }

        // Perkiraan sederhana: 1 token ≈ 4 karakter
        // Untuk akurasi lebih tinggi, gunakan library seperti tiktoken
        return (int) ceil(strlen($text) / 4);
    }

    /**
     * Siapkan konteks dari arsip yang relevan (legacy method)
     *
     * @param \Illuminate\Support\Collection $arsipRelevan
     * @return string
     */
    private function siapkanKonteksArsip(\Illuminate\Support\Collection $arsipRelevan): string
    {
        if ($arsipRelevan->isEmpty()) {
            return "Tidak ada arsip yang relevan ditemukan.";
        }

        $konteks = "";
        foreach ($arsipRelevan as $index => $arsip) {
            $konteks .= "\n--- ARSIP " . ($index + 1) . " ---\n";
            $konteks .= "Nomor Surat: " . ($arsip->nomor_surat ?? '—') . "\n";
            $konteks .= "Nama Arsip: " . ($arsip->nama_arsip ?? '—') . "\n";
            $konteks .= "Perihal: " . ($arsip->perihal_surat ?? '—') . "\n";
            $konteks .= "Tanggal: " . ($arsip->tanggal_arsip ?? '—') . "\n";
            $konteks .= "Jenis: " . ($arsip->nama_jenis ?? '—') . "\n";
            $konteks .= "Isi Dokumen:\n" . ($arsip->isi_dokumen ?? '—') . "\n";
            $konteks .= "---\n";
        }

        return $konteks;
    }

    /**
     * Buat prompt untuk ringkasan arsip
     *
     * @param string $isiDokumen
     * @return string
     */
    public function buatPromptRingkasan(string $isiDokumen): string
    {
        $prompt = <<<PROMPT
Buat ringkasan dari dokumen berikut dalam bahasa Indonesia:

ISI DOKUMEN:
{$isiDokumen}

INSTRUKSI:
1. Buat ringkasan yang jelas dan ringkas
2. Fokus pada poin-poin penting
3. Gunakan format bullet points
4. Maksimal 5 poin

RINGKASAN:
PROMPT;

        return $prompt;
    }

    /**
     * Buat prompt untuk ekstraksi informasi spesifik
     *
     * @param string $isiDokumen
     * @param array $informasiYangDicari Tipe informasi yang dicari
     * @return string
     */
    public function buatPromptEkstraksiInformasi(string $isiDokumen, array $informasiYangDicari): string
    {
        $daftarInformasi = implode(', ', $informasiYangDicari);
        
        $prompt = <<<PROMPT
Ekstrak informasi berikut dari dokumen: {$daftarInformasi}

ISI DOKUMEN:
{$isiDokumen}

INSTRUKSI:
1. Ekstrak informasi yang diminta
2. Jika informasi tidak ditemukan, tulis "Tidak ditemukan"
3. Gunakan format yang mudah dibaca

INFORMASI YANG DIEKSTRAK:
PROMPT;

        return $prompt;
    }

    /**
     * Buat prompt untuk analisis sentimen
     *
     * @param string $isiDokumen
     * @return string
     */
    public function buatPromptAnalisisSentimen(string $isiDokumen): string
    {
        $prompt = <<<PROMPT
Analisis sentimen dari dokumen berikut:

ISI DOKUMEN:
{$isiDokumen}

INSTRUKSI:
1. Tentukan sentimen dokumen (positif, negatif, atau netral)
2. Jelaskan alasan analisis
3. Berikan confidence score (0-100%)

ANALISIS SENTIMEN:
PROMPT;

        return $prompt;
    }

    /**
     * Buat prompt untuk klasifikasi dokumen
     *
     * @param string $isiDokumen
     * @param array $kategoriKlasifikasi Kategori yang tersedia
     * @return string
     */
    public function buatPromptKlasifikasi(string $isiDokumen, array $kategoriKlasifikasi): string
    {
        $kategori = implode(', ', $kategoriKlasifikasi);
        
        $prompt = <<<PROMPT
Klasifikasikan dokumen berikut ke dalam salah satu kategori: {$kategori}

ISI DOKUMEN:
{$isiDokumen}

INSTRUKSI:
1. Pilih kategori yang paling sesuai
2. Jelaskan alasan pemilihan
3. Berikan confidence score (0-100%)

KLASIFIKASI:
PROMPT;

        return $prompt;
    }

    /**
     * Buat prompt untuk pertanyaan lanjutan
     *
     * @param string $pertanyaanSebelumnya
     * @param string $jawabanSebelumnya
     * @param string $pertanyaanBaru
     * @return string
     */
    public function buatPromptPertanyaanLanjutan(
        string $pertanyaanSebelumnya,
        string $jawabanSebelumnya,
        string $pertanyaanBaru
    ): string {
        $prompt = <<<PROMPT
KONTEKS PERCAKAPAN SEBELUMNYA:
Pertanyaan: {$pertanyaanSebelumnya}
Jawaban: {$jawabanSebelumnya}

PERTANYAAN LANJUTAN:
{$pertanyaanBaru}

INSTRUKSI:
1. Jawab pertanyaan lanjutan dengan mempertimbangkan konteks percakapan sebelumnya
2. Jika pertanyaan tidak terkait, jelaskan bahwa ini adalah topik baru
3. Berikan jawaban yang konsisten dengan jawaban sebelumnya

JAWABAN:
PROMPT;

        return $prompt;
    }

    /**
     * Format jawaban AI untuk display
     *
     * @param string $jawabanAI
     * @param array $sumberArsip
     * @return array
     */
    public function formatJawaban(string $jawabanAI, array $sumberArsip): array
    {
        return [
            'jawaban' => $jawabanAI,
            'sumber_arsip' => $sumberArsip,
            'timestamp' => now()->format('d M Y H:i:s'),
        ];
    }

    /**
     * Validasi prompt sebelum dikirim ke AI
     *
     * @param string $prompt
     * @return bool
     */
    public function validasiPrompt(string $prompt): bool
    {
        // Cek panjang prompt
        if (strlen($prompt) > 10000) {
            return false;
        }

        // Cek konten berbahaya (SQL injection patterns)
        $kataBerbahaya = ['drop table', 'truncate table', 'exec(', 'eval(', '; delete', '; drop'];
        foreach ($kataBerbahaya as $kata) {
            if (stripos($prompt, $kata) !== false) {
                \Log::warning('Prompt mengandung pola berbahaya: ' . $kata);
                return false;
            }
        }

        return true;
    }
}
