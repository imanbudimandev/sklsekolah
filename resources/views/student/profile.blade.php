@extends('layouts.student')

@section('title', 'Data Saya')
@section('page_title', 'Data Saya')

@section('styles')
<style>
    .profile-wrap {
        display: grid;
        grid-template-columns: 180px 1fr 1fr;
        gap: 24px;
    }
    .info-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
    }
    .info-card.full {
        grid-column: 1 / -1;
    }
    .info-card .card-label {
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        color: #6366f1;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-card .card-label i {
        font-size: 0.9rem;
    }
    .info-row {
        display: flex;
        padding: 10px 0;
        border-bottom: 1px solid #f8fafc;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-row .label {
        width: 140px;
        font-size: 0.85rem;
        color: #94a3b8;
        flex-shrink: 0;
    }
    .info-row .value {
        flex: 1;
        font-size: 0.9rem;
        font-weight: 600;
        color: #0f172a;
    }
    .status-badge-lg {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 20px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
    }
    .status-badge-lg.lulus {
        background: #dcfce7;
        color: #166534;
    }
    .status-badge-lg.tidak {
        background: #fee2e2;
        color: #991b1b;
    }

    @media (max-width: 768px) {
        .profile-wrap {
            grid-template-columns: 1fr;
        }
        .info-row {
            flex-direction: column;
            gap: 2px;
        }
        .info-row .label {
            width: auto;
        }
    }
</style>
@endsection

@section('content')
<div class="fade-in">
    <div class="profile-wrap">
        <div class="info-card" style="display:flex;flex-direction:column;align-items:center;text-align:center;">
            <div style="width:120px;height:120px;border-radius:50%;overflow:hidden;margin-bottom:12px;border:3px solid #e0e7ff;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                @if($student->photo && file_exists(public_path($student->photo)))
                    <img src="{{ asset($student->photo) }}" alt="Foto {{ $student->name }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <i class="fa-solid fa-user-graduate" style="font-size:2.5rem;color:#94a3b8;"></i>
                @endif
            </div>
            <div style="font-weight:700;color:#0f172a;font-size:1rem;">{{ $student->name }}</div>
            <div style="font-size:0.78rem;color:#94a3b8;">{{ $student->class }}</div>
        </div>

        <div class="info-card">
            <div class="card-label"><i class="fa-solid fa-id-card"></i> Identitas</div>
            <div class="info-row">
                <span class="label">Nama Lengkap</span>
                <span class="value">{{ $student->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">NIS</span>
                <span class="value">{{ $student->nis ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="label">NISN</span>
                <span class="value">{{ $student->nisn }}</span>
            </div>
            <div class="info-row">
                <span class="label">Kelas</span>
                <span class="value">{{ $student->class }}</span>
            </div>
            @if($student->jurusan)
            <div class="info-row">
                <span class="label">Jurusan</span>
                <span class="value">{{ $student->jurusan }}</span>
            </div>
            @endif
        </div>

        <div class="info-card">
            <div class="card-label"><i class="fa-solid fa-info-circle"></i> Detail</div>
            <div class="info-row">
                <span class="label">Tempat Lahir</span>
                <span class="value">{{ $student->birth_place ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Tanggal Lahir</span>
                <span class="value">{{ $student->birth_date_formatted ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Status</span>
                <span class="value">
                    @if($student->status === 'LULUS')
                        <span class="status-badge-lg lulus"><i class="fa-solid fa-check-circle"></i> LULUS</span>
                    @else
                        <span class="status-badge-lg tidak"><i class="fa-solid fa-xmark-circle"></i> TIDAK LULUS</span>
                    @endif
                </span>
            </div>
            @if($student->tahun_lulus)
            <div class="info-row">
                <span class="label">Tahun Lulus</span>
                <span class="value">{{ $student->tahun_lulus }}</span>
            </div>
            @endif
        </div>

        <div class="info-card full">
            <div class="card-label"><i class="fa-solid fa-chart-simple"></i> Rata-Rata Per Semester</div>
            @php
                $semesterNames = ['Semester 1','Semester 2','Semester 3','Semester 4','Semester 5','Semester 6'];
            @endphp
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(100px,1fr));gap:12px;">
                @foreach($semesterNames as $sem)
                    @php
                        $avg = round($student->grades->where('semester', $sem)->avg('score') ?? 0, 2);
                    @endphp
                    <div style="text-align:center;padding:16px 8px;background:#f8fafc;border-radius:12px;border:1px solid #f1f5f9;">
                        <div style="font-size:0.7rem;font-weight:600;color:#94a3b8;text-transform:uppercase;letter-spacing:0.3px;">{{ $sem }}</div>
                        <div style="font-size:1.6rem;font-weight:800;color:#0f172a;margin-top:4px;">{{ $avg ?: '-' }}</div>
                    </div>
                @endforeach
                @php
                    $avgIjazah = round($student->grades->where('semester', 'Nilai Ijazah')->avg('score') ?? 0, 2);
                @endphp
                <div style="text-align:center;padding:16px 8px;background:#fefce8;border-radius:12px;border:1px solid #fef08a;">
                    <div style="font-size:0.7rem;font-weight:600;color:#a16207;text-transform:uppercase;letter-spacing:0.3px;">Nilai Ijazah</div>
                    <div style="font-size:1.6rem;font-weight:800;color:#854d0e;margin-top:4px;">{{ $avgIjazah ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
