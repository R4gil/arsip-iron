<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'IRON SMART') - IRON SMART</title>
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <style>
        :root {
            --primary-dark: #1e3a8a;
            --primary-light: #2563eb;
            --surface: #ffffff;
            --surface-muted: #f8fafc;
            --border: #e5e7eb;
        }

        body { background: #edf2f7; min-height: 100vh; }
        .sidebar {
            width: 260px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, var(--primary-dark), var(--primary-light));
            color: #f8fafc;
            padding: 1.75rem 1.25rem;
            z-index: 20;
        }
        .sidebar .brand {
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #fff;
            text-decoration: none;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.9);
            padding: .85rem 1rem;
            border-radius: .85rem;
            margin-bottom: .35rem;
            transition: background .2s ease, color .2s ease;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,.15);
            color: #fff;
            text-decoration: none;
        }
        .sidebar .nav-link i { width: 1.2rem; }
        .sidebar .sidebar-footer {
            margin-top: auto;
            padding-top: 1.5rem;
            font-size: .85rem;
            color: rgba(255,255,255,.72);
        }
        .layout-wrapper { margin-left: 260px; transition: margin .2s ease; }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 15;
        }
        .page-header {
            display: grid;
            gap: 0.25rem;
        }
        .breadcrumb a { color: var(--primary-dark); }
        .card-soft { background: #fff; border: 1px solid #e5e7eb; }
        .card-header-soft { background: #f8fafc; border-bottom: 1px solid #e5e7eb; }
        .btn-primary { background-color: var(--primary-light); border-color: var(--primary-light); }
        .btn-primary:hover { background-color: #1d4ed8; border-color: #1d4ed8; }
        .badge-soft-primary { background: rgba(37,99,235,.1); color: var(--primary-dark); }
        .text-primary-600 { color: var(--primary-light) !important; }
        @media (max-width: 991.98px) {
            .sidebar { position: relative; width: 100%; min-height: auto; }
            .layout-wrapper { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar d-flex flex-column">
        <div class="mb-4">
            <a href="{{ route('dashboard') }}" class="brand">IRON SMART</a>
            <p class="small text-white-75 mt-2 mb-0">Sistem Monitoring dan Manajemen Arsip Digital</p>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i> Dashboard Arsip</a>
            <a class="nav-link {{ request()->routeIs('arsip.*') ? 'active' : '' }}" href="{{ route('arsip.index') }}"><i class="fas fa-archive me-2"></i> Data Arsip</a>
            <a class="nav-link {{ request()->routeIs('borrowings.*') ? 'active' : '' }}" href="{{ route('borrowings.index') }}"><i class="fas fa-hand-holding-box me-2"></i> Peminjaman Arsip</a>
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="fas fa-users me-2"></i> User</a>
            @if(auth()->user()->role === 'Admin')
                <a class="nav-link {{ request()->routeIs('locations.*') || request()->routeIs('cabinets.*') || request()->routeIs('racks.*') || request()->routeIs('classifications.*') ? 'active' : '' }}" href="{{ route('locations.index') }}"><i class="fas fa-cogs me-2"></i> Control Panel</a>
            @endif
        </nav>
        <div class="sidebar-footer mt-auto">
            <div class="mb-2">Versi 1.0</div>
            <div>Enterprise archive management</div>
        </div>
    </div>

    <div class="layout-wrapper">
        <header class="topbar d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
            <div class="page-header">
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h4 mb-0">@yield('title')</h1>
                </div>
                <p class="text-muted mb-0">@yield('subtitle', 'Sistem arsip dan peminjaman digital yang modern dan responsif.')</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-none d-lg-flex align-items-center gap-2 text-muted small">
                    <i class="fas fa-globe"></i>
                    <span>IRON SMART Dashboard</span>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name ?? 'Tamu' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
                        <li><a class="dropdown-item" href="{{ route('users.edit', auth()->user()->id ?? 0) }}">Profil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="container-fluid py-4 px-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
