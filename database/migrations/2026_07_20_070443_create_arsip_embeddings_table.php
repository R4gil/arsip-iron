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
        Schema::create('arsip_embeddings', function (Blueprint $table) {
            $table->id();
            $table->integer('arsip_id')->unsigned()->unique();
            $table->text('embedding'); // JSON array of vector
            $table->string('model')->default('ollama/qwen3:8b'); // Model used for embedding
            $table->integer('dimension')->default(768); // Vector dimension
            $table->timestamps();
            
            // Foreign key constraint removed due to type mismatch
            // Will be added manually if needed
            $table->index('arsip_id');
            $table->index('model');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_embeddings');
    }
};
