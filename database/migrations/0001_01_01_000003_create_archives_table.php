<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arsip', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->string('nama_arsip');
            $table->text('perihal_surat')->nullable();
            $table->date('tanggal_arsip');
            $table->integer('tahun_arsip');
            $table->unsignedBigInteger('jenis_arsip_id')->nullable();
            $table->unsignedBigInteger('lokasi_id')->nullable();
            $table->string('status')->default('Aktif');
            $table->string('status_ketersediaan')->default('Tersedia');
            $table->string('file_arsip')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsip');
    }
};
