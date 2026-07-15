<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['kode', 'nama'])]
class Classification extends Model
{
    use HasFactory;

    protected $table = 'klasifikasi';
    public $timestamps = true;

    public function archives()
    {
        return $this->hasMany(Archive::class, 'classification_id');
    }
}
