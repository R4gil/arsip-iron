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
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pengguna
                </a>
            </header>
            
            <div class="card-body">
                <form method="GET" action="{{ route('users.index') }}" class="mb-4">
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
                            <a class="btn btn-default" href="{{ route('users.index') }}">Reset</a>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0" id="usersTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Unit Kerja</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Dibuat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td class="font-weight-semibold">{{ $user->nama_pengguna }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->unit_kerja }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'petugas' ? 'warning' : 'info') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="d-inline-block" onsubmit="return confirm('Yakin ingin menghapus?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center">Belum ada pengguna.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </section>
    </div>
</div>
@endsection