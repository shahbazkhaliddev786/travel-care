<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TravelCare') }} - Admin Panel</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/logo.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>


</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="admin-sidebar" id="sidebar">
            <div class="sidebar-brand">
                <a href="{{ route('admin.dashboard') }}" class="brand-text">
                    <i class="bi bi-shield-check"></i>
                    TravelCare Admin
                </a>
                <button class="sidebar-close" onclick="closeSidebar()">
                    <i class="bi bi-x"></i>
                </button>
            </div>
            
            <div class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i>
                            Dashboard
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section">User Management</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.customers.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.customers*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i>
                            Customers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.doctors.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.doctors*') ? 'active' : '' }}">
                            <i class="bi bi-heart-pulse"></i>
                            Doctors
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.laboratories.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.laboratories*') ? 'active' : '' }}">
                            <i class="bi bi-flask"></i>
                            Laboratories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.translators.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.translators*') ? 'active' : '' }}">
                            <i class="bi bi-translate"></i>
                            Translators
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section">Content Management</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="{{ route('admin.hospitals.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.hospitals*') ? 'active' : '' }}">
                            <i class="bi bi-hospital"></i>
                            Hospitals
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.tags.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.tags*') ? 'active' : '' }}">
                            <i class="bi bi-tags"></i>
                            Tags & Services
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.footer-pages.index') }}" 
                           class="nav-link {{ request()->routeIs('admin.footer-pages*') ? 'active' : '' }}">
                            <i class="bi bi-file-text"></i>
                            Footer Pages
                        </a>
                    </li>
                </ul>
            </div>

            <!-- User Dropdown -->
            <div class="user-dropdown">
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        @if(Auth::user()->profile_photo)
                            <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Admin" class="user-avatar">
                        @else
                            <div class="user-avatar bg-primary d-flex align-items-center justify-content-center">
                                <i class="bi bi-person text-white"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <div style="font-weight: 600; font-size: 0.9rem;">{{ Auth::user()->name }}</div>
                            <div style="font-size: 0.75rem; opacity: 0.7;">Administrator</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('admin.profile') }}">
                            <i class="bi bi-person me-2"></i>Profile
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}">
                            <i class="bi bi-box-arrow-right me-2"></i>Sign out
                        </a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main class="main-content">
            <!-- Header -->
            <header class="content-header d-flex justify-content-between align-items-start">
                <!-- Mobile Sidebar Toggle -->
                <button class="mobile-toggle" onclick="openSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="header-content">
                    <h1 class="page-title">@yield('title')</h1>
                    @hasSection('subtitle')
                        <p class="page-subtitle">@yield('subtitle')</p>
                    @endif
                </div>
                <div class="header-actions">
                    @yield('actions')
                </div>
            </header>

            <!-- Content -->
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        <i class="bi bi-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @yield('scripts')
    @stack('scripts')
</body>
</html>