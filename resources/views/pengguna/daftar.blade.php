@extends('layouts.dashboard')

@section('content')
<header class="page-header">
    <h2>Manajemen Pengguna</h2>
</header>

<div class="row">
    <div class="col-lg-12">
        <section class="card">
            <header class="card-header d-flex justify-content-between align-items-center">
                <h2 class="card-title">Daftar Pengguna Sistem</h2>
                <a href="{{ route('pengguna.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pengguna
                </a>
            </header>
            
            <div class="card-body">
                <form method="GET" action="{{ route('pengguna.index') }}" class="mb-4">
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama, username, atau email...">
                        </div>
                        <div class="col-sm-3">
                            <select name="role" class="form-control">
                                <option value="">-- Semua Role --</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                                <option value="pengguna" {{ request('role') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-primary" type="submit">Cari</button>
                            <a class="btn btn-default" href="{{ route('pengguna.index') }}">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                    <div class="card-body px-0 py-0">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background: #f8fafc; border-radius: 12px 12px 0 0;">
                            <div class="d-flex align-items-center gap-2">
                                <span style="color: #64748b; font-size: 0.8rem;">Tampilkan</span>
                                <select class="form-select form-select-sm" style="width: auto; min-width: 70px; border-radius: 6px; border: 1.5px solid #e2e8f0; font-size: 0.8rem; background-color: #fff;" onchange="window.location.href='{{ route('pengguna.index') }}?per_page='+this.value+'&search={{ request('search') }}&role={{ request('role') }}'">
                                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span style="color: #64748b; font-size: 0.8rem;">per halaman</span>
                            </div>
            <span style="color: #64748b; font-size: 0.8rem;">Data pengguna</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table is-table mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-3" style="width: 50px;">No</th>
                                        <th>Foto</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Unit Kerja</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Dibuat</th>
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $key => $user)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ $users->firstItem() + $key }}</td>
                                        <td>
                                            @if($user->profile_photo)
                                                <img src="{{ route('profile.photo', $user->profile_photo) }}" alt="Profile" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; border: 2px solid #d4af37;">
                                            @else
                                                <div style="width: 40px; height: 40px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-user text-muted" style="color: #94a3b8;"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="fw-bold">{{ $user->nama_pengguna }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td style="font-size: 0.8rem;">{{ $user->unit_kerja }}</td>
                                        <td style="font-size: 0.8rem;">{{ $user->email }}</td>
                                        <td>
                                            @php
                                                $roleBadge = 'bg-secondary';
                                                if ($user->role === 'admin') $roleBadge = 'bg-danger';
                                                elseif ($user->role === 'petugas') $roleBadge = 'bg-warning';
                                                elseif ($user->role === 'pengguna') $roleBadge = 'bg-info';
                                            @endphp
                                            <span class="is-badge {{ $roleBadge }}">{{ ucfirst($user->role) }}</span>
                                        </td>
                                        <td style="white-space: nowrap; font-size: 0.8rem;">{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="text-center" style="white-space: nowrap;">
                                    <a href="{{ route('pengguna.edit', $user->id) }}" class="btn btn-sm me-1" style="background:#fffbeb;color:#b45309;border:1.5px solid #fcd34d;font-weight:600;border-radius:8px;">Edit</a>
                                    <form method="POST" action="{{ route('pengguna.destroy', $user->id) }}" class="d-inline-block" onsubmit="return confirm('Yakin ingin menghapus?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:1.5px solid #fecaca;font-weight:600;border-radius:8px;">Hapus</button>
                                    </form>
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
            </div>
        </section>
    </div>
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
