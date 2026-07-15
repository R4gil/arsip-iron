@extends('layouts.dashboard')

@section('title', 'Ubah Rak')

@section('content')
@include('partials.page-header', ['title' => 'Ubah Rak', 'subtitle' => 'Perbarui data rak penyimpanan.'])
@include('partials.lokasi-hierarchy')

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
            <i class="fas fa-edit me-2" style="color: #d4af37;"></i>Form Edit Rak
        </h6>
        <form action="{{ route('rak.update', $rack) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Lokasi (Ruangan) <span class="text-danger">*</span></label>
                    <select name="ruangarsip_id" id="location_select" class="form-select" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                        <option value="">Pilih lokasi...</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ old('ruangarsip_id', $rack->cabinet?->ruangarsip_id) == $location->id ? 'selected' : '' }}>{{ $location->ruangan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Lemari <span class="text-danger">*</span></label>
                    <select name="lemari_id" id="cabinet_select" class="form-select" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                        <option value="">Pilih lemari...</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Nama Rak <span class="text-danger">*</span></label>
                    <input type="text" name="rak_nama" class="form-control" value="{{ old('rak_nama', $rack->rak_nama) }}" required
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Keterangan</label>
                    <textarea name="rak_keterangan" class="form-control" rows="2"
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc;">{{ old('rak_keterangan', $rack->rak_keterangan) }}</textarea>
                </div>
            </div>
            <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                <a href="{{ route('rak.index') }}" class="btn btn-light px-4 py-2" style="border-radius: 8px; font-weight: 600; border: 1.5px solid #e2e8f0;">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
                <button type="submit" class="btn fw-bold px-4 py-2" style="border-radius: 8px; background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; border: none; box-shadow: 0 3px 10px rgba(212, 175, 55, 0.3);">
                    <i class="fas fa-save me-2"></i>Perbarui Rak
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15) !important;
        background-color: #fff !important;
    }
    .form-control:hover, .form-select:hover { border-color: #cbd5e1 !important; }
    .card { transition: box-shadow 0.3s ease; }
    .card:hover { box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important; }
</style>
@endsection

@push('scripts')
<script>
    window._cabinets = @json($cabinets ?? []);
    document.addEventListener('DOMContentLoaded', function () {
        const locationSelect = document.getElementById('location_select');
        const cabinetSelect = document.getElementById('cabinet_select');
        const selectedCabinet = '{{ old('lemari_id', $rack->lemari_id) }}';

        async function loadCabinets(locationId) {
            cabinetSelect.innerHTML = '<option value="">Pilih lemari...</option>';
            if (!locationId) return;

            let items = [];
            try {
                const response = await fetch(`{{ route('ajax.cabinets') }}?location_id=${encodeURIComponent(locationId)}`, { credentials: 'same-origin' });
                if (response.ok) items = await response.json();
            } catch (e) { items = []; }

            if (!items.length) {
                items = (window._cabinets || []).filter(c => String(c.ruangarsip_id) === String(locationId));
            }

            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id ?? item.lemari_id;
                option.textContent = item.nama_lemari ?? item.lemari_nama;
                if (selectedCabinet && selectedCabinet === String(option.value)) option.selected = true;
                cabinetSelect.appendChild(option);
            });
        }

        locationSelect.addEventListener('change', () => loadCabinets(locationSelect.value));
        if (locationSelect.value) loadCabinets(locationSelect.value);
    });
</script>
@endpush