@extends('layouts.dashboard')

@section('title', 'Master Kabinet')
@section('subtitle', 'Kelola kabinet akan terikat pada lokasi tertentu untuk manajemen arsip cepat.')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4>Master Kabinet</h4>
            <p class="text-muted mb-0">Maintain kabinet berdasarkan lokasi penyimpanan arsip.</p>
        </div>
        <a href="{{ route('cabinets.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Kabinet</a>
    </div>

    <div class="card card-soft shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle" id="cabinetsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Kabinet</th>
                            <th>Lokasi</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cabinets as $cabinet)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $cabinet->nama_kabinet }}</td>
                                <td>{{ $cabinet->location->nama_lokasi ?? '-' }}</td>
                                <td>{{ Str::limit($cabinet->keterangan, 50) }}</td>
                                <td>
                                    <a href="{{ route('cabinets.edit', $cabinet) }}" class="btn btn-sm btn-soft-primary me-1">Edit</a>
                                    <form action="{{ route('cabinets.destroy', $cabinet) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus kabinet ini?');">
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
        const table = document.querySelector('#cabinetsTable');
        if (table && window.DataTable) {
            new DataTable(table, { searchable: true, fixedHeight: true });
        }
    });
</script>
@endpush
