@extends('layouts.student')

@section('title', 'Dashboard Siswa')
@section('page_title', 'Dashboard')

@section('styles')
<style>
    .greeting {
        margin-bottom: 24px;
    }
    .greeting h2 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .greeting p {
        margin: 4px 0 0;
        font-size: 0.88rem;
        color: #94a3b8;
    }

    .student-bio {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .student-bio .avatar {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: #eef2ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: #4f46e5;
        flex-shrink: 0;
    }
    .student-bio .bio-info {
        flex: 1;
        min-width: 180px;
    }
    .student-bio .bio-info h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
    }
    .student-bio .bio-info .bio-meta {
        font-size: 0.82rem;
        color: #94a3b8;
        margin-top: 2px;
        display: flex;
        flex-wrap: wrap;
        gap: 4px 16px;
    }
    .student-bio .bio-status {
        padding: 6px 20px;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 700;
    }
    .student-bio .bio-status.lulus {
        background: #dcfce7;
        color: #166534;
    }
    .student-bio .bio-status.tidak {
        background: #fee2e2;
        color: #991b1b;
    }

    .stats-mini {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }
    .stat-mini {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }
    .stat-mini .stat-num {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }
    .stat-mini .stat-label {
        font-size: 0.72rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-top: 4px;
    }

    .card-table {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 24px;
    }
    .card-table .card-header {
        padding: 16px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-table .card-header h3 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .card-table .card-header h3 i {
        color: #4f46e5;
        font-size: 0.95rem;
    }

    .table-simple {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.88rem;
    }
    .table-simple thead th {
        padding: 10px 16px;
        font-size: 0.72rem;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        text-align: left;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
    }
    .table-simple thead th.center {
        text-align: center;
    }
    .table-simple tbody td {
        padding: 10px 16px;
        border-bottom: 1px solid #f8fafc;
        color: #1e293b;
    }
    .table-simple tbody td.center {
        text-align: center;
        font-weight: 600;
    }
    .table-simple tbody tr:last-child td {
        border-bottom: none;
    }
    .table-simple tbody tr:hover {
        background: #fafbfc;
    }

    .download-bar {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }
    .download-bar .left {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .download-bar .left i {
        font-size: 1.2rem;
        color: #4f46e5;
    }
    .download-bar .left span {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0f172a;
    }
    .download-bar .actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        border: 1.5px solid #e2e8f0;
        color: #475569;
        background: #fff;
        transition: all 0.2s;
    }
    .btn-outline:hover {
        border-color: #4f46e5;
        color: #4f46e5;
        background: #f8f7ff;
    }
    .btn-outline i {
        font-size: 0.85rem;
    }
    .btn-solid {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        text-decoration: none;
        color: #fff;
        border: none;
        transition: all 0.2s;
    }
    .btn-solid.skl {
        background: #4f46e5;
    }
    .btn-solid.skl:hover {
        background: #4338ca;
    }
    .btn-solid.transkrip {
        background: #059669;
    }
    .btn-solid.transkrip:hover {
        background: #047857;
    }

    @media (max-width: 640px) {
        .stats-mini {
            grid-template-columns: repeat(2, 1fr);
        }
        .student-bio {
            flex-direction: column;
            text-align: center;
        }
        .student-bio .bio-meta {
            justify-content: center;
        }
        .download-bar {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endsection

@section('content')
<div class="fade-in">
    @php
        $hour = now()->hour;
        if ($hour < 12) $greeting = 'Selamat Pagi';
        elseif ($hour < 15) $greeting = 'Selamat Siang';
        elseif ($hour < 18) $greeting = 'Selamat Sore';
        else $greeting = 'Selamat Malam';
    @endphp

    <div class="greeting">
        <h2>{{ $greeting }}, {{ $student->name }}</h2>
        <p>Berikut ringkasan data kelulusan Anda.</p>
    </div>

    <div class="student-bio">
        <div class="avatar">
            <i class="fa-solid fa-user-graduate"></i>
        </div>
        <div class="bio-info">
            <h3>{{ $student->name }}</h3>
            <div class="bio-meta">
                <span>{{ $student->nisn }}</span>
                @if($student->nis) <span>{{ $student->nis }}</span> @endif
                <span>{{ $student->class }}</span>
                @if($student->jurusan) <span>{{ $student->jurusan }}</span> @endif
            </div>
        </div>
        <div class="bio-status {{ $student->status === 'LULUS' ? 'lulus' : 'tidak' }}">
            {{ $student->status === 'LULUS' ? 'LULUS' : 'TIDAK LULUS' }}
        </div>
    </div>

    @php
        $avgRapor = round($student->grades->whereIn('semester', ['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5','Semester 6'])->avg('score') ?? 0, 2);
        $avgIjazah = round($student->grades->where('semester', 'Nilai Ijazah')->avg('score') ?? 0, 2);
        $totalMapel = $student->grades->unique('subject_id')->count();
    @endphp

    <div class="stats-mini">
        <div class="stat-mini">
            <div class="stat-num">{{ $avgRapor }}</div>
            <div class="stat-label">Rata-Rata Rapor</div>
        </div>
        <div class="stat-mini">
            <div class="stat-num">{{ $avgIjazah ?: '-' }}</div>
            <div class="stat-label">Nilai Ijazah</div>
        </div>
        <div class="stat-mini">
            <div class="stat-num">{{ $totalMapel }}</div>
            <div class="stat-label">Mata Pelajaran</div>
        </div>
        <div class="stat-mini">
            <div class="stat-num">{{ $student->tahun_lulus ?? '-' }}</div>
            <div class="stat-label">Tahun Lulus</div>
        </div>
    </div>

    <div class="card-table">
        <div class="card-header">
            <h3><i class="fa-solid fa-table"></i> Daftar Nilai</h3>
        </div>
        <div style="overflow-x:auto;">
            <table class="table-simple">
                <thead>
                    <tr>
                        <th>Mata Pelajaran</th>
                        @foreach(['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5','Semester 6'] as $sem)
                            <th class="center">{{ $sem }}</th>
                        @endforeach
                        <th class="center">Ijazah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects as $subject)
                        <tr>
                            <td>{{ $subject->name }}</td>
                            @foreach(['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5','Semester 6'] as $sem)
                                @php
                                    $g = $student->grades->where('subject_id', $subject->id)->where('semester', $sem)->first();
                                @endphp
                                <td class="center">{{ $g ? $g->score : '-' }}</td>
                            @endforeach
                            @php
                                $ig = $student->grades->where('subject_id', $subject->id)->where('semester', 'Nilai Ijazah')->first();
                            @endphp
                            <td class="center">{{ $ig ? $ig->score : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($student->status === 'LULUS')
    <div class="download-bar">
        <div class="left">
            <i class="fa-solid fa-file-pdf"></i>
            <span>Unduh Dokumen Kelulusan</span>
        </div>
        <div class="actions">
            <a href="{{ route('public.skl.pdf', $student->id) }}" class="btn-solid skl" target="_blank">
                <i class="fa-solid fa-file-signature"></i> SKL
            </a>
            <a href="{{ route('public.transcript.pdf', $student->id) }}" class="btn-solid transkrip" target="_blank">
                <i class="fa-solid fa-file-invoice"></i> Transkrip
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
