<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KlasifikasiSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('klasifikasi.csv');

        if (!file_exists($csvFile)) {
            $this->command->error("File klasifikasi.csv tidak ditemukan di folder database/");
            return;
        }

        $handle = fopen($csvFile, 'r');
        if ($handle === false) {
            $this->command->error("Gagal membuka file klasifikasi.csv");
            return;
        }

        $inserted = 0;
        $isFirstRow = true;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            // Skip baris header (baris pertama)
            if ($isFirstRow) {
                $isFirstRow = false;
                continue;
            }

            if (count($row) < 2) continue;

            $kode = trim($row[0]);
            $nama = trim($row[1]);
            if (mb_strlen($nama) > 255) {
                $nama = mb_substr($nama, 0, 255);
            }

            if ($kode === '' || $nama === '') continue;

            // Jangan import data header (kode="Kode" atau nama="Keterangan")
            if (strtolower($kode) === 'kode' || strtolower($nama) === 'keterangan') continue;

            DB::table('klasifikasi')->updateOrInsert(
                ['kode' => $kode],
                ['nama' => $nama, 'updated_at' => now(), 'created_at' => now()]
            );

            $inserted++;
        }

        fclose($handle);

        echo "KlasifikasiSeeder: Selesai. Total baris terproses: {$inserted}\n";
    }
}
