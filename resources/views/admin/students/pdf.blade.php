<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Siswa</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
        }
        .header-logo {
            width: 80px;
            text-align: center;
        }
        .header-logo img {
            max-height: 70px;
            max-width: 80px;
        }
        .header-text {
            text-align: center;
            padding-right: 80px; /* Offset for logo center alignment */
        }
        .school-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 5px 0;
        }
        .school-address {
            font-size: 10px;
            color: #555;
            margin: 0;
        }
        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 10px;
            font-size: 10px;
        }
        .meta-left {
            text-align: left;
        }
        .meta-right {
            text-align: right;
            color: #666;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #4f46e5;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            border: 1px solid #ddd;
            padding: 8px 6px;
            text-align: left;
        }
        .data-table td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 9px;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
        }
        .badge-success {
            background-color: #def7ec;
            color: #03543f;
        }
        .badge-danger {
            background-color: #fde8e8;
            color: #9b1c1c;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            @if($logo_path)
                <td class="header-logo">
                    <img src="{{ $logo_path }}" alt="Logo">
                </td>
            @endif
            <td class="header-text" style="{{ !$logo_path ? 'padding-right: 0;' : '' }}">
                <h1 class="school-name">{{ $settings['school_name'] }}</h1>
                <p class="school-address">{{ $settings['school_address'] }}</p>
            </td>
        </tr>
    </table>

    <div class="title">Daftar Data Siswa</div>

    <table class="meta-info">
        <tr>
            <td class="meta-left">
                @if($search)
                    <strong>Pencarian:</strong> "{{ $search }}"
                @endif
            </td>
            <td class="meta-right">
                Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th width="10%">NIS</th>
                <th width="12%">NISN</th>
                <th>Nama Siswa</th>
                <th width="10%">Kelas</th>
                <th width="12%">Jurusan</th>
                <th width="12%" class="text-center">Status</th>
                <th width="10%" class="text-center">Thn Lulus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $student->nis ?? '-' }}</td>
                    <td>{{ $student->nisn }}</td>
                    <td><strong>{{ $student->name }}</strong></td>
                    <td>{{ $student->class ?? '-' }}</td>
                    <td>{{ $student->jurusan ?? '-' }}</td>
                    <td class="text-center">
                        @if($student->status === 'LULUS')
                            <span class="badge badge-success">LULUS</span>
                        @else
                            <span class="badge badge-danger">TIDAK LULUS</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $student->tahun_lulus ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #666;">Data siswa tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
