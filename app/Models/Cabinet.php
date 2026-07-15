<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ruangarsip_id', 'lemari_nama', 'lemari_keterangan'])]
class Cabinet extends Model
{
    use HasFactory;

    protected $table = 'lemari';
    protected $primaryKey = 'lemari_id';
    public $timestamps = true;

    public function getKeyName()
    {
        return 'lemari_id';
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'ruangarsip_id');
    }

    public function racks()
    {
        return $this->hasMany(Rack::class, 'lemari_id');
    }

    public function archives()
    {
        return $this->hasMany(Archive::class, 'cabinet_id');
    }
}
