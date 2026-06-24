@extends('layouts.dashboard')

@section('content')
<div class="card shadow-sm p-4">
    <h4 class="mb-4">Form Tambah Arsip</h4>
    
    <form action="{{ route('arsip.store') }}" method="POST" enctype="multipart/form-data" id="arsipForm">
        @csrf
        
        <div class="row g-4" style="font-family: 'Nunito', 'Segoe UI', sans-serif;">
            <div class="col-md-6">
                <div class="p-4 mb-4" style="border: 1px solid #e3e6f0; border-radius: 8px; background-color: #f8f9fa;">
                    <div class="mb-3 pb-2" style="border-bottom: 2px solid #eaecf4;">
                        <h6 class="m-0 fw-bold text-dark">KLASIFIKASI ARSIP</h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small mb-1">Klasifikasi Utama (Level 1)</label>
                            <select id="klasifikasi_utama" class="form-select text-dark" style="height: 42px; border-radius: 6px;">
                                <option value="">-- Pilih Klasifikasi Utama --</option>
                                @foreach($klasifikasi_utama as $item)
                                    <option value="{{ $item->id }}">{{ $item->kode }} - {{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small mb-1">Sub Klasifikasi (Level 2)</label>
                            <select id="klasifikasi_sub" class="form-select text-dark" style="height: 42px; border-radius: 6px;" disabled>
                                <option value="">-- Pilih Sub Klasifikasi --</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-secondary small mb-1">Detail Klasifikasi (Level 3)</label>
                            <select id="klasifikasi_id" name="klasifikasi_id" class="form-select text-primary fw-bold" style="height: 42px; border-radius: 6px;" disabled required>
                                <option value="">-- Pilih Detail Klasifikasi --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold text-secondary small mb-1">Status Dokumen</label>
                        <select name="status" class="form-select" style="height: 42px; border-radius: 6px;" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Inaktif">Inaktif</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label fw-semibold text-secondary small mb-1">Ketersediaan Fisik</label>
                        <select name="status_ketersediaan" class="form-select" style="height: 42px; border-radius: 6px;" required>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Dipinjam">Dipinjam</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small mb-1">Tanggal Arsip</label>
                    <input type="date" id="tanggal_arsip" name="tanggal_arsip" class="form-control" value="{{ date('Y-m-d') }}" style="height: 42px; border-radius: 6px;" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small mb-1">Tahun Arsip</label>
                    <input type="number" name="tahun" id="input_tahun" class="form-control" value="{{ date('Y') }}" style="height: 42px; border-radius: 6px;" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small mb-1">Nomor Surat</label>
                    <div class="input-group" style="height: 42px;">
                        <span class="input-group-text bg-light">WIM.11.IMI.2-</span>
                        <span id="label_kode_klasifikasi" class="input-group-text bg-light fw-bold text-primary">... -</span>
                        <input type="text" id="nomor_surat_inti" class="form-control fw-bold" placeholder="Contoh: 5460" required>
                        <span id="label_tahun_otomatis" class="input-group-text bg-light">/{{ date('Y') }}</span>
                    </div>
                    <input type="hidden" id="nomor_surat" name="nomor_surat">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small mb-1">Nama / Judul Arsip</label>
                    <input type="text" name="nama_arsip" class="form-control" style="height: 42px; border-radius: 6px;" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small mb-1">Klasifikasi / Jenis Arsip</label>
                    <select name="jenis_arsip_id" class="form-select" style="height: 42px; border-radius: 6px;" required>
                        <option value="">Pilih jenis arsip...</option>
                        @foreach($jenis_arsip as $item)<option value="{{ $item->id }}">{{ $item->nama_jenis }}</option>@endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary small mb-1">Lokasi Penyimpanan</label>
                    <select name="lokasi_id" class="form-select" style="height: 42px; border-radius: 6px;" required>
                        <option value="">Pilih lokasi...</option>
                        @foreach($lokasi_simpan as $lok)<option value="{{ $lok->id }}">Ruang: {{ $lok->ruangan }} | Lemari: {{ $lok->lemari }} | Rak: {{ $lok->rak }}</option>@endforeach
                    </select>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold text-secondary small mb-1">Perihal / Uraian Surat</label>
                <textarea name="perihal_surat" class="form-control" rows="3" style="border-radius: 6px;"></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold text-secondary small mb-1">Upload File Arsip</label>
                <input type="file" name="file_arsip" class="form-control" style="height: 42px; border-radius: 6px;">
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary px-4 py-2">Simpan Arsip</button>
                <a href="{{ route('arsip.index') }}" class="btn btn-secondary px-4 py-2">Batal</a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const utamaSelect = document.getElementById('klasifikasi_utama');
        const subSelect = document.getElementById('klasifikasi_sub');
        const detailSelect = document.getElementById('klasifikasi_id');
        const inputTahun = document.getElementById('input_tahun');
        const inputNomorInti = document.getElementById('nomor_surat_inti');
        const hiddenNomorSurat = document.getElementById('nomor_surat');
        const labelKlasifikasi = document.getElementById('label_kode_klasifikasi');
        const labelTahun = document.getElementById('label_tahun_otomatis');

        let kode = '...';

        function updateNomor() {
            const tahun = inputTahun.value || new Date().getFullYear();
            labelTahun.textContent = '/' + tahun;
            if (inputNomorInti.value) {
                hiddenNomorSurat.value = `WIM.11.IMI.2-${kode}-${inputNomorInti.value}/${tahun}`;
            }
        }

        utamaSelect.addEventListener('change', function() {
            fetch(`/api/get-sub-klasifikasi/${this.value}`)
                .then(r => r.json()).then(data => {
                    subSelect.innerHTML = '<option value="">-- Pilih Sub Klasifikasi --</option>';
                    data.forEach(i => subSelect.innerHTML += `<option value="${i.id}">${i.kode} - ${i.nama}</option>`);
                    subSelect.disabled = false;
                });
        });

        subSelect.addEventListener('change', function() {
            fetch(`/api/get-sub-klasifikasi/${this.value}`)
                .then(r => r.json()).then(data => {
                    detailSelect.innerHTML = '<option value="">-- Pilih Detail Klasifikasi --</option>';
                    data.forEach(i => detailSelect.innerHTML += `<option value="${i.id}" data-kode="${i.kode}">${i.kode} - ${i.nama}</option>`);
                    detailSelect.disabled = false;
                });
        });

        detailSelect.addEventListener('change', function() {
            kode = this.options[this.selectedIndex].getAttribute('data-kode');
            labelKlasifikasi.textContent = kode + ' -';
            updateNomor();
        });

        inputNomorInti.addEventListener('input', updateNomor);
        inputTahun.addEventListener('input', updateNomor);
    });
</script>
@endpush
@endsection