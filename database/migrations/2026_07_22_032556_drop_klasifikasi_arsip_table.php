<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('klasifikasi_arsip');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('klasifikasi_arsip')) {
            Schema::create('klasifikasi_arsip', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id')->nullable(); 
                $table->string('kode', 50);
                $table->text('nama');
                $table->timestamps();

                $table->foreign('parent_id')->references('id')->on('klasifikasi_arsip')->onDelete('cascade');
            });
        }
    }
};
