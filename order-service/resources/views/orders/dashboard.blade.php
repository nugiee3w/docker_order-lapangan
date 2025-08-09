@extends('layouts.app')

@section('title', 'Admin Dashboard - Order Management')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="mb-0">
            <i class="fas fa-tachometer-alt me-2"></i>
            Dashboard Order Management
        </h1>
        <p class="text-muted">Kelola semua pesanan booking lapangan dengan mudah</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card confirmed-card">
            <div class="card-body text-center">
                <i class="fas fa-shopping-cart stats-icon mb-3"></i>
                <h2 class="mb-2">{{ $totalOrders }}</h2>
                <p class="mb-0">Total Orders</p>
                <small class="opacity-75">Semua waktu</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card revenue-card">
            <div class="card-body text-center">
                <i class="fas fa-money-bill-wave stats-icon mb-3"></i>
                <h2 class="mb-2">{{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                <p class="mb-0">Total Revenue</p>
                <small class="opacity-75">Rp (Confirmed)</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card pending-card">
            <div class="card-body text-center">
                <i class="fas fa-clock stats-icon mb-3"></i>
                <h2 class="mb-2">{{ $pendingOrders }}</h2>
                <p class="mb-0">Pending Orders</p>
                <small class="opacity-75">Perlu konfirmasi</small>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stats-card cancelled-card">
            <div class="card-body text-center">
                <i class="fas fa-calendar-day stats-icon mb-3"></i>
                <h2 class="mb-2">{{ $ordersToday }}</h2>
                <p class="mb-0">Orders Hari Ini</p>
                <small class="opacity-75">{{ now()->format('d M Y') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Status Overview -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Status Overview
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="border rounded p-3 mb-3 bg-warning bg-opacity-10">
                            <h3 class="text-warning">{{ $pendingOrders }}</h3>
                            <span class="text-muted">Pending</span>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-warning" style="width: {{ $totalOrders > 0 ? ($pendingOrders/$totalOrders)*100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 mb-3 bg-success bg-opacity-10">
                            <h3 class="text-success">{{ $confirmedOrders }}</h3>
                            <span class="text-muted">Confirmed</span>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-success" style="width: {{ $totalOrders > 0 ? ($confirmedOrders/$totalOrders)*100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 mb-3 bg-danger bg-opacity-10">
                            <h3 class="text-danger">{{ $cancelledOrders }}</h3>
                            <span class="text-muted">Cancelled</span>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-danger" style="width: {{ $totalOrders > 0 ? ($cancelledOrders/$totalOrders)*100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card revenue-card">
            <div class="card-header border-0">
                <h5 class="mb-0 text-white">
                    <i class="fas fa-chart-line me-2"></i>
                    Revenue Hari Ini
                </h5>
            </div>
            <div class="card-body">
                <h2 class="text-white mb-0">Rp {{ number_format($revenueToday, 0, ',', '.') }}</h2>
                <small class="opacity-75">{{ now()->format('d F Y') }}</small>
                
                <div class="mt-3">
                    <div class="d-flex justify-content-between">
                        <span class="opacity-75">Orders Hari Ini:</span>
                        <strong>{{ $ordersToday }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="opacity-75">Rata-rata per Order:</span>
                        <strong>Rp {{ $ordersToday > 0 ? number_format($revenueToday/$ordersToday, 0, ',', '.') : '0' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="btn btn-warning btn-lg w-100">
                            <i class="fas fa-clock me-2"></i>
                            Pending Orders ({{ $pendingOrders }})
                        </a>
                    </div>
                    <div class="col-md-6 mb-2">
                        <a href="{{ route('orders.index') }}" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-list me-2"></i>
                            Semua Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Recent Orders (10 Terbaru)
                </h5>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye me-1"></i>Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
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
                                        <strong class="text-primary">{{ $order->order_number }}</strong>
                                        <br><small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $order->customer_name }}</strong>
                                        <br><small class="text-muted">{{ $order->customer_email }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $order->lapangan_info['nama'] ?? 'N/A' }}</span>
                                        <br><small class="text-muted">{{ $order->lapangan_info['jenis'] ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($order->tanggal_booking)->format('d M Y') }}</td>
                                    <td>
                                        <span class="fw-bold">{{ $order->jam_mulai }} - {{ $order->jam_selesai }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
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
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-info btn-action">
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
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada orders</h5>
                        <p class="text-muted">Orders akan muncul di sini setelah customer melakukan booking.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection
