@extends('layouts.dashboard')

@section('title', 'Daftar Peminjaman')

@section('content')
<div class="mb-4">
    <h5 class="fw-bold text-dark">Daftar Peminjaman</h5>
    <p class="text-secondary small">Kelola data peminjaman arsip.</p>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
            <thead class="bg-light text-secondary">
                <tr>
                    <th class="ps-3 py-3">#</th>
                    <th>Peminjam</th>
                    <th>Arsip</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
<tbody>
    @forelse($borrowings as $key => $borrowing)
    <tr>
        <td class="ps-3">{{ $borrowings->firstItem() + $key }}</td>
        <td>{{ $borrowing->nama_peminjam }}</td>
        
        <td>{{ $borrowing->archive->nama_arsip ?? 'Data Arsip Hilang' }}</td>
        
        <td>
            <span class="badge {{ ($borrowing->archive->status_ketersediaan ?? '') == 'Tersedia' ? 'bg-success' : 'bg-danger' }}">
                {{ $borrowing->archive->status_ketersediaan ?? 'N/A' }}
            </span>
        </td>

        <td class="text-center pe-3">
            <div class="d-flex justify-content-center gap-1">
                <button type="button" class="btn btn-sm btn-info text-white" 
                        data-bs-toggle="modal" 
                        data-bs-target="#lihatArsipModal" 
                        data-file="{{ route('arsip.view', $borrowing->archive->file_arsip ?? '') }}" 
                        data-nama="{{ $borrowing->archive->nama_arsip ?? 'Arsip Tidak Ditemukan' }}">
                    Lihat
                </button>
                
                <a href="{{ route('arsip.edit', $borrowing->arsip_id) }}" class="btn btn-sm btn-warning">Edit</a>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="5" class="text-center py-4 text-muted">Data peminjaman tidak ditemukan</td></tr>
    @endforelse
</tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="lihatArsipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 95%;"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center" id="fileContainer" style="min-height: 70vh;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var lihatModal = document.getElementById('lihatArsipModal');
    lihatModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var fileUrl = button.getAttribute('data-file');
        var namaArsip = button.getAttribute('data-nama');
        
        console.log("File URL:", fileUrl);
        
        var modalTitle = lihatModal.querySelector('.modal-title');
        var fileContainer = lihatModal.querySelector('#fileContainer');
        
        modalTitle.textContent = 'File: ' + namaArsip;
        
        // Pengecekan agar modal tampil lebih besar
        if (fileUrl) {
            if(fileUrl.toLowerCase().endsWith('.pdf')) {
                // Tinggi diatur ke 700px agar lebih lega
                fileContainer.innerHTML = '<embed src="'+fileUrl+'" width="100%" height="900px" type="application/pdf">';
            } else {
                // Gambar dibuat memenuhi lebar modal (100%)
                fileContainer.innerHTML = '<img src="'+fileUrl+'" class="img-fluid" style="width: 100%;">';
            }
        } else {
            fileContainer.innerHTML = '<p class="text-danger">File tidak ditemukan atau path kosong di database.</p>';
        }
    });
</script>
@endpush