@extends('layouts.admin')

@section('title', 'Transkrip Nilai Siswa')
@section('page_title', 'Transkrip Nilai (Rata-Rata Semester)')

@section('styles')
<style>
    .transcripts-container {
        margin-top: 15px;
    }
    .score-avg-value {
        font-weight: 700;
        color: #1e293b;
    }
    .score-final-value {
        font-weight: 800;
        color: var(--primary);
        font-size: 1.02rem;
    }
    
    /* Modal Styles */
    .preview-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        animation: fadeIn 0.3s ease;
    }
    
    .preview-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .preview-modal-content {
        background-color: white;
        width: 90%;
        max-width: 900px;
        height: 85vh;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
        animation: slideDown 0.3s ease;
    }
    
    @keyframes slideDown {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    
    .preview-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        background: linear-gradient(135deg, #f0f4f8 0%, #fff 100%);
    }
    
    .preview-modal-header h4 {
        margin: 0;
        font-size: 1.25rem;
        color: #1e293b;
    }
    
    .preview-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #64748b;
        transition: color 0.2s;
    }
    
    .preview-modal-close:hover {
        color: #1e293b;
    }
    
    .preview-modal-body {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .preview-modal-pdf {
        flex: 1;
        width: 100%;
        border: none;
    }
    
    .preview-modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
        border-top: 1px solid #e5e7eb;
        background: #f8fafc;
    }
    
    .preview-info {
        font-size: 0.875rem;
        color: #64748b;
    }
    
    .preview-actions {
        display: flex;
        gap: 10px;
    }
</style>
@endsection

@section('content')
<div class="transcripts-container fade-in">
    <!-- Control Card -->
    <div class="card control-card">
        <div class="control-header">
            <!-- Search Form -->
            <form action="{{ route('admin.transcripts') }}" method="GET" class="admin-search-form">
                <div class="input-group">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" placeholder="Cari siswa..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('admin.transcripts') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <div class="control-actions" style="display: flex; gap: 10px; align-items: center;">
                <a href="{{ route('admin.transcripts.bulk_pdf', ['search' => request('search')]) }}" class="btn btn-secondary">
                    <i class="fa-solid fa-file-pdf"></i> Unduh PDF Masal
                </a>
                <a href="{{ route('admin.transcripts.settings') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-gear"></i> Pengaturan Transkrip
                </a>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="110">NISN</th>
                        <th>Nama Siswa</th>
                        <th class="text-center" width="75">Smt 1</th>
                        <th class="text-center" width="75">Smt 2</th>
                        <th class="text-center" width="75">Smt 3</th>
                        <th class="text-center" width="75">Smt 4</th>
                        <th class="text-center" width="75">Smt 5</th>
                        <th class="text-center" width="75">Smt 6</th>
                        <th class="text-center" width="90">Nilai Ijazah</th>
                        <th class="text-center" width="95">Nilai Sekolah</th>
                        <th class="text-center" width="110">Status</th>
                        <th class="text-center" width="130">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($students->isEmpty())
                        <tr>
                            <td colspan="11" class="text-center py-4 text-muted">Data siswa tidak ditemukan.</td>
                        </tr>
                    @else
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->nisn }}</td>
                                <td class="font-semibold">{{ $student->name }}</td>
                                
                                <td class="text-center score-avg-value">
                                    {{ $student->getSemesterAverage('Semester 1') !== null ? number_format($student->getSemesterAverage('Semester 1'), 2) : '-' }}
                                </td>
                                <td class="text-center score-avg-value">
                                    {{ $student->getSemesterAverage('Semester 2') !== null ? number_format($student->getSemesterAverage('Semester 2'), 2) : '-' }}
                                </td>
                                <td class="text-center score-avg-value">
                                    {{ $student->getSemesterAverage('Semester 3') !== null ? number_format($student->getSemesterAverage('Semester 3'), 2) : '-' }}
                                </td>
                                <td class="text-center score-avg-value">
                                    {{ $student->getSemesterAverage('Semester 4') !== null ? number_format($student->getSemesterAverage('Semester 4'), 2) : '-' }}
                                </td>
                                <td class="text-center score-avg-value">
                                    {{ $student->getSemesterAverage('Semester 5') !== null ? number_format($student->getSemesterAverage('Semester 5'), 2) : '-' }}
                                </td>
                                <td class="text-center score-avg-value">
                                    {{ $student->getSemesterAverage('Semester 6') !== null ? number_format($student->getSemesterAverage('Semester 6'), 2) : '-' }}
                                </td>
                                <td class="text-center score-avg-value" style="color: #475569;">
                                    {{ $student->getSemesterAverage('Nilai Ijazah') !== null ? number_format($student->getSemesterAverage('Nilai Ijazah'), 2) : '-' }}
                                </td>
                                <td class="text-center score-final-value">
                                    {{ number_format($student->average_score, 2) }}
                                </td>
                                <td class="text-center">
                                    @if($student->status === 'LULUS')
                                        <span class="status-badge status-lulus" style="font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-circle-check"></i> LULUS
                                        </span>
                                    @else
                                        <span class="status-badge status-tidak-lulus" style="font-size: 0.75rem; padding: 4px 8px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-circle-xmark"></i> TIDAK LULUS
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="table-actions" style="display: flex; justify-content: center; gap: 5px;">
                                        <a href="{{ route('admin.transcripts.skl.preview', $student->id) }}" 
                                           target="_blank" 
                                           class="btn btn-success btn-icon-sm" 
                                           title="Pratinjau SKL & Cetak Langsung" 
                                           style="padding: 6px 10px; font-size: 0.85rem; background: var(--success); border-color: var(--success); color: white; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-file-pdf"></i> SKL
                                        </a>
                                        <a href="{{ route('admin.transcripts.preview', $student->id) }}" 
                                           target="_blank" 
                                           class="btn btn-primary btn-icon-sm" 
                                           title="Pratinjau Transkrip & Cetak Langsung" 
                                           style="padding: 6px 10px; font-size: 0.85rem; background: var(--primary); border-color: var(--primary); color: white; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-eye"></i> Transkrip
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper" style="padding: 20px;">
            {{ $students->appends(request()->query())->links('pagination::simple-default') }}
        </div>
    </div>
</div>

@endsection
