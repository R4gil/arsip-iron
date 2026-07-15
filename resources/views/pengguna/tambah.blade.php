@extends('layouts.dashboard')

@section('title', 'Tambah Pengguna Baru')

@section('content')
@include('partials.page-header', ['title' => 'Tambah Pengguna Baru', 'subtitle' => 'Buat akun pengguna baru untuk mengakses sistem.'])

@if ($errors->any())
    <div class="alert alert-danger p-3 mb-4" style="border-radius: 8px; border-left: 4px solid #dc2626;">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
            <i class="fas fa-user-plus me-2" style="color: #d4af37;"></i>Data Pengguna Baru
        </h6>
        <form method="POST" action="{{ route('pengguna.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Nama Pengguna <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pengguna" value="{{ old('nama_pengguna') }}" class="form-control" placeholder="Masukkan nama lengkap" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" class="form-control" placeholder="Masukkan username" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Unit Kerja <span class="text-danger">*</span></label>
                    <select name="unit_kerja" class="form-select" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                        <option value="">-- Pilih Unit Kerja --</option>
                        <option value="Subbagian Tata Usaha" {{ old('unit_kerja') == 'Subbagian Tata Usaha' ? 'selected' : '' }}>Subbagian Tata Usaha</option>
                        <option value="Seksi Lalu Lintas Keimigrasian" {{ old('unit_kerja') == 'Seksi Lalu Lintas Keimigrasian' ? 'selected' : '' }}>Seksi Lalu Lintas Keimigrasian</option>
                        <option value="Seksi Teknologi Informasi dan Komunikasi Keimigrasian" {{ old('unit_kerja') == 'Seksi Teknologi Informasi dan Komunikasi Keimigrasian' ? 'selected' : '' }}>Seksi Teknologi Informasi dan Komunikasi Keimigrasian</option>
                        <option value="Seksi Intelijen dan Penindakan Keimigrasian" {{ old('unit_kerja') == 'Seksi Intelijen dan Penindakan Keimigrasian' ? 'selected' : '' }}>Seksi Intelijen dan Penindakan Keimigrasian</option>
                        <option value="Seksi Ijin Tinggal dan Status Keimigrasian" {{ old('unit_kerja') == 'Seksi Ijin Tinggal dan Status Keimigrasian' ? 'selected' : '' }}>Seksi Ijin Tinggal dan Status Keimigrasian</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="email@contoh.com" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Konfirmasi Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Role Akses <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="petugas" {{ old('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
                        <option value="pengguna" {{ old('role') === 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Foto Profil</label>
                    <input type="file" name="profile_photo" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif"
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.5rem; font-size: 0.9rem;">
                    <div class="form-text" style="color: #64748b; font-size: 0.8rem;">Format: JPEG, PNG, JPG, GIF (Max 2MB)</div>
                </div>
            </div>

            <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                <a href="{{ route('pengguna.index') }}" class="btn btn-light px-4 py-2" style="border-radius: 8px; font-weight: 600; border: 1.5px solid #e2e8f0;">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
                <button type="submit" class="btn fw-bold px-4 py-2" style="border-radius: 8px; background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; border: none; box-shadow: 0 3px 10px rgba(212, 175, 55, 0.3);">
                    <i class="fas fa-save me-2"></i>Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15) !important;
        background-color: #fff !important;
    }
    .form-control:hover, .form-select:hover { border-color: #cbd5e1 !important; }
    .card { transition: box-shadow 0.3s ease; }
    .card:hover { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important; }
</style>
@endsection