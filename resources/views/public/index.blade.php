@extends('layouts.app')

@section('title', 'Info Kelulusan Online')

@php
    $studentBg = \App\Models\Setting::get('student_login_bg');
    $hasStudentBg = !empty($studentBg) && file_exists(public_path($studentBg));
@endphp

@section('styles')
<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --primary-light: #e0e7ff;
        --success: #10b981;
        --danger: #ef4444;
        --slate-900: #0f172a;
        --slate-800: #1e293b;
        --slate-700: #334155;
        --slate-600: #475569;
        --slate-500: #64748b;
        --slate-100: #f1f5f9;
        --slate-50: #f8fafc;
    }

    body {
        background-color: #f1f5f9 !important;
        background-image: 
            radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%), 
            radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.05) 0px, transparent 50%),
            radial-gradient(at 50% 100%, rgba(99, 102, 241, 0.03) 0px, transparent 50%) !important;
        color: var(--slate-900) !important;
        font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        box-sizing: border-box;
    }

    .public-wrapper {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
        padding: 60px 20px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    /* Portal Header */
    .portal-header {
        text-align: center;
        margin-bottom: 40px;
        animation: fadeInDown 0.6s ease-out;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .school-logo {
        height: 100px;
        width: auto;
        object-fit: contain;
        margin-bottom: 24px;
        filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.03));
    }

    .portal-title {
        font-size: 2.2rem;
        font-weight: 800;
        color: var(--slate-900);
        margin: 0 0 8px 0;
        letter-spacing: -0.03em;
        line-height: 1.2;
    }

    .portal-subtitle {
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--slate-500);
        margin: 0;
        letter-spacing: -0.01em;
    }

    /* Main Action Card */
    .panel-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 24px;
        padding: 48px;
        width: 100%;
        max-width: 580px;
        box-sizing: border-box;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.02), 0 10px 10px -5px rgba(0, 0, 0, 0.01), inset 0 1px 0 #ffffff;
        transition: all 0.3s ease;
        animation: fadeInUp 0.6s ease-out 0.1s both;
    }

    .panel-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 25px 40px -10px rgba(0, 0, 0, 0.04);
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Countdown Layout */
    .countdown-box {
        background: var(--slate-50);
        border: 1px solid var(--slate-100);
        border-radius: 18px;
        padding: 20px;
        margin-bottom: 32px;
        text-align: center;
    }

    .countdown-box-title {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 12px;
    }

    .timer-row {
        display: flex;
        justify-content: center;
        gap: 12px;
    }

    .timer-col {
        border-radius: 16px;
        min-width: 75px;
        padding: 14px 10px;
        color: #ffffff;
        text-align: center;
        transition: all 0.3s ease;
    }

    .timer-col:hover {
        transform: scale(1.05) translateY(-3px);
    }

    .timer-val {
        font-size: 2.1rem;
        font-weight: 900;
        display: block;
        line-height: 1;
        margin-bottom: 4px;
        color: #ffffff;
        text-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }

    .timer-lbl {
        font-size: 0.65rem;
        font-weight: 800;
        color: rgba(255, 255, 255, 0.95);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Premium Form Inputs */
    .input-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--slate-700);
        margin-bottom: 8px;
        text-align: left;
    }

    .input-group-custom {
        position: relative;
        margin-bottom: 24px;
    }

    .input-group-custom i {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--slate-500);
        font-size: 1.1rem;
        transition: color 0.2s;
    }

    .input-field-custom {
        width: 100%;
        background: var(--slate-50);
        border: 1.5px solid var(--slate-100);
        border-radius: 16px;
        padding: 16px 20px 16px 52px;
        font-size: 1rem;
        font-weight: 600;
        color: var(--slate-900);
        box-sizing: border-box;
        outline: none;
        transition: all 0.2s ease;
    }

    .input-field-custom:focus {
        background: #ffffff;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .input-field-custom:focus ~ i {
        color: var(--primary);
    }

    /* Button Action */
    .btn-action {
        width: 100%;
        background: var(--slate-900);
        color: #ffffff;
        border: none;
        border-radius: 16px;
        padding: 16px 24px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.1);
    }

    .btn-action:hover {
        background: var(--slate-800);
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.15);
    }

    .btn-action:active {
        transform: translateY(0);
    }

    .form-tip {
        display: block;
        font-size: 0.75rem;
        font-weight: 500;
        color: var(--slate-500);
        margin-top: 14px;
    }

    .spinner-custom {
        display: none;
        width: 18px;
        height: 18px;
        border: 2.5px solid rgba(255,255,255,0.2);
        border-radius: 50%;
        border-top-color: #ffffff;
        animation: spin 0.6s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Alerts */
    .alert-custom {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 16px;
        padding: 16px 20px;
        color: #991b1b;
        font-size: 0.9rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        text-align: left;
        animation: fadeInUp 0.4s ease-out;
    }

    /* Student Result Card (WIDE & Elegant) */
    .result-panel {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 28px;
        padding: 48px;
        width: 100%;
        max-width: 1050px;
        box-sizing: border-box;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.01), 0 10px 10px -5px rgba(0, 0, 0, 0.01);
        margin-top: 40px;
        animation: fadeInUp 0.6s ease-out 0.2s both;
    }

    .result-head-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        border-bottom: 1.5px solid var(--slate-100);
        padding-bottom: 28px;
        margin-bottom: 32px;
    }

    .student-info-main h3 {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--slate-900);
        margin: 0 0 6px 0;
        letter-spacing: -0.03em;
    }

    .student-info-main p {
        font-size: 0.95rem;
        font-weight: 600;
        color: var(--slate-500);
        margin: 0;
    }

    .badge-status {
        font-size: 0.9rem;
        font-weight: 800;
        padding: 12px 28px;
        border-radius: 14px;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .badge-status-lulus {
        background: #ecfdf5;
        border: 1.5px solid #a7f3d0;
        color: #065f46;
        animation: pulse-emerald 2s infinite;
    }

    @keyframes pulse-emerald {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.2); }
        70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .badge-status-tidak {
        background: #fef2f2;
        border: 1.5px solid #fecaca;
        color: #991b1b;
    }

    /* Meta Grid */
    .profile-meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .meta-item-box {
        background: var(--slate-50);
        border: 1px solid var(--slate-100);
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: all 0.2s;
    }

    .meta-item-box:hover {
        background: var(--slate-100);
        transform: translateY(-2px);
    }

    .meta-item-icon {
        background: #ffffff;
        color: var(--primary);
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .meta-item-lbl {
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--slate-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: block;
        margin-bottom: 2px;
    }

    .meta-item-val {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--slate-900);
    }

    /* Grades Table redesign */
    .table-container-custom {
        background: #ffffff;
        border: 1px solid var(--slate-100);
        border-radius: 18px;
        overflow-x: auto;
        margin-bottom: 32px;
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        min-width: 750px;
    }

    .table-custom th {
        background: var(--slate-50);
        color: var(--slate-600);
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 18px 16px;
        border-bottom: 2px solid var(--slate-100);
    }

    .table-custom td {
        padding: 16px 16px;
        border-bottom: 1px solid var(--slate-50);
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--slate-700);
    }

    .table-custom tbody tr:hover {
        background: var(--slate-50);
    }

    .table-score-highlight {
        font-weight: 800 !important;
        color: var(--primary) !important;
        text-align: center;
        background: rgba(99, 102, 241, 0.02);
        border-left: 1.5px solid var(--slate-100);
    }

    .table-average-row {
        background: var(--slate-50) !important;
    }

    .table-average-row td {
        border-top: 2px solid var(--slate-100);
        padding: 20px 16px;
        font-weight: 800;
        color: var(--slate-900);
    }

    .average-val-display {
        font-size: 1.2rem !important;
        color: var(--primary) !important;
        font-weight: 900 !important;
        text-align: center;
        background: rgba(99, 102, 241, 0.06);
    }

    /* Actions Download */
    .download-row {
        display: flex;
        justify-content: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .btn-download-premium {
        padding: 16px 36px;
        border-radius: 16px;
        font-size: 0.95rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }

    .btn-download-premium:hover {
        transform: translateY(-2px);
    }

    .btn-skl-green {
        background: #10b981;
        color: #ffffff;
    }

    .btn-skl-green:hover {
        background: #059669;
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.2);
    }

    .btn-transkrip-blue {
        background: var(--primary);
        color: #ffffff;
    }

    .btn-transkrip-blue:hover {
        background: var(--primary-hover);
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.2);
    }

    .responsive-hint {
        display: none;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.72rem;
        color: var(--slate-500);
        font-weight: 700;
        margin-top: 12px;
    }

    /* Footer styles */
    .portal-footer {
        width: 100%;
        margin-top: 60px;
        padding-top: 24px;
        border-top: 1px solid var(--slate-100);
        text-align: center;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--slate-500);
        animation: fadeInDown 0.6s ease-out 0.3s both;
    }

    .visitor-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(99, 102, 241, 0.06);
        color: var(--primary);
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 700;
        margin-left: 8px;
    }

    /* Responsive Breakpoints */
    @media (max-width: 768px) {
        .panel-card {
            padding: 32px 24px;
        }

        .result-panel {
            padding: 32px 20px;
        }

        .result-head-row {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .badge-status {
            width: 100%;
            justify-content: center;
        }

        .profile-meta-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .download-row {
            flex-direction: column;
        }

        .btn-download-premium {
            width: 100%;
            justify-content: center;
        }

        .responsive-hint {
            display: flex;
        }
    }

    @media (max-width: 480px) {
        .profile-meta-grid {
            grid-template-columns: 1fr;
        }

        .timer-row {
            gap: 6px;
        }

        .timer-col {
            min-width: 50px;
            padding: 8px;
        }

        .timer-val {
            font-size: 1.3rem;
        }
    }
</style>
@endsection

@section('content')
<div style="{{ $hasStudentBg ? 'background-image: url(' . asset($studentBg) . '); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative; min-height: 100vh; width: 100%; display: flex; align-items: center; justify-content: center;' : '' }}">
    @if($hasStudentBg)
        <!-- Blur and Dark overlay -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 1;"></div>
    @endif
    <div class="public-wrapper" style="position: relative; z-index: 2; width: 100%;">
        <!-- Header -->
        <header class="portal-header">
            @if(!empty($settings['school_logo']))
                <img src="{{ asset($settings['school_logo']) }}" alt="Logo Sekolah" class="school-logo">
            @else
                <img src="https://upload.wikimedia.org/wikipedia/commons/e/e0/Lambang_Kabupaten_Tegal.png" alt="Logo Tegal" class="school-logo">
            @endif
            <h1 class="portal-title" style="{{ $hasStudentBg ? 'color: #ffffff; text-shadow: 0 2px 4px rgba(0,0,0,0.3);' : '' }}">{{ \App\Models\Setting::get('admin_panel_name', 'Informasi Kelulusan Siswa') }}</h1>
            <p class="portal-subtitle" style="{{ $hasStudentBg ? 'color: rgba(255,255,255,0.85); text-shadow: 0 1px 2px rgba(0,0,0,0.2);' : '' }} text-transform: uppercase;">{{ $settings['school_name'] ?? 'SMP NURUL IHSAN BANJARAN' }}</p>
        </header>

        <!-- Pengecekan Panel -->
        <div class="panel-card" style="{{ $hasStudentBg ? 'background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.4); box-shadow: 0 20px 45px rgba(0, 0, 0, 0.15);' : '' }}">
        <!-- Countdown Section -->
        @if(!$isAnnounced && $announcementDate)
            <!-- Real-time Current Clock Card (Vibrant Gradient) -->
            <div style="background: linear-gradient(135deg, #4f46e5, #ec4899); border-radius: 20px; padding: 14px 20px; margin-bottom: 24px; text-align: center; color: #ffffff; box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.35); animation: fadeInUp 0.6s ease-out; position: relative; overflow: hidden;">
                <!-- Decorative background elements -->
                <div style="position: absolute; top: -20px; left: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.12); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -30px; right: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
                
                <div id="live-date" style="font-size: 1rem; font-weight: 700; color: #ffffff; margin-bottom: 4px; text-shadow: 0 1px 2px rgba(0,0,0,0.15);">
                    Loading tanggal...
                </div>
                <div id="live-time" style="font-size: 2.2rem; font-weight: 900; color: #ffffff; letter-spacing: 1.5px; font-family: monospace; text-shadow: 0 2px 4px rgba(0,0,0,0.25);">
                    00:00:00 WIB
                </div>
            </div>

            <div class="countdown-box" style="margin-bottom: 0; {{ $hasStudentBg ? 'background: rgba(255, 255, 255, 0.45); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5);' : '' }}">
                <div class="countdown-box-title" style="color: var(--slate-800); font-weight: 800; font-size: 0.85rem;">
                    <i class="fa-solid fa-hourglass-start" style="color: #4f46e5;"></i> PENGUMUMAN DIBUKA DALAM
                </div>
                <div class="timer-row">
                    <div class="timer-col" style="background: linear-gradient(135deg, #f59e0b, #ef4444); box-shadow: 0 6px 16px rgba(239, 68, 68, 0.25);">
                        <span id="days" class="timer-val">00</span>
                        <span class="timer-lbl">Hari</span>
                    </div>
                    <div class="timer-col" style="background: linear-gradient(135deg, #8b5cf6, #6366f1); box-shadow: 0 6px 16px rgba(99, 102, 241, 0.25);">
                        <span id="hours" class="timer-val">00</span>
                        <span class="timer-lbl">Jam</span>
                    </div>
                    <div class="timer-col" style="background: linear-gradient(135deg, #0ea5e9, #3b82f6); box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25);">
                        <span id="minutes" class="timer-val">00</span>
                        <span class="timer-lbl">Menit</span>
                    </div>
                    <div class="timer-col" style="background: linear-gradient(135deg, #10b981, #06b6d4); box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);">
                        <span id="seconds" class="timer-val">00</span>
                        <span class="timer-lbl">Detik</span>
                    </div>
                </div>
            </div>

        @else
            <form action="{{ route('public.index') }}" method="GET" id="checkerForm">
                <div style="margin-bottom: 20px;">
                    <label for="search_input" class="input-label">Nomor Induk Siswa Nasional (NISN)</label>
                    <div class="input-group-custom">
                        <input type="text" id="search_input" name="search" class="input-field-custom" placeholder="Masukkan NISN Anda..." value="{{ $searchQuery }}" required autocomplete="off">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>

                <button type="submit" class="btn-action" id="btnSubmit">
                    <span class="spinner-custom" id="btnSpinner"></span>
                    <span id="btnText"><i class="fa-solid fa-magnifying-glass"></i> Periksa Kelulusan</span>
                </button>
                <span class="form-tip">Masukkan 10 digit NISN terdaftar Anda untuk melihat hasil kelulusan.</span>
            </form>

            @if($error)
                <div class="alert-custom">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 1.15rem;"></i> {{ $error }}
                </div>
            @endif
        @endif
    </div>

    <!-- Student Result Card -->
    @if($student)
        <div class="result-panel">
            <div class="result-head-row">
                <div class="student-info-main">
                    <h3>{{ $student->name }}</h3>
                    <p>Siswa / Siswi Kelas {{ $student->class }}</p>
                </div>
                
                @if($student->status === 'LULUS')
                    <div class="badge-status badge-status-lulus">
                        <i class="fa-solid fa-circle-check"></i> LULUS
                    </div>
                @else
                    <div class="badge-status badge-status-tidak">
                        <i class="fa-solid fa-circle-xmark"></i> TIDAK LULUS
                    </div>
                @endif
            </div>

            <!-- Profile Meta Grid -->
            <div class="profile-meta-grid">
                <div class="meta-item-box">
                    <div class="meta-item-icon"><i class="fa-solid fa-fingerprint"></i></div>
                    <div class="meta-text">
                        <span class="meta-item-lbl">NISN</span>
                        <span class="meta-item-val">{{ $student->nisn }}</span>
                    </div>
                </div>
                <div class="meta-item-box">
                    <div class="meta-item-icon"><i class="fa-solid fa-id-card-clip"></i></div>
                    <div class="meta-text">
                        <span class="meta-item-lbl">NIS</span>
                        <span class="meta-item-val">{{ $student->nis ?? '-' }}</span>
                    </div>
                </div>
                <div class="meta-item-box">
                    <div class="meta-item-icon"><i class="fa-solid fa-school"></i></div>
                    <div class="meta-text">
                        <span class="meta-item-lbl">Kelas</span>
                        <span class="meta-item-val">{{ $student->class }}</span>
                    </div>
                </div>
                @if($student->jurusan)
                <div class="meta-item-box">
                    <div class="meta-item-icon"><i class="fa-solid fa-compass"></i></div>
                    <div class="meta-text">
                        <span class="meta-item-lbl">Jurusan</span>
                        <span class="meta-item-val">{{ $student->jurusan }}</span>
                    </div>
                </div>
                @endif
                <div class="meta-item-box">
                    <div class="meta-item-icon"><i class="fa-solid fa-cake-candles"></i></div>
                    <div class="meta-text">
                        <span class="meta-item-lbl">TTL</span>
                        <span class="meta-item-val" style="font-size: 0.85rem;">{{ $student->birth_place }}, {{ $student->birth_date_formatted }}</span>
                    </div>
                </div>
            </div>

            <!-- Grades Table -->
            <div style="margin-bottom: 24px;">
                <h4 style="font-size: 0.95rem; font-weight: 800; text-transform: uppercase; color: var(--slate-800); letter-spacing: 0.05em; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-clipboard-list" style="color: var(--primary);"></i> Transkrip Nilai Rapor
                </h4>
                
                <div class="table-container-custom">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th style="width: 50px; text-align: center;">No</th>
                                <th>Mata Pelajaran</th>
                                <th style="text-align: center; width: 62px;">Smt 1</th>
                                <th style="text-align: center; width: 62px;">Smt 2</th>
                                <th style="text-align: center; width: 62px;">Smt 3</th>
                                <th style="text-align: center; width: 62px;">Smt 4</th>
                                <th style="text-align: center; width: 62px;">Smt 5</th>
                                <th style="text-align: center; width: 62px;">Smt 6</th>
                                <th style="text-align: center; width: 110px;">Nilai Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($subjects as $subject)
                                @php
                                    $subGrades = $student->grades->where('subject_id', $subject->id);
                                    $finalGrade = $student->calculateFinalGradeForSubject($subject->id);
                                @endphp
                                <tr>
                                    <td style="text-align: center; font-weight: 700; color: var(--slate-500);">{{ $no++ }}</td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--slate-900);">{{ $subject->name }}</div>
                                        <div style="font-size: 0.72rem; color: var(--slate-500); font-weight: 600;">{{ $subject->code }}</div>
                                    </td>
                                    @for($i = 1; $i <= 6; $i++)
                                        @php
                                            $semGrade = $subGrades->where('semester', "Semester $i")->first();
                                        @endphp
                                        <td style="text-align: center; font-weight: 600; color: var(--slate-600);">{{ $semGrade ? number_format($semGrade->score, 0) : '-' }}</td>
                                    @endfor
                                    <td class="table-score-highlight">
                                        {{ $finalGrade !== null ? number_format($finalGrade, 1) : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="table-average-row">
                                <td colspan="8" style="text-align: right; font-weight: 800; color: var(--slate-900);">Rata-Rata Nilai Akhir:</td>
                                <td class="average-val-display">
                                    {{ number_format($student->average_score, 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="responsive-hint">
                    <i class="fa-solid fa-left-right"></i> Geser tabel ke kanan untuk melihat rincian semester
                </div>
            </div>

            <!-- Actions row -->
            <div class="download-row">
                @if($student->status === 'LULUS')
                    <a href="{{ route('public.skl.pdf', $student->id) }}" class="btn-download-premium btn-skl-green" target="_blank">
                        <i class="fa-solid fa-file-pdf"></i> Cetak SKL (PDF)
                    </a>
                    <a href="{{ route('public.transcript.pdf', $student->id) }}" class="btn-download-premium btn-transkrip-blue" target="_blank">
                        <i class="fa-solid fa-file-lines"></i> Download Transkrip Nilai
                    </a>
                @else
                    <div style="width: 100%; padding: 18px 24px; background: #fffbeb; border: 1.5px solid #fef3c7; border-radius: 16px; color: #b45309; font-size: 0.92rem; font-weight: 600; text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem;"></i> Informasi kelulusan belum lengkap. Silakan berkonsultasi langsung dengan wali kelas atau pihak sekolah.
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Footer -->
    <footer class="portal-footer" style="{{ $hasStudentBg ? 'border-top: none !important; color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.5);' : '' }}">
        <div>
            © {{ date('Y') }} {{ $settings['school_name'] ?? 'Sistem Informasi Kelulusan' }} - All Rights Reserved
        </div>
    </footer>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkerForm = document.getElementById("checkerForm");
        const btnSubmit = document.getElementById("btnSubmit");
        const btnSpinner = document.getElementById("btnSpinner");
        const btnText = document.getElementById("btnText");

        if (checkerForm && btnSubmit) {
            checkerForm.addEventListener("submit", function() {
                btnSubmit.disabled = true;
                btnSubmit.style.opacity = "0.8";
                btnSubmit.style.cursor = "not-allowed";
                btnSpinner.style.display = "inline-block";
                btnText.innerHTML = "Memproses Data...";
            });
        }
    });
</script>

@if(!$isAnnounced && $announcementDate)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const countdownDate = new Date("{{ $announcementDate->toIso8601String() }}").getTime();
        
        const updateTimer = function() {
            const now = new Date().getTime();
            const distance = countdownDate - now;
            
            if (distance < 0) {
                clearInterval(timerInterval);
                window.location.reload();
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            document.getElementById("days").innerText = String(days).padStart(2, '0');
            document.getElementById("hours").innerText = String(hours).padStart(2, '0');
            document.getElementById("minutes").innerText = String(minutes).padStart(2, '0');
            document.getElementById("seconds").innerText = String(seconds).padStart(2, '0');
        };
        
        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);

        // Live Running Clock (WIB)
        const updateLiveClock = function() {
            const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Asia/Jakarta' };
            const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Jakarta' };
            
            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', optionsDate);
            const timeStr = now.toLocaleTimeString('id-ID', optionsTime);
            
            const liveDateEl = document.getElementById("live-date");
            const liveTimeEl = document.getElementById("live-time");
            
            if (liveDateEl) liveDateEl.innerText = dateStr;
            if (liveTimeEl) liveTimeEl.innerText = timeStr + " WIB";
        };
        updateLiveClock();
        setInterval(updateLiveClock, 1000);
    });
</script>
@endif
@endsection
