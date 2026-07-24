@extends('layouts.dashboard')
@section('title', 'Manajemen Pengguna')
@section('content')
@include('partials.page-header', ['title' => 'Manajemen Pengguna', 'subtitle' => 'Kelola akun pengguna dan hak akses sistem IRON SMART.', 'action' => route('pengguna.create'), 'actionLabel' => 'Tambah Pengguna'])

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body px-0 py-0">

        {{-- Toolbar --}}
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background: #f8fafc; border-radius: 12px 12px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span style="color: #64748b; font-size: 0.8rem;">Tampilkan</span>
                <select class="form-select form-select-sm" style="width: auto; min-width: 70px; border-radius: 6px; border: 1.5px solid #e2e8f0; font-size: 0.8rem; background-color: #fff;"
                    onchange="window.location.href='{{ route('pengguna.index') }}?per_page='+this.value+'&search={{ request('search') }}&role={{ request('role') }}'">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span style="color: #64748b; font-size: 0.8rem;">per halaman</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('pengguna.exportExcel', request()->query()) }}" class="btn btn-success btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('pengguna.exportPDF', request()->query()) }}" class="btn btn-danger btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
            </div>
        </div>

        {{-- Filter --}}
        <div class="px-3 py-2 border-bottom" style="background: #fff;">
            <form method="GET" action="{{ route('pengguna.index') }}" class="d-flex gap-2 flex-wrap align-items-center">
                <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Cari nama, username, atau email..." style="max-width: 280px; border-radius: 8px; border: 1.5px solid #e2e8f0;">
                <select name="role" class="form-select form-select-sm" style="max-width: 160px; border-radius: 8px; border: 1.5px solid #e2e8f0;">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                    <option value="pengguna" {{ request('role') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                </select>
                <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1d2127; border: none; font-weight: 600; border-radius: 8px;">
                    <i class="fas fa-search me-1"></i> Cari
                </button>
                @if(request('search') || request('role'))
                <a href="{{ route('pengguna.index') }}" class="btn btn-sm" style="background: #f1f5f9; color: #64748b; border: 1.5px solid #e2e8f0; font-weight: 600; border-radius: 8px;">
                    <i class="fas fa-times me-1"></i> Reset
                </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table is-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th style="width: 50px;">Foto</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Unit Kerja</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Dibuat</th>
                        <th class="text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $key => $user)
                    <tr>
                        <td class="ps-3 text-muted">{{ $users->firstItem() + $key }}</td>
                        <td>
                            @if($user->profile_photo)
                                <img src="{{ route('profile.photo', $user->profile_photo) }}" alt="Foto" style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%; border: 2px solid #d4af37;">
                            @else
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user" style="color: #94a3b8; font-size: 0.85rem;"></i>
                                </div>
                            @endif
                        </td>
                        <td class="fw-bold">{{ $user->nama_pengguna }}</td>
                        <td>{{ $user->username }}</td>
                        <td style="font-size: 0.8rem;">{{ $user->unit_kerja ?: '—' }}</td>
                        <td style="font-size: 0.8rem;">{{ $user->email }}</td>
                        <td>
                            @php
                                $roleStyle = match($user->role) {
                                    'admin'    => 'background:#fee2e2;color:#dc2626;',
                                    'petugas'  => 'background:#fef9c3;color:#b45309;',
                                    'pengguna' => 'background:#dbeafe;color:#1d4ed8;',
                                    default    => 'background:#f1f5f9;color:#64748b;',
                                };
                            @endphp
                            <span style="display:inline-block;padding:2px 10px;border-radius:20px;font-size:0.75rem;font-weight:700;{{ $roleStyle }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td style="white-space: nowrap; font-size: 0.8rem;">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="text-center" style="white-space: nowrap;">
                            <a href="{{ route('pengguna.edit', $user->id) }}" class="btn btn-sm me-1" style="background:#fffbeb;color:#b45309;border:1.5px solid #fcd34d;font-weight:600;border-radius:8px;">Edit</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('pengguna.destroy', $user->id) }}" class="d-inline-block" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239,68,68,0.25); color: white;">Hapus</button>
                            </form>
                            @else
                            <span class="btn btn-sm disabled" style="background:#f1f5f9;color:#94a3b8;border:1.5px solid #e2e8f0;font-weight:600;border-radius:8px;">Hapus</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="is-empty">Belum ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="card-body border-top d-flex justify-content-between align-items-center" style="background: #f8fafc; border-radius: 0 0 12px 12px;">
        <span style="color: #64748b; font-size: 0.8rem;">&nbsp;</span>
        {{ $users->withQueryString()->links('pagination::simple-bootstrap-4') }}
    </div>
    @endif
</div>

<style>
.form-control:focus,.form-select:focus{border-color:#d4af37!important;box-shadow:0 0 0 3px rgba(212,175,55,0.15)!important;background-color:#fff!important;}
.form-control:hover,.form-select:hover{border-color:#cbd5e1!important;}
.card{transition:box-shadow 0.3s ease;}
.card:hover{box-shadow:0 4px 20px rgba(0,0,0,0.08)!important;}
.table th{background:#f1f5f9!important;color:#334155!important;font-size:0.78rem!important;text-transform:uppercase;letter-spacing:0.03em;border-bottom:2px solid #e2e8f0!important;padding:0.7rem 0.75rem!important;}
.table td{vertical-align:middle!important;font-size:0.85rem;padding:0.65rem 0.75rem!important;}
.table tbody tr:hover{background:#f8fafc!important;}
.table tbody tr:not(:last-child) td{border-bottom:1px solid #f1f5f9!important;}
.pagination{margin-bottom:0!important;gap:8px!important;}
.pagination .page-link{background:#fff!important;border:1.5px solid #e2e8f0!important;color:#334155!important;font-weight:600!important;font-size:0.85rem!important;padding:0.5rem 0.85rem!important;border-radius:8px!important;transition:all 0.2s ease!important;text-decoration:none!important;}
.pagination .page-item:first-child .page-link,.pagination .page-item:last-child .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#d4af37!important;color:#1d2127!important;font-weight:700!important;padding:0.5rem 1.2rem!important;box-shadow:0 2px 8px rgba(212,175,55,0.25)!important;}
.pagination .page-item:first-child .page-link:hover,.pagination .page-item:last-child .page-link:hover{background:linear-gradient(135deg,#f3e5ab,#d4af37)!important;border-color:#aa7c11!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(212,175,55,0.4)!important;}
.pagination .page-item.active .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#aa7c11!important;color:#1d2127!important;font-weight:700!important;box-shadow:0 2px 8px rgba(212,175,55,0.3)!important;}
.pagination .page-item.disabled .page-link{background:#f8fafc!important;border-color:#e2e8f0!important;color:#94a3b8!important;cursor:not-allowed!important;opacity:0.5!important;}
</style>
@endsection
