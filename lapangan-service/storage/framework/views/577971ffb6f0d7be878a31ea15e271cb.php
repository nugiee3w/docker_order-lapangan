

<?php $__env->startSection('title', 'Kelola Lapangan - Booking Lapangan'); ?>

<?php $__env->startSection('content'); ?>
<!-- Breadcrumb Navigation -->
<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white p-2 rounded shadow-sm mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('dashboard')); ?>" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-map-marked-alt me-1"></i>Kelola Lapangan
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Header Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white py-4 px-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-2 fw-bold">
                            <i class="fas fa-map-marked-alt me-3"></i>
                            Kelola Lapangan
                        </h1>
                        <p class="mb-0 opacity-75">Tambah, edit, dan kelola data lapangan olahraga dengan mudah</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-light btn-lg shadow" data-bs-toggle="modal" data-bs-target="#addLapanganModal">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Lapangan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Container with Better Spacing -->
<div class="container-fluid px-3 px-md-4">

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="fw-bold text-primary mb-1"><?php echo e($lapangans->count()); ?></h3>
                        <p class="text-muted mb-0 small">Total Lapangan</p>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-map-marked-alt text-primary fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="fw-bold text-success mb-1"><?php echo e($lapangans->where('status', 'tersedia')->count()); ?></h3>
                        <p class="text-muted mb-0 small">Lapangan Tersedia</p>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-check-circle text-success fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="fw-bold text-warning mb-1"><?php echo e($lapangans->where('status', 'maintenance')->count()); ?></h3>
                        <p class="text-muted mb-0 small">Maintenance</p>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-tools text-warning fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h3 class="fw-bold text-info mb-1"><?php echo e($lapangans->groupBy('jenis')->count()); ?></h3>
                        <p class="text-muted mb-0 small">Jenis Olahraga</p>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-layer-group text-info fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lapangan Cards Grid -->
