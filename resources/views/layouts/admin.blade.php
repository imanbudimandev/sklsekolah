<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Favicon -->
    @php
        $favicon = \App\Models\Setting::get('favicon');
        $dashboardLogo = \App\Models\Setting::get('dashboard_logo');
    @endphp
    @if($favicon && file_exists(public_path($favicon)))
        <link rel="icon" type="image/png" href="{{ asset($favicon) }}">
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    @yield('styles')

    <!-- Avoid Browser Caching: Inline Styles for Sidebar Dropdown & Collapse -->
    <style>
        .nav-item-dropdown {
            display: flex;
            flex-direction: column;
        }
        .dropdown-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between !important;
            width: 100%;
        }
        .dropdown-arrow {
            display: inline-block !important;
            font-size: 0.8rem !important;
            transition: transform 0.3s ease;
        }
        .nav-item-dropdown.open .dropdown-arrow {
            transform: rotate(180deg) !important;
        }
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
            background-color: transparent !important; /* Force transparent unless active or hovered */
        }
        .sidebar-nav .submenu-list li a i {
            font-size: 0.95rem !important;
            width: 18px;
            text-align: center;
        }
        .sidebar-nav .submenu-list li:hover a,
        .sidebar-nav .submenu-list li.active-sub a {
            color: #ffffff !important;
            background-color: #1e293b !important;
        }
        .sidebar-nav .submenu-list li.active-sub a i {
            color: #818cf8 !important;
        }

        /* Sidebar Collapse (Hide/Unhide) Transitions */
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
    </style>
</head>
<body class="admin-body">
    <div class="admin-layout">
        <script>
            if (localStorage.getItem('sidebar-collapsed') === 'true') {
                document.querySelector('.admin-layout').classList.add('sidebar-collapsed');
            }
        </script>
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <div class="sidebar-brand">
                @if($dashboardLogo && file_exists(public_path($dashboardLogo)))
                    <img src="{{ asset($dashboardLogo) }}" alt="Logo" class="sidebar-logo" style="max-height: 32px; max-width: 32px; border-radius: 4px; object-fit: contain; margin-right: 4px;">
                @else
                    <i class="fa-solid fa-user-shield"></i>
                @endif
                <span>{{ \App\Models\Setting::get('admin_panel_name', 'Panel Admin') }}</span>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="{{ Route::is('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa-solid fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="{{ Route::is('admin.students') ? 'active' : '' }}">
                        <a href="{{ route('admin.students') }}">
                            <i class="fa-solid fa-user-graduate"></i>
                            <span>Data Siswa</span>
                        </a>
                    </li>
                    <li class="{{ Route::is('admin.subjects') ? 'active' : '' }}">
                        <a href="{{ route('admin.subjects') }}">
                            <i class="fa-solid fa-book"></i>
                            <span>Mata Pelajaran</span>
                        </a>
                    </li>
                    <li class="{{ Route::is('admin.grades') ? 'active' : '' }}">
                        <a href="{{ route('admin.grades') }}">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <span>Manajemen Nilai</span>
                        </a>
                    </li>
                    <li class="nav-item-dropdown {{ Route::is('admin.transcripts*') || Route::is('admin.skl.*') ? 'open active' : '' }}">
                        <a href="#" class="dropdown-toggle">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <i class="fa-solid fa-envelope-open-text"></i>
                                <span>Manajemen Surat</span>
                            </div>
                            <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <ul class="submenu-list {{ Route::is('admin.transcripts*') || Route::is('admin.skl.*') ? 'show' : '' }}">
                            <li class="{{ Route::is('admin.transcripts') ? 'active-sub' : '' }}">
                                <a href="{{ route('admin.transcripts') }}">
                                    <i class="fa-solid fa-print"></i>
                                    <span>Cetak Surat</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('admin.skl.settings') ? 'active-sub' : '' }}">
                                <a href="{{ route('admin.skl.settings') }}">
                                    <i class="fa-solid fa-file-signature"></i>
                                    <span>Setting SKL</span>
                                </a>
                            </li>
                            <li class="{{ Route::is('admin.transcripts.settings') ? 'active-sub' : '' }}">
                                <a href="{{ route('admin.transcripts.settings') }}">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    <span>Setting Transkrip</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="{{ Route::is('admin.settings') ? 'active' : '' }}">
                        <a href="{{ route('admin.settings') }}">
                            <i class="fa-solid fa-gears"></i>
                            <span>Pengaturan</span>
                        </a>
                    </li>
                    <li class="{{ Route::is('admin.tools*') ? 'active' : '' }}">
                        <a href="{{ route('admin.tools') }}">
                            <i class="fa-solid fa-screwdriver-wrench"></i>
                            <span>Database Tools</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <form action="{{ route('admin.logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-content-wrapper">
            <!-- Admin Topbar Header -->
            <header class="admin-topbar">
                <div class="topbar-left" style="display: flex; align-items: center; gap: 15px;">
                    <button type="button" id="sidebar-toggle" class="btn-sidebar-toggle" title="Sembunyikan/Tampilkan Sidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h2 style="margin: 0;">@yield('page_title', 'Dashboard')</h2>
                </div>
                <div class="topbar-right">
                    <a href="{{ route('public.index') }}" target="_blank" class="btn btn-secondary btn-sm">
                        <i class="fa-solid fa-globe"></i> Lihat Portal Publik
                    </a>
                    <span class="user-profile">
                        <i class="fa-regular fa-circle-user"></i> {{ Auth::user()->name }}
                    </span>
                </div>
            </header>

            <!-- Main Panel Body -->
            <main class="admin-main">
                <!-- Session Alerts -->
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Robust event delegation for dropdown menu toggling
            document.addEventListener('click', function(e) {
                const toggle = e.target.closest('.dropdown-toggle');
                if (toggle) {
                    e.preventDefault();
                    e.stopPropagation();
                    const parent = toggle.closest('.nav-item-dropdown');
                    if (parent) {
                        const submenu = parent.querySelector('.submenu-list');
                        parent.classList.toggle('open');
                        if (submenu) {
                            submenu.classList.toggle('show');
                        }
                    }
                }
            });

            // Sidebar Collapse and toggle functionality
            const toggleBtn = document.getElementById('sidebar-toggle');
            const layout = document.querySelector('.admin-layout');
            
            if (toggleBtn && layout) {
                toggleBtn.addEventListener('click', function() {
                    layout.classList.toggle('sidebar-collapsed');
                    const isCollapsed = layout.classList.contains('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', isCollapsed);
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
