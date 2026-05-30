@extends('layouts.admin')

@section('title', 'Kelola Data Siswa')
@section('page_title', 'Data Siswa')

@section('content')
<div class="students-container fade-in">
    <!-- Top Control Card -->
    <div class="card control-card">
        <div class="control-header">
            <!-- Search Form -->
            <form action="{{ route('admin.students') }}" method="GET" class="admin-search-form">
                <div class="input-group">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" placeholder="Cari nama, NISN, kelas..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">Cari</button>
                    @if(request('search'))
                        <a href="{{ route('admin.students') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </div>
            </form>

            <!-- Action Buttons -->
            <div class="control-actions">
                <button onclick="openModal('addStudentModal')" class="btn btn-primary">
                    <i class="fa-solid fa-user-plus"></i> Tambah Siswa
                </button>
                <button onclick="openModal('importCsvModal')" class="btn btn-secondary">
                    <i class="fa-solid fa-file-import"></i> Impor Excel
                </button>
                <a href="{{ route('admin.students.export') }}" class="btn btn-success">
                    <i class="fa-solid fa-file-export"></i> Ekspor Excel
                </a>
            </div>
        </div>
    </div>

    <!-- Student Table Card -->
    <div class="card table-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <th>TTL</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                        <th>Tahun Lulus</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if($students->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">Data siswa tidak ditemukan.</td>
                        </tr>
                    @else
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->nis ?? '-' }}</td>
                                <td>{{ $student->nisn }}</td>
                                <td><strong>{{ $student->name }}</strong></td>
                                <td>
                                    {{ $student->birth_place ?? '-' }}, 
                                    {{ $student->birth_date ? $student->birth_date->format('d/m/Y') : '-' }}
                                </td>
                                <td>{{ $student->class }}</td>
                                <td>{{ $student->jurusan ?? '-' }}</td>
                                <td>
                                    @if($student->status === 'LULUS')
                                        <span class="badge badge-success">LULUS</span>
                                    @else
                                        <span class="badge badge-danger">TIDAK LULUS</span>
                                    @endif
                                </td>
                                <td>{{ $student->tahun_lulus ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="table-actions">
                                        <!-- Edit button -->
                                        <button 
                                            onclick="openEditModal({{ json_encode($student) }})" 
                                            class="btn btn-warning btn-icon-sm" 
                                            title="Edit">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                        
                                        <!-- Delete form -->
                                        <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')" style="display:inline-block;">
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
            {{ $students->links('pagination::simple-default') }}
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- ADD STUDENT MODAL -->
<div id="addStudentModal" class="modal">
    <div class="modal-content modal-lg card glass">
        <div class="modal-header">
            <h3>Tambah Siswa Baru</h3>
            <span class="close-modal" onclick="closeModal('addStudentModal')">&times;</span>
        </div>
        <form action="{{ route('admin.students.store') }}" method="POST">
            @csrf
            <div class="modal-body scrollable-y">
                <!-- Meta data siswa -->
                <h4 class="form-section-title">Informasi Pribadi</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="add_exam_number">Nomor Peserta Ujian</label>
                        <input type="text" id="add_exam_number" name="exam_number" placeholder="Contoh: 02-001-001-1" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="add_nis">NIS</label>
                        <input type="text" id="add_nis" name="nis" placeholder="Nomor Induk Siswa" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="add_nisn">NISN <span class="required">*</span></label>
                        <input type="text" id="add_nisn" name="nisn" placeholder="Contoh: 1234567890" required autocomplete="off">
                    </div>
                    <div class="form-group col-span-2">
                        <label for="add_name">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" id="add_name" name="name" placeholder="Nama lengkap siswa" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="add_birth_place">Tempat Lahir</label>
                        <input type="text" id="add_birth_place" name="birth_place" placeholder="Contoh: Bandung" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="add_birth_date">Tanggal Lahir</label>
                        <input type="date" id="add_birth_date" name="birth_date">
                    </div>
                    <div class="form-group">
                        <label for="add_class">Kelas</label>
                        <input type="text" id="add_class" name="class" placeholder="Contoh: IX-A" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="add_jurusan">Jurusan</label>
                        <input type="text" id="add_jurusan" name="jurusan" placeholder="Contoh: IPA / IPS" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="add_status">Status Kelulusan <span class="required">*</span></label>
                        <select id="add_status" name="status" required>
                            <option value="LULUS">LULUS</option>
                            <option value="TIDAK LULUS">TIDAK LULUS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_password">Password</label>
                        <input type="text" id="add_password" name="password" placeholder="Password login siswa" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="add_tahun_lulus">Tahun Lulus</label>
                        <input type="text" id="add_tahun_lulus" name="tahun_lulus" placeholder="Contoh: 2026" autocomplete="off">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addStudentModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT STUDENT MODAL -->
<div id="editStudentModal" class="modal">
    <div class="modal-content modal-lg card glass">
        <div class="modal-header">
            <h3>Edit Data Siswa</h3>
            <span class="close-modal" onclick="closeModal('editStudentModal')">&times;</span>
        </div>
        <form id="editStudentForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body scrollable-y">
                <!-- Meta data siswa -->
                <h4 class="form-section-title">Informasi Pribadi</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_exam_number">Nomor Peserta Ujian <span class="required">*</span></label>
                        <input type="text" id="edit_exam_number" name="exam_number" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="edit_nis">NIS</label>
                        <input type="text" id="edit_nis" name="nis" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="edit_nisn">NISN <span class="required">*</span></label>
                        <input type="text" id="edit_nisn" name="nisn" required autocomplete="off">
                    </div>
                    <div class="form-group col-span-2">
                        <label for="edit_name">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" id="edit_name" name="name" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="edit_birth_place">Tempat Lahir</label>
                        <input type="text" id="edit_birth_place" name="birth_place" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="edit_birth_date">Tanggal Lahir</label>
                        <input type="date" id="edit_birth_date" name="birth_date">
                    </div>
                    <div class="form-group">
                        <label for="edit_class">Kelas</label>
                        <input type="text" id="edit_class" name="class" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="edit_jurusan">Jurusan</label>
                        <input type="text" id="edit_jurusan" name="jurusan" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="edit_status">Status Kelulusan <span class="required">*</span></label>
                        <select id="edit_status" name="status" required>
                            <option value="LULUS">LULUS</option>
                            <option value="TIDAK LULUS">TIDAK LULUS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Password</label>
                        <input type="text" id="edit_password" name="password" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="edit_tahun_lulus">Tahun Lulus</label>
                        <input type="text" id="edit_tahun_lulus" name="tahun_lulus" autocomplete="off">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- IMPORT CSV MODAL -->
<div id="importCsvModal" class="modal">
    <div class="modal-content card glass">
        <div class="modal-header">
            <h3>Impor Data Siswa & Nilai</h3>
            <span class="close-modal" onclick="closeModal('importCsvModal')">&times;</span>
        </div>
        <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <p>Unggah file Excel berisi data siswa. Format kolom: <strong>NIS, NISN, NAMA, TEMPAT, TANGGAL_LAHIR, KELAS, JURUSAN, KELULUSAN, PASSWORD, TAHUN_LULUS</strong>.</p>
                
                <div class="form-group py-3">
                    <label for="import_file" class="btn-file-upload">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Pilih File Excel</span>
                        <input type="file" id="import_file" name="file" accept=".xlsx,.xls" required onchange="displayFileName(this)">
                    </label>
                    <p id="file-name" class="text-center text-muted mt-2"></p>
                </div>

                <div class="template-download-section alert alert-info">
                    <div class="alert-content">
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Gunakan template Excel resmi agar format data Anda tidak salah.</span>
                    </div>
                    <a href="{{ route('admin.students.template') }}" class="btn btn-secondary btn-sm mt-2 btn-block">
                        <i class="fa-solid fa-download"></i> Unduh Template Excel Siswa
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
    function openEditModal(student) {
        const formAction = `/admin/students/${student.id}`;
        document.getElementById('editStudentForm').setAttribute('action', formAction);

        document.getElementById('edit_exam_number').value = student.exam_number;
        document.getElementById('edit_nis').value = student.nis || '';
        document.getElementById('edit_nisn').value = student.nisn;
        document.getElementById('edit_name').value = student.name;
        document.getElementById('edit_birth_place').value = student.birth_place || '';
        document.getElementById('edit_class').value = student.class || '';
        document.getElementById('edit_jurusan').value = student.jurusan || '';
        document.getElementById('edit_status').value = student.status;
        document.getElementById('edit_password').value = student.password || '';
        document.getElementById('edit_tahun_lulus').value = student.tahun_lulus || '';

        if (student.birth_date) {
            const date = new Date(student.birth_date);
            const yyyy = date.getFullYear();
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            document.getElementById('edit_birth_date').value = `${yyyy}-${mm}-${dd}`;
        } else {
            document.getElementById('edit_birth_date').value = '';
        }

        openModal('editStudentModal');
    }

    // Auto-open modals via URL params (useful for Quick Actions on Dashboard)
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('add')) {
            openModal('addStudentModal');
        } else if (urlParams.has('import')) {
            openModal('importCsvModal');
        }
    });

    // Close modals on clicking outside content
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = "none";
        }
    }
</script>
@endsection