<div class="row">
    <?php $__currentLoopData = $lapangans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lapangan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm border-0 hover-shadow">
            <?php if($lapangan->gambar): ?>
            <div class="card-img-top position-relative" style="height: 200px; overflow: hidden;">
                <img src="<?php echo e(asset('storage/' . $lapangan->gambar)); ?>" 
                     alt="<?php echo e($lapangan->nama); ?>" 
                     class="w-100 h-100" 
                     style="object-fit: cover;">
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge jenis-badge fs-6
                        <?php if($lapangan->jenis == 'futsal'): ?> bg-primary
                        <?php elseif($lapangan->jenis == 'badminton'): ?> bg-success
                        <?php elseif($lapangan->jenis == 'basket'): ?> bg-warning
                        <?php elseif($lapangan->jenis == 'tenis_meja'): ?> bg-info
                        <?php else: ?> bg-secondary
                        <?php endif; ?>
                    ">
                        <i class="fas fa-futbol me-1"></i><?php echo e(ucfirst(str_replace('_', ' ', $lapangan->jenis))); ?>

                    </span>
                </div>
            </div>
            <?php else: ?>
            <div class="card-img-top position-relative no-image-placeholder" style="height: 200px;">
                <i class="fas fa-image opacity-50"></i>
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge jenis-badge fs-6
                        <?php if($lapangan->jenis == 'futsal'): ?> bg-primary
                        <?php elseif($lapangan->jenis == 'badminton'): ?> bg-success
                        <?php elseif($lapangan->jenis == 'basket'): ?> bg-warning
                        <?php elseif($lapangan->jenis == 'tenis_meja'): ?> bg-info
                        <?php else: ?> bg-secondary
                        <?php endif; ?>
                    ">
                        <i class="fas fa-futbol me-1"></i><?php echo e(ucfirst(str_replace('_', ' ', $lapangan->jenis))); ?>

                    </span>
                </div>
            </div>
            <?php endif; ?>
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><?php echo e($lapangan->nama); ?></h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="viewLapanganDetail(<?php echo e($lapangan->id); ?>)">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="editLapangan(<?php echo e($lapangan->id); ?>)">
                            <i class="fas fa-edit me-2"></i>Edit Lapangan
                        </a></li>
                        <li><a class="dropdown-item" href="<?php echo e(route('jadwal', ['lapangan' => $lapangan->id])); ?>">
                            <i class="fas fa-calendar me-2"></i>Lihat Jadwal
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteLapangan(<?php echo e($lapangan->id); ?>)">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text text-muted"><?php echo e($lapangan->deskripsi); ?></p>
                
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <div class="bg-light rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small"><i class="fas fa-money-bill me-1"></i>Harga per Jam</span>
                                <span class="text-success fw-bold">Rp <?php echo e(number_format($lapangan->harga_per_jam, 0, ',', '.')); ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i>Lokasi</span>
                                <span class="fw-medium"><?php echo e($lapangan->lokasi); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold mb-2"><i class="fas fa-tools me-2"></i>Fasilitas:</h6>
                    <div class="d-flex flex-wrap gap-1">
                        <?php $__currentLoopData = $lapangan->fasilitas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fasilitas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="badge bg-secondary"><?php echo e($fasilitas); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Status:</span>
                        <span class="badge status-badge <?php echo e($lapangan->status == 'tersedia' ? 'bg-success' : ($lapangan->status == 'maintenance' ? 'bg-warning' : 'bg-danger')); ?>">
                            <i class="fas fa-circle me-1"></i><?php echo e(ucfirst($lapangan->status)); ?>

                        </span>
                    </div>
                </div>
                
                <!-- Jadwal Summary -->
                <div class="border-top pt-3">
                    <h6 class="fw-bold mb-3"><i class="fas fa-clock me-2"></i>Statistik Booking:</h6>
                    <?php
                        // Use order statistics from order service if available
                        $totalPemesan = $lapangan->order_stats['total_pemesan'] ?? 0;
                        $totalJamDipesan = $lapangan->order_stats['total_jam_dipesan'] ?? 0;
                    ?>
                    <div class="row text-center g-2">
                        <div class="col-6">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                <div class="fw-bold"><?php echo e($totalPemesan); ?></div>
                                <small>Pemesan</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 text-success rounded p-2">
                                <div class="fw-bold"><?php echo e($totalJamDipesan); ?></div>
                                <small>Total Jam Dipesan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="editLapangan(<?php echo e($lapangan->id); ?>)">
                        <i class="fas fa-edit me-2"></i>Edit Lapangan
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    
    <?php if($lapangans->isEmpty()): ?>
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-map-marked-alt fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">Belum Ada Lapangan</h4>
                <p class="text-muted mb-4">Tambahkan lapangan pertama Anda untuk memulai mengelola booking.</p>
                <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addLapanganModal">
                    <i class="fas fa-plus me-2"></i>Tambah Lapangan Pertama
                </button>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Add Lapangan Modal -->
