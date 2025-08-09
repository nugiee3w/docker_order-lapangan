<?php $__env->startSection('title', 'Dashboard - Booking Lapangan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
                    <p class="text-muted">Kelola sistem pemesanan booking lapangan dengan mudah</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Total Orders</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo e($stats['total_orders'] ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">Rp <?php echo e(number_format($stats['total_revenue'] ?? 0, 0, ',', '.')); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Pending Orders</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo e($stats['pending_orders'] ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row g-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                Confirmed Orders</div>
                            <div class="h5 mb-0 fw-bold text-gray-800"><?php echo e($stats['confirmed_orders'] ?? 0); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary">Recent Orders</h6>
                    <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <?php if(isset($recentOrders) && $recentOrders->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Order Number</th>
                                        <th>Customer</th>
                                        <th>Lapangan</th>
                                        <th>Tanggal Booking</th>
                                        <th>Waktu</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?php echo e($order->order_number); ?></div>
                                            <small class="text-muted"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></small>
                                        </td>
                                        <td>
                                            <div><?php echo e($order->customer_name); ?></div>
                                            <small class="text-muted"><?php echo e($order->customer_email); ?></small>
                                        </td>
                                        <td>
                                            <?php if(isset($order->lapangan_info)): ?>
                                                <div class="fw-bold"><?php echo e($order->lapangan_info['nama']); ?></div>
                                                <small class="text-muted"><?php echo e($order->lapangan_info['jenis']); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e(\Carbon\Carbon::parse($order->tanggal_booking)->format('d M Y')); ?></td>
                                        <td>
                                            <span class="badge bg-info text-white"><?php echo e($order->jam_mulai); ?> - <?php echo e($order->jam_selesai); ?></span>
                                        </td>
                                        <td>Rp <?php echo e(number_format($order->total_harga, 0, ',', '.')); ?></td>
                                        <td>
                                            <?php if($order->status == 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php elseif($order->status == 'confirmed'): ?>
                                                <span class="badge bg-success">Confirmed</span>
                                            <?php elseif($order->status == 'cancelled'): ?>
                                                <span class="badge bg-danger">Cancelled</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e(ucfirst($order->status)); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($order->payment_status == 'paid'): ?>
                                                <span class="badge bg-success">Paid</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Unpaid</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('orders.show', $order->id)); ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Belum ada pemesanan.</p>
                            <a href="<?php echo e(route('orders.create')); ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Pemesanan Pertama
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions & System Info -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="<?php echo e(route('orders.index')); ?>" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>Semua Orders
                        </a>
                        <a href="<?php echo e(route('orders.index', ['status' => 'pending'])); ?>" class="btn btn-warning">
                            <i class="fas fa-clock me-2"></i>Pending Orders (<?php echo e($stats['pending_orders'] ?? 0); ?>)
                        </a>
                        <a href="<?php echo e(route('orders.index', ['status' => 'confirmed'])); ?>" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Confirmed Orders (<?php echo e($stats['confirmed_orders'] ?? 0); ?>)
                        </a>
                        <a href="<?php echo e(route('orders.create')); ?>" class="btn btn-info">
                            <i class="fas fa-plus me-2"></i>Tambah Pemesanan Baru
                        </a>
                        <a href="#" class="btn btn-secondary">
                            <i class="fas fa-building me-2"></i>Kelola Lapangan
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">Ringkasan Hari Ini</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Orders Hari Ini:</span>
                            <span class="fw-bold"><?php echo e($stats['orders_today'] ?? 0); ?></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Revenue Hari Ini:</span>
                            <span class="fw-bold text-success">Rp <?php echo e(number_format($stats['revenue_today'] ?? 0, 0, ',', '.')); ?></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Orders Pending:</span>
                            <span class="fw-bold text-warning"><?php echo e($stats['pending_orders'] ?? 0); ?></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Orders Confirmed:</span>
                            <span class="fw-bold text-success"><?php echo e($stats['confirmed_orders'] ?? 0); ?></span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="bg-success rounded-circle me-2" style="width: 10px; height: 10px;"></div>
                            <span class="text-success">Sistem Online</span>
                        </div>
                        <small class="text-muted">
                            Last Update: <?php echo e(now()->format('d M Y H:i')); ?>

                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.card {
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15) !important;
    border: 1px solid #e3e6f0 !important;
}

/* Quick Actions Button Styling */
.d-grid .btn {
    border-radius: 8px;
    font-weight: 500;
    padding: 12px 16px;
    transition: all 0.3s ease;
    text-decoration: none;
    color: white !important;
}

.d-grid .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: white !important;
}

.btn-primary {
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    border: none;
}

.btn-warning {
    background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
    border: none;
}

.btn-success {
    background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    border: none;
}

.btn-info {
    background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
    border: none;
}

.btn-secondary {
    background: linear-gradient(135deg, #858796 0%, #60616f 100%);
    border: none;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/dashboard.blade.php ENDPATH**/ ?>