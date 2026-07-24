

<?php $__env->startSection('title', 'Retensi Arsip'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.page-header', [
    'title' => 'Retensi Arsip',
    'subtitle' => 'Daftar arsip berdasarkan masa retensi dan status retensi.',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<form method="POST" action="<?php echo e(route('arsip.ajukanRetensi')); ?>" id="ajukanRetensiForm" style="display:none;"><?php echo csrf_field(); ?> <input type="hidden" name="ids" id="ajukanRetensiIds"></form>
<form method="POST" action="<?php echo e(route('arsip.selesaiRetensi')); ?>" id="selesaiRetensiForm" style="display:none;"><?php echo csrf_field(); ?> <input type="hidden" name="ids" id="selesaiRetensiIds"></form>
<form method="POST" action="<?php echo e(route('arsip.batalRetensi')); ?>" id="batalRetensiForm" style="display:none;"><?php echo csrf_field(); ?> <input type="hidden" name="ids" id="batalRetensiIds"></form>
<form method="POST" action="<?php echo e(route('arsip.bulkDelete')); ?>" id="bulkDeleteForm" style="display:none;"><?php echo csrf_field(); ?> <input type="hidden" name="ids" id="bulkDeleteIds"></form>
<form method="GET" action="<?php echo e(route('arsip.print')); ?>" id="printForm" style="display:none;"><input type="hidden" name="ids" id="printIds"></form>


<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?php echo e(route('retensi.index')); ?>">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Status Retensi</label>
                    <select name="status_retensi" class="form-select form-select-sm">
                        <option value="semua">Semua</option>
                        <option value="Masuk Masa Retensi" <?php echo e(request('status_retensi') == 'Masuk Masa Retensi' ? 'selected' : ''); ?>>Masuk Masa Retensi</option>
                        <option value="Proses Retensi" <?php echo e(request('status_retensi') == 'Proses Retensi' ? 'selected' : ''); ?>>Proses Retensi</option>
                        <option value="Selesai Retensi" <?php echo e(request('status_retensi') == 'Selesai Retensi' ? 'selected' : ''); ?>>Selesai Retensi</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Cari Arsip</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control form-control-sm" placeholder="Nomor, nama, kategori...">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="fas fa-search me-2"></i>Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>


<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3 d-flex gap-2 flex-wrap align-items-center">
        <button type="button" onclick="ajukanRetensi()" class="btn btn-warning btn-sm fw-bold"><i class="fas fa-hourglass-start me-2"></i>Ajukan Retensi</button>
        <button type="button" onclick="selesaiRetensi()" class="btn btn-success btn-sm fw-bold"><i class="fas fa-check-circle me-2"></i>Selesai Retensi</button>
        <button type="button" onclick="batalRetensi()" class="btn btn-secondary btn-sm fw-bold"><i class="fas fa-undo me-2"></i>Batal Retensi</button>
        <button type="button" onclick="bulkDelete()" class="btn btn-danger btn-sm fw-bold" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25); color: white;"><i class="fas fa-trash me-2"></i>Hapus</button>
    </div>
</div>


<div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom" style="background: #f8fafc; border-radius: 15px 15px 0 0;">
        <div class="d-flex align-items-center gap-2">
            <span style="color: #64748b; font-size: 0.8rem;">Tampilkan</span>
            <select class="form-select form-select-sm" style="width: auto; min-width: 70px; border-radius: 6px; border: 1.5px solid #e2e8f0; font-size: 0.8rem; background-color: #fff;" onchange="window.location.href='<?php echo e(url()->current()); ?>?per_page='+this.value">
                <?php $__currentLoopData = [10, 25, 50, 100]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <option value="<?php echo e($n); ?>" <?php echo e(request('per_page', 15) == $n ? 'selected' : ''); ?>><?php echo e($n); ?></option> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <span style="color: #64748b; font-size: 0.8rem;">per halaman</span>
        </div>
        <div class="d-flex gap-2">
            <button onclick="exportExcel()" class="btn btn-success btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <button onclick="exportPDF()" class="btn btn-danger btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                    <th>Nomor Arsip</th>
                    <th>Tanggal Surat</th>
                    <th>Nama Arsip</th>
                    <th>Kategori</th>
                    <th>Lokasi Arsip</th>
                    <th>Status Dokumen</th>
                    <th>Tgl Retensi</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $archives; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $archive): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="ps-4"><input type="checkbox" class="archive-checkbox" value="<?php echo e($archive->id); ?>"></td>
                    <td class="fw-bold"><?php echo e($archive->nomor_surat ?? '—'); ?></td>
                    <td><?php echo e($archive->tanggal_arsip ? \Carbon\Carbon::parse($archive->tanggal_arsip)->format('d-m-Y') : '—'); ?></td>
                    <td class="text-truncate"><?php echo e($archive->nama_arsip ?? '—'); ?></td>
                    <td><?php echo e($archive->nama_jenis ?? '—'); ?></td>
                    <td>
                        <?php
                            $locationParts = [];
                            if ($archive->ruangan) $locationParts[] = $archive->ruangan;
                            if ($archive->lemari_nama) $locationParts[] = $archive->lemari_nama;
                            if ($archive->rak_nama) $locationParts[] = $archive->rak_nama;
                            $locationDisplay = $locationParts ? implode(' → ', $locationParts) : '—';
                        ?>
                        <span style="font-size: 0.85rem;"><?php echo e($locationDisplay); ?></span>
                    </td>
                    <td>
                        <?php
                            $status = $archive->status ?? '—';
                            $badgeClass = 'bg-secondary';
                            if ($status === 'Aktif') $badgeClass = 'bg-success';
                            elseif ($status === 'Inaktif') $badgeClass = 'bg-danger';
                        ?>
                        <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($status); ?></span>
                    </td>
                    <td><?php echo e($archive->tanggal_retensi ? \Carbon\Carbon::parse($archive->tanggal_retensi)->format('d-m-Y') : '—'); ?></td>
                    <td>
                        <?php
                            $sr = $archive->status_retensi;
                            // Map DB values to display values
                            $displayStatus = $sr;
                            if ($sr === 'Sudah Retensi') $displayStatus = 'Selesai Retensi';
                            // Badge class mapping
                            $class = 'bg-secondary';
                            if ($sr === 'Masuk Masa Retensi') $class = 'bg-danger';
                            elseif ($sr === 'Proses Retensi') $class = 'bg-warning text-dark';
                            elseif ($sr === 'Sudah Retensi') $class = 'bg-success';
                        ?>
                        <span class="badge <?php echo e($class); ?>"><?php echo e($displayStatus); ?></span>
                    </td>
                    <td class="text-center" style="white-space: nowrap;">
                        <a href="<?php echo e(route('arsip.show', $archive->id)); ?>" class="btn btn-sm" style="background:linear-gradient(135deg,#d4af37,#aa7c11);color:#1d2127;border:none;font-weight:700;border-radius:8px;box-shadow:0 2px 6px rgba(212,175,55,0.25);">Lihat</a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="10" class="text-center py-5">Data tidak ditemukan.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-3 border-top d-flex justify-content-between align-items-center" style="background: #f8fafc; border-radius: 0 0 15px 15px;">
        <span style="color: #64748b; font-size: 0.8rem;">&nbsp;</span>
        <?php echo e($archives->withQueryString()->links('pagination::simple-bootstrap-4')); ?>

    </div>
</div>


