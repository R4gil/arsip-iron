@extends('layouts.dashboard')

@section('title', 'Tambah Peminjaman')

@section('content')
@include('partials.page-header', [
    'title' => 'Tambah Peminjaman',
    'subtitle' => 'Pilih arsip untuk dipinjam atau kembalikan arsip yang sedang dipinjam.',
    'action' => route('peminjaman.index'),
    'actionLabel' => 'Lihat Daftar',
    'actionIcon' => 'fa-list',
])

<div class="row g-3">
    <!-- ===== ARSIP TERSEDIA ===== -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
                    <h6 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase;">
                        <i class="fas fa-check-circle me-2" style="color: #d4af37;"></i>Arsip Tersedia
                    </h6>
                    <span class="badge fs-6 px-3 py-2" style="background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; font-weight: 600; border-radius: 8px;">{{ $totalArsip }} tersedia</span>
                </div>

                <!-- Search -->
                <form method="GET" action="{{ route('peminjaman.create') }}" class="mb-3">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-8">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari arsip...">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-dark w-100">Cari</button>
                        </div>
                    </div>
                </form>
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background: #f8fafc; border-radius: 12px 12px 0 0;">
                    <div class="d-flex align-items-center gap-2">
                        <span style="color: #64748b; font-size: 0.8rem;">Tampilkan</span>
                        <select class="form-select form-select-sm" style="width: auto; min-width: 70px; border-radius: 6px; border: 1.5px solid #e2e8f0; font-size: 0.8rem; background-color: #fff;" onchange="window.location.href='{{ route('peminjaman.create') }}?per_page='+this.value+'&search={{ request('search') }}'">
                            <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span style="color: #64748b; font-size: 0.8rem;">per halaman</span>
                    </div>
                    <span style="color: #64748b; font-size: 0.8rem;">Total: <strong>{{ $totalArsip }}</strong> arsip</span>
                </div>
                <div class="table-responsive">
                    <table class="table is-table mb-0">
                        <thead><tr><th class="ps-3">Nomor</th><th>Nama Arsip</th><th>Jenis</th><th>Lokasi</th><th class="text-center">Aksi</th></tr></thead>
                        <tbody>
                            @forelse($arsipTersedia as $arsip)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $arsip->nomor_surat ?? '—' }}</td>
                                <td>
                                    <div class="fw-bold">{{ $arsip->nama_arsip }}</div>
                                    <div class="text-muted small">{{ $arsip->perihal_surat ?? 'Tanpa perihal' }}</div>
                                </td>
                                <td>{{ $arsip->jenisArsip->nama ?? '—' }}</td>
                                <td>
                                    @php
                                        $locationParts = [];
                                        if ($arsip->lokasi) $locationParts[] = $arsip->lokasi->ruangan;
                                        if ($arsip->cabinet) $locationParts[] = $arsip->cabinet->lemari_nama;
                                        if ($arsip->rack) $locationParts[] = $arsip->rack->rak_nama;
                                        $locationDisplay = implode(' → ', $locationParts);
                                    @endphp
                                    {{ $locationDisplay ?: '—' }}
                                </td>
                                <td class="text-center">
                                    @if($arsip->file_arsip)
                                    @php $extension = strtolower(pathinfo($arsip->file_arsip, PATHINFO_EXTENSION)); @endphp
                                    <button type="button" class="btn btn-sm is-btn-soft is-btn-soft-primary me-1" onclick="openDokumenModal('{{ $arsip->file_arsip }}', '{{ $arsip->nomor_surat }}', '{{ $arsip->nama_arsip }}', '{{ $extension }}')">Lihat</button>
                                    @endif
                                    <button type="button" class="btn btn-sm fw-bold" style="background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; border: none; border-radius: 6px; padding: 0.4rem 1rem;" onclick="openPinjamModal({{ $arsip->id }}, '{{ $arsip->nomor_surat }}', '{{ $arsip->nama_arsip }}')">
                                        <i class="fas fa-hand-holding me-1"></i> Pinjam
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="is-empty">Tidak ada arsip tersedia.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $arsipTersedia->withQueryString()->links('pagination::simple-bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== ARSIP DIPINJAM ===== -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
                    <h6 class="fw-bold mb-0" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase;">
                        <i class="fas fa-book me-2" style="color: #d4af37;"></i>Arsip Sedang Dipinjam
                    </h6>
                    <span class="badge fs-6 px-3 py-2" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; font-weight: 600; border-radius: 8px;">{{ $peminjamanAktif->count() }} aktif</span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span style="color: #64748b; font-size: 0.8rem;">Tampilkan</span>
                        <select class="form-select form-select-sm" style="width: auto; min-width: 70px; border-radius: 6px; border: 1.5px solid #e2e8f0; font-size: 0.8rem; background-color: #fff;" onchange="window.location.href='{{ route('peminjaman.create') }}?per_page='+this.value+'&search={{ request('search') }}'">
                            <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span style="color: #64748b; font-size: 0.8rem;">per halaman</span>
                    </div>

                    <div>
                        <span style="color: #64748b; font-size: 0.8rem;">Total: <strong>{{ $totalPinjam }}</strong> peminjaman</span>
                    </div>
                </div>

                    <table class="table is-table mb-0">
                        <thead><tr><th class="ps-3">Nomor</th><th>Nama</th><th>Peminjam</th><th>Tgl</th><th class="text-center">Aksi</th></tr></thead>
                        <tbody>
                            @forelse($peminjamanAktif as $pinjam)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $pinjam->archive->nomor_surat ?? '—' }}</td>
                                <td>{{ $pinjam->archive->nama_arsip ?? '—' }}</td>
                                <td>{{ $pinjam->nama_peminjam }}@if($pinjam->divisi_peminjam)<br><small class="text-muted">{{ $pinjam->divisi_peminjam }}</small>@endif</td>
                                <td>{{ $pinjam->tanggal_keluar ? \Carbon\Carbon::parse($pinjam->tanggal_keluar)->format('d-m-Y') : '—' }}</td>
                                <td class="text-center">
                                    <form action="{{ route('peminjaman.kembalikan', $pinjam->arsip_id) }}" method="POST" class="d-inline" onsubmit="return confirm('Kembalikan arsip ini?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm fw-bold" style="background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; border: none; border-radius: 6px; padding: 0.4rem 1rem;">
                                            <i class="fas fa-undo me-1"></i>Kembalikan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="is-empty">Tidak ada arsip yang sedang dipinjam.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $peminjamanAktif->withQueryString()->links('pagination::simple-bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL FORM PEMINJAMAN ===== -->
<div class="modal fade" id="pinjamModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background: #1e293b; color: #fff; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold" style="font-size: 1rem;">
                    <i class="fas fa-hand-holding me-2" style="color: #d4af37;"></i>Form Peminjaman Arsip
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert d-flex align-items-center gap-2 p-3 mb-4" style="background: linear-gradient(135deg, #fefce8, #fef9c3); border-left: 4px solid #d4af37; border-radius: 8px; color: #854d0e;">
                    <i class="fas fa-archive" style="color: #d4af37;"></i>
                    <strong>Arsip:</strong> <span id="modalArsipInfo"></span>
                </div>
                <form action="{{ route('peminjaman.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="arsip_id" id="modalArsipId">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Nama Peminjam <span class="text-danger">*</span></label>
                        <input type="text" name="nama_peminjam" class="form-control"
                            value="{{ auth()->user()->nama_pengguna ?? auth()->user()->name }}" required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Divisi / Unit Kerja</label>
                        <input type="text" name="divisi_peminjam" class="form-control" value="{{ auth()->user()->unit_kerja }}"
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Tanggal Pinjam <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_keluar" class="form-control" value="{{ date('Y-m-d') }}" required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Keterangan Kondisi</label>
                        <textarea name="keterangan_kondisi" class="form-control" rows="2" placeholder="Catatan kondisi arsip (opsional)" 
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;"></textarea>
                    </div>
                    <div class="d-flex gap-2 justify-content-end pt-2">
                        <button type="button" class="btn btn-light px-4" style="border-radius: 8px; font-weight: 600; border: 1.5px solid #e2e8f0;" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn fw-bold px-4" style="border-radius: 8px; background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; border: none; box-shadow: 0 3px 10px rgba(212, 175, 55, 0.3);">
                            <i class="fas fa-save me-2"></i>Simpan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL DOKUMEN ===== -->
<div class="modal fade" id="dokumenModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background: #1e293b; color: #fff; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold" style="font-size: 1rem;">
                    <i class="fas fa-file-alt me-2" style="color: #d4af37;"></i>Lihat Dokumen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert d-flex align-items-center gap-2 p-3 mb-4" style="background: linear-gradient(135deg, #fefce8, #fef9c3); border-left: 4px solid #d4af37; border-radius: 8px; color: #854d0e;">
                    <i class="fas fa-archive" style="color: #d4af37;"></i>
                    <strong>Arsip:</strong> <span id="dokumenArsipInfo"></span>
                </div>
                <div id="dokumenContainer" class="p-3" style="min-height: 800px;"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15) !important;
        background-color: #fff !important;
    }
    .form-control:hover, .form-select:hover {
        border-color: #cbd5e1 !important;
    }
    .card { transition: box-shadow 0.3s ease; }
    .card:hover { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important; }
</style>
@endsection

@push('scripts')
<script>
function openPinjamModal(arsipId, nomorSurat, namaArsip) {
    document.getElementById('modalArsipId').value = arsipId;
    document.getElementById('modalArsipInfo').textContent = nomorSurat + ' — ' + namaArsip;
    new bootstrap.Modal(document.getElementById('pinjamModal')).show();
}

function openDokumenModal(fileArsip, nomorSurat, namaArsip, extension) {
    document.getElementById('dokumenArsipInfo').textContent = nomorSurat + ' — ' + namaArsip;
    var container = document.getElementById('dokumenContainer');
    var filePath = '/arsip/view/' + fileArsip;

    if (['jpg', 'jpeg', 'png'].includes(extension)) {
        container.innerHTML = '<img src="' + filePath + '" class="img-fluid rounded border" alt="File Arsip" style="max-height: 900px;">';
    } else if (extension == 'pdf') {
        container.innerHTML = '<iframe src="' + filePath + '" width="100%" height="900px" class="border rounded"></iframe>';
    } else {
        container.innerHTML = '<div class="alert alert-info mb-0">File tidak dapat ditampilkan langsung. <a href="' + filePath + '" class="btn btn-primary btn-sm ms-2" target="_blank">Download File</a></div>';
    }
    new bootstrap.Modal(document.getElementById('dokumenModal')).show();
}
</script>
@endpush