@extends('layouts.dashboard')

@section('title', 'Daftar Arsip')
@section('subtitle', 'Kelola arsip, klasifikasi, lokasi dan status peminjaman di satu halaman.')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold">Daftar Arsip</h4>
            <p class="text-muted mb-0">Kelola pencatatan arsip lengkap dengan kategori dan lokasi penyimpanan.</p>
        </div>
        <a href="{{ route('arsip.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Arsip</a>
    </div>

    <div class="card card-soft shadow-sm mb-4">
        <div class="card-body bg-light">
            <form method="GET" action="{{ route('arsip.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted">Cari Dokumen</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari nomor surat, nama, perihal...">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Lokasi Ruangan</label>
                        <select name="location_id" class="form-select">
                            <option value="">-- Semua Lokasi --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->nama_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Tahun</label>
                        <select name="tahun" class="form-select">
                            <option value="">-- Semua --</option>
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Status</label>
                        <select name="status" class="form-select">
                            <option value="">-- Semua --</option>
                            <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Inaktif" {{ request('status') == 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-dark w-100"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-soft shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0" id="archivesTable">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3" style="width: 60px;">#</th>
                            <th>Nomor Surat</th>
                            <th>Nama Arsip</th>
                            <th>Klasifikasi</th>
                            <th>Lokasi Penyimpanan</th>
                            <th class="text-center">Tahun</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Fisik</th>
                            <th class="text-center pe-3" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archives as $key => $archive)
                            <tr>
                                <td class="ps-3 fw-bold text-muted">{{ $archives->firstItem() + $key }}</td>
                                <td><strong>{{ $archive->nomor_surat ?? '-' }}</strong></td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $archive->nama_arsip }}</div>
                                    @if($archive->perihal_surat)
                                        <small class="text-muted d-block text-truncate" style="max-width: 300px;">
                                            {{ $archive->perihal_surat }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($archive->nama_klasifikasi)
                                        <span class="badge bg-secondary">
                                            {{ $archive->nama_klasifikasi }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($archive->nama_lokasi)
                                        <small><i class="fas fa-box text-muted me-1"></i> {{ $archive->nama_lokasi }}</small>
                                    @else
                                        <span class="text-danger small">Belum Diatur</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $archive->tahun }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $archive->status === 'Aktif' ? 'success' : 'secondary' }}">
                                        {{ $archive->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $archive->status_ketersediaan === 'Tersedia' ? 'info' : 'warning text-dark' }}">
                                        {{ $archive->status_ketersediaan ?? 'Tersedia' }}
                                    </span>
                                </td>
                                <td class="text-center pe-3">
                                    <a href="{{ route('arsip.edit', $archive->id) }}" class="btn btn-sm btn-warning text-dark" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('arsip.destroy', $archive->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus arsip ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                    Tidak ada data arsip yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
        <div class="text-muted small">
            Menampilkan {{ $archives->firstItem() ?? 0 }} sampai {{ $archives->lastItem() ?? 0 }} dari {{ $archives->total() }} arsip.
        </div>
        <div>
            {{ $archives->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // DataTables di-disable agar tidak conflict dengan pagination bawaan Laravel
        console.log("Halaman indeks arsip berhasil dimuat tanpa conflict JavaScript.");
    });
</script>
@endpush