<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Pratinjau Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="previewModalBody" style="min-height: 600px; background: #f1f5f9;">
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                    <p>Memuat dokumen...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="previewDownloadBtn" class="btn btn-primary btn-sm" target="_blank"><i class="fas fa-download me-1"></i>Download</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold" id="deleteConfirmModalLabel">Konfirmasi Hapus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-3">
                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                <p class="mb-0">Yakin ingin menghapus arsip yang dipilih?</p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="confirmBulkDelete()" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25); color: white;">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let selectedIds = [];

    function toggleSelectAll() {
        document.querySelectorAll('.archive-checkbox').forEach(cb => cb.checked = document.getElementById('selectAll').checked);
    }

    function getSelected() {
        let ids = Array.from(document.querySelectorAll('.archive-checkbox:checked')).map(cb => cb.value);
        if(ids.length === 0) { alert('Pilih arsip dulu!'); return null; }
        return ids.join(',');
    }

    function ajukanRetensi() {
        let ids = getSelected();
        if(ids) {
            if(confirm('Ajukan retensi untuk ' + ids.split(',').length + ' arsip?')) {
                document.getElementById('ajukanRetensiIds').value = ids;
                document.getElementById('ajukanRetensiForm').submit();
            }
        }
    }

    function selesaiRetensi() {
        let ids = getSelected();
        if(ids) {
            if(confirm('Selesaikan retensi untuk ' + ids.split(',').length + ' arsip?')) {
                document.getElementById('selesaiRetensiIds').value = ids;
                document.getElementById('selesaiRetensiForm').submit();
            }
        }
    }

    function batalRetensi() {
        let ids = getSelected();
        if(ids) {
            if(confirm('Batalkan retensi untuk ' + ids.split(',').length + ' arsip? Status akan kembali ke "Masuk Masa Retensi".')) {
                document.getElementById('batalRetensiIds').value = ids;
                document.getElementById('batalRetensiForm').submit();
            }
        }
    }

    function bulkDelete() {
        let ids = getSelected();
        if(ids) {
            selectedIds = ids;
            var modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
        }
    }

    function confirmBulkDelete() {
        document.getElementById('bulkDeleteIds').value = selectedIds;
        document.getElementById('bulkDeleteForm').submit();
    }

    function exportExcel() {
        let ids = getSelected();
        if(ids) {
            const url = new URL('<?php echo e(route('retensi.exportExcel')); ?>', window.location.origin);
            url.searchParams.append('ids', ids);
            
            // Add existing filters
            const params = new URLSearchParams(window.location.search);
            params.forEach((value, key) => {
                if (key !== 'page') {
                    url.searchParams.append(key, value);
                }
            });
            
            window.open(url.toString(), '_blank');
        }
    }

    function exportPDF() {
        let ids = getSelected();
        if(ids) {
            const url = new URL('<?php echo e(route('retensi.exportPDF')); ?>', window.location.origin);
            url.searchParams.append('ids', ids);
            
            // Add existing filters
            const params = new URLSearchParams(window.location.search);
            params.forEach((value, key) => {
                if (key !== 'page') {
                    url.searchParams.append(key, value);
                }
            });
            
            window.open(url.toString(), '_blank');
        }
    }

    function printData() {
        let ids = getSelected();
        if(ids) {
            document.getElementById('printIds').value = ids;
            document.getElementById('printForm').submit();
        }
    }

    function previewDokumen(id) {
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        const body = document.getElementById('previewModalBody');
        const downloadBtn = document.getElementById('previewDownloadBtn');
        
        body.innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-spinner fa-spin fa-2x mb-2"></i><p>Memuat dokumen...</p></div>';
        downloadBtn.style.display = 'none';
        modal.show();

        fetch('<?php echo e(url("arsip")); ?>/' + id + '/file-info')
            .then(response => response.json())
            .then(data => {
                if (data.file_url) {
                    let extension = data.extension || '';
                    let fileUrl = data.file_url;
                    
                    if (['jpg', 'jpeg', 'png', 'gif'].includes(extension)) {
                        body.innerHTML = '<div class="p-3 text-center"><img src="' + fileUrl + '" class="img-fluid rounded" alt="Dokumen" style="max-height: 85vh; width: auto;"></div>';
                    } else if (extension === 'pdf') {
                        body.innerHTML = '<iframe src="' + fileUrl + '" width="100%" height="750px" style="border: none;"></iframe>';
                    } else {
                        body.innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-file fa-3x mb-3"></i><p>File tidak dapat ditampilkan. Silakan download.</p><a href="' + fileUrl + '" class="btn btn-primary btn-sm" target="_blank">Download File</a></div>';
                    }
                    downloadBtn.href = fileUrl;
                    downloadBtn.style.display = 'inline-block';
                } else {
                    body.innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-file-slash fa-3x mb-3"></i><p>Tidak ada file dokumen.</p></div>';
                    downloadBtn.style.display = 'none';
                }
            })
            .catch(error => {
                body.innerHTML = '<div class="text-center py-5 text-muted"><i class="fas fa-exclamation-circle fa-3x mb-3"></i><p>Gagal memuat dokumen.</p></div>';
                downloadBtn.style.display = 'none';
            });
    }
</script>
<?php $__env->stopPush(); ?>

<style>
.pagination{margin-bottom:0!important;gap:8px!important;}
.pagination .page-link{background:#fff!important;border:1.5px solid #e2e8f0!important;color:#334155!important;font-weight:600!important;font-size:0.85rem!important;padding:0.5rem 0.85rem!important;border-radius:8px!important;transition:all 0.2s ease!important;text-decoration:none!important;}
.pagination .page-item:first-child .page-link,.pagination .page-item:last-child .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#d4af37!important;color:#1d2127!important;font-weight:700!important;padding:0.5rem 1.2rem!important;box-shadow:0 2px 8px rgba(212,175,55,0.25)!important;}
.pagination .page-item:first-child .page-link:hover,.pagination .page-item:last-child .page-link:hover{background:linear-gradient(135deg,#f3e5ab,#d4af37)!important;border-color:#aa7c11!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(212,175,55,0.4)!important;}
.pagination .page-item.active .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#aa7c11!important;color:#1d2127!important;font-weight:700!important;box-shadow:0 2px 8px rgba(212,175,55,0.3)!important;}
.pagination .page-item.disabled .page-link{background:#f8fafc!important;border-color:#e2e8f0!important;color:#94a3b8!important;cursor:not-allowed!important;opacity:0.5!important;}
.pagination .page-link .sr-only{display:none!important;}

.modal-xl {
    max-width: 1200px !important;
}
.modal-dialog.modal-xl {
    width: 80vw !important;
}
.modal-content {
    border-radius: 12px !important;
    border: none !important;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
}
.modal-header {
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 1rem 1.25rem !important;
}
.modal-footer {
    border-top: 1px solid #e2e8f0 !important;
    padding: 0.75rem 1.25rem !important;
}
</style>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\iron-smart\resources\views/retensi/daftar.blade.php ENDPATH**/ ?>