<div class="modal fade" id="addLapanganModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Tambah Lapangan Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addLapanganForm">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama" class="form-label">Nama Lapangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="col-md-6">
                            <label for="jenis" class="form-label">Jenis Olahraga <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis" name="jenis" required>
                                <option value="">Pilih Jenis</option>
                                <option value="futsal">Futsal</option>
                                <option value="badminton">Badminton</option>
                                <option value="basket">Basket</option>
                                <option value="tenis_meja">Tenis Meja</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="harga_per_jam" class="form-label">Harga per Jam <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="harga_per_jam" name="harga_per_jam" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" required>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="tersedia">Tersedia</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="tidak_tersedia">Tidak Tersedia</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fasilitas" class="form-label">Fasilitas</label>
                            <input type="text" class="form-control" id="fasilitas" name="fasilitas" 
                                   placeholder="Pisahkan dengan koma (contoh: AC, Toilet, Parkir)">
                            <small class="text-muted">Pisahkan setiap fasilitas dengan koma</small>
                        </div>
                        <div class="col-12">
                            <label for="gambar" class="form-label">Gambar Lapangan</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                            <small class="text-muted">Format yang didukung: JPG, JPEG, PNG, GIF (Maksimal 2MB)</small>
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Lapangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lapangan Modal -->
<div class="modal fade" id="editLapanganModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Lapangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editLapanganForm">
                <input type="hidden" id="edit_lapangan_id" name="id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_nama" class="form-label">Nama Lapangan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_nama" name="nama" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_jenis" class="form-label">Jenis Olahraga <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_jenis" name="jenis" required>
                                <option value="">Pilih Jenis</option>
                                <option value="futsal">Futsal</option>
                                <option value="badminton">Badminton</option>
                                <option value="basket">Basket</option>
                                <option value="tenis_meja">Tenis Meja</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_harga_per_jam" class="form-label">Harga per Jam <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="edit_harga_per_jam" name="harga_per_jam" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_lokasi" class="form-label">Lokasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_lokasi" name="lokasi" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="tersedia">Tersedia</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="tidak_tersedia">Tidak Tersedia</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_fasilitas" class="form-label">Fasilitas</label>
                            <input type="text" class="form-control" id="edit_fasilitas" name="fasilitas" 
                                   placeholder="Pisahkan dengan koma (contoh: AC, Toilet, Parkir)">
                            <small class="text-muted">Pisahkan setiap fasilitas dengan koma</small>
                        </div>
                        <div class="col-12">
                            <label for="edit_gambar" class="form-label">Gambar Lapangan</label>
                            <input type="file" class="form-control" id="edit_gambar" name="gambar" accept="image/*">
                            <small class="text-muted">Format yang didukung: JPG, JPEG, PNG, GIF (Maksimal 2MB). Kosongkan jika tidak ingin mengubah gambar.</small>
                            <div id="editImagePreview" class="mt-3">
                                <img id="editPreviewImg" src="" alt="Current Image" class="img-thumbnail" style="max-width: 200px; max-height: 200px; display: none;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Lapangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteLapanganModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus lapangan ini?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-warning me-2"></i>
                    <strong>Peringatan:</strong> Semua jadwal yang terkait dengan lapangan ini juga akan terhapus.
                </div>
                <div id="deleteConfirmationText" class="fw-bold"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Detail Modal -->
<div class="modal fade" id="viewDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Detail Lapangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4" id="detailContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="editFromDetailBtn">
                    <i class="fas fa-edit me-2"></i>Edit Lapangan
                </button>
            </div>
        </div>
    </div>
</div>
<?php $__env->startSection('scripts'); ?>
<script>
// Base URL untuk Web API
const WEB_BASE_URL = window.location.origin + '/web';

// Get CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Configure default headers for all requests
const defaultHeaders = {
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': csrfToken
};

// Image preview functionality
document.getElementById('gambar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            e.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.');
            e.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Edit image preview functionality
document.getElementById('edit_gambar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const previewImg = document.getElementById('editPreviewImg');
    
    if (file) {
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 2MB.');
            e.target.value = '';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format file tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewImg.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Add Lapangan
document.getElementById('addLapanganForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`${WEB_BASE_URL}/lapangan`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showAlert('success', 'Lapangan berhasil ditambahkan!');
            bootstrap.Modal.getInstance(document.getElementById('addLapanganModal')).hide();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('error', result.message || 'Terjadi kesalahan saat menambah lapangan');
        }
    } catch (error) {
        showAlert('error', 'Terjadi kesalahan koneksi');
        console.error('Error:', error);
    }
});

