@extends('layouts.dashboard')

@section('title', 'Ubah Arsip')
@section('subtitle', 'Perbarui data arsip saat lokasi, status, atau klasifikasi berubah.')

@section('content')
    <div class="card card-soft shadow-sm">
        <div class="card-header card-header-soft">
            <h5 class="mb-0">Form Edit Arsip</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('arsip.update', $archive) }}" method="POST">
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
