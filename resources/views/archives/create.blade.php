@extends('layouts.dashboard')

@section('title', 'Tambah Arsip Baru')
@section('subtitle', 'Isi data arsip dan simpan dengan cepat ke dalam sistem IRON SMART.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Tambah Arsip</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('arsip.store') }}" method="POST">
                @include('archives.form')
                <div class="mt-4">
                    <a href="{{ route('arsip.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Arsip</button>
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
</script>
@endpush
