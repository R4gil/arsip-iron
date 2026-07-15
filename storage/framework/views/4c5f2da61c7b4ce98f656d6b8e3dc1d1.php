

<?php $__env->startSection('title', 'Tambah Arsip Baru'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.page-header', ['title' => 'Tambah Arsip Baru', 'subtitle' => 'Isi data arsip dan simpan dengan cepat ke dalam sistem IRON SMART.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="is-card">
    <div class="is-card-body is-form">
        <form action="<?php echo e(route('arsip.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('arsip.formulir', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\iron-smart\resources\views/arsip/tambah.blade.php ENDPATH**/ ?>