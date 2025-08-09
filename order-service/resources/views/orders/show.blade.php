@extends('layouts.app')

@section('title', 'Detail Pemesanan #' . $order->order_number)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt text-info"></i> Detail Pemesanan #{{ $order->order_number }}
        </h1>
        <div>
            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Order
            </a>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user"></i> Informasi Customer
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Nama Lengkap:</strong></td>
                                    <td>{{ $order->customer_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>
                                        <a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>No. Telepon:</strong></td>
                                    <td>
                                        <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center">
                                <div class="btn-group-vertical" role="group">
                                    <a href="mailto:{{ $order->customer_email }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-envelope"></i> Kirim Email
                                    </a>
                                    <a href="tel:{{ $order->customer_phone }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-phone"></i> Telepon Customer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-calendar-alt"></i> Detail Booking Lapangan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Lapangan:</strong></td>
                                    <td>
                                        <div class="font-weight-bold text-primary">{{ $order->lapangan_info['nama'] ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $order->lapangan_info['jenis'] ?? 'N/A' }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Lokasi:</strong></td>
                                    <td>{{ $order->lapangan_info['lokasi'] ?? 'N/A' }}</td>
                                </tr>
                                @if(isset($order->lapangan_info['status']))
                                <tr>
                                    <td><strong>Status Lapangan:</strong></td>
                                    <td>
                                        <span class="badge badge-success">{{ $order->lapangan_info['status'] }}</span>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Tanggal:</strong></td>
                                    <td>
                                        <span class="font-weight-bold">
                                            {{ $order->tanggal_booking->format('d/m/Y') }}
                                        </span>
                                        <small class="text-muted">
                                            ({{ $order->tanggal_booking->translatedFormat('l') }})
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Waktu:</strong></td>
                                    <td>
                                        <span class="badge badge-info badge-pill">
                                            {{ $order->jam_mulai }} - {{ $order->jam_selesai }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Harga per Jam:</strong></td>
                                    <td>Rp {{ number_format($order->lapangan_info['harga_per_jam'] ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Durasi:</strong></td>
                                    <td>
                                        @php
                                            $jamMulai = \Carbon\Carbon::parse($order->jam_mulai);
                                            $jamSelesai = \Carbon\Carbon::parse($order->jam_selesai);
                                            $duration = $jamSelesai->diffInHours($jamMulai);
                                        @endphp
                                        {{ $duration }} jam
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Total Harga:</strong></td>
                                    <td>
                                        <span class="h5 text-success font-weight-bold">
                                            Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @if(!empty($order->lapangan_info['fasilitas']))
                                <tr>
                                    <td><strong>Fasilitas:</strong></td>
                                    <td>
                                        @foreach($order->lapangan_info['fasilitas'] as $fasilitas)
                                            <span class="badge badge-secondary badge-sm">{{ $fasilitas }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($order->notes)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-sticky-note"></i> Catatan Tambahan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-light">
                        {{ $order->notes }}
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle"></i> Status & Informasi Order
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Order Number:</strong></td>
                            <td>{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status Booking:</strong></td>
                            <td>
                                @if($order->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($order->status == 'confirmed')
                                    <span class="badge badge-success">Confirmed</span>
                                @else
                                    <span class="badge badge-danger">Cancelled</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status Pembayaran:</strong></td>
                            <td>
                                @if($order->payment_status == 'paid')
                                    <span class="badge badge-success">Sudah Dibayar</span>
                                @else
                                    <span class="badge badge-danger">Belum Dibayar</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat:</strong></td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Terakhir Update:</strong></td>
                            <td>{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Quick Status Update -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Update Status Cepat
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group">
                            <label for="status" class="form-label">Status Booking:</label>
                            <select class="form-control form-control-sm" id="status" name="status">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="payment_status" class="form-label">Status Pembayaran:</label>
                            <select class="form-control form-control-sm" id="payment_status" name="payment_status">
                                <option value="unpaid" {{ $order->payment_status == 'unpaid' ? 'selected' : '' }}>Belum Dibayar</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes" class="form-label">Tambah Catatan:</label>
                            <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2" 
                                      placeholder="Catatan perubahan status..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-cogs"></i> Aksi Order
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit Lengkap
                        </a>
                        
                        @if($order->status != 'cancelled')
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Hapus Order
                        </button>
                        @endif
                        
                        <a href="{{ route('orders.create') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> Order Baru
                        </a>
                        
                        <a href="mailto:{{ $order->customer_email }}?subject=Konfirmasi Booking {{ $order->order_number }}" 
                           class="btn btn-info btn-sm">
                            <i class="fas fa-envelope"></i> Email Customer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger"></i> Konfirmasi Hapus Order
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus order <strong>#{{ $order->order_number }}</strong>?</p>
                <div class="alert alert-warning">
                    <small>
                        <i class="fas fa-info-circle"></i> 
                        Tindakan ini tidak dapat dibatalkan. Pastikan customer telah dihubungi terlebih dahulu.
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Ya, Hapus Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Improved badge contrast for detail page */
    .badge {
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    
    .badge-success {
        background-color: #198754 !important;
        color: #fff !important;
        border: 1px solid #146c43;
    }
    
    .badge-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
        border: 1px solid #ffcd39;
    }
    
    .badge-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
        border: 1px solid #b02a37;
    }
    
    .badge-info {
        background-color: #0dcaf0 !important;
        color: #000 !important;
        border: 1px solid #3dd5f3;
        font-weight: 700;
    }
    
    .badge-secondary {
        background-color: #6c757d !important;
        color: #fff !important;
        border: 1px solid #565e64;
    }
    
    /* Better card content readability */
    .card-body {
        background-color: #ffffff !important;
    }
    
    .table-borderless td {
        color: #212529 !important;
    }
    
    .table-borderless strong {
        color: #000 !important;
        font-weight: 700;
    }
    
    /* Enhanced text colors */
    .text-success {
        color: #198754 !important;
        font-weight: 700;
    }
    
    .text-primary {
        color: #0d6efd !important;
        font-weight: 600;
    }
    
    .text-gray-800 {
        color: #212529 !important;
        font-weight: 600;
    }
    
    /* Button improvements */
    .btn-warning {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #000 !important;
        font-weight: 600;
    }
    
    .btn-secondary {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        font-weight: 600;
    }
    
    .btn-primary {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #fff !important;
        font-weight: 600;
    }
    
    .btn-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #fff !important;
        font-weight: 600;
    }
    
    /* Alert improvements */
    .alert-success {
        background-color: #d1e7dd !important;
        border-color: #badbcc !important;
        color: #0f5132 !important;
    }
    
    /* Card header improvements */
    .card-header {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6 !important;
    }
    
    .font-weight-bold {
        font-weight: 700 !important;
    }
    
    /* Icon color improvements */
    .text-info {
        color: #0dcaf0 !important;
    }
    
    /* Form control improvements */
    .form-control {
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        color: #212529 !important;
    }
    
    .form-control:focus {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
    }
    
    /* Select styling */
    .form-select {
        background-color: #fff !important;
        border: 1px solid #ced4da !important;
        color: #212529 !important;
    }
</style>
@endsection
