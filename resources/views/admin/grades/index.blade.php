@extends('layouts.admin')

@section('title', 'Manajemen Nilai')
@section('page_title', 'Manajemen Nilai')

@section('styles')
<style>
    .semester-tabs-container {
        margin-top: 15px;
    }
    .semester-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 8px;
    }
    .semester-tab {
        padding: 8px 16px;
        background: var(--white);
        border: 1px solid #cbd5e1;
        border-radius: var(--border-radius-md);
        color: var(--text-dark);
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
        transition: var(--transition-fast);
        font-size: 0.9rem;
    }
    .semester-tab.active {
        background: var(--primary);
        color: var(--white);
        border-color: var(--primary);
        box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
    }
    .semester-tab:hover:not(.active) {
        background: #f1f5f9;
        border-color: #94a3b8;
    }
    .grade-input {
        width: 65px;
        height: 32px;
        padding: 4px;
        border: 1px solid #cbd5e1;
        border-radius: var(--border-radius-sm);
        text-align: center;
        font-weight: 700;
        outline: none;
        transition: var(--transition-fast);
        font-size: 0.85rem;
    }
    .grade-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        background: #fff;
    }
    .grade-input:invalid {
        border-color: var(--danger);
        background-color: #fef2f2;
    }
    /* Sticky Save Bar */
    .save-bar {
        position: sticky;
        bottom: 0;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-top: 2px solid #e2e8f0;
        padding: 16px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 10;
        margin: 20px -24px -24px -24px;
        border-bottom-left-radius: var(--border-radius-lg);
        border-bottom-right-radius: var(--border-radius-lg);
        box-shadow: 0 -4px 10px -3px rgba(0, 0, 0, 0.05);
    }
    .save-bar-info {
        font-size: 0.95rem;
        color: #475569;
    }
    .save-bar-info strong {
        color: var(--primary);
    }
    .grade-header-code {
        cursor: help;
    }

    @media (max-width: 768px) {
        .semester-tabs-container {
            margin-top: 10px;
        }
        .semester-tab {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        .save-bar {
            flex-direction: column;
            gap: 10px;
            padding: 12px 16px;
            margin: 15px -16px -16px -16px;
            text-align: center;
        }
        .save-bar-info {
            font-size: 0.85rem;
        }
        .save-bar-actions .btn {
            width: 100%;
        }
        .control-header > div:first-child {
            flex-direction: column !important;
        }
        .admin-search-form {
            max-width: 100% !important;
        }
        .admin-search-form .input-group input[type="text"] {
            max-width: none;
        }
        .control-actions {
            width: 100%;
        }
        .control-actions .btn {
            flex: 1;
            font-size: 0.85rem;
            padding: 8px 12px;
        }
        .grade-input {
            width: 50px;
            height: 28px;
            font-size: 0.75rem;
            padding: 2px;
        }
        th, td {
            padding: 6px 4px !important;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 480px) {
        .grade-input {
            width: 40px;
            height: 24px;
            font-size: 0.7rem;
        }
        th, td {
            padding: 4px 2px !important;
            font-size: 0.7rem;
        }
        th[width] {
            width: auto !important;
            min-width: auto !important;
        }
    }
</style>
@endsection

@section('content')
<div class="grades-container fade-in">
    <!-- Control Card -->
    <div class="card control-card">
        <div class="control-header" style="flex-direction: column; align-items: stretch; gap: 15px;">
            <!-- Top Controls -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <!-- Search Form -->
                <form action="{{ route('admin.grades') }}" method="GET" class="admin-search-form">
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <div class="input-group">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" name="search" placeholder="Cari siswa..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('admin.grades', ['semester' => $semester]) }}" class="btn btn-secondary">Reset</a>
                        @endif
                    </div>
                </form>

                <!-- Actions -->
                <div class="control-actions" style="display: flex; gap: 10px;">
                    <a href="{{ route('admin.grades.template', ['semester' => $semester]) }}" class="btn btn-secondary">
                        <i class="fa-solid fa-download"></i> Unduh Template Excel
                    </a>
                    <button onclick="openModal('importCsvModal')" class="btn btn-primary">
                        <i class="fa-solid fa-file-import"></i> Impor Excel Nilai
                    </button>
                </div>
            </div>

            <!-- Semester Tabs Navigation -->
            <div class="semester-tabs-container">
                <div class="semester-tabs">
                    @foreach($validSemesters as $sem)
                        <a href="{{ route('admin.grades', ['semester' => $sem, 'search' => request('search')]) }}" 
                           class="semester-tab {{ $semester === $sem ? 'active' : '' }}">
                            {{ $sem }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card containing the bulk edit form -->
    <div class="card table-card">
        <form action="{{ route('admin.grades.store') }}" method="POST">
            @csrf
            <input type="hidden" name="semester" value="{{ $semester }}">

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="110">NISN</th>
                            <th>Nama Siswa</th>
                            @foreach($subjects as $subject)
                                <th class="text-center grade-header-code" title="{{ $subject->name }}" width="80">
                                    {{ $subject->code }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @if($students->isEmpty())
                            <tr>
                                <td colspan="{{ count($subjects) + 2 }}" class="text-center py-4 text-muted">
                                    Data siswa tidak ditemukan.
                                </td>
                            </tr>
                        @else
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->nisn }}</td>
                                    <td class="font-semibold">{{ $student->name }}</td>
                                    
                                    @foreach($subjects as $subject)
                                        @php
                                            $grade = $student->grades->firstWhere('subject_id', $subject->id);
                                        @endphp
                                        <td class="text-center">
                                            <input type="number" 
                                                   name="grades[{{ $student->id }}][{{ $subject->id }}]" 
                                                   class="grade-input" 
                                                   min="0" 
                                                   max="100" 
                                                   step="0.01" 
                                                   value="{{ $grade ? $grade->score : '' }}" 
                                                   placeholder="-">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Sticky Save Bar -->
            <div class="save-bar">
                <div class="save-bar-info">
                    Sedang mengedit nilai untuk: <strong>{{ $semester }}</strong>
                </div>
                <div class="save-bar-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Nilai
                    </button>
                </div>
            </div>
        </form>

        <!-- Pagination -->
        <div class="pagination-wrapper" style="padding: 20px;">
            {{ $students->appends(['semester' => $semester, 'search' => request('search')])->links('pagination::simple-default') }}
        </div>
    </div>
