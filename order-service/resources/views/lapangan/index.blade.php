@extends('layouts.app')

@section('title', 'Pilih Lapangan - Order Service')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">
        <i class="fas fa-map-marked-alt me-2"></i>Pilih Lapangan
    </h1>
</div>

@if(isset($error))
<div class="alert alert-danger border-left-danger" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ $error }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger border-left-danger" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ session('error') }}
</div>
@endif

@if(session('success'))
<div class="alert alert-success border-left-success" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
</div>
@endif

<div class="row">
    @forelse($lapangans as $lapangan)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card shadow h-100">
            <!-- Header dengan gambar lapangan -->
            <div class="card-header p-0">
                <div class="position-relative">
                    @if(isset($lapangan['image']) && $lapangan['image'])
                        <img src="{{ $lapangan['image'] }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $lapangan['nama'] }}" loading="lazy" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="d-none align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            @if($lapangan['jenis'] == 'futsal')
                                <i class="fas fa-futbol fa-4x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'badminton')
                                <i class="fas fa-shuttle-van fa-4x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'basket')
                                <i class="fas fa-basketball-ball fa-4x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'voli')
                                <i class="fas fa-volleyball-ball fa-4x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'tennis')
                                <i class="fas fa-table-tennis fa-4x text-white opacity-50"></i>
                            @else
                                <i class="fas fa-running fa-4x text-white opacity-50"></i>
                            @endif
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            @if($lapangan['jenis'] == 'futsal')
                                <i class="fas fa-futbol fa-4x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'badminton')
                                <i class="fas fa-shuttle-van fa-4x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'basket')
                                <i class="fas fa-basketball-ball fa-4x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'voli')
                                <i class="fas fa-volleyball-ball fa-4x text-white opacity-50"></i>
                            @elseif($lapangan['jenis'] == 'tennis')
                                <i class="fas fa-table-tennis fa-4x text-white opacity-50"></i>
                            @else
                                <i class="fas fa-running fa-4x text-white opacity-50"></i>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Status badge -->
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge 
                            @if($lapangan['status'] == 'tersedia') bg-success
                            @elseif($lapangan['status'] == 'maintenance') bg-warning
                            @else bg-danger
                            @endif
                        ">
                            {{ ucfirst($lapangan['status']) }}
                        </span>
                    </div>
                    
                    <!-- Jenis lapangan badge -->
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge 
                            @if($lapangan['jenis'] == 'futsal') bg-primary
                            @elseif($lapangan['jenis'] == 'badminton') bg-success
                            @elseif($lapangan['jenis'] == 'basket') bg-warning
                            @elseif($lapangan['jenis'] == 'voli') bg-info
                            @elseif($lapangan['jenis'] == 'tennis') bg-secondary
                            @else bg-info
                            @endif
                        ">
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
                    </div>
                </div>
            </div>

            <!-- Body dengan informasi lapangan -->
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h5 class="card-title mb-0 fw-bold">{{ $lapangan['nama'] }}</h5>
                    <div class="text-end">
                        <div class="text-success fw-bold">Rp {{ number_format($lapangan['harga_per_jam'], 0, ',', '.') }}</div>
                        <small class="text-muted">per jam</small>
                    </div>
                </div>

                <p class="card-text text-muted mb-3">{{ $lapangan['deskripsi'] ?? 'Tidak ada deskripsi' }}</p>

                <div class="mb-3">
                    <small class="text-muted d-block">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $lapangan['lokasi'] }}
                    </small>
                </div>

                <!-- Fasilitas -->
                @if(isset($lapangan['fasilitas']) && is_array($lapangan['fasilitas']) && count($lapangan['fasilitas']) > 0)
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Fasilitas:</small>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach($lapangan['fasilitas'] as $fasilitas)
                            <span class="badge bg-secondary">{{ $fasilitas }}</span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Statistik pesanan -->
                <div class="mb-3">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="fw-bold text-primary">{{ $lapangan['total_orders'] ?? 0 }}</div>
                                <small class="text-muted">Total Pesanan</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="fw-bold text-success">{{ count($lapangan['recent_orders'] ?? []) }}</div>
                            <small class="text-muted">Pesanan Aktif</small>
                        </div>
                    </div>
                </div>

                <!-- Recent orders preview -->
                @if(isset($lapangan['recent_orders']) && count($lapangan['recent_orders']) > 0)
                <div class="mb-3">
                    <small class="text-muted d-block mb-2">Pesanan Terbaru:</small>
                    <div class="list-group list-group-flush">
                        @foreach(array_slice($lapangan['recent_orders']->toArray(), 0, 3) as $order)
                        <div class="list-group-item px-0 py-1 border-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="fw-medium">{{ $order['customer_name'] }}</small>
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($order['tanggal_booking'])->format('d M') }} - {{ $order['jam_mulai'] }}</small>
                                </div>
                                <span class="badge 
                                    @if($order['status'] == 'confirmed') bg-success
                                    @elseif($order['status'] == 'pending') bg-warning
                                    @else bg-secondary
                                    @endif
                                    badge-sm">
                                    {{ $order['status'] }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Footer dengan aksi -->
            <div class="card-footer bg-transparent">
                <div class="d-grid gap-2">
                    <a href="{{ route('lapangan.show', $lapangan['id']) }}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>Lihat Detail & Pesanan
                    </a>
                    @if($lapangan['status'] == 'tersedia')
                        <a href="{{ route('orders.create', ['lapangan_id' => $lapangan['id']]) }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus me-2"></i>Buat Pesanan Baru
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-map-marked-alt fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">Tidak Ada Lapangan Tersedia</h4>
                <p class="text-muted">Saat ini tidak ada lapangan yang tersedia dalam sistem.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

@endsection

@section('styles')
<style>
.badge-sm {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}

.card-img-top {
    transition: transform 0.3s ease;
}

.card:hover .card-img-top {
    transform: scale(1.05);
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.list-group-item:last-child {
    border-bottom: 0 !important;
}

/* Perbaikan untuk gambar */
.card-img-top {
    border-top-left-radius: calc(0.375rem - 1px);
    border-top-right-radius: calc(0.375rem - 1px);
}

/* Loading state untuk gambar */
.card-img-top[loading="lazy"] {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Responsif untuk mobile */
@media (max-width: 768px) {
    .card-img-top {
        height: 150px !important;
    }
    
    .card-title {
        font-size: 1rem;
    }
    
    .badge {
        font-size: 0.65rem;
    }
}
</style>
@endsection
