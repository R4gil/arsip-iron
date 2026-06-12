<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['nama_lokasi', 'keterangan'])]
class Location extends Model
{
    use HasFactory;

    public function cabinets()
    {
        return $this->hasMany(Cabinet::class);
    }

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }
}
