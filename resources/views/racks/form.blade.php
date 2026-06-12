@csrf
<div class="row g-3">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Nama Rak</label>
            <input type="text" name="nama_rak" value="{{ old('nama_rak', $rack->nama_rak ?? '') }}" class="form-control" required>
            @error('nama_rak')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Kabinet</label>
            <select name="cabinet_id" class="form-select" required>
                <option value="">Pilih kabinet</option>
                @foreach($cabinets as $cabinet)
                    <option value="{{ $cabinet->id }}" {{ old('cabinet_id', $rack->cabinet_id ?? '') == $cabinet->id ? 'selected' : '' }}>{{ $cabinet->nama_kabinet }} - {{ $cabinet->location->nama_lokasi ?? '-' }}</option>
                @endforeach
            </select>
            @error('cabinet_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
