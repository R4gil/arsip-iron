@extends('layouts.dashboard')

@section('title', 'Detail Arsip')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0 fw-bold">Detail Arsip: {{ $archive->nomor_surat }}</h5>
                <p class="text-muted small mb-0">Informasi lengkap data dan lokasi penyimpanan.</p>
            </div>
            <a href="{{ route('arsip.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Nomor Surat</dt>
                        <dd class="col-7 fw-bold text-dark">{{ $archive->nomor_surat ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Nama Arsip</dt>
                        <dd class="col-7">{{ $archive->nama_arsip ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Klasifikasi</dt>
                        <dd class="col-7">{{ $archive->nama_klasifikasi ?? '-' }}</dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Lokasi</dt>
                        <dd class="col-7">{{ $archive->nama_lokasi ?? '-' }}</dd>

                        <dt class="col-5 text-muted">Tanggal Arsip</dt>
                        <dd class="col-7">{{ $archive->tanggal_arsip ? date('d M Y', strtotime($archive->tanggal_arsip)) : '-' }}</dd>

                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            <span class="badge {{ $archive->status == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $archive->status ?? '-' }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Pratinjau Dokumen</h6>
        </div>
        <div class="card-body p-0">
            @if(!empty($archive->file_arsip))
                @php
                    $filePath = asset('dokumen_arsip/' . $archive->file_arsip);
                    $extension = strtolower(pathinfo($archive->file_arsip, PATHINFO_EXTENSION));
                @endphp

                <div class="p-3">
                    @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                        <img src="{{ $filePath }}" class="img-fluid rounded border" alt="File Arsip">
                    @elseif($extension == 'pdf')
                        <iframe src="{{ $filePath }}" width="100%" height="600px" class="border rounded"></iframe>
                    @else
                        <div class="alert alert-info mb-0">
                            File tidak dapat ditampilkan langsung. 
                            <a href="{{ $filePath }}" class="btn btn-primary btn-sm ms-2" target="_blank">Download File</a>
                        </div>
                    @endif
                </div>
            @else
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-file-slash fa-2x mb-2"></i>
                    <p>Tidak ada file arsip yang dilampirkan.</p>
                </div>
            @endif
        </div>
    </div>
@endsection