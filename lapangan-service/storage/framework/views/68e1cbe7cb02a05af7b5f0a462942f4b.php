

<?php $__env->startSection('title', 'Dashboard - Lapangan Service'); ?>

<?php $__env->startSection('content'); ?>
<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white py-4 px-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2 fw-bold" style="color: #2d3748;">
                            <i class="fas fa-chart-line me-3" style="color: #2d3748;"></i>
                            Dashboard Management
                        </h1>
                        <p class="mb-0 opacity-75" style="color: #4a5568;">Kelola lapangan dan jadwal booking dengan mudah</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="badge bg-white text-dark px-3 py-2">
                            <i class="fas fa-calendar me-1"></i>
                            <?php echo e(date('d M Y')); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Container -->
<div class="container-fluid px-3 px-md-4">

<!-- Modern Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="fw-bold text-primary mb-1"><?php echo e($totalLapangan); ?></h3>
                        <p class="text-muted mb-0 small">Total Lapangan</p>
                        <div class="mt-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-arrow-up me-1"></i>Active
                            </span>
                        </div>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-map-marked-alt text-primary fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="fw-bold text-success mb-1"><?php echo e($totalOrders); ?></h3>
                        <p class="text-muted mb-0 small">Total Order Booking</p>
                        <div class="mt-2">
                            <span class="badge bg-success bg-opacity-10 text-success">
                                <i class="fas fa-shopping-cart me-1"></i>Orders
                            </span>
                        </div>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-calendar-check text-success fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="fw-bold text-warning mb-1"><?php echo e($totalHours); ?></h3>
                        <p class="text-muted mb-0 small">Total Jam Booking</p>
                        <div class="mt-2">
                            <span class="badge bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-clock me-1"></i>Hours
                            </span>
                        </div>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-hourglass-half text-warning fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="fw-bold mb-1">
                    <i class="fas fa-bolt text-warning me-2"></i>
                    Quick Actions
                </h5>
                <p class="text-muted small mb-0">Akses cepat ke fitur utama sistem</p>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                        <a href="<?php echo e(route('lapangan')); ?>" class="text-decoration-none">
                            <div class="card border-0 bg-primary bg-opacity-10 h-100 hover-shadow transition-all">
                                <div class="card-body text-center py-4">
                                    <div class="bg-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-map-marked-alt text-white fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary mb-2">Kelola Lapangan</h6>
                                    <p class="text-muted small mb-0">Tambah, edit, dan hapus data lapangan</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="<?php echo e(route('jadwal')); ?>" class="text-decoration-none">
                            <div class="card border-0 bg-success bg-opacity-10 h-100 hover-shadow transition-all">
                                <div class="card-body text-center py-4">
                                    <div class="bg-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-calendar-plus text-white fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-success mb-2">Kelola Jadwal</h6>
                                    <p class="text-muted small mb-0">Atur jadwal ketersediaan lapangan</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="http://localhost:8000" target="_blank" class="text-decoration-none">
                            <div class="card border-0 bg-info bg-opacity-10 h-100 hover-shadow transition-all">
                                <div class="card-body text-center py-4">
                                    <div class="bg-info rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-shopping-cart text-white fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-info mb-2">Order Service</h6>
                                    <p class="text-muted small mb-0">Lihat dan kelola pemesanan</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="fw-bold mb-1">
                    <i class="fas fa-chart-pie text-success me-2"></i>
                    Activity Overview
                </h5>
                <p class="text-muted small mb-0">Ringkasan aktivitas sistem booking</p>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-4">
                        <div class="text-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-map-marked-alt text-primary"></i>
                            </div>
                            <h4 class="fw-bold text-primary mb-1"><?php echo e($totalLapangan); ?></h4>
                            <p class="text-muted small mb-0">Lapangan Aktif</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="bg-success bg-opacity-10 rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-shopping-cart text-success"></i>
                            </div>
                            <h4 class="fw-bold text-success mb-1"><?php echo e($totalOrders); ?></h4>
                            <p class="text-muted small mb-0">Total Orders</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center">
                            <div class="bg-warning bg-opacity-10 rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-clock text-warning"></i>
                            </div>
                            <h4 class="fw-bold text-warning mb-1"><?php echo e($totalHours); ?></h4>
                            <p class="text-muted small mb-0">Jam Booking</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pb-0">
                <h5 class="fw-bold mb-1">
                    <i class="fas fa-cog text-secondary me-2"></i>
                    System Info
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Laravel</span>
                    <span class="badge bg-primary"><?php echo e(app()->version()); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">PHP</span>
                    <span class="badge bg-secondary"><?php echo e(PHP_VERSION); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">Environment</span>
                    <span class="badge bg-<?php echo e(app()->environment('production') ? 'danger' : 'warning'); ?>">
                        <?php echo e(ucfirst(app()->environment())); ?>

                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small">Debug</span>
                    <span class="badge bg-<?php echo e(config('app.debug') ? 'warning' : 'success'); ?>">
                        <?php echo e(config('app.debug') ? 'On' : 'Off'); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
<!-- End Main Content Container -->

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.transition-all {
    transition: all 0.3s ease;
}

/* Enhanced spacing and layout */
.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
}

/* Navigation pills styling */
.nav-pills .nav-link {
    border-radius: 0.5rem;
    padding: 0.75rem 1.5rem;
    margin: 0 0.25rem;
    transition: all 0.3s ease;
    font-weight: 500;
}
.nav-pills .nav-link:hover:not(.active) {
    background-color: #f1f5f9;
    color: #475569 !important;
    transform: translateY(-1px);
}
.nav-pills .nav-link.active {
    color: white !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Card improvements */
.card {
    border-radius: 0.75rem;
    border: none;
    transition: all 0.2s ease;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .container-fluid {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .nav-pills .nav-link {
        padding: 0.5rem 1rem;
        margin: 0.1rem;
        font-size: 0.9rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/dashboard.blade.php ENDPATH**/ ?>