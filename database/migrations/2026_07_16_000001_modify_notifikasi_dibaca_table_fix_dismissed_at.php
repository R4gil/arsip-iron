<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi_dibaca', function (Blueprint $table) {
            $table->dropColumn('dismissed_at');
        });
        
        Schema::table('notifikasi_dibaca', function (Blueprint $table) {
            $table->timestamp('dismissed_at')->nullable()->after('notification_key');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi_dibaca', function (Blueprint $table) {
            $table->dropColumn('dismissed_at');
        });
        
        Schema::table('notifikasi_dibaca', function (Blueprint $table) {
            $table->timestamp('dismissed_at')->useCurrent()->after('notification_key');
        });
    }
};
