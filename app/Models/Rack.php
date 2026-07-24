<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['lemari_id', 'rak_nama', 'rak_keterangan'])]
class Rack extends Model
{
    use HasFactory;

    protected $table = 'rak';
    protected $primaryKey = 'rak_id';
    public $timestamps = true;

    public function cabinet()
    {
        return $this->belongsTo(Cabinet::class, 'lemari_id');
    }

    public function archives()
    {
        return $this->hasMany(Archive::class, 'rack_id');
    }
}
