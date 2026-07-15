@extends('layouts.dashboard')

@section('title', 'Ubah Klasifikasi')

@section('content')
@include('partials.page-header', ['title' => 'Ubah Klasifikasi', 'subtitle' => 'Perbarui detail klasifikasi arsip yang ada.'])

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
            <i class="fas fa-edit me-2" style="color: #d4af37;"></i>Form Edit Klasifikasi
        </h6>
        <form action="{{ route('klasifikasi.update', $classification) }}" method="POST">
            @method('PUT')
            @include('klasifikasi.formulir')
            <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                <a href="{{ route('klasifikasi.index') }}" class="btn btn-light px-4 py-2" style="border-radius: 8px; font-weight: 600; border: 1.5px solid #e2e8f0;">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
                <button type="submit" class="btn fw-bold px-4 py-2" style="border-radius: 8px; background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; border: none; box-shadow: 0 3px 10px rgba(212, 175, 55, 0.3);">
                    <i class="fas fa-save me-2"></i>Perbarui Klasifikasi
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .card { transition: box-shadow 0.3s ease; }
    .card:hover { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important; }
</style>
@endsection