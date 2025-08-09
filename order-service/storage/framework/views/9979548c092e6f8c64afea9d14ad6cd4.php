

<?php $__env->startSection('title', 'Detail Lapangan - ' . $lapangan['nama']); ?>

<?php $__env->startSection('content'); ?>
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?php echo e(route('lapangan.index')); ?>">Pilih Lapangan</a>
        </li>
        <li class="breadcrumb-item active"><?php echo e($lapangan['nama']); ?></li>
    </ol>
</nav>

<!-- Header dengan info lapangan -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="row g-0">
                <div class="col-md-4">
                    <?php if(isset($lapangan['image']) && $lapangan['image']): ?>
                        <img src="<?php echo e($lapangan['image']); ?>" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="<?php echo e($lapangan['nama']); ?>">
                    <?php else: ?>
                        <div class="d-flex align-items-center justify-content-center h-100 rounded-start" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 300px;">
                            <i class="fas fa-futbol fa-5x text-white opacity-50"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h2 class="card-title fw-bold"><?php echo e($lapangan['nama']); ?></h2>
                                <span class="badge 
                                    <?php if($lapangan['jenis'] == 'futsal'): ?> bg-primary
                                    <?php elseif($lapangan['jenis'] == 'badminton'): ?> bg-success
                                    <?php elseif($lapangan['jenis'] == 'basket'): ?> bg-warning
                                    <?php else: ?> bg-info
                                    <?php endif; ?>
                                    fs-6 me-2">
                                    <i class="fas fa-futbol me-1"></i><?php echo e(ucfirst($lapangan['jenis'])); ?>

                                </span>
                                <span class="badge 
                                    <?php if($lapangan['status'] == 'tersedia'): ?> bg-success
                                    <?php elseif($lapangan['status'] == 'maintenance'): ?> bg-warning
                                    <?php else: ?> bg-danger
                                    <?php endif; ?>
                                    fs-6">
                                    <?php echo e(ucfirst($lapangan['status'])); ?>

                                </span>
                            </div>
                            <div class="text-end">
                                <div class="text-success fw-bold fs-3">Rp <?php echo e(number_format($lapangan['harga_per_jam'], 0, ',', '.')); ?></div>
                                <small class="text-muted">per jam</small>
                            </div>
                        </div>

                        <p class="card-text mb-3"><?php echo e($lapangan['deskripsi'] ?? 'Tidak ada deskripsi'); ?></p>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <strong>Lokasi:</strong> <?php echo e($lapangan['lokasi']); ?>

                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <strong>Total Pesanan:</strong> <?php echo e($orders->total()); ?> pesanan
                                </small>
                            </div>
                        </div>

                        <!-- Fasilitas -->
                        <?php if(isset($lapangan['fasilitas']) && is_array($lapangan['fasilitas']) && count($lapangan['fasilitas']) > 0): ?>
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2"><strong>Fasilitas:</strong></small>
                            <div class="d-flex flex-wrap gap-1">
                                <?php $__currentLoopData = $lapangan['fasilitas']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fasilitas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <span class="badge bg-secondary"><?php echo e($fasilitas); ?></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2 d-md-flex">
                            <?php if($lapangan['status'] == 'tersedia'): ?>
                                <a href="<?php echo e(route('orders.create', ['lapangan_id' => $lapangan['id']])); ?>" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Buat Pesanan Baru
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo e(route('lapangan.index')); ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Pesanan -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="m-0 fw-bold text-primary">
                    <i class="fas fa-list-alt me-2"></i>Daftar Pesanan Lapangan
                </h5>
            </div>
            <div class="card-body">
                <?php if($orders->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal Booking</th>
                                    <th>Waktu</th>
                                    <th>Customer</th>
                                    <th>Kontak</th>
                                    <th>Status</th>
                                    <th>Pembayaran</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e(\Carbon\Carbon::parse($order->tanggal_booking)->format('d M Y')); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($order->created_at)->diffForHumans()); ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-medium"><?php echo e($order->jam_mulai); ?> - <?php echo e($order->jam_selesai); ?></span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo e($order->customer_name); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo e($order->customer_email); ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo e($order->customer_phone); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php if($order->status == 'confirmed'): ?> bg-success
                                            <?php elseif($order->status == 'pending'): ?> bg-warning
                                            <?php elseif($order->status == 'cancelled'): ?> bg-danger
                                            <?php else: ?> bg-secondary
                                            <?php endif; ?>
                                        ">
                                            <?php echo e(ucfirst($order->status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            <?php if($order->payment_status == 'paid'): ?> bg-success
                                            <?php elseif($order->payment_status == 'pending'): ?> bg-warning
                                            <?php else: ?> bg-danger
                                            <?php endif; ?>
                                        ">
                                            <?php echo e(ucfirst($order->payment_status)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-success">Rp <?php echo e(number_format($order->total_harga, 0, ',', '.')); ?></strong>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('orders.edit', $order->id)); ?>" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <?php echo e($orders->links()); ?>

                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum Ada Pesanan</h5>
                        <p class="text-muted">Lapangan ini belum memiliki pesanan apapun.</p>
                        <?php if($lapangan['status'] == 'tersedia'): ?>
                            <a href="<?php echo e(route('orders.create', ['lapangan_id' => $lapangan['id']])); ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Buat Pesanan Pertama
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('styles'); ?>
<style>
.img-fluid {
    transition: transform 0.3s ease;
}

.card:hover .img-fluid {
    transform: scale(1.02);
}

.table th {
    font-weight: 600;
    color: #5a5c69;
    border-top: none;
}

.badge {
    font-size: 0.75em;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.4rem;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/lapangan/show.blade.php ENDPATH**/ ?>