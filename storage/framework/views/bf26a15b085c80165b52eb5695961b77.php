

<?php $__env->startSection('title', 'Detail Arsip'); ?>

<?php $__env->startSection('content'); ?>
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0 fw-bold">Detail Arsip: <?php echo e($archive->nomor_surat); ?></h5>
                <p class="text-muted small mb-0">Informasi lengkap data dan lokasi penyimpanan.</p>
            </div>
            <a href="<?php echo e(route('arsip.index')); ?>" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Nomor Surat</dt>
                        <dd class="col-7 fw-bold text-dark"><?php echo e($archive->nomor_surat ?? '-'); ?></dd>

                        <dt class="col-5 text-muted">Nama Arsip</dt>
                        <dd class="col-7"><?php echo e($archive->nama_arsip ?? '-'); ?></dd>

                        <dt class="col-5 text-muted">Jenis Arsip</dt>
                        <dd class="col-7"><?php echo e($archive->nama_jenis ?? '-'); ?></dd>
                    </dl>
                </div>
                <div class="col-md-6">
                    <dl class="row mb-0">
                        <dt class="col-5 text-muted">Lokasi Penyimpanan</dt>
                        <dd class="col-7">
                            <?php
                                $locationParts = [];
                                if ($archive->ruangan) $locationParts[] = $archive->ruangan;
                                if ($archive->lemari_nama) $locationParts[] = $archive->lemari_nama;
                                if ($archive->rak_nama) $locationParts[] = $archive->rak_nama;
                                $locationDisplay = implode(' → ', $locationParts);
                            ?>
                            <?php echo e($locationDisplay ?: '-'); ?>

                        </dd>

                        <dt class="col-5 text-muted">Tanggal Arsip</dt>
                        <dd class="col-7"><?php echo e($archive->tanggal_arsip ? date('d M Y', strtotime($archive->tanggal_arsip)) : '-'); ?></dd>

                        <?php if(!empty($archive->masa_retensi)): ?>
                        <dt class="col-5 text-muted">Masa Retensi</dt>
                        <dd class="col-7"><?php echo e($archive->masa_retensi); ?></dd>

                        <dt class="col-5 text-muted">Tanggal Retensi</dt>
                        <dd class="col-7"><?php echo e($archive->tanggal_retensi ? date('d M Y', strtotime($archive->tanggal_retensi)) : '-'); ?></dd>

                        <dt class="col-5 text-muted">Status Retensi</dt>
                        <dd class="col-7">
                            <span class="badge <?php echo e(($archive->status_retensi ?? '') === 'Sudah Memasuki Masa Retensi' ? 'bg-danger' : 'bg-secondary'); ?>">
                                <?php echo e($archive->status_retensi ?? '-'); ?>

                            </span>
                        </dd>
                        <?php endif; ?>

                        <dt class="col-5 text-muted">Status</dt>
                        <dd class="col-7">
                            <span class="badge <?php echo e($archive->status == 'Aktif' ? 'bg-success' : 'bg-secondary'); ?>">
                                <?php echo e($archive->status ?? '-'); ?>

                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold">Pratinjau Dokumen</h6>
        </div>
        <div class="card-body p-0">
            <?php if(!empty($archive->file_arsip)): ?>
                <?php
                    $filePath = route('arsip.viewFile', ['filename' => $archive->file_arsip]);
                    $extension = strtolower(pathinfo($archive->file_arsip, PATHINFO_EXTENSION));
                ?>

                <div class="p-3" style="min-height: 800px;">
                    <?php if(in_array($extension, ['jpg', 'jpeg', 'png'])): ?>
                        <img src="<?php echo e($filePath); ?>" class="img-fluid rounded border" alt="File Arsip" style="max-height: 900px;">
                    <?php elseif($extension == 'pdf'): ?>
                        <iframe src="<?php echo e($filePath); ?>" width="100%" height="900px" class="border rounded"></iframe>
                    <?php else: ?>
                        <div class="alert alert-info mb-0">
                            File tidak dapat ditampilkan langsung.
                            <a href="<?php echo e($filePath); ?>" class="btn btn-primary btn-sm ms-2" target="_blank">Download File</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-file-slash fa-2x mb-2"></i>
                    <p>Tidak ada file arsip yang dilampirkan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\iron-smart\resources\views/arsip/detail.blade.php ENDPATH**/ ?>