@csrf
<div class="row g-3">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">User</label>
            <select name="user_id" class="form-select" required>
                <option value="">Pilih user</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $borrowing->user_id ?? '') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            @error('user_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label class="form-label">Arsip</label>
            <select name="archive_id" class="form-select" required>
                <option value="">Pilih arsip</option>
                @foreach($archives as $archive)
                    <option value="{{ $archive->id }}" {{ old('archive_id', $borrowing->archive_id ?? '') == $archive->id ? 'selected' : '' }}>{{ $archive->nomor_arsip }} - {{ $archive->nama_arsip }}</option>
                @endforeach
            </select>
            @error('archive_id')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Tanggal Pinjam</label>
            <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', optional($borrowing->tanggal_pinjam)->format('Y-m-d')) }}" class="form-control" required>
            @error('tanggal_pinjam')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Tanggal Kembali</label>
            <input type="date" name="tanggal_kembali" value="{{ old('tanggal_kembali', optional($borrowing->tanggal_kembali)->format('Y-m-d')) }}" class="form-control">
            @error('tanggal_kembali')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-4">
        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="dipinjam" {{ old('status', $borrowing->status ?? '') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="selesai" {{ old('status', $borrowing->status ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="ditolak" {{ old('status', $borrowing->status ?? '') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            @error('status')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-12">
        <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $borrowing->catatan ?? '') }}</textarea>
            @error('catatan')<div class="text-danger mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>
