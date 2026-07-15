<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifikasiDibaca extends Model
{
    public $timestamps = false;

    protected $table = 'notifikasi_dibaca';

    protected $fillable = [
        'user_id',
        'notification_key',
        'dismissed_at',
    ];

    protected $casts = [
        'dismissed_at' => 'datetime',
    ];
}
