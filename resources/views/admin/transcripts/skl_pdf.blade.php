<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Lulus</title>
    <style>
        @page {
            margin: 10mm 15mm;
            size: A4;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #1f2937;
        margin: 0;
        padding: 0;
        background-color: #fff;
    }
    .modern-container {
        position: relative;
        padding: 8px 18px;
        border-top: 4px solid #0d9488;
        background-color: #ffffff;
    }
    .print-kop {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 3px;
    }
    .kop-logo {
        width: 55px;
        height: 55px;
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
    .print-divider {
        border: none;
        height: 0.5px;
        background-color: #e5e7eb;
        margin: 6px 0 10px 0;
    }
    .cert-title {
        text-align: center;
        margin-bottom: 8px;
    }
    .cert-title h3 {
        font-size: 10pt;
        font-weight: 800;
        color: #111827;
        margin: 0 0 1px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-decoration: underline;
    }
    .cert-title .letter-number {
        font-size: 7.5pt;
        color: #374151;
        margin: 0;
    }
    .cert-body {
        font-size: 8.5pt;
        text-align: justify;
    }
    .cert-body p {
        margin-top: 0;
        margin-bottom: 4px;
        text-indent: 25px;
    }
    .cert-body p.no-indent {
        text-indent: 0;
    }
    .table-print-meta {
        width: 80%;
        margin: 6px auto 8px auto;
        border-collapse: collapse;
    }
    .table-print-meta td {
        padding: 2px 6px;
        vertical-align: top;
    }
    .meta-label {
        width: 160px;
        color: #374151;
        font-weight: 600;
    }
    .meta-colon {
        width: 10px;
        text-align: center;
        color: #4b5563;
    }
    .meta-value {
        color: #111827;
        font-weight: 700;
    }
    .print-status-box {
        width: 180px;
        margin: 6px auto;
        padding: 4px;
        border: 2px solid #059669;
        background-color: #ecfdf5;
        color: #065f46;
        text-align: center;
        font-size: 10pt;
        font-weight: 800;
        border-radius: 3px;
        letter-spacing: 2px;
    }
    .print-status-box.tidak-lulus {
        border: 2px solid #dc2626;
        background-color: #fef2f2;
        color: #991b1b;
    }
    .cert-note {
        font-size: 7pt;
        color: #4b5563;
        margin-top: 6px;
        font-style: italic;
        line-height: 1.25;
    }
    .cert-footer {
        margin-top: 10px;
        width: 100%;
    }
    .footer-table {
        width: 100%;
        border-collapse: collapse;
    }
    .footer-right {
        width: 50%;
        text-align: left;
        padding-left: 240px;
    }
    .footer-right p {
        margin: 0 0 1px 0;
        font-size: 8pt;
    }
    .sig-img {
        height: 40px;
        width: auto;
        margin: 2px 0;
    }
    .signature-space {
        height: 40px;
    }
    .table-skl-grades {
        width: 100%;
        border-collapse: collapse;
        margin: 6px 0;
        border: 1px solid #115e59;
        font-size: 7pt;
    }
    .table-skl-grades th {
        font-weight: 700;
        text-align: center;
        background-color: #115e59;
        color: #ffffff;
        font-size: 6.5pt;
        text-transform: uppercase;
        padding: 4px 3px;
        border: 1px solid #0f766e;
    }
    .table-skl-grades td {
        padding: 3px 4px;
        border: 1px solid #cbd5e1;
        color: #374151;
    }
    .table-skl-grades tr:nth-child(even) {
        background-color: #f8fafc;
    }
    .table-skl-grades td.center {
        text-align: center;
    }
    .table-skl-grades .category-row td {
        font-weight: 800;
        background-color: #f1f5f9;
        color: #0f172a;
        padding: 3px 4px;
        border: 1px solid #cbd5e1;
        text-transform: uppercase;
        font-size: 6.5pt;
    }
    .table-skl-grades .avg-row td {
        font-weight: 800;
        background-color: #e2e8f0;
        color: #0f172a;
        padding: 3px 4px;
        border: 1px solid #cbd5e1;
        font-size: 6.5pt;
    }
    </style>
</head>
<body>
    @foreach($students ?? [$student] as $student)
    <div class="modern-container" style="{{ $loop->last ? '' : 'page-break-after: always;' }}">
        <table class="print-kop">
            <tr>
                @if($logo_path)
                    <td width="60" align="center" valign="middle">
                        <img src="{{ $logo_path }}" class="kop-logo" alt="Logo">
                    </td>
                @endif
                <td class="kop-text" valign="middle">
                    @if(!empty($settings['skl_header']))
                        <div style="font-size: 9pt; font-weight: bold; line-height: 1.25;">
                            {!! $settings['skl_header'] !!}
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
            </tr>
        </table>
        <div class="print-divider"></div>

        <!-- Title -->
        <div class="cert-title">
            <h3>SURAT KETERANGAN LULUS</h3>
            <p class="letter-number">Nomor: {{ $letterNumber }}</p>
        </div>

        <!-- Body -->
        <div class="cert-body">
            @php
                $openingText = $settings['skl_opening_text'] ?? 'Yang bertanda tangan di bawah ini, Kepala Sekolah [NAMA_SEKOLAH] Kecamatan Banjaran Kabupaten Bandung, menerangkan bahwa:';
                $openingText = str_replace('[NAMA_SEKOLAH]', $settings['school_name'], $openingText);
            @endphp
            <p class="no-indent">{!! $openingText !!}</p>
            
            <table class="table-print-meta">
                <tr>
                    <td class="meta-label">Nama Lengkap</td>
                    <td class="meta-colon">:</td>
                    <td class="meta-value">{{ strtoupper($student->name) }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Tempat, Tanggal Lahir</td>
                    <td class="meta-colon">:</td>
                    <td class="meta-value">{{ $student->birth_place ?? '-' }}, {{ $student->birth_date_formatted ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Nomor Induk Siswa Nasional</td>
                    <td class="meta-colon">:</td>
                    <td class="meta-value">{{ $student->nisn }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Sekolah Asal</td>
                    <td class="meta-colon">:</td>
                    <td class="meta-value">{{ $settings['school_name'] }}</td>
                </tr>
            </table>

            @php
                $bodyText = $settings['skl_body_text'] ?? 'Berdasarkan Kriteria Kelulusan Peserta Didik yang diatur dalam kurikulum yang berlaku dan Rapat Pleno Dewan Guru [NAMA_SEKOLAH] tentang Kelulusan Siswa Kelas IX Tahun Pelajaran [TAHUN_PELAJARAN] pada tanggal [TANGGAL_PENGUMUMAN], dengan ini menyatakan bahwa siswa tersebut di atas:';
                $bodyText = str_replace(
                    ['[NAMA_SEKOLAH]', '[TAHUN_PELAJARAN]', '[TANGGAL_PENGUMUMAN]'],
                    [$settings['school_name'], ((date('Y')-1) . '/' . date('Y')), ($announcementDate ? $announcementDate->translatedFormat('d F Y') : date('d F Y'))],
                    $bodyText
                );
            @endphp
            <p>
                {!! $bodyText !!}
            </p>

            @if($student->status === 'LULUS')
                <div class="print-status-box">
                    L U L U S
                </div>
            @else
                <div class="print-status-box tidak-lulus">
                    TIDAK LULUS
                </div>
            @endif
            @if(!empty($settings['skl_after_lulus_text']))
                <p style="text-indent: 0; margin-top: 8px; font-size: 8pt; font-weight: 600;">{!! $settings['skl_after_lulus_text'] !!}</p>
            @endif
        </div>

        <!-- Grade Table -->
        @php
            $groupedSubjects = $subjects->groupBy('category');
            $no = 1;
            $ijazahTotal = 0;
            $ijazahCount = 0;
        @endphp
        <table class="table-skl-grades">
            <thead>
                <tr>
                    <th width="18">No</th>
                    <th>Mata Pelajaran</th>
                    <th width="60">Nilai Ijazah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedSubjects as $category => $categorySubjects)
                    <tr class="category-row">
                        <td colspan="3"><strong>{{ $category }}</strong></td>
                    </tr>
                    @foreach($categorySubjects as $subject)
                        @php
                            $finalGrade = $student->calculateFinalGradeForSubject($subject->id);
                            if ($finalGrade !== null) {
                                $ijazahTotal += $finalGrade;
                                $ijazahCount++;
                            }
                        @endphp
                        <tr>
                            <td class="center">{{ $no++ }}</td>
                            <td>{{ $subject->name }}</td>
                            <td class="center">{{ $finalGrade !== null ? number_format($finalGrade, 2) : '-' }}</td>
                        </tr>
                    @endforeach
                @endforeach
                <!-- Average Row -->
                <tr class="avg-row">
                    <td colspan="2">RATA-RATA</td>
                    <td class="center">{{ $ijazahCount > 0 ? number_format($ijazahTotal / $ijazahCount, 2) : '-' }}</td>
                </tr>
            </tbody>
        </table>

        <div class="cert-body">
            @if(!empty($settings['skl_before_ttd_text']))
                <p class="no-indent" style="font-weight: 600; margin-bottom: 6px;">{!! $settings['skl_before_ttd_text'] !!}</p>
            @endif
            @php
                $footerText = $settings['skl_footer_text'] ?? '* Surat Keterangan Lulus ini berlaku sementara sampai diterbitkannya Ijazah asli bagi peserta didik yang dinyatakan lulus, guna melengkapi syarat pendaftaran jenjang pendidikan selanjutnya.';
            @endphp
            <p class="no-indent cert-note">
                {!! $footerText !!}
            </p>
        </div>

        <!-- Footer Signature -->
        <div class="cert-footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-right">
                        <p>{{ $settings['skl_place'] ?? 'Banjaran' }}, {{ $announcementDate ? $announcementDate->translatedFormat($settings['skl_date_format'] ?? 'd F Y') : date($settings['skl_date_format'] ?? 'd F Y') }}</p>
                        <p>{{ $settings['skl_signature_text'] ?? 'Kepala Sekolah,' }}</p>
                        
                        @if($signature_path)
                            <img src="{{ $signature_path }}" class="sig-img" alt="Tanda Tangan">
                        @else
                            <div class="signature-space"></div>
                        @endif
                        
                        <p style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;">{{ $settings['principal_name'] }}</p>
                        @if(!empty($settings['principal_nip']))
                            <p style="color: #4b5563; font-size: 8.5pt;">NIP. {{ $settings['principal_nip'] }}</p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @endforeach
</body>
</html>
