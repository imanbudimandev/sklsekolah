@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')

@section('styles')
<style>
    .dashboard-container {
        padding: 4px 0;
    }

    .greeting-section {
        margin-bottom: 28px;
    }
    .greeting-section h1 {
        font-size: 1.75rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px 0;
    }
    .greeting-section p {
        color: #64748b;
        margin: 0;
        font-size: 0.95rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        position: relative;
        background: #fff;
        border-radius: 16px;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        transition: height 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 35px rgba(0,0,0,0.1);
    }
    .stat-card:hover::before {
        height: 6px;
    }

    .stat-card.primary::before { background: linear-gradient(90deg, #4f46e5, #818cf8); }
    .stat-card.success::before { background: linear-gradient(90deg, #059669, #34d399); }
    .stat-card.danger::before { background: linear-gradient(90deg, #dc2626, #f87171); }
    .stat-card.info::before { background: linear-gradient(90deg, #0284c7, #38bdf8); }
    .stat-card.warning::before { background: linear-gradient(90deg, #d97706, #fbbf24); }

    .stat-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-left: 20px;
        flex-shrink: 0;
        transition: all 0.3s;
    }
    .stat-card:hover .stat-icon-wrap {
        transform: scale(1.08);
    }
    .stat-card.primary .stat-icon-wrap {
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        color: #4f46e5;
    }
    .stat-card.success .stat-icon-wrap {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        color: #059669;
    }
    .stat-card.danger .stat-icon-wrap {
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        color: #dc2626;
    }
    .stat-card.info .stat-icon-wrap {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        color: #0284c7;
    }
    .stat-card.warning .stat-icon-wrap {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        color: #d97706;
    }

    .stat-content {
        display: flex;
        flex-direction: column;
        gap: 1px;
        padding: 20px 20px 20px 0;
        flex: 1;
    }
    .stat-title {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .stat-card.primary .stat-title { color: #818cf8; }
    .stat-card.success .stat-title { color: #34d399; }
    .stat-card.danger .stat-title { color: #f87171; }
    .stat-card.info .stat-title { color: #38bdf8; }
    .stat-card.warning .stat-title { color: #fbbf24; }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.1;
        color: #0f172a;
    }
    .stat-desc {
        font-size: 0.78rem;
        color: #94a3b8;
        margin-top: 2px;
    }

    .dashboard-details-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 22px;
        margin-bottom: 28px;
    }

    .card-modern {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden;
        transition: box-shadow 0.3s;
    }
    .card-modern:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .card-header-modern {
        padding: 20px 24px 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-header-modern h3 {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .card-header-modern h3 i {
        color: #6366f1;
        font-size: 1.1rem;
    }

    .card-body-modern {
        padding: 16px 24px 24px 24px;
    }

    .table-dashboard {
        width: 100%;
        border-collapse: collapse;
    }
    .table-dashboard thead th {
        text-align: left;
        padding: 10px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #64748b;
        border-bottom: 2px solid #f1f5f9;
    }
    .table-dashboard tbody td {
        padding: 12px 12px;
        font-size: 0.9rem;
        color: #1e293b;
        border-bottom: 1px solid #f8fafc;
    }
    .table-dashboard tbody tr:hover {
        background: #f8fafc;
    }
    .table-dashboard tbody tr:last-child td {
        border-bottom: none;
    }

    .badge-lulus {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #dcfce7;
        color: #166534;
    }
    .badge-tidak {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #fee2e2;
        color: #991b1b;
    }

    .btn-sm-modern {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 18px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        background: #f1f5f9;
        color: #475569;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    .btn-sm-modern:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .quick-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .quick-action-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 20px 12px;
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        text-align: center;
        border: 1.5px solid #f1f5f9;
        background: #fafbfc;
    }
    .quick-action-item i {
        font-size: 1.6rem;
        transition: transform 0.25s;
    }
    .quick-action-item span {
        font-size: 0.8rem;
        font-weight: 600;
        color: #334155;
        line-height: 1.3;
    }
    .quick-action-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        border-color: transparent;
    }
    .quick-action-item:hover i {
        transform: scale(1.15);
    }
    .quick-action-item.primary { border-left: 3px solid #6366f1; }
    .quick-action-item.primary i { color: #6366f1; }
    .quick-action-item.primary:hover { background: linear-gradient(135deg, #eef2ff, #fff); }

    .quick-action-item.success { border-left: 3px solid #10b981; }
    .quick-action-item.success i { color: #10b981; }
    .quick-action-item.success:hover { background: linear-gradient(135deg, #ecfdf5, #fff); }

    .quick-action-item.warning { border-left: 3px solid #f59e0b; }
    .quick-action-item.warning i { color: #f59e0b; }
    .quick-action-item.warning:hover { background: linear-gradient(135deg, #fffbeb, #fff); }

    .quick-action-item.info { border-left: 3px solid #3b82f6; }
    .quick-action-item.info i { color: #3b82f6; }
    .quick-action-item.info:hover { background: linear-gradient(135deg, #eff6ff, #fff); }

    .semester-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .semester-card {
        background: #fff;
        border-radius: 16px;
        padding: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        overflow: hidden;
    }
    .semester-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.1);
    }

    .sem-card-head {
        padding: 16px 18px 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        border-bottom: 1px solid #f8fafc;
    }
    .sem-card-head .sem-badge {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .sem-card-head .sem-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }
    .sem-card-head .sem-sub {
        font-size: 0.7rem;
        color: #94a3b8;
    }

    .sem-card-body {
        padding: 14px 18px 18px;
    }
    .sem-card-body .sem-value-lg {
        font-size: 1.6rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 2px;
    }
    .sem-card-body .sem-count-sm {
        font-size: 0.72rem;
        color: #94a3b8;
    }
    .sem-card-body .sem-bar {
        margin-top: 10px;
        height: 5px;
        border-radius: 6px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .sem-card-body .sem-bar-inner {
        height: 100%;
        border-radius: 6px;
        transition: width 0.8s ease;
    }

    .sem-card-1 .sem-badge { background: #eef2ff; color: #4f46e5; }
    .sem-card-1 .sem-value-lg { color: #4f46e5; }
    .sem-card-1 .sem-bar-inner { background: linear-gradient(90deg, #4f46e5, #818cf8); }

    .sem-card-2 .sem-badge { background: #f0fdf4; color: #16a34a; }
    .sem-card-2 .sem-value-lg { color: #16a34a; }
    .sem-card-2 .sem-bar-inner { background: linear-gradient(90deg, #16a34a, #4ade80); }

    .sem-card-3 .sem-badge { background: #fef2f2; color: #dc2626; }
    .sem-card-3 .sem-value-lg { color: #dc2626; }
    .sem-card-3 .sem-bar-inner { background: linear-gradient(90deg, #dc2626, #f87171); }

    .sem-card-4 .sem-badge { background: #fff7ed; color: #ea580c; }
    .sem-card-4 .sem-value-lg { color: #ea580c; }
    .sem-card-4 .sem-bar-inner { background: linear-gradient(90deg, #ea580c, #fb923c); }

    .sem-card-5 .sem-badge { background: #fdf4ff; color: #c026d3; }
    .sem-card-5 .sem-value-lg { color: #c026d3; }
    .sem-card-5 .sem-bar-inner { background: linear-gradient(90deg, #c026d3, #e879f9); }

    .sem-card-6 .sem-badge { background: #ecfeff; color: #0891b2; }
    .sem-card-6 .sem-value-lg { color: #0891b2; }
    .sem-card-6 .sem-bar-inner { background: linear-gradient(90deg, #0891b2, #22d3ee); }

    .sem-card-ujian .sem-badge { background: #fefce8; color: #ca8a04; }
    .sem-card-ujian .sem-value-lg { color: #ca8a04; }
    .sem-card-ujian .sem-bar-inner { background: linear-gradient(90deg, #ca8a04, #eab308); }

    .sem-card-ijazah .sem-badge { background: #f0f9ff; color: #0369a1; }
    .sem-card-ijazah .sem-value-lg { color: #0369a1; }
    .sem-card-ijazah .sem-bar-inner { background: linear-gradient(90deg, #0369a1, #38bdf8); }

    @media (max-width: 991px) {
        .dashboard-details-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 600px) {
    }
</style>
@endsection

@section('content')
<div class="dashboard-container fade-in">
    @php
        $hour = now()->hour;
        if ($hour < 12) $greeting = 'Selamat Pagi';
        elseif ($hour < 15) $greeting = 'Selamat Siang';
        elseif ($hour < 18) $greeting = 'Selamat Sore';
        else $greeting = 'Selamat Malam';
    @endphp

    <div class="greeting-section">
        <h1>{{ $greeting }}, {{ Auth::user()->name }}! 👋</h1>
        <p>Selamat datang di panel kelulusan siswa.</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon-wrap"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="stat-content">
                <span class="stat-title">Total Siswa</span>
                <span class="stat-value">{{ $total_students }}</span>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-icon-wrap"><i class="fa-solid fa-circle-check"></i></div>
            <div class="stat-content">
                <span class="stat-title">Siswa Lulus</span>
                <span class="stat-value">{{ $total_lulus }}</span>
                @if($total_students > 0)
                    <span class="stat-desc">{{ round(($total_lulus / max($total_students,1)) * 100, 1) }}% dari total</span>
                @endif
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-icon-wrap"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="stat-content">
                <span class="stat-title">Tidak Lulus</span>
                <span class="stat-value">{{ $total_tidak_lulus }}</span>
                @if($total_students > 0)
                    <span class="stat-desc">{{ round(($total_tidak_lulus / max($total_students,1)) * 100, 1) }}% dari total</span>
                @endif
            </div>
        </div>

        <div class="stat-card info">
            <div class="stat-icon-wrap"><i class="fa-solid fa-book"></i></div>
            <div class="stat-content">
                <span class="stat-title">Mata Pelajaran</span>
                <span class="stat-value">{{ $total_subjects }}</span>
                <span class="stat-desc">{{ $total_classes }} kelas terdaftar</span>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon-wrap"><i class="fa-solid fa-chart-simple"></i></div>
            <div class="stat-content">
                <span class="stat-title">Rata-Rata Nilai</span>
                <span class="stat-value">{{ $average_score }}</span>
            </div>
        </div>
    </div>

    @if($semesters->isNotEmpty())
    <div class="card-modern" style="margin-bottom: 22px;">
        <div class="card-header-modern">
            <h3><i class="fa-solid fa-layer-group"></i> Rata-Rata Nilai Per Semester</h3>
        </div>
        <div class="card-body-modern">
            <div class="semester-grid">
                @php $maxAvg = max($semesters->pluck('avg_score')->max(), 1); @endphp
                @foreach($semesters as $sem)
                    @php
                        $semSlug = preg_replace('/[^a-z0-9]/i', '', $sem->semester);
                        if (str_contains($sem->semester, 'Ujian')) $semClass = 'ujian';
                        elseif (str_contains($sem->semester, 'Ijazah')) $semClass = 'ijazah';
                        else $semClass = preg_replace('/[^0-9]/', '', $sem->semester);
                    @endphp
                    <div class="semester-card sem-card-{{ $semClass }}">
                        <div class="sem-card-head">
                            <div class="sem-badge">
                                @if(str_contains($sem->semester, 'Ujian'))
                                    <i class="fa-solid fa-pen-to-square"></i>
                                @elseif(str_contains($sem->semester, 'Ijazah'))
                                    <i class="fa-solid fa-certificate"></i>
                                @else
                                    {{ preg_replace('/[^0-9]/', '', $sem->semester) }}
                                @endif
                            </div>
                            <div>
                                <div class="sem-title">{{ $sem->semester }}</div>
                                <div class="sem-sub">{{ $sem->total_grades }} nilai</div>
                            </div>
                        </div>
                        <div class="sem-card-body">
                            <div class="sem-value-lg">{{ number_format($sem->avg_score, 1) }}</div>
                            <div class="sem-bar">
                                <div class="sem-bar-inner" style="width: {{ ($sem->avg_score / 100) * 100 }}%;"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="dashboard-details-grid">
        <div class="card-modern">
            <div class="card-header-modern">
                <h3><i class="fa-solid fa-clock-rotate-left"></i> Siswa Terbaru</h3>
                <a href="{{ route('admin.students') }}" class="btn-sm-modern">
                    <i class="fa-solid fa-arrow-right"></i> Lihat Semua
                </a>
            </div>
            <div class="card-body-modern">
                @if($recent_students->isEmpty())
                    <p class="text-muted text-center py-4" style="color:#94a3b8;text-align:center;padding:24px 0;">Belum ada data siswa.</p>
                @else
                    <div style="overflow-x:auto;">
                        <table class="table-dashboard">
                            <thead>
                                <tr>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_students as $student)
                                    <tr>
                                        <td style="color:#64748b;">{{ $student->nisn }}</td>
                                        <td><strong>{{ $student->name }}</strong></td>
                                        <td>{{ $student->class }}</td>
                                        <td>
                                            @if($student->status === 'LULUS')
                                                <span class="badge-lulus">LULUS</span>
                                            @else
                                                <span class="badge-tidak">TIDAK LULUS</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="card-modern">
            <div class="card-header-modern">
                <h3><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Aktivitas Siswa</h3>
            </div>
            <div class="card-body-modern" style="padding-top:8px;">
                @if($recent_logins->isEmpty())
                    <p class="text-muted text-center py-4" style="color:#94a3b8;text-align:center;padding:24px 0;">Belum ada aktivitas login siswa.</p>
                @else
                    <div style="display:flex;flex-direction:column;gap:0;">
                        @foreach($recent_logins as $student)
                            <div style="display:flex;align-items:flex-start;gap:14px;padding:12px 0;border-bottom:1px solid #f8fafc;">
                                <div style="width:36px;height:36px;border-radius:10px;background:#eef2ff;display:flex;align-items:center;justify-content:center;color:#4f46e5;font-size:0.9rem;flex-shrink:0;">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-weight:600;color:#0f172a;font-size:0.9rem;">{{ $student->name }}</div>
                                    <div style="font-size:0.78rem;color:#94a3b8;margin-top:2px;">
                                        {{ $student->nisn }} · {{ $student->class }}
                                        @if($student->status === 'LULUS')
                                            <span style="display:inline-block;padding:1px 8px;border-radius:10px;font-size:0.7rem;font-weight:600;background:#dcfce7;color:#166534;margin-left:6px;">LULUS</span>
                                        @else
                                            <span style="display:inline-block;padding:1px 8px;border-radius:10px;font-size:0.7rem;font-weight:600;background:#fee2e2;color:#991b1b;margin-left:6px;">TIDAK LULUS</span>
                                        @endif
                                    </div>
                                </div>
                                <div style="text-align:right;flex-shrink:0;">
                                    <div style="font-size:0.7rem;color:#94a3b8;">{{ $student->last_login_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div style="text-align:center;margin-top:12px;">
                        <a href="{{ route('admin.students') }}" style="display:inline-flex;align-items:center;gap:6px;padding:6px 18px;border-radius:10px;font-size:0.8rem;font-weight:600;text-decoration:none;background:#f1f5f9;color:#475569;transition:all 0.2s;">
                            Lihat Semua Siswa <i class="fa-solid fa-arrow-right" style="font-size:0.7rem;"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div style="text-align:center;padding:12px 0 4px;font-size:0.78rem;color:#94a3b8;">
        <i class="fa-regular fa-calendar"></i> {{ now()->translatedFormat('l, d F Y') }}
    </div>
</div>
@endsection