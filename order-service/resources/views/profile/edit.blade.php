@extends('layouts.app')

@section('title', 'Edit Profil Admin')

@section('styles')
<style>
.profile-header {
    background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
    color: white;
    border-radius: 10px;
    margin-bottom: 20px;
}

.profile-stats {
    background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
    border-radius: 10px;
    color: white;
}

.card {
    border: none;
    border-radius: 15px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(90deg, #4e73df 0%, #224abe 100%) !important;
    border-radius: 15px 15px 0 0 !important;
}

.input-group-text {
    background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
    color: white;
    border: none;
}

.btn-primary {
    background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
    border: none;
    border-radius: 25px;
    padding: 10px 25px;
}

.btn-warning {
    background: linear-gradient(90deg, #f6c23e 0%, #dda20a 100%);
    border: none;
    border-radius: 25px;
    padding: 10px 25px;
    color: white;
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.alert {
    border-radius: 10px;
    border: none;
}

.badge {
    border-radius: 20px;
    padding: 5px 12px;
}

.bg-gradient-primary {
    background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
}

.bg-gradient-warning {
    background: linear-gradient(90deg, #f6c23e 0%, #dda20a 100%);
}

.bg-gradient-success {
    background: linear-gradient(90deg, #1cc88a 0%, #13855c 100%);
}

.bg-gradient-info {
    background: linear-gradient(90deg, #36b9cc 0%, #258391 100%);
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.6s ease forwards;
}

.password-strength-very-weak { color: #dc3545; }
.password-strength-weak { color: #fd7e14; }
.password-strength-medium { color: #ffc107; }
.password-strength-strong { color: #20c997; }
.password-strength-very-strong { color: #28a745; }
</style>
@endsection

@section('content')
<!-- Page Heading with Gradient Background -->
<div class="profile-header p-4 mb-4 animate-fade-in">
    <div class="d-sm-flex align-items-center justify-content-between">
        <div>
            <h1 class="h2 mb-2 text-white">
                <i class="fas fa-user-cog"></i> Edit Profil Admin
            </h1>
            <p class="mb-0 opacity-75">Kelola informasi akun dan keamanan profil Anda</p>
        </div>
        <div class="mt-3 mt-sm-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}" class="text-white-50">
                            <i class="fas fa-home"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Edit Profil</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <!-- Profile Information Card -->
    <div class="col-lg-8 mb-4 animate-fade-in">
        <div class="card shadow-lg">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-white">
                    <i class="fas fa-user-edit"></i> Informasi Profil
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    <i class="fas fa-info-circle text-primary"></i>
                    Perbarui informasi profil dan alamat email akun administrator Anda.
                </p>
                
                @if (session('status') === 'profile-updated')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Profil berhasil diperbarui!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('patch')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" 
                                   required autofocus autocomplete="name" placeholder="Masukkan nama lengkap">
                        </div>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" 
                                   required autocomplete="username" placeholder="admin@example.com">
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2">
                                <div class="alert alert-warning" role="alert">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Email Anda belum diverifikasi.
                                    <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 align-baseline text-decoration-none">
                                            Klik di sini untuk mengirim ulang email verifikasi.
                                        </button>
                                    </form>
                                </div>

                                @if (session('status') === 'verification-link-sent')
                                    <div class="alert alert-info" role="alert">
                                        <i class="fas fa-info-circle"></i>
                                        Link verifikasi baru telah dikirim ke alamat email Anda.
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Password Card -->
    <div class="col-lg-4 mb-4 animate-fade-in" style="animation-delay: 0.2s;">
        <div class="card shadow-lg">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-white">
                    <i class="fas fa-shield-alt"></i> Keamanan Password
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    <i class="fas fa-lock text-warning"></i>
                    Pastikan akun menggunakan password yang kuat dan aman.
                </p>
                
                @if (session('status') === 'password-updated')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> Password berhasil diperbarui!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="post" action="{{ route('password.update') }}">
                    @csrf
                    @method('put')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                                   id="current_password" name="current_password" autocomplete="current-password" 
                                   placeholder="Masukkan password saat ini">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                <i class="fas fa-eye" id="current_password_icon"></i>
                            </button>
                        </div>
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                   id="password" name="password" autocomplete="new-password" 
                                   placeholder="Masukkan password baru">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password_icon"></i>
                            </button>
                        </div>
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Password minimal 8 karakter
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-check"></i></span>
                            <input type="password" class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                                   id="password_confirmation" name="password_confirmation" autocomplete="new-password" 
                                   placeholder="Konfirmasi password baru">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye" id="password_confirmation_icon"></i>
                            </button>
                        </div>
                        @error('password_confirmation', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-shield-alt"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(inputId + '_icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
