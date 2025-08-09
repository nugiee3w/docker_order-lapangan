@extends('layouts.app')

@section('title', 'Semua Orders - Order Management')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Manajemen Pemesanan Lapangan
                </h1>
                <p class="text-muted">Kelola dan pantau semua pesanan booking lapangan</p>
            </div>
            <div>
                <a href="{{ route('orders.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Tambah Pemesanan Baru
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>
            Filter & Search
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('orders.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status Order</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="payment_status" class="form-label">Status Payment</label>
                    <select name="payment_status" id="payment_status" class="form-select">
                        <option value="">Semua Payment</option>
                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="date_from" class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label for="date_to" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-1"></i>Reset
                    </a>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Search Customer / Order Number</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Cari nama customer, email, atau order number..." 
                           value="{{ request('search') }}">
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-table me-2"></i>
            Orders List ({{ $orders->total() }} total)
        </h5>
        <div>
            <button class="btn btn-success btn-sm" onclick="exportOrders()">
                <i class="fas fa-download me-1"></i>Export
            </button>
            <button class="btn btn-info btn-sm" onclick="refreshData()">
                <i class="fas fa-sync-alt me-1"></i>Refresh
            </button>
        </div>
    </div>
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-custom" id="ordersTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Order Info</th>
                            <th>Customer</th>
                            <th>Lapangan & Jadwal</th>
                            <th>Booking Details</th>
                            <th>Financial</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong class="text-primary">{{ $order->order_number }}</strong>
                                <br><small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                <br><span class="badge bg-secondary">ID: {{ $order->id }}</span>
                            </td>
                            <td>
                                <strong>{{ $order->customer_name }}</strong>
                                <br><small class="text-muted">{{ $order->customer_email }}</small>
                                <br><small class="text-muted">{{ $order->customer_phone }}</small>
                            </td>
                            <td>
                                <strong>{{ $order->lapangan_info['nama'] ?? 'N/A' }}</strong>
                                <br><span class="badge bg-info">{{ $order->lapangan_info['jenis'] ?? 'N/A' }}</span>
                                <br><small class="text-muted">{{ $order->lapangan_info['lokasi'] ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($order->tanggal_booking)->format('d M Y') }}</strong>
                                <br><span class="fw-bold text-primary">{{ $order->jam_mulai }} - {{ $order->jam_selesai }}</span>
                                <br><small class="text-muted">Durasi: {{ \Carbon\Carbon::parse($order->jam_mulai)->diffInHours(\Carbon\Carbon::parse($order->jam_selesai)) }} jam</small>
                            </td>
                            <td>
                                <strong class="text-success">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                                <br><span class="status-badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge 
                                    @if($order->status == 'pending') bg-warning
                                    @elseif($order->status == 'confirmed') bg-success
                                    @elseif($order->status == 'cancelled') bg-danger
                                    @else bg-secondary
                                    @endif
                                ">
                                    {{ ucfirst($order->status) }}
                                </span>
                                @if($order->notes)
                                    <br><small class="text-muted">{{ Str::limit($order->notes, 30) }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group-vertical" role="group">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-info btn-action btn-sm" title="Detail Order">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-outline-warning btn-action btn-sm" title="Edit Order">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @if($order->status == 'cancelled')
                                        <!-- Order sudah dibatalkan -->
                                        <button type="button" class="btn btn-outline-secondary btn-action btn-sm" disabled 
                                                title="Order sudah dibatalkan - Tidak dapat dihapus" 
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                            <i class="fas fa-ban"></i> Dibatalkan
                                        </button>
                                    @elseif($order->status == 'confirmed' && $order->payment_status == 'paid')
                                        <!-- Order sudah dikonfirmasi dan dibayar -->
                                        <button type="button" class="btn btn-outline-secondary btn-action btn-sm" disabled 
                                                title="Tidak dapat dihapus - Order sudah dikonfirmasi dan dibayar. Silakan batalkan order terlebih dahulu." 
                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                            <i class="fas fa-lock"></i> Terkunci
                                        </button>
                                    @else
                                        <!-- Order dapat dihapus -->
                                        <button type="button" class="btn btn-outline-danger btn-action btn-sm" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal{{ $order->id }}" 
                                                title="Hapus Order - {{ $order->order_number }}">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    @endif
                                    
                                    <!-- Quick Actions -->
                                    @if($order->status == 'pending')
                                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin mengkonfirmasi order {{ $order->order_number }}?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="confirmed">
                                        <button type="submit" class="btn btn-outline-success btn-action btn-sm" title="Konfirmasi Order">
                                            <i class="fas fa-check"></i> Konfirmasi
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($order->payment_status == 'unpaid')
                                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menandai order {{ $order->order_number }} sebagai sudah dibayar?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="payment_status" value="paid">
                                        <button type="submit" class="btn btn-outline-primary btn-action btn-sm" title="Tandai Sudah Dibayar">
                                            <i class="fas fa-dollar-sign"></i> Bayar
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($order->status == 'confirmed' && $order->payment_status == 'paid')
                                    <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menandai order {{ $order->order_number }} sebagai selesai?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-outline-info btn-action btn-sm" title="Tandai Selesai">
                                            <i class="fas fa-flag-checkered"></i> Selesai
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Tidak ada orders yang ditemukan</h5>
                <p class="text-muted">Coba ubah filter atau kriteria pencarian Anda.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable for better searching and sorting
    $('#ordersTable').DataTable({
        "paging": false,
        "searching": false,
        "info": false,
        "ordering": true,
        "order": [[ 0, "desc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [6] }
        ]
    });
    
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Debug: Check if modal buttons are working
    $('[data-bs-toggle="modal"]').on('click', function() {
        console.log('Modal trigger clicked:', $(this).attr('data-bs-target'));
    });
    
    // Handle all form submissions for debugging
    $('form').on('submit', function(e) {
        var form = $(this);
        var action = form.attr('action');
        var method = form.find('input[name="_method"]').val() || 'POST';
        
        console.log('Form submitting:', {
            action: action,
            method: method,
            data: form.serialize()
        });
        
        // Handle DELETE requests
        if (form.find('input[name="_method"][value="DELETE"]').length > 0) {
            console.log('Delete form submitting...');
            if (!confirm('Apakah Anda benar-benar yakin ingin menghapus order ini? Tindakan ini tidak dapat dibatalkan!')) {
                e.preventDefault();
                return false;
            }
        }
        
        // Handle PATCH requests for status updates
        if (form.find('input[name="_method"][value="PATCH"]').length > 0) {
            console.log('Status update form submitting...');
            var statusField = form.find('input[name="status"]').val();
            var paymentField = form.find('input[name="payment_status"]').val();
            
            if (statusField) {
                console.log('Updating status to:', statusField);
            }
            if (paymentField) {
                console.log('Updating payment status to:', paymentField);
            }
        }
    });
});

function confirmDelete(orderNumber) {
    return confirm(`Konfirmasi terakhir: Hapus order ${orderNumber}?\n\nTindakan ini TIDAK DAPAT DIBATALKAN!`);
}

function confirmOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin mengkonfirmasi order ini?')) {
        // In real implementation, this would call the API
        alert('Fitur ini akan terhubung ke API untuk mengkonfirmasi order ID: ' + orderId);
        // Example API call:
        // PUT /api/orders/{orderId} with {"status": "confirmed", "payment_status": "paid"}
    }
}

function cancelOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin membatalkan order ini?')) {
        // In real implementation, this would call the API
        alert('Fitur ini akan terhubung ke API untuk membatalkan order ID: ' + orderId);
        // Example API call:
        // DELETE /api/orders/{orderId}
    }
}

function exportOrders() {
    alert('Fitur export akan mengunduh data orders dalam format CSV/Excel');
    // Implementation for export functionality
}

function refreshData() {
    location.reload();
}
</script>

<!-- Delete Confirmation Modals -->
@foreach($orders as $order)
@if($order->status != 'cancelled' && !($order->status == 'confirmed' && $order->payment_status == 'paid'))
<div class="modal fade" id="deleteModal{{ $order->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel{{ $order->id }}">
                    <i class="fas fa-exclamation-triangle"></i> Konfirmasi Hapus Order
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <h6>Apakah Anda yakin ingin menghapus order ini?</h6>
                </div>
                
                <div class="alert alert-info">
                    <strong>Detail Order:</strong><br>
                    <small>
                        <i class="fas fa-hashtag"></i> Order Number: <strong>{{ $order->order_number }}</strong><br>
                        <i class="fas fa-user"></i> Customer: {{ $order->customer_name }}<br>
                        <i class="fas fa-envelope"></i> Email: {{ $order->customer_email }}<br>
                        <i class="fas fa-building"></i> Lapangan: {{ $order->lapangan_info['nama'] ?? 'N/A' }}<br>
                        <i class="fas fa-calendar"></i> Tanggal: {{ \Carbon\Carbon::parse($order->tanggal_booking)->format('d M Y') }}<br>
                        <i class="fas fa-clock"></i> Waktu: {{ $order->jam_mulai }} - {{ $order->jam_selesai }}<br>
                        <i class="fas fa-money-bill"></i> Total: Rp {{ number_format($order->total_harga, 0, ',', '.') }}<br>
                        <i class="fas fa-info-circle"></i> Status: {{ ucfirst($order->status) }} / {{ ucfirst($order->payment_status) }}
                    </small>
                </div>
                
                @if($order->status == 'confirmed' || $order->payment_status == 'paid')
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Perhatian:</strong> Order ini sudah dikonfirmasi dan/atau dibayar. Pastikan Anda memiliki alasan yang kuat untuk menghapusnya.
                </div>
                @endif
                
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Peringatan:</strong> Tindakan ini akan menghapus order secara permanen dan tidak dapat dibatalkan!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display: inline;" id="deleteForm{{ $order->id }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirmDelete('{{ $order->order_number }}')">
                        <i class="fas fa-trash"></i> Ya, Hapus Order
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection

@section('styles')
<style>
    /* Improved text contrast for better readability */
    .table td {
        background-color: #ffffff !important;
    }
    
    .table tbody tr:hover td {
        background-color: #f8f9fa !important;
    }
    
    /* Enhanced badge styling for better contrast */
    .badge.bg-info {
        background-color: #0dcaf0 !important;
        color: #000 !important;
        font-weight: 600;
    }
    
    .badge.bg-success {
        background-color: #198754 !important;
        color: #fff !important;
        font-weight: 600;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
        font-weight: 600;
    }
    
    .badge.bg-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
        font-weight: 600;
    }
    
    .badge.bg-secondary {
        background-color: #6c757d !important;
        color: #fff !important;
        font-weight: 600;
    }
    
    /* Enhanced status badge styling */
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-badge.bg-success {
        background-color: #198754 !important;
        color: #fff !important;
        border: 1px solid #146c43;
    }
    
    .status-badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
        border: 1px solid #ffcd39;
    }
    
    .status-badge.bg-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
        border: 1px solid #b02a37;
    }
    
    /* Text color improvements */
    .text-primary {
        color: #0d6efd !important;
        font-weight: 600;
    }
    
    .text-success {
        color: #198754 !important;
        font-weight: 600;
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    /* Table header improvements */
    .table-dark th {
        background-color: #212529 !important;
        color: #fff !important;
        border-color: #32383e !important;
    }
    
    /* Action button improvements */
    .btn-info {
        background-color: #0dcaf0 !important;
        border-color: #0dcaf0 !important;
        color: #000 !important;
        font-weight: 600;
    }
    
    .btn-warning {
        background-color: #ffc107 !important;
        border-color: #ffc107 !important;
        color: #000 !important;
        font-weight: 600;
    }
    
    .btn-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #fff !important;
        font-weight: 600;
    }
    
    /* Disabled button styling */
    .btn-outline-secondary:disabled {
        background-color: #f8f9fa !important;
        border-color: #dee2e6 !important;
        color: #6c757d !important;
        opacity: 0.8;
        cursor: not-allowed;
    }
    
    .btn-outline-secondary:disabled:hover {
        background-color: #f8f9fa !important;
        border-color: #dee2e6 !important;
        color: #6c757d !important;
    }
    
    /* Enhance lapangan info text contrast */
    .table td strong {
        color: #212529 !important;
        font-weight: 700;
    }
    
    /* Better small text readability */
    .table td small {
        color: #495057 !important;
        font-size: 0.85rem;
    }
    
    /* Improve order number readability */
    .text-primary.fw-bold {
        color: #0d6efd !important;
        font-weight: 700 !important;
    }
    
    /* Better financial amount visibility */
    .text-success {
        color: #198754 !important;
        font-weight: 700;
    }
</style>
@endsection
