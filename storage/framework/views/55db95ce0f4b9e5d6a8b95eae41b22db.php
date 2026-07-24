<?php $__env->startSection('title', 'Profil Saya'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.page-header', ['title' => 'Profil Saya', 'subtitle' => 'Kelola informasi profil dan akun Anda.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php if($errors->any()): ?>
    <div class="alert alert-danger p-3 mb-4" style="border-radius: 8px; border-left: 4px solid #dc2626;">
        <ul class="mb-0 ps-3">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<?php if(session('success')): ?>
    <div class="alert alert-success" style="border-radius: 8px; border-left: 4px solid #16a34a;"><?php echo e(session('success')); ?></div>
<?php endif; ?>

<div class="row g-3">
    <!-- ===== KIRI: FOTO PROFIL & UPLOAD ===== -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <?php if($user->profile_photo): ?>
                        <img src="<?php echo e(route('profile.photo', $user->profile_photo)); ?>" alt="Profile Photo" class="rounded-circle"
                            style="width: 120px; height: 120px; object-fit: cover; border: 3px solid #d4af37; box-shadow: 0 3px 15px rgba(212,175,55,0.3);"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <i class="fas fa-user-circle text-muted" style="font-size: 100px; display: none;"></i>
                    <?php else: ?>
                        <i class="fas fa-user-circle text-muted" style="font-size: 100px; color: #cbd5e1 !important;"></i>
                    <?php endif; ?>
                </div>
                <h5 class="fw-bold mb-1" style="color: #1e293b;"><?php echo e($user->nama_pengguna ?? 'Pengguna'); ?></h5>
                <p class="text-muted mb-2" style="font-size: 0.85rem;"><?php echo e($user->username ?? '-'); ?></p>
                <span class="badge px-3 py-2" style="background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; font-weight: 600; border-radius: 6px;"><?php echo e($user->role ?? 'User'); ?></span>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3" style="color: #1e293b; font-size: 0.85rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 8px;">
                    <i class="fas fa-camera me-2" style="color: #d4af37;"></i>Upload Foto Profil
                </h6>
                <form action="<?php echo e(route('profile.update')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <input type="file" name="profile_photo" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif" required
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.5rem; font-size: 0.9rem; background-color: #f8fafc;">
                        <div class="form-text mt-2" style="color: #64748b; font-size: 0.8rem;">Format: JPEG, PNG, JPG, GIF (Max 2MB)</div>
                    </div>
                    <button type="submit" class="btn fw-bold w-100 py-2" style="border-radius: 8px; background: linear-gradient(135deg, #d4af37, #aa7c11); color: #1e293b; border: none; box-shadow: 0 3px 10px rgba(212, 175, 55, 0.3);">
                        <i class="fas fa-upload me-2"></i>Upload Foto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== KANAN: DETAIL PROFIL ===== -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-4" style="color: #1e293b; font-size: 0.9rem; letter-spacing: 0.03em; text-transform: uppercase; border-bottom: 2px solid #d4af37; padding-bottom: 10px;">
                    <i class="fas fa-id-card me-2" style="color: #d4af37;"></i>Detail Profil
                </h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Nama Lengkap</label>
                        <input type="text" class="form-control" value="<?php echo e($user->nama_pengguna ?? '-'); ?>" readonly
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background: #f1f5f9;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Username</label>
                        <input type="text" class="form-control" value="<?php echo e($user->username ?? '-'); ?>" readonly
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background: #f1f5f9;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Email</label>
                        <input type="email" class="form-control" value="<?php echo e($user->email ?? '-'); ?>" readonly
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background: #f1f5f9;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Role</label>
                        <input type="text" class="form-control" value="<?php echo e(ucfirst($user->role ?? '-')); ?>" readonly
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background: #f1f5f9;">
                    </div>
                    <?php if($user->unit_kerja): ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #334155; font-size: 0.85rem;">Unit Kerja</label>
                        <input type="text" class="form-control" value="<?php echo e($user->unit_kerja); ?>" readonly
                            style="border-radius: 8px; border: 1.5px solid #e2e8f0; padding: 0.6rem 0.75rem; font-size: 0.9rem; background: #f1f5f9;">
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\iron-smart\resources\views/auth/profile.blade.php ENDPATH**/ ?>