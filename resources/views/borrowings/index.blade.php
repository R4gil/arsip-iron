@extends('layouts.dashboard')

@section('title', 'Manajemen Peminjaman')
@section('subtitle', 'Lihat riwayat peminjaman arsip dan catat pengembalian dengan cepat.')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Daftar Peminjaman</h4>
            <p class="text-muted mb-0">Riwayat peminjaman arsip lengkap dengan tanggal kembali dan status persetujuan.</p>
        </div>
        <a href="{{ route('borrowings.create') }}" class="btn btn-primary"><i class="fas fa-book-reader me-2"></i>Tambah Peminjaman</a>
    </div>

    <div class="card card-soft shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle" id="borrowingsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pengguna</th>
                            <th>Arsip</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($borrowings as $borrowing)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $borrowing->user->name }}</td>
                                <td>{{ $borrowing->archive->nama_arsip }}</td>
                                <td>{{ $borrowing->tanggal_pinjam->format('d M Y') }}</td>
                                <td>{{ $borrowing->tanggal_kembali ? $borrowing->tanggal_kembali->format('d M Y') : '-' }}</td>
                                <td><span class="badge bg-{{ $borrowing->status === 'dipinjam' ? 'warning text-dark' : ($borrowing->status === 'selesai' ? 'success' : 'secondary') }}">{{ ucfirst($borrowing->status) }}</span></td>
                                <td>
                                    <a href="{{ route('borrowings.edit', $borrowing) }}" class="btn btn-sm btn-soft-primary me-1">Edit</a>
                                    <form action="{{ route('borrowings.destroy', $borrowing) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus transaksi peminjaman?');">
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
        const table = document.querySelector('#borrowingsTable');
        if (table && window.DataTable) {
            new DataTable(table, { searchable: true, fixedHeight: true });
        }
    });
</script>
@endpush
