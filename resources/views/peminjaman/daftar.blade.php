@extends('layouts.dashboard')

@section('title', 'Daftar Peminjaman')

@section('content')
@include('partials.page-header', [
    'title' => 'Daftar Peminjaman',
    'subtitle' => 'Riwayat seluruh transaksi peminjaman arsip.',
    'action' => route('peminjaman.create'),
    'actionLabel' => 'Tambah Peminjaman',
])

<div class="row g-3 mb-4">
    @foreach([['Total', $stats['total'], 'fa-list'], ['Dipinjam', $stats['dipinjam'], 'fa-book'], ['Dikembalikan', $stats['dikembalikan'], 'fa-check']] as $s)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div style="background: #f8fafc; color: #475569; width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;"><i class="fas {{ $s[2] }}"></i></div>
                <div><div style="color: #64748b; font-size: 0.8rem; font-weight: 600;">{{ $s[0] }}</div><div style="font-weight: 700; font-size: 1.5rem; color: #1e293b;">{{ $s[1] }}</div></div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('peminjaman.index') }}" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Peminjam, nomor/nama arsip..." style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Status</label>
                <select name="status" class="form-select form-select-sm" style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                    <option value="">Semua Status</option>
                    <option value="Dipinjam" {{ request('status') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="Dikembalikan" {{ request('status') == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark btn-sm w-100 py-2" style="border-radius: 8px; font-weight: 600;"><i class="fas fa-search me-1"></i>Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-start mb-3">
    <form action="{{ route('peminjaman.clearHistory') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus semua riwayat peminjaman yang sudah dikembalikan? Data peminjaman yang masih aktif (Dipinjam) akan tetap disimpan.');">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm fw-bold" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25); color: white;">
            <i class="fas fa-trash-alt me-2"></i>Hapus Riwayat Peminjaman
        </button>
    </form>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body px-0 py-0">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background: #f8fafc; border-radius: 12px 12px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span style="color: #64748b; font-size: 0.8rem;">Tampilkan</span>
                <select class="form-select form-select-sm" style="width: auto; min-width: 70px; border-radius: 6px; border: 1.5px solid #e2e8f0; font-size: 0.8rem; background-color: #fff;" onchange="window.location.href='{{ route('peminjaman.index') }}?per_page='+this.value+'&search={{ request('search') }}&status={{ request('status') }}'">
                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span style="color: #64748b; font-size: 0.8rem;">per halaman</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('peminjaman.exportExcel', request()->all()) }}" class="btn btn-success btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('peminjaman.exportPDF', request()->all()) }}" class="btn btn-danger btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table is-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th>Nomor Arsip</th>
                        <th>Nama Arsip</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Peminjam</th>
                        <th>Divisi</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $key => $borrowing)
                    <tr>
                        <td class="ps-3 text-muted">{{ $borrowings->firstItem() + $key }}</td>
                        @if($borrowing->archive)
                        <td class="fw-bold" style="font-size: 0.8rem;">{{ $borrowing->archive->nomor_surat ?? '—' }}</td>
                        <td>{{ $borrowing->archive->nama_arsip ?? '—' }}</td>
                        <td style="font-size: 0.8rem;">{{ $borrowing->archive->jenisArsip->nama ?? '—' }}</td>
                        <td style="font-size: 0.8rem;">
                            @php
                                $parts = [];
                                if ($borrowing->archive->lokasi) $parts[] = $borrowing->archive->lokasi->ruangan;
                                if ($borrowing->archive->cabinet) $parts[] = $borrowing->archive->cabinet->lemari_nama;
                                if ($borrowing->archive->rack) $parts[] = $borrowing->archive->rack->rak_nama;
                            @endphp
                            {{ $parts ? implode(' → ', $parts) : '—' }}
                        </td>
                        @else
                        <td class="text-muted" colspan="4">Arsip tidak tersedia (dihapus)</td>
                        @endif
                        <td>{{ $borrowing->nama_peminjam }}</td>
                        <td style="font-size: 0.8rem;">{{ $borrowing->divisi_peminjam ?? '—' }}</td>
                        <td style="white-space: nowrap; font-size: 0.8rem;">{{ $borrowing->tanggal_keluar ? \Carbon\Carbon::parse($borrowing->tanggal_keluar)->format('d-m-Y') : '—' }}</td>
                        <td style="white-space: nowrap; font-size: 0.8rem;">{{ $borrowing->tanggal_masuk ? \Carbon\Carbon::parse($borrowing->tanggal_masuk)->format('d-m-Y') : '—' }}</td>
                        <td><span class="is-badge {{ $borrowing->status_pinjam === 'Dipinjam' ? 'bg-danger' : 'bg-success' }}">{{ $borrowing->status_pinjam }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="is-empty">Data peminjaman tidak ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($borrowings->hasPages())
    <div class="card-body border-top d-flex justify-content-between align-items-center" style="background: #f8fafc; border-radius: 0 0 12px 12px;">
        <span style="color: #64748b; font-size: 0.8rem;">&nbsp;</span>
        {{ $borrowings->withQueryString()->links('pagination::simple-bootstrap-4') }}
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
    
    .pagination{margin-bottom:0!important;gap:8px!important;}
    .pagination .page-link{background:#fff!important;border:1.5px solid #e2e8f0!important;color:#334155!important;font-weight:600!important;font-size:0.85rem!important;padding:0.5rem 0.85rem!important;border-radius:8px!important;transition:all 0.2s ease!important;text-decoration:none!important;}
    .pagination .page-item:first-child .page-link,.pagination .page-item:last-child .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#d4af37!important;color:#1d2127!important;font-weight:700!important;padding:0.5rem 1.2rem!important;box-shadow:0 2px 8px rgba(212,175,55,0.25)!important;}
    .pagination .page-item:first-child .page-link:hover,.pagination .page-item:last-child .page-link:hover{background:linear-gradient(135deg,#f3e5ab,#d4af37)!important;border-color:#aa7c11!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(212,175,55,0.4)!important;}
    .pagination .page-item.active .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#aa7c11!important;color:#1d2127!important;font-weight:700!important;box-shadow:0 2px 8px rgba(212,175,55,0.3)!important;}
    .pagination .page-item.disabled .page-link{background:#f8fafc!important;border-color:#e2e8f0!important;color:#94a3b8!important;cursor:not-allowed!important;opacity:0.5!important;}
    .pagination .page-link .sr-only{display:none!important;}
</style>
@endsection
