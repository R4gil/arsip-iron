<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'aktivitas', 'detail', 'ip_address'])]
class AktivitasLog extends Model
{
    use HasFactory;

    protected $table = 'aktivitas_log';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
