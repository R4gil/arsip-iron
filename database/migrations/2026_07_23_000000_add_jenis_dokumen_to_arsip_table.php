<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            if (!Schema::hasColumn('arsip', 'jenis_dokumen')) {
                $table->enum('jenis_dokumen', [
                    'Dokumen Tata Usaha',
                    'Dokumen Keimigrasian',
                    'Dokumen Pengawasan dan Penindakan',
                ])->nullable()->after('jenis_arsip_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            if (Schema::hasColumn('arsip', 'jenis_dokumen')) {
                $table->dropColumn('jenis_dokumen');
            }
        });
    }
};
