<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transkip Nilai - Modern Sleek Perfect Fill</title>
    <style>
        @page {
            margin: 10mm 12mm;
            size: A4;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.8pt;
            line-height: 1.45;
            color: #1f2937;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .page-break {
            box-sizing: border-box;
            padding: 0;
            height: 100%;
        }

        /* === PERFECT VERTICAL FILL CONTAINER === */
        .modern-container {
            position: relative;
            padding: 20px 25px;
            height: 97%;
            border-top: 6px solid #0d9488; /* Teal branding top bar */
            background-color: #ffffff;
        }

        /* === KOP SURAT === */
        .print-kop {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .kop-logo {
            width: 65px;
            height: 65px;
            object-fit: contain;
        }
        .kop-text {
            text-align: left;
            padding-left: 15px;
        }
        .kop-text .kop-yayasan {
            font-size: 8.5pt;
            font-weight: 700;
            color: #0d9488;
            margin: 0 0 2px 0;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }
        .kop-text h1 {
            font-size: 13.5pt;
            font-weight: 800;
            color: #1f2937;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text .kop-info {
            font-size: 8pt;
            margin: 2px 0;
            color: #4b5563;
        }
        .kop-text .kop-detail {
            font-size: 7.5pt;
            margin: 1px 0;
            color: #6b7280;
        }
        .kop-text .kop-detail a {
            color: #0d9488;
            text-decoration: none;
            font-weight: 600;
        }
        .kop-qr {
            width: 65px;
            text-align: right;
        }
        .kop-qr img {
            width: 55px;
            height: 55px;
        }
        .print-divider {
            border: none;
            height: 1px;
            background-color: #e5e7eb;
            margin: 6px 0 12px 0;
        }

        /* === TITLE === */
        .cert-title {
            text-align: center;
            margin-bottom: 15px;
        }
        .cert-title h3 {
            font-size: 12pt;
            font-weight: 800;
            color: #1f2937;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cert-title .subtitle {
            font-size: 8.5pt;
            color: #6b7280;
            margin-top: 3px;
            font-weight: 500;
        }

        /* === STUDENT META CARD === */
        .table-print-meta {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin: 10px 0 15px 0;
            background-color: #f9fafb;
            border-radius: 8px;
            border: 1px solid #f3f4f6;
        }
        .table-print-meta td {
            padding: 7px 12px;
            vertical-align: middle;
        }
        .meta-label {
            width: 130px;
            color: #4b5563;
            font-weight: 600;
        }
        .meta-colon {
            width: 10px;
            text-align: center;
            color: #9ca3af;
        }
        .meta-value {
            color: #1f2937;
            font-weight: 700;
        }

        /* === COMFORTABLE GRADES TABLE === */
        .table-print-grades {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 8.5pt;
        }
        .table-print-grades th {
            font-weight: 700;
            text-align: center;
            background-color: #0d9488;
            color: #ffffff;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6.5px 8px;
            border: none;
        }
        .table-print-grades td {
            padding: 5.5px 8px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        .table-print-grades td.center {
            text-align: center;
        }
        .table-print-grades .category-row td {
            font-weight: 800;
            background-color: #f3f4f6;
            color: #1f2937;
            padding: 5.5px 8px;
            border-bottom: 1px solid #e5e7eb;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.5px;
        }
        .row-print-average {
            background-color: #f0fdfa;
        }
        .row-print-average td {
            color: #0d9488;
            font-size: 9.5pt;
            font-weight: 800;
            border-top: 1.5px solid #0d9488;
            border-bottom: 1.5px solid #0d9488;
            padding: 8px 10px;
        }

        /* === PERFECT FIT FOOTER / SIGNATURE === */
        .cert-footer {
            margin-top: 25px;
            width: 100%;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-left {
            width: 40%;
            vertical-align: bottom;
            padding: 0;
        }
        .footer-right {
            width: 60%;
            text-align: left;
            vertical-align: top;
            padding-left: 50px;
        }
        .footer-right p {
            margin: 0 0 2px 0;
            font-size: 9pt;
        }
        .sig-img {
            height: 50px;
            width: auto;
            margin: 4px 0;
        }
        .signature-space {
            height: 50px;
        }
        .photo-box {
            width: 75px;
            height: 100px;
            border-radius: 6px;
            border: 2px solid #e5e7eb;
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
            font-size: 7pt;
            color: #9ca3af;
            font-weight: 600;
        }
    </style>
</head>
<body>
    @foreach($students as $student)
        <div class="page-break" style="{{ $loop->last ? 'page-break-after: avoid;' : 'page-break-after: always;' }}">
            <div class="modern-container">
                <!-- Kop Surat -->
                <table class="print-kop">
                    <tr>
                        @if($logo_path)
                            <td width="65" align="center" valign="middle">
                                <img src="{{ $logo_path }}" class="kop-logo" alt="Logo">
                            </td>
                        @endif
                        <td class="kop-text" valign="middle">
                            @if(!empty($settings['transcript_header']))
                                <div style="font-size: 10pt; font-weight: bold; line-height: 1.3;">
                                    {!! $settings['transcript_header'] !!}
                                </div>
                            @else
                                <p class="kop-yayasan">LEMBAGA PENDIDIKAN ISLAM &ldquo;RIYADHUL JANNAH&rdquo;</p>
                                <h1>{{ $settings['school_name'] }}</h1>
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
                    </tr>
                </table>
                <div class="print-divider"></div>

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
                        
                        <td class="meta-label" style="padding-left: 20px;">NIS / NISN</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $student->nis ?? '-' }} / {{ $student->nisn }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tempat, Tgl Lahir</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date_formatted ?? '-' }}</td>
                        
                        <td class="meta-label" style="padding-left: 20px;">&nbsp;</td>
                        <td class="meta-colon">&nbsp;</td>
                        <td class="meta-value">&nbsp;</td>
                    </tr>
                </table>

                <!-- Grade Matrix Table with Modern Minimalist Style -->
                <table class="table-print-grades">
                    <thead>
                        <tr>
                            <th width="30" style="border-top-left-radius: 6px; border-bottom-left-radius: 6px;">No</th>
                            <th align="left">Mata Pelajaran</th>
                            <th width="45">I</th>
                            <th width="45">II</th>
                            <th width="45">III</th>
                            <th width="45">IV</th>
                            <th width="45">V</th>
                            <th width="45">VI</th>
                            <th width="55" style="border-top-right-radius: 6px; border-bottom-right-radius: 6px;">Ijazah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                            $groupedSubjects = $subjects->groupBy('category');
                            $semesterNames = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6', 'Nilai Ijazah'];
                        @endphp

                        @foreach($groupedSubjects as $category => $categorySubjects)
                            <tr class="category-row">
                                <td colspan="9"><strong>{{ $category }}</strong></td>
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
                                        <td class="center" style="{{ $semName == 'Nilai Ijazah' ? 'font-weight: 700; color: #0d9488;' : '' }}">
                                            {{ $semGrade ? number_format($semGrade->score, 2) : '-' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                        
                        <!-- Rata-Rata Row -->
                        <tr class="row-print-average">
                            <td colspan="8" align="right" style="padding: 8px 12px; border-bottom-left-radius: 6px;"><strong>RATA-RATA NILAI AKHIR:</strong></td>
                            <td class="center" style="border-bottom-right-radius: 6px;">{{ number_format($student->average_score, 2) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Signature Block -->
                <div class="cert-footer">
                    <table class="footer-table">
                        <tr>
                            <td class="footer-left" valign="bottom">
                                @if(!empty($settings['transcript_footer']))
                                    <p style="font-size: 8pt; color: #6b7280; margin-bottom: 12px; font-style: italic; line-height: 1.4; max-width: 180px;">{{ $settings['transcript_footer'] }}</p>
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
                                    <p style="font-size: 8pt; color: #6b7280; white-space: pre-wrap; margin-bottom: 12px; font-style: italic; line-height: 1.45;">{{ $settings['transcript_signature_text'] }}</p>
                                @endif
                                <p style="color: #4b5563; font-weight: 500;">{{ $settings['transcript_place'] ?? 'Subang' }}, {{ $announcementDate ? $announcementDate->translatedFormat($settings['transcript_date_format'] ?? 'd F Y') : date($settings['transcript_date_format'] ?? 'd F Y') }}</p>
                                <p style="font-weight: 700; color: #0d9488;">Kepala {{ $settings['school_name'] }}</p>
                                
                                @if($signature_path)
                                    <img src="{{ $signature_path }}" class="sig-img" alt="Tanda Tangan">
                                @else
                                    <div class="signature-space"></div>
                                @endif
                                
                                <p style="font-size: 9.5pt; font-weight: bold; color: #1f2937; margin-bottom: 1px;"><u>{{ $settings['principal_name'] }}</u></p>
                                @if(!empty($settings['principal_nip']))
                                    <p style="font-size: 8pt; color: #6b7280;">NIP. {{ $settings['principal_nip'] }}</p>
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
