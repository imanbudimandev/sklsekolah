<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Siswa') - {{ $settings['school_name'] ?? 'SMP Nurul Ihsan Banjaran' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @php
        $favicon = \App\Models\Setting::get('favicon');
    @endphp
    @if($favicon && file_exists(public_path($favicon)))
        <link rel="icon" type="image/png" href="{{ asset($favicon) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif

    @yield('styles')

    <style>
        .sidebar-nav .submenu-list {
            list-style: none !important;
            padding-left: 24px !important;
            margin-top: 4px;
            display: none !important;
            flex-direction: column;
            gap: 4px;
        }
        .sidebar-nav .submenu-list.show {
            display: flex !important;
        }
        .sidebar-nav .submenu-list li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px !important;
            font-size: 0.88rem !important;
            border-radius: var(--border-radius-sm);
            color: #94a3b8 !important;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition-fast);
            background-color: transparent !important;
        }
        .sidebar-nav .submenu-list li:hover a,
        .sidebar-nav .submenu-list li.active-sub a {
            color: #ffffff !important;
            background-color: #1e293b !important;
        }
        .sidebar-nav .submenu-list li.active-sub a i {
            color: #818cf8 !important;
        }

        /* Colorful Minimalist Sidebar - Student */
        .admin-sidebar {
            background: linear-gradient(180deg, #090d16 0%, #2e1065 100%) !important; /* Deep Dark Purple Gradient */
            border-right: 1px solid rgba(139, 92, 246, 0.12) !important;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .sidebar-brand {
            border-bottom: 1px solid rgba(139, 92, 246, 0.12) !important;
        }
        .sidebar-brand span.brand-text {
            background: linear-gradient(135deg, #f472b6, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }
        .sidebar-nav li a {
            position: relative;
            transition: all 0.25s ease !important;
            border-left: 3px solid transparent !important;
            border-radius: 8px !important;
            margin: 2px 8px !important;
        }
        .sidebar-nav li:hover > a {
            background-color: rgba(139, 92, 246, 0.08) !important;
            color: #ffffff !important;
        }
        .sidebar-nav li.active > a {
            background: rgba(139, 92, 246, 0.16) !important;
            border-left: 3px solid #8b5cf6 !important;
            color: #c084fc !important;
            font-weight: 700;
        }
        .sidebar-nav li.active > a i {
            color: #c084fc !important;
        }
        .sidebar-nav li a i {
            transition: color 0.25s ease;
        }
        .sidebar-nav .submenu-list li a {
            border-left: none !important;
            margin: 2px 0 !important;
        }
        .sidebar-nav .submenu-list li.active-sub a {
            background: rgba(139, 92, 246, 0.12) !important;
            color: #c084fc !important;
            font-weight: 700;
        }
        .sidebar-nav .submenu-list li.active-sub a i {
            color: #c084fc !important;
        }
        .sidebar-footer {
            border-top: 1px solid rgba(139, 92, 246, 0.12) !important;
        }

        /* Sidebar Collapse (Hide/Unhide) Transitions */
        @media (min-width: 769px) {
            .admin-sidebar {
                transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            .admin-content-wrapper {
                transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            /* Sidebar Collapse (Icon Only Mode) */
            .sidebar-collapsed .admin-sidebar {
                width: 70px !important;
                left: 0 !important;
            }
            .sidebar-collapsed .admin-content-wrapper {
                margin-left: 70px !important;
            }
            
            /* Center elements perfectly when collapsed */
            .sidebar-collapsed .sidebar-brand {
                justify-content: center !important;
                padding: 20px 0 !important;
            }
            .sidebar-collapsed .sidebar-brand span {
                display: none !important;
            }
            .sidebar-collapsed .sidebar-brand img {
                margin-right: 0 !important;
            }
            .sidebar-collapsed .sidebar-nav {
                padding: 15px 0 !important;
            }
            .sidebar-collapsed .sidebar-nav ul {
                padding: 0 !important;
                margin: 0 !important;
                align-items: center !important;
            }
            .sidebar-collapsed .sidebar-nav li {
                width: 100% !important;
                display: flex !important;
                justify-content: center !important;
            }
            .sidebar-collapsed .sidebar-nav li a {
                width: 50px !important;
                height: 50px !important;
                justify-content: center !important;
                align-items: center !important;
                padding: 0 !important;
                margin: 4px auto !important;
                gap: 0 !important;
                border-radius: 12px !important;
                display: flex !important;
            }
            .sidebar-collapsed .sidebar-nav li a span {
                display: none !important;
            }
            .sidebar-collapsed .sidebar-nav li a i {
                font-size: 0.92rem !important;
            }
            .sidebar-collapsed .sidebar-footer {
                padding: 15px 0 !important;
                display: flex !important;
                justify-content: center !important;
            }
            .sidebar-collapsed .sidebar-footer form {
                width: 100% !important;
                display: flex !important;
                justify-content: center !important;
            }
            .sidebar-collapsed .btn-logout {
                width: 50px !important;
                height: 50px !important;
                justify-content: center !important;
                align-items: center !important;
                padding: 0 !important;
                margin: 0 auto !important;
                border-radius: 12px !important;
                gap: 0 !important;
            }
            .sidebar-collapsed .btn-logout span {
                display: none !important;
            }
        }

        @media (max-width: 768px) {
            .admin-sidebar {
                transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            .admin-content-wrapper {
                transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            .sidebar-collapsed .admin-sidebar {
                left: -260px !important;
            }
            .sidebar-collapsed .admin-content-wrapper {
                margin-left: 0 !important;
            }
        }
        .btn-sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
            border-radius: var(--border-radius-sm);
            transition: var(--transition-fast);
        }
        .btn-sidebar-toggle:hover {
            background-color: #e0e7ff;
            color: #4f46e5;
        }

        @media (max-width: 768px) {
            .doc-grid {
                grid-template-columns: 1fr !important;
            }
            .profile-wrap {
                grid-template-columns: 1fr !important;
            }
            .info-card.full .info-row {
                flex-direction: column;
                gap: 2px;
            }
            .info-card.full .info-row .label {
                width: auto;
            }
            .grades-table-wrap {
                overflow-x: auto;
            }
            .grades-table-wrap table {
                min-width: 700px;
            }
        }

        @media (max-width: 480px) {
            .admin-main {
                padding: 16px !important;
            }
            .topbar-right .user-profile span {
                display: none;
            }
            .profile-card {
                flex-direction: column !important;
                text-align: center !important;
                padding: 20px !important;
            }
            .profile-card .details {
                justify-content: center;
            }
            .doc-card .doc-preview iframe {
                height: 350px !important;
            }
        }
    </style>
</head>
<body class="admin-body">
    <div class="admin-layout">
        <script>
            if (localStorage.getItem('student-sidebar') === 'true') {
                document.querySelector('.admin-layout').classList.add('sidebar-collapsed');
            }
        </script>
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                @php $dashboardLogo = \App\Models\Setting::get('dashboard_logo'); @endphp
                @if($dashboardLogo && file_exists(public_path($dashboardLogo)))
                    <img src="{{ asset($dashboardLogo) }}" alt="Logo" class="sidebar-logo" style="max-height:32px;max-width:32px;border-radius:4px;object-fit:contain;margin-right:4px;">
                @else
                    <i class="fa-solid fa-graduation-cap"></i>
                @endif
                <span class="brand-text">Portal Siswa</span>
            </div>

            <nav class="sidebar-nav">
                <ul>
                    <li class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('student.dashboard') }}">
                            <i class="fa-solid fa-chart-line" style="color: #10b981;"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('student.profile') ? 'active' : '' }}">
                        <a href="{{ route('student.profile') }}">
                            <i class="fa-solid fa-user-graduate" style="color: #3b82f6;"></i>
                            <span>Data Saya</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('student.documents') ? 'active' : '' }}">
                        <a href="{{ route('student.documents') }}">
                            <i class="fa-solid fa-download" style="color: #f59e0b;"></i>
                            <span>Unduh Dokumen</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <form action="{{ route('student.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fa-solid fa-right-from-bracket" style="color: #f43f5e;"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="admin-content-wrapper">
            <header class="admin-topbar">
                <div class="topbar-left" style="display:flex;align-items:center;gap:15px;">
                    <button type="button" id="sidebar-toggle" class="btn-sidebar-toggle" title="Sembunyikan/Tampilkan Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2 style="margin:0;">@yield('page_title', 'Dashboard')</h2>
                </div>
                <div class="topbar-right">
                    <a href="{{ route('public.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-globe"></i> Beranda
                    </a>
                    <span class="user-profile">
                        <i class="fa-regular fa-circle-user"></i> {{ $student->name }}
                    </span>
                </div>
            </header>

            <main class="admin-main">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade-in">
                        <div class="alert-content">
                            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                        </div>
                        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade-in">
                        <div class="alert-content">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                        </div>
                        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('sidebar-toggle');
            const layout = document.querySelector('.admin-layout');

            if (toggleBtn && layout) {
                toggleBtn.addEventListener('click', function() {
                    layout.classList.toggle('sidebar-collapsed');
                    const isCollapsed = layout.classList.contains('sidebar-collapsed');
                    localStorage.setItem('student-sidebar', isCollapsed);
                });
            }
        });
    </script>
</body>
</html>
