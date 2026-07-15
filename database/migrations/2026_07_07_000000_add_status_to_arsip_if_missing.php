<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('arsip')) {
            return;
        }

        Schema::table('arsip', function (Blueprint $table) {
            if (!Schema::hasColumn('arsip', 'status')) {
                $table->string('status')->default('Aktif')->after('tahun_arsip');
            }

            if (!Schema::hasColumn('arsip', 'status_ketersediaan')) {
                $table->string('status_ketersediaan')->default('Tersedia')->after('status');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('arsip')) {
            return;
        }

        Schema::table('arsip', function (Blueprint $table) {
            if (Schema::hasColumn('arsip', 'status_ketersediaan')) {
                $table->dropColumn('status_ketersediaan');
            }

            if (Schema::hasColumn('arsip', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
