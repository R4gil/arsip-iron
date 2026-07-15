<!doctype html>
<html class="fixed" lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'IRON SMART') - IRON SMART</title>
    
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    @include('partials.ui-styles')
    <style>
        /* GLOBAL FONT SIZE ADJUSTMENTS FOR BETTER READABILITY */
        body {
            font-size: 0.95rem !important;
        }
        .content-body {
            font-size: 0.95rem !important;
        }
        .content-body h1, .content-body h2, .content-body h3, .content-body h4, .content-body h5, .content-body h6 {
            font-size: 1.1rem !important;
        }
        .content-body .form-label {
            font-size: 0.95rem !important;
            font-weight: 600 !important;
        }
        .content-body .form-control,
        .content-body .form-select {
            font-size: 0.95rem !important;
            padding: 0.6rem 0.75rem !important;
        }
        .content-body .form-text {
            font-size: 0.85rem !important;
        }
        .content-body .btn {
            font-size: 0.95rem !important;
            padding: 0.6rem 1.2rem !important;
        }
        .content-body .table {
            font-size: 0.9rem !important;
        }
        .content-body .table th,
        .content-body .table td {
            padding: 0.75rem !important;
            vertical-align: middle !important;
        }
        .content-body .card-body {
            font-size: 0.95rem !important;
        }
        .content-body .badge {
            font-size: 0.8rem !important;
            padding: 0.35rem 0.65rem !important;
        }
        .content-body .is-page-header h4 {
            font-size: 1.4rem !important;
        }
        .content-body .is-page-header p {
            font-size: 0.95rem !important;
        }

        /* SINKRONISASI WARNA EMAS PREMIUM (GOLD TONE) PORTO ADMIN */
        :root {
            --porto-dark-bg: #1d2127;       /* Latar gelap sidebar */
            --porto-dark-header: #191c21;   /* Latar header sidebar */
            --porto-gold-primary: #d4af37;  /* Warna Gold Utama */
            --porto-gold-hover: #f3e5ab;    /* Warna Gold Terang untuk Hover */
            --porto-gold-dark: #aa7c11;     /* Warna Gold Gelap untuk Bayangan */
        }

        /* Override latar belakang sidebar */
        html.fixed .sidebar-left {
            background: var(--porto-dark-bg) !important;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        /* Header Sidebar (Navigation Menu) */
        .sidebar-left .sidebar-header {
            background: var(--porto-dark-header) !important;
            border-bottom: 1px solid #242930;
        }
        .sidebar-left .sidebar-header .sidebar-title {
            color: #ffffff !important;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .sidebar-left .sidebar-header .sidebar-toggle {
            background: #14161a !important;
            color: var(--porto-gold-primary) !important;
        }

        /* Styling Item Menu Utama */
        .nav-main ul.nav-main > li > a {
            color: #f8fafc !important;
            font-weight: 600;
            font-size: 0.95rem !important;
            padding: 12px 18px;
            border-radius: 6px;
            margin: 4px 10px;
            display: flex;
            align-items: center;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
        }
        
        /* Efek Hover Menu */
        .nav-main ul.nav-main > li > a:hover {
            background: rgba(212, 175, 55, 0.15) !important;
            color: var(--porto-gold-hover) !important;
        }
        .nav-main ul.nav-main > li > a:hover i {
            color: var(--porto-gold-hover) !important;
        }
        
        /* MENU AKTIF (NAV-ACTIVE): Tombol Full Gold Mewah */
        .nav-main ul.nav-main > li.nav-active > a {
            background: linear-gradient(135deg, var(--porto-gold-primary), var(--porto-gold-dark)) !important;
            color: #1d2127 !important;
            font-weight: 700 !important;
            box-shadow: 0 3px 10px rgba(212, 175, 55, 0.35) !important;
        }
        
        /* Warna Icon Menu */
        .nav-main ul.nav-main > li > a i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            margin-right: 8px;
            color: var(--porto-gold-primary) !important;
            transition: all 0.2s ease;
        }
        
        /* Warna Icon saat Menu Aktif */
        .nav-main ul.nav-main > li.nav-active > a i {
            color: #1d2127 !important;
        }

        /* Efek Panah Dropdown Porto agar Berwarna Gold */
        .nav-main ul.nav-main > li.nav-parent > a::after {
            color: var(--porto-gold-primary) !important;
        }
        
        /* Ketika Dropdown Aktif Terbuka */
        .nav-main ul.nav-main > li.nav-expanded > a {
            background: rgba(212, 175, 55, 0.1) !important;
            color: var(--porto-gold-hover) !important;
        }

        /* Styling Sub-Menu Anak di Dalam Dropdown */
        .nav-main ul.nav-main .nav-children li.active > a {
            color: var(--porto-gold-primary) !important;
            font-weight: 700 !important;
        }
        .nav-main ul.nav-main .nav-children li > a:hover {
            color: var(--porto-gold-hover) !important;
            background: transparent !important;
            padding-left: 30px !important;
            transition: all 0.2s ease;
        }

        .sidebar-brand-sub {
            padding: 0 18px;
            border-bottom: 1px solid #242930;
        }

        /* GLOBAL BUTTON GOLD DI HALAMAN KONTEN */
        .content-body .btn-primary, 
        .content-body .btn-default,
        .content-body button[type="submit"] {
            background: linear-gradient(135deg, var(--porto-gold-primary), var(--porto-gold-dark)) !important;
            border-color: var(--porto-gold-dark) !important;
            color: #1d2127 !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 5px rgba(212, 175, 55, 0.2) !important;
        }
        .content-body .btn-primary:hover, 
        .content-body .btn-default:hover,
        .content-body button[type="submit"]:hover {
            background: var(--porto-gold-hover) !important;
            border-color: var(--porto-gold-primary) !important;
            color: #1d2127 !important;
        }

        /* Pagination Styling - Gold Theme */
        .pagination {
            gap: 4px;
        }
        .pagination .page-link {
            background: #fff !important;
            border: 1.5px solid #e2e8f0 !important;
            color: #334155 !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            padding: 0.5rem 0.85rem !important;
            border-radius: 8px !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
        }
        .pagination .page-link:hover {
            background: #fef3c7 !important;
            border-color: #d4af37 !important;
            color: #1e293b !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.2) !important;
        }
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #d4af37, #aa7c11) !important;
            border-color: #d4af37 !important;
            color: #1e293b !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3) !important;
        }
        .pagination .page-item.disabled .page-link {
            background: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }
        .pagination .page-link .sr-only {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <section class="body">

        <header class="header">
            <div class="logo-container">
                <a href="{{ route('dashboard') }}" class="logo" style="text-decoration: none; display: flex; align-items: center; height: 100%;">
                    <span style="font-weight: 800; font-size: 20px; color: #333; letter-spacing: 1px; padding-left: 10px;">IRON SMART</span>
                </a>
                <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
                    <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
                </div>
            </div>
        
            <div class="header-right">
                <div class="d-none d-lg-inline-block pt-2 me-3" style="max-width: 350px; vertical-align: middle;">
                    <p class="text-muted small mb-0" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 500;">
                        @yield('subtitle', 'Sistem Monitoring dan Manajemen Arsip Digital')
                    </p>
                </div>
                <span class="separator"></span>

                @include('partials.header-notifications')
        
                <div id="userbox" class="userbox">
                    <a href="#" data-bs-toggle="dropdown" aria-expanded="false" class="d-flex align-items-center gap-2" style="text-decoration: none;">
                        <figure class="profile-picture">
                            <i class="fas fa-user-circle text-muted" style="font-size: 28px; margin-top: 4px;"></i>
                        </figure>
                        <div class="profile-info">
                            <span class="name" style="font-weight: 600; color: #2d3748;">{{ auth()->user()->name ?? 'Administrator' }}</span>
                            <span class="role text-muted" style="font-size: 10px; text-transform: capitalize;">{{ auth()->user()->role ?? 'Admin' }}</span>
                        </div>
                        <i class="fa custom-caret"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow-sm" style="border: 1px solid #e5e7eb; border-radius: 6px;">
                        <ul class="list-unstyled mb-0">
                            <li><a role="menuitem" href="{{ route('profile') }}" style="padding: 10px 16px; display: flex; align-items: center; gap: 8px; color: #4a5568; text-decoration: none;"><i class="fas fa-id-card text-muted"></i> Profil Saya</a></li>
                            <li class="divider" style="border-top: 1px solid #edf2f7; margin: 0;"></li>
                            <li>
                                <a role="menuitem" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="padding: 10px 16px; display: flex; align-items: center; gap: 8px; text-decoration: none;" class="text-danger">
                                    <i class="fas fa-power-off"></i> Logout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>
        <div class="inner-wrapper">
            
            <aside id="sidebar-left" class="sidebar-left">
                <div class="sidebar-header">
                    <div class="sidebar-title">Navigation Menu</div>
                    <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
                        <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
                    </div>
                </div>
            
                <div class="nano">
                    <div class="nano-content">
                        <nav id="menu" class="nav-main" role="navigation">
                            <ul class="nav nav-main">
                                <li class="{{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
                                    <a class="nav-link" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>

                                <li class="nav-parent {{ request()->routeIs('arsip.*') || request()->routeIs('retensi.*') ? 'nav-expanded nav-active' : '' }}">
                                    <a class="nav-link" href="#">
                                        <i class="fas fa-archive" aria-hidden="true"></i>
                                        <span>Data Arsip</span>
                                    </a>
                                    <ul class="nav nav-children" style="background: rgba(0, 0, 0, 0.15); border-radius: 4px;">
                                        <li class="{{ request()->routeIs('arsip.index') ? 'active' : '' }}">
                                            <a href="{{ route('arsip.index') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                <i class="fas fa-list-ul" style="font-size: 0.85rem; margin-right: 5px;"></i> Daftar Arsip
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('arsip.create') ? 'active' : '' }}">
                                            <a href="{{ route('arsip.create') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                <i class="fas fa-plus-circle" style="font-size: 0.85rem; margin-right: 5px;"></i> Tambah Arsip
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('retensi.*') ? 'active' : '' }}">
                                            <a href="{{ route('retensi.index') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                <i class="fas fa-hourglass-half" style="font-size: 0.85rem; margin-right: 5px;"></i>
                                                Retensi Arsip
                                                @if(($retensiNotifCount ?? 0) > 0)
                                                    <span class="badge bg-danger ms-1">{{ $retensiNotifCount }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-parent {{ request()->routeIs('peminjaman.*') ? 'nav-expanded nav-active' : '' }}">
                                        <a class="nav-link" href="#">
                                            <i class="fas fa-book" aria-hidden="true"></i>
                                            <span>
                                                Peminjaman Arsip
                                                @if(($peminjamanAktifCount ?? 0) > 0)
                                                    <span class="badge bg-warning text-dark ms-1">{{ $peminjamanAktifCount }}</span>
                                                @endif
                                            </span>
                                        </a>
                                    <ul class="nav nav-children" style="background: rgba(0, 0, 0, 0.15); border-radius: 4px;">
                                        <li class="{{ request()->routeIs('peminjaman.index') ? 'active' : '' }}">
                                            <a href="{{ route('peminjaman.index') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                <i class="fas fa-list-ul" style="font-size: 0.85rem; margin-right: 5px;"></i> Daftar Peminjaman
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('peminjaman.create') ? 'active' : '' }}">
                                             <a href="{{ route('peminjaman.create') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                 <i class="fas fa-plus-circle" style="font-size: 0.85rem; margin-right: 5px;"></i> Tambah Peminjaman
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-parent {{ request()->routeIs('pengguna.*') || request()->routeIs('activity-log.*') ? 'nav-expanded nav-active' : '' }}">
                                    <a class="nav-link" href="#">
                                        <i class="fas fa-users" aria-hidden="true"></i>
                                        <span>Manajemen Pengguna</span>
                                    </a>
                                    <ul class="nav nav-children" style="background: rgba(0, 0, 0, 0.15); border-radius: 4px;">
                                        <li class="{{ request()->routeIs('pengguna.*') ? 'active' : '' }}">
                                            <a href="{{ route('pengguna.index') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                <i class="fas fa-users" style="font-size: 0.85rem; margin-right: 5px;"></i> Daftar Pengguna
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('activity-log.*') ? 'active' : '' }}">
                                            <a href="{{ route('activity-log.index') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                <i class="fas fa-history" style="font-size: 0.85rem; margin-right: 5px;"></i> Log Aktivitas
                                            </a>
                                        </li>
                                    </ul>
                                </li>


                                @if(auth()->user() && auth()->user()->role === 'Admin')
                                <li class="nav-parent {{ request()->routeIs('lokasi.*') || request()->routeIs('lemari.*') || request()->routeIs('rak.*') || request()->routeIs('klasifikasi.*') ? 'nav-expanded nav-active' : '' }}">
                                    <a class="nav-link" href="#">
                                        <i class="fas fa-cogs" aria-hidden="true"></i>
                                        <span>Pengaturan Arsip</span>
                                    </a>
                                    <ul class="nav nav-children" style="background: rgba(0, 0, 0, 0.15); border-radius: 4px;">
                                        <li class="{{ request()->routeIs('lokasi.*') ? 'active' : '' }}">
                                            <a href="{{ route('lokasi.index') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                <i class="fas fa-map-marker-alt" style="font-size: 0.85rem; margin-right: 5px;"></i> Lokasi Arsip
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('lemari.*') ? 'active' : '' }}">
                                            <a href="{{ route('lemari.index') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                <i class="fas fa-boxes" style="font-size: 0.85rem; margin-right: 5px;"></i> Lemari Arsip
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('rak.*') ? 'active' : '' }}">
                                            <a href="{{ route('rak.index') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                <i class="fas fa-layer-group" style="font-size: 0.85rem; margin-right: 5px;"></i> Rak Arsip
                                            </a>
                                        </li>
                                        <li class="{{ request()->routeIs('klasifikasi.*') ? 'active' : '' }}">
                                            <a href="{{ route('klasifikasi.index') }}" style="color: #f8fafc !important; font-size: 0.9rem !important; padding-left: 25px;">
                                                <i class="fas fa-tags" style="font-size: 0.85rem; margin-right: 5px;"></i> Klasifikasi
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </aside>
            <section role="main" class="content-body">
                <header class="page-header">
                    <h2>@yield('title', 'Dashboard')</h2>
                    <div class="right-wrapper text-end" style="padding-right: 25px;">
                        <ol class="breadcrumbs">
                            <li><a href="{{ route('dashboard') }}"><i class="fas fa-home"></i></a></li>
                            <li><span>App</span></li>
                            <li><span>@yield('title')</span></li>
                        </ol>
                    </div>
                </header>

                <div class="container-fluid py-3 px-0">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-12">
                            @yield('content')
                        </div>
                    </div>
                </div>
            </section>
            </div>
    </section>

    <script src="{{ asset('assets/vendor/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('assets/vendor/nanoscroller/nanoscroller.js') }}"></script>
    
    <script src="{{ asset('assets/js/theme.js') }}"></script>
    <script src="{{ asset('assets/js/theme.init.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>