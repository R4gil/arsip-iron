@csrf
<div class="row g-3">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Nama Lokasi</label>
            <input type="text" name="nama_lokasi" value="{{ old('nama_lokasi', $location->nama_lokasi ?? '') }}" class="form-control" required>
            @error('nama_lokasi')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat" value="{{ old('alamat', $location->alamat ?? '') }}" class="form-control">
            @error('alamat')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12">
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $location->keterangan ?? '') }}</textarea>
            @error('keterangan')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
