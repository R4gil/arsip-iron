<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KlasifikasiArsipSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Matikan pengecekan Foreign Key & Kosongkan Tabel
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
        DB::table('klasifikasi_arsip')->truncate();

        $csvFile = database_path('klasifikasi.csv'); 
        
        if (!file_exists($csvFile)) {
            $this->command->error("File klasifikasi.csv tidak ditemukan di folder database/ !");
            return;
        }

        $fileHandle = fopen($csvFile, 'r');
        
        $kodeToIdMap = [];
        $insertedCount = 0;

        $this->command->info("Memproses CSV dengan proteksi ketat hierarki level...");

        while (($row = fgetcsv($fileHandle, 1000, ',')) !== FALSE) {
            if (empty($row) || count($row) < 2) {
                continue;
            }

            $kodeRaw = trim($row[0]); 
            $namaRaw = trim($row[1]); 

            // FILTER KETAT 1: Validasi format kode kearsipan resmi
            if (!preg_match('/^[A-Z]{2,3}(\.[0-9]{2}){0,2}$/', $kodeRaw)) {
                continue;
            }

            // FILTER KETAT 2: Anti-Duplikat penjelasan panjang
            if (isset($kodeToIdMap[$kodeRaw])) {
                continue;
            }

            if (empty($namaRaw) || strlen($namaRaw) <= 2) {
                continue;
            }

            // Bersihkan teks nama dari spasi biner bawaan Excel
            $namaRaw = str_replace(chr(160), ' ', $namaRaw);
            $namaRaw = preg_replace('/\x{00a0}/u', ' ', $namaRaw);
            $namaClean = mb_convert_encoding($namaRaw, 'UTF-8', 'UTF-8,Windows-1252,ISO-8859-1');
            $namaClean = preg_replace('/[[:cntrl:]]/', '', $namaClean);
            $nama = trim(preg_replace('/\s+/u', ' ', $namaClean));

            // --- LOGIKA PENENTUAN LEVEL SECARA STRUKTURAL ---
            $parentId = null;
            
            if (strpos($kodeRaw, '.') !== false) {
                // Jika kodenya punya titik (Level 2 atau Level 3), potong ujungnya untuk cari induknya
                $lastDotPos = strrpos($kodeRaw, '.');
                $parentKode = substr($kodeRaw, 0, $lastDotPos);

                if (isset($kodeToIdMap[$parentKode])) {
                    $parentId = $kodeToIdMap[$parentKode];
                } else {
                    // FAILSAFE: Jika induknya belum terdaftar di DB (karena urutan CSV lompat),
                    // buatkan baris induk bayangan terlebih dahulu agar anak ini tidak menjadi Level 1 (parent_id NULL)
                    $fixNamaParent = "Induk " . $parentKode;
                    if ($parentKode === 'GR') $fixNamaParent = 'KEIMIGRASIAN';
                    if ($parentKode === 'HK') $fixNamaParent = 'HUKUM';
                    if ($parentKode === 'KU') $fixNamaParent = 'KEUANGAN';
                    if ($parentKode === 'KP') $fixNamaParent = 'KEPEGAWAIAN';
                    if ($parentKode === 'PR') $fixNamaParent = 'PERENCANAAN';
                    if ($parentKode === 'TI') $fixNamaParent = 'TEKNOLOGI INFORMASI';
                    if ($parentKode === 'UM') $fixNamaParent = 'UMUM';

                    // Cek lagi apakah di potongan level di atasnya masih ada induk lagi (misal dari Level 3 ke Level 2)
                    $grandParentId = null;
                    if (strpos($parentKode, '.') !== false) {
                        $grandDotPos = strrpos($parentKode, '.');
                        $grandParentKode = substr($parentKode, 0, $grandDotPos);
                        if (isset($kodeToIdMap[$grandParentKode])) {
                            $grandParentId = $kodeToIdMap[$grandParentKode];
                        }
                    }

                    $idParentBaru = DB::table('klasifikasi_arsip')->insertGetId([
                        'parent_id'  => $grandParentId,
                        'kode'       => $parentKode,
                        'nama'       => $fixNamaParent,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $kodeToIdMap[$parentKode] = $idParentBaru;
                    $parentId = $idParentBaru;
                }
            }

            // Masukkan data asli ke database
            $insertedId = DB::table('klasifikasi_arsip')->insertGetId([
                'parent_id'  => $parentId,
                'kode'       => $kodeRaw,
                'nama'       => Str::limit($nama, 250, '...'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $kodeToIdMap[$kodeRaw] = $insertedId;
            $insertedCount++;
        }

        fclose($fileHandle);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        
        $this->command->info("BERHASIL! Hierarki dikunci total. Total data valid: {$insertedCount}");
    }
}