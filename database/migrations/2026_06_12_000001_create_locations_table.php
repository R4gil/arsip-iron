<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lokasi_simpan', function (Blueprint $table) {
            $table->id();
            $table->string('ruangan');
            $table->string('lemari')->default('-');
            $table->string('rak')->default('-');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lokasi_simpan');
    }
};
