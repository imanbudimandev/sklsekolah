@extends('layouts.admin')

@section('title', 'Database Tools')
@section('page_title', 'Database Tools')

@section('content')
<div class="tools-container fade-in">
    <div class="card mb-4">
        <div class="card-header">
            <h3><i class="fa-solid fa-database"></i> Backup Database</h3>
        </div>
        <div class="card-body">
            <p class="text-secondary">Buat backup database MySQL untuk keamanan data. File backup tersimpan di server dan bisa diunduh kapan saja.</p>
            <form action="{{ route('admin.tools.backup') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-circle-plus"></i> Buat Backup Baru
                </button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3><i class="fa-solid fa-file-export"></i> Daftar Backup</h3>
        </div>
        <div class="card-body">
            @if($backups->isEmpty())
                <p class="text-secondary">Belum ada file backup.</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nama File</th>
                                <th>Ukuran</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr>
                                    <td><code>{{ $backup['filename'] }}</code></td>
                                    <td>{{ number_format($backup['size'] / 1024, 2) }} KB</td>
                                    <td>{{ $backup['date']->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        <div style="display: flex; gap: 6px;">
                                            <a href="{{ route('admin.tools.download', $backup['filename']) }}" class="btn btn-sm btn-success">
                                                <i class="fa-solid fa-download"></i> Download
                                            </a>
                                            <form action="{{ route('admin.tools.restore') }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin akan merestore database dari {{ $backup['filename'] }}? Data saat ini akan diganti!')">
                                                @csrf
                                                <input type="hidden" name="backup_file" value="{{ $backup['filename'] }}">
                                                <button type="submit" class="btn btn-sm btn-warning">
                                                    <i class="fa-solid fa-rotate-left"></i> Restore
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.tools.delete', $backup['filename']) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus backup {{ $backup['filename'] }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3><i class="fa-solid fa-broom"></i> Bersihkan Data</h3>
        </div>
        <div class="card-body">
            <p class="text-secondary">Pilih data yang ingin dibersihkan. Data pengguna (admin) dan pengaturan tidak akan terpengaruh.</p>
            <form action="{{ route('admin.tools.clean') }}" method="POST" id="cleanForm" onsubmit="return validateCleanForm()">
                @csrf
                <div class="clean-options">
                    <label class="clean-checkbox">
                        <input type="checkbox" name="tables[]" value="students">
                        <span class="check-content">
                            <i class="fa-solid fa-user-graduate"></i>
                            <strong>Data Siswa</strong>
                            <small>Hapus semua data siswa</small>
                        </span>
                    </label>
                    <label class="clean-checkbox">
                        <input type="checkbox" name="tables[]" value="subjects">
                        <span class="check-content">
                            <i class="fa-solid fa-book"></i>
                            <strong>Mata Pelajaran</strong>
                            <small>Hapus semua mata pelajaran</small>
                        </span>
                    </label>
                    <label class="clean-checkbox">
                        <input type="checkbox" name="tables[]" value="grades">
                        <span class="check-content">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <strong>Manajemen Nilai</strong>
                            <small>Hapus semua nilai siswa</small>
                        </span>
                    </label>
                    <label class="clean-checkbox">
                        <input type="checkbox" name="tables[]" value="letters">
                        <span class="check-content">
                            <i class="fa-solid fa-envelope-open-text"></i>
                            <strong>Surat</strong>
                            <small>Reset data transkrip & SKL (nilai ijazah)</small>
                        </span>
                    </label>
                </div>
                <button type="submit" class="btn btn-danger mt-3" id="cleanBtn" disabled>
                    <i class="fa-solid fa-broom"></i> Bersihkan Data Terpilih
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function validateCleanForm() {
        const checked = document.querySelectorAll('input[name="tables[]"]:checked');
        if (checked.length === 0) {
            alert('Pilih minimal satu kategori data yang akan dibersihkan.');
            return false;
        }
        const labels = [];
        checked.forEach(cb => {
            const label = cb.closest('.clean-checkbox').querySelector('strong').textContent;
            labels.push(label);
        });
        return confirm('Yakin akan membersihkan data berikut?\n- ' + labels.join('\n- ') + '\n\nTindakan ini tidak bisa dibatalkan!');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="tables[]"]');
        const btn = document.getElementById('cleanBtn');
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const checked = document.querySelectorAll('input[name="tables[]"]:checked');
                btn.disabled = checked.length === 0;
            });
        });
    });
</script>
@endsection

@section('styles')
<style>
    .tools-container .card {
        margin-bottom: 24px;
    }
    .tools-container .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e8f0;
        background: transparent;
    }
    .tools-container .card-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #1e293b;
    }
    .tools-container .card-header h3 i {
        margin-right: 8px;
        color: #4f46e5;
    }
    .tools-container .card-body {
        padding: 20px;
    }
    .tools-container .card-body p {
        margin-bottom: 16px;
    }
    .tools-container .table {
        width: 100%;
        border-collapse: collapse;
    }
    .tools-container .table th,
    .tools-container .table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .tools-container .table th {
        font-weight: 600;
        color: #475569;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #f8fafc;
    }
    .tools-container .table td code {
        font-size: 0.85rem;
        color: #1e293b;
    }
    .tools-container .table tr:hover td {
        background: #f8fafc;
    }
    .btn-sm {
        padding: 6px 14px;
        font-size: 0.82rem;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .btn-sm i {
        font-size: 0.8rem;
    }
    .clean-options {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .clean-checkbox {
        display: flex;
        align-items: center;
        padding: 14px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #f8fafc;
    }
    .clean-checkbox:hover {
        border-color: #818cf8;
        background: #eef2ff;
    }
    .clean-checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 14px;
        accent-color: #4f46e5;
        cursor: pointer;
    }
    .clean-checkbox .check-content {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .clean-checkbox .check-content i {
        display: none;
    }
    .clean-checkbox .check-content strong {
        font-size: 0.95rem;
        color: #1e293b;
    }
    .clean-checkbox .check-content small {
        font-size: 0.8rem;
        color: #64748b;
    }
    .mt-3 {
        margin-top: 16px;
    }
    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endsection
