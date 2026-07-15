@extends('layouts.dashboard')

@section('title', 'Tambah Lokasi')

@section('content')
@include('partials.page-header', ['title' => 'Tambah Lokasi', 'subtitle' => 'Buat ruangan/lokasi penyimpanan arsip baru.'])

@include('partials.lokasi-hierarchy')

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
            <i class="fas fa-map-marker-alt me-2" style="color: #d4af37;"></i>Form Lokasi Baru
        </h6>
        <form action="{{ route('lokasi.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Nama Ruangan <span class="text-danger">*</span></label>
                    <input type="text" name="ruangan" class="form-control" value="{{ old('ruangan') }}" placeholder="Contoh: Ruang Arsip A" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Keterangan</label>
                    <input type="text" name="keterangan" class="form-control" value="{{ old('keterangan') }}" placeholder="Deskripsi singkat (opsional)"
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                <a href="{{ route('lokasi.index') }}" class="btn btn-light px-4 py-2" style="border-radius: 8px; font-weight: 600; border: 1.5px solid #e2e8f0;">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
                <button type="submit" class="btn fw-bold px-4 py-2" style="border-radius: 8px; background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; border: none; box-shadow: 0 3px 10px rgba(212, 175, 55, 0.3);">
                    <i class="fas fa-save me-2"></i>Simpan Lokasi
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