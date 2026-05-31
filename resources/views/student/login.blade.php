@extends('layouts.app')

@section('title', 'Portal Siswa')

@php
    $studentBg = \App\Models\Setting::get('student_login_bg');
    $hasStudentBg = !empty($studentBg) && file_exists(public_path($studentBg));
@endphp

@section('content')
<div class="login-container" style="{{ $hasStudentBg ? 'background-image: url(' . asset($studentBg) . '); background-size: cover; background-position: center; background-repeat: no-repeat; position: relative;' : '' }}">
    @if($hasStudentBg)
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 1;"></div>
    @else
        <!-- Decorative Background Blobs -->
        <div style="position: absolute; top: 10%; left: 15%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(99,102,241,0.25) 0%, rgba(255,255,255,0) 70%); filter: blur(40px); border-radius: 50%; pointer-events: none; z-index: 1; animation: floatBlob1 12s infinite alternate;"></div>
        <div style="position: absolute; bottom: 15%; right: 10%; width: 350px; height: 350px; background: radial-gradient(circle, rgba(236,72,153,0.2) 0%, rgba(255,255,255,0) 70%); filter: blur(50px); border-radius: 50%; pointer-events: none; z-index: 1; animation: floatBlob2 15s infinite alternate-reverse;"></div>
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 400px; height: 400px; background: radial-gradient(circle, rgba(20,184,166,0.18) 0%, rgba(255,255,255,0) 70%); filter: blur(45px); border-radius: 50%; pointer-events: none; z-index: 1;"></div>
    @endif
    <div class="login-card card glass fade-in" style="max-width:420px;margin:0 auto; position: relative; z-index: 2; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.4); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);">
        <div class="login-header">
            <div class="admin-icon">
                @if(!empty($settings['school_logo']) && file_exists(public_path($settings['school_logo'])))
                    <img src="{{ asset($settings['school_logo']) }}" alt="Logo Sekolah" style="max-height:64px;max-width:64px;border-radius:8px;object-fit:contain;">
                @else
                    <i class="fa-solid fa-graduation-cap"></i>
                @endif
            </div>
            <h2>Portal Siswa</h2>
            <p>Masukkan NIS atau NISN untuk melihat hasil kelulusan.</p>
        </div>

        <!-- Colorful Live Running Clock (WIB) -->
        <div style="background: linear-gradient(135deg, #6366f1, #a855f7); border-radius: 16px; padding: 10px 14px; margin-bottom: 24px; text-align: center; color: #ffffff; box-shadow: 0 8px 16px -4px rgba(99, 102, 241, 0.25); position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; left: -10px; width: 40px; height: 40px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
            <div id="live-date" style="font-size: 0.85rem; font-weight: 700; color: #ffffff; margin-bottom: 2px;">
                Loading tanggal...
            </div>
            <div id="live-time" style="font-size: 1.6rem; font-weight: 900; color: #ffffff; letter-spacing: 1px; font-family: monospace;">
                00:00:00 WIB
            </div>
        </div>

        <form action="{{ route('student.login') }}" method="POST" class="login-form">
            @csrf

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
                </div>
            @endif

            <div class="form-group">
                <label for="nis"><i class="fa-solid fa-id-card"></i> NIS / NISN</label>
                <input type="text" id="nis" name="nis" placeholder="Masukkan NIS atau NISN" value="{{ old('nis') }}" autofocus>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </button>
        </form>

    </div>

    <!-- Footer -->
    <footer class="portal-footer" style="position: relative; z-index: 2; margin-top: 40px; text-align: center; font-size: 0.82rem; font-weight: 600; width: 100%; max-width: 420px; margin-left: auto; margin-right: auto; {{ $hasStudentBg ? 'border-top: none !important; color: #ffffff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.5);' : 'border-top: 1px solid rgba(0,0,0,0.08); padding-top: 16px; color: #64748b;' }}">
        <div>
            © {{ date('Y') }} {{ $settings['school_name'] ?? 'Sistem Informasi Kelulusan' }} - All Rights Reserved
        </div>
    </footer>
</div>
@endsection

@section('styles')
<style>
    /* Premium Colorful Form Styles */
    .form-group label {
        font-weight: 700;
        font-size: 0.88rem;
        color: #4c1d95 !important; /* Deep Purple */
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .form-group label i {
        color: #6366f1; /* Indigo icon */
        font-size: 1rem;
    }
    .form-group input {
        background: rgba(255, 255, 255, 0.65) !important;
        border: 1.5px solid rgba(99, 102, 241, 0.2) !important;
        border-radius: 12px !important;
        padding: 14px 16px !important;
        font-size: 0.98rem !important;
        font-weight: 600 !important;
        color: #0f172a !important;
        outline: none !important;
        transition: all 0.25s ease !important;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02) !important;
        width: 100%;
        box-sizing: border-box;
    }
    .form-group input:focus {
        background: #ffffff !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15), inset 0 2px 4px rgba(0,0,0,0.01) !important;
    }
    /* Gradient Button */
    .btn-primary.btn-block {
        background: linear-gradient(135deg, #6366f1, #a855f7) !important;
        border: none !important;
        border-radius: 12px !important;
        padding: 14px 24px !important;
        font-size: 1.05rem !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 10px !important;
        transition: all 0.25s ease !important;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25) !important;
        width: 100%;
        text-transform: none;
    }
    .btn-primary.btn-block:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(99, 102, 241, 0.35) !important;
    }
    .btn-primary.btn-block:active {
        transform: translateY(0);
    }
    
    @keyframes floatBlob1 {
        0% { transform: translateY(0px) scale(1); }
        100% { transform: translateY(-30px) scale(1.05); }
    }
    @keyframes floatBlob2 {
        0% { transform: translateY(0px) scale(1); }
        100% { transform: translateY(40px) scale(0.95); }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
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
@endsection
