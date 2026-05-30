@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')

@section('content')
<div class="dashboard-container fade-in">
    <!-- Stats Cards Grid -->
    <div class="stats-grid">
        <div class="card stat-card border-left-primary">
            <div class="stat-content">
                <span class="stat-title">Total Siswa</span>
                <span class="stat-value">{{ $total_students }}</span>
            </div>
            <div class="stat-icon text-primary">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
        </div>

        <div class="card stat-card border-left-success">
            <div class="stat-content">
                <span class="stat-title">Siswa Lulus</span>
                <span class="stat-value text-success">{{ $total_lulus }}</span>
                @if($total_students > 0)
                    <span class="stat-desc">({{ round(($total_lulus / $total_students) * 100, 1) }}% dari total)</span>
                @endif
            </div>
            <div class="stat-icon text-success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="card stat-card border-left-danger">
            <div class="stat-content">
                <span class="stat-title">Siswa Tidak Lulus</span>
                <span class="stat-value text-danger">{{ $total_tidak_lulus }}</span>
                @if($total_students > 0)
                    <span class="stat-desc">({{ round(($total_tidak_lulus / $total_students) * 100, 1) }}% dari total)</span>
                @endif
            </div>
            <div class="stat-icon text-danger">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
        </div>

        <div class="card stat-card border-left-info">
            <div class="stat-content">
                <span class="stat-title">Mata Pelajaran</span>
                <span class="stat-value">{{ $total_subjects }}</span>
            </div>
            <div class="stat-icon text-info">
                <i class="fa-solid fa-book"></i>
            </div>
        </div>

        <div class="card stat-card border-left-warning">
            <div class="stat-content">
                <span class="stat-title">Rata-Rata Nilai</span>
                <span class="stat-value text-warning">{{ $average_score }}</span>
            </div>
            <div class="stat-icon text-warning">
                <i class="fa-solid fa-chart-simple"></i>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="dashboard-details-grid">
        <!-- Recent Students Table -->
        <div class="card recent-students-card">
            <div class="card-header">
                <h3>Siswa Terbaru Diperbarui</h3>
                <a href="{{ route('admin.students') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if($recent_students->isEmpty())
                    <p class="text-muted text-center py-4">Belum ada data siswa.</p>
                @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No Ujian</th>
                                    <th>NISN</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_students as $student)
                                    <tr>
                                        <td>{{ $student->exam_number }}</td>
                                        <td>{{ $student->nisn }}</td>
                                        <td><strong>{{ $student->name }}</strong></td>
                                        <td>{{ $student->class }}</td>
                                        <td>
                                            @if($student->status === 'LULUS')
                                                <span class="badge badge-success">LULUS</span>
                                            @else
                                                <span class="badge badge-danger">TIDAK LULUS</span>
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

        <!-- Quick Access panel -->
        <div class="card quick-actions-card">
            <div class="card-header">
                <h3>Aksi Cepat</h3>
            </div>
            <div class="card-body quick-actions-list">
                <a href="{{ route('admin.students') }}?add=1" class="btn btn-primary btn-block">
                    <i class="fa-solid fa-user-plus"></i> Tambah Siswa Baru
                </a>
                <a href="{{ route('admin.students') }}?import=1" class="btn btn-secondary btn-block">
                    <i class="fa-solid fa-file-import"></i> Impor Nilai Siswa (CSV)
                </a>
                <a href="{{ route('admin.subjects') }}" class="btn btn-secondary btn-block">
                    <i class="fa-solid fa-folder-plus"></i> Kelola Mata Pelajaran
                </a>
                <a href="{{ route('admin.settings') }}" class="btn btn-secondary btn-block">
                    <i class="fa-solid fa-wrench"></i> Atur Waktu & Kop Surat
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
