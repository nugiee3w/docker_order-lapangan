@extends('layouts.app')

@section('title', 'Edit Pemesanan #' . $order->orde                                        @foreach($lapangan_list as $lapangan)
                                            <option value="{{ $lapangan['id'] }}" 
                                                    data-harga="{{ (float)$lapangan['harga_per_jam'] }}"
                                                    data-nama="{{ $lapangan['nama'] }}"
                                                    data-jenis="{{ $lapangan['jenis'] }}"
                                                    data-lokasi="{{ $lapangan['lokasi'] ?? 'Tidak tersedia' }}"
                                                    data-fasilitas="{{ is_array($lapangan['fasilitas']) ? implode(', ', $lapangan['fasilitas']) : $lapangan['fasilitas'] }}"
                                                    data-deskripsi="{{ $lapangan['deskripsi'] ?? 'Tidak ada deskripsi' }}"
                                                    data-status="{{ $lapangan['status'] }}"
                                                    data-gambar="{{ $lapangan['gambar'] ?? '' }}"
                                                    {{ old('lapangan_id', $order->lapangan_id) == $lapangan['id'] ? 'selected' : '' }}>
                                                {{ $lapangan['nama'] }} - {{ ucfirst($lapangan['jenis']) }} 
                                                (Rp {{ number_format((float)$lapangan['harga_per_jam'], 0, ',', '.') }}/jam)
                                            </option>
                                        @endforeachber)

@section('content')
<div class="container-fluid px-4">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit text-warning"></i> Edit Pemesanan #{{ $order->order_number }}
        </h1>
        <div>
            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> Detail Order
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-warning">Edit Data Pemesanan</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Customer Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-warning border-bottom pb-2">
                                    <i class="fas fa-user"></i> Informasi Customer
                                </h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_name" class="form-label">Nama Customer <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                       id="customer_name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                       id="customer_email" name="customer_email" value="{{ old('customer_email', $order->customer_email) }}" required>
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="customer_phone" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                       id="customer_phone" name="customer_phone" value="{{ old('customer_phone', $order->customer_phone) }}" required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Booking Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-warning border-bottom pb-2">
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
                                                    {{ old('lapangan_id', $order->lapangan_id) == $lapangan['id'] ? 'selected' : '' }}>
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
                                       id="tanggal_booking" name="tanggal_booking" 
                                       value="{{ old('tanggal_booking', $order->tanggal_booking->format('Y-m-d')) }}" required>
                                @error('tanggal_booking')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jam_mulai" class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('jam_mulai') is-invalid @enderror" 
                                       id="jam_mulai" name="jam_mulai" 
                                       value="{{ old('jam_mulai', $order->jam_mulai) }}" required>
                                @error('jam_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jam_selesai" class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('jam_selesai') is-invalid @enderror" 
                                       id="jam_selesai" name="jam_selesai" 
                                       value="{{ old('jam_selesai', $order->jam_selesai) }}" required>
                                @error('jam_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="total_harga" class="form-label">Total Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control @error('total_harga') is-invalid @enderror" 
                                           id="total_harga" name="total_harga" 
                                           value="{{ old('total_harga', $order->total_harga) }}" 
                                           min="0" step="1000">
                                </div>
                                @error('total_harga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-warning border-bottom pb-2">
                                    <i class="fas fa-info-circle"></i> Status & Informasi Tambahan
                                </h5>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status Booking <span class="text-danger">*</span></label>
                                <select class="form-control @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ old('status', $order->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="payment_status" class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                                <select class="form-control @error('payment_status') is-invalid @enderror" 
                                        id="payment_status" name="payment_status" required>
                                    <option value="unpaid" {{ old('payment_status', $order->payment_status) == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                                    <option value="paid" {{ old('payment_status', $order->payment_status) == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" name="notes" rows="3" 
                                          placeholder="Masukkan catatan tambahan jika diperlukan...">{{ old('notes', $order->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Pemesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-info">
                        <i class="fas fa-info-circle"></i> Informasi Order
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Order Number:</strong></td>
                            <td>{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat:</strong></td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Terakhir Update:</strong></td>
                            <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status Saat Ini:</strong></td>
                            <td>
                                @if($order->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($order->status == 'confirmed')
                                    <span class="badge bg-success">Confirmed</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status Bayar:</strong></td>
                            <td>
                                @if($order->payment_status == 'paid')
                                    <span class="badge bg-success">Sudah Dibayar</span>
                                @else
                                    <span class="badge bg-danger">Belum Dibayar</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-warning">
                        <i class="fas fa-exclamation-triangle"></i> Peringatan Edit
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning small">
                        <ul class="mb-0">
                            <li>Pastikan customer telah dihubungi sebelum mengubah data booking</li>
                            <li>Perubahan tanggal/jam bisa mempengaruhi ketersediaan lapangan</li>
                            <li>Update status pembayaran hanya jika pembayaran sudah dikonfirmasi</li>
                            <li>Simpan log perubahan di catatan tambahan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto calculate total harga
function calculateTotal() {
    const lapanganSelect = document.getElementById('lapangan_id');
    const jamMulai = document.getElementById('jam_mulai');
    const jamSelesai = document.getElementById('jam_selesai');
    const totalHarga = document.getElementById('total_harga');
    
    if (lapanganSelect.value && jamMulai.value && jamSelesai.value) {
        const hargaPerJam = parseInt(lapanganSelect.options[lapanganSelect.selectedIndex].dataset.harga) || 0;
        
        const mulai = new Date(`2000-01-01 ${jamMulai.value}`);
        const selesai = new Date(`2000-01-01 ${jamSelesai.value}`);
        
        if (selesai > mulai) {
            const diffHours = (selesai - mulai) / (1000 * 60 * 60);
            const total = Math.round(diffHours * hargaPerJam);
            totalHarga.value = total;
        }
    }
}

document.getElementById('lapangan_id').addEventListener('change', calculateTotal);
document.getElementById('jam_mulai').addEventListener('change', calculateTotal);
document.getElementById('jam_selesai').addEventListener('change', calculateTotal);
</script>
@endsection
