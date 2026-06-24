@extends('layouts.dashboard')

@section('content')
<header class="page-header">
    <h2>Manajemen Peminjaman</h2>
</header>

<div class="row">
    <div class="col-lg-12">
        <section class="card">
            <header class="card-header d-flex justify-content-between align-items-center">
                <h2 class="card-title">Daftar Peminjaman Arsip</h2>
                <a href="{{ route('borrowings.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Peminjaman
                </a>
            </header>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0" id="borrowingsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Peminjam</th>
                                <th>Arsip</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($borrowings as $borrowing)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $borrowing->nama_peminjam }} <br> <small class="text-muted">{{ $borrowing->divisi_peminjam }}</small></td>
                                    <td>{{ $borrowing->archive->nama_arsip ?? 'N/A' }}</td>
                                    <td>{{ $borrowing->tanggal_keluar ? \Carbon\Carbon::parse($borrowing->tanggal_keluar)->format('d M Y') : '-' }}</td>
                                    <td>{{ $borrowing->tanggal_masuk ? \Carbon\Carbon::parse($borrowing->tanggal_masuk)->format('d M Y') : '-' }}</td>
                                    <td>
                                        @php
                                            $badge = $borrowing->status_pinjam === 'Dipinjam' ? 'warning' : 'success';
                                        @endphp
                                        <span class="badge badge-{{ $badge }}">
                                            {{ $borrowing->status_pinjam }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($borrowing->status_pinjam === 'Dipinjam')
                                            <form action="{{ route('borrowings.update', $borrowing->id) }}" method="POST" class="d-inline-block">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="action" value="return">
                                                <button type="submit" class="btn btn-sm btn-success" title="Kembalikan Arsip">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <a href="{{ route('borrowings.edit', $borrowing->id) }}" class="btn btn-sm btn-info" title="Edit">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        
                                        <form action="{{ route('borrowings.destroy', $borrowing->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus data ini?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection