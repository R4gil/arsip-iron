@extends('layouts.dashboard')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<style>
    /* Styling agar form lebih rapi, compact, dan profesional */
    .form-control, .form-select {
        font-size: 0.9rem !important;
        padding: 0.4rem 0.6rem !important;
        border-radius: 6px !important;
    }
    .form-label {
        font-size: 0.85rem !important;
        font-weight: 600;
        margin-bottom: 0.3rem !important;
        color: #495057;
    }
    .card { border-radius: 10px !important; }
</style>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h5 mb-0">Tambah Pengguna Baru</h1>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger p-2 mb-3">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama Pengguna</label>
                        <input type="text" name="nama_pengguna" value="{{ old('nama_pengguna') }}" class="form-control" placeholder="Masukkan nama" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" class="form-control" placeholder="Masukkan username" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Unit Kerja</label>
                        <select name="unit_kerja" class="form-select" required>
                            <option value="">-- Pilih Unit Kerja --</option>
                            <option value="Subbagian Tata Usaha" {{ old('unit_kerja') == 'Subbagian Tata Usaha' ? 'selected' : '' }}>Subbagian Tata Usaha</option>
                            <option value="Seksi Lalu Lintas Keimigrasian" {{ old('unit_kerja') == 'Seksi Lalu Lintas Keimigrasian' ? 'selected' : '' }}>Seksi Lalu Lintas Keimigrasian</option>
                            <option value="Seksi Teknologi Informasi dan Komunikasi Keimigrasian" {{ old('unit_kerja') == 'Seksi Teknologi Informasi dan Komunikasi Keimigrasian' ? 'selected' : '' }}>Seksi Teknologi Informasi dan Komunikasi Keimigrasian</option>
                            <option value="Seksi Intelijen dan Penindakan Keimigrasian" {{ old('unit_kerja') == 'Seksi Intelijen dan Penindakan Keimigrasian' ? 'selected' : '' }}>Seksi Intelijen dan Penindakan Keimigrasian</option>
                            <option value="Seksi Ijin Tinggal dan Status Keimigrasian" {{ old('unit_kerja') == 'Seksi Ijin Tinggal dan Status Keimigrasian' ? 'selected' : '' }}>Seksi Ijin Tinggal dan Status Keimigrasian</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="email@contoh.com" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Role Akses</label>
                        <select name="role" class="form-select" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                            <option value="pengguna" {{ old('role') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 border-top pt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-4">Simpan Pengguna</button>
                    <button type="reset" class="btn btn-light btn-sm px-4">Reset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection