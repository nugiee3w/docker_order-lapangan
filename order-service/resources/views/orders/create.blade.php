@extends('layouts.app')

@section('title', 'Tambah Pemesanan Baru')

@section('styles')
<style>
    .card {
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    
    .animate__fadeIn {
        animation-duration: 0.5s;
    }
    
    #lapangan-details .card-header {
        border-radius: 0.375rem 0.375rem 0 0;
    }
    
    #detail-gambar {
        border: 3px solid #dee2e6;
        transition: all 0.3s ease;
    }
    
    #detail-gambar:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }
    
    .facility-badge {
        margin: 2px;
        transition: all 0.2s ease;
    }
    
    .facility-badge:hover {
        transform: scale(1.1);
    }
    
    .status-indicator {
        position: relative;
        display: inline-block;
    }
    
    .status-indicator::before {
        content: '';
        position: absolute;
        left: -15px;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #28a745;
    }
    
    .status-maintenance::before {
        background: #ffc107;
    }
    
    .status-unavailable::before {
        background: #dc3545;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle text-primary"></i> Tambah Pemesanan Baru
        </h1>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Order
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Pemesanan Lapangan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        
                        <!-- Customer Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-user"></i> Informasi Customer
                                </h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Nama Customer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                       id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                       id="customer_email" name="customer_email" value="{{ old('customer_email') }}" required>
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_phone" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                       id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Booking Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-calendar-alt"></i> Informasi Booking
                                </h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="lapangan_id" class="form-label">Pilih Lapangan <span class="text-danger">*</span></label>
                                <select class="form-control @error('lapangan_id') is-invalid @enderror" 
                                        id="lapangan_id" name="lapangan_id" required>
                                    <option value="">-- Pilih Lapangan --</option>
                                    @if(!empty($lapangan_list))
                                        @foreach($lapangan_list as $lapangan)
                                            <option value="{{ $lapangan['id'] }}" 
                                                    data-harga="{{ (float)$lapangan['harga_per_jam'] }}"
                                                    data-nama="{{ $lapangan['nama'] }}"
                                                    data-jenis="{{ $lapangan['jenis'] }}"
                                                    data-lokasi="{{ $lapangan['lokasi'] ?? 'Tidak tersedia' }}"
                                                    data-fasilitas="{{ is_array($lapangan['fasilitas']) ? implode(', ', $lapangan['fasilitas']) : $lapangan['fasilitas'] }}"
                                                    data-deskripsi="{{ $lapangan['deskripsi'] ?? 'Tidak ada deskripsi' }}"
                                                    data-status="{{ $lapangan['status'] }}"
                                                    data-gambar="{{ $lapangan['gambar'] ?? '' }}"
                                                    {{ old('lapangan_id') == $lapangan['id'] ? 'selected' : '' }}>
                                                {{ $lapangan['nama'] }} - {{ ucfirst($lapangan['jenis']) }} 
                                                (Rp {{ number_format((float)$lapangan['harga_per_jam'], 0, ',', '.') }}/jam)
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">Tidak ada lapangan tersedia</option>
                                    @endif
                                </select>
                                @error('lapangan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_booking" class="form-label">Tanggal Booking <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal_booking') is-invalid @enderror" 
                                       id="tanggal_booking" name="tanggal_booking" value="{{ old('tanggal_booking') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('tanggal_booking')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                <select class="form-control @error('jam_mulai') is-invalid @enderror" 
                                        id="jam_mulai" name="jam_mulai" required>
                                    <option value="">-- Pilih Jam Mulai --</option>
                                    <option value="06:00" {{ old('jam_mulai') == '06:00' ? 'selected' : '' }}>06:00 - Jam 6 Pagi</option>
                                    <option value="07:00" {{ old('jam_mulai') == '07:00' ? 'selected' : '' }}>07:00 - Jam 7 Pagi</option>
                                    <option value="08:00" {{ old('jam_mulai') == '08:00' ? 'selected' : '' }}>08:00 - Jam 8 Pagi</option>
                                    <option value="09:00" {{ old('jam_mulai') == '09:00' ? 'selected' : '' }}>09:00 - Jam 9 Pagi</option>
                                    <option value="10:00" {{ old('jam_mulai') == '10:00' ? 'selected' : '' }}>10:00 - Jam 10 Pagi</option>
                                    <option value="11:00" {{ old('jam_mulai') == '11:00' ? 'selected' : '' }}>11:00 - Jam 11 Pagi</option>
                                    <option value="12:00" {{ old('jam_mulai') == '12:00' ? 'selected' : '' }}>12:00 - Jam 12 Siang</option>
                                    <option value="13:00" {{ old('jam_mulai') == '13:00' ? 'selected' : '' }}>13:00 - Jam 1 Siang</option>
                                    <option value="14:00" {{ old('jam_mulai') == '14:00' ? 'selected' : '' }}>14:00 - Jam 2 Siang</option>
                                    <option value="15:00" {{ old('jam_mulai') == '15:00' ? 'selected' : '' }}>15:00 - Jam 3 Sore</option>
                                    <option value="16:00" {{ old('jam_mulai') == '16:00' ? 'selected' : '' }}>16:00 - Jam 4 Sore</option>
                                    <option value="17:00" {{ old('jam_mulai') == '17:00' ? 'selected' : '' }}>17:00 - Jam 5 Sore</option>
                                    <option value="18:00" {{ old('jam_mulai') == '18:00' ? 'selected' : '' }}>18:00 - Jam 6 Sore</option>
                                    <option value="19:00" {{ old('jam_mulai') == '19:00' ? 'selected' : '' }}>19:00 - Jam 7 Malam</option>
                                    <option value="20:00" {{ old('jam_mulai') == '20:00' ? 'selected' : '' }}>20:00 - Jam 8 Malam</option>
                                    <option value="21:00" {{ old('jam_mulai') == '21:00' ? 'selected' : '' }}>21:00 - Jam 9 Malam</option>
                                    <option value="22:00" {{ old('jam_mulai') == '22:00' ? 'selected' : '' }}>22:00 - Jam 10 Malam</option>
                                    <option value="23:00" {{ old('jam_mulai') == '23:00' ? 'selected' : '' }}>23:00 - Jam 11 Malam</option>
                                </select>
                                @error('jam_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                <select class="form-control @error('jam_selesai') is-invalid @enderror" 
                                        id="jam_selesai" name="jam_selesai" required>
                                    <option value="">-- Pilih Jam Selesai --</option>
                                    <option value="07:00" {{ old('jam_selesai') == '07:00' ? 'selected' : '' }}>07:00 - Jam 7 Pagi</option>
                                    <option value="08:00" {{ old('jam_selesai') == '08:00' ? 'selected' : '' }}>08:00 - Jam 8 Pagi</option>
                                    <option value="09:00" {{ old('jam_selesai') == '09:00' ? 'selected' : '' }}>09:00 - Jam 9 Pagi</option>
                                    <option value="10:00" {{ old('jam_selesai') == '10:00' ? 'selected' : '' }}>10:00 - Jam 10 Pagi</option>
                                    <option value="11:00" {{ old('jam_selesai') == '11:00' ? 'selected' : '' }}>11:00 - Jam 11 Pagi</option>
                                    <option value="12:00" {{ old('jam_selesai') == '12:00' ? 'selected' : '' }}>12:00 - Jam 12 Siang</option>
                                    <option value="13:00" {{ old('jam_selesai') == '13:00' ? 'selected' : '' }}>13:00 - Jam 1 Siang</option>
                                    <option value="14:00" {{ old('jam_selesai') == '14:00' ? 'selected' : '' }}>14:00 - Jam 2 Siang</option>
                                    <option value="15:00" {{ old('jam_selesai') == '15:00' ? 'selected' : '' }}>15:00 - Jam 3 Sore</option>
                                    <option value="16:00" {{ old('jam_selesai') == '16:00' ? 'selected' : '' }}>16:00 - Jam 4 Sore</option>
                                    <option value="17:00" {{ old('jam_selesai') == '17:00' ? 'selected' : '' }}>17:00 - Jam 5 Sore</option>
                                    <option value="18:00" {{ old('jam_selesai') == '18:00' ? 'selected' : '' }}>18:00 - Jam 6 Sore</option>
                                    <option value="19:00" {{ old('jam_selesai') == '19:00' ? 'selected' : '' }}>19:00 - Jam 7 Malam</option>
                                    <option value="20:00" {{ old('jam_selesai') == '20:00' ? 'selected' : '' }}>20:00 - Jam 8 Malam</option>
                                    <option value="21:00" {{ old('jam_selesai') == '21:00' ? 'selected' : '' }}>21:00 - Jam 9 Malam</option>
                                    <option value="22:00" {{ old('jam_selesai') == '22:00' ? 'selected' : '' }}>22:00 - Jam 10 Malam</option>
                                    <option value="23:00" {{ old('jam_selesai') == '23:00' ? 'selected' : '' }}>23:00 - Jam 11 Malam</option>
                                    <option value="00:00" {{ old('jam_selesai') == '00:00' ? 'selected' : '' }}>00:00 - Jam 12 Malam (Hari Berikutnya)</option>
                                </select>
                                @error('jam_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="total_harga" class="form-label">Total Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('total_harga') is-invalid @enderror" 
                                           id="total_harga" name="total_harga" value="{{ old('total_harga') }}" 
                                           min="0" step="1000" placeholder="0">
                                </div>
                                <small class="form-text text-muted">Akan dihitung otomatis berdasarkan jam dan lapangan</small>
                                @error('total_harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Dynamic Lapangan Details Display -->
                        <div class="row mb-4" id="lapangan-details" style="display: none;">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-info-circle"></i> Detail Lapangan
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fas fa-futbol"></i> Informasi Umum</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Nama:</strong></div>
                                            <div class="col-sm-8" id="detail-nama">-</div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Jenis:</strong></div>
                                            <div class="col-sm-8">
                                                <span class="badge bg-info" id="detail-jenis">-</span>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Lokasi:</strong></div>
                                            <div class="col-sm-8">
                                                <i class="fas fa-map-marker-alt text-danger"></i>
                                                <span id="detail-lokasi">-</span>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Status:</strong></div>
                                            <div class="col-sm-8">
                                                <span class="badge" id="detail-status">-</span>
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="row">
                                            <div class="col-sm-4"><strong>Harga:</strong></div>
                                            <div class="col-sm-8">
                                                <span class="text-success fw-bold" id="detail-harga">-</span>
                                                <small class="text-muted">/jam</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-tools"></i> Fasilitas & Deskripsi</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <strong>Fasilitas Tersedia:</strong>
                                            <div id="detail-fasilitas" class="mt-2">
                                                <span class="text-muted">Pilih lapangan untuk melihat fasilitas</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <div>
                                            <strong>Deskripsi:</strong>
                                            <p id="detail-deskripsi" class="mt-2 text-muted">
                                                Pilih lapangan untuk melihat deskripsi
                                            </p>
                                        </div>
                                        <div id="detail-gambar-container" class="mt-3" style="display: none;">
                                            <strong>Preview:</strong>
                                            <div class="mt-2">
                                                <img id="detail-gambar" class="img-thumbnail rounded" 
                                                     style="max-width: 200px; max-height: 150px; object-fit: cover;" 
                                                     alt="Gambar Lapangan">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Availability Display -->
                        <div class="row mb-4" id="schedule-availability" style="display: none;">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-clock"></i> Ketersediaan Jadwal
                                </h5>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-check-circle"></i> Jadwal Tersedia</h6>
                                    </div>
                                    <div class="card-body" id="available-slots">
                                        <p class="text-muted">Pilih lapangan dan tanggal untuk melihat jadwal tersedia</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-danger">
                                    <div class="card-header bg-danger text-white">
                                        <h6 class="mb-0"><i class="fas fa-times-circle"></i> Jadwal Sudah Dibooking</h6>
                                    </div>
                                    <div class="card-body" id="booked-slots">
                                        <p class="text-muted">Pilih lapangan dan tanggal untuk melihat jadwal yang sudah dibooking</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-info-circle"></i> Status & Informasi Tambahan
                                </h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status Booking <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payment_status" class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-control @error('payment_status') is-invalid @enderror" 
                                        id="payment_status" name="payment_status" required>
                                    <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" placeholder="Masukkan catatan tambahan jika diperlukan...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pemesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-info-circle"></i> Panduan Pemesanan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb"></i> Tips:</h6>
                        <ul class="mb-0 small">
                            <li>Pastikan data customer sudah benar</li>
                            <li>Pilih lapangan sesuai kebutuhan</li>
                            <li>Tanggal booking minimal hari ini</li>
                            <li>Total harga akan dihitung otomatis</li>
                            <li>Status bisa diubah setelah order dibuat</li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Perhatian:</h6>
                        <ul class="mb-0 small">
                            <li>Order yang sudah dibuat tidak bisa diubah datanya secara drastis</li>
                            <li>Hubungi customer sebelum mengubah status</li>
                            <li>Pastikan pembayaran sudah dikonfirmasi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
console.log('Script loaded!');

// Auto calculate total harga
function calculateTotal() {
    console.log('calculateTotal function called');
    
    const lapanganSelect = document.getElementById('lapangan_id');
    const jamMulai = document.getElementById('jam_mulai');
    const jamSelesai = document.getElementById('jam_selesai');
    const totalHarga = document.getElementById('total_harga');
    
    if (!lapanganSelect || !jamMulai || !jamSelesai || !totalHarga) {
        console.error('Some elements not found');
        return;
    }
    
    console.log('Lapangan:', lapanganSelect.value);
    console.log('Jam Mulai:', jamMulai.value);
    console.log('Jam Selesai:', jamSelesai.value);
    
    if (lapanganSelect.value && jamMulai.value && jamSelesai.value) {
        const selectedOption = lapanganSelect.options[lapanganSelect.selectedIndex];
        const hargaPerJam = parseInt(selectedOption.getAttribute('data-harga')) || 0;
        
        console.log('Harga per jam:', hargaPerJam);
        
        // Parse hours for simple calculation
        const mulaiHour = parseInt(jamMulai.value.split(':')[0]);
        let selesaiHour = parseInt(jamSelesai.value.split(':')[0]);
        
        // If selesai is 00:00 (midnight next day), treat as 24
        if (jamSelesai.value === '00:00') {
            selesaiHour = 24;
        }
        
        console.log('Mulai Hour:', mulaiHour);
        console.log('Selesai Hour:', selesaiHour);
        
        if (selesaiHour > mulaiHour) {
            const diffHours = selesaiHour - mulaiHour;
            const total = diffHours * hargaPerJam;
            totalHarga.value = total;
            
            console.log('Duration:', diffHours + ' hours');
            console.log('Total calculated:', total);
            
            // Visual feedback
            totalHarga.style.backgroundColor = '#d4edda';
            setTimeout(() => {
                totalHarga.style.backgroundColor = '';
            }, 1000);
        } else {
            totalHarga.value = '';
            console.warn('End time must be greater than start time');
        }
    } else {
        totalHarga.value = '';
        console.log('Missing values - calculation skipped');
    }
}

// Function to fetch and display available time slots
async function fetchAvailableTimeSlots() {
    console.log('fetchAvailableTimeSlots called');
    
    const lapanganSelect = document.getElementById('lapangan_id');
    const tanggalBooking = document.getElementById('tanggal_booking');
    const scheduleSection = document.getElementById('schedule-availability');
    const availableSlots = document.getElementById('available-slots');
    const bookedSlots = document.getElementById('booked-slots');
    
    console.log('Lapangan value:', lapanganSelect.value);
    console.log('Tanggal value:', tanggalBooking.value);
    
    if (!lapanganSelect.value || !tanggalBooking.value) {
        console.log('Missing lapangan or tanggal, hiding schedule section');
        scheduleSection.style.display = 'none';
        return;
    }
    
    console.log('Making API request...');
    
    try {
        // Show loading
        availableSlots.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat jadwal...</div>';
        bookedSlots.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat jadwal...</div>';
        scheduleSection.style.display = 'block';
        
        const apiUrl = `{{ route('api.available-time-slots') }}?lapangan_id=${lapanganSelect.value}&tanggal_booking=${tanggalBooking.value}`;
        console.log('API URL:', apiUrl);
        
        const response = await fetch(apiUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        const data = await response.json();
        
        console.log('API Response:', data); // Debug log
        
        if (response.ok && data.success) {
            // Display available slots
            if (data.available_slots && data.available_slots.length > 0) {
                availableSlots.innerHTML = data.available_slots.map(slot => 
                    `<span class="badge bg-success me-2 mb-2 p-2">${slot.time}</span>`
                ).join('');
            } else {
                availableSlots.innerHTML = '<p class="text-muted mb-0">Tidak ada jadwal tersedia</p>';
            }
            
            // Display booked slots
            if (data.booked_slots && data.booked_slots.length > 0) {
                bookedSlots.innerHTML = data.booked_slots.map(slot => 
                    `<div class="mb-2">
                        <span class="badge bg-danger me-2 p-2">${slot.time}</span>
                        <small class="text-muted">oleh ${slot.booked_by}</small>
                        ${slot.order_number ? `<br><small class="text-muted">Order: ${slot.order_number}</small>` : ''}
                    </div>`
                ).join('');
            } else {
                bookedSlots.innerHTML = '<p class="text-muted mb-0">Belum ada yang booking</p>';
            }
            
        } else {
            console.error('API Error:', data);
            const errorMsg = data.message || data.error || 'Gagal memuat jadwal';
            availableSlots.innerHTML = `<div class="alert alert-danger p-2 mb-0">
                <small><i class="fas fa-exclamation-triangle"></i> Error: ${errorMsg}</small>
            </div>`;
            bookedSlots.innerHTML = `<div class="alert alert-danger p-2 mb-0">
                <small><i class="fas fa-exclamation-triangle"></i> Error: ${errorMsg}</small>
            </div>`;
        }
        
    } catch (error) {
        console.error('Error fetching time slots:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack,
            lapangan_id: lapanganSelect.value,
            tanggal_booking: tanggalBooking.value
        });
        
        const errorMsg = 'Gagal terhubung ke server. Silakan coba lagi.';
        availableSlots.innerHTML = `<div class="alert alert-warning p-2 mb-0">
            <small><i class="fas fa-wifi"></i> ${errorMsg}</small>
            <br><small class="text-muted">Periksa console browser untuk detail teknis.</small>
        </div>`;
        bookedSlots.innerHTML = `<div class="alert alert-warning p-2 mb-0">
            <small><i class="fas fa-wifi"></i> ${errorMsg}</small>
        </div>`;
    }
}

// Function to validate selected time against booked slots
function validateSelectedTime() {
    const jamMulai = document.getElementById('jam_mulai');
    const jamSelesai = document.getElementById('jam_selesai');
    const lapanganSelect = document.getElementById('lapangan_id');
    const tanggalBooking = document.getElementById('tanggal_booking');
    
    if (!jamMulai.value || !jamSelesai.value || !lapanganSelect.value || !tanggalBooking.value) {
        return;
    }
    
    // Clear previous warnings
    jamMulai.classList.remove('is-invalid');
    jamSelesai.classList.remove('is-invalid');
    
    // Remove existing warning messages
    const existingWarning = document.getElementById('time-conflict-warning');
    if (existingWarning) {
        existingWarning.remove();
    }
    
    // Check against booked slots (this will be populated by fetchAvailableTimeSlots)
    const bookedSlotsContainer = document.getElementById('booked-slots');
    const bookedSlotElements = bookedSlotsContainer.querySelectorAll('.badge');
    
    for (let badge of bookedSlotElements) {
        const timeText = badge.textContent.trim();
        const [bookedStart, bookedEnd] = timeText.split(' - ');
        
        // Check for time overlap
        if (isTimeOverlap(jamMulai.value, jamSelesai.value, bookedStart, bookedEnd)) {
            // Show warning
            jamMulai.classList.add('is-invalid');
            jamSelesai.classList.add('is-invalid');
            
            const warningDiv = document.createElement('div');
            warningDiv.id = 'time-conflict-warning';
            warningDiv.className = 'alert alert-warning mt-3';
            warningDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Perhatian!</strong> Waktu yang Anda pilih (${jamMulai.value} - ${jamSelesai.value}) 
                bentrok dengan booking yang sudah ada (${timeText}). 
                Silakan pilih waktu lain.
            `;
            
            const scheduleSection = document.getElementById('schedule-availability');
            scheduleSection.appendChild(warningDiv);
            break;
        }
    }
}

// Helper function to check time overlap
function isTimeOverlap(startA, endA, startB, endB) {
    return (startA < endB) && (endA > startB);
}

// Function to display lapangan details dynamically
function displayLapanganDetails() {
    const lapanganSelect = document.getElementById('lapangan_id');
    const selectedOption = lapanganSelect.selectedOptions[0];
    const detailsContainer = document.getElementById('lapangan-details');
    
    if (!selectedOption || !selectedOption.value) {
        // Hide details container if no lapangan selected
        detailsContainer.style.display = 'none';
        return;
    }
    
    // Show details container
    detailsContainer.style.display = 'block';
    
    // Get data attributes
    const nama = selectedOption.getAttribute('data-nama') || '-';
    const jenis = selectedOption.getAttribute('data-jenis') || '-';
    const lokasi = selectedOption.getAttribute('data-lokasi') || 'Tidak tersedia';
    const fasilitas = selectedOption.getAttribute('data-fasilitas') || 'Tidak ada fasilitas';
    const deskripsi = selectedOption.getAttribute('data-deskripsi') || 'Tidak ada deskripsi';
    const status = selectedOption.getAttribute('data-status') || 'unknown';
    const harga = selectedOption.getAttribute('data-harga') || '0';
    const gambar = selectedOption.getAttribute('data-gambar') || '';
    
    // Update detail elements
    document.getElementById('detail-nama').textContent = nama;
    document.getElementById('detail-jenis').textContent = jenis.charAt(0).toUpperCase() + jenis.slice(1);
    document.getElementById('detail-lokasi').textContent = lokasi;
    
    // Update status badge with appropriate color
    const statusElement = document.getElementById('detail-status');
    statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    statusElement.className = 'badge ' + getStatusBadgeClass(status);
    
    // Format and display harga
    const hargaFormatted = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(parseFloat(harga));
    document.getElementById('detail-harga').textContent = hargaFormatted;
    
    // Display fasilitas as badges
    const fasilitasContainer = document.getElementById('detail-fasilitas');
    if (fasilitas && fasilitas !== 'Tidak ada fasilitas') {
        const fasilitasList = fasilitas.split(', ');
        fasilitasContainer.innerHTML = fasilitasList.map(f => 
            `<span class="badge bg-primary facility-badge me-1 mb-1">${f.trim()}</span>`
        ).join('');
    } else {
        fasilitasContainer.innerHTML = '<span class="text-muted">Tidak ada fasilitas tersedia</span>';
    }
    
    // Display deskripsi
    const deskripsiElement = document.getElementById('detail-deskripsi');
    deskripsiElement.textContent = deskripsi;
    deskripsiElement.className = deskripsi === 'Tidak ada deskripsi' ? 'mt-2 text-muted fst-italic' : 'mt-2';
    
    // Handle gambar
    const gambarContainer = document.getElementById('detail-gambar-container');
    const gambarElement = document.getElementById('detail-gambar');
    
    if (gambar && gambar.trim() !== '') {
        // Construct full image URL
        const imageUrl = `http://localhost:8001/storage/${gambar}`;
        gambarElement.src = imageUrl;
        gambarElement.onerror = function() {
            // If image fails to load, hide container
            gambarContainer.style.display = 'none';
        };
        gambarElement.onload = function() {
            // Show container when image loads successfully
            gambarContainer.style.display = 'block';
        };
    } else {
        gambarContainer.style.display = 'none';
    }
    
    // Add animation effect
    detailsContainer.classList.add('animate__animated', 'animate__fadeIn');
    setTimeout(() => {
        detailsContainer.classList.remove('animate__animated', 'animate__fadeIn');
    }, 1000);
}

// Helper function to get appropriate badge class for status
function getStatusBadgeClass(status) {
    switch(status.toLowerCase()) {
        case 'tersedia':
            return 'bg-success';
        case 'maintenance':
            return 'bg-warning text-dark';
        case 'tidak_tersedia':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}

// Event listeners setup
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Setting up event listeners');
    
    const lapanganSelect = document.getElementById('lapangan_id');
    const jamMulai = document.getElementById('jam_mulai');
    const jamSelesai = document.getElementById('jam_selesai');
    
    if (lapanganSelect) {
        console.log('Adding change listener to lapangan');
        lapanganSelect.addEventListener('change', function() {
            displayLapanganDetails();
            calculateTotal();
            fetchAvailableTimeSlots();
        });
    }
    
    if (jamMulai) {
        console.log('Adding change listener to jam_mulai');
        jamMulai.addEventListener('change', function() {
            calculateTotal();
            validateSelectedTime();
        });
    }
    
    if (jamSelesai) {
        console.log('Adding change listener to jam_selesai');
        jamSelesai.addEventListener('change', function() {
            calculateTotal();
            validateSelectedTime();
        });
    }
    
    // Add listener for tanggal_booking
    const tanggalBooking = document.getElementById('tanggal_booking');
    if (tanggalBooking) {
        console.log('Adding change listener to tanggal_booking');
        tanggalBooking.addEventListener('change', fetchAvailableTimeSlots);
    }
    
    // Test initial calculation
    console.log('Running initial calculation test...');
    calculateTotal();
    
    // Check if lapangan is already selected (for edit mode or validation errors)
    if (lapanganSelect && lapanganSelect.value) {
        displayLapanganDetails();
        fetchAvailableTimeSlots();
    }
});
</script>
@endsection
