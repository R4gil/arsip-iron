@csrf
<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label font-weight-bold">Nama Peminjam</label>
            <input type="text" name="nama_peminjam" value="{{ old('nama_peminjam', $borrowing->nama_peminjam ?? '') }}" class="form-control" required placeholder="Masukkan nama peminjam">
            @error('nama_peminjam')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label font-weight-bold">Arsip</label>
            <select name="arsip_id" class="form-control select2-js" required>
                <option value="">-- Pilih Arsip --</option>
                @foreach($archives as $archive)
                    <option value="{{ $archive->id }}" {{ old('arsip_id', $borrowing->arsip_id ?? '') == $archive->id ? 'selected' : '' }}>
                        {{ $archive->nomor_surat ?? 'No-Surat-Kosong' }} - {{ $archive->nama_arsip }}
                    </option>
                @endforeach
            </select>
            @error('arsip_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="form-label font-weight-bold">Divisi Peminjam</label>
            <input type="text" name="divisi_peminjam" value="{{ old('divisi_peminjam', $borrowing->divisi_peminjam ?? '') }}" class="form-control" required placeholder="Contoh: IT / HRD">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="form-label font-weight-bold">Tanggal Pinjam</label>
            <input type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar', isset($borrowing) && $borrowing->tanggal_keluar ? \Carbon\Carbon::parse($borrowing->tanggal_keluar)->format('Y-m-d') : date('Y-m-d')) }}" class="form-control">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="form-label font-weight-bold">Status Peminjaman</label>
            <select name="status_pinjam" class="form-control" required>
                <option value="Dipinjam" {{ old('status_pinjam', $borrowing->status_pinjam ?? '') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="Dikembalikan" {{ old('status_pinjam', $borrowing->status_pinjam ?? '') == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                <option value="Terlambat" {{ old('status_pinjam', $borrowing->status_pinjam ?? '') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
            </select>
        </div>
    </div>
</div>

<div class="form-group mb-3">
    <label class="form-label font-weight-bold">Keterangan Kondisi</label>
    <textarea name="keterangan_kondisi" class="form-control" rows="3">{{ old('keterangan_kondisi', $borrowing->keterangan_kondisi ?? '') }}</textarea>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (jQuery().select2) {
            $('.select2-js').select2({
                theme: 'bootstrap',
                width: '100%'
            });
        }
    });
</script>