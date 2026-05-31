<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transkip Nilai - Modern Sleek Perfect Fill</title>
    <style>
        @page {
            margin: 6mm 10mm;
            size: A4;
        }
        body, table, tr, td, th {
            font-family: 'Times New Roman', 'Times', serif;
            font-size: 9pt;
            line-height: 1.25;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .page-break {
            box-sizing: border-box;
            padding: 0;
        }
        .modern-container {
            position: relative;
            padding: 4px 8px;
            border-top: 4px solid #0d9488;
            background-color: #ffffff;
        }
        .print-kop {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }
        .kop-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }
        .kop-text {
            text-align: center;
            padding-left: 8px;
            padding-right: 8px;
        }
        .kop-text .kop-yayasan {
            font-size: 7.5pt;
            font-weight: 700;
            color: #0d9488;
            margin: 0 0 1px 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .kop-text h1 {
            font-size: 10.5pt;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .kop-text .kop-info {
            font-size: 7pt;
            margin: 1px 0;
            color: #4b5563;
        }
        .kop-text .kop-detail {
            font-size: 6.5pt;
            margin: 0;
            color: #6b7280;
        }
        .kop-text .kop-detail a {
            color: #0d9488;
            text-decoration: none;
            font-weight: 600;
        }
        .kop-qr {
            width: 50px;
            text-align: right;
        }
        .kop-qr img {
            width: 40px;
            height: 40px;
        }
        .print-divider {
            border: none;
            height: 2.5px;
            background-color: #111827;
            margin: 4px 0 6px 0;
        }
        .footer-divider {
            border: none;
            height: 0.5px;
            background-color: #e5e7eb;
            margin: 8px 0 6px 0;
        }
        .cert-title {
            text-align: center;
            margin-bottom: 5px;
        }
        .cert-title h3 {
            font-size: 10pt;
            font-weight: bold;
            color: #000000;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cert-title .subtitle {
            font-size: 9.5pt;
            color: #000000;
            margin-top: 1px;
            font-weight: bold;
        }
        .table-print-meta {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0 5px 0;
            background-color: transparent;
            border: none;
        }
        .table-print-meta td {
            padding: 2px 0;
            vertical-align: middle;
        }
        .meta-label {
            width: 110px;
            color: #4b5563;
            font-weight: 600;
        }
        .meta-colon {
            width: 8px;
            text-align: center;
            color: #9ca3af;
        }
        .meta-value {
            color: #1f2937;
            font-weight: 700;
        }
        .table-print-grades {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            border: 1px solid #115e59;
        }
        .table-print-grades th {
            font-weight: 700;
            text-align: center;
            background-color: #115e59;
            color: #ffffff;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8.5px 6px;
            border: 1px solid #0f766e;
        }
        .table-print-grades td {
            padding: 8.5px 6px;
            border: 1px solid #cbd5e1;
            color: #374151;
            font-size: 8pt;
        }
        .table-print-grades tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .table-print-grades td.center {
            text-align: center;
            border: 1px solid #cbd5e1;
        }
        .table-print-grades .category-row td {
            font-weight: 800;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 7px 6px;
            border: 1px solid #cbd5e1;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.3px;
        }
        .cert-footer {
            margin-top: 6px;
            width: 100%;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-left {
            width: 65%;
            vertical-align: middle;
            padding: 0;
        }
        .footer-right {
            width: 35%;
            text-align: center;
            vertical-align: middle;
            padding: 0;
        }
        .footer-right p {
            margin: 0 0 1px 0;
            font-size: 8.5pt;
            color: #000000;
        }
        .sig-img {
            height: 45px;
            width: auto;
            margin: 2px 0;
        }
        .signature-space {
            height: 45px;
        }
        .photo-box {
            width: 75px;
            height: 100px;
            border-radius: 4px;
            border: 1.5px solid #e5e7eb;
            overflow: hidden;
            display: inline-block;
            background-color: #f9fafb;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .photo-placeholder {
            width: 100%;
            height: 100%;
            display: table;
            background: #f3f4f6;
        }
        .photo-placeholder-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            font-size: 6.5pt;
            color: #9ca3af;
            font-weight: 600;
        }
    </style>
</head>
<body>
    @foreach($students as $student)
        @php
            $headerType = \App\Models\Setting::get('transcript_header_type', 'text');
            $headerImage = \App\Models\Setting::get('transcript_header_image');
            $hasImageHeader = ($headerType === 'image' && !empty($headerImage) && file_exists(public_path($headerImage)));
        @endphp
        <div class="page-break" style="{{ $loop->last ? 'page-break-after: avoid;' : 'page-break-after: always;' }}">
            <div class="modern-container" style="{{ $hasImageHeader ? 'border-top: none;' : '' }}">
                <!-- Kop Surat -->

                @if($hasImageHeader)
                    <div style="width: 100%; text-align: center; margin-bottom: 8px;">
                        <img src="{{ public_path($headerImage) }}" style="width: 100%; height: auto; display: block; max-height: 120px; object-fit: contain;">
                    </div>
                @else
                    <table class="print-kop">
                        <tr>
                            @if($logo_path)
                                <td width="80" align="center" valign="middle">
                                    <img src="{{ $logo_path }}" class="kop-logo" alt="Logo">
                                </td>
                            @endif
                            <td class="kop-text" valign="middle">
                                @if(!empty($settings['transcript_header']))
                                    <div style="font-size: 9pt; font-weight: bold; line-height: 1.25;">
                                        {!! $settings['transcript_header'] !!}
                                    </div>
                                @else
                                    <p class="kop-yayasan">LEMBAGA PENDIDIKAN ISLAM &ldquo;RIYADHUL JANNAH&rdquo;</p>
                                    <h1>{{ strtoupper($settings['school_name']) }}</h1>
                                    <p class="kop-info">
                                        NSS : <strong>{{ $settings['nss'] ?? '202000012010' }}</strong> 
                                        &bull; 
                                        NPSN : <strong>{{ $settings['npsn'] ?? '20233628' }}</strong> 
                                        &bull; 
                                        Akreditasi : <strong>&ldquo;{{ $settings['accreditation'] ?? 'B' }}&rdquo;</strong>
                                    </p>
                                    <p class="kop-detail">
                                        Website: <a href="http://smpnurulihsanbanjaran.sch.id/">smpnurulihsanbanjaran.sch.id</a>
                                        &bull; 
                                        E-mail: <a href="mailto:smpnurulihsanbanjaran@gmail.com">smpnurulihsanbanjaran@gmail.com</a>
                                    </p>
                                    <p class="kop-detail" style="color: #4b5563; font-weight: 500;">{{ $settings['school_address'] }}</p>
                                @endif
                            </td>
                            @if(isset($qr_codes[$student->id]))
                                <td class="kop-qr" valign="middle">
                                    <img src="data:image/png;base64,{{ $qr_codes[$student->id] }}" alt="QR Code">
                                </td>
                            @endif
                    </table>
                    <div class="print-divider"></div>
                @endif

                <!-- Title -->
                <div class="cert-title">
                    <h3>TRANSKRIP NILAI KELULUSAN</h3>
                    <div class="subtitle">Tahun Pelajaran: {{ (date('Y')-1) }}/{{ date('Y') }}</div>
                </div>

                <!-- Student Metadata Card -->
                <table class="table-print-meta">
                    <tr>
                        <td class="meta-label">Nama Lengkap</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ strtoupper($student->name) }}</td>
                        
                        <td class="meta-label" style="padding-left: 40px;">NIS</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $student->nis ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tempat, Tgl Lahir</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date_formatted ?? '-' }}</td>
                        
                        <td class="meta-label" style="padding-left: 40px;">NISN</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $student->nisn }}</td>
                    </tr>
                </table>

                <!-- Grade Matrix Table with Modern Minimalist Style -->
                <table class="table-print-grades">
                    <thead>
                        <tr>
                            <th rowspan="2" width="24" style="vertical-align: middle;">No</th>
                            <th rowspan="2" align="left" style="vertical-align: middle;">Mata Pelajaran</th>
                            <th colspan="6" style="padding: 5px 8px; font-size: 7.5pt; letter-spacing: 0.5px;">Semester</th>
                        </tr>
                        <tr>
                            <th width="32" style="font-size: 7.5pt; padding: 4px 8px;">I</th>
                            <th width="32" style="font-size: 7.5pt; padding: 4px 8px;">II</th>
                            <th width="32" style="font-size: 7.5pt; padding: 4px 8px;">III</th>
                            <th width="32" style="font-size: 7.5pt; padding: 4px 8px;">IV</th>
                            <th width="32" style="font-size: 7.5pt; padding: 4px 8px;">V</th>
                            <th width="32" style="font-size: 7.5pt; padding: 4px 8px;">VI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $groupedSubjects = $subjects->groupBy('category');
                            $semesterNames = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6'];
                        @endphp

                        @foreach($groupedSubjects as $category => $categorySubjects)
                            <tr class="category-row">
                                <td colspan="8"><strong>{{ $category }}</strong></td>
                            </tr>
                            @foreach($categorySubjects as $subject)
                                @php
                                    $subGrades = $student->grades->where('subject_id', $subject->id);
                                @endphp
                                <tr>
                                    <td class="center" style="color: #6b7280;">{{ $no++ }}</td>
                                    <td style="font-weight: 600; color: #1f2937;">{{ $subject->name }}</td>
                                    @foreach($semesterNames as $semName)
                                        @php
                                            $semGrade = $subGrades->where('semester', $semName)->first();
                                        @endphp
                                        <td class="center">
                                            {{ $semGrade ? number_format($semGrade->score, 2) : '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>

                <div class="footer-divider"></div>

                <!-- Signature Block -->
                <div class="cert-footer">
                    <table class="footer-table">
                        <tr>
                            <td class="footer-left" valign="bottom">
                                @if(!empty($settings['transcript_footer']))
                                    <p style="font-size: 8pt; color: #6b7280; margin-bottom: 10px; font-style: italic; line-height: 1.35; max-width: 180px;">{{ $settings['transcript_footer'] }}</p>
                                @endif
                                <!-- Student Photo -->
                                <div class="photo-box">
                                    @if($student->photo && file_exists(public_path($student->photo)))
                                        <img src="{{ public_path($student->photo) }}" alt="Foto {{ $student->name }}">
                                    @else
                                        <div class="photo-placeholder">
                                            <span class="photo-placeholder-text">Pas Foto<br>3x4</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="footer-right">
                                @if(!empty($settings['transcript_signature_text']))
                                    <p style="font-size: 8.5pt; color: #000000; white-space: pre-wrap; margin-bottom: 10px; font-style: italic; line-height: 1.35;">{{ $settings['transcript_signature_text'] }}</p>
                                @endif
                                <p style="color: #000000; font-weight: normal;">{{ $settings['transcript_place'] ?? 'Subang' }}, {{ $announcementDate ? $announcementDate->locale('id')->translatedFormat($settings['transcript_date_format'] ?? 'd F Y') : \Carbon\Carbon::now()->locale('id')->translatedFormat($settings['transcript_date_format'] ?? 'd F Y') }}</p>
                                <p style="font-weight: normal; color: #000000;">Kepala {{ $settings['school_name'] }}</p>
                                
                                @if($signature_path)
                                    <table style="width: 140px; border-collapse: collapse; margin: 4px auto; background: transparent; border: none;">
                                        <tr>
                                            <td style="vertical-align: middle; padding: 0; border: none; background: transparent; text-align: left; width: 60%;">
                                                <img src="{{ $signature_path }}" class="sig-img" alt="Tanda Tangan" style="height: 50px; width: auto; margin: 0;">
                                            </td>
                                            <td style="vertical-align: middle; padding: 0; border: none; background: transparent; text-align: left; width: 40%;">
                                                @php
                                                    $qrText = "VERIFIKASI TANDATANGAN DIGITAL\n"
                                                            . "Nama: " . $settings['principal_name'] . "\n"
                                                            . "Jabatan: Kepala Sekolah\n"
                                                            . "Sekolah: " . $settings['school_name'] . "\n"
                                                            . "NIP: " . ($settings['principal_nip'] ?? '-');
                                                    $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(40)->generate($qrText));
                                                @endphp
                                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 40px; height: 40px; display: inline-block; vertical-align: middle;" alt="QR Code">
                                            </td>
                                        </tr>
                                    </table>
                                @else
                                    <div style="margin: 6px auto;">
                                        @php
                                            $qrText = "VERIFIKASI TANDATANGAN DIGITAL\n"
                                                    . "Nama: " . $settings['principal_name'] . "\n"
                                                    . "Jabatan: Kepala Sekolah\n"
                                                    . "Sekolah: " . $settings['school_name'] . "\n"
                                                    . "NIP: " . ($settings['principal_nip'] ?? '-');
                                            $qrCode = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(55)->generate($qrText));
                                        @endphp
                                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 55px; height: 55px; display: inline-block;" alt="QR Code">
                                    </div>
                                @endif
                                
                                <p style="font-size: 9pt; font-weight: bold; color: #000000; margin-bottom: 1px;"><u>{{ $settings['principal_name'] }}</u></p>
                                @if(!empty($settings['principal_nip']))
                                    <p style="font-size: 8.5pt; color: #000000;">NIP. {{ $settings['principal_nip'] }}</p>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>
