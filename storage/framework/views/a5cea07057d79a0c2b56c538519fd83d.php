<?php $__env->startSection('title', 'Lokasi Arsip'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.page-header', ['title' => 'Lokasi Arsip', 'subtitle' => 'Tambah dan kelola ruangan/lokasi utama penyimpanan arsip.', 'action' => route('lokasi.create'), 'actionLabel' => 'Tambah Lokasi'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.lokasi-hierarchy', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body px-0 py-0">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background: #f8fafc; border-radius: 12px 12px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span style="color: #64748b; font-size: 0.8rem;">Tampilkan</span>
                <select class="form-select form-select-sm" style="width: auto; min-width: 70px; border-radius: 6px; border: 1.5px solid #e2e8f0; font-size: 0.8rem; background-color: #fff;" onchange="window.location.href='<?php echo e(route('lokasi.index')); ?>?per_page='+this.value">
                    <option value="10" <?php echo e(request('per_page', 15) == 10 ? 'selected' : ''); ?>>10</option>
                    <option value="25" <?php echo e(request('per_page', 15) == 25 ? 'selected' : ''); ?>>25</option>
                    <option value="50" <?php echo e(request('per_page', 15) == 50 ? 'selected' : ''); ?>>50</option>
                    <option value="100" <?php echo e(request('per_page', 15) == 100 ? 'selected' : ''); ?>>100</option>
                </select>
                <span style="color: #64748b; font-size: 0.8rem;">per halaman</span>
            </div>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('lokasi.exportExcel')); ?>" class="btn btn-success btn-sm" style="background: linear-gradient(135deg, #10b981, #059669); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </a>
                <a href="<?php echo e(route('lokasi.exportPDF')); ?>" class="btn btn-danger btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25);">
                    <i class="fas fa-file-pdf me-1"></i> Export PDF
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table is-table mb-0">
                <thead><tr><th class="ps-3" style="width:50px;">No</th><th>Nama Ruangan</th><th>Jml Lemari</th><th>Jml Rak</th><th>Jml Arsip</th><th>Keterangan</th><th class="text-center" style="width:200px;">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php $totalRak = $location->cabinets->sum('racks_count'); ?>
                    <tr>
                        <td class="ps-3 text-muted"><?php echo e($locations->firstItem() + $loop->index); ?></td>
                        <td class="fw-bold"><?php echo e($location->ruangan); ?></td>
                        <td><a href="<?php echo e(route('lemari.index', ['lokasi_id' => $location->id])); ?>" class="text-decoration-none fw-semibold"><?php echo e($location->cabinets_count); ?> lemari</a></td>
                        <td><?php echo e($totalRak); ?> rak</td>
                        <td><?php echo e($location->archives_count); ?> arsip</td>
                        <td style="font-size:0.8rem;"><?php echo e($location->keterangan ?: '—'); ?></td>
                        <td class="text-center" style="white-space:nowrap;">
                            <a href="<?php echo e(route('lemari.create', ['lokasi_id' => $location->id])); ?>" class="btn btn-sm me-1" style="background:linear-gradient(135deg,#d4af37,#aa7c11);color:#1d2127;border:none;font-weight:700;border-radius:8px;box-shadow:0 2px 6px rgba(212,175,55,0.25);">+ Lemari</a>
                            <a href="<?php echo e(route('lokasi.edit', $location)); ?>" class="btn btn-sm me-1" style="background:#fffbeb;color:#b45309;border:1.5px solid #fcd34d;font-weight:600;border-radius:8px;">Edit</a>
                            <form action="<?php echo e(route('lokasi.destroy', $location)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus lokasi ini?');"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; font-weight: 600; border-radius: 8px; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25); color: white;">Hapus</button></form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="is-empty">Belum ada lokasi arsip.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($locations->hasPages()): ?>
    <div class="card-body border-top d-flex justify-content-between align-items-center" style="background:#f8fafc;border-radius:0 0 12px 12px;">
        <span style="color:#64748b;font-size:0.8rem;">&nbsp;</span>
        <?php echo e($locations->withQueryString()->links('pagination::simple-bootstrap-4')); ?>

    </div>
    <?php endif; ?>
</div>
<style>
.form-control:focus,.form-select:focus{border-color:#d4af37!important;box-shadow:0 0 0 3px rgba(212,175,55,0.15)!important;background-color:#fff!important;}
.form-control:hover,.form-select:hover{border-color:#cbd5e1!important;}
.card{transition:box-shadow 0.3s ease;}
.card:hover{box-shadow:0 4px 20px rgba(0,0,0,0.08)!important;}
.table th{background:#f1f5f9!important;color:#334155!important;font-size:0.78rem!important;text-transform:uppercase;letter-spacing:0.03em;border-bottom:2px solid #e2e8f0!important;padding:0.7rem 0.75rem!important;}
.table td{vertical-align:middle!important;font-size:0.85rem;padding:0.65rem 0.75rem!important;}
.table tbody tr:hover{background:#f8fafc!important;}
.table tbody tr:not(:last-child) td{border-bottom:1px solid #f1f5f9!important;}
.pagination{margin-bottom:0!important;gap:8px!important;}
.pagination .page-link{background:#fff!important;border:1.5px solid #e2e8f0!important;color:#334155!important;font-weight:600!important;font-size:0.85rem!important;padding:0.5rem 0.85rem!important;border-radius:8px!important;transition:all 0.2s ease!important;text-decoration:none!important;}
.pagination .page-item:first-child .page-link,.pagination .page-item:last-child .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#d4af37!important;color:#1d2127!important;font-weight:700!important;padding:0.5rem 1.2rem!important;box-shadow:0 2px 8px rgba(212,175,55,0.25)!important;}
.pagination .page-item:first-child .page-link:hover,.pagination .page-item:last-child .page-link:hover{background:linear-gradient(135deg,#f3e5ab,#d4af37)!important;border-color:#aa7c11!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(212,175,55,0.4)!important;}
.pagination .page-item.active .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#aa7c11!important;color:#1d2127!important;font-weight:700!important;box-shadow:0 2px 8px rgba(212,175,55,0.3)!important;}
.pagination .page-item.disabled .page-link{background:#f8fafc!important;border-color:#e2e8f0!important;color:#94a3b8!important;cursor:not-allowed!important;opacity:0.5!important;}
.pagination .page-link .sr-only{display:none!important;}
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\iron-smart\resources\views/lokasi/index.blade.php ENDPATH**/ ?>