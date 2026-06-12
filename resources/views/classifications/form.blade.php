@csrf
<div class="row g-3">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Nama Klasifikasi</label>
            <input type="text" name="nama_klasifikasi" value="{{ old('nama_klasifikasi', $classification->nama_klasifikasi ?? '') }}" class="form-control" required>
            @error('nama_klasifikasi')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Kode Klasifikasi</label>
            <input type="text" name="kode_klasifikasi" value="{{ old('kode_klasifikasi', $classification->kode_klasifikasi ?? '') }}" class="form-control" required>
            @error('kode_klasifikasi')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12">
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $classification->deskripsi ?? '') }}</textarea>
            @error('deskripsi')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
