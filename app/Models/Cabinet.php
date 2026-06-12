<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['location_id', 'nama_lemari'])]
class Cabinet extends Model
{
    use HasFactory;

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function racks()
    {
        return $this->hasMany(Rack::class);
    }

    public function archives()
    {
        return $this->hasMany(Archive::class);
    }
}
