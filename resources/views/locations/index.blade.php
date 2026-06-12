@extends('layouts.dashboard')

@section('title', 'Master Lokasi')
@section('subtitle', 'Kelola lokasi penyimpanan kabinet dan rak arsip.')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Master Lokasi</h4>
            <p class="text-muted mb-0">Tambah, ubah, atau hapus lokasi fisik untuk pengelolaan arsip.</p>
        </div>
        <a href="{{ route('locations.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Lokasi</a>
    </div>

    <div class="card card-soft shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle" id="locationsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Lokasi</th>
                            <th>Alamat</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $location)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $location->nama_lokasi }}</td>
                                <td>{{ $location->alamat }}</td>
                                <td>{{ Str::limit($location->keterangan, 50) }}</td>
                                <td>
                                    <a href="{{ route('locations.edit', $location) }}" class="btn btn-sm btn-soft-primary me-1">Edit</a>
                                    <form action="{{ route('locations.destroy', $location) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus lokasi ini?');">
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
        const table = document.querySelector('#locationsTable');
        if (table && window.DataTable) {
            new DataTable(table, { searchable: true, fixedHeight: true });
        }
    });
</script>
@endpush
