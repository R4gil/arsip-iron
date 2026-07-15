@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Kode Klasifikasi <span class="text-danger">*</span></label>
        <input type="text" name="kode" value="{{ old('kode', $classification->kode ?? '') }}" class="form-control" placeholder="Contoh: 5.1.2" required
            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Nama Klasifikasi <span class="text-danger">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $classification->nama ?? '') }}" class="form-control" placeholder="Contoh: Surat Masuk" required
            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background-color: #f8fafc; transition: border-color 0.2s;">
    </div>
</div>

<style>
    .form-control:focus {
        border-color: #d4af37 !important;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.15) !important;
        background-color: #fff !important;
    }
    .form-control:hover {
        border-color: #cbd5e1 !important;
    }
</style>