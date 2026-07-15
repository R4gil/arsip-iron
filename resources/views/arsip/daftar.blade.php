@extends('layouts.dashboard')

@section('title', 'Daftar Arsip')

@section('content')
@include('partials.page-header', [
    'title' => 'Daftar Arsip',
    'subtitle' => 'Kelola data arsip dan lokasi penyimpanan.',
    'action' => route('arsip.create'),
    'actionLabel' => 'Tambah Arsip',
])

<div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('arsip.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Cari Dokumen</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Nomor, nama, perihal..." style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control form-control-sm" style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control form-control-sm" style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Lokasi</label>
                    <select name="lokasi_id" class="form-select form-select-sm" style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                        <option value="">Semua Lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('lokasi_id') == $loc->id ? 'selected' : '' }}>{{ $loc->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Status</label>
                    <select name="status" class="form-select form-select-sm" style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                        <option value="">Semua</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Inaktif" {{ request('status') == 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100 py-2" style="border-radius: 8px; font-weight: 600;"><i class="fas fa-search me-1"></i>Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body px-0 py-0">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background: #f8fafc; border-radius: 12px 12px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span style="color: #64748b; font-size: 0.8rem;">Tampilkan</span>
                <select class="form-select form-select-sm" style="width: auto; min-width: 70px; border-radius: 6px; border: 1.5px solid #e2e8f0; font-size: 0.8rem; background-color: #fff;" onchange="window.location.href='{{ route('arsip.index') }}?per_page='+this.value+'&search={{ request('search') }}&tanggal_mulai={{ request('tanggal_mulai') }}&tanggal_selesai={{ request('tanggal_selesai') }}&lokasi_id={{ request('lokasi_id') }}&status={{ request('status') }}'">
                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span style="color: #64748b; font-size: 0.8rem;">per halaman</span>
            </div>
            <span style="color: #64748b; font-size: 0.8rem;">Data arsip</span>
        </div>
        <div class="table-responsive">
            <table class="table is-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Nomor Surat</th>
                        <th>Tanggal</th>
                        <th>Nama Arsip</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
                        @if($retensiTersedia ?? false)
                        <th>Masa Retensi</th>
                        <th>Tgl Retensi</th>
                        @endif
                        <th>Status</th>
                        <th>Ketersediaan</th>
                        <th>Status Retensi</th>
                        <th class="text-center pe-3" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($archives as $key => $archive)
                    <tr>
                        <td class="ps-3 text-muted">{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $archive->nomor_surat ?? '—' }}</td>
                        <td style="white-space: nowrap;">{{ $archive->tanggal_arsip ? \Carbon\Carbon::parse($archive->tanggal_arsip)->format('d-m-Y') : '—' }}</td>
                        <td>
                            <div class="fw-bold" style="font-size: 0.85rem;">{{ $archive->nama_arsip }}</div>
                            <div class="text-muted" style="font-size: 0.75rem !important;">{{ $archive->perihal_surat ?? '' }}</div>
                        </td>
                        <td style="font-size: 0.8rem;">{{ $archive->nama_jenis ?? '—' }}</td>
                        <td style="font-size: 0.8rem;">
                            @php
                                $parts = array_filter([$archive->ruangan ?? '', $archive->lemari_nama ?? '', $archive->rak_nama ?? '']);
                            @endphp
                            {{ $parts ? implode(' → ', $parts) : '—' }}
                        </td>
                        @if($retensiTersedia ?? false)
                        <td style="font-size: 0.8rem;">{{ $archive->masa_retensi ?? '—' }}</td>
                        <td style="font-size: 0.8rem; white-space: nowrap;">{{ $archive->tanggal_retensi ? \Carbon\Carbon::parse($archive->tanggal_retensi)->format('d-m-Y') : '—' }}</td>
                        @endif
                        <td><span class="is-badge {{ $archive->status == 'Aktif' ? 'bg-success' : 'bg-secondary' }}">{{ $archive->status }}</span></td>
                        <td><span class="is-badge {{ ($archive->status_ketersediaan ?? 'Tersedia') == 'Tersedia' ? 'bg-success' : 'bg-danger' }}">{{ $archive->status_ketersediaan ?? 'Tersedia' }}</span></td>
                        <td>
                            @php
                                $sr = $archive->status_retensi ?? 'Belum Memasuki Masa Retensi';
                                $bc = 'bg-secondary';
                                if ($sr == 'Masuk Masa Retensi') $bc = 'bg-danger';
                                elseif ($sr == 'Proses Retensi') $bc = 'bg-warning text-dark';
                                elseif ($sr == 'Sudah Retensi') $bc = 'bg-success';
                            @endphp
                            <span class="is-badge {{ $bc }}" style="font-size: 0.7rem !important;">{{ $sr }}</span>
                        </td>
                        <td class="text-center pe-3" style="white-space: nowrap;">
                            <a href="{{ route('arsip.show', $archive->id) }}" class="btn btn-sm me-1" style="background:linear-gradient(135deg,#d4af37,#aa7c11);color:#1d2127;border:none;font-weight:700;border-radius:8px;box-shadow:0 2px 6px rgba(212,175,55,0.25);">Lihat</a>
                            <a href="{{ route('arsip.edit', $archive->id) }}" class="btn btn-sm me-1" style="background:#fffbeb;color:#b45309;border:1.5px solid #fcd34d;font-weight:600;border-radius:8px;">Edit</a>
                            <form action="{{ route('arsip.destroy', $archive->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:1.5px solid #fecaca;font-weight:600;border-radius:8px;" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="{{ ($retensiTersedia ?? false) ? 12 : 10 }}" class="is-empty">Data tidak ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($archives->hasPages())
    <div class="card-body border-top d-flex justify-content-between align-items-center" style="background: #f8fafc; border-radius: 0 0 12px 12px;">
        <span style="color: #64748b; font-size: 0.8rem;">&nbsp;</span>
        {{ $archives->withQueryString()->links('pagination::simple-bootstrap-4') }}
    </div>
    @endif
</div>

<style>
    .form-control:focus, .form-select:focus { border-color: #d4af37 !important; box-shadow: 0 0 0 3px rgba(212,175,55,0.15) !important; background-color: #fff !important; }
    .form-control:hover, .form-select:hover { border-color: #cbd5e1 !important; }
    .card { transition: box-shadow 0.3s ease; }
    .card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important; }
    .table th { background: #f1f5f9 !important; color: #334155 !important; font-size: 0.78rem !important; text-transform: uppercase; letter-spacing: 0.03em; border-bottom: 2px solid #e2e8f0 !important; padding: 0.7rem 0.75rem !important; }
    .table td { vertical-align: middle !important; font-size: 0.85rem; padding: 0.65rem 0.75rem !important; }
    .table tbody tr:hover { background: #f8fafc !important; }
    .table tbody tr:not(:last-child) td { border-bottom: 1px solid #f1f5f9 !important; }
    .is-badge { font-size: 0.72rem !important; padding: 0.25rem 0.55rem !important; border-radius: 6px !important; font-weight: 600 !important; }
</style>
@endsection