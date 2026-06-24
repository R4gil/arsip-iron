@extends('layouts.dashboard')

@section('title', 'Tambah Arsip Baru')
@section('subtitle', 'Isi data arsip dan simpan dengan cepat ke dalam sistem IRON SMART.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h4 class="mb-0">Form Tambah Arsip</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('arsip.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('archives.form') 
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Arsip</button>
                    <a href="{{ route('arsip.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function filterCabinetOptions() {
        const selectedLocation = document.querySelector('#selectLocation').value;
        document.querySelectorAll('#selectCabinet option').forEach(option => {
            const location = option.dataset.location;
            option.hidden = option.value !== '' && location !== selectedLocation;
        });
    }

    function filterRackOptions() {
        const selectedCabinet = document.querySelector('#selectCabinet').value;
        document.querySelectorAll('#selectRack option').forEach(option => {
            const cabinet = option.dataset.cabinet;
            option.hidden = option.value !== '' && cabinet !== selectedCabinet;
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const locationSelect = document.querySelector('#selectLocation');
        const cabinetSelect = document.querySelector('#selectCabinet');

        if (locationSelect) {
            locationSelect.addEventListener('change', () => {
                filterCabinetOptions();
                cabinetSelect.value = '';
                filterRackOptions();
            });
            filterCabinetOptions();
        }

        if (cabinetSelect) {
            cabinetSelect.addEventListener('change', () => {
                filterRackOptions();
                document.querySelector('#selectRack').value = '';
            });
            filterRackOptions();
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
    const utamaSelect = document.getElementById('klasifikasi_utama');
    const subSelect = document.getElementById('klasifikasi_sub');
    const detailSelect = document.getElementById('klasifikasi_id');
    const previewText = document.getElementById('text_preview_klasifikasi');

    // 1. KETIKA LEVEL 1 (UTAMA) DIUBAH
    utamaSelect.addEventListener('change', function () {
        const parentId = this.value;
        
        // Reset dropdown anak & cucu
        subSelect.innerHTML = '<option value="">-- Pilih Sub Klasifikasi --</option>';
        detailSelect.innerHTML = '<option value="">-- Pilih Detail Klasifikasi --</option>';
        subSelect.disabled = true;
        detailSelect.disabled = true;
        previewText.textContent = '';

        if (parentId) {
            fetch(`/api/get-sub-klasifikasi/${parentId}`)
                .then(response => response.json())
                .then(data => {
                    if(data.length > 0) {
                        subSelect.disabled = false;
                        data.forEach(item => {
                            subSelect.innerHTML += `<option value="${item.id}">${item.kode} - ${item.nama}</option>`;
                        });
                    }
                });
        }
    });

    // 2. KETIKA LEVEL 2 (SUB) DIUBAH
    subSelect.addEventListener('change', function () {
        const parentId = this.value;

        // Reset dropdown cucu
        detailSelect.innerHTML = '<option value="">-- Pilih Detail Klasifikasi --</option>';
        detailSelect.disabled = true;
        previewText.textContent = '';

        if (parentId) {
            fetch(`/api/get-sub-klasifikasi/${parentId}`)
                .then(response => response.json())
                .then(data => {
                    if(data.length > 0) {
                        detailSelect.disabled = false;
                        data.forEach(item => {
                            detailSelect.innerHTML += `<option value="${item.id}">${item.kode} - ${item.nama}</option>`;
                        });
                    }
                });
        }
    });

    // 3. KETIKA LEVEL 3 (DETAIL) DIUBAH (Menampilkan teks biru di bawahnya)
    detailSelect.addEventListener('change', function () {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            previewText.textContent = selectedOption.text;
        } else {
            previewText.textContent = '';
        }
    });
});
</script>
@endpush
