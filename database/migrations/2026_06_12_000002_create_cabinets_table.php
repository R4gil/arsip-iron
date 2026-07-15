<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lemari', function (Blueprint $table) {
            $table->id('lemari_id');
            $table->unsignedBigInteger('ruangarsip_id')->nullable();
            $table->string('lemari_nama');
            $table->text('lemari_keterangan')->nullable();
            $table->timestamps();

            $table->foreign('ruangarsip_id')->references('id')->on('lokasi_simpan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lemari');
    }
};
