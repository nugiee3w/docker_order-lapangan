@extends('layouts.app')

@section('title', 'Jadwal Lapangan - Booking Lapangan')

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
                    <i class="fas fa-clock me-1"></i>Jadwal Lapangan
                </li>
            </ol>
        </nav>
    </div>
</div>

<!-- Main Content Container -->
<div class="container-fluid px-3 px-md-4">

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-gradient mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body text-white py-4 px-4">
                <h1 class="mb-2 fw-bold">
                    <i class="fas fa-clock me-3"></i>
                    Jadwal Lapangan
                </h1>
                <p class="mb-0 opacity-75">Kelola dan pantau jadwal booking lapangan olahraga</p>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="filter-section">
    <form method="GET" action="{{ route('jadwal') }}">
        <div class="row">
            <div class="col-md-3">
                <label for="jenis" class="form-label">Jenis Lapangan</label>
                <select name="jenis" id="jenis" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="futsal" {{ request('jenis') == 'futsal' ? 'selected' : '' }}>Futsal</option>
                    <option value="badminton" {{ request('jenis') == 'badminton' ? 'selected' : '' }}>Badminton</option>
                    <option value="basket" {{ request('jenis') == 'basket' ? 'selected' : '' }}>Basket</option>
                    <option value="tenis_meja" {{ request('jenis') == 'tenis_meja' ? 'selected' : '' }}>Tenis Meja</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="{{ request('tanggal') }}">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="dipesan" {{ request('status') == 'dipesan' ? 'selected' : '' }}>Dipesan</option>
                    <option value="sedang_digunakan" {{ request('status') == 'sedang_digunakan' ? 'selected' : '' }}>Sedang Digunakan</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('jadwal') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-undo me-1"></i>Reset
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Jadwal Cards -->
<div class="row">
    @forelse($jadwals as $jadwal)
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100 {{ $jadwal->status == 'tersedia' ? 'border-success' : ($jadwal->status == 'dipesan' ? 'border-warning' : 'border-secondary') }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">{{ $jadwal->lapangan->nama }}</h6>
                <span class="badge jenis-badge
                    @if($jadwal->lapangan->jenis == 'futsal') bg-primary
                    @elseif($jadwal->lapangan->jenis == 'badminton') bg-success
                    @elseif($jadwal->lapangan->jenis == 'basket') bg-warning
                    @else bg-info
                    @endif
                ">
                    {{ ucfirst($jadwal->lapangan->jenis) }}
                </span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong><i class="fas fa-calendar me-2"></i>Tanggal:</strong>
                    <span>{{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d M Y') }}</span>
                </div>
                
                <div class="mb-3">
                    <strong><i class="fas fa-clock me-2"></i>Waktu:</strong>
                    <span class="fw-bold">{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</span>
                </div>
                
                <div class="mb-3">
                    <strong><i class="fas fa-money-bill me-2"></i>Harga:</strong>
                    <span class="text-success fw-bold">Rp {{ number_format($jadwal->harga, 0, ',', '.') }}</span>
                </div>
                
                <div class="mb-3">
                    <strong><i class="fas fa-map-marker-alt me-2"></i>Lokasi:</strong>
                    <span>{{ $jadwal->lapangan->lokasi }}</span>
                </div>
                
                <div class="mb-3">
                    <strong><i class="fas fa-info-circle me-2"></i>Status:</strong>
                    <span class="badge status-badge 
                        @if($jadwal->status == 'tersedia') bg-success
                        @elseif($jadwal->status == 'dipesan') bg-warning
                        @elseif($jadwal->status == 'sedang_digunakan') bg-info
                        @else bg-secondary
                        @endif
                    ">
                        {{ ucfirst(str_replace('_', ' ', $jadwal->status)) }}
                    </span>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-grid gap-2">
                    @if($jadwal->status == 'tersedia')
                        <button class="btn btn-success btn-sm" onclick="bookingInfo({{ $jadwal->id }})">
                            <i class="fas fa-calendar-plus me-1"></i>Booking via API
                        </button>
                    @else
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            <i class="fas fa-ban me-1"></i>Tidak Tersedia
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle me-2"></i>
            Tidak ada jadwal yang ditemukan dengan filter yang dipilih.
        </div>
    </div>
    @endforelse
</div>

<!-- Booking Info Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Booking via API</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Untuk melakukan booking, gunakan Order Service API endpoint berikut:
                </div>
                
                <h6>1. Login untuk mendapatkan token:</h6>
                <div class="bg-light p-3 rounded mb-3">
                    <pre><code>POST http://localhost:8002/api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "customer123"
}</code></pre>
                </div>
                
                <h6>2. Buat order dengan token:</h6>
                <div class="bg-light p-3 rounded mb-3">
                    <pre><code id="orderExample"></code></pre>
                </div>
                
                <h6>3. cURL Example:</h6>
                <div class="bg-dark text-white p-3 rounded">
                    <pre><code id="curlBookingExample"></code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function bookingInfo(jadwalId) {
    const orderExample = `POST http://localhost:8002/api/orders
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
    "lapangan_id": 1,
    "jadwal_lapangan_id": ${jadwalId},
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "customer_phone": "081234567890",
    "tanggal_booking": "2025-07-27T00:00:00.000000Z",
    "jam_mulai": "10:00",
    "jam_selesai": "12:00"
}`;

    const curlExample = `# Login dulu
curl -X POST "http://localhost:8002/api/login" \\
-H "Content-Type: application/json" \\
-d '{"email":"john@example.com","password":"customer123"}'

# Kemudian booking dengan token
curl -X POST "http://localhost:8002/api/orders" \\
-H "Authorization: Bearer YOUR_TOKEN" \\
-H "Content-Type: application/json" \\
-d '{
    "lapangan_id": 1,
    "jadwal_lapangan_id": ${jadwalId},
    "customer_name": "John Doe",
    "customer_email": "john@example.com", 
    "customer_phone": "081234567890",
    "tanggal_booking": "2025-07-27T00:00:00.000000Z",
    "jam_mulai": "10:00",
    "jam_selesai": "12:00"
}'`;
    
    document.getElementById('orderExample').textContent = orderExample;
    document.getElementById('curlBookingExample').textContent = curlExample;
    new bootstrap.Modal(document.getElementById('bookingModal')).show();
}
</script>

</div>
<!-- End Main Content Container -->

@endsection
