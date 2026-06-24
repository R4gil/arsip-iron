<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klasifikasi_arsip', function (Blueprint $table) {
            $table->id();
            // parent_id dibuat nullable karena Level 1 (Utama) tidak punya induk
            $table->unsignedBigInteger('parent_id')->nullable(); 
            $table->string('kode', 50);
            $table->string('nama', 255);
            $table->timestamps();

            // Opsional: Menambahkan foreign key relasi ke dirinya sendiri agar data konsisten
            $table->foreign('parent_id')->references('id')->on('klasifikasi_arsip')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klasifikasi_arsip');
    }
};
