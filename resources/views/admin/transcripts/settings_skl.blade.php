@extends('layouts.admin')

@section('title', 'Pengaturan SKL')
@section('page_title', 'Pengaturan Surat Keterangan Lulus (SKL)')

@section('styles')
<style>
    .skl-settings {
        margin-top: 15px;
    }
    .form-section {
        display: grid;
        gap: 20px;
    }
    .form-section h5 {
        margin-top: 25px;
        margin-bottom: 15px;
        font-weight: 600;
        color: #0f172a;
        border-bottom: 2px solid #e2e8f0;
        padding-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-section h5 i {
        color: var(--primary);
    }
    .form-section h5:first-child {
        margin-top: 0;
    }
    .form-row-flex {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    .form-group label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
        display: block;
    }
    .btn-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .preview-section {
        background: linear-gradient(135deg, #f0f4f8 0%, #fff 100%);
        border-radius: 12px;
        padding: 24px;
        margin-top: 30px;
        border: 1px solid #cbd5e1;
    }
    .preview-section h5 {
        margin-top: 0;
        border-bottom: none;
        margin-bottom: 20px;
        font-weight: 700;
        color: #1e293b;
    }
    .preview-box {
        border: 1px solid #94a3b8;
        border-radius: 8px;
        padding: 30px;
        background: white;
        display: none;
        margin-top: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        font-family: 'Helvetica', 'Arial', sans-serif;
    }
    .preview-box.visible {
        display: block;
    }
    .preview-header-section {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
    }
    .preview-logo {
        width: 70px;
        height: 70px;
        object-fit: contain;
    }
    .preview-meta {
        font-size: 0.9rem;
        color: #334155;
        margin-top: 15px;
        line-height: 1.6;
    }
    .preview-divider {
        border: none;
        border-top: 2px solid #0f172a;
        margin: 10px 0;
    }
    .logo-preview-wrapper {
        margin-bottom: 10px;
    }
    .logo-preview-wrapper img {
        max-height: 70px;
        border-radius: 4px;
        border: 1px solid #e2e8f0;
    }
    .help-card {
        background-color: #eff6ff;
        border-left: 4px solid #3b82f6;
        padding: 12px 16px;
        border-radius: 4px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        color: #1e3a8a;
    }
    .help-card ul {
        margin-left: 20px;
        margin-top: 6px;
    }
</style>
@endsection

@section('content')
<div class="skl-settings fade-in">
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade-in">
                    <div class="alert-content">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                    <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
                </div>
            @endif

            <div class="help-card">
                <i class="fa-solid fa-circle-info"></i> <strong>Pengaturan Nomor Surat</strong>
                Pengaturan format nomor surat dan nomor awal telah dipindah ke <a href="{{ route('admin.settings') }}">Pengaturan Aplikasi</a> &mdash; berlaku untuk SKL dan Transkrip.
            </div>

            <form action="{{ route('admin.skl.update_settings') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-section">
                    <!-- Logo & Header Section -->
                    <div>
                        <h5><i class="fa-solid fa-image"></i> Logo & Kop Surat SKL</h5>
                        <div class="form-group">
                            <label>Logo Khusus SKL</label>
                            @if(!empty($settings['skl_logo']) && file_exists(public_path($settings['skl_logo'])))
                                <div class="logo-preview-wrapper"><img id="preview-logo" src="{{ asset($settings['skl_logo']) }}" alt="logo"></div>
                            @else
                                <div class="logo-preview-wrapper"><img id="preview-logo" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="logo" style="display:none;"></div>
                            @endif
                            <input type="file" name="skl_logo" id="skl_logo" class="form-control" accept="image/*">
                            <small class="text-muted">Jika dikosongkan, sistem akan otomatis menggunakan Logo Sekolah Global.</small>
                        </div>

                        <div class="form-group" style="margin-top: 15px; margin-bottom: 15px;">
                            <label>Tipe Kop Surat SKL</label>
                            <select name="skl_header_type" id="skl_header_type" class="form-control" onchange="toggleHeaderType(this.value)">
                                <option value="text" {{ ($settings['skl_header_type'] ?? 'text') === 'text' ? 'selected' : '' }}>Kop Teks (Editor Visual)</option>
                                <option value="image" {{ ($settings['skl_header_type'] ?? 'text') === 'image' ? 'selected' : '' }}>Kop Gambar (Upload File)</option>
                            </select>
                        </div>

                        <div id="header-text-container" style="{{ ($settings['skl_header_type'] ?? 'text') === 'text' ? 'display: block;' : 'display: none;' }}">
                            <div class="form-group">
                                <label>Kop Surat / Header SKL (Editor Visual)</label>
                                @php
                                    $skl_header_val = old('skl_header', $settings['skl_header']);
                                    if (strip_tags($skl_header_val) === $skl_header_val) {
                                        $skl_header_val = nl2br($skl_header_val);
                                    }
                                @endphp
                                <textarea name="skl_header" id="skl_header" class="form-control rich-text" rows="5" placeholder="Ketik desain kop surat di sini...">{!! $skl_header_val !!}</textarea>
                                <small class="text-muted">Gunakan editor di atas atau biarkan kosong untuk menggunakan layout Kop Surat formal bawaan.</small>
                            </div>
                        </div>

                        <div id="header-image-container" style="{{ ($settings['skl_header_type'] ?? 'text') === 'image' ? 'display: block;' : 'display: none;' }}">
                            <div class="form-group">
                                <label>Kop Gambar SKL</label>
                                @if(!empty($settings['skl_header_image']) && file_exists(public_path($settings['skl_header_image'])))
                                    <div class="logo-preview-wrapper"><img id="preview-header-image" src="{{ asset($settings['skl_header_image']) }}" alt="kop_gambar" style="max-height: 100px; border-radius: 4px; display: block; margin-bottom: 8px; border: 1px solid #e2e8f0;"></div>
                                @else
                                    <div class="logo-preview-wrapper"><img id="preview-header-image" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="kop_gambar" style="max-height: 100px; border-radius: 4px; display: none; margin-bottom: 8px; border: 1px solid #e2e8f0;"></div>
                                @endif
                                <input type="file" name="skl_header_image" id="skl_header_image" class="form-control" accept="image/*" onchange="previewHeaderImage(this)">
                                <small class="text-muted">Rekomendasi ukuran: 800px x 150px (proporsional 16:3). Format: JPG, PNG, JPEG, SVG.</small>
                            </div>
                        </div>
                    </div>



                    <!-- Texts Section -->
                    <div style="margin-top: 30px;">
                        <h5><i class="fa-solid fa-align-left"></i> Teks Isi Surat</h5>
                        
                        <div class="form-group">
                            <label>Teks Pembuka (Sebelum Data Siswa)</label>
                            <textarea name="skl_opening_text" id="skl_opening_text" class="form-control rich-text" rows="2" placeholder="Yang bertanda tangan di bawah ini, Kepala Sekolah...">{{ old('skl_opening_text', $settings['skl_opening_text']) }}</textarea>
                            <small class="text-muted">Variabel yang tersedia: <code>[NAMA_SEKOLAH]</code></small>
                        </div>
                        
                        <div class="form-group">
                            <label>Teks Pernyataan (Setelah Data Siswa)</label>
                            <textarea name="skl_body_text" id="skl_body_text" class="form-control rich-text" rows="3" placeholder="Berdasarkan Kriteria Kelulusan Peserta Didik...">{{ old('skl_body_text', $settings['skl_body_text']) }}</textarea>
                            <small class="text-muted">Variabel yang tersedia: <code>[NAMA_SEKOLAH]</code>, <code>[TAHUN_PELAJARAN]</code>, <code>[TANGGAL_PENGUMUMAN]</code></small>
                        </div>
                        
                        <div class="form-group">
                            <label>Catatan Kaki (Bawah Surat)</label>
                            <textarea name="skl_footer_text" id="skl_footer_text" class="form-control rich-text" rows="2" placeholder="* Surat Keterangan Lulus ini berlaku sementara...">{{ old('skl_footer_text', $settings['skl_footer_text']) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Teks Setelah Status Kelulusan (Sebelum Tabel Nilai)</label>
                            <textarea name="skl_after_lulus_text" id="skl_after_lulus_text" class="form-control rich-text" rows="2" placeholder="Teks yang muncul setelah kotak LULUS/TIDAK LULUS...">{{ old('skl_after_lulus_text', $settings['skl_after_lulus_text']) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label>Teks Sebelum Tanda Tangan (Setelah Tabel Nilai)</label>
                            <textarea name="skl_before_ttd_text" id="skl_before_ttd_text" class="form-control rich-text" rows="3" placeholder="SKL ini dapat digunakan untuk keperluan Penerimaan Peserta Didik Baru (PPDB)...">{{ old('skl_before_ttd_text', $settings['skl_before_ttd_text']) }}</textarea>
                            <small class="text-muted">Teks yang muncul setelah tabel nilai, sebelum tanda tangan.</small>
                        </div>
                    </div>

                    <!-- Document Details Section -->
                    <div>
                        <h5><i class="fa-solid fa-file-contract"></i> Detail Dokumen SKL</h5>
                        <div class="form-row-flex">
                            <div class="form-group">
                                <label>Tempat Penandatanganan</label>
                                <input type="text" name="skl_place" id="skl_place" class="form-control" placeholder="Banjaran" value="{{ old('skl_place', $settings['skl_place']) }}">
                            </div>
                            <div class="form-group">
                                <label>Format Tampilan Tanggal</label>
                                <select name="skl_date_format" id="skl_date_format" class="form-control">
                                    <option value="d F Y" {{ $settings['skl_date_format'] == 'd F Y' ? 'selected' : '' }}>29 Mei 2026</option>
                                    <option value="j F Y" {{ $settings['skl_date_format'] == 'j F Y' ? 'selected' : '' }}>29 Mei 2026</option>
                                    <option value="d M Y" {{ $settings['skl_date_format'] == 'd M Y' ? 'selected' : '' }}>29 Mei 2026</option>
                                    <option value="d/m/Y" {{ $settings['skl_date_format'] == 'd/m/Y' ? 'selected' : '' }}>29/05/2026</option>
                                    <option value="d-m-Y" {{ $settings['skl_date_format'] == 'd-m-Y' ? 'selected' : '' }}>29-05-2026</option>
                                    <option value="Y-m-d" {{ $settings['skl_date_format'] == 'Y-m-d' ? 'selected' : '' }}>2026-05-29</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top: 15px;">
                            <label>Jabatan Penandatangan (Teks Tanda Tangan)</label>
                            <input type="text" name="skl_signature_text" id="skl_signature_text" class="form-control" placeholder="Kepala Sekolah," value="{{ old('skl_signature_text', $settings['skl_signature_text']) }}">
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-check"></i> Simpan Pengaturan
                        </button>
                        <a href="{{ route('admin.transcripts') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Kembali ke Cetak
                        </a>
                        <button type="button" id="btn-preview" class="btn btn-outline-primary">
                            <i class="fa-solid fa-eye"></i> Pratinjau Desain
                        </button>
                    </div>
                </div>
            </form>

            <!-- Preview Section -->
            <div class="preview-section">
                <h5><i class="fa-solid fa-eye"></i> Pratinjau Desain Surat SKL</h5>
                <div id="skl-preview" class="preview-box">
                    <div class="preview-header-wrapper" id="preview-header-wrapper">
                        <!-- Header Layout -->
                        <div class="preview-header-mock" id="preview-header-mock" style="display: flex; align-items: center; gap: 15px; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px;">
                            <img id="preview-logo-large" src="{{ (!empty($settings['skl_logo']) && file_exists(public_path($settings['skl_logo']))) ? asset($settings['skl_logo']) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' }}" alt="logo" class="preview-logo" style="width: 60px; height: 60px; {{ (!empty($settings['skl_logo']) && file_exists(public_path($settings['skl_logo']))) ? '' : 'display:none;' }}">
                            <div style="flex: 1; text-align: center;">
                                <div id="default-header-content">
                                    <h4 style="margin: 0; text-transform: uppercase; font-size: 11pt;">YAYASAN NURUL IHSAN BANJARAN</h4>
                                    <h3 style="margin: 0; font-size: 14pt; text-transform: uppercase;">SMP NURUL IHSAN</h3>
                                    <p style="margin: 0; font-size: 8pt; color: #555;">Jl. Raya Banjaran No. 123, Bandung, Jawa Barat</p>
                                </div>
                                <div id="custom-header-preview" style="display:none; font-size: 11pt; font-weight: bold; line-height: 1.3;"></div>
                            </div>
                        </div>
                    
                    <div style="text-align: center; margin-bottom: 20px;">
                        <h3 style="text-decoration: underline; font-size: 13pt; font-weight: bold; margin: 0;">SURAT KETERANGAN LULUS</h3>
                        <p style="margin: 3px 0 0 0; font-size: 9pt;">Nomor: <span id="preview-letter-number" style="font-weight: bold;">-</span></p>
                    </div>

                    <div style="font-size: 9.5pt; text-align: justify; line-height: 1.5;">
                        <p style="text-indent: 30px; margin-bottom: 12px;">Yang bertanda tangan di bawah ini, Kepala Sekolah SMP Nurul Ihsan menerangkan bahwa:</p>
                        
                        <table style="width: 80%; margin: 10px auto; font-size: 9.5pt; border-collapse: collapse;">
                            <tr>
                                <td style="width: 150px; font-weight: bold; padding: 3px 0;">Nama Lengkap</td>
                                <td style="width: 15px; text-align: center;">:</td>
                                <td style="font-weight: bold; text-transform: uppercase;">SISWA CONTOH PRATINJAU</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; padding: 3px 0;">Tempat, Tanggal Lahir</td>
                                <td>:</td>
                                <td>Bandung, 12 Desember 2010</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; padding: 3px 0;">NISN</td>
                                <td>:</td>
                                <td>0102030405</td>
                            </tr>
                        </table>

                        <p style="margin-top: 15px;">Dinyatakan: <strong style="color: green; text-decoration: underline;">L U L U S</strong></p>
                    </div>

                    <div style="margin-top: 30px; display: flex; justify-content: flex-end;">
                        <div style="text-align: left; width: 250px;">
                            <p style="margin: 0;"><span id="preview-place">Banjaran</span>, <span id="preview-date">{{ date('d F Y') }}</span></p>
                            <p style="margin: 2px 0 0 0;" id="preview-signature-text">Kepala Sekolah,</p>
                            <div style="height: 50px;"></div>
                            <p style="font-weight: bold; text-decoration: underline; margin: 0;">NAMA KEPALA SEKOLAH</p>
                            <p style="font-size: 8.5pt; color: #555; margin: 0;">NIP. 197503122002121002</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btn-preview');
    const preview = document.getElementById('skl-preview');
    const headerInput = document.getElementById('skl_header');
    const letterInput = document.getElementById('skl_letter_number');
    const placeInput = document.getElementById('skl_place');
    const dateFormatInput = document.getElementById('skl_date_format');
    const sigTextInput = document.getElementById('skl_signature_text');
    
    const previewLetter = document.getElementById('preview-letter-number');
    const previewPlace = document.getElementById('preview-place');
    const previewDate = document.getElementById('preview-date');
    const previewSigText = document.getElementById('preview-signature-text');
    
    const logoInput = document.getElementById('skl_logo');
    const previewLogoLarge = document.getElementById('preview-logo-large');
    const previewLogoSmall = document.getElementById('preview-logo');
    const defaultMockHeader = document.getElementById('default-header-content');
    const customHeaderPreview = document.getElementById('custom-header-preview');

    function formatDate(format) {
        const d = new Date();
        const day = String(d.getDate()).padStart(2, '0');
        const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const month = monthNames[d.getMonth()];
        const year = d.getFullYear();
        return format.replace('d', day).replace('F', month).replace('Y', year).replace('m', String(d.getMonth() + 1).padStart(2, '0'));
    }

    btn.addEventListener('click', function() {
        // Handle Header
        if (headerInput.value.trim() !== '') {
            defaultMockHeader.style.display = 'none';
            customHeaderPreview.innerHTML = headerInput.value;
            customHeaderPreview.style.display = 'block';
        } else {
            defaultMockHeader.style.display = 'flex';
            customHeaderPreview.style.display = 'none';
        }

        // Handle letter number
        const letterVal = letterInput.value || '421.3/[NUMBER]/SMP.NI/[YEAR]';
        const start = parseInt(document.getElementById('skl_number_start').value) || 1;
        const yearNow = new Date().getFullYear();
        previewLetter.textContent = letterVal.replace(/\[NUMBER(?::(\d+))?\]/g, function(m, w) {
            const num = start.toString();
            return w ? num.padStart(parseInt(w), '0') : num;
        }).replace('[YEAR]', yearNow);

        // Place and dates
        previewPlace.textContent = placeInput.value || 'Banjaran';
        const fmt = dateFormatInput.value || 'd F Y';
        previewDate.textContent = formatDate(fmt);

        // Signature Text
        previewSigText.textContent = sigTextInput.value || 'Kepala Sekolah,';

        // Show card
        preview.classList.add('visible');
        preview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    if (logoInput) {
        logoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                previewLogoLarge.src = ev.target.result;
                previewLogoLarge.style.display = 'block';
                if (previewLogoSmall) {
                    previewLogoSmall.src = ev.target.result;
                    previewLogoSmall.style.display = 'block';
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // Initialize TinyMCE
    tinymce.init({
        selector: '.rich-text',
        menubar: false,
        plugins: 'lists link',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect | forecolor backcolor',
        height: 250,
        branding: false,
        setup: function (editor) {
            editor.on('change keyup', function () {
                editor.save();
                // Update preview if it's the header
                if (editor.id === 'skl_header') {
                    const content = editor.getContent().trim();
                    if (content !== '') {
                        defaultMockHeader.style.display = 'none';
                        customHeaderPreview.innerHTML = content;
                        customHeaderPreview.style.display = 'block';
                    } else {
                        defaultMockHeader.style.display = 'flex';
                        customHeaderPreview.style.display = 'none';
                    }
                }
            });
        }
    });
    window.toggleHeaderType = function(type) {
        const textContainer = document.getElementById('header-text-container');
        const imageContainer = document.getElementById('header-image-container');
        if (type === 'image') {
            textContainer.style.display = 'none';
            imageContainer.style.display = 'block';
        } else {
            textContainer.style.display = 'block';
            imageContainer.style.display = 'none';
        }
    };
    
    window.previewHeaderImage = function(input) {
        const preview = document.getElementById('preview-header-image');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    };
});
</script>
@endsection
