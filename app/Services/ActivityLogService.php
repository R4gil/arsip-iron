<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    public function log(string $aktivitas, ?string $detail = null): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => $aktivitas,
            'detail' => $detail,
            'ip_address' => Request::ip(),
        ]);
    }
}
