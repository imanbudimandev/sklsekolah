@extends('layouts.app')

@section('title', 'Cek Kelulusan Siswa')

@section('styles')
<style>
    /* ==========================================================================
       OFFICIAL & PROFESSIONAL EDUCATION PORTAL SYSTEM
       ========================================================================== */

    body {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        background-attachment: fixed;
        color: #1e293b;
    }

    .public-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 60px 20px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    /* Top Branding Header */
    .public-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .school-logo-container {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100px;
        height: 100px;
        background: #ffffff;
        border-radius: 24px;
        margin-bottom: 16px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .school-logo-img {
        height: 70px;
        width: 70px;
        object-fit: contain;
    }

    .school-logo-placeholder {
        font-size: 3rem;
        color: var(--primary);
    }

    .public-header h1 {
        font-size: 2.2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
        letter-spacing: -0.03em;
    }

    .school-address {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .school-address i {
        color: var(--primary);
    }

    /* Clean Official Cards */
    .card.glass {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.05), 0 10px 10px -5px rgba(0,0,0,0.03);
        border-radius: 20px;
        padding: 40px;
        width: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card.glass:hover {
        transform: translateY(-2px);
        box-shadow: 0 25px 30px -5px rgba(0,0,0,0.08), 0 15px 15px -5px rgba(0,0,0,0.04);
    }

    /* Official Search Input Group */
    .search-card h2 {
        font-size: 1.6rem;
        color: #0f172a;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .search-card p {
        color: #64748b;
        font-size: 0.95rem;
        margin-bottom: 24px;
    }

    .search-form .input-group {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 16px;
        padding: 4px;
        transition: all 0.2s ease;
        position: relative;
    }

    .search-form .input-group:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        background: #ffffff;
    }

    .search-icon {
        color: #94a3b8;
        font-size: 1.1rem;
        margin-left: 16px;
    }

    .search-form input {
        border: none;
        outline: none;
        background: transparent;
        padding: 12px 14px;
        font-size: 1rem;
        color: #0f172a;
        flex-grow: 1;
        font-family: var(--font-primary);
        font-weight: 500;
    }

    .search-form input::placeholder {
        color: #94a3b8;
    }

    .search-form button {
        border-radius: 12px;
        padding: 12px 28px;
        font-weight: 600;
        font-size: 0.95rem;
        background: var(--primary);
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .search-form button:hover {
        background: var(--primary-hover);
    }

    /* Result Layout Section */
    .result-card {
        margin-top: 10px;
    }

    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .student-meta h3 {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }

    .student-meta p {
        font-size: 0.95rem;
        color: #64748b;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .meta-tag {
        background: #f1f5f9;
        padding: 4px 12px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .meta-tag strong {
        color: #0f172a;
    }

    /* Clear Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 1.15rem;
        font-weight: 700;
        padding: 10px 24px;
        border-radius: 12px;
        letter-spacing: 0.02em;
    }

    .status-lulus {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .status-tidak-lulus {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Grades Table Customization */
    .grades-section {
        margin-top: 10px;
    }

    .grades-section h4 {
        font-size: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569;
        margin-bottom: 16px;
        font-weight: 700;
    }

    .grades-section h4 i {
        color: var(--primary);
        margin-right: 6px;
    }

    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .table-grades {
        width: 100%;
        border-collapse: collapse;
        color: #334155;
    }

    .table-grades th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 14px 16px;
        border-bottom: 1.5px solid #e2e8f0;
        text-align: left;
    }

    .table-grades td {
        padding: 12px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    .table-grades tbody tr:last-child td {
        border-bottom: none;
    }

    .subject-cell {
        display: flex;
        flex-direction: column;
    }

    .subject-name {
        font-weight: 600;
        color: #0f172a;
    }

    .subject-code {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .td-score-val {
        font-weight: 700;
        font-size: 0.95rem;
        color: #0f172a;
        text-align: center;
    }

    .row-average {
        background: #f8fafc !important;
    }

    .row-average td {
        border-top: 1.5px solid #cbd5e1;
        padding: 16px;
    }

    .score-avg {
        font-size: 1.15rem;
        color: var(--primary);
        font-weight: 800;
    }

    /* Print & Action triggers */
    .result-actions {
        display: flex;
        justify-content: center;
        margin-top: 24px;
    }

    .btn-lg {
        padding: 14px 32px;
        font-size: 0.95rem;
        border-radius: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-success {
        background-color: var(--success);
        color: white;
        border: none;
        cursor: pointer;
        transition: background-color 0.2s ease;
        box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.1), 0 2px 4px -1px rgba(16, 185, 129, 0.06);
    }

    .btn-success:hover {
        background-color: var(--success-hover);
    }

    /* Alerts */
    .alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.9rem;
        margin-top: 16px;
        text-align: left;
    }

    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .alert-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e3a8a;
        width: 100%;
        justify-content: center;
    }

    /* Countdown layout */
    .countdown-timer {
        display: flex;
        justify-content: center;
        gap: 16px;
        margin: 24px 0;
    }

    .timer-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        width: 80px;
        padding: 12px 0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .timer-num {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary);
        line-height: 1;
        margin-bottom: 4px;
    }

    .timer-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .announcement-date-text {
        font-size: 0.9rem;
        background: #f8fafc;
        padding: 8px 16px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .announcement-date-text i {
        color: #d97706;
    }

    /* Print only definitions */
    .print-only-container {
        display: none;
    }

    @media print {
        body {
            background: white !important;
        }
        .public-container {
            padding: 0;
            max-width: 100%;
        }
        .public-header, .search-card, .result-actions, .countdown-wrapper {
            display: none !important;
        }
        .print-only-container {
            display: block !important;
        }
        .print-page {
            page-break-after: always;
            padding: 20px;
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
        }
        .print-kop {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .print-kop img {
            max-height: 80px;
        }
        .print-kop .kop-text {
            flex: 1;
            text-align: center;
        }
        .print-kop .kop-text h2 {
            font-size: 14pt;
            margin: 0;
        }
        .print-kop .kop-text h1 {
            font-size: 18pt;
            margin: 5px 0;
        }
        .print-kop .kop-text p {
            font-size: 10pt;
            color: #333;
        }
        .print-divider {
            border-top: 2px solid black;
            margin: 10px 0;
        }
        .cert-title {
            text-align: center;
        }
        .table-print-meta {
            width: 100%;
            margin: 10px 0;
        }
        .table-print-meta td {
            padding: 4px 8px;
        }
        .table-print-grades {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .table-print-grades th, .table-print-grades td {
            border: 1px solid #333;
            padding: 6px 8px;
            font-size: 10pt;
        }
        .print-status-box {
            text-align: center;
            border: 2px solid #059669;
            padding: 10px;
            width: 200px;
            margin: 20px auto;
        }
        .cert-note {
            font-size: 9pt;
            font-style: italic;
        }
        .cert-footer {
            margin-top: 40px;
        }
        .signature-box {
            text-align: center;
            width: 300px;
            margin-left: auto;
        }
        .sig-img {
            max-height: 60px;
        }
        .signature-space {
            height: 60px;
        }
        .page-break {
            page-break-after: always;
        }
        .no-print {
            display: none !important;
        }
        .cert-body p {
            text-align: justify;
            text-indent: 30px;
        }
        .cert-statement {
            text-align: justify;
            text-indent: 30px;
        }
        .font-bold {
            font-weight: bold;
        }
        .row-print-average td {
            border-top: 2px solid #333 !important;
            font-weight: bold;
        }
        .short td {
            padding: 2px 8px;
            font-size: 10pt;
        }
    }

    @media (max-width: 768px) {
        .public-header h1 {
            font-size: 1.8rem;
        }

        .student-meta h3 {
            font-size: 1.4rem;
        }

        .result-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .status-badge {
            width: auto;
        }

        .card.glass {
            padding: 24px;
        }

        .table-grades th, .table-grades td {
            padding: 10px 8px;
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('content')
<div class="public-container">
    <!-- Header/Logo School -->
    <header class="public-header">
        <div class="school-logo-container">
            @if(!empty($settings['school_logo']) && file_exists(public_path($settings['school_logo'])))
                <img src="{{ asset($settings['school_logo']) }}" alt="Logo Sekolah" class="school-logo-img">
            @else
                <div class="school-logo-placeholder">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
            @endif
        </div>
        <h1>{{ $settings['school_name'] }}</h1>
        <p class="school-address"><i class="fa-solid fa-location-dot"></i> {{ $settings['school_address'] ?: 'Kecamatan Banjaran, Kabupaten Bandung, Jawa Barat' }}</p>
    </header>

    @if(!$isAnnounced)
        <!-- COUNTDOWN TIMER VIEW -->
        <div class="countdown-wrapper card glass">
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
                <i class="fa-regular fa-calendar-days"></i> Waktu Pengumuman: 
                <strong>{{ $announcementDate ? $announcementDate->translatedFormat('d F Y \p\u\k\u\l H:i') : '-' }} WIB</strong>
            </p>
        </div>
    @else
        <!-- SEARCH & PORTAL VIEW -->
        <div class="portal-wrapper">
            <!-- Search Box Card -->
            <div class="search-card card glass">
                <h2>Cek Status Kelulusan Anda</h2>
                <p>Masukkan NISN atau NIS Anda untuk memeriksa status kelulusan.</p>
                
                <form action="{{ route('public.index') }}" method="GET" class="search-form">
                    <div class="input-group">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" name="search" placeholder="Masukkan NISN atau NIS" value="{{ $searchQuery }}" required autocomplete="off">
                        <button type="submit" class="btn">Periksa</button>
                    </div>
                </form>

                @if($error)
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $error }}
                    </div>
                @endif
            </div>

            @if($student)
                <!-- RESULT DISPLAY CARD -->
                <div class="result-card card glass">
                    <div class="result-header">
                        <div class="student-meta">
                            @if($student->status === 'LULUS')
                                <p style="color: #065f46; font-weight: 700; font-size: 1.05rem; margin-bottom: 4px;">
                                    <i class="fa-solid fa-check-circle"></i> Selamat! Anda dinyatakan LULUS
                                </p>
                            @endif
                            <h3>{{ $student->name }}</h3>
                            <p>
                                <span class="meta-tag">NISN: <strong>{{ $student->nisn }}</strong></span> 
                                <span class="meta-tag">NIS: <strong>{{ $student->nis ?? '-' }}</strong></span>
                                <span class="meta-tag">Kelas: <strong>{{ $student->class }}</strong></span>
                            </p>
                        </div>
                        
                        <!-- Status Badge -->
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

                    <!-- GRADES TABLE -->
                    <div class="grades-section">
                        <h4><i class="fa-solid fa-file-invoice"></i> Nilai Hasil Ujian</h4>
                        <div class="table-responsive">
                            <table class="table table-grades">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Mata Pelajaran</th>
                                        <th class="text-center" style="width: 60px;">Smt 1</th>
                                        <th class="text-center" style="width: 60px;">Smt 2</th>
                                        <th class="text-center" style="width: 60px;">Smt 3</th>
                                        <th class="text-center" style="width: 60px;">Smt 4</th>
                                        <th class="text-center" style="width: 60px;">Smt 5</th>
                                        <th class="text-center" style="width: 60px;">Smt 6</th>
                                        <th class="text-center" style="width: 100px;">Nilai Ijazah</th>
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
                                                <div class="subject-cell">
                                                    <span class="subject-name">{{ $subject->name }}</span>
                                                    <span class="subject-code">{{ $subject->code }}</span>
                                                </div>
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
                                        <td colspan="8" class="text-right" style="color: #1e293b; font-weight: 700;">Rata-Rata Nilai Ijazah:</td>
                                        <td class="text-center score-avg">
                                            <strong>{{ number_format($student->average_score, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="result-actions" style="gap: 12px; flex-wrap: wrap;">
                        @if($student->status === 'LULUS')
                            <a href="{{ route('public.skl.pdf', $student->id) }}" class="btn btn-success btn-lg" target="_blank">
                                <i class="fa-solid fa-download"></i> Download SKL
                            </a>
                            <a href="{{ route('public.transcript.pdf', $student->id) }}" class="btn btn-primary btn-lg" target="_blank" style="background: #0d9488; border-color: #0d9488;">
                                <i class="fa-solid fa-download"></i> Download Transkrip
                            </a>
                        @else
                            <div class="alert alert-info">
                                <i class="fa-solid fa-circle-info"></i> Silakan hubungi wali kelas atau pihak sekolah untuk informasi lebih lanjut.
                            </div>
                        @endif
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
