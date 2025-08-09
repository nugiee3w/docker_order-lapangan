<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Order Management - Booking Lapangan')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        
        <style>
            body {
                font-family: 'Nunito', sans-serif;
                background-color: #f8f9fc;
            }
            .navbar-brand {
                font-weight: bold;
            }
            .sidebar {
                min-height: 100vh;
                background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
                background-size: cover;
            }
            .sidebar .nav-link {
                color: rgba(255, 255, 255, 0.8);
                font-weight: 400;
            }
            .sidebar .nav-link:hover {
                color: #fff;
            }
            .sidebar .nav-link.active {
                color: #fff;
                background-color: rgba(255, 255, 255, 0.1);
                border-radius: 0.35rem;
            }
        </style>
    </head>
    <body>
        <div class="d-flex">
            <!-- Sidebar -->
                        <nav class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" 
                 style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                
                <!-- Sidebar - Brand -->
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
                    <div class="sidebar-brand-icon">
                        <i class="fas fa-futbol"></i>
                    </div>
                    <div class="sidebar-brand-text mx-3">Sport Court Admin</div>
                </a>

                <!-- Divider -->
                <hr class="sidebar-divider my-0">

                <!-- Nav Item - Dashboard -->
                <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-fw fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading">
                    Manajemen Pemesanan
                </div>

                <!-- Nav Item - Orders Menu -->
                <li class="nav-item {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOrders"
                        aria-expanded="{{ request()->routeIs('orders.*') ? 'true' : 'false' }}" aria-controls="collapseOrders">
                        <i class="fas fa-fw fa-list-alt"></i>
                        <span>Pemesanan</span>
                    </a>
                    <div id="collapseOrders" class="collapse {{ request()->routeIs('orders.*') ? 'show' : '' }}" 
                         aria-labelledby="headingOrders" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Menu Pemesanan:</h6>
                            <a class="collapse-item {{ request()->routeIs('orders.index') ? 'active' : '' }}" 
                               href="{{ route('orders.index') }}">
                                <i class="fas fa-list"></i> Semua Pemesanan
                            </a>
                            <a class="collapse-item {{ request()->routeIs('orders.create') ? 'active' : '' }}" 
                               href="{{ route('orders.create') }}">
                                <i class="fas fa-plus"></i> Tambah Pemesanan
                            </a>
                            <div class="dropdown-divider"></div>
                            <h6 class="collapse-header">Filter Cepat:</h6>
                            <a class="collapse-item" href="{{ route('orders.index', ['status' => 'pending']) }}">
                                <i class="fas fa-clock text-warning"></i> Pending
                            </a>
                            <a class="collapse-item" href="{{ route('orders.index', ['status' => 'confirmed']) }}">
                                <i class="fas fa-check text-success"></i> Confirmed
                            </a>
                            <a class="collapse-item" href="{{ route('orders.index', ['payment_status' => 'unpaid']) }}">
                                <i class="fas fa-exclamation text-danger"></i> Belum Dibayar
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider">

                <!-- Heading -->
                <div class="sidebar-heading">
                    Lapangan
                </div>

                <!-- Nav Item - Lapangan -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-fw fa-map-marked-alt"></i>
                        <span>Daftar Lapangan</span>
                    </a>
                </li>

                <!-- Divider -->
                <hr class="sidebar-divider d-none d-md-block">

                <!-- Sidebar Toggler (Sidebar) -->
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>

            </nav>
            
            <!-- Content Wrapper -->
            <div class="flex-grow-1">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <div class="container-fluid">
                        <div class="navbar-nav ms-auto">
                            <div class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                    <span class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name ?? 'Admin' }}</span>
                                    <i class="fas fa-user-circle fa-lg"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                                        Profile
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                
                <!-- Page Content -->
                <main>
                    @yield('content')
                    {{ $slot ?? '' }}
                </main>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        @yield('scripts')
    </body>
</html>
