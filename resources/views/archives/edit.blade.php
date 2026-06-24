@extends('layouts.dashboard')

@section('title', 'Ubah Arsip')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Edit Arsip</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('arsip.update', $arsip->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                @include('archives.form')

                <div class="mt-4">
                    <a href="{{ route('arsip.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Perbarui Arsip</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const utamaSelect = document.getElementById('klasifikasi_utama');
    const subSelect = document.getElementById('klasifikasi_sub');
    const detailSelect = document.getElementById('klasifikasi_id');
    
    const inputInti = document.getElementById('nomor_surat_inti');
    const hiddenNomor = document.getElementById('nomor_surat');
    const labelKode = document.getElementById('label_kode_klasifikasi');
    const labelTahun = document.getElementById('label_tahun_otomatis');

    const currentUtama = "{{ $arsip->klasifikasi_utama_id ?? '' }}";
    const currentSub = "{{ $arsip->klasifikasi_sub_id ?? '' }}";
    const currentDetail = "{{ $arsip->klasifikasi_id ?? '' }}";

    // Fungsi Gabung Nomor Surat
    function syncNomorSurat() {
        const prefix = "WIM.11.IMI.2-";
        const kode = labelKode.innerText.replace(' -', '').trim();
        const inti = inputInti.value;
        const tahun = labelTahun.innerText; 
        
        if (kode !== '...' && inti !== '') {
            hiddenNomor.value = prefix + kode + "-" + inti + tahun;
        }
    }

    // Fungsi Fetch Data
    async function fetchData(parentId, targetSelect, selectedValue) {
        if (!parentId) return;
        const response = await fetch(`/api/get-sub-klasifikasi/${parentId}`);
        const data = await response.json();
        
        targetSelect.innerHTML = '<option value="">-- Pilih --</option>';
        data.forEach(item => {
            const selected = (item.id == selectedValue) ? 'selected' : '';
            targetSelect.innerHTML += `<option value="${item.id}" data-kode="${item.kode}" ${selected}>${item.kode} - ${item.nama}</option>`;
        });
        targetSelect.disabled = false;
    }

    // Event saat Detail dipilih (Update Label Kode)
    detailSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            labelKode.innerText = selectedOption.getAttribute('data-kode') + ' -';
        } else {
            labelKode.innerText = '... -';
        }
        syncNomorSurat();
    });

    // Event saat input nomor inti diketik
    inputInti.addEventListener('input', syncNomorSurat);

    // AUTO-LOAD DATA MODE EDIT
    if (currentUtama) {
        utamaSelect.value = currentUtama;
        fetchData(currentUtama, subSelect, currentSub).then(() => {
            if (currentSub) {
                fetchData(currentSub, detailSelect, currentDetail).then(() => {
                    // Set label kode setelah detail terload
                    const selectedDetail = detailSelect.options[detailSelect.selectedIndex];
                    if (selectedDetail && selectedDetail.value) {
                        labelKode.innerText = selectedDetail.getAttribute('data-kode') + ' -';
                    }
                    syncNomorSurat();
                });
            }
        });
    }

    // Event Listener Manual
    utamaSelect.addEventListener('change', function() {
        subSelect.innerHTML = '<option value="">-- Pilih Sub --</option>';
        detailSelect.innerHTML = '<option value="">-- Pilih Detail --</option>';
        labelKode.innerText = '... -';
        fetchData(this.value, subSelect, null);
    });

    subSelect.addEventListener('change', function() {
        detailSelect.innerHTML = '<option value="">-- Pilih Detail --</option>';
        labelKode.innerText = '... -';
        fetchData(this.value, detailSelect, null);
    });
});
</script>
@endpush