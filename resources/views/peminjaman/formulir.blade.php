<form action="{{ route('peminjaman.store') }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label font-weight-bold">Nama Peminjam</label>
                <input type="text" name="nama_peminjam" class="form-control" required placeholder="Masukkan nama peminjam">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label font-weight-bold">Pilih Arsip</label>
                <select name="arsip_id" id="arsip_select" class="form-control select2-js" required style="width: 100%;">
                    <option value="">-- Cari atau Pilih Arsip --</option>
                    @foreach($archives as $archive)
                        <option value="{{ $archive->id }}">
                            {{ $archive->nomor_surat }} - {{ $archive->nama_arsip }} ({{ $archive->status_ketersediaan }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label font-weight-bold">Unit Kerja</label>
                <input type="text" name="divisi_peminjam" class="form-control" required placeholder="Contoh: IT / HRD">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label font-weight-bold">Tanggal Pinjam</label>
                <input type="date" name="tanggal_keluar" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>
    </div>

    <div class="form-group mb-3">
        <label class="form-label font-weight-bold">Keterangan Kondisi</label>
        <textarea name="keterangan_kondisi" class="form-control" rows="2"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Simpan Peminjaman</button>
</form>

<hr class="my-5">

<h5>Daftar Arsip (Referensi)</h5>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="arsipTable" style="width: 100%;">
        <thead>
            <tr>
                <th>No Surat</th>
                <th>Nama Arsip</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($archives as $archive)
            <tr>
                <td>{{ $archive->nomor_surat }}</td>
                <td>{{ $archive->nama_arsip }}</td>
                <td>{{ $archive->status_ketersediaan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi Select2
        $('#arsip_select').select2({
            theme: 'bootstrap-5',
            placeholder: "Cari atau Pilih Arsip...",
            allowClear: true,
            width: '100%'
        });

        // Inisialisasi DataTable
        $('#arsipTable').DataTable({
            "language": { "search": "Filter Arsip:" }
        });
    });
</script>
@endpush