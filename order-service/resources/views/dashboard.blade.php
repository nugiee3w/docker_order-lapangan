@extends('layouts.app')

@section('title', 'Dashboard - Booking Lapangan')

@section('content')
<div class="container-fluid py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['total_orders'] ?? 0 }}</div>
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
                            <div class="h5 mb-0 fw-bold text-gray-800">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</div>
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
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['pending_orders'] ?? 0 }}</div>
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
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $stats['confirmed_orders'] ?? 0 }}</div>
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
                    <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
                </div>
                <div class="card-body">
                    @if(isset($recentOrders) && $recentOrders->count() > 0)
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
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $order->order_number }}</div>
                                            <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div>{{ $order->customer_name }}</div>
                                            <small class="text-muted">{{ $order->customer_email }}</small>
                                        </td>
                                        <td>
                                            @if(isset($order->lapangan_info))
                                                <div class="fw-bold">{{ $order->lapangan_info['nama'] }}</div>
                                                <small class="text-muted">{{ $order->lapangan_info['jenis'] }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($order->tanggal_booking)->format('d M Y') }}</td>
                                        <td>
                                            <span class="badge bg-info text-white">{{ $order->jam_mulai }} - {{ $order->jam_selesai }}</span>
                                        </td>
                                        <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                        <td>
                                            @if($order->status == 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($order->status == 'confirmed')
                                                <span class="badge bg-success">Confirmed</span>
                                            @elseif($order->status == 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($order->payment_status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Unpaid</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Belum ada pemesanan.</p>
                            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Pemesanan Pertama
                            </a>
                        </div>
                    @endif
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
                        <a href="{{ route('orders.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-2"></i>Semua Orders
                        </a>
                        <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="btn btn-warning">
                            <i class="fas fa-clock me-2"></i>Pending Orders ({{ $stats['pending_orders'] ?? 0 }})
                        </a>
                        <a href="{{ route('orders.index', ['status' => 'confirmed']) }}" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Confirmed Orders ({{ $stats['confirmed_orders'] ?? 0 }})
                        </a>
                        <a href="{{ route('orders.create') }}" class="btn btn-info">
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
                            <span class="fw-bold">{{ $stats['orders_today'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Revenue Hari Ini:</span>
                            <span class="fw-bold text-success">Rp {{ number_format($stats['revenue_today'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Orders Pending:</span>
                            <span class="fw-bold text-warning">{{ $stats['pending_orders'] ?? 0 }}</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Orders Confirmed:</span>
                            <span class="fw-bold text-success">{{ $stats['confirmed_orders'] ?? 0 }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="bg-success rounded-circle me-2" style="width: 10px; height: 10px;"></div>
                            <span class="text-success">Sistem Online</span>
                        </div>
                        <small class="text-muted">
                            Last Update: {{ now()->format('d M Y H:i') }}
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
@endsection
