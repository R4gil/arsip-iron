@extends('layouts.dashboard')

@section('title', 'Daftar Arsip')

@section('content')
<div class="mb-4">
    <h5 class="fw-bold text-dark">Daftar Arsip Editan</h5>
    <p class="text-secondary small">Kelola data arsip dan lokasi penyimpanan.</p>
</div>

<div class="card shadow-sm mb-4 border-0">
    <div class="card-body p-3 bg-white">
        <form method="GET" action="{{ route('arsip.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Cari Dokumen</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Nomor, nama, atau perihal...">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-muted small fw-bold mb-1">Lokasi</label>
                    <select name="location_id" class="form-select form-select-sm">
                        <option value="">-- Semua Lokasi --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold mb-1">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm">
                        <option value="">-- Semua --</option>
                        @foreach($years as $yr)
                            <option value="{{ $yr }}" {{ request('tahun') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-muted small fw-bold mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Semua --</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Inaktif" {{ request('status') == 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100">Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-3 py-3">#</th>
                    <th>Nomor Surat</th>
                    <th>Tanggal Surat</th>
                    <th>Nama Arsip</th>
                    <th>Klasifikasi</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Status Pinjam</th>
                    <th class="text-center pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($archives as $key => $archive)
                <tr>
                    <td class="ps-3">{{ $archives->firstItem() + $key }}</td>
                    <td class="fw-bold">
                        {{ explode('/', $archive->nomor_surat)[0] }}
                    </td>
                    <td>
                        {{ $archive->tanggal_arsip ? \Carbon\Carbon::parse($archive->tanggal_arsip)->format('d-m-Y') : '-' }}
                    </td>
                    <td>{{ $archive->nama_arsip }}</td>
                    <td>{{ $archive->nama_klasifikasi ?? '-' }}</td>
                    <td>{{ $archive->nama_lokasi ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $archive->status == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">
                            {{ $archive->status }}
                        </span>
                    </td>
                    <td>{{ $archive->status_pinjam ?? 'Tersedia' }}</td>
                    <td class="text-center pe-3">
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('arsip.show', $archive->id) }}" class="btn btn-sm btn-info text-white">Lihat</a>
                            <a href="{{ route('arsip.edit', $archive->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('arsip.destroy', $archive->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">Data tidak ditemukan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $archives->withQueryString()->links() }}
</div>
@endsection