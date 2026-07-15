<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman_arsip';

    protected $fillable = [
        'arsip_id',
        'nama_peminjam',
        'divisi_peminjam',
        'tanggal_keluar',
        'petugas_keluar_id',
        'tanggal_masuk',
        'petugas_masuk_id',
        'status_pinjam',
        'keterangan_kondisi'
    ];

    public function archive()
    {
        return $this->belongsTo(Archive::class, 'arsip_id');
    }
}