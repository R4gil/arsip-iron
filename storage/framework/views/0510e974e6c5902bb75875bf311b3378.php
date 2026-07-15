

<?php $__env->startSection('title', 'Ubah Arsip'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('partials.page-header', ['title' => 'Ubah Arsip', 'subtitle' => 'Edit data arsip yang sudah ada.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="is-card">
    <div class="is-card-body is-form">
        <form action="<?php echo e(route('arsip.update', $arsip->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('arsip.formulir', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\iron-smart\resources\views/arsip/ubah.blade.php ENDPATH**/ ?>