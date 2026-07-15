@csrf
<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label font-weight-bold">Arsip</label>
            <select name="arsip_id" class="form-control select2" required>
                <option value="">-- Pilih Arsip --</option>
                @foreach($archives as $archive)
                    <option value="{{ $archive->id }}" {{ old('arsip_id', $borrowing->arsip_id ?? '') == $archive->id ? 'selected' : '' }}>
                        {{ $archive->nama_arsip }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label font-weight-bold">Nama Peminjam</label>
            <input type="text" name="nama_peminjam" value="{{ old('nama_peminjam', $borrowing->nama_peminjam ?? '') }}" class="form-control" required>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label font-weight-bold">Divisi</label>
            <input type="text" name="divisi_peminjam" value="{{ old('divisi_peminjam', $borrowing->divisi_peminjam ?? '') }}" class="form-control" required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label font-weight-bold">Tanggal Pinjam</label>
            <input type="date" name="tanggal_keluar" value="{{ old('tanggal_keluar', isset($borrowing->tanggal_keluar) ? \Carbon\Carbon::parse($borrowing->tanggal_keluar)->format('Y-m-d') : '') }}" class="form-control" required>
        </div>
    </div>
</div>