<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_arsip')->unique();
            $table->string('nama_arsip');
            $table->text('uraian')->nullable();
            $table->foreignId('classification_id')->constrained('classifications')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->foreignId('cabinet_id')->constrained('cabinets')->cascadeOnDelete();
            $table->foreignId('rack_id')->constrained('racks')->cascadeOnDelete();
            $table->integer('tahun');
            $table->enum('status', ['tersedia', 'dipinjam', 'inaktif'])->default('tersedia');
            $table->date('tanggal_arsip');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
