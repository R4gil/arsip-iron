@extends('layouts.dashboard')

@section('title', 'Kelola Pengguna')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Kelola Pengguna</h1>
            <p class="text-muted mb-0">Atur pengguna, role akses (Admin, Petugas, Pengguna) dan izin akses sistem.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('users.create') }}" class="btn btn-success">Tambah Pengguna Baru</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form class="row gy-2 gx-2 align-items-center mb-3" method="GET" action="{{ route('users.index') }}">
                <div class="col-sm-6">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nama, username, atau email pengguna">
                </div>
                <div class="col-sm-3">
                    <select name="role" class="form-select">
                        <option value="">-- Filter Role --</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                        <option value="pengguna" {{ request('role') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                    </select>
                </div>
                <div class="col-sm-3 d-flex gap-2">
                    <button class="btn btn-primary w-100" type="submit">Cari</button>
                    <a class="btn btn-outline-secondary" href="{{ route('users.index') }}">Reset</a>
                </div>
            </form>

            @if($users->isEmpty())
                <div class="alert alert-secondary mb-0">Belum ada pengguna yang tersedia.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td class="fw-semibold">{{ $user->name }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'petugas' ? 'warning text-dark' : 'info') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">{{ $users->links() }}</div>
            @endif
        </div>
    </div>
@endsection
