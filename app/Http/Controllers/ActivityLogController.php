<?php

namespace App\Http\Controllers;

use App\Models\AktivitasLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AktivitasLog::with('user')->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by activity type
        if ($request->has('aktivitas') && $request->aktivitas != '') {
            $query->where('aktivitas', 'like', '%' . $request->aktivitas . '%');
        }

        // Filter by date range
        if ($request->has('tanggal_mulai') && $request->tanggal_mulai != '') {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->has('tanggal_selesai') && $request->tanggal_selesai != '') {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        $activities = $query->simplePaginate($request->get('per_page', 10))->withQueryString();
        $users = User::all();

        return view('activity-log.index', compact('activities', 'users'));
    }

    public function clear()
    {
        try {
            $deletedCount = AktivitasLog::count();
            AktivitasLog::truncate();

            // Log this action
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Clear Log',
                'detail' => "Menghapus semua log aktivitas ({$deletedCount} records)",
                'ip_address' => request()->ip(),
            ]);

            return redirect()->route('activity-log.index')->with('success', "Semua log aktivitas berhasil dihapus ({$deletedCount} records).");
        } catch (\Exception $e) {
            return redirect()->route('activity-log.index')->with('error', 'Gagal menghapus log: ' . $e->getMessage());
        }
    }
}