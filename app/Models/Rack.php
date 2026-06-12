<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['cabinet_id', 'nama_rak'])]
class Rack extends Model
{
    use HasFactory;

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }
}
