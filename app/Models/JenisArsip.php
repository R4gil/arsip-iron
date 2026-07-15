<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama_jenis', 'masa_retensi_tahun', 'keterangan'])]
class JenisArsip extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'jenis_arsip';

    protected $fillable = [
        'nama_jenis',
        'masa_retensi_tahun',
        'keterangan',
    ];

    public function archives()
    {
        return $this->hasMany(Archive::class, 'jenis_arsip_id');
    }
}
