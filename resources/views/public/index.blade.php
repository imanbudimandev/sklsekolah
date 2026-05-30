@extends('layouts.app')

@section('title', 'Cek Kelulusan Siswa')

@section('styles')
<style>
    /* ==========================================================================
       THEME: MODERN ACADEMIC — Clean, Airy, Professional
       ========================================================================== */

    body {
        background: linear-gradient(160deg, #f0f4ff 0%, #e8edf5 50%, #f5f0ff 100%) !important;
        color: #1e293b !important;
        font-family: 'Inter', 'Plus Jakarta Sans', -apple-system, sans-serif !important;
        margin: 0;
        padding: 0;
        min-height: 100vh;
    }

    .public-container {
        max-width: 820px !important;
        margin: 0 auto !important;
        padding: 60px 20px 80px !important;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-sizing: border-box;
    }

    /* ── Top Branding ── */
    .public-header {
        text-align: center;
        margin-bottom: 36px;
        width: 100%;
    }

    .brand-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .school-logo-container {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 72px;
        height: 72px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04), 0 8px 24px -8px rgba(79, 70, 229, 0.12);
        border: 1px solid rgba(226, 232, 240, 0.8);
        flex-shrink: 0;
    }

    .school-logo-img {
        height: 48px;
        width: 48px;
        object-fit: contain;
    }

    .school-logo-placeholder {
        font-size: 2rem;
        color: #6366f1;
    }

    .brand-text {
        text-align: left;
    }

    .brand-text h1 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.3;
        letter-spacing: -0.03em;
    }

    .brand-text .school-address {
        font-size: 0.8rem;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
    }

    .brand-text .school-address i {
        color: #6366f1;
    }

    .brand-badge {
        display: inline-block;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        padding: 4px 14px;
        border-radius: 99px;
        margin-top: 8px;
    }

    /* ── Card ── */
    .card-glass {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.6);
        border-radius: 24px;
        padding: 40px;
        width: 100%;
        box-sizing: border-box;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 20px 40px -12px rgba(79, 70, 229, 0.08);
        transition: box-shadow 0.25s ease;
    }

    .card-glass:hover {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02), 0 24px 48px -12px rgba(79, 70, 229, 0.12);
    }

    .portal-wrapper {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ── Search ── */
    .search-card {
        text-align: center;
    }

    .search-card h2 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .search-card > p {
        color: #64748b;
        font-size: 0.875rem;
        margin-bottom: 28px;
    }

    .search-form .input-group {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 4px;
        transition: all 0.2s ease;
    }

    .search-form .input-group:focus-within {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        background: #ffffff;
    }

    .search-icon {
        color: #94a3b8;
        font-size: 1rem;
        margin-left: 16px;
        flex-shrink: 0;
    }

    .search-form input {
        border: none;
        outline: none;
        background: transparent;
        padding: 14px 12px;
        font-size: 0.95rem;
        color: #0f172a;
        flex-grow: 1;
        font-family: inherit;
        font-weight: 500;
        min-width: 0;
    }

    .search-form input::placeholder {
        color: #94a3b8;
    }

    .search-form button {
        border-radius: 12px;
        padding: 14px 28px;
        font-weight: 600;
        font-size: 0.85rem;
        background: #6366f1;
        color: #fff;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .search-form button:hover {
        background: #4f46e5;
        transform: translateY(-1px);
    }

    /* ── Alert ── */
    .alert {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 14px 18px;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.85rem;
        margin-top: 18px;
        text-align: left;
    }

    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
    }

    .alert-info {
        background: #f0f4ff;
        border: 1px solid #c7d2fe;
        color: #4338ca;
        justify-content: center;
    }

    /* ── Student Meta ── */
    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 0;
    }

    .student-meta .selamat-text {
        color: #059669;
        font-weight: 700;
        font-size: 0.85rem;
        margin-bottom: 4px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .student-meta h3 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px 0;
        letter-spacing: -0.02em;
    }

    .student-meta .meta-tags {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .meta-tag {
        background: #f1f5f9;
        padding: 4px 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        font-size: 0.75rem;
        font-weight: 600;
        color: #475569;
    }

    .meta-tag strong {
        color: #0f172a;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 800;
        padding: 8px 20px;
        border-radius: 12px;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        flex-shrink: 0;
    }

    .status-lulus {
        background: #d1fae5;
        color: #065f46;
        border: 1.5px solid #6ee7b7;
    }

    .status-tidak-lulus {
        background: #fee2e2;
        color: #991b1b;
        border: 1.5px solid #fca5a5;
    }

    .divider {
        border: 0;
        height: 1px;
        background: #e2e8f0;
        margin: 24px 0;
    }

    /* ── Grades Table ── */
    .grades-section h4 {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #64748b;
        margin-bottom: 16px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .grades-section h4 i {
        color: #6366f1;
    }

    .table-responsive {
        border-radius: 16px;
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .table-grades {
        width: 100%;
        border-collapse: collapse;
        color: #334155;
        min-width: 640px;
    }

    .table-grades th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 10px;
        border-bottom: 1.5px solid #e2e8f0;
        text-align: left;
        white-space: nowrap;
    }

    .table-grades th.text-center {
        text-align: center;
    }

    .table-grades td {
        padding: 12px 10px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }

    .table-grades tbody tr:last-child td {
        border-bottom: none;
    }

    .table-grades tbody tr:hover {
        background: #f8fafc;
    }

    .subject-name {
        font-weight: 600;
        color: #0f172a;
    }

    .subject-code {
        font-size: 0.65rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .td-score-val {
        font-weight: 700;
        font-size: 0.85rem;
        color: #6366f1;
        text-align: center;
        background: #f8faff;
        border-left: 1px solid #e2e8f0;
    }

    .row-average {
        background: #f8fafc !important;
    }

    .row-average td {
        border-top: 1.5px solid #cbd5e1;
        padding: 16px 10px;
        font-weight: 700;
        color: #0f172a;
    }

    .score-avg {
        font-size: 1.1rem;
        color: #6366f1;
        font-weight: 900;
        text-align: center;
    }

    /* ── Buttons ── */
    .result-actions {
        display: flex;
        justify-content: center;
        gap: 14px;
        margin-top: 28px;
        flex-wrap: wrap;
    }

    .btn-download {
        padding: 14px 32px;
        font-size: 0.85rem;
        border-radius: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }

    .btn-download:hover {
        transform: translateY(-2px);
    }

    .btn-download-skl {
        background: linear-gradient(135deg, #059669, #047857);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
    }

    .btn-download-skl:hover {
        box-shadow: 0 6px 20px rgba(5, 150, 105, 0.35);
    }

    .btn-download-transkrip {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25);
    }

    .btn-download-transkrip:hover {
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
    }

    /* ── Countdown ── */
    .countdown-wrapper {
        text-align: center;
    }

    .countdown-wrapper h2 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .countdown-wrapper > p {
        color: #64748b;
        font-size: 0.875rem;
        margin-bottom: 28px;
    }

    .countdown-timer {
        display: flex;
        justify-content: center;
        gap: 14px;
        margin-bottom: 28px;
    }

    .timer-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        width: 78px;
        padding: 16px 0 12px;
    }

    .timer-num {
        font-size: 1.8rem;
        font-weight: 800;
        color: #6366f1;
        line-height: 1;
        margin-bottom: 4px;
    }

    .timer-label {
        font-size: 0.65rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .announcement-date-text {
        font-size: 0.8rem;
        background: #f8fafc;
        padding: 10px 20px;
        border-radius: 99px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .announcement-date-text i {
        color: #f59e0b;
    }

    /* ── Result Card (different top radius when stacked) ── */
    .result-card {
        border-top-left-radius: 0 !important;
        border-top-right-radius: 0 !important;
        margin-top: -24px;
        padding-top: 36px !important;
    }

    .search-card + .result-card {
        margin-top: -28px;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .public-container {
            padding: 32px 16px 60px !important;
        }

        .card-glass {
            padding: 28px 16px !important;
            border-radius: 20px !important;
        }

        .brand-row {
            flex-direction: column;
            text-align: center;
        }

        .brand-text {
            text-align: center;
        }

        .brand-text h1 {
            font-size: 1.25rem;
        }

        .result-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .status-badge {
            width: 100%;
            justify-content: center;
            box-sizing: border-box;
        }

        .result-actions {
            flex-direction: column;
            width: 100%;
        }

        .btn-download {
            width: 100%;
            justify-content: center;
            box-sizing: border-box;
        }

        .result-card {
            border-top-left-radius: 20px !important;
            border-top-right-radius: 20px !important;
            margin-top: 0 !important;
            padding-top: 28px !important;
        }

        .countdown-timer {
            gap: 8px;
        }

        .timer-box {
            width: 64px;
            padding: 12px 0 10px;
        }

        .timer-num {
            font-size: 1.4rem;
        }
    }
</style>
@endsection

@section('content')
<div class="public-container">
    <!-- Branding -->
    <header class="public-header">
        <div class="brand-row">
            <div class="school-logo-container">
                @if(!empty($settings['school_logo']) && file_exists(public_path($settings['school_logo'])))
                    <img src="{{ asset($settings['school_logo']) }}" alt="Logo Sekolah" class="school-logo-img">
                @else
                    <div class="school-logo-placeholder">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                @endif
            </div>
            <div class="brand-text">
                <h1>{{ $settings['school_name'] }}</h1>
                <p class="school-address"><i class="fa-solid fa-location-dot"></i> {{ $settings['school_address'] ?: 'Kecamatan Banjaran, Kabupaten Bandung, Jawa Barat' }}</p>
                <span class="brand-badge">Cek Kelulusan</span>
            </div>
        </div>
    </header>

    @if(!$isAnnounced)
        <!-- Countdown -->
        <div class="card-glass countdown-wrapper">
            <h2>Pengumuman Kelulusan</h2>
            <p>Pengumuman kelulusan siswa kelas IX akan dibuka dalam:</p>
            <div class="countdown-timer">
                <div class="timer-box">
                    <span id="days" class="timer-num">00</span>
                    <span class="timer-label">Hari</span>
                </div>
                <div class="timer-box">
                    <span id="hours" class="timer-num">00</span>
                    <span class="timer-label">Jam</span>
                </div>
                <div class="timer-box">
                    <span id="minutes" class="timer-num">00</span>
                    <span class="timer-label">Menit</span>
                </div>
                <div class="timer-box">
                    <span id="seconds" class="timer-num">00</span>
                    <span class="timer-label">Detik</span>
                </div>
            </div>
            <p class="announcement-date-text">
                <i class="fa-regular fa-calendar-days"></i>
                {{ $announcementDate ? $announcementDate->translatedFormat('l, d F Y \p\u\k\u\l H:i') : '-' }} WIB
            </p>
        </div>
    @else
        <div class="portal-wrapper">
            <!-- Search Card -->
            <div class="card-glass search-card">
                <h2>Cek Status Kelulusan</h2>
                <p>Masukkan NISN atau NIS Anda untuk memeriksa status kelulusan.</p>
                <form action="{{ route('public.index') }}" method="GET" class="search-form">
                    <div class="input-group">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" name="search" placeholder="Masukkan NISN atau NIS" value="{{ $searchQuery }}" required autocomplete="off">
                        <button type="submit">Periksa</button>
                    </div>
                </form>
                @if($error)
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $error }}
                    </div>
                @endif
            </div>

            @if($student)
                <!-- Result Card -->
                <div class="card-glass result-card">
                    <div class="result-header">
                        <div class="student-meta">
                            @if($student->status === 'LULUS')
                                <p class="selamat-text">
                                    <i class="fa-solid fa-check-circle"></i> Selamat! Anda dinyatakan LULUS
                                </p>
                            @endif
                            <h3>{{ $student->name }}</h3>
                            <div class="meta-tags">
                                <span class="meta-tag">NISN: <strong>{{ $student->nisn }}</strong></span>
                                <span class="meta-tag">NIS: <strong>{{ $student->nis ?? '-' }}</strong></span>
                                <span class="meta-tag">Kelas: <strong>{{ $student->class }}</strong></span>
                            </div>
                        </div>
                        @if($student->status === 'LULUS')
                            <div class="status-badge status-lulus">
                                <i class="fa-solid fa-circle-check"></i> LULUS
                            </div>
                        @else
                            <div class="status-badge status-tidak-lulus">
                                <i class="fa-solid fa-circle-xmark"></i> TIDAK LULUS
                            </div>
                        @endif
                    </div>

                    <hr class="divider">

                    <div class="grades-section">
                        <h4><i class="fa-solid fa-file-invoice"></i> Nilai Hasil Ujian</h4>
                        <div class="table-responsive">
                            <table class="table table-grades">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;">No</th>
                                        <th>Mata Pelajaran</th>
                                        <th class="text-center" style="width: 52px;">Smt 1</th>
                                        <th class="text-center" style="width: 52px;">Smt 2</th>
                                        <th class="text-center" style="width: 52px;">Smt 3</th>
                                        <th class="text-center" style="width: 52px;">Smt 4</th>
                                        <th class="text-center" style="width: 52px;">Smt 5</th>
                                        <th class="text-center" style="width: 52px;">Smt 6</th>
                                        <th class="text-center" style="width: 80px;">Nilai Ijazah</th>
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
                                            <td>{{ $no++ }}</td>
                                            <td>
                                                <div class="subject-name">{{ $subject->name }}</div>
                                                <div class="subject-code">{{ $subject->code }}</div>
                                            </td>
                                            @for($i = 1; $i <= 6; $i++)
                                                @php
                                                    $semGrade = $subGrades->where('semester', "Semester $i")->first();
                                                @endphp
                                                <td class="text-center" style="color: #64748b;">{{ $semGrade ? number_format($semGrade->score, 0) : '-' }}</td>
                                            @endfor
                                            <td class="text-center td-score-val">
                                                {{ $finalGrade !== null ? number_format($finalGrade, 1) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="row-average">
                                        <td colspan="8" class="text-right" style="font-weight: 700;">Rata-Rata Nilai Ijazah:</td>
                                        <td class="text-center score-avg">
                                            <strong>{{ number_format($student->average_score, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="result-actions">
                        @if($student->status === 'LULUS')
                            <a href="{{ route('public.skl.pdf', $student->id) }}" class="btn-download btn-download-skl" target="_blank">
                                <i class="fa-solid fa-download"></i> Download SKL
                            </a>
                            <a href="{{ route('public.transcript.pdf', $student->id) }}" class="btn-download btn-download-transkrip" target="_blank">
                                <i class="fa-solid fa-download"></i> Download Transkrip
                            </a>
                        @else
                            <div class="alert alert-info">
                                <i class="fa-solid fa-circle-info"></i> Silakan hubungi wali kelas atau pihak sekolah untuk informasi lebih lanjut.
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@section('scripts')
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
    });
</script>
@endif
@endsection
