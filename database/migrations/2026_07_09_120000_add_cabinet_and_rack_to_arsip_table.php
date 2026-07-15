<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            if (!Schema::hasColumn('arsip', 'cabinet_id')) {
                $table->unsignedBigInteger('cabinet_id')->nullable()->after('lokasi_id');
                $table->foreign('cabinet_id')->references('lemari_id')->on('lemari')->nullOnDelete();
            }
            if (!Schema::hasColumn('arsip', 'rack_id')) {
                $table->unsignedBigInteger('rack_id')->nullable()->after('cabinet_id');
                $table->foreign('rack_id')->references('rak_id')->on('rak')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('arsip', function (Blueprint $table) {
            if (Schema::hasColumn('arsip', 'rack_id')) {
                $table->dropForeign(['rack_id']);
                $table->dropColumn('rack_id');
            }
            if (Schema::hasColumn('arsip', 'cabinet_id')) {
                $table->dropForeign(['cabinet_id']);
                $table->dropColumn('cabinet_id');
            }
        });
    }
};
