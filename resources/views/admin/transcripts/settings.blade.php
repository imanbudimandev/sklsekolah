@extends('layouts.admin')

@section('title', 'Setting Transkrip')
@section('page_title', 'Pengaturan Transkrip Nilai')

@section('styles')
<style>
    .transcript-settings {
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
        color: #1e293b;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 8px;
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
        font-weight: 500;
        color: #334155;
        margin-bottom: 6px;
    }
    .btn-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
    .preview-section {
        background: linear-gradient(135deg, #f0f4f8 0%, #fff 100%);
        border-radius: 8px;
        padding: 24px;
        margin-top: 30px;
    }
    .preview-section h5 {
        margin-top: 0;
        border-bottom: none;
        margin-bottom: 20px;
    }
    .preview-box {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 20px;
        background: white;
        display: none;
        margin-top: 15px;
    }
    .preview-box.visible {
        display: block;
    }
    .preview-header-section {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }
    .preview-logo {
        width: 70px;
        height: 70px;
        object-fit: contain;
    }
    .preview-meta {
        font-size: 0.9rem;
        color: #475569;
        margin-top: 15px;
        line-height: 1.6;
    }
    .preview-divider {
        border: none;
        border-top: 1px solid #e5e7eb;
        margin: 15px 0;
    }
    .logo-preview-wrapper {
        margin-bottom: 10px;
    }
    .logo-preview-wrapper img {
        max-height: 70px;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<div class="transcript-settings">
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.transcripts.update_settings') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-section">
                    <!-- Logo & Header Section -->
                    <div>
                        <h5><i class="fa-solid fa-image"></i> Logo & Kop Surat</h5>
                        <div class="form-group">
                            <label>Logo Transkrip</label>
                            @if(!empty($settings['transcript_logo']) && file_exists(public_path($settings['transcript_logo'])))
                                <div class="logo-preview-wrapper"><img id="preview-logo" src="{{ asset($settings['transcript_logo']) }}" alt="logo"></div>
                            @else
                                <div class="logo-preview-wrapper"><img id="preview-logo" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" alt="logo" style="display:none;"></div>
                            @endif
                            <input type="file" name="transcript_logo" id="transcript_logo" class="form-control" accept="image/*">
                        </div>

                        <div class="form-group">
                            <label>Kop Surat / Header (Editor Visual)</label>
                            @php
                                $transcript_header_val = old('transcript_header', $settings['transcript_header']);
                                if (strip_tags($transcript_header_val) === $transcript_header_val) {
                                    $transcript_header_val = nl2br($transcript_header_val);
                                }
                            @endphp
                            <textarea name="transcript_header" id="transcript_header" class="form-control rich-text" rows="5" placeholder="Ketik desain kop surat di sini...">{!! $transcript_header_val !!}</textarea>
                            <small class="text-muted">Gunakan editor di atas untuk membuat Kop Surat tanpa perlu mengetik kode HTML.</small>
                        </div>
                    </div>

                    <!-- Footer Section -->
                    <div>
                        <h5><i class="fa-solid fa-paragraph"></i> Footer / Catatan</h5>
                        <div class="form-group">
                            <label>Kata-kata Footer</label>
                            <textarea name="transcript_footer" id="transcript_footer" class="form-control" rows="2" placeholder="Catatan atau keterangan tambahan">{{ old('transcript_footer', $settings['transcript_footer']) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Kata-kata Surat (Penandatangan)</label>
                            <textarea name="transcript_signature_text" id="transcript_signature_text" class="form-control" rows="3" placeholder="Contoh: Surat ini merupakan dokumen resmi. Berlaku seumur hidup.">{{ old('transcript_signature_text', $settings['transcript_signature_text']) }}</textarea>
                            <small class="text-muted">Teks ini akan ditampilkan di sebelah kanan penandatangan di PDF</small>
                        </div>
                    </div>

                    <!-- Document Details Section -->
                    <div>
                        <h5><i class="fa-solid fa-file-contract"></i> Detail Dokumen</h5>
                        <div class="form-row-flex">
                            <div class="form-group">
                                <label>Nomor Surat</label>
                                <input type="text" name="transcript_letter_number" id="transcript_letter_number" class="form-control" placeholder="NO/YY/SMA/001" value="{{ old('transcript_letter_number', $settings['transcript_letter_number']) }}">
                            </div>
                            <div class="form-group">
                                <label>Tempat Penandatangan</label>
                                <input type="text" name="transcript_place" id="transcript_place" class="form-control" placeholder="Jakarta" value="{{ old('transcript_place', $settings['transcript_place']) }}">
                            </div>
                            <div class="form-group">
                                <label>Format Tanggal</label>
                                <input type="text" name="transcript_date_format" id="transcript_date_format" class="form-control" placeholder="d F Y" value="{{ old('transcript_date_format', $settings['transcript_date_format']) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-check"></i> Simpan
                        </button>
                        <a href="{{ route('admin.transcripts') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Kembali
                        </a>
                        <button type="button" id="btn-preview" class="btn btn-outline-primary">
                            <i class="fa-solid fa-eye"></i> Pratinjau
                        </button>
                    </div>
                </div>
            </form>

            <!-- Preview Section -->
            <div class="preview-section">
                <h5><i class="fa-solid fa-eye"></i> Pratinjau Dokumen</h5>
                <div id="transcript-preview" class="preview-box">
                    <div class="preview-header-section" style="display: flex; align-items: center; gap: 15px;">
                        <img id="preview-logo-large" src="{{ (!empty($settings['transcript_logo']) && file_exists(public_path($settings['transcript_logo']))) ? asset($settings['transcript_logo']) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' }}" alt="logo" class="preview-logo" style="width: 60px; height: 60px; {{ (!empty($settings['transcript_logo']) && file_exists(public_path($settings['transcript_logo']))) ? '' : 'display:none;' }}">
                        <div style="flex:1;">
                            <div id="default-header-content" style="text-align: left;">
                                <p style="margin:0; font-size: 8.5pt; font-weight: bold; color: #0d9488;">LEMBAGA PENDIDIKAN ISLAM "RIYADHUL JANNAH"</p>
                                <h3 style="margin: 0; font-size: 13.5pt; font-weight: bold; color: #1f2937;">SMP NURUL IHSAN</h3>
                                <p style="margin: 0; font-size: 7.5pt; color: #6b7280;">NSS: 202000012010 &bull; NPSN: 20233628</p>
                                <p style="margin: 0; font-size: 7.5pt; color: #6b7280;">Website: smpnurulihsanbanjaran.sch.id &bull; E-mail: smpnurulihsanbanjaran@gmail.com</p>
                            </div>
                            <div id="preview-header" style="display:none; text-align: left; font-size: 10pt; font-weight: bold; line-height: 1.3;"></div>
                        </div>
                    </div>
                    <hr class="preview-divider">
                    <div class="preview-meta">
                        <strong>Nomor Surat:</strong> <span id="preview-letter-number">-</span><br>
                        <strong>Tempat, Tanggal:</strong> <span id="preview-place">-</span>, <span id="preview-date">-</span>
                    </div>
                    <hr class="preview-divider">
                    <div style="margin-top: 30px; text-align: right;">
                        <div id="preview-signature-text" style="color: #666; font-size: 0.9rem; margin-bottom: 30px;"></div>
                        <div id="preview-footer" style="color: #666;"></div>
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
    const preview = document.getElementById('transcript-preview');
    const headerInput = document.getElementById('transcript_header');
    const footerInput = document.getElementById('transcript_footer');
    const signatureTextInput = document.getElementById('transcript_signature_text');
    const letterInput = document.getElementById('transcript_letter_number');
    const placeInput = document.getElementById('transcript_place');
    const dateFormatInput = document.getElementById('transcript_date_format');
    const previewHeader = document.getElementById('preview-header');
    const previewFooter = document.getElementById('preview-footer');
    const previewSignatureText = document.getElementById('preview-signature-text');
    const previewLetter = document.getElementById('preview-letter-number');
    const previewPlace = document.getElementById('preview-place');
    const previewDate = document.getElementById('preview-date');
    const logoInput = document.getElementById('transcript_logo');
    const previewLogoLarge = document.getElementById('preview-logo-large');
    const previewLogoSmall = document.getElementById('preview-logo');

    function formatDate(format) {
        const d = new Date();
        const day = String(d.getDate()).padStart(2, '0');
        const monthNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const month = monthNames[d.getMonth()];
        const year = d.getFullYear();
        return format.replace('d', day).replace('F', month).replace('Y', year);
    }

    btn.addEventListener('click', function() {
        previewHeader.innerHTML = headerInput.value || previewHeader.innerHTML || '<em>Header akan tampil di sini</em>';
        previewFooter.innerHTML = footerInput.value || previewFooter.innerHTML || '';
        previewSignatureText.innerHTML = signatureTextInput.value ? signatureTextInput.value.replace(/\n/g, '<br>') : '';
        previewLetter.textContent = letterInput.value || '-';
        previewPlace.textContent = placeInput.value || '-';
        const fmt = dateFormatInput.value || 'd F Y';
        previewDate.textContent = formatDate(fmt);
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
                if (editor.id === 'transcript_header') {
                    const content = editor.getContent().trim();
                    const defaultHeader = document.getElementById('default-header-content');
                    if (content !== '') {
                        defaultHeader.style.display = 'none';
                        previewHeader.innerHTML = content;
                        previewHeader.style.display = 'block';
                    } else {
                        defaultHeader.style.display = 'block';
                        previewHeader.style.display = 'none';
                    }
                }
            });
        }
    });
    
    // Initial load for transcript header preview
    const initialContent = tinymce.get('transcript_header') ? tinymce.get('transcript_header').getContent().trim() : document.getElementById('transcript_header').value.trim();
    const defaultHeader = document.getElementById('default-header-content');
    if (initialContent !== '') {
        defaultHeader.style.display = 'none';
        previewHeader.innerHTML = initialContent;
        previewHeader.style.display = 'block';
    } else {
        defaultHeader.style.display = 'block';
        previewHeader.style.display = 'none';
    }
});
</script>
@endsection
