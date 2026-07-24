<?php

namespace App\Services;

class LayananKlasifikasiIntent
{
    /**
     * Klasifikasi intent pertanyaan pengguna
     *
     * @param string $pertanyaan
     * @return string Intent: 'statistik', 'daftar', 'isi_dokumen', 'umum'
     */
    public function klasifikasiIntent(string $pertanyaan): string
    {
        $pertanyaanLower = strtolower($pertanyaan);
        
        // Kata kunci untuk statistik (prioritas tertinggi)
        $kataKunciStatistik = [
            'berapa', 'jumlah', 'total', 'banyak', 'sebanyak', 'count',
            'statistik', 'summary', 'ringkasan data',
            'terbanyak', 'terbesar', 'terkecil', 'paling sedikit',
            'rata-rata', 'average', 'max', 'min', 'avg'
        ];
        
        // Kata kunci untuk daftar arsip
        $kataKunciDaftar = [
            'tampilkan', 'daftar', 'list', 'tunjukkan', 'lihat',
            'arsip', 'dokumen', 'surat', 'file', 'semua', 'seluruh',
            'terakhir', 'terbaru', 'pertama', '10', '5', '3'
        ];
        
        // Kata kunci untuk isi dokumen/ringkasan
        $kataKunciIsiDokumen = [
            'ringkasan', 'summary', 'isi', 'konten', 'content',
            'jelaskan', 'apa isi', 'tentang apa', 'menjelaskan',
            'nomor', 'surat ini', 'arsip ini',
            // Kata kunci nominal/nilai dalam dokumen
            'nilai', 'nominal', 'biaya', 'harga', 'anggaran', 'dana',
            'rupiah', 'rp', 'idr', 'bayar', 'pembayaran', 'tagihan',
            'kontrak', 'perjanjian', 'senilai', 'sebesar',
        ];
        
        // Cek intent isi dokumen (prioritas tertinggi setelah statistik)
        foreach ($kataKunciIsiDokumen as $kata) {
            if (strpos($pertanyaanLower, $kata) !== false) {
                // Pastikan bukan ringkasan data/statistik
                if (strpos($pertanyaanLower, 'statistik') === false) {
                    return 'isi_dokumen';
                }
            }
        }
        
        // Cek intent statistik
        foreach ($kataKunciStatistik as $kata) {
            if (strpos($pertanyaanLower, $kata) !== false) {
                return 'statistik';
            }
        }
        
        // Cek intent daftar arsip
        foreach ($kataKunciDaftar as $kata) {
            if (strpos($pertanyaanLower, $kata) !== false) {
                return 'daftar';
            }
        }
        
        // Default: pertanyaan umum
        return 'umum';
    }
    
    /**
     * Cek apakah pertanyaan membutuhkan data spesifik dari database
     *
     * @param string $pertanyaan
     * @return bool
     */
    public function butuhDataArsip(string $pertanyaan): bool
    {
        $intent = $this->klasifikasiIntent($pertanyaan);
        return in_array($intent, ['statistik', 'pencarian', 'ringkasan']);
    }
    
    /**
     * Cek apakah pertanyaan membutuhkan isi dokumen lengkap
     *
     * @param string $pertanyaan
     * @return bool
     */
    public function butuhIsiDokumen(string $pertanyaan): bool
    {
        return $this->klasifikasiIntent($pertanyaan) === 'ringkasan';
    }
    
    /**
     * Ekstrak kata kunci dari pertanyaan untuk pencarian database
     *
     * @param string $pertanyaan
     * @return array Daftar kata kunci yang diekstrak
     */
    public function ekstrakKataKunci(string $pertanyaan): array
    {
        $pertanyaanLower = strtolower($pertanyaan);
        
        // Stopwords Bahasa Indonesia yang lengkap
        $stopwords = [
            // Kata kerja permintaan
            'tolong', 'cek', 'cari', 'temukan', 'tunjukkan', 'lihat', 'berikan',
            'tampilkan', 'sampaikan', 'jelaskan', 'sebutkan', 'daftarkan',
            
            // Kata tanya dan kuantitas
            'berapa', 'jumlah', 'total', 'banyak', 'sebanyak', 'count',
            'ada', 'apakah', 'siapa', 'apa', 'mengapa', 'bagaimana', 'kapan',
            'dimana', 'mana', 'dari', 'ke', 'kepada',
            
            // Kata penghubung dan preposisi
            'yang', 'di', 'pada', 'untuk', 'dengan', 'dan', 'atau', 'serta',
            'tentang', 'mengenai', 'terkait', 'sebagai', 'yaitu', 'yakni',
            
            // Kata umum lainnya
            'arsip', 'dokumen', 'surat', 'file', 'data', 'isi', 'konten',
            'ringkasan', 'summary', 'halo', 'hai', 'selamat', 'terima', 'kasih',
            'makasih', 'thanks', 'dong', 'ya', 'deh', 'lah', 'kah',
            
            // Kata waktu
            'tahun', 'bulan', 'tanggal', 'waktu', 'periode', 'hari', 'minggu',
            
            // Kata status
            'aktif', 'inaktif', 'tersedia', 'dipinjam', 'status',
            
            // Kata lain yang tidak relevan untuk pencarian
            'adalah', 'ini', 'itu', 'tersebut', 'tersebutlah', 'semua',
            'seluruh', 'beberapa', 'setiap', 'masing', 'sendiri', 'juga',
            'lagi', 'lebih', 'paling', 'sangat', 'cukup', 'hanya',
            'saja', 'pun', 'nya', 'si', 'saya', 'kamu', 'kita',
            'mereka', 'beliau', 'anda', 'tuan', 'nyonya', ' Saudara'
        ];
        
        // Tokenisasi pertanyaan
        $kataKunci = preg_split('/[\s,?.!]+/', $pertanyaanLower, -1, PREG_SPLIT_NO_EMPTY);
        
        // Filter stopwords dan kata pendek (< 3 karakter)
        $kataKunci = array_filter($kataKunci, function($kata) use ($stopwords) {
            return strlen($kata) >= 3 && !in_array($kata, $stopwords);
        });
        
        // Hapus duplikat
        $kataKunci = array_unique($kataKunci);
        
        // Batasi jumlah kata kunci (maksimal 5)
        $kataKunci = array_slice($kataKunci, 0, 5);
        
        return array_values($kataKunci);
    }
    
    /**
     * Ekstrak filter tahun dari pertanyaan
     *
     * @param string $pertanyaan
     * @return int|null Tahun yang diekstrak atau null
     */
    public function ekstrakTahun(string $pertanyaan): ?int
    {
        // Cek pola tahun 4 digit (1900-2099)
        if (preg_match('/\b(19|20)\d{2}\b/', $pertanyaan, $matches)) {
            return (int) $matches[0];
        }
        
        return null;
    }
}
