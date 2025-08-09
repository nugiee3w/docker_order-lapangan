@extends('layouts.app')

@section('title', 'Detail Order #' . $order->order_number . ' - Order Management')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    Detail Order #{{ $order->order_number }}
                </h1>
                <p class="text-muted">{{ $order->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
            <div>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
                <button class="btn btn-primary" onclick="printOrder()">
                    <i class="fas fa-print me-1"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Order Status & Actions -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Status Order
                </h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <span class="status-badge-large 
                        @if($order->status == 'pending') bg-warning
                        @elseif($order->status == 'confirmed') bg-success
                        @elseif($order->status == 'cancelled') bg-danger
                        @else bg-secondary
                        @endif
                    ">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                
                <div class="mb-3">
                    <span class="status-badge-large {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                        Payment: {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
                
                @if($order->status == 'pending')
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="confirmOrder({{ $order->id }})">
                            <i class="fas fa-check me-1"></i>Konfirmasi Order
                        </button>
                        <button class="btn btn-danger" onclick="cancelOrder({{ $order->id }})">
                            <i class="fas fa-times me-1"></i>Batalkan Order
                        </button>
                    </div>
                @endif
                
                @if($order->payment_status == 'unpaid' && $order->status == 'confirmed')
                    <div class="d-grid mt-2">
                        <button class="btn btn-info" onclick="markAsPaid({{ $order->id }})">
                            <i class="fas fa-credit-card me-1"></i>Tandai Sudah Bayar
                        </button>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Info -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Quick Info
                </h5>
            </div>
            <div class="card-body">
                <div class="info-item">
                    <strong>Order ID:</strong>
                    <span>{{ $order->id }}</span>
                </div>
                <div class="info-item">
                    <strong>Dibuat:</strong>
                    <span>{{ $order->created_at->diffForHumans() }}</span>
                </div>
                <div class="info-item">
                    <strong>Update Terakhir:</strong>
                    <span>{{ $order->updated_at->diffForHumans() }}</span>
                </div>
                <div class="info-item">
                    <strong>Total Harga:</strong>
                    <span class="text-success fw-bold">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Details -->
    <div class="col-md-8">
        <!-- Customer Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Informasi Customer
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Nama:</strong>
                            <span>{{ $order->customer_name }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong>
                            <span>{{ $order->customer_email }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Telepon:</strong>
                            <span>{{ $order->customer_phone }}</span>
                        </div>
                        @if($order->customer_id)
                        <div class="info-item">
                            <strong>Customer ID:</strong>
                            <span>{{ $order->customer_id }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Booking Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Informasi Booking
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Lapangan ID:</strong>
                            <span>{{ $order->lapangan_id }}</span>
                        </div>
                        @if($order->lapangan_info)
                        <div class="info-item">
                            <strong>Nama Lapangan:</strong>
                            <span>{{ $order->lapangan_info['nama'] ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Jenis:</strong>
                            <span class="badge bg-info">{{ $order->lapangan_info['jenis'] ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Lokasi:</strong>
                            <span>{{ $order->lapangan_info['lokasi'] ?? 'N/A' }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Tanggal Booking:</strong>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($order->tanggal_booking)->format('d F Y') }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Jam Main:</strong>
                            <span class="fw-bold text-primary">{{ $order->jam_mulai }} - {{ $order->jam_selesai }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Durasi:</strong>
                            <span>{{ \Carbon\Carbon::parse($order->jam_mulai)->diffInHours(\Carbon\Carbon::parse($order->jam_selesai)) }} jam</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Payment Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    Informasi Pembayaran
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Total Harga:</strong>
                            <span class="text-success fw-bold fs-5">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                        </div>
                        <div class="info-item">
                            <strong>Status Payment:</strong>
                            <span class="status-badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if($order->payment_status == 'paid')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Pembayaran telah dikonfirmasi
                        </div>
                        @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Menunggu pembayaran
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Notes -->
        @if($order->notes)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-sticky-note me-2"></i>
                    Catatan
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $order->notes }}</p>
            </div>
        </div>
        @endif
        
        <!-- API Information -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-code me-2"></i>
                    API Information
                </h5>
            </div>
            <div class="card-body">
                <div class="bg-light p-3 rounded">
                    <h6>Endpoints untuk Order ini:</h6>
                    <div class="mb-2">
                        <strong>GET:</strong> <code>/api/orders/{{ $order->id }}</code>
                    </div>
                    <div class="mb-2">
                        <strong>PUT:</strong> <code>/api/orders/{{ $order->id }}</code>
                    </div>
                    <div class="mb-2">
                        <strong>DELETE:</strong> <code>/api/orders/{{ $order->id }}</code>
                    </div>
                </div>
                
                <button class="btn btn-outline-info btn-sm mt-2" onclick="showFullApiDocs()">
                    <i class="fas fa-book me-1"></i>Lihat Dokumentasi Lengkap
                </button>
            </div>
        </div>
    </div>
</div>

<!-- API Documentation Modal -->
<div class="modal fade" id="apiDocsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">API Documentation - Order {{ $order->order_number }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Get Order Detail</h6>
                        <div class="bg-dark text-white p-3 rounded mb-3">
                            <pre>curl -X GET "http://localhost:8002/api/orders/{{ $order->id }}" \
-H "Authorization: Bearer YOUR_TOKEN" \
-H "Accept: application/json"</pre>
                        </div>
                        
                        <h6>Update Order Status</h6>
                        <div class="bg-dark text-white p-3 rounded mb-3">
                            <pre>curl -X PUT "http://localhost:8002/api/orders/{{ $order->id }}" \
-H "Authorization: Bearer YOUR_TOKEN" \
-H "Content-Type: application/json" \
-d '{
  "status": "confirmed",
  "payment_status": "paid",
  "notes": "Order dikonfirmasi"
}'</pre>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Response Example</h6>
                        <div class="bg-light p-3 rounded mb-3">
                            <pre>{
  "success": true,
  "data": {
    "id": {{ $order->id }},
    "order_number": "{{ $order->order_number }}",
    "customer_name": "{{ $order->customer_name }}",
    "lapangan_id": {{ $order->lapangan_id }},
    "tanggal_booking": "{{ $order->tanggal_booking }}",
    "jam_mulai": "{{ $order->jam_mulai }}",
    "jam_selesai": "{{ $order->jam_selesai }}",
    "total_harga": {{ $order->total_harga }},
    "status": "{{ $order->status }}",
    "payment_status": "{{ $order->payment_status }}"
  }
}</pre>
                        </div>
                        
                        <h6>Status Values</h6>
                        <ul>
                            <li><strong>Order Status:</strong> pending, confirmed, cancelled</li>
                            <li><strong>Payment Status:</strong> unpaid, paid</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin mengkonfirmasi order ini?')) {
        // In real implementation, this would call the API
        alert('Order akan dikonfirmasi. Implementasi API: PUT /api/orders/' + orderId + ' dengan status "confirmed"');
        // location.reload(); // Refresh page after API call
    }
}

function cancelOrder(orderId) {
    const reason = prompt('Masukkan alasan pembatalan (opsional):');
    if (confirm('Apakah Anda yakin ingin membatalkan order ini?')) {
        // In real implementation, this would call the API
        alert('Order akan dibatalkan. Implementasi API: PUT /api/orders/' + orderId + ' dengan status "cancelled"');
        // location.reload(); // Refresh page after API call
    }
}

function markAsPaid(orderId) {
    if (confirm('Apakah Anda yakin pembayaran sudah diterima?')) {
        // In real implementation, this would call the API
        alert('Status pembayaran akan diubah. Implementasi API: PUT /api/orders/' + orderId + ' dengan payment_status "paid"');
        // location.reload(); // Refresh page after API call
    }
}

function showFullApiDocs() {
    new bootstrap.Modal(document.getElementById('apiDocsModal')).show();
}

function printOrder() {
    // Create a print-friendly version
    const printContent = `
        <div style="padding: 20px; font-family: Arial, sans-serif;">
            <h2>Order #{{ $order->order_number }}</h2>
            <p><strong>Tanggal:</strong> {{ $order->created_at->format('d F Y, H:i') }}</p>
            
            <h3>Customer Information</h3>
            <p><strong>Nama:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->customer_email }}</p>
            <p><strong>Telepon:</strong> {{ $order->customer_phone }}</p>
            
            <h3>Booking Details</h3>
            <p><strong>Lapangan:</strong> {{ $order->lapangan_info['nama'] ?? 'N/A' }} ({{ $order->lapangan_info['jenis'] ?? 'N/A' }})</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($order->tanggal_booking)->format('d F Y') }}</p>
            <p><strong>Waktu:</strong> {{ $order->jam_mulai }} - {{ $order->jam_selesai }}</p>
            <p><strong>Total Harga:</strong> Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
            <p><strong>Payment:</strong> {{ ucfirst($order->payment_status) }}</p>
            
            @if($order->notes)
            <h3>Catatan</h3>
            <p>{{ $order->notes }}</p>
            @endif
        </div>
    `;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}
</script>

<style>
.status-badge-large {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 20px;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.info-item {
    margin-bottom: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item strong {
    color: #333;
    min-width: 120px;
}

@media print {
    .btn, .card-header, nav, .sidebar, .modal {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endsection
