@extends('layouts.dashboard')

@section('title', 'Master Rak')
@section('subtitle', 'Kelola rak berdasarkan kabinet untuk penyimpanan arsip yang terstruktur.')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Master Rak</h4>
            <p class="text-muted mb-0">Atur rak dan hubungkan dengan kabinet yang sesuai.</p>
        </div>
        <a href="{{ route('racks.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Rak</a>
    </div>

    <div class="card card-soft shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle" id="racksTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Rak</th>
                            <th>Kabinet</th>
                            <th>Lokasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($racks as $rack)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $rack->nama_rak }}</td>
                                <td>{{ $rack->cabinet->nama_kabinet ?? '-' }}</td>
                                <td>{{ $rack->cabinet->location->nama_lokasi ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('racks.edit', $rack) }}" class="btn btn-sm btn-soft-primary me-1">Edit</a>
                                    <form action="{{ route('racks.destroy', $rack) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus rak ini?');">
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
        const table = document.querySelector('#racksTable');
        if (table && window.DataTable) {
            new DataTable(table, { searchable: true, fixedHeight: true });
        }
    });
</script>
@endpush
