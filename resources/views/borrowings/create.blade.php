@extends('layouts.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-dark">Kelola Peminjaman Arsip</h1>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="arsipTable">
                    <thead class="table-light">
                        <tr class="text-uppercase text-secondary" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                            <th class="py-3 ps-4">No Surat</th>
                            <th class="py-3">Nama Arsip</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archives as $arsip)
                        <tr>
                            <td class="ps-4 fw-medium text-dark">{{ $arsip->nomor_surat }}</td>
                            <td class="text-secondary">{{ $arsip->nama_arsip }}</td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2 {{ $arsip->status_ketersediaan == 'Tersedia' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                    {{ $arsip->status_ketersediaan }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    @if(!empty($arsip->file_arsip))
                                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#lihatArsipModal" 
                                                data-file="{{ route('arsip.view', $arsip->file_arsip) }}" 
                                                data-nama="{{ $arsip->nama_arsip }}">
                                            <i class="bi bi-eye"></i> Lihat
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-light rounded-pill px-3" disabled>Kosong</button>
                                    @endif

                                    @if($arsip->status_ketersediaan == 'Tersedia')
                                        <form action="{{ route('borrowings.store') }}" method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="arsip_id" value="{{ $arsip->id }}">
                                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3">Pinjam</button>
                                        </form>
                                    @else
                                        <form action="{{ route('borrowings.return', $arsip->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3 text-white">Kembalikan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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

@push('styles')
<style>
    /* Paksa iframe agar tidak meluap keluar dari modal */
    #fileContainer iframe {
        width: 100% !important;
        height: 100% !important;
        border: none !important;
        display: block;
    }

    /* Memastikan modal tidak tertutup sidebar atau navbar */
    #lihatArsipModal {
        z-index: 1060 !important;
    }
</style>
@endpush

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