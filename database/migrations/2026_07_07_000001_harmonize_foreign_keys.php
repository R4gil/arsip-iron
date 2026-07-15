<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $maps = [
            ['table' => 'peminjaman_arsip', 'column' => 'petugas_keluar_id', 'ref_table' => 'users', 'ref_column' => 'id'],
            ['table' => 'peminjaman_arsip', 'column' => 'petugas_masuk_id', 'ref_table' => 'users', 'ref_column' => 'id'],
            ['table' => 'peminjaman_arsip', 'column' => 'arsip_id', 'ref_table' => 'arsip', 'ref_column' => 'id'],
            ['table' => 'lemari', 'column' => 'ruangarsip_id', 'ref_table' => 'lokasi_simpan', 'ref_column' => 'id'],
            ['table' => 'rak', 'column' => 'lemari_id', 'ref_table' => 'lemari', 'ref_column' => 'lemari_id'],
            ['table' => 'arsip', 'column' => 'jenis_arsip_id', 'ref_table' => 'jenis_arsip', 'ref_column' => 'id'],
            ['table' => 'arsip', 'column' => 'lokasi_id', 'ref_table' => 'lokasi_simpan', 'ref_column' => 'id'],
        ];

        foreach ($maps as $m) {
            if (! Schema::hasTable($m['table'])) {
                continue;
            }

            if (! Schema::hasColumn($m['table'], $m['column'])) {
                continue;
            }

            // drop existing foreign key constraint if any
            $constraint = DB::selectOne("
                SELECT CONSTRAINT_NAME AS name
                FROM information_schema.key_column_usage
                WHERE table_schema = DATABASE()
                  AND table_name = '" . $m['table'] . "'
                  AND column_name = '" . $m['column'] . "'
                  AND referenced_table_name IS NOT NULL
            ");

            if ($constraint && isset($constraint->name)) {
                try {
                    DB::statement("ALTER TABLE `{$m['table']}` DROP FOREIGN KEY `{$constraint->name}`");
                } catch (\Exception $e) {
                    // ignore
                }
            }

            // normalize column type to BIGINT UNSIGNED NULL (safe for FK columns)
            try {
                DB::statement("ALTER TABLE `{$m['table']}` MODIFY `{$m['column']}` BIGINT UNSIGNED NULL");
            } catch (\Exception $e) {
                // ignore failures (e.g. driver not MySQL or permission issue)
            }

            // add foreign key if referenced table exists
            if (Schema::hasTable($m['ref_table'])) {
                $fkName = "fk_{$m['table']}_{$m['column']}";
                try {
                    DB::statement("ALTER TABLE `{$m['table']}` ADD CONSTRAINT `{$fkName}` FOREIGN KEY (`{$m['column']}`) REFERENCES `{$m['ref_table']}`(`{$m['ref_column']}`) ON DELETE SET NULL ON UPDATE CASCADE");
                } catch (\Exception $e) {
                    // ignore if constraint addition fails
                }
            }
        }
    }

    public function down(): void
    {
        // no-op: manual rollback recommended
    }
};