</div>

<!-- ================= IMPOR EXCEL MODAL ================= -->
<div id="importCsvModal" class="modal">
    <div class="modal-content card glass">
        <div class="modal-header">
            <h3>Impor Nilai Excel ({{ $semester }})</h3>
            <span class="close-modal" onclick="closeModal('importCsvModal')">&times;</span>
        </div>
        <form action="{{ route('admin.grades.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="semester" value="{{ $semester }}">
            <div class="modal-body">
                <p>Unggah file Excel berisi nilai untuk semester <strong>{{ $semester }}</strong>.</p>
                
                <div class="form-group py-3">
                    <label for="file_import" class="btn-file-upload">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Pilih File Excel</span>
                        <input type="file" id="file_import" name="file_import" accept=".xlsx,.xls" required onchange="displayFileName(this)">
                    </label>
                    <p id="file-name" class="text-center text-muted mt-2"></p>
                </div>

                <div class="template-download-section alert alert-info" style="text-align: left;">
                    <div class="alert-content" style="display: block;">
                        <i class="fa-solid fa-circle-info"></i>
                        <span style="display: block; margin-top: 6px;">Format kolom: <strong>No</strong>, <strong>NIS</strong>, <strong>NAMA SISWA</strong>, diikuti kode mapel urut.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('importCsvModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Mulai Impor</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // General Modal Open/Close
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "flex";
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    // Display File Name in Upload
    function displayFileName(input) {
        const file = input.files[0];
        if (file) {
            document.getElementById('file-name').innerText = "File terpilih: " + file.name;
        }
    }

    // Close modals on clicking outside content
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }
</script>
@endsection
