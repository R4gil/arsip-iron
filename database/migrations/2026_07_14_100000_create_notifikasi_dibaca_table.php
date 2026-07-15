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
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('notification_key', 120);
                $table->timestamp('dismissed_at')->useCurrent();
                $table->unique(['user_id', 'notification_key']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_dibaca');
    }
};
