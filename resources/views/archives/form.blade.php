@csrf
<div class="row g-3">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Nomor Surat / Arsip</label>
            <input type="text" name="nomor_surat" value="{{ old('nomor_surat', $archive->nomor_surat ?? '') }}" class="form-control" required>
            @error('nomor_surat')<div class="text-danger mt-1">{{ $message }}</div>@enderror
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
            <label class="form-label">Klasifikasi / Jenis Arsip</label>
            <select name="jenis_arsip_id" class="form-select" required>
                <option value="">Pilih klasifikasi</option>
                @foreach($klasifikasi as $item)
                    <option value="{{ $item->id }}" {{ old('jenis_arsip_id', $archive->jenis_arsip_id ?? '') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama_jenis }}
                    </option>
                @endforeach
            </select>
            @error('jenis_arsip_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-8">
        <div class="mb-3">
            <label class="form-label">Lokasi Penyimpanan (Ruangan - Lemari - Rak)</label>
            <select name="lokasi_id" class="form-select" required>
                <option value="">Pilih lokasi detail</option>
                @foreach($lokasi as $lok)
                    <option value="{{ $lok->id }}" {{ old('lokasi_id', $archive->lokasi_id ?? '') == $lok->id ? 'selected' : '' }}>
                        Ruangan: {{ $lok->ruangan }} | Lemari: {{ $lok->lemari }} | Rak: {{ $lok->rak }}
                    </option>
                @endforeach
            </select>
            @error('lokasi_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Tahun</label>
            <input type="number" name="tahun" min="1900" max="2100" value="{{ old('tahun', $archive->tahun ?? date('Y')) }}" class="form-control" required>
            @error('tahun')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Tanggal Arsip</label>
            <input type="date" name="tanggal_arsip" value="{{ old('tanggal_arsip', isset($archive->tanggal_arsip) ? (\Carbon\Carbon::parse($archive->tanggal_arsip)->format('Y-m-d')) : '') }}" class="form-control" required>
            @error('tanggal_arsip')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Status Dokumen</label>
            <select name="status" class="form-select" required>
                <option value="Aktif" {{ old('status', $archive->status ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="Inaktif" {{ old('status', $archive->status ?? '') == 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
            </select>
            @error('status')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-3">
        <div class="mb-3">
            <label class="form-label">Ketersediaan Fisik</label>
            <select name="status_ketersediaan" class="form-select" required>
                <option value="Tersedia" {{ old('status_ketersediaan', $archive->status_ketersediaan ?? '') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="Dipinjam" {{ old('status_ketersediaan', $archive->status_ketersediaan ?? '') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
            </select>
            @error('status_ketersediaan')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-12">
        <div class="mb-3">
            <label class="form-label">Perihal / Uraian Surat</label>
            <textarea name="perihal_surat" class="form-control" rows="3">{{ old('perihal_surat', $archive->perihal_surat ?? '') }}</textarea>
            @error('perihal_surat')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>