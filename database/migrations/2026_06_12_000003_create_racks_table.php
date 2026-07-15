<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rak', function (Blueprint $table) {
            $table->id('rak_id');
            $table->unsignedBigInteger('lemari_id')->nullable();
            $table->string('rak_nama');
            $table->text('rak_keterangan')->nullable();
            $table->timestamps();

            $table->foreign('lemari_id')->references('lemari_id')->on('lemari')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rak');
    }
};
