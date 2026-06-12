<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('racks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabinet_id')->constrained('cabinets')->cascadeOnDelete();
            $table->string('nama_rak');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('racks');
    }
};
