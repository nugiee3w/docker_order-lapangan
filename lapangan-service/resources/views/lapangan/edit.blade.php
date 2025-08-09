@extends('layouts.app')

@section('title', 'Edit Lapangan - Booking Lapangan')

@section('content')
<!-- Breadcrumb Navigation -->
<div class="row mb-3">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white p-2 rounded shadow-sm mb-0" style="font-size: 0.85rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none">
                        <i class="fas fa-home me-1"></i>Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('lapangan.index') }}" class="text-decoration-none">
                        <i class="fas fa-map-marked-alt me-1"></i>Kelola Lapangan
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-edit me-1"></i>Edit Lapangan
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
                            <i class="fas fa-edit me-3"></i>
                            Edit Lapangan
                        </h1>
                        <p class="mb-0 opacity-75">Ubah informasi lapangan olahraga</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('lapangan.index') }}" class="btn btn-light btn-lg shadow">
                            <i class="fas fa-arrow-left me-2"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container-fluid px-3 px-md-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Loading State -->
            <div id="loadingState" class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0 text-muted">Memuat data lapangan...</p>
                </div>
            </div>

            <!-- Edit Form -->
            <div id="editFormContainer" class="card border-0 shadow-sm" style="display: none;">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-edit me-2 text-primary"></i>
                        Form Edit Lapangan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form id="editLapanganForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Nama Lapangan -->
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label fw-semibold">
                                    <i class="fas fa-signature me-1"></i>
                                    Nama Lapangan <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" id="nama" name="nama" 
                                       placeholder="Masukkan nama lapangan" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Jenis Lapangan -->
                            <div class="col-md-6 mb-3">
                                <label for="jenis" class="form-label fw-semibold">
                                    <i class="fas fa-layer-group me-1"></i>
                                    Jenis Lapangan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="jenis" name="jenis" required>
                                    <option value="">Pilih jenis lapangan</option>
                                    <option value="Futsal">Futsal</option>
                                    <option value="Badminton">Badminton</option>
                                    <option value="Basket">Basket</option>
                                    <option value="Tenis">Tenis</option>
                                    <option value="Voli">Voli</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Harga per Jam -->
                            <div class="col-md-6 mb-3">
                                <label for="harga_per_jam" class="form-label fw-semibold">
                                    <i class="fas fa-money-bill me-1"></i>
                                    Harga per Jam <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="harga_per_jam" name="harga_per_jam" 
                                           placeholder="0" min="0" step="1000" required>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-semibold">
                                    <i class="fas fa-toggle-on me-1"></i>
                                    Status <span class="text-danger">*</span>
                                </label>
                                <select class="form-select form-select-lg" id="status" name="status" required>
                                    <option value="">Pilih status</option>
                                    <option value="tersedia">Tersedia</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="tidak_tersedia">Tidak Tersedia</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <!-- Fasilitas -->
                        <div class="mb-3">
                            <label for="fasilitas" class="form-label fw-semibold">
                                <i class="fas fa-list me-1"></i>
                                Fasilitas Lapangan
                            </label>
                            <textarea class="form-control" id="fasilitas" name="fasilitas" rows="3" 
                                      placeholder="Masukkan fasilitas lapangan, pisahkan dengan koma (contoh: Parkir, Toilet, Kantin, AC)"></textarea>
                            <div class="form-text">Masukkan fasilitas yang tersedia, pisahkan dengan koma</div>
                        </div>

                        <!-- Lokasi -->
                        <div class="mb-3">
                            <label for="lokasi" class="form-label fw-semibold">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Lokasi Lapangan
                            </label>
                            <input type="text" class="form-control form-control-lg" id="lokasi" name="lokasi" 
                                   placeholder="Masukkan lokasi lapangan (contoh: Lantai 1 Spot A, Gedung Utama)">
                            <div class="form-text">Lokasi atau alamat spesifik lapangan dalam area kompleks</div>
                            <div class="invalid-feedback"></div>
                        </div>

                        <!-- Gambar Lapangan -->
                        <div class="mb-3">
                            <label for="gambar" class="form-label fw-semibold">
                                <i class="fas fa-image me-1"></i>
                                Gambar Lapangan
                            </label>
                            <div class="image-upload-area">
                                <input type="file" class="form-control form-control-lg" id="gambar" name="gambar" 
                                       accept="image/jpeg,image/jpg,image/png,image/gif">
                                <div class="mt-2">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Klik untuk upload gambar baru atau drag & drop</p>
                                </div>
                            </div>
                            <div class="form-text">Upload gambar lapangan baru (JPEG, JPG, PNG, GIF). Maksimal 2MB. Kosongkan jika tidak ingin mengubah gambar</div>
                            <div class="invalid-feedback"></div>
                            
                            <!-- Current Image Display -->
                            <div id="currentImageContainer" class="mt-3" style="display: none;">
                                <div class="d-flex align-items-center bg-light p-3 rounded">
                                    <img id="currentImg" src="" alt="Current Image" class="img-thumbnail me-3" style="width: 100px; height: 100px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-semibold">Gambar Saat Ini</p>
                                        <small class="text-muted">Gambar yang sedang digunakan</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Preview Container -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <div class="d-flex align-items-center bg-success bg-opacity-10 p-3 rounded border border-success border-opacity-25">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail me-3" style="width: 100px; height: 100px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <p class="mb-1 fw-semibold text-success">Preview Gambar Baru</p>
                                        <small class="text-muted" id="imageInfo"></small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeImage()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label for="deskripsi" class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1"></i>
                                Deskripsi Lapangan
                            </label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" 
                                      placeholder="Masukkan deskripsi lapangan (opsional)"></textarea>
                            <div class="form-text">Deskripsi dapat berisi informasi ukuran, material, atau keterangan lainnya</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between pt-3 border-top">
                            <a href="{{ route('lapangan.index') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="fas fa-times me-2"></i>
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>
                                Update Lapangan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Error State -->
            <div id="errorState" class="card border-0 shadow-sm" style="display: none;">
                <div class="card-body text-center py-5">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <h5 class="mb-2">Data Tidak Ditemukan</h5>
                    <p class="text-muted mb-3">Lapangan yang Anda cari tidak ditemukan atau telah dihapus.</p>
                    <a href="{{ route('lapangan.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali ke Daftar Lapangan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0">Mengupdate data lapangan...</p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Get lapangan ID from URL
