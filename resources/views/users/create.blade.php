@extends('layouts.dashboard')

@section('title', 'Tambah Pengguna Baru')

@section('content')
    <div class="card shadow-sm p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h1 class="h4 mb-1">Tambah Pengguna Baru</h1>
                <p class="text-muted mb-0">Buat akun pengguna baru dengan role akses yang sesuai.</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Kembali ke Daftar</a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Pengguna</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" class="form-control @error('username') is-invalid @enderror" required>
                    @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role Akses</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                        <option value="pengguna" {{ old('role') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
