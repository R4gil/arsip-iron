@extends('layouts.dashboard')

@section('title', 'Daftar Arsip')
@section('subtitle', 'Kelola arsip, klasifikasi, lokasi dan status peminjaman di satu halaman.')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Daftar Arsip</h4>
            <p class="text-muted mb-0">Kelola pencatatan arsip lengkap dengan kategori dan lokasi penyimpanan.</p>
        </div>
        <a href="{{ route('arsip.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Arsip</a>
    </div>

    <div class="card card-soft shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle" id="archivesTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nomor</th>
                            <th>Nama Arsip</th>
                            <th>Klasifikasi</th>
                            <th>Lokasi</th>
                            <th>Rak</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archives as $archive)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $archive->nomor_arsip }}</td>
                                <td>{{ $archive->nama_arsip }}</td>
                                <td>{{ $archive->classification->nama ?? '-' }}</td>
                                <td>{{ $archive->location->nama_lokasi ?? '-' }}</td>
                                <td>{{ $archive->rack->nama_rak ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $archive->status === 'dipinjam' ? 'warning text-dark' : ($archive->status === 'inaktif' ? 'secondary' : 'success') }}">{{ ucfirst($archive->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('arsip.edit', $archive) }}" class="btn btn-sm btn-soft-primary me-1">Edit</a>
                                    <form action="{{ route('arsip.destroy', $archive) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus arsip ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const table = document.querySelector('#archivesTable');
        if (table && window.DataTable) {
            new DataTable(table, { searchable: true, fixedHeight: true });
        }
    });
</script>
@endpush