const pathArray = window.location.pathname.split('/');
const lapanganId = pathArray[pathArray.indexOf('lapangan') + 1];

// Load lapangan data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadLapanganData();
});

function loadLapanganData() {
    fetch(`/web/lapangan/${lapanganId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateForm(data.data);
                showEditForm();
            } else {
                showErrorState();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorState();
        });
}

function populateForm(lapangan) {
    document.getElementById('nama').value = lapangan.nama || '';
    document.getElementById('jenis').value = lapangan.jenis || '';
    document.getElementById('harga_per_jam').value = lapangan.harga_per_jam || '';
    document.getElementById('status').value = lapangan.status || '';
    document.getElementById('deskripsi').value = lapangan.deskripsi || '';
    document.getElementById('lokasi').value = lapangan.lokasi || '';
    
    // Populate fasilitas (convert array to comma-separated string)
    if (lapangan.fasilitas && Array.isArray(lapangan.fasilitas)) {
        document.getElementById('fasilitas').value = lapangan.fasilitas.join(', ');
    } else if (lapangan.fasilitas && typeof lapangan.fasilitas === 'string') {
        document.getElementById('fasilitas').value = lapangan.fasilitas;
    }
    
    // Show current image if exists
    if (lapangan.gambar) {
        const currentImg = document.getElementById('currentImg');
        const currentContainer = document.getElementById('currentImageContainer');
        // Assuming images are stored in storage/app/public/lapangan folder
        currentImg.src = `/storage/${lapangan.gambar}`;
        currentContainer.style.display = 'block';
    }
}

function showEditForm() {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('editFormContainer').style.display = 'block';
    document.getElementById('errorState').style.display = 'none';
}

function showErrorState() {
    document.getElementById('loadingState').style.display = 'none';
    document.getElementById('editFormContainer').style.display = 'none';
    document.getElementById('errorState').style.display = 'block';
}

// Handle form submission
document.getElementById('editLapanganForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show loading modal
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    // Clear previous errors
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    // Prepare form data
    const formData = new FormData(this);
    
    // Submit form
    fetch(`/web/lapangan/${lapanganId}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-HTTP-Method-Override': 'PUT'
        }
    })
    .then(response => response.json())
    .then(data => {
        loadingModal.hide();
        
        if (data.success) {
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message || 'Lapangan berhasil diupdate',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ route("lapangan.index") }}';
            });
        } else {
            throw new Error(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        loadingModal.hide();
        console.error('Error:', error);
        
        // Show error message
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: error.message || 'Terjadi kesalahan saat mengupdate data',
            confirmButtonText: 'OK'
        });
    });
});

// Format currency input
document.getElementById('harga_per_jam').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value) {
        // Keep the raw value for form submission
        e.target.value = value;
    }
});

// Image preview functionality
document.getElementById('gambar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'File Terlalu Besar!',
                text: 'Ukuran file tidak boleh lebih dari 2MB',
                confirmButtonText: 'OK'
            });
            e.target.value = '';
            return;
        }

        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format File Tidak Valid!',
                text: 'Hanya file JPEG, JPG, PNG, dan GIF yang diperbolehkan',
                confirmButtonText: 'OK'
            });
            e.target.value = '';
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imageInfo').textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)} MB)`;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        // Hide preview if no file selected
        document.getElementById('imagePreview').style.display = 'none';
    }
});

// Drag and drop functionality
const uploadArea = document.querySelector('.image-upload-area');
const fileInput = document.getElementById('gambar');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    uploadArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    uploadArea.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    uploadArea.classList.add('dragover');
}

function unhighlight(e) {
    uploadArea.classList.remove('dragover');
}

uploadArea.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length > 0) {
        fileInput.files = files;
        fileInput.dispatchEvent(new Event('change'));
    }
}

function removeImage() {
    document.getElementById('gambar').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
    document.getElementById('imageInfo').textContent = '';
}
</script>

<!-- Include SweetAlert2 for better alerts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush