@csrf
<div class="row g-3">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Nomor Arsip</label>
            <input type="text" name="nomor_arsip" value="{{ old('nomor_arsip', $archive->nomor_arsip ?? '') }}" class="form-control" required>
            @error('nomor_arsip')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Nama Arsip</label>
            <input type="text" name="nama_arsip" value="{{ old('nama_arsip', $archive->nama_arsip ?? '') }}" class="form-control" required>
            @error('nama_arsip')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Klasifikasi</label>
            <select name="classification_id" class="form-select" required>
                <option value="">Pilih klasifikasi</option>
                @foreach($classifications as $classification)
                    <option value="{{ $classification->id }}" {{ old('classification_id', $archive->classification_id ?? '') == $classification->id ? 'selected' : '' }}>{{ $classification->nama }}</option>
                @endforeach
            </select>
            @error('classification_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <select name="location_id" id="selectLocation" class="form-select" required>
                <option value="">Pilih lokasi</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}" {{ old('location_id', $archive->location_id ?? '') == $location->id ? 'selected' : '' }}>{{ $location->nama_lokasi }}</option>
                @endforeach
            </select>
            @error('location_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Kabinet</label>
            <select name="cabinet_id" id="selectCabinet" class="form-select" required>
                <option value="">Pilih kabinet</option>
                @foreach($cabinets as $cabinet)
                    <option value="{{ $cabinet->id }}" data-location="{{ $cabinet->location_id }}" {{ old('cabinet_id', $archive->cabinet_id ?? '') == $cabinet->id ? 'selected' : '' }}>{{ $cabinet->nama_kabinet }}</option>
                @endforeach
            </select>
            @error('cabinet_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Rak</label>
            <select name="rack_id" id="selectRack" class="form-select" required>
                <option value="">Pilih rak</option>
                @foreach($racks as $rack)
                    <option value="{{ $rack->id }}" data-cabinet="{{ $rack->cabinet_id }}" {{ old('rack_id', $archive->rack_id ?? '') == $rack->id ? 'selected' : '' }}>{{ $rack->nama_rak }}</option>
                @endforeach
            </select>
            @error('rack_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" min="1900" max="2100" value="{{ old('tahun', $archive->tahun ?? '') }}" class="form-control" required>
            @error('tahun')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Tanggal Arsip</label>
            <input type="date" name="tanggal_arsip" value="{{ old('tanggal_arsip', optional($archive->tanggal_arsip)->format('Y-m-d')) }}" class="form-control" required>
            @error('tanggal_arsip')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="tersedia" {{ old('status', $archive->status ?? '') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="dipinjam" {{ old('status', $archive->status ?? '') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="inaktif" {{ old('status', $archive->status ?? '') == 'inaktif' ? 'selected' : '' }}>Inaktif</option>
            </select>
            @error('status')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-12">
        <div class="mb-3">
            <label class="form-label">Uraian</label>
            <textarea name="uraian" class="form-control" rows="3">{{ old('uraian', $archive->uraian ?? '') }}</textarea>
            @error('uraian')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
