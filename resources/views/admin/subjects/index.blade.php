@extends('layouts.admin')

@section('title', 'Kelola Mata Pelajaran')
@section('page_title', 'Mata Pelajaran')

@section('content')
<div class="subjects-container fade-in">
    <!-- Control Card -->
    <div class="card control-card">
        <div class="control-header">
            <!-- Search Form -->
            <form action="{{ route('admin.subjects') }}" method="GET" class="admin-search-form">
                <div class="input-group">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" placeholder="Cari kode, nama mapel, kelompok..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('admin.subjects') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Actions -->
            <div class="control-actions">
                <button onclick="openModal('addSubjectModal')" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Tambah Mapel
                </button>
                <button onclick="openModal('importCsvModal')" class="btn btn-secondary">
                    <i class="fa-solid fa-file-import"></i> Impor CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="150">Kode Mapel</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Kategori / Kelompok</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($subjects->isEmpty())
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Mata pelajaran tidak ditemukan.</td>
                        </tr>
                    @else
                        @foreach($subjects as $subject)
                            <tr>
                                <td class="font-bold">{{ $subject->code }}</td>
                                <td>{{ $subject->name }}</td>
                                <td><span class="badge-category">{{ $subject->category ?? '-' }}</span></td>
                                <td class="text-center">
                                    <div class="table-actions">
                                        <!-- Edit button -->
                                        <button 
                                            onclick="openEditModal({{ json_encode($subject) }})" 
                                            class="btn btn-warning btn-icon-sm" 
                                            title="Edit">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                        
                                        <!-- Delete form -->
                                        <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini? Semua nilai siswa untuk mapel ini juga akan terhapus!')" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-icon-sm" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $subjects->links('pagination::simple-default') }}
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- ADD SUBJECT MODAL -->
<div id="addSubjectModal" class="modal">
    <div class="modal-content card glass">
        <div class="modal-header">
            <h3>Tambah Mata Pelajaran</h3>
            <span class="close-modal" onclick="closeModal('addSubjectModal')">&times;</span>
        </div>
        <form action="{{ route('admin.subjects.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="add_code">Kode Mapel <span class="required">*</span></label>
                    <input type="text" id="add_code" name="code" placeholder="Contoh: PAI, MTK, IND" required autocomplete="off">
                    <p class="form-help">Gunakan singkatan huruf besar unik, minimal 2 karakter.</p>
                </div>
                <div class="form-group">
                    <label for="add_name">Nama Mata Pelajaran <span class="required">*</span></label>
                    <input type="text" id="add_name" name="name" placeholder="Contoh: Matematika" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="add_category">Kategori / Kelompok</label>
                    <input type="text" id="add_category" name="category" placeholder="Contoh: Kelompok A, Kelompok B, Muatan Lokal" autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addSubjectModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT SUBJECT MODAL -->
<div id="editSubjectModal" class="modal">
    <div class="modal-content card glass">
        <div class="modal-header">
            <h3>Edit Mata Pelajaran</h3>
            <span class="close-modal" onclick="closeModal('editSubjectModal')">&times;</span>
        </div>
        <form id="editSubjectForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_code">Kode Mapel <span class="required">*</span></label>
                    <input type="text" id="edit_code" name="code" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="edit_name">Nama Mata Pelajaran <span class="required">*</span></label>
                    <input type="text" id="edit_name" name="name" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label for="edit_category">Kategori / Kelompok</label>
                    <input type="text" id="edit_category" name="category" autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editSubjectModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- IMPORT CSV MODAL -->
<div id="importCsvModal" class="modal">
    <div class="modal-content card glass">
        <div class="modal-header">
            <h3>Impor Mata Pelajaran</h3>
            <span class="close-modal" onclick="closeModal('importCsvModal')">&times;</span>
        </div>
        <form action="{{ route('admin.subjects.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p>Unggah file CSV yang berisi daftar mata pelajaran.</p>
                
                <div class="form-group py-3">
                    <label for="csv_file" class="btn-file-upload">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Pilih File CSV</span>
                        <input type="file" id="csv_file" name="csv_file" accept=".csv,.txt" required onchange="displayFileName(this)">
                    </label>
                    <p id="file-name" class="text-center text-muted mt-2"></p>
                </div>

                <div class="template-download-section alert alert-info">
                    <div class="alert-content">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>File CSV harus memiliki kolom header: <strong>code, name, category</strong>.</span>
                    </div>
                    <a href="{{ route('admin.subjects.template') }}" class="btn btn-secondary btn-sm mt-2 btn-block">
                        <i class="fa-solid fa-download"></i> Unduh Template CSV Mapel
                    </a>
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

    // Open Edit Modal and Populate Fields
    function openEditModal(subject) {
        // Set form action URL
        const formAction = `/admin/subjects/${subject.id}`;
        document.getElementById('editSubjectForm').setAttribute('action', formAction);

        // Populate fields
        document.getElementById('edit_code').value = subject.code;
        document.getElementById('edit_name').value = subject.name;
        document.getElementById('edit_category').value = subject.category || '';

        openModal('editSubjectModal');
    }

    // Close modals on clicking outside content
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }
</script>
@endsection
