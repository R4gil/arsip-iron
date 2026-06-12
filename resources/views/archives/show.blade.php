@extends('layouts.dashboard')

@section('title', 'Detail Arsip')
@section('subtitle', 'Informasi lengkap arsip beserta klasifikasi, lokasi, dan status penyimpanan.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Detail Arsip</h5>
                <p class="text-muted mb-0">Lihat data arsip secara lengkap dan pantau status penyimpanannya.</p>
            </div>
            <a href="{{ route('arsip.index') }}" class="btn btn-outline-secondary btn-sm">Kembali ke Daftar</a>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Nomor Arsip</dt>
                        <dd class="col-7">{{ $archive->nomor_arsip }}</dd>

                        <dt class="col-5 text-muted">Nama Arsip</dt>
                        <dd class="col-7">{{ $archive->nama_arsip }}</dd>

                        <dt class="col-5 text-muted">Klasifikasi</dt>
                        <dd class="col-7">{{ $archive->classification->nama ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Lokasi</dt>
                        <dd class="col-7">{{ $archive->location->nama_lokasi ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Kabinet</dt>
                        <dd class="col-7">{{ $archive->cabinet->nama_lemari ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Rak</dt>
                        <dd class="col-7">{{ $archive->rack->nama_rak ?? '-' }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Tahun</dt>
                        <dd class="col-7">{{ $archive->tahun }}</dd>

                        <dt class="col-5 text-muted">Tanggal Arsip</dt>
                        <dd class="col-7">{{ $archive->tanggal_arsip?->format('d M Y') }}</dd>

                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            <span class="badge bg-{{ $archive->status === 'dipinjam' ? 'warning text-dark' : ($archive->status === 'inaktif' ? 'secondary' : 'success') }}">
                                {{ ucfirst($archive->status) }}
                            </span>
                        </dd>

                        <dt class="col-5 text-muted">Uraian</dt>
                        <dd class="col-7">{{ $archive->uraian ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
