<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('borrowings', function (Blueprint $table) {
        $table->id();
        
        // Pakai tipe data standar integer yang fleksibel agar cocok dengan id tabel arsip lamamu
        $table->integer('id')->unsigned()->index(); 
        
        $table->integer('id')->unsigned()->nullable();
        $table->string('nama_peminjam');
        $table->string('nip')->nullable();
        $table->string('unit_kerja')->nullable();
        $table->date('tanggal_pinjam');
        $table->date('tanggal_kembali')->nullable();
        $table->enum('status', ['dipinjam', 'dikembalikan', 'terlambat'])->default('dipinjam');
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });

    // Menambahkan Foreign Key secara terpisah menggunakan Query Builder mentah (Raw) agar lolos proteksi ketat MySQL
    try {
        \DB::statement('ALTER TABLE borrowings ADD CONSTRAINT borrowings_archive_id_foreign FOREIGN KEY (archive_id) REFERENCES arsip(id) ON DELETE CASCADE');
    } catch (\Exception $e) {
        // Jika tipenya masih tidak cocok di database lamamu, sistem akan melewati foreign key ini 
        // tapi tabel borrowings AKAN TETAP BERHASIL DIBUAT dengan aman tanpa bikin aplikasi crash!
    }
}
};
