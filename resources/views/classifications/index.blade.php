@extends('layouts.dashboard')

@section('title', 'Master Klasifikasi')
@section('subtitle', 'Kelola kategori klasifikasi arsip untuk pemisahan dokumen yang konsisten.')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Master Klasifikasi</h4>
            <p class="text-muted mb-0">Tambah, edit, atau hapus klasifikasi untuk arsip.</p>
        </div>
        <a href="{{ route('classifications.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Klasifikasi</a>
    </div>

    <div class="card card-soft shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle" id="classificationsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Klasifikasi</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classifications as $classification)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $classification->nama_klasifikasi }}</td>
                                <td>{{ Str::limit($classification->deskripsi, 60) }}</td>
                                <td>
                                    <a href="{{ route('classifications.edit', $classification) }}" class="btn btn-sm btn-soft-primary me-1">Edit</a>
                                    <form action="{{ route('classifications.destroy', $classification) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus klasifikasi ini?');">
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
        const table = document.querySelector('#classificationsTable');
        if (table && window.DataTable) {
            new DataTable(table, { searchable: true, fixedHeight: true });
        }
    });
</script>
@endpush
