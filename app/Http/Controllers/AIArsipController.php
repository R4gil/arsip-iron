<?php

namespace App\Http\Controllers;

use App\Services\LayananPencarianArsip;
use App\Services\LayananPromptAI;
use App\Services\LayananAI;
use App\Services\LayananKlasifikasiIntent;
use App\Services\StatistikService;
use App\Services\KonteksPercakapanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AIArsipController extends Controller
{
    protected $layananPencarian;
    protected $layananPrompt;
    protected $layananAI;
    protected $layananKlasifikasiIntent;
    protected $statistikService;
    protected $konteksPercakapanService;

    public function __construct(
        LayananPencarianArsip $layananPencarian,
        LayananPromptAI $layananPrompt,
        LayananAI $layananAI,
        LayananKlasifikasiIntent $layananKlasifikasiIntent,
        StatistikService $statistikService,
        KonteksPercakapanService $konteksPercakapanService
    ) {
        $this->layananPencarian = $layananPencarian;
        $this->layananPrompt = $layananPrompt;
        $this->layananAI = $layananAI;
        $this->layananKlasifikasiIntent = $layananKlasifikasiIntent;
        $this->statistikService = $statistikService;
        $this->konteksPercakapanService = $konteksPercakapanService;
    }

    /**
     * Tampilkan halaman AI Arsip
     */
    public function index()
    {
        $statistik = $this->layananPencarian->dapatkanStatistikPencarian();
        
        return view('ai-arsip.index', [
            'statistik' => $statistik,
        ]);
    }

    /**
     * Tanya AI dengan pertanyaan user
     */
    public function tanyaAI(Request $request)
    {
        $startTime = microtime(true);
        $userId = auth()->id() ?? session()->getId();
        
        $request->validate([
            'pertanyaan' => 'required|string|max:1000',
        ]);

        $pertanyaan = $request->input('pertanyaan');
        Log::info('=== Mulai proses AI Arsip ===', ['pertanyaan' => $pertanyaan, 'user_id' => $userId]);

        try {
            // Klasifikasi intent pertanyaan
            $intentStart = microtime(true);
            $intent = $this->layananKlasifikasiIntent->klasifikasiIntent($pertanyaan);
            $intentTime = round((microtime(true) - $intentStart) * 1000, 2);
            Log::info('Intent terdeteksi', ['intent' => $intent, 'waktu_ms' => $intentTime]);

            // Ekstrak kata kunci dari pertanyaan
            $keywordStart = microtime(true);
            $kataKunci = $this->layananKlasifikasiIntent->ekstrakKataKunci($pertanyaan);
            $tahun = $this->layananKlasifikasiIntent->ekstrakTahun($pertanyaan);
            $keywordTime = round((microtime(true) - $keywordStart) * 1000, 2);
            Log::info('Kata kunci diekstrak', ['kata_kunci' => $kataKunci, 'tahun' => $tahun, 'waktu_ms' => $keywordTime]);

            // Cek referensi ke konteks percakapan sebelumnya
            $adaReferensiKonteks = $this->konteksPercakapanService->deteksiReferensiKonteks($pertanyaan);
            Log::info('Deteksi referensi konteks', ['ada_referensi' => $adaReferensiKonteks]);

            // Inisialisasi variabel
            $arsipRelevan = collect();
            $dataDatabase = [];
            $statistikTime = 0;
            $searchTime = 0;
            $jumlahArsipDitemukan = 0;
            $sumberJawaban = 'pengetahuan_umum';

            // Handle berdasarkan intent
            switch ($intent) {
                case 'statistik':
                    $statistikStart = microtime(true);
                    $filter = [];
                    if ($tahun) {
                        $filter['tahun'] = $tahun;
                    }
                    
                    // Jika ada referensi konteks, gunakan kata kunci terakhir
                    if ($adaReferensiKonteks && $this->konteksPercakapanService->adaHasilPencarianTerakhir($userId)) {
                        $kataKunci = $this->konteksPercakapanService->ambilkataKunciTerakhir($userId);
                        Log::info('Menggunakan kata kunci dari konteks', ['kata_kunci' => $kataKunci]);
                    }
                    
                    $dataDatabase = $this->statistikService->hitungStatistikLengkap($kataKunci, $filter);
                    $jumlahArsipDitemukan = $dataDatabase['Total Arsip'] ?? 0;
                    $statistikTime = round((microtime(true) - $statistikStart) * 1000, 2);
                    $sumberJawaban = 'database_statistik';
                    Log::info('Data statistik diambil', ['data' => $dataDatabase, 'waktu_ms' => $statistikTime]);
                    
                    // Simpan kata kunci untuk konteks
                    $this->konteksPercakapanService->simpanKataKunciTerakhir($userId, $kataKunci);
                    break;

                case 'daftar':
                    $searchStart = microtime(true);
                    
                    // Jika ada referensi konteks, gunakan hasil pencarian terakhir
                    if ($adaReferensiKonteks && $this->konteksPercakapanService->adaHasilPencarianTerakhir($userId)) {
                        $arsipRelevan = collect($this->konteksPercakapanService->ambilHasilPencarianTerakhir($userId));
                        Log::info('Menggunakan hasil pencarian dari konteks', ['jumlah_arsip' => $arsipRelevan->count()]);
                    } else {
                        $queryString = implode(' ', $kataKunci);
                        if ($tahun) {
                            $queryString .= " {$tahun}";
                        }
                        $arsipRelevan = $this->layananPencarian->cariArsip($queryString)->take(5);
                        $this->konteksPercakapanService->simpanHasilPencarianTerakhir($userId, $arsipRelevan->toArray());
                        $this->konteksPercakapanService->simpanKataKunciTerakhir($userId, $kataKunci);
                    }
                    
                    $jumlahArsipDitemukan = $arsipRelevan->count();
                    $searchTime = round((microtime(true) - $searchStart) * 1000, 2);
                    $sumberJawaban = $arsipRelevan->isEmpty() ? 'pengetahuan_umum' : 'database_arsip';
                    Log::info('Pencarian arsip selesai', ['waktu_ms' => $searchTime, 'jumlah_arsip' => $jumlahArsipDitemukan]);
                    break;

                case 'isi_dokumen':
                    $searchStart = microtime(true);
                    $queryString = implode(' ', $kataKunci);
                    if ($tahun) {
                        $queryString .= " {$tahun}";
                    }
                    $arsipRelevan = $this->layananPencarian->cariArsip($queryString)->take(5);
                    $jumlahArsipDitemukan = $arsipRelevan->count();
                    $searchTime = round((microtime(true) - $searchStart) * 1000, 2);
                    $sumberJawaban = 'database_dokumen';
                    Log::info('Pencarian arsip untuk isi dokumen selesai', ['waktu_ms' => $searchTime, 'jumlah_arsip' => $jumlahArsipDitemukan]);
                    break;

                case 'umum':
                    Log::info('Pertanyaan umum, tidak perlu query database');
                    $sumberJawaban = 'pengetahuan_umum';
                    break;
            }

            // Buat prompt berdasarkan intent
            $contextStart = microtime(true);
            $prompt = $this->layananPrompt->buatPrompt($pertanyaan, $arsipRelevan, $intent, $dataDatabase);
            $contextTime = round((microtime(true) - $contextStart) * 1000, 2);
            Log::info('Penyusunan konteks selesai', ['waktu_ms' => $contextTime, 'prompt_length' => strlen($prompt)]);

            // Cek ketersediaan AI
            if (!$this->layananAI->cekKetersediaan()) {
                $totalTime = round((microtime(true) - $startTime) * 1000, 2);
                Log::error('AI tidak tersedia', ['total_waktu_ms' => $totalTime]);
                return response()->json([
                    'success' => false,
                    'message' => 'Layanan AI belum dikonfigurasi. Silakan hubungi administrator.',
                    'jawaban' => 'Maaf, layanan AI belum tersedia. Silakan hubungi administrator untuk konfigurasi.',
                    'sumber_arsip' => [],
                ]);
            }

            // Validasi prompt
            if (!$this->layananPrompt->validasiPrompt($prompt)) {
                $totalTime = round((microtime(true) - $startTime) * 1000, 2);
                Log::error('Prompt tidak valid', ['total_waktu_ms' => $totalTime]);
                return response()->json([
                    'success' => false,
                    'message' => 'Prompt tidak valid.',
                    'jawaban' => 'Maaf, terjadi kesalahan dalam memproses pertanyaan Anda.',
                    'sumber_arsip' => [],
                ]);
            }

            // Kirim prompt ke AI
            $ollamaStart = microtime(true);
            $jawabanAI = $this->layananAI->kirimPrompt($prompt);
            $ollamaTime = round((microtime(true) - $ollamaStart) * 1000, 2);
            Log::info('Request ke Ollama selesai', ['waktu_ms' => $ollamaTime, 'berhasil' => !empty($jawabanAI)]);

            if (!$jawabanAI) {
                $totalTime = round((microtime(true) - $startTime) * 1000, 2);
                Log::error('Gagal mendapatkan jawaban dari AI', ['total_waktu_ms' => $totalTime]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mendapatkan jawaban dari AI. Silakan coba lagi.',
                    'jawaban' => 'Maaf, terjadi kesalahan saat menghubungi layanan AI. Silakan coba lagi nanti.',
                    'sumber_arsip' => [],
                ]);
            }

            // Format jawaban dengan informasi file arsip
            $formatStart = microtime(true);
            $sumberArsip = $arsipRelevan->map(function ($arsip) use ($pertanyaan, $kataKunci) {
                return [
                    'id' => $arsip->id,
                    'nomor_surat' => $arsip->nomor_surat,
                    'nama_arsip' => $arsip->nama_arsip,
                    'perihal' => $arsip->perihal_surat,
                    'excerpt' => $this->layananPencarian->ambilExcerptDenganHighlight(
                        $arsip->isi_dokumen ?? '',
                        implode(' ', $kataKunci),
                        200
                    ),
                    'file_arsip' => $arsip->file_arsip,
                    'url_file' => $arsip->file_arsip ? route('arsip.viewFile', $arsip->file_arsip) : null,
                    'relevansi_score' => $arsip->relevansi_score ?? 0,
                ];
            })->toArray();
            $formatTime = round((microtime(true) - $formatStart) * 1000, 2);

            // Simpan riwayat percakapan
            $this->konteksPercakapanService->simpanRiwayatPercakapan($userId, $pertanyaan, $jawabanAI, [
                'intent' => $intent,
                'kata_kunci' => $kataKunci,
                'jumlah_arsip' => $jumlahArsipDitemukan,
                'sumber_jawaban' => $sumberJawaban,
            ]);

            $totalTime = round((microtime(true) - $startTime) * 1000, 2);
            Log::info('=== Proses AI Arsip selesai ===', [
                'intent_ms' => $intentTime,
                'keyword_ms' => $keywordTime,
                'statistik_ms' => $statistikTime,
                'pencarian_ms' => $searchTime,
                'konteks_ms' => $contextTime,
                'ollama_ms' => $ollamaTime,
                'format_ms' => $formatTime,
                'total_ms' => $totalTime,
                'intent' => $intent,
                'kata_kunci' => $kataKunci,
                'tahun' => $tahun,
                'jumlah_arsip_ditemukan' => $jumlahArsipDitemukan,
                'sumber_jawaban' => $sumberJawaban,
                'ada_referensi_konteks' => $adaReferensiKonteks,
            ]);

            return response()->json([
                'success' => true,
                'jawaban' => $jawabanAI,
                'sumber_arsip' => $sumberArsip,
                'jumlah_arsip' => count($sumberArsip),
                'total_arsip_relevan' => $arsipRelevan->count(),
                'sumber_jawaban' => $sumberJawaban,
                'intent' => $intent,
                'kata_kunci' => $kataKunci,
                'jumlah_arsip_ditemukan' => $jumlahArsipDitemukan,
                'provider' => $this->layananAI->dapatkanProvider(),
                'waktu_proses_ms' => $totalTime,
            ]);
        } catch (\Exception $e) {
            $totalTime = round((microtime(true) - $startTime) * 1000, 2);
            Log::error('Exception di tanyaAI', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'total_waktu_ms' => $totalTime,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
                'jawaban' => 'Maaf, terjadi kesalahan sistem. Silakan coba lagi nanti.',
                'sumber_arsip' => [],
            ]);
        }
    }

    /**
     * Export hasil pencarian ke Excel (fleksibel berdasarkan permintaan)
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'nullable|string|max:1000',
            'kolom' => 'nullable|array',
            'kolom.*' => 'in:id,nomor_surat,nama_arsip,perihal_surat,tanggal_arsip,status,status_ketersediaan,nama_jenis,ruangan,lemari_nama,rak_nama,isi_dokumen',
            'format' => 'nullable|in:ringkas,lengkap',
        ]);

        $pertanyaan = $request->input('pertanyaan', '');
        $kolomYangDipilih = $request->input('kolom', [
            'nomor_surat', 'nama_arsip', 'perihal_surat', 'tanggal_arsip', 
            'status', 'nama_jenis', 'ruangan'
        ]);
        $format = $request->input('format', 'ringkas');

        // Cari arsip
        $arsip = $pertanyaan 
            ? $this->layananPencarian->cariArsip($pertanyaan)
            : $this->layananPencarian->cariArsip('');

        if ($arsip->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data untuk diexport.',
            ]);
        }

        // Siapkan data berdasarkan kolom yang dipilih
        $data = [];
        foreach ($arsip as $item) {
            $row = [];
            foreach ($kolomYangDipilih as $kolom) {
                $value = $item->$kolom ?? '';
                
                // Format khusus untuk isi_dokumen jika format ringkas
                if ($kolom === 'isi_dokumen' && $format === 'ringkas') {
                    $layananEkstraksi = new \App\Services\LayananEkstraksiDokumen();
                    $value = $layananEkstraksi->ekstrakRingkasan($value, 500);
                }
                
                $row[$kolom] = $value;
            }
            $data[] = $row;
        }

        // Buat file CSV
        $filename = 'ai_arsip_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, array_map(function($kolom) {
            return ucwords(str_replace('_', ' ', $kolom));
        }, $kolomYangDipilih));

        // Data
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export hasil jawaban AI ke Excel
     */
    public function exportJawabanExcel(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'jawaban' => 'required|string',
            'sumber_arsip' => 'required|array',
        ]);

        $pertanyaan = $request->input('pertanyaan');
        $jawaban = $request->input('jawaban');
        $sumberArsip = $request->input('sumber_arsip');

        $filename = 'ai_jawaban_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, ['Pertanyaan', 'Jawaban AI', 'Timestamp']);

        // Data utama
        fputcsv($handle, [
            $pertanyaan,
            $jawaban,
            now()->format('d M Y H:i:s'),
        ]);

        // Empty row
        fputcsv($handle, []);

        // Header sumber arsip
        fputcsv($handle, ['Sumber Arsip']);

        // Data sumber arsip
        foreach ($sumberArsip as $sumber) {
            fputcsv($handle, [
                $sumber['nomor_surat'] ?? '',
                $sumber['nama_arsip'] ?? '',
                $sumber['perihal'] ?? '',
                $sumber['excerpt'] ?? '',
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Dapatkan daftar jenis arsip untuk filter
     */
    public function getJenisArsip()
    {
        $jenisArsip = DB::table('klasifikasi')
            ->select('id', 'nama')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $jenisArsip,
        ]);
    }

    /**
     * Dapatkan daftar lokasi untuk filter
     */
    public function getLokasi()
    {
        $lokasi = DB::table('lokasi_simpan')
            ->select('id', 'ruangan')
            ->orderBy('ruangan')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $lokasi,
        ]);
    }
}
