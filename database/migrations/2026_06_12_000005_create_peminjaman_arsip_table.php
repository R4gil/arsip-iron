<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('peminjaman_arsip')) {
            Schema::create('peminjaman_arsip', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('arsip_id')->nullable();
                $table->string('nama_peminjam');
                $table->string('divisi_peminjam')->nullable();
                $table->date('tanggal_keluar');
                $table->unsignedBigInteger('petugas_keluar_id')->nullable();
                $table->date('tanggal_masuk')->nullable();
                $table->unsignedBigInteger('petugas_masuk_id')->nullable();
                $table->enum('status_pinjam', ['Dipinjam', 'Dikembalikan', 'Terlambat'])->default('Dipinjam');
                $table->text('keterangan_kondisi')->nullable();
                $table->timestamps();

                if (Schema::hasTable('arsip')) {
                    $table->foreign('arsip_id')->references('id')->on('arsip')->cascadeOnDelete();
                }
                if (Schema::hasTable('users')) {
                    $table->foreign('petugas_keluar_id')->references('id')->on('users')->nullOnDelete();
                    $table->foreign('petugas_masuk_id')->references('id')->on('users')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman_arsip');
    }
};
