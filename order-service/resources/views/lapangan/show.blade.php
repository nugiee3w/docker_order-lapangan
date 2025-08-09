@extends('layouts.app')

@section('title', 'Detail Lapangan - ' . $lapangan['nama'])

@section('content')
<!-- Breadcrumb -->
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">Dashboard</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('lapangan.index') }}">Pilih Lapangan</a>
        </li>
        <li class="breadcrumb-item active">{{ $lapangan['nama'] }}</li>
    </ol>
</nav>

<!-- Header dengan info lapangan -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="row g-0">
                <div class="col-md-4">
                    @if(isset($lapangan['gambar']) && $lapangan['gambar'])
                        <img src="http://localhost:8001/storage/{{ $lapangan['gambar'] }}" 
                             class="img-fluid rounded-start h-100" 
                             style="object-fit: cover;" 
                             alt="{{ $lapangan['nama'] }}" 
                             loading="lazy"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="d-none align-items-center justify-content-center h-100 rounded-start" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 300px;">
                            @if($lapangan['jenis'] == 'futsal')
                                <i class="fas fa-futbol fa-5x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'badminton')
                                <i class="fas fa-shuttle-van fa-5x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'basket')
                                <i class="fas fa-basketball-ball fa-5x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'voli')
                                <i class="fas fa-volleyball-ball fa-5x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'tennis')
                                <i class="fas fa-table-tennis fa-5x text-white opacity-50"></i>
                            @else
                                <i class="fas fa-running fa-5x text-white opacity-50"></i>
                            @endif
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 rounded-start" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 300px;">
                            @if($lapangan['jenis'] == 'futsal')
                                <i class="fas fa-futbol fa-5x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'badminton')
                                <i class="fas fa-shuttle-van fa-5x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'basket')
                                <i class="fas fa-basketball-ball fa-5x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'voli')
                                <i class="fas fa-volleyball-ball fa-5x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'tennis')
                                <i class="fas fa-table-tennis fa-5x text-white opacity-50"></i>
                            @else
                                <i class="fas fa-running fa-5x text-white opacity-50"></i>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h2 class="card-title fw-bold">{{ $lapangan['nama'] }}</h2>
                                <span class="badge 
                                    @if($lapangan['jenis'] == 'futsal') bg-primary
                                    @elseif($lapangan['jenis'] == 'badminton') bg-success
                                    @elseif($lapangan['jenis'] == 'basket') bg-warning
                                    @elseif($lapangan['jenis'] == 'voli') bg-info
                                    @elseif($lapangan['jenis'] == 'tennis') bg-secondary
                                    @else bg-info
                                    @endif
                                    fs-6 me-2">
                                    @if($lapangan['jenis'] == 'futsal')
                                        <i class="fas fa-futbol me-1"></i>
                                    @elseif($lapangan['jenis'] == 'badminton')
                                        <i class="fas fa-shuttlecock me-1"></i>
                                    @elseif($lapangan['jenis'] == 'basket')
                                        <i class="fas fa-basketball-ball me-1"></i>
                                    @elseif($lapangan['jenis'] == 'voli')
                                        <i class="fas fa-volleyball-ball me-1"></i>
                                    @elseif($lapangan['jenis'] == 'tennis')
                                        <i class="fas fa-table-tennis me-1"></i>
                                    @else
                                        <i class="fas fa-running me-1"></i>
                                    @endif
                                    {{ ucfirst($lapangan['jenis']) }}
                                </span>
                                <span class="badge 
                                    @if($lapangan['status'] == 'tersedia') bg-success
                                    @elseif($lapangan['status'] == 'maintenance') bg-warning
                                    @else bg-danger
                                    @endif
                                    fs-6">
                                    {{ ucfirst($lapangan['status']) }}
                                </span>
                            </div>
                            <div class="text-end">
                                <div class="text-success fw-bold fs-3">Rp {{ number_format($lapangan['harga_per_jam'], 0, ',', '.') }}</div>
                                <small class="text-muted">per jam</small>
                            </div>
                        </div>

                        <p class="card-text mb-3">{{ $lapangan['deskripsi'] ?? 'Tidak ada deskripsi' }}</p>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <strong>Lokasi:</strong> {{ $lapangan['lokasi'] }}
                                </small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <strong>Total Pesanan:</strong> {{ $orders->total() }} pesanan
                                </small>
                            </div>
                        </div>

                        <!-- Fasilitas -->
                        @if(isset($lapangan['fasilitas']) && is_array($lapangan['fasilitas']) && count($lapangan['fasilitas']) > 0)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-2"><strong>Fasilitas:</strong></small>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($lapangan['fasilitas'] as $fasilitas)
                                    <span class="badge bg-secondary">{{ $fasilitas }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div class="d-grid gap-2 d-md-flex">
                            @if($lapangan['status'] == 'tersedia')
                                <a href="{{ route('orders.create', ['lapangan_id' => $lapangan['id']]) }}" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Buat Pesanan Baru
                                </a>
                            @endif
                            <a href="{{ route('lapangan.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Pesanan -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="m-0 fw-bold text-primary">
                    <i class="fas fa-list-alt me-2"></i>Daftar Pesanan Lapangan
                </h5>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal Booking</th>
                                    <th>Waktu</th>
                                    <th>Customer</th>
                                    <th>Kontak</th>
                                    <th>Status</th>
                                    <th>Pembayaran</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($order->tanggal_booking)->format('d M Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="fw-medium">{{ $order->jam_mulai }} - {{ $order->jam_selesai }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $order->customer_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $order->customer_email }}</small>
                                        </div>
                                    </td>
                                    <td>{{ $order->customer_phone }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($order->status == 'confirmed') bg-success
                                            @elseif($order->status == 'pending') bg-warning
                                            @elseif($order->status == 'cancelled') bg-danger
                                            @else bg-secondary
                                            @endif
                                        ">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($order->payment_status == 'paid') bg-success
                                            @elseif($order->payment_status == 'pending') bg-warning
                                            @else bg-danger
                                            @endif
                                        ">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-success">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum Ada Pesanan</h5>
                        <p class="text-muted">Lapangan ini belum memiliki pesanan apapun.</p>
                        @if($lapangan['status'] == 'tersedia')
                            <a href="{{ route('orders.create', ['lapangan_id' => $lapangan['id']]) }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Buat Pesanan Pertama
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
.img-fluid {
    transition: transform 0.3s ease;
}

.card:hover .img-fluid {
    transform: scale(1.02);
}

.table th {
    font-weight: 600;
    color: #5a5c69;
    border-top: none;
}

.badge {
    font-size: 0.75em;
}

/* Loading state untuk gambar */
.img-fluid[loading="lazy"] {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.4rem;
    }
    
    .card-body h2 {
        font-size: 1.5rem;
    }
    
    .fs-3 {
        font-size: 1.25rem !important;
    }
}
</style>
@endsection
