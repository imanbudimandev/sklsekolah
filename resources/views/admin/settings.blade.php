@extends('layouts.admin')

@section('title', 'Pengaturan Aplikasi')
@section('page_title', 'Pengaturan Aplikasi')

@section('content')
<div class="settings-container fade-in">
    <div class="card settings-card">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="settings-sections">
                <!-- Section 1: Profil Sekolah -->
                <div class="settings-section">
                    <h3 class="section-title"><i class="fa-solid fa-school"></i> Profil Sekolah</h3>
                    <div class="form-group">
                        <label for="school_name">Nama Sekolah <span class="required">*</span></label>
                        <input type="text" id="school_name" name="school_name" value="{{ old('school_name', $settings['school_name']) }}" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="school_address">Alamat Sekolah</label>
                        <textarea id="school_address" name="school_address" rows="3" placeholder="Masukkan alamat lengkap sekolah beserta kode pos...">{{ old('school_address', $settings['school_address']) }}</textarea>
                    </div>
                </div>

                <!-- Section 2: Kepala Sekolah -->
                <div class="settings-section">
                    <h3 class="section-title"><i class="fa-solid fa-user-tie"></i> Kepala Sekolah</h3>
                    <div class="form-group">
                        <label for="principal_name">Nama Kepala Sekolah <span class="required">*</span></label>
                        <input type="text" id="principal_name" name="principal_name" value="{{ old('principal_name', $settings['principal_name']) }}" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="principal_nip">NIP Kepala Sekolah</label>
                        <input type="text" id="principal_nip" name="principal_nip" value="{{ old('principal_nip', $settings['principal_nip']) }}" placeholder="Contoh: 197608242005011002" autocomplete="off">
                    </div>
                </div>

                <!-- Section 3: Waktu Pengumuman -->
                <div class="settings-section">
                    <h3 class="section-title"><i class="fa-solid fa-clock"></i> Waktu Pengumuman</h3>
                    <div class="form-group">
                        <label for="announcement_date">Tanggal & Jam Kelulusan <span class="required">*</span></label>
                        @php
                            $datetimeVal = '';
                            if(!empty($settings['announcement_date'])) {
                                $datetimeVal = date('Y-m-d\TH:i', strtotime($settings['announcement_date']));
                            }
                        @endphp
                        <input type="datetime-local" id="announcement_date" name="announcement_date" value="{{ old('announcement_date', $datetimeVal) }}" required>
                        <p class="form-help">Waktu lokal (WIB) kapan tombol cari kelulusan dapat digunakan oleh siswa.</p>
                    </div>
                </div>

                <!-- Section 4: Logo & Tanda Tangan -->
                <div class="settings-section">
                    <h3 class="section-title"><i class="fa-solid fa-images"></i> Logo, Favicon & Stempel</h3>
                    
                    <div class="upload-grid">
                        <!-- School Logo Upload -->
                        <div class="upload-box">
                            <label>Logo Resmi Sekolah (Laporan/PDF)</label>
                            <div class="image-preview-container">
                                @if(!empty($settings['school_logo']) && file_exists(public_path($settings['school_logo'])))
                                    <img id="logo_preview" src="{{ asset($settings['school_logo']) }}" alt="Logo Sekolah" class="setting-img-preview">
                                @else
                                    <div id="logo_placeholder" class="setting-img-placeholder">
                                        <i class="fa-solid fa-graduation-cap"></i>
                                    </div>
                                    <img id="logo_preview" src="#" alt="Logo Preview" class="setting-img-preview" style="display:none;">
                                @endif
                            </div>
                            <label for="school_logo" class="btn btn-secondary btn-sm mt-2 btn-block file-input-label">
                                <i class="fa-solid fa-upload"></i> Unggah Logo
                                <input type="file" id="school_logo" name="school_logo" accept="image/*" class="file-input-hidden" onchange="previewImage(this, 'logo_preview', 'logo_placeholder')">
                            </label>
                            <p class="form-help text-center">Format: PNG/JPG (Max 2MB)</p>
                        </div>

                        <!-- Dashboard Logo Upload -->
                        <div class="upload-box">
                            <label>Logo Dashboard Admin</label>
                            <div class="image-preview-container">
                                @if(!empty($settings['dashboard_logo']) && file_exists(public_path($settings['dashboard_logo'])))
                                    <img id="dashboard_logo_preview" src="{{ asset($settings['dashboard_logo']) }}" alt="Logo Dashboard" class="setting-img-preview">
                                @else
                                    <div id="dashboard_logo_placeholder" class="setting-img-placeholder">
                                        <i class="fa-solid fa-gauge-high"></i>
                                    </div>
                                    <img id="dashboard_logo_preview" src="#" alt="Logo Dashboard Preview" class="setting-img-preview" style="display:none;">
                                @endif
                            </div>
                            <label for="dashboard_logo" class="btn btn-secondary btn-sm mt-2 btn-block file-input-label">
                                <i class="fa-solid fa-upload"></i> Unggah Logo Dashboard
                                <input type="file" id="dashboard_logo" name="dashboard_logo" accept="image/*" class="file-input-hidden" onchange="previewImage(this, 'dashboard_logo_preview', 'dashboard_logo_placeholder')">
                            </label>
                            <p class="form-help text-center">Format: PNG/JPG (Max 2MB)</p>
                        </div>

                        <!-- Favicon Upload -->
                        <div class="upload-box">
                            <label>Favicon Website (Ikon Browser)</label>
                            <div class="image-preview-container">
                                @if(!empty($settings['favicon']) && file_exists(public_path($settings['favicon'])))
                                    <img id="favicon_preview" src="{{ asset($settings['favicon']) }}" alt="Favicon" class="setting-img-preview">
                                @else
                                    <div id="favicon_placeholder" class="setting-img-placeholder">
                                        <i class="fa-solid fa-window-restore"></i>
                                    </div>
                                    <img id="favicon_preview" src="#" alt="Favicon Preview" class="setting-img-preview" style="display:none;">
                                @endif
                            </div>
                            <label for="favicon" class="btn btn-secondary btn-sm mt-2 btn-block file-input-label">
                                <i class="fa-solid fa-upload"></i> Unggah Favicon
                                <input type="file" id="favicon" name="favicon" accept="image/*" class="file-input-hidden" onchange="previewImage(this, 'favicon_preview', 'favicon_placeholder')">
                            </label>
                            <p class="form-help text-center">Format: ICO/PNG/JPG (Max 1MB)</p>
                        </div>

                        <!-- Signature Upload -->
                        <div class="upload-box">
                            <label>Tanda Tangan & Stempel Digital</label>
                            <div class="image-preview-container">
                                @if(!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature'])))
                                    <img id="sig_preview" src="{{ asset($settings['principal_signature']) }}" alt="Tanda Tangan Kepala Sekolah" class="setting-img-preview">
                                @else
                                    <div id="sig_placeholder" class="setting-img-placeholder">
                                        <i class="fa-solid fa-signature"></i>
                                    </div>
                                    <img id="sig_preview" src="#" alt="Signature Preview" class="setting-img-preview" style="display:none;">
                                @endif
                            </div>
                            <label for="principal_signature" class="btn btn-secondary btn-sm mt-2 btn-block file-input-label">
                                <i class="fa-solid fa-upload"></i> Unggah Tanda Tangan
                                <input type="file" id="principal_signature" name="principal_signature" accept="image/*" class="file-input-hidden" onchange="previewImage(this, 'sig_preview', 'sig_placeholder')">
                            </label>
                            <p class="form-help text-center">Format: PNG transparan (Max 2MB)</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="divider">

            <div class="settings-footer">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Semua Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewImage(input, previewId, placeholderId) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);
                
                previewImg.src = e.target.result;
                previewImg.style.display = "block";
                if (placeholder) {
                    placeholder.style.display = "none";
                }
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
