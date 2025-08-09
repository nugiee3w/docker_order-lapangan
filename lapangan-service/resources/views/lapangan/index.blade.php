@extends('layouts.app')

@section('title', 'Kelola Lapangan - Booking Lapangan')

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
                        <a href="{{ route('lapangan.create') }}" class="btn btn-light btn-lg shadow">
                            <i class="fas fa-plus me-2"></i>
                            Tambah Lapangan
                        </a>
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
                        <h3 class="fw-bold text-primary mb-1">{{ $lapangans->count() }}</h3>
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
                        <h3 class="fw-bold text-success mb-1">{{ $lapangans->where('status', 'tersedia')->count() }}</h3>
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
                        <h3 class="fw-bold text-warning mb-1">{{ $lapangans->where('status', 'maintenance')->count() }}</h3>
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
                        <h3 class="fw-bold text-info mb-1">{{ $lapangans->groupBy('jenis')->count() }}</h3>
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
    @foreach($lapangans as $lapangan)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 shadow-sm border-0 hover-shadow">
            @if($lapangan->gambar)
            <div class="card-img-top position-relative" style="height: 200px; overflow: hidden;">
                <img src="{{ url('storage/' . $lapangan->gambar) }}" 
                     alt="{{ $lapangan->nama }}" 
                     class="w-100 h-100" 
                     style="object-fit: cover;"
                     onerror="this.parentElement.innerHTML='<div class=&quot;no-image-placeholder d-flex align-items-center justify-content-center h-100&quot; style=&quot;background: #f8f9fa;&quot;><i class=&quot;fas fa-image fa-3x text-muted&quot;></i></div>'">
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge jenis-badge fs-6
                        @if($lapangan->jenis == 'futsal') bg-primary
                        @elseif($lapangan->jenis == 'badminton') bg-success
                        @elseif($lapangan->jenis == 'basket') bg-warning
                        @elseif($lapangan->jenis == 'tenis_meja') bg-info
                        @else bg-secondary
                        @endif
                    ">
                        <i class="fas fa-futbol me-1"></i>{{ ucfirst(str_replace('_', ' ', $lapangan->jenis)) }}
                    </span>
                </div>
            </div>
            @else
            <div class="card-img-top position-relative no-image-placeholder" style="height: 200px;">
                <i class="fas fa-image opacity-50"></i>
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge jenis-badge fs-6
                        @if($lapangan->jenis == 'futsal') bg-primary
                        @elseif($lapangan->jenis == 'badminton') bg-success
                        @elseif($lapangan->jenis == 'basket') bg-warning
                        @elseif($lapangan->jenis == 'tenis_meja') bg-info
                        @else bg-secondary
                        @endif
                    ">
                        <i class="fas fa-futbol me-1"></i>{{ ucfirst(str_replace('_', ' ', $lapangan->jenis)) }}
                    </span>
                </div>
            </div>
            @endif
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">{{ $lapangan->nama }}</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="viewLapanganDetail({{ $lapangan->id }})">
                            <i class="fas fa-eye me-2"></i>Lihat Detail
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('lapangan.edit', $lapangan->id) }}">
                            <i class="fas fa-edit me-2"></i>Edit Lapangan
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('jadwal', ['lapangan' => $lapangan->id]) }}">
                            <i class="fas fa-calendar me-2"></i>Lihat Jadwal
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteLapangan({{ $lapangan->id }})">
                            <i class="fas fa-trash me-2"></i>Hapus
                        </a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text text-muted">{{ $lapangan->deskripsi }}</p>
                
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <div class="bg-light rounded p-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted small"><i class="fas fa-money-bill me-1"></i>Harga per Jam</span>
                                <span class="text-success fw-bold">Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i>Lokasi</span>
                                <span class="fw-medium">{{ $lapangan->lokasi }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold mb-2"><i class="fas fa-tools me-2"></i>Fasilitas:</h6>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($lapangan->fasilitas as $fasilitas)
                            <span class="badge bg-secondary">{{ $fasilitas }}</span>
                        @endforeach
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold"><i class="fas fa-info-circle me-2"></i>Status:</span>
                        <span class="badge status-badge {{ $lapangan->status == 'tersedia' ? 'bg-success' : ($lapangan->status == 'maintenance' ? 'bg-warning' : 'bg-danger') }}">
                            <i class="fas fa-circle me-1"></i>{{ ucfirst($lapangan->status) }}
                        </span>
                    </div>
                </div>
                
                <!-- Jadwal Summary -->
                <div class="border-top pt-3">
                    <h6 class="fw-bold mb-3"><i class="fas fa-clock me-2"></i>Statistik Booking:</h6>
                    @php
                        // Use order statistics from order service if available
                        $totalPemesan = $lapangan->order_stats['total_pemesan'] ?? 0;
                        $totalJamDipesan = $lapangan->order_stats['total_jam_dipesan'] ?? 0;
                    @endphp
                    <div class="row text-center g-2">
                        <div class="col-6">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                <div class="fw-bold">{{ $totalPemesan }}</div>
                                <small>Pemesan</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-success bg-opacity-10 text-success rounded p-2">
                                <div class="fw-bold">{{ $totalJamDipesan }}</div>
                                <small>Total Jam Dipesan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0">
                <div class="d-grid gap-2">
                    <a href="{{ route('lapangan.edit', $lapangan->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Lapangan
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    
    @if($lapangans->isEmpty())
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-map-marked-alt fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">Belum Ada Lapangan</h4>
                <p class="text-muted mb-4">Tambahkan lapangan pertama Anda untuk memulai mengelola booking.</p>
                <a href="{{ route('lapangan.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>Tambah Lapangan Pertama
                </a>
            </div>
        </div>
    </div>
    @endif
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" class="btn btn-primary" id="editFromDetailBtn">
                    <i class="fas fa-edit me-2"></i>Edit Lapangan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Add CSS for no-image placeholder -->
<style>
.no-image-placeholder {
    background: linear-gradient(45deg, #f8f9fa 25%, transparent 25%), 
                linear-gradient(-45deg, #f8f9fa 25%, transparent 25%), 
                linear-gradient(45deg, transparent 75%, #f8f9fa 75%), 
                linear-gradient(-45deg, transparent 75%, #f8f9fa 75%);
    background-size: 20px 20px;
    background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.hover-shadow {
    transition: box-shadow 0.3s ease;
}

.hover-shadow:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    transform: translateY(-2px);
}

.jenis-badge {
    font-weight: 500;
    padding: 6px 12px;
}

.status-badge {
    font-weight: 500;
    padding: 4px 8px;
}
</style>
@endsection

@section('scripts')
<script>
// Base URL untuk Web API
const WEB_BASE_URL = window.location.origin + '/web';

// Get CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
        
        if (!response.ok) {
            throw new Error('Lapangan tidak ditemukan');
        }
        
        const result = await response.json();
        const lapangan = result.data;
        
        // Build detail content
        const detailContent = document.getElementById('detailContent');
        const editBtn = document.getElementById('editFromDetailBtn');
        
        editBtn.href = `/lapangan/${lapangan.id}/edit`;
        
        detailContent.innerHTML = `
            <div class="col-12">
                ${lapangan.gambar ? `
                    <div class="text-center mb-4">
                        <img src="${window.location.origin}/storage/${lapangan.gambar}" 
                             alt="${lapangan.nama}" 
                             class="img-fluid rounded shadow-sm"
                             style="max-height: 300px; object-fit: cover;">
                    </div>
                ` : ''}
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong>Nama Lapangan:</strong><br>
                        <span class="text-muted">${lapangan.nama}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Jenis Olahraga:</strong><br>
                        <span class="badge bg-primary">${lapangan.jenis.replace('_', ' ').toUpperCase()}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Harga per Jam:</strong><br>
                        <span class="text-success fw-bold">Rp ${new Intl.NumberFormat('id-ID').format(lapangan.harga_per_jam)}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Lokasi:</strong><br>
                        <span class="text-muted">${lapangan.lokasi}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Status:</strong><br>
                        <span class="badge ${lapangan.status === 'tersedia' ? 'bg-success' : (lapangan.status === 'maintenance' ? 'bg-warning' : 'bg-danger')}">${lapangan.status.toUpperCase()}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Fasilitas:</strong><br>
                        ${lapangan.fasilitas && lapangan.fasilitas.length > 0 ? 
                            lapangan.fasilitas.map(f => `<span class="badge bg-secondary me-1">${f}</span>`).join('') 
                            : '<span class="text-muted">Tidak ada fasilitas</span>'}
                    </div>
                    ${lapangan.deskripsi ? `
                        <div class="col-12">
                            <strong>Deskripsi:</strong><br>
                            <span class="text-muted">${lapangan.deskripsi}</span>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        // Show modal
        new bootstrap.Modal(document.getElementById('viewDetailModal')).show();
        
    } catch (error) {
        showAlert('error', 'Gagal memuat detail lapangan: ' + error.message);
    }
}

// Delete Lapangan
let lapanganToDelete = null;

function deleteLapangan(id) {
    lapanganToDelete = id;
    
    // Show confirmation modal
    const modal = new bootstrap.Modal(document.getElementById('deleteLapanganModal'));
    
    // You can add lapangan name here if needed
    document.getElementById('deleteConfirmationText').textContent = `ID Lapangan: ${id}`;
    
    modal.show();
}

// Confirm delete
document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!lapanganToDelete) return;
    
    try {
        const response = await fetch(`${WEB_BASE_URL}/lapangan/${lapanganToDelete}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const result = await response.json();
        
        if (response.ok) {
            showAlert('success', result.message || 'Lapangan berhasil dihapus!');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('error', result.message || 'Gagal menghapus lapangan');
        }
    } catch (error) {
        showAlert('error', 'Terjadi kesalahan koneksi');
        console.error('Error:', error);
    } finally {
        // Hide modal
        bootstrap.Modal.getInstance(document.getElementById('deleteLapanganModal')).hide();
        lapanganToDelete = null;
    }
});

// Show alert function
function showAlert(type, message) {
    // Remove existing alerts
    document.querySelectorAll('.alert').forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at top of container
    const container = document.querySelector('.container-fluid') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>
@endsection
