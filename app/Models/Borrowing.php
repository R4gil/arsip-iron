<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['archive_id', 'user_id', 'nama_peminjam', 'nip', 'unit_kerja', 'tanggal_pinjam', 'tanggal_kembali', 'status', 'keterangan'])]
class Borrowing extends Model
{
    use HasFactory;

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
    ];

    public function archive()
    {
        return $this->belongsTo(Archive::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
