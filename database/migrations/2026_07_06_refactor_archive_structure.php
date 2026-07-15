<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lemari')) {
            Schema::create('lemari', function (Blueprint $table) {
                $table->id('lemari_id');
                $table->unsignedBigInteger('ruangarsip_id')->nullable();
                $table->string('lemari_nama')->nullable();
                $table->text('lemari_keterangan')->nullable();
                $table->timestamps();

                if (Schema::hasTable('lokasi_simpan')) {
                    $table->foreign('ruangarsip_id')->references('id')->on('lokasi_simpan')->nullOnDelete();
                }
            });
        }

        if (!Schema::hasTable('rak')) {
            Schema::create('rak', function (Blueprint $table) {
                $table->id('rak_id');
                $table->unsignedBigInteger('lemari_id')->nullable();
                $table->string('rak_nama')->nullable();
                $table->text('rak_keterangan')->nullable();
                $table->timestamps();

                if (Schema::hasTable('lemari')) {
                    $table->foreign('lemari_id')->references('lemari_id')->on('lemari')->nullOnDelete();
                }
            });
        }

        if (!Schema::hasTable('peminjaman_arsip')) {
            Schema::create('peminjaman_arsip', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('arsip_id')->nullable();
                $table->string('nama_peminjam')->nullable();
                $table->string('divisi_peminjam')->nullable();
                $table->date('tanggal_keluar')->nullable();
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

        if (!Schema::hasTable('aktivitas_log')) {
            Schema::create('aktivitas_log', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('aktivitas')->nullable();
                $table->unsignedBigInteger('arsip_id')->nullable();
                $table->timestamp('tanggal')->useCurrent();
                $table->timestamps();

                if (Schema::hasTable('users')) {
                    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                }
                if (Schema::hasTable('arsip')) {
                    $table->foreign('arsip_id')->references('id')->on('arsip')->nullOnDelete();
                }
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'nama_pengguna')) { $table->string('nama_pengguna')->nullable()->after('name'); }
            if (!Schema::hasColumn('users', 'username')) { $table->string('username')->nullable()->after('nama_pengguna'); }
            if (!Schema::hasColumn('users', 'unit_kerja')) { $table->string('unit_kerja')->nullable()->after('username'); }
            if (!Schema::hasColumn('users', 'role')) { $table->string('role')->default('Operator')->after('unit_kerja'); }
            if (!Schema::hasColumn('users', 'last_login')) { $table->timestamp('last_login')->nullable()->after('role'); }
            if (!Schema::hasColumn('users', 'last_logout')) { $table->timestamp('last_logout')->nullable()->after('last_login'); }
        });

        Schema::table('arsip', function (Blueprint $table) {
            if (!Schema::hasColumn('arsip', 'nama_arsip')) { $table->string('nama_arsip')->nullable(); }
            if (!Schema::hasColumn('arsip', 'nomor_surat')) { $table->string('nomor_surat')->nullable(); }
            if (!Schema::hasColumn('arsip', 'perihal_surat')) { $table->text('perihal_surat')->nullable(); }
            if (!Schema::hasColumn('arsip', 'tanggal_arsip')) { $table->date('tanggal_arsip')->nullable(); }
            if (!Schema::hasColumn('arsip', 'tahun')) { $table->integer('tahun')->nullable(); }
            if (!Schema::hasColumn('arsip', 'tahun_arsip')) { $table->integer('tahun_arsip')->nullable(); }
            if (!Schema::hasColumn('arsip', 'jenis_arsip_id')) { $table->unsignedBigInteger('jenis_arsip_id')->nullable(); }
            if (!Schema::hasColumn('arsip', 'lokasi_id')) { $table->unsignedBigInteger('lokasi_id')->nullable(); }
            if (!Schema::hasColumn('arsip', 'status')) { $table->string('status')->default('Aktif'); }
            if (!Schema::hasColumn('arsip', 'status_ketersediaan')) { $table->string('status_ketersediaan')->default('Tersedia'); }
            if (!Schema::hasColumn('arsip', 'file_arsip')) { $table->string('file_arsip')->nullable(); }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aktivitas_log');
        Schema::dropIfExists('peminjaman_arsip');
        Schema::dropIfExists('rak');
        Schema::dropIfExists('lemari');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'nama_pengguna')) { $table->dropColumn('nama_pengguna'); }
            if (Schema::hasColumn('users', 'username')) { $table->dropColumn('username'); }
            if (Schema::hasColumn('users', 'unit_kerja')) { $table->dropColumn('unit_kerja'); }
            if (Schema::hasColumn('users', 'role')) { $table->dropColumn('role'); }
            if (Schema::hasColumn('users', 'last_login')) { $table->dropColumn('last_login'); }
            if (Schema::hasColumn('users', 'last_logout')) { $table->dropColumn('last_logout'); }
        });

        Schema::table('arsip', function (Blueprint $table) {
            if (Schema::hasColumn('arsip', 'nama_arsip')) { $table->dropColumn('nama_arsip'); }
            if (Schema::hasColumn('arsip', 'nomor_surat')) { $table->dropColumn('nomor_surat'); }
            if (Schema::hasColumn('arsip', 'perihal_surat')) { $table->dropColumn('perihal_surat'); }
            if (Schema::hasColumn('arsip', 'tanggal_arsip')) { $table->dropColumn('tanggal_arsip'); }
            if (Schema::hasColumn('arsip', 'tahun')) { $table->dropColumn('tahun'); }
            if (Schema::hasColumn('arsip', 'tahun_arsip')) { $table->dropColumn('tahun_arsip'); }
            if (Schema::hasColumn('arsip', 'jenis_arsip_id')) { $table->dropColumn('jenis_arsip_id'); }
            if (Schema::hasColumn('arsip', 'lokasi_id')) { $table->dropColumn('lokasi_id'); }
            if (Schema::hasColumn('arsip', 'status')) { $table->dropColumn('status'); }
            if (Schema::hasColumn('arsip', 'status_ketersediaan')) { $table->dropColumn('status_ketersediaan'); }
            if (Schema::hasColumn('arsip', 'file_arsip')) { $table->dropColumn('file_arsip'); }
        });
    }
};
