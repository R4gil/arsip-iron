<?php

namespace App\Services;

use App\Models\AktivitasLog;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public function log(string $aktivitas, ?string $detail = null): AktivitasLog
    {
        return AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => $aktivitas,
            'detail' => $detail,
            'ip_address' => Request::ip(),
        ]);
    }
}