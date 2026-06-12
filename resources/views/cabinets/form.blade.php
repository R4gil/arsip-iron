@csrf
<div class="row g-3">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Nama Kabinet</label>
            <input type="text" name="nama_kabinet" value="{{ old('nama_kabinet', $cabinet->nama_kabinet ?? '') }}" class="form-control" required>
            @error('nama_kabinet')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <select name="location_id" class="form-select" required>
                <option value="">Pilih lokasi</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" {{ old('location_id', $cabinet->location_id ?? '') == $location->id ? 'selected' : '' }}>{{ $location->nama_lokasi }}</option>
                @endforeach
            </select>
            @error('location_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12">
        <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $cabinet->keterangan ?? '') }}</textarea>
            @error('keterangan')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