// Edit Lapangan
async function editLapangan(id) {
    try {
        const response = await fetch(`${WEB_BASE_URL}/lapangan/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const result = await response.json();
        
        if (response.ok && result.success) {
            const lapangan = result.data;
            
            // Populate form
            document.getElementById('edit_lapangan_id').value = lapangan.id;
            document.getElementById('edit_nama').value = lapangan.nama;
            document.getElementById('edit_jenis').value = lapangan.jenis;
            document.getElementById('edit_deskripsi').value = lapangan.deskripsi || '';
            document.getElementById('edit_harga_per_jam').value = lapangan.harga_per_jam;
            document.getElementById('edit_lokasi').value = lapangan.lokasi;
            document.getElementById('edit_status').value = lapangan.status;
            document.getElementById('edit_fasilitas').value = Array.isArray(lapangan.fasilitas) ? lapangan.fasilitas.join(', ') : '';
            
            // Show current image if exists
            const editPreviewImg = document.getElementById('editPreviewImg');
            if (lapangan.gambar) {
                editPreviewImg.src = `${window.location.origin}/storage/${lapangan.gambar}`;
                editPreviewImg.style.display = 'block';
            } else {
                editPreviewImg.style.display = 'none';
            }
            
            // Clear file input
            document.getElementById('edit_gambar').value = '';
            
            // Show modal
            new bootstrap.Modal(document.getElementById('editLapanganModal')).show();
        } else {
            showAlert('error', 'Gagal memuat data lapangan');
        }
    } catch (error) {
        showAlert('error', 'Terjadi kesalahan koneksi');
        console.error('Error:', error);
    }
}

// View Lapangan Detail
async function viewLapanganDetail(id) {
    try {
        const response = await fetch(`${WEB_BASE_URL}/lapangan/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const result = await response.json();
        
        if (response.ok && result.success) {
            const lapangan = result.data;
            
            // Generate fasilitas badges
            const fasilitasBadges = Array.isArray(lapangan.fasilitas) 
                ? lapangan.fasilitas.map(f => `<span class="badge bg-secondary me-1 mb-1">${f}</span>`).join('')
                : '<span class="text-muted">Tidak ada fasilitas</span>';
            
            // Generate status badge
            const statusClass = lapangan.status === 'tersedia' ? 'bg-success' : 
                               lapangan.status === 'maintenance' ? 'bg-warning' : 'bg-danger';
            
            // Generate jenis badge
            const jenisClass = lapangan.jenis === 'futsal' ? 'bg-primary' :
                              lapangan.jenis === 'badminton' ? 'bg-success' :
                              lapangan.jenis === 'basket' ? 'bg-warning' :
                              lapangan.jenis === 'tenis_meja' ? 'bg-info' : 'bg-secondary';
            
            // Generate image section
            const imageSection = lapangan.gambar 
                ? `<div class="col-12 mb-4">
                     <div class="text-center">
                       <img src="${window.location.origin}/storage/${lapangan.gambar}" 
                            alt="${lapangan.nama}" 
                            class="img-fluid rounded shadow"
                            style="max-height: 300px; object-fit: cover;">
                     </div>
                   </div>`
                : '';
            
            // Populate detail content
            document.getElementById('detailContent').innerHTML = `
                ${imageSection}
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h3 class="fw-bold text-primary">${lapangan.nama}</h3>
                                <span class="badge ${jenisClass} fs-6">
                                    <i class="fas fa-futbol me-1"></i>${lapangan.jenis.replace('_', ' ').charAt(0).toUpperCase() + lapangan.jenis.replace('_', ' ').slice(1)}
                                </span>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="bg-white rounded p-3 h-100">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Dasar</h6>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Deskripsi:</small>
                                            <span>${lapangan.deskripsi || 'Tidak ada deskripsi'}</span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Lokasi:</small>
                                            <span><i class="fas fa-map-marker-alt me-1 text-danger"></i>${lapangan.lokasi}</span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Status:</small>
                                            <span class="badge ${statusClass}">
                                                <i class="fas fa-circle me-1"></i>${lapangan.status.charAt(0).toUpperCase() + lapangan.status.slice(1)}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="bg-white rounded p-3 h-100">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-money-bill me-2 text-success"></i>Harga & Fasilitas</h6>
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Harga per Jam:</small>
                                            <span class="fs-4 fw-bold text-success">Rp ${parseInt(lapangan.harga_per_jam).toLocaleString('id-ID')}</span>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block mb-2">Fasilitas:</small>
                                            <div>${fasilitasBadges}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="bg-white rounded p-3">
                                        <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2 text-info"></i>Statistik Booking</h6>
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                                    <div class="fs-5 fw-bold">${lapangan.order_stats ? lapangan.order_stats.total_pemesan : 0}</div>
                                                    <small>Pemesan</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="bg-success bg-opacity-10 text-success rounded p-2">
                                                    <div class="fs-5 fw-bold">${lapangan.order_stats ? lapangan.order_stats.total_jam_dipesan : 0}</div>
                                                    <small>Total Jam Dipesan</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Store ID for edit button
            document.getElementById('editFromDetailBtn').onclick = function() {
                bootstrap.Modal.getInstance(document.getElementById('viewDetailModal')).hide();
                setTimeout(() => editLapangan(id), 300);
            };
            
            // Show modal
            new bootstrap.Modal(document.getElementById('viewDetailModal')).show();
        } else {
            showAlert('error', 'Gagal memuat detail lapangan');
        }
    } catch (error) {
        showAlert('error', 'Terjadi kesalahan koneksi');
        console.error('Error:', error);
    }
}

// Update Lapangan
document.getElementById('editLapanganForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const id = formData.get('id');
    
    // Add method override for Laravel
    formData.append('_method', 'PUT');
    
    try {
        const response = await fetch(`${WEB_BASE_URL}/lapangan/${id}`, {
            method: 'POST', // Laravel requires POST with _method override for file uploads
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showAlert('success', 'Lapangan berhasil diupdate!');
            bootstrap.Modal.getInstance(document.getElementById('editLapanganModal')).hide();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('error', result.message || 'Terjadi kesalahan saat mengupdate lapangan');
        }
    } catch (error) {
        showAlert('error', 'Terjadi kesalahan koneksi');
        console.error('Error:', error);
    }
});

// Delete Lapangan
let deleteTargetId = null;

function deleteLapangan(id) {
    deleteTargetId = id;
    
    // Find lapangan name for confirmation
    const lapanganCard = document.querySelector(`[onclick="editLapangan(${id})"]`).closest('.card');
    const lapanganName = lapanganCard.querySelector('h5').textContent;
    
    document.getElementById('deleteConfirmationText').textContent = `Lapangan: ${lapanganName}`;
    new bootstrap.Modal(document.getElementById('deleteLapanganModal')).show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!deleteTargetId) return;
    
    try {
        const response = await fetch(`${WEB_BASE_URL}/lapangan/${deleteTargetId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showAlert('success', 'Lapangan berhasil dihapus!');
            bootstrap.Modal.getInstance(document.getElementById('deleteLapanganModal')).hide();
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('error', result.message || 'Terjadi kesalahan saat menghapus lapangan');
        }
    } catch (error) {
        showAlert('error', 'Terjadi kesalahan koneksi');
        console.error('Error:', error);
    }
    
    deleteTargetId = null;
});

// Alert Helper
function showAlert(type, message) {
    const alertContainer = document.getElementById('alertContainer') || createAlertContainer();
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alertContainer';
    container.style.position = 'fixed';
    container.style.top = '20px';
    container.style.right = '20px';
    container.style.zIndex = '9999';
    container.style.maxWidth = '400px';
    document.body.appendChild(container);
    return container;
}

// Reset forms when modals are hidden
document.getElementById('addLapanganModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('addLapanganForm').reset();
    document.getElementById('imagePreview').style.display = 'none';
});

