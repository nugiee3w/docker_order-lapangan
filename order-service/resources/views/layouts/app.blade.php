<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard - Booking Lapangan')</title>

    <!-- Custom fonts for this template-->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        .btn-group-vertical .btn {
            margin-bottom: 2px;
            min-width: 70px;
        }
        
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
            background-size: cover;
        }
        
        .collapse-item.active {
            background-color: #eaecf4 !important;
            color: #3a3b45 !important;
            font-weight: bold;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .d-grid {
            display: grid !important;
            gap: 0.5rem;
        }
        
        .modal-content {
            border-radius: 0.35rem;
        }
        
        .alert {
            border-radius: 0.35rem;
        }
        
        /* Navbar styles */
        .navbar {
            z-index: 1030 !important;
        }
        
        /* Body padding for fixed navbar */
        body {
            padding-top: 70px; /* Adjust based on navbar height */
        }
        
        .navbar-brand {
            font-size: 1.5rem;
        }
        
        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.375rem;
        }
        
        .dropdown-menu {
            border-radius: 0.5rem;
            border: none;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-top: 0.5rem;
            z-index: 1040;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fc;
        }
        
        .dropdown-header {
            color: #5a5c69;
            font-weight: 600;
            font-size: 0.65rem;
            text-transform: uppercase;
        }
        
        .navbar-nav .nav-link {
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.375rem;
        }
        
        .dropdown-item {
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }
        
        .navbar-toggler {
            border: none;
            padding: 0.25rem 0.5rem;
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }
        
        /* Border utility classes */
        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }
        
        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }
        
        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }
        
        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }
        
        .border-left-danger {
            border-left: 0.25rem solid #e74a3b !important;
        }
        
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        
        /* Scroll to top button */
        .scroll-to-top {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            display: none;
            width: 2.75rem;
            height: 2.75rem;
            text-align: center;
            color: #fff;
            background: rgba(90, 92, 105, 0.5);
            line-height: 46px;
            z-index: 1000;
            border-radius: 100%;
            text-decoration: none;
        }
        
        .scroll-to-top:focus,
        .scroll-to-top:hover {
            color: white;
        }
        
        .scroll-to-top:hover {
            background: #5a5c69;
        }
        
        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            body {
                padding-top: 60px; /* Smaller padding for mobile */
            }
            
            .navbar-nav {
                padding-top: 1rem;
            }
            
            .navbar-nav .nav-link {
                border-radius: 0.375rem;
                margin: 0.25rem 0;
            }
        }
        
        @media (min-width: 992px) {
            body {
                padding-top: 75px; /* Larger padding for desktop */
            }
        }
        
        /* Table responsive improvements */
        .table-responsive {
            border-radius: 0.375rem;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: #5a5c69;
            background-color: #f8f9fc;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .badge {
            font-size: 0.75em;
            padding: 0.375rem 0.75rem;
        }
    </style>
    
    @yield('styles')
</head>

<body id="page-top">

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary fixed-top shadow">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <div class="rotate-n-15 me-2">
                    <i class="fas fa-futbol text-white"></i>
                </div>
                <span class="fw-bold">Sport Court Admin</span>
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-bold' : '' }}" 
                           href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            Dashboard
                        </a>
                    </li>

                    <!-- Orders Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('orders.*') ? 'active fw-bold' : '' }}" 
                           href="#" id="ordersDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-list-alt me-1"></i>
                            Pemesanan
                        </a>
                        <ul class="dropdown-menu shadow border-0" aria-labelledby="ordersDropdown">
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="fas fa-list text-primary"></i> Menu Pemesanan
                                </h6>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('orders.index') ? 'active' : '' }}" 
                                   href="{{ route('orders.index') }}">
                                    <i class="fas fa-list me-2 text-primary"></i>
                                    Semua Pemesanan
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('orders.create') ? 'active' : '' }}" 
                                   href="{{ route('orders.create') }}">
                                    <i class="fas fa-plus me-2 text-success"></i>
                                    Tambah Pemesanan
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <h6 class="dropdown-header">
                                    <i class="fas fa-filter text-info"></i> Filter Cepat
                                </h6>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index', ['status' => 'pending']) }}">
                                    <i class="fas fa-clock me-2 text-warning"></i>
                                    Pending
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index', ['status' => 'confirmed']) }}">
                                    <i class="fas fa-check me-2 text-success"></i>
                                    Confirmed
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('orders.index', ['payment_status' => 'unpaid']) }}">
                                    <i class="fas fa-exclamation me-2 text-danger"></i>
                                    Belum Dibayar
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Lapangan -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('lapangan.*') ? 'active fw-bold' : '' }}" href="{{ route('lapangan.index') }}">
                            <i class="fas fa-map-marked-alt me-1"></i>
                            Pilih Lapangan
                        </a>
                    </li>
                </ul>

                <!-- Right Side - User Menu -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" 
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2"></i>
                            <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user me-2 text-primary"></i>
                                    Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2 text-danger"></i>
                                        Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container-fluid">
        @yield('content')
    </div>
    <!-- End of Page Content -->

    <!-- Footer -->
    <footer class="bg-white py-4 mt-5 border-top">
        <div class="container-fluid">
            <div class="copyright text-center">
                <span class="text-muted">Copyright &copy; Sport Court Admin {{ date('Y') }}</span>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- Custom scripts for navbar-->
    <script>
        // Scroll to top button
        $(document).ready(function(){
            $(window).scroll(function(){
                if($(this).scrollTop() > 100){
                    $('.scroll-to-top').fadeIn();
                } else {
                    $('.scroll-to-top').fadeOut();
                }
            });
            
            $('.scroll-to-top').click(function(){
                $('html, body').animate({scrollTop : 0}, 800);
                return false;
            });
            
            // Navbar dropdowns
            $('.dropdown-toggle').dropdown();
        });
    </script>

    @yield('scripts')
</body>
</html>
