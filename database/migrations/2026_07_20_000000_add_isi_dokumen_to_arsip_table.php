<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            if (!Schema::hasColumn('arsip', 'isi_dokumen')) {
                $table->longText('isi_dokumen')->nullable()->after('file_arsip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            if (Schema::hasColumn('arsip', 'isi_dokumen')) {
                $table->dropColumn('isi_dokumen');
            }
        });
    }
};