document.getElementById('editLapanganModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('editLapanganForm').reset();
    document.getElementById('editPreviewImg').style.display = 'none';
});
</script>

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
.jenis-badge {
    font-size: 0.875rem;
}
.card-header .dropdown-toggle::after {
    display: none;
}

/* Enhanced spacing and layout */
.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
}

/* Image upload preview styles */
#imagePreview, #editImagePreview {
    transition: all 0.3s ease;
}

#previewImg, #editPreviewImg {
    border-radius: 0.5rem;
    transition: transform 0.2s ease;
}

#previewImg:hover, #editPreviewImg:hover {
    transform: scale(1.05);
}

/* Card image styles */
.card-img-top img {
    transition: transform 0.3s ease;
}

.card:hover .card-img-top img {
    transform: scale(1.02);
}

/* Image overlay badge */
.card-img-top .position-absolute {
    background: rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(4px);
    border-radius: 0.25rem;
}

/* No image placeholder */
.no-image-placeholder {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 2rem;
}

/* Breadcrumb styling */
.breadcrumb {
    margin-bottom: 0;
    font-size: 0.9rem;
}
.breadcrumb-item a {
    color: #6366f1;
    transition: color 0.2s;
}
.breadcrumb-item a:hover {
    color: #4f46e5;
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

/* Table responsive improvements */
.table-responsive {
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Statistics cards improvements */
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 1rem;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/resources/views/lapangan/index.blade.php ENDPATH**/ ?>