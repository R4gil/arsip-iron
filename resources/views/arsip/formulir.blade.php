@php $arsip = $arsip ?? null; @endphp

<div class="row g-4">

    <!-- ======== KARTU 1: INFORMASI DOKUMEN ======== -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
                    <i class="fas fa-file-alt me-2" style="color: #d4af37;"></i>Informasi Dokumen
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Jenis Dokumen <span class="text-danger">*</span></label>
                        <select name="jenis_arsip_id" class="form-select" required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
                            <option value="">Pilih jenis dokumen...</option>
                            @foreach($jenis_arsips as $item)
                                <option value="{{ $item->id }}" {{ old('jenis_arsip_id', optional($arsip)->jenis_arsip_id ?? '') == $item->id ? 'selected' : '' }}>{{ $item->nama_jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Status Dokumen <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
                            <option value="Aktif" {{ old('status', $arsip->status ?? '') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Inaktif" {{ old('status', $arsip->status ?? '') == 'Inaktif' ? 'selected' : '' }}>Inaktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Ketersediaan <span class="text-danger">*</span></label>
                        <select name="status_ketersediaan" class="form-select" required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
                            <option value="Tersedia" {{ old('status_ketersediaan', $arsip->status_ketersediaan ?? '') == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="Dipinjam" {{ old('status_ketersediaan', $arsip->status_ketersediaan ?? '') == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======== KARTU 2: INFORMASI SURAT & RETENSI ======== -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
                    <i class="fas fa-envelope me-2" style="color: #d4af37;"></i>Informasi Surat
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Tanggal Arsip <span class="text-danger">*</span></label>
                        <input type="date" id="tanggal_arsip" name="tanggal_arsip" class="form-control"
                            value="{{ old('tanggal_arsip', isset($arsip->tanggal_arsip) ? date('Y-m-d', strtotime($arsip->tanggal_arsip)) : date('Y-m-d')) }}" required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
                    </div>
                    @if($retensiTersedia ?? false)
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Masa Retensi <span class="text-danger">*</span></label>
                        <select name="masa_retensi" class="form-select" required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
                            <option value="">Pilih masa retensi...</option>
                            @foreach(['3 Tahun', '5 Tahun', '10 Tahun'] as $opsi)
                                <option value="{{ $opsi }}" {{ old('masa_retensi', $arsip->masa_retensi ?? '') == $opsi ? 'selected' : '' }}>{{ $opsi }}</option>
                            @endforeach
                        </select>
                        <div class="mt-2 p-2 rounded" id="preview_tanggal_retensi"
                            style="display: none; background: linear-gradient(135deg, #fefce8, #fef9c3); color: #854d0e; font-weight: 600; font-size: 0.85rem; border-left: 3px solid #d4af37;">
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ======== KARTU 3: NOMOR DAN JUDUL ======== -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
                    <i class="fas fa-hashtag me-2" style="color: #d4af37;"></i>Nomor dan Judul
                </h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Mode Penomoran <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode_penomoran" id="mode_otomatis" value="otomatis" checked>
                                <label class="form-check-label fw-semibold" for="mode_otomatis" style="color: #334155; font-size: 0.85rem;">
                                    <i class="fas fa-cog me-1"></i>Otomatis
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="mode_penomoran" id="mode_manual" value="manual">
                                <label class="form-check-label fw-semibold" for="mode_manual" style="color: #334155; font-size: 0.85rem;">
                                    <i class="fas fa-edit me-1"></i>Manual
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Nomor Surat <span class="text-danger">*</span></label>
                        
                        <!-- Layout Otomatis -->
                        <div id="layout_otomatis">
                            <div class="input-group">
                                <span class="input-group-text fw-bold" style="background: #1e293b; color: #fff; border: 1.5px solid #1e293b; border-radius: 8px 0 0 8px; font-size: 0.85rem;">WIM.11.IMI.2-</span>
                                <input type="text" id="nomor_surat_inti" class="form-control fw-bold"
                                    placeholder="Contoh: 5460 atau kode klasifikasi"
                                    value="{{ old('nomor_surat_inti', isset($arsip->nomor_surat) ? preg_replace('/^WIM\.11\.IMI\.2\-(.*?)\/\d{4}$/', '$1', $arsip->nomor_surat) : '') }}"
                                    style="border-radius: 0; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem;">
                                <button type="button" class="btn" style="background: #475569; color: #fff; border: 1.5px solid #475569; border-radius: 0; padding: 0.6rem 1rem; font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#classificationSearchModal">
                                    <i class="fas fa-search me-1"></i>Cari Kode
                                </button>
                                <span id="label_tahun_otomatis" class="input-group-text fw-bold"
                                    style="background: #d4af37; color: #1e293b; border: 1.5px solid #d4af37; border-radius: 0 8px 8px 0; font-size: 0.9rem;">
                                    /{{ old('tahun_arsip', isset($arsip->tanggal_arsip) ? date('Y', strtotime($arsip->tanggal_arsip)) : date('Y')) }}
                                </span>
                            </div>
                            <div class="form-text mt-2" style="color: #64748b; font-size: 0.8rem;">
                                <i class="fas fa-info-circle me-1"></i>Isi nomor surat sendiri atau pilih kode klasifikasi untuk membangun nomor otomatis. Tahun akan menyesuaikan dengan tanggal arsip.
                            </div>
                        </div>

                        <!-- Layout Manual -->
                        <div id="layout_manual" style="display: none;">
                            <input type="text" id="nomor_surat_manual" class="form-control fw-bold"
                                placeholder="Masukkan nomor surat manual..."
                                value="{{ old('nomor_surat_manual', '') }}"
                                style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem;">
                            <div class="form-text mt-2" style="color: #64748b; font-size: 0.8rem;">
                                <i class="fas fa-info-circle me-1"></i>Masukkan nomor surat secara manual tanpa format otomatis.
                            </div>
                        </div>

                        <input type="hidden" id="nomor_surat" name="nomor_surat" value="{{ old('nomor_surat', $arsip->nomor_surat ?? '') }}">
                        <input type="hidden" id="tahun_arsip" name="tahun_arsip" value="{{ old('tahun_arsip', isset($arsip->tanggal_arsip) ? date('Y', strtotime($arsip->tanggal_arsip)) : date('Y')) }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Nama / Judul Arsip <span class="text-danger">*</span></label>
                        <input type="text" name="nama_arsip" class="form-control"
                            value="{{ old('nama_arsip', $arsip->nama_arsip ?? '') }}"
                            placeholder="Masukkan nama atau judul arsip..." required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======== KARTU 4: LOKASI PENYIMPANAN ======== -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
                    <i class="fas fa-map-marker-alt me-2" style="color: #d4af37;"></i>Lokasi Penyimpanan
                </h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Ruangan / Lokasi <span class="text-danger">*</span></label>
                        <select name="lokasi_id" id="lokasi_id" class="form-select" required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
                            <option value="">Pilih lokasi...</option>
                            @foreach($locations as $lok)
                                <option value="{{ $lok->id }}" {{ old('lokasi_id', $arsip->lokasi_id ?? '') == $lok->id ? 'selected' : '' }}>{{ $lok->ruangan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Lemari</label>
                        <select name="cabinet_id" id="cabinet_id" class="form-select"
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
                            <option value="">Pilih lemari...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Rak</label>
                        <select name="rack_id" id="rack_id" class="form-select"
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
                            <option value="">Pilih rak...</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======== KARTU 5: INFORMASI TAMBAHAN & FILE ======== -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
                    <i class="fas fa-info-circle me-2" style="color: #d4af37;"></i>Informasi Tambahan & File
                </h6>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Perihal / Uraian Surat</label>
                        <textarea name="perihal_surat" class="form-control" rows="3"
                            placeholder="Masukkan perihal atau uraian surat..."
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">{{ old('perihal_surat', $arsip->perihal_surat ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Upload File Arsip</label>
                        <div class="border-2 border-dashed rounded p-3 text-center"
                            style="border-color: #d4af37; border-style: dashed; background: #fefce8; border-radius: 8px !important;">
                            <input type="file" name="file_arsip" class="form-control"
                                style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.5rem; font-size: 0.9rem;">
                            <div class="form-text mt-2" style="color: #64748b; font-size: 0.8rem;">
                                <i class="fas fa-file-pdf me-1"></i> <i class="fas fa-file-image me-1"></i>
                                Format: PDF, JPG, PNG (Opsional, maks 2MB)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ======== TOMBOL AKSI ======== -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; background: #f8fafc;">
            <div class="card-body p-3">
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('arsip.index') }}" class="btn btn-light px-4 py-2" style="border-radius: 8px; font-weight: 600; border: 1.5px solid #e2e8f0;">
                        <i class="fas fa-times me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn px-4 py-2 fw-bold"
                        style="border-radius: 8px; background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; border: none; box-shadow: 0 3px 10px rgba(212, 175, 55, 0.3);">
                        <i class="fas fa-save me-2"></i>Simpan Arsip
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Modal Klasifikasi -->
<div class="modal fade" id="classificationSearchModal" tabindex="-1" aria-labelledby="classificationSearchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background: #1e293b; color: #fff; border-radius: 12px 12px 0 0;">
                <h5 class="modal-title fw-bold" id="classificationSearchModalLabel" style="font-size: 1rem;">
                    <i class="fas fa-tag me-2" style="color: #d4af37;"></i>Cari Kode Klasifikasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Ketik kode atau nama klasifikasi</label>
                    <input type="text" id="classification_search_input" class="form-control"
                        placeholder="Contoh: 5.1.2 atau Surat Masuk..."
                        style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem;">
                </div>
                <div id="classification_search_results" class="list-group" style="max-height: 300px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-light px-4" style="border-radius: 8px; font-weight: 600; border: 1.5px solid #e2e8f0;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover & focus effects for all inputs */
    .form-control:focus, .form-select:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15) !important;
        background-color: #fff !important;
    }
    .form-control:hover, .form-select:hover {
        border-color: #cbd5e1 !important;
    }

    /* Card hover effect */
    .card {
        transition: box-shadow 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
    }

    /* Dashed file upload area */
    .border-dashed {
        border-style: dashed !important;
    }

    /* Nomor surat input group - remove double borders */
    .input-group .form-control {
        border-left: none;
        border-right: none;
    }
    .input-group .form-control:focus {
        box-shadow: none !important;
        border-color: #e2e8f0 !important;
    }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputNomorInti = document.getElementById('nomor_surat_inti');
        const hiddenNomorSurat = document.getElementById('nomor_surat');
        const tahunArsipInput = document.getElementById('tahun_arsip');
        const labelTahun = document.getElementById('label_tahun_otomatis');
        const tanggalArsip = document.getElementById('tanggal_arsip');
        const masaRetensi = document.querySelector('[name="masa_retensi"]');
        const previewRetensi = document.getElementById('preview_tanggal_retensi');
        
        // Mode penomoran elements
        const modeOtomatis = document.getElementById('mode_otomatis');
        const modeManual = document.getElementById('mode_manual');
        const layoutOtomatis = document.getElementById('layout_otomatis');
        const layoutManual = document.getElementById('layout_manual');
        const nomorSuratManual = document.getElementById('nomor_surat_manual');

        // Handle mode switching
        function switchMode(mode) {
            if (mode === 'otomatis') {
                layoutOtomatis.style.display = 'block';
                layoutManual.style.display = 'none';
                inputNomorInti.required = true;
                nomorSuratManual.required = false;
            } else {
                layoutOtomatis.style.display = 'none';
                layoutManual.style.display = 'block';
                inputNomorInti.required = false;
                nomorSuratManual.required = true;
            }
        }

        if (modeOtomatis) {
            modeOtomatis.addEventListener('change', function() {
                if (this.checked) switchMode('otomatis');
            });
        }

        if (modeManual) {
            modeManual.addEventListener('change', function() {
                if (this.checked) switchMode('manual');
            });
        }

        // Dependent dropdowns for location hierarchy
        const lokasiSelect = document.getElementById('lokasi_id');
        const cabinetSelect = document.getElementById('cabinet_id');
        const rackSelect = document.getElementById('rack_id');

        // Load cabinets based on selected location
        async function loadCabinets(locationId) {
            if (!locationId) {
                cabinetSelect.innerHTML = '<option value="">Pilih lemari...</option>';
                rackSelect.innerHTML = '<option value="">Pilih rak...</option>';
                return;
            }

            try {
                const url = new URL('{{ route('ajax.cabinets') }}', window.location.origin);
                url.searchParams.set('location_id', locationId);
                const response = await fetch(url.toString(), { credentials: 'same-origin' });
                const cabinets = await response.json();

                cabinetSelect.innerHTML = '<option value="">Pilih lemari...</option>';
                if (cabinets.length > 0) {
                    cabinets.forEach(cabinet => {
                        const option = document.createElement('option');
                        option.value = cabinet.id;
                        option.textContent = cabinet.lemari_nama || cabinet.nama_lemari || 'Tanpa nama';
                        cabinetSelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = 'Tidak ada lemari tersedia';
                    option.disabled = true;
                    cabinetSelect.appendChild(option);
                }

                // Reset rack dropdown
                rackSelect.innerHTML = '<option value="">Pilih rak...</option>';
            } catch (error) {
                console.error('Error loading cabinets:', error);
                cabinetSelect.innerHTML = '<option value="">Error memuat lemari</option>';
            }
        }

        // Load racks based on selected cabinet
        async function loadRacks(cabinetId) {
            if (!cabinetId) {
                rackSelect.innerHTML = '<option value="">Pilih rak...</option>';
                return;
            }

            try {
                const url = new URL('{{ route('ajax.racks') }}', window.location.origin);
                url.searchParams.set('cabinet_id', cabinetId);
                const response = await fetch(url.toString(), { credentials: 'same-origin' });
                const racks = await response.json();

                rackSelect.innerHTML = '<option value="">Pilih rak...</option>';
                racks.forEach(rack => {
                    const option = document.createElement('option');
                    option.value = rack.id;
                    option.textContent = rack.rak_nama;
                    rackSelect.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading racks:', error);
            }
        }

        // Event listeners for dependent dropdowns
        if (lokasiSelect) {
            lokasiSelect.addEventListener('change', function() {
                loadCabinets(this.value);
            });
        }

        if (cabinetSelect) {
            cabinetSelect.addEventListener('change', function() {
                loadRacks(this.value);
            });
        }

        // Pre-populate dropdowns for edit mode
        @if(isset($arsip) && $arsip->lokasi_id)
            const initialLocationId = {{ $arsip->lokasi_id }};
            const initialCabinetId = {{ $arsip->cabinet_id ?? 'null' }};
            const initialRackId = {{ $arsip->rack_id ?? 'null' }};

            if (initialLocationId) {
                loadCabinets(initialLocationId).then(() => {
                    if (initialCabinetId) {
                        cabinetSelect.value = initialCabinetId;
                        loadRacks(initialCabinetId).then(() => {
                            if (initialRackId) {
                                rackSelect.value = initialRackId;
                            }
                        });
                    }
                });
            }
        @endif

        function updatePreviewRetensi() {
            if (!previewRetensi || !tanggalArsip || !masaRetensi || !masaRetensi.value) {
                if (previewRetensi) { previewRetensi.textContent = ''; previewRetensi.style.display = 'none'; }
                return;
            }

            const tahunMap = { '3 Tahun': 3, '5 Tahun': 5, '10 Tahun': 10 };
            const tambah = tahunMap[masaRetensi.value];
            if (!tambah || !tanggalArsip.value) {
                previewRetensi.style.display = 'none';
                return;
            }

            const tgl = new Date(tanggalArsip.value);
            tgl.setFullYear(tgl.getFullYear() + tambah);
            const formatted = tgl.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
            previewRetensi.textContent = '🗓️ Tanggal retensi: ' + formatted;
            previewRetensi.style.display = 'block';
        }

        function updateNomor() {
            const tahun = tanggalArsip.value ? new Date(tanggalArsip.value).getFullYear() : new Date().getFullYear();
            labelTahun.textContent = '/' + tahun;
            
            // Only update if in automatic mode
            if (modeOtomatis && modeOtomatis.checked) {
                if (inputNomorInti.value.trim()) {
                    hiddenNomorSurat.value = `WIM.11.IMI.2-${inputNomorInti.value.trim()}/${tahun}`;
                }
            }
        }

        inputNomorInti.addEventListener('input', updateNomor);
        if (nomorSuratManual) {
            nomorSuratManual.addEventListener('input', function() {
                if (modeManual && modeManual.checked) {
                    hiddenNomorSurat.value = this.value.trim();
                }
            });
        }
        if (tanggalArsip) tanggalArsip.addEventListener('change', updateNomor);
        if (tanggalArsip) tanggalArsip.addEventListener('change', updatePreviewRetensi);
        if (masaRetensi) masaRetensi.addEventListener('change', updatePreviewRetensi);

        // Auto-populate nomor_surat on form submit
        const form = document.querySelector('form[action="{{ route('arsip.store') }}"]');
        if (form) {
            form.addEventListener('submit', function(e) {
                // Check which mode is selected
                if (modeManual && modeManual.checked) {
                    // Manual mode: use the manual input value
                    hiddenNomorSurat.value = nomorSuratManual.value.trim();
                } else {
                    // Automatic mode: generate from nomor_surat_inti
                    updateNomor();
                    if (!hiddenNomorSurat.value || hiddenNomorSurat.value.trim() === '') {
                        const tahun = tanggalArsip.value ? tanggalArsip.value.substring(0, 4) : new Date().getFullYear();
                        const nomorInti = inputNomorInti.value.trim();
                        if (nomorInti) {
                            hiddenNomorSurat.value = `WIM.11.IMI.2-${nomorInti}/${tahun}`;
                        } else {
                            // Generate default nomor surat
                            hiddenNomorSurat.value = `WIM.11.IMI.2-NO-${tahun}`;
                        }
                    }
                }
            });
        }

        const classificationSearchInput = document.getElementById('classification_search_input');
        const classificationSearchResults = document.getElementById('classification_search_results');

        async function searchClassifications(term) {
            const url = new URL('{{ route('ajax.klasifikasi') }}', window.location.origin);
            url.searchParams.set('q', term);
            const response = await fetch(url.toString(), { credentials: 'same-origin' });
            if (!response.ok) return [];
            const ct = response.headers.get('content-type') || '';
            if (!ct.includes('application/json')) return [];
            return response.json();
        }

        async function updateClassificationResults() {
            const term = classificationSearchInput.value.trim();
            const results = await searchClassifications(term);
            classificationSearchResults.innerHTML = '';

            if (!results.length) {
                classificationSearchResults.innerHTML = '<div class="list-group-item text-muted" style="border: none; text-align: center;">Tidak ada hasil ditemukan.</div>';
                return;
            }

            results.forEach(item => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'list-group-item list-group-item-action';
                button.style.cssText = 'border: none; border-bottom: 1px solid #f1f5f9; padding: 10px 14px; font-size: 0.9rem; transition: background 0.2s;';
                button.innerHTML = `<span class="badge me-2" style="background: #d4af37; color: #1e293b; font-weight: 700;">${item.kode}</span> ${item.nama}`;
                button.addEventListener('click', function () {
                    inputNomorInti.value = item.kode + '-';
                    updateNomor();
                    const modal = bootstrap.Modal.getInstance(document.getElementById('classificationSearchModal'));
                    modal.hide();
                });
                classificationSearchResults.appendChild(button);
            });
        }

        classificationSearchInput.addEventListener('input', function () {
            updateClassificationResults();
        });

        updateNomor();
        updatePreviewRetensi();
    });
</script>
@endpush