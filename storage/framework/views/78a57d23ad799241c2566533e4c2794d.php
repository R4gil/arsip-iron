<?php $__env->startSection('title', 'Log Aktivitas'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.page-header', [
    'title' => 'Log Aktivitas',
    'subtitle' => 'Riwayat aktivitas seluruh pengguna sistem.',
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <form action="<?php echo e(route('activity-log.clear')); ?>" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus semua log aktivitas? Aksi ini tidak dapat dibatalkan.')">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash-alt me-1"></i> Clear Log
        </button>
    </form>
</div>

<div class="is-card mb-3">
    <div class="is-card-body is-form py-3">
        <form method="GET" action="<?php echo e(route('activity-log.index')); ?>">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Pengguna</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua Pengguna</option>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($user->id); ?>" <?php echo e(request('user_id') == $user->id ? 'selected' : ''); ?>><?php echo e($user->nama_pengguna ?? $user->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Aktivitas</label>
                    <input type="text" name="aktivitas" value="<?php echo e(request('aktivitas')); ?>" class="form-control form-control-sm" placeholder="Cari aktivitas...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="<?php echo e(request('tanggal_mulai')); ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="<?php echo e(request('tanggal_selesai')); ?>" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100">Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="is-card">
    <div class="table-responsive">
        <table class="table is-table mb-0">
            <thead>
                <tr>
                    <th class="ps-3">#</th>
                    <th>Tanggal & Waktu</th>
                    <th>Pengguna</th>
                    <th>Aktivitas</th>
                    <th>Detail</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="ps-3"><?php echo e($activities->firstItem() + $key); ?></td>
                    <td><?php echo e(\Carbon\Carbon::parse($activity->created_at)->format('d-m-Y H:i:s')); ?></td>
                    <td>
                        <?php if($activity->user): ?>
                            <div class="fw-bold"><?php echo e($activity->user->nama_pengguna ?? $activity->user->name); ?></div>
                            <div class="text-muted small"><?php echo e($activity->user->role ?? '—'); ?></div>
                        <?php else: ?>
                            <span class="text-muted">Pengguna dihapus</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="is-badge bg-primary"><?php echo e($activity->aktivitas); ?></span></td>
                    <td><?php echo e($activity->detail ?? '—'); ?></td>
                    <td><span class="text-muted small"><?php echo e($activity->ip_address ?? '—'); ?></span></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="is-empty">Data aktivitas tidak ditemukan</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if($activities->hasPages()): ?>
        <div class="is-card-body border-top"><?php echo e($activities->withQueryString()->links('pagination::simple-bootstrap-4')); ?></div>
    <?php endif; ?>
</div>

<style>
.pagination{margin-bottom:0!important;gap:8px!important;}
.pagination .page-link{background:#fff!important;border:1.5px solid #e2e8f0!important;color:#334155!important;font-weight:600!important;font-size:0.85rem!important;padding:0.5rem 0.85rem!important;border-radius:8px!important;transition:all 0.2s ease!important;text-decoration:none!important;}
.pagination .page-item:first-child .page-link,.pagination .page-item:last-child .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#d4af37!important;color:#1d2127!important;font-weight:700!important;padding:0.5rem 1.2rem!important;box-shadow:0 2px 8px rgba(212,175,55,0.25)!important;}
.pagination .page-item:first-child .page-link:hover,.pagination .page-item:last-child .page-link:hover{background:linear-gradient(135deg,#f3e5ab,#d4af37)!important;border-color:#aa7c11!important;transform:translateY(-1px)!important;box-shadow:0 4px 12px rgba(212,175,55,0.4)!important;}
.pagination .page-item.active .page-link{background:linear-gradient(135deg,#d4af37,#aa7c11)!important;border-color:#aa7c11!important;color:#1d2127!important;font-weight:700!important;box-shadow:0 2px 8px rgba(212,175,55,0.3)!important;}
.pagination .page-item.disabled .page-link{background:#f8fafc!important;border-color:#e2e8f0!important;color:#94a3b8!important;cursor:not-allowed!important;opacity:0.5!important;}
.pagination .page-link .sr-only{display:none!important;}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\iron-smart\resources\views/activity-log/index.blade.php ENDPATH**/ ?>