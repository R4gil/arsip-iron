<?php

namespace App\Models;

use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nomor_surat', 'nama_arsip', 'perihal_surat', 'jenis_arsip_id', 'lokasi_id', 'cabinet_id', 'rack_id', 'tanggal_arsip', 'tahun_arsip', 'status', 'status_ketersediaan', 'file_arsip', 'masa_retensi', 'tanggal_retensi', 'status_retensi'])]
class Archive extends Model
{
    use HasFactory;

    protected $table = 'arsip';

    protected $casts = [
        'tanggal_arsip' => 'date',
        'tanggal_retensi' => 'date',
        'tahun_arsip' => 'integer',
        // Tambahkan dua baris ini agar created_at dan updated_at bisa di-format()
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function jenisArsip()
    {
        return $this->belongsTo(JenisArsip::class, 'jenis_arsip_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Location::class, 'lokasi_id');
    }

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class, 'cabinet_id');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }

    public function borrowings()
    {
        return $this->hasMany(Peminjaman::class, 'arsip_id');
    }
}