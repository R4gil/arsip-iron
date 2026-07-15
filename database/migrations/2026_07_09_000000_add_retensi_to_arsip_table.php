<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hanya MENAMBAH kolom baru. Tidak menghapus tabel, kolom, atau data yang sudah ada.
     */
    public function up(): void
    {
        if (!Schema::hasTable('arsip')) {
            return;
        }

        Schema::table('arsip', function (Blueprint $table) {
            if (!Schema::hasColumn('arsip', 'masa_retensi')) {
                $table->string('masa_retensi')->nullable();
            }
        });

        Schema::table('arsip', function (Blueprint $table) {
            if (!Schema::hasColumn('arsip', 'tanggal_retensi')) {
                $table->date('tanggal_retensi')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Rollback sengaja dikosongkan agar kolom tidak terhapus secara tidak sengaja.
    }
};
