<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'subtitle' => null, 'action' => null, 'actionLabel' => null, 'actionIcon' => 'fa-plus', 'extraActions' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title', 'subtitle' => null, 'action' => null, 'actionLabel' => null, 'actionIcon' => 'fa-plus', 'extraActions' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="is-page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
    <div>
        <h4 class="mb-1"><?php echo e($title); ?></h4>
        <?php if($subtitle): ?>
            <p><?php echo e($subtitle); ?></p>
        <?php endif; ?>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <?php if($extraActions): ?>
            <?php $__currentLoopData = $extraActions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extraAction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($extraAction['url']); ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas <?php echo e($extraAction['icon']); ?> me-1"></i><?php echo e($extraAction['label']); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
        <?php if($action): ?>
            <a href="<?php echo e($action); ?>" class="btn is-btn-gold">
                <i class="fas <?php echo e($actionIcon); ?> me-2"></i><?php echo e($actionLabel ?? 'Tambah'); ?>

            </a>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\iron-smart\resources\views/partials/page-header.blade.php ENDPATH**/ ?>