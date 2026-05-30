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
            padding: 5px 10px;
            background-color: #ffffff;
        }
        .print-kop {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }
        .kop-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }
        .kop-text {
            text-align: center;
            padding-left: 8px;
            padding-right: 8px;
        }
        .kop-text h2 {
            font-size: 9pt;
            font-weight: 700;
            color: #111827;
            margin: 0 0 1px 0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .kop-text h1 {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text .kop-address {
            font-size: 7.5pt;
            margin: 1px 0;
            color: #4b5563;
        }
        .kop-text .kop-detail {
            font-size: 7pt;
            margin: 0;
            color: #6b7280;
        }
        .print-divider {
            border: none;
            height: 1px;
            background-color: #111827;
            margin: 3px 0 8px 0;
        }
        .cert-title {
            text-align: center;
            margin-bottom: 12px;
        }
        .cert-title h3 {
            font-size: 11pt;
            font-weight: 800;
            color: #111827;
            margin: 0 0 1px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: underline;
        }
        .cert-title .letter-number {
            font-size: 8pt;
            color: #374151;
            margin: 0;
        }
        .cert-body {
            font-size: 9pt;
            text-align: justify;
        }
        .cert-body p {
            margin-top: 0;
            margin-bottom: 6px;
            text-indent: 25px;
        }
        .cert-body p.no-indent {
            text-indent: 0;
        }
        .table-print-meta {
            width: 80%;
            margin: 8px auto 10px auto;
            border-collapse: collapse;
        }
        .table-print-meta td {
            padding: 3px 8px;
            vertical-align: top;
        }
        .meta-label {
            width: 170px;
            color: #374151;
            font-weight: 600;
        }
        .meta-colon {
            width: 12px;
            text-align: center;
            color: #4b5563;
        }
        .meta-value {
            color: #111827;
            font-weight: 700;
        }
        .print-status-box {
            width: 200px;
            margin: 10px auto;
            padding: 6px;
            border: 2px solid #059669;
            background-color: #ecfdf5;
            color: #065f46;
            text-align: center;
            font-size: 11pt;
            font-weight: 800;
            border-radius: 4px;
            letter-spacing: 2px;
        }
        .print-status-box.tidak-lulus {
            border: 2px solid #dc2626;
            background-color: #fef2f2;
            color: #991b1b;
        }
        .cert-note {
            font-size: 7.5pt;
            color: #4b5563;
            margin-top: 8px;
            font-style: italic;
            line-height: 1.3;
        }
        .cert-footer {
            margin-top: 15px;
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
            font-size: 8.5pt;
        }
        .sig-img {
            height: 45px;
            width: auto;
            margin: 2px 0;
        }
        .signature-space {
            height: 45px;
        }
    </style>
</head>
<body>
    @foreach($students ?? [$student] as $student)
    <div class="modern-container" style="{{ $loop->last ? '' : 'page-break-after: always;' }}">
        <table class="print-kop">
            <tr>
                @if($logo_path)
                    <td width="75" align="center" valign="middle">
                        <img src="{{ $logo_path }}" class="kop-logo" alt="Logo">
                    </td>
                @endif
                <td class="kop-text" valign="middle">
                    @if(!empty($settings['skl_header']))
                        <div style="font-size: 11pt; font-weight: bold; line-height: 1.3;">
                            {!! $settings['skl_header'] !!}
                        </div>
                    @else
                        <h2>YAYASAN NURUL IHSAN BANJARAN</h2>
                        <h1>{{ strtoupper($settings['school_name']) }}</h1>
                        <p class="kop-address">{{ $settings['school_address'] ?: 'Jl. Raya Banjaran No. 123, Banjaran, Bandung, Jawa Barat' }}</p>
                        <p class="kop-detail">
                            Website: <a href="http://smpnurulihsanbanjaran.sch.id/">smpnurulihsanbanjaran.sch.id</a>
                            &bull; 
                            E-mail: <a href="mailto:smpnurulihsanbanjaran@gmail.com">smpnurulihsanbanjaran@gmail.com</a>
                        </p>
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
            <p class="no-indent">{{ $openingText }}</p>
            
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
                    <td class="meta-label">Nomor Peserta Ujian</td>
                    <td class="meta-colon">:</td>
                    <td class="meta-value">{{ $student->exam_number ?? '-' }}</td>
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
                {{ $bodyText }}
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

            @php
                $footerText = $settings['skl_footer_text'] ?? '* Surat Keterangan Lulus ini berlaku sementara sampai diterbitkannya Ijazah asli bagi peserta didik yang dinyatakan lulus, guna melengkapi syarat pendaftaran jenjang pendidikan selanjutnya.';
            @endphp
            <p class="no-indent cert-note">
                {{ $footerText }}
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
