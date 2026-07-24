<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifikasi_dibaca')) {
            Schema::create('notifikasi_dibaca', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('notification_key', 120);
                $table->timestamp('dismissed_at')->nullable();
                $table->unique(['user_id', 'notification_key']);
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_dibaca');
    }
};
