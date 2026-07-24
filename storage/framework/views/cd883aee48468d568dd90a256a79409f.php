

<?php $__env->startSection('title', 'Daftar Arsip'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.page-header', [
    'title' => 'Daftar Arsip',
    'subtitle' => 'Kelola data arsip dan lokasi penyimpanan.',
    'action' => route('arsip.create'),
    'actionLabel' => 'Tambah Arsip',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
    <div class="card-body p-3">
        <form method="GET" action="<?php echo e(route('arsip.index')); ?>">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Cari Dokumen</label>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" class="form-control form-control-sm" placeholder="Nomor, nama, perihal..." style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Dari Tanggal</label>
                    <input type="date" name="tanggal_mulai" value="<?php echo e(request('tanggal_mulai')); ?>" class="form-control form-control-sm" style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Sampai Tanggal</label>
                    <input type="date" name="tanggal_selesai" value="<?php echo e(request('tanggal_selesai')); ?>" class="form-control form-control-sm" style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Lokasi</label>
                    <select name="lokasi_id" class="form-select form-select-sm" style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                        <option value="">Semua Lokasi</option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc->id); ?>" <?php echo e(request('lokasi_id') == $loc->id ? 'selected' : ''); ?>><?php echo e($loc->nama_lokasi); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Status</label>
                    <select name="status" class="form-select form-select-sm" style="border-radius: 8px; border: 1.5px solid #e2e8f0; font-size: 0.85rem; background-color: #f8fafc;">
                        <option value="">Semua</option>
                        <option value="Aktif" <?php echo e(request('status') == 'Aktif' ? 'selected' : ''); ?>>Aktif</option>
                        <option value="Inaktif" <?php echo e(request('status') == 'Inaktif' ? 'selected' : ''); ?>>Inaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100 py-2" style="border-radius: 8px; font-weight: 600;"><i class="fas fa-search me-1"></i>Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body px-0 py-0">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background: #f8fafc; border-radius: 12px 12px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span style="color: #64748b; font-size: 0.8rem;">Tampilkan</span>
                <select class="form-select form-select-sm" style="width: auto; min-width: 70px; border-radius: 6px; border: 1.5px solid #e2e8f0; font-size: 0.8rem; background-color: #fff;" onchange="window.location.href='<?php echo e(route('arsip.index')); ?>?per_page='+this.value+'&search=<?php echo e(request('search')); ?>&tanggal_mulai=<?php echo e(request('tanggal_mulai')); ?>&tanggal_selesai=<?php echo e(request('tanggal_selesai')); ?>&lokasi_id=<?php echo e(request('lokasi_id')); ?>&status=<?php echo e(request('status')); ?>'">
                    <option value="10" <?php echo e(request('per_page', 15) == 10 ? 'selected' : ''); ?>>10</option>
                    <option value="25" <?php echo e(request('per_page', 15) == 25 ? 'selected' : ''); ?>>25</option>
                    <option value="50" <?php echo e(request('per_page', 15) == 50 ? 'selected' : ''); ?>>50</option>
                    <option value="100" <?php echo e(request('per_page', 15) == 100 ? 'selected' : ''); ?>>100</option>
                </select>
                <span style="color: #64748b; font-size: 0.8rem;">per halaman</span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('arsip.exportExcel', request()->all())); ?>" class="btn btn-success btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <button onclick="exportPDF()" class="btn btn-danger btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table is-table mb-0">
                <thead>
                    <tr>
                        <th class="ps-3" style="width: 50px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
                        <th class="ps-3" style="width: 50px;">No</th>
                        <th><a href="<?php echo e(sortUrl('nomor_surat')); ?>" class="sort-link">No. Surat <?php echo sortIcon('nomor_surat'); ?></a></th>
                        <th><a href="<?php echo e(sortUrl('tanggal_arsip')); ?>" class="sort-link">Tanggal <?php echo sortIcon('tanggal_arsip'); ?></a></th>
                        <th><a href="<?php echo e(sortUrl('nama_arsip')); ?>" class="sort-link">Nama Arsip <?php echo sortIcon('nama_arsip'); ?></a></th>
                        <th><a href="<?php echo e(sortUrl('nama_jenis')); ?>" class="sort-link">Jenis <?php echo sortIcon('nama_jenis'); ?></a></th>
                        <th>Lokasi</th>
                        <?php if($retensiTersedia ?? false): ?>
                        <th><a href="<?php echo e(sortUrl('masa_retensi')); ?>" class="sort-link">Masa Retensi <?php echo sortIcon('masa_retensi'); ?></a></th>
                        <th><a href="<?php echo e(sortUrl('tanggal_retensi')); ?>" class="sort-link">Tgl Retensi <?php echo sortIcon('tanggal_retensi'); ?></a></th>
                        <?php endif; ?>
                        <th><a href="<?php echo e(sortUrl('status')); ?>" class="sort-link">Status <?php echo sortIcon('status'); ?></a></th>
                        <th><a href="<?php echo e(sortUrl('status_ketersediaan')); ?>" class="sort-link">Ketersediaan <?php echo sortIcon('status_ketersediaan'); ?></a></th>
                        <th><a href="<?php echo e(sortUrl('status_retensi')); ?>" class="sort-link">Status Retensi <?php echo sortIcon('status_retensi'); ?></a></th>
                        <th class="text-center pe-3" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $archives; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $archive): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-3"><input type="checkbox" class="archive-checkbox" value="<?php echo e($archive->id); ?>"></td>
                        <td class="ps-3 text-muted"><?php echo e($archives->firstItem() + $loop->index); ?></td>
                        <td class="fw-bold"><?php echo e($archive->nomor_surat ?? '—'); ?></td>
                        <td style="white-space: nowrap;"><?php echo e($archive->tanggal_arsip ? \Carbon\Carbon::parse($archive->tanggal_arsip)->format('d-m-Y') : '—'); ?></td>
                        <td>
                            <div class="fw-bold" style="font-size: 0.85rem;"><?php echo e($archive->nama_arsip); ?></div>
                            <div class="text-muted" style="font-size: 0.75rem !important;"><?php echo e($archive->perihal_surat ?? ''); ?></div>
                        </td>
                        <td style="font-size: 0.8rem;"><?php echo e($archive->nama_jenis ?? ($archive->jenis_dokumen ?? '—')); ?></td>
                        <td style="font-size: 0.8rem;">
                            <?php
                                $parts = array_filter([$archive->ruangan ?? '', $archive->lemari_nama ?? '', $archive->rak_nama ?? '']);
                            ?>
                            <?php echo e($parts ? implode(' → ', $parts) : '—'); ?>

                        </td>
                        <?php if($retensiTersedia ?? false): ?>
                        <td style="font-size: 0.8rem;"><?php echo e($archive->masa_retensi ?? '—'); ?></td>
                        <td style="font-size: 0.8rem; white-space: nowrap;"><?php echo e($archive->tanggal_retensi ? \Carbon\Carbon::parse($archive->tanggal_retensi)->format('d-m-Y') : '—'); ?></td>
                        <?php endif; ?>
                        <td><span class="is-badge <?php echo e($archive->status == 'Aktif' ? 'bg-success' : 'bg-secondary'); ?>"><?php echo e($archive->status); ?></span></td>
                        <td><span class="is-badge <?php echo e(($archive->status_ketersediaan ?? 'Tersedia') == 'Tersedia' ? 'bg-success' : 'bg-danger'); ?>"><?php echo e($archive->status_ketersediaan ?? 'Tersedia'); ?></span></td>
                        <td>
                            <?php
                                $sr = $archive->status_retensi ?? 'Belum Memasuki Masa Retensi';
                                $bc = 'bg-secondary';
                                if ($sr == 'Masuk Masa Retensi') $bc = 'bg-danger';
                                elseif ($sr == 'Proses Retensi') $bc = 'bg-warning text-dark';
                                elseif ($sr == 'Sudah Retensi') $bc = 'bg-success';
                            ?>
                            <span class="is-badge <?php echo e($bc); ?>" style="font-size: 0.7rem !important;"><?php echo e($sr); ?></span>
                        </td>
                        <td class="text-center pe-3" style="white-space: nowrap;">
                            <a href="<?php echo e(route('arsip.show', $archive->id)); ?>" class="btn btn-sm me-1" style="background:linear-gradient(135deg,#d4af37,#aa7c11);color:#1d2127;border:none;font-weight:700;border-radius:8px;box-shadow:0 2px 6px rgba(212,175,55,0.25);">Lihat</a>
                            <a href="<?php echo e(route('arsip.edit', $archive->id)); ?>" class="btn btn-sm me-1" style="background:#fffbeb;color:#b45309;border:1.5px solid #fcd34d;font-weight:600;border-radius:8px;">Edit</a>
                            <form action="<?php echo e(route('arsip.destroy', $archive->id)); ?>" method="POST" style="display:inline;">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25); color: white;" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="<?php echo e(($retensiTersedia ?? false) ? 13 : 11); ?>" class="is-empty">Data tidak ditemukan</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-body border-top d-flex justify-content-between align-items-center" style="background: #f8fafc; border-radius: 0 0 12px 12px;">
        <span style="color: #64748b; font-size: 0.85rem;">
            <strong>Total:</strong> <?php echo e($archives->total()); ?> arsip
            <?php if($archives->total() > 0): ?>
                <span style="color: #94a3b8;">&mdash; Halaman <?php echo e($archives->currentPage()); ?> dari <?php echo e($archives->lastPage()); ?></span>
            <?php endif; ?>
        </span>
        <?php if($archives->hasPages()): ?>
        <div>
            <?php echo e($archives->withQueryString()->links('pagination::simple-bootstrap-4')); ?>

        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .form-control:focus, .form-select:focus { border-color: #d4af37 !important; box-shadow: 0 0 0 3px rgba(212,175,55,0.15) !important; background-color: #fff !important; }
    .form-control:hover, .form-select:hover { border-color: #cbd5e1 !important; }
    .card { transition: box-shadow 0.3s ease; }
    .card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08) !important; }
    .table th { background: #f1f5f9 !important; color: #334155 !important; font-size: 0.78rem !important; text-transform: uppercase; letter-spacing: 0.03em; border-bottom: 2px solid #e2e8f0 !important; padding: 0.7rem 0.75rem !important; }
    .table td { vertical-align: middle !important; font-size: 0.85rem; padding: 0.65rem 0.75rem !important; }
    .table tbody tr:hover { background: #f8fafc !important; }
    .table tbody tr:not(:last-child) td { border-bottom: 1px solid #f1f5f9 !important; }
    .is-badge { font-size: 0.72rem !important; padding: 0.25rem 0.55rem !important; border-radius: 6px !important; font-weight: 600 !important; }
    .sort-link { color: #334155 !important; text-decoration: none !important; display: inline-flex; align-items: center; gap: 2px; }
    .sort-link:hover { color: #d4af37 !important; }
</style>

<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.archive-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

function exportPDF() {
    const checkboxes = document.querySelectorAll('.archive-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        alert('Pilih arsip terlebih dahulu dengan checklist');
        return;
    }
    
    const url = new URL('<?php echo e(route('arsip.exportPDF')); ?>', window.location.origin);
    url.searchParams.append('ids', ids.join(','));
    
    // Add existing filters
    const params = new URLSearchParams(window.location.search);
    params.forEach((value, key) => {
        if (key !== 'page') {
            url.searchParams.append(key, value);
        }
    });
    
    window.open(url.toString(), '_blank');
}
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\iron-smart\resources\views/arsip/daftar.blade.php ENDPATH**/ ?>