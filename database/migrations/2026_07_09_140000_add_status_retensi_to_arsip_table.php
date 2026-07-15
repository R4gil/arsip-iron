<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom status_retensi untuk tracking proses retensi
     */
    public function up(): void
    {
        if (!Schema::hasTable('arsip')) {
            return;
        }

        Schema::table('arsip', function (Blueprint $table) {
            if (!Schema::hasColumn('arsip', 'status_retensi')) {
                $table->string('status_retensi')->default('Belum Memasuki Masa Retensi')->after('tanggal_retensi');
            }
        });
    }

    public function down(): void
    {
        // Rollback sengaja dikosongkan agar kolom tidak terhapus secara tidak sengaja.
    }
};
