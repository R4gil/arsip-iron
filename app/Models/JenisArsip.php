<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama', 'kode', 'masa_retensi_tahun', 'keterangan'])]
class JenisArsip extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'klasifikasi';

    protected $fillable = [
        'nama',
        'kode',
        'masa_retensi_tahun',
        'keterangan',
    ];

    public function archives()
    {
        return $this->hasMany(Archive::class, 'jenis_arsip_id');
    }

    /**
     * Get the value of the model's name column (alias for compatibility)
     *
     * @return string
     */
    public function getNamaJenisAttribute()
    {
        return $this->nama;
    }
}
