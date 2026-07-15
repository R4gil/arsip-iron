
<?php $__env->startSection('title','Rak Arsip'); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.page-header',['title'=>'Rak Arsip','subtitle'=>'Kelola rak berdasarkan lokasi dan lemari.','action'=>route('rak.create'),'actionLabel'=>'Tambah Rak'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('partials.lokasi-hierarchy', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
    <div class="card-body p-3">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="color:#334155;font-size:0.85rem;">Filter Lokasi</label>
                <select name="lokasi_id" id="filter_lokasi" class="form-select form-select-sm" style="border-radius:8px;border:1.5px solid #e2e8f0;font-size:0.85rem;background-color:#f8fafc;">
                    <option value="">Semua Lokasi</option>
                    <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($loc->id); ?>" <?php echo e(request('lokasi_id')==$loc->id?'selected':''); ?>><?php echo e($loc->ruangan); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold" style="color:#334155;font-size:0.85rem;">Filter Lemari</label>
                <select name="lemari_id" id="filter_lemari" class="form-select form-select-sm" style="border-radius:8px;border:1.5px solid #e2e8f0;font-size:0.85rem;background-color:#f8fafc;">
                    <option value="">Semua Lemari</option>
                    <?php $__currentLoopData = $cabinets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cab->lemari_id); ?>" data-lokasi="<?php echo e($cab->ruangarsip_id); ?>" <?php echo e(request('lemari_id')==$cab->lemari_id?'selected':''); ?>><?php echo e($cab->location?->ruangan); ?> — <?php echo e($cab->lemari_nama); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-dark btn-sm w-100 py-2" style="border-radius:8px;font-weight:600;">Terapkan</button>
            </div>
        </form>
    </div>
</div>
<div class="card border-0 shadow-sm" style="border-radius:12px;">
    <div class="card-body px-0 py-0">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom" style="background:#f8fafc;border-radius:12px 12px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <span style="color:#64748b;font-size:0.8rem;">Tampilkan</span>
                <select class="form-select form-select-sm" style="width:auto;min-width:70px;border-radius:6px;border:1.5px solid #e2e8f0;font-size:0.8rem;background-color:#fff;" onchange="window.location.href='<?php echo e(route('rak.index')); ?>?per_page='+this.value+'&lokasi_id=<?php echo e(request('lokasi_id')); ?>&lemari_id=<?php echo e(request('lemari_id')); ?>'">
                    <option value="10" <?php echo e(request('per_page',15)==10?'selected':''); ?>>10</option>
                    <option value="25" <?php echo e(request('per_page',15)==25?'selected':''); ?>>25</option>
                    <option value="50" <?php echo e(request('per_page',15)==50?'selected':''); ?>>50</option>
                    <option value="100" <?php echo e(request('per_page',15)==100?'selected':''); ?>>100</option>
                </select>
                <span style="color:#64748b;font-size:0.8rem;">per halaman</span>
            </div>
            <span style="color:#64748b;font-size:0.8rem;">Data rak</span>
        </div>
        <div class="table-responsive">
            <table class="table is-table mb-0">
                <thead><tr><th class="ps-3" style="width:50px;">No</th><th>Lokasi</th><th>Lemari</th><th>Nama Rak</th><th>Keterangan</th><th class="text-center" style="width:150px;">Aksi</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $racks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="ps-3 text-muted"><?php echo e($racks->firstItem()+$loop->index); ?></td>
                        <td style="font-size:0.8rem;"><?php echo e($rack->cabinet?->location?->ruangan??'—'); ?></td>
                        <td style="font-size:0.8rem;"><?php echo e($rack->cabinet?->lemari_nama??'—'); ?></td>
                        <td class="fw-bold"><?php echo e($rack->rak_nama); ?></td>
                        <td style="font-size:0.8rem;"><?php echo e($rack->rak_keterangan?:'—'); ?></td>
                        <td class="text-center" style="white-space:nowrap;">
                            <a href="<?php echo e(route('rak.edit', $rack)); ?>" class="btn btn-sm me-1" style="background:#fffbeb;color:#b45309;border:1.5px solid #fcd34d;font-weight:600;border-radius:8px;">Edit</a>
                            <form action="<?php echo e(route('rak.destroy', $rack)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Hapus rak ini?');"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm" style="background:#fee2e2;color:#dc2626;border:1.5px solid #fecaca;font-weight:600;border-radius:8px;">Hapus</button></form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="is-empty">Belum ada rak.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if($racks->hasPages()): ?>
    <div class="card-body border-top d-flex justify-content-between align-items-center" style="background:#f8fafc;border-radius:0 0 12px 12px;">
        <span style="color:#64748b;font-size:0.8rem;">&nbsp;</span>
        <?php echo e($racks->withQueryString()->links('pagination::simple-bootstrap-4')); ?>

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
<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('filter_lokasi')?.addEventListener('change',function(){
    const lokasiId=this.value;
    const lemariSelect=document.getElementById('filter_lemari');
    Array.from(lemariSelect.options).forEach((opt,i)=>{
        if(i===0)return;
        opt.hidden=lokasiId&&opt.dataset.lokasi!==lokasiId;
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\iron-smart\resources\views/rak/index.blade.php ENDPATH**/ ?>