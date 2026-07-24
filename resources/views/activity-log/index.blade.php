@extends('layouts.dashboard')

@section('title', 'Log Aktivitas')

@section('content')
@include('partials.page-header', [
    'title' => 'Log Aktivitas',
    'subtitle' => 'Riwayat aktivitas seluruh pengguna sistem.',
])

<div class="is-card mb-3">
    <div class="is-card-body is-form py-3">
        <form method="GET" action="{{ route('activity-log.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Pengguna</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->nama_pengguna ?? $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Aktivitas</label>
                    <input type="text" name="aktivitas" value="{{ request('aktivitas') }}" class="form-control form-control-sm" placeholder="Cari aktivitas...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100">Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="is-card">
    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background: #f8fafc;">
        <form action="{{ route('activity-log.clear') }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus semua log aktivitas? Aksi ini tidak dapat dibatalkan.')">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);">
                <i class="fas fa-trash-alt me-1"></i> Clear Log
            </button>
        </form>
        <div class="d-flex gap-2">
            <a href="{{ route('activity-log.exportExcel', request()->all()) }}" class="btn btn-success btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('activity-log.exportPDF', request()->all()) }}" class="btn btn-danger btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table is-table mb-0">
            <thead>
                <tr>
                    <th class="ps-3">#</th>
                    <th>Tanggal & Waktu</th>
                    <th>Pengguna</th>
                    <th>Aktivitas</th>
                    <th>Detail</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $key => $activity)
                <tr>
                    <td class="ps-3">{{ $activities->firstItem() + $key }}</td>
                    <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('d-m-Y H:i:s') }}</td>
                    <td>
                        @if($activity->user)
                            <div class="fw-bold">{{ $activity->user->nama_pengguna ?? $activity->user->name }}</div>
                            <div class="text-muted small">{{ $activity->user->role ?? '—' }}</div>
                        @else
                            <span class="text-muted">Pengguna dihapus</span>
                        @endif
                    </td>
                    <td><span class="is-badge bg-primary">{{ $activity->aktivitas }}</span></td>
                    <td>{{ $activity->detail ?? '—' }}</td>
                    <td><span class="text-muted small">{{ $activity->ip_address ?? '—' }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" class="is-empty">Data aktivitas tidak ditemukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($activities->hasPages())
        <div class="is-card-body border-top">{{ $activities->withQueryString()->links('pagination::simple-bootstrap-4') }}</div>
    @endif
</div>

<style>
.pagination{margin-bottom:0!important;gap:8px!important;}
.pagination .page-link{background:#fff!important;border:1.5px solid #e2e8f0!important;color:#334155!important;font-weight:600!important;font-size:0.85rem!important;padding:0.5rem 0.85rem!important;border-radius:8px!important;transition:all 0.2s ease!important;text-decoration:none!important;}
.pagination .page-item:first-child .page-link,.pagination .page-item:last-child .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#d4af37!important;color:#1d2127!important;font-weight:700!important;padding:0.5rem 1.2rem!important;box-shadow:0 2px 8px rgba(212,175,55,0.25)!important;}
.pagination .page-item:first-child .page-link:hover,.pagination .page-item:last-child .page-link:hover{background:linear-gradient(135deg,#f3e5ab,#d4af37)!important;border-color:#aa7c11!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(212,175,55,0.4)!important;}
.pagination .page-item.active .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#aa7c11!important;color:#1d2127!important;font-weight:700!important;box-shadow:0 2px 8px rgba(212,175,55,0.3)!important;}
.pagination .page-item.disabled .page-link{background:#f8fafc!important;border-color:#e2e8f0!important;color:#94a3b8!important;cursor:not-allowed!important;opacity:0.5!important;}
.pagination .page-link .sr-only{display:none!important;}
</style>
@endsection
