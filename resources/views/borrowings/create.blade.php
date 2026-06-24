@extends('layouts.dashboard')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<div class="card shadow-sm p-4">
    <h1 class="h4 mb-3">Form Peminjaman Baru</h1>
    
    <form method="POST" action="{{ route('borrowings.store') }}">
        @csrf
        <div class="row">
                <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label font-weight-bold">Nama Arsip</label>
                    <select name="arsip_id" class="form-control select2-js" data-placeholder="Cari arsip..." required>
                        <option value="">-- Cari atau Pilih Arsip --</option>
                        @foreach($archives as $arsip)
                            <option value="{{ $arsip->id }}" data-status="{{ $arsip->status_ketersediaan }}">{{ $arsip->nama_arsip }} ({{ $arsip->status_ketersediaan }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label font-weight-bold">Nama Pengguna</label>
                    <select name="user_id" class="form-control select2-js" data-placeholder="Cari pengguna..." required>
                        <option value="">-- Cari atau Pilih Pengguna --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->nama_pengguna }} ({{ $user->username }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Unit Kerja</label>
                    <select name="unit_kerja_id" class="form-control select2-js" style="width: 100%;" required>
                        <option value="">-- Cari atau Pilih Unit Kerja --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->unit_kerja }}" {{ old('unit_kerja_id') == $unit->unit_kerja ? 'selected' : '' }}>
                                    {{ $unit->unit_kerja }}
                                </option>
                            @endforeach
                    </select>
                    @error('unit_kerja_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label font-weight-bold">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_keluar" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
        <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>
</div>



@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Inisialisasi Select2 untuk semua elemen dengan class 'select2-js'
        $('.select2-js').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih...",
            allowClear: true,
            width: '100%' // Penting untuk memastikan lebar select2 sesuai
        });
    });
</script>

@endpush