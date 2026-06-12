<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nomor_arsip', 'nama_arsip', 'uraian', 'classification_id', 'location_id', 'cabinet_id', 'rack_id', 'tahun', 'status', 'tanggal_arsip'])]
class Archive extends Model
{
    use HasFactory;

    protected $casts = [
        'tanggal_arsip' => 'date',
    ];

    public function classification()
    {
        return $this->belongsTo(Classification::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
}
