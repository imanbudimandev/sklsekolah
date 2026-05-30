<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }
        $settings = [
            'school_name' => Setting::get('school_name', 'SMP Nurul Ihsan Banjaran'),
            'school_logo' => Setting::get('school_logo'),
        ];
        return view('admin.login', compact('settings'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }

    public function dashboard()
    {
        $total_students = Student::count();
        $total_lulus = Student::where('status', 'LULUS')->count();
        $total_tidak_lulus = Student::where('status', 'TIDAK LULUS')->count();
        $total_subjects = Subject::count();
        $average_score = round(Grade::avg('score') ?? 0, 2);

        // Recent students
        $recent_students = Student::orderBy('updated_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'total_students',
            'total_lulus',
            'total_tidak_lulus',
            'total_subjects',
            'average_score',
            'recent_students'
        ));
    }

    // --- Student Management ---

    public function students(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('exam_number', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
        }

        $students = $query->with('grades.subject')->orderBy('name')->paginate(20);
        $subjects = Subject::all();

        return view('admin.students.index', compact('students', 'subjects'));
    }

    public function storeStudent(Request $request)
    {
        $data = $request->validate([
            'exam_number' => 'nullable|unique:students,exam_number',
            'nis' => 'nullable',
            'nisn' => 'required|unique:students,nisn',
            'name' => 'required',
            'birth_place' => 'nullable',
            'birth_date' => 'nullable|date',
            'class' => 'nullable',
            'jurusan' => 'nullable',
            'status' => 'required|in:LULUS,TIDAK LULUS',
            'password' => 'nullable',
            'tahun_lulus' => 'nullable',
        ]);

        Student::create($data);

        return redirect()->route('admin.students')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function updateStudent(Request $request, Student $student)
    {
        $data = $request->validate([
            'exam_number' => 'nullable|unique:students,exam_number,' . $student->id,
            'nis' => 'nullable',
            'nisn' => 'required|unique:students,nisn,' . $student->id,
            'name' => 'required',
            'birth_place' => 'nullable',
            'birth_date' => 'nullable|date',
            'class' => 'nullable',
            'jurusan' => 'nullable',
            'status' => 'required|in:LULUS,TIDAK LULUS',
            'password' => 'nullable',
            'tahun_lulus' => 'nullable',
        ]);

        $student->update($data);

        return redirect()->route('admin.students')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroyStudent(Student $student)
    {
        $student->delete();
        return redirect()->route('admin.students')->with('success', 'Data siswa berhasil dihapus.');
    }

    // --- Subject Management ---

    public function subjects(Request $request)
    {
        $query = Subject::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        $subjects = $query->orderBy('code')->paginate(20);

        return view('admin.subjects.index', compact('subjects'));
    }

    public function storeSubject(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:subjects,code',
            'name' => 'required',
            'category' => 'nullable',
        ]);

        Subject::create($data);

        return redirect()->route('admin.subjects')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'code' => 'required|unique:subjects,code,' . $subject->id,
            'name' => 'required',
            'category' => 'nullable',
        ]);

        $subject->update($data);

        return redirect()->route('admin.subjects')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroySubject(Subject $subject)
    {
        $subject->delete();
        return redirect()->route('admin.subjects')->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    // --- Settings Management ---

    public function settings()
    {
        $settings = [
            'school_name' => Setting::get('school_name', 'SMP Nurul Ihsan Banjaran'),
            'school_address' => Setting::get('school_address', ''),
            'principal_name' => Setting::get('principal_name', ''),
            'principal_nip' => Setting::get('principal_nip', ''),
            'announcement_date' => Setting::get('announcement_date', ''),
            'school_logo' => Setting::get('school_logo'),
            'principal_signature' => Setting::get('principal_signature'),
            'dashboard_logo' => Setting::get('dashboard_logo'),
            'favicon' => Setting::get('favicon'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'school_name' => 'required',
            'school_address' => 'nullable',
            'principal_name' => 'required',
            'principal_nip' => 'nullable',
            'announcement_date' => 'required',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'principal_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'dashboard_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico|max:1024',
        ]);

        Setting::set('school_name', $request->input('school_name'));
        Setting::set('school_address', $request->input('school_address'));
        Setting::set('principal_name', $request->input('principal_name'));
        Setting::set('principal_nip', $request->input('principal_nip'));
        
        // Save date format properly
        $announcementDate = Carbon::parse($request->input('announcement_date'))->format('Y-m-d H:i:s');
        Setting::set('announcement_date', $announcementDate);

        // Ensure upload directory exists
        if (!File::exists(public_path('uploads'))) {
            File::makeDirectory(public_path('uploads'), 0755, true);
        }

        // Handle school logo
        if ($request->hasFile('school_logo')) {
            // Delete old if exists
            $oldLogo = Setting::get('school_logo');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }

            $logoFile = $request->file('school_logo');
            $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('uploads'), $logoName);
            Setting::set('school_logo', 'uploads/' . $logoName);
        }

        // Handle dashboard logo
        if ($request->hasFile('dashboard_logo')) {
            // Delete old if exists
            $oldDashLogo = Setting::get('dashboard_logo');
            if ($oldDashLogo && File::exists(public_path($oldDashLogo))) {
                File::delete(public_path($oldDashLogo));
            }

            $dashLogoFile = $request->file('dashboard_logo');
            $dashLogoName = 'dash_logo_' . time() . '.' . $dashLogoFile->getClientOriginalExtension();
            $dashLogoFile->move(public_path('uploads'), $dashLogoName);
            Setting::set('dashboard_logo', 'uploads/' . $dashLogoName);
        }

        // Handle favicon
        if ($request->hasFile('favicon')) {
            // Delete old if exists
            $oldFavicon = Setting::get('favicon');
            if ($oldFavicon && File::exists(public_path($oldFavicon))) {
                File::delete(public_path($oldFavicon));
            }

            $faviconFile = $request->file('favicon');
            $faviconName = 'favicon_' . time() . '.' . $faviconFile->getClientOriginalExtension();
            $faviconFile->move(public_path('uploads'), $faviconName);
            Setting::set('favicon', 'uploads/' . $faviconName);
        }

        // Handle principal signature
        if ($request->hasFile('principal_signature')) {
            // Delete old if exists
            $oldSig = Setting::get('principal_signature');
            if ($oldSig && File::exists(public_path($oldSig))) {
                File::delete(public_path($oldSig));
            }

            $sigFile = $request->file('principal_signature');
            $sigName = 'signature_' . time() . '.' . $sigFile->getClientOriginalExtension();
            $sigFile->move(public_path('uploads'), $sigName);
            Setting::set('principal_signature', 'uploads/' . $sigName);
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil diperbarui.');
    }

    // --- Transcript Settings ---
    public function transcriptSettings()
    {
        $settings = [
            'transcript_logo' => Setting::get('transcript_logo'),
            'transcript_header' => Setting::get('transcript_header', "LEMBAGA PENDIDIKAN ISLAM \"RIYADHUL JANNAH\"\nSMP NURUL IHSAN\nNSS: 202000012010 | NPSN: 20233628 | Akreditasi: \"B\"\nWebsite: smpnurulihsanbanjaran.sch.id | E-mail: smpnurulihsanbanjaran@gmail.com\nJl. Raya Banjaran No. 123, Banjaran, Bandung, Jawa Barat"),
            'transcript_footer' => Setting::get('transcript_footer', 'Catatan: Nilai akhir merupakan rata-rata dari semester I hingga VI.'),
            'transcript_letter_number' => Setting::get('transcript_letter_number', '421.3/[NUMBER]/SMP.NI/[YEAR]'),
            'transcript_place' => Setting::get('transcript_place', 'Subang'),
            'transcript_date_format' => Setting::get('transcript_date_format', 'd F Y'),
            'transcript_signature_text' => Setting::get('transcript_signature_text', 'Surat transkrip ini merupakan dokumen resmi yang sah.'),
        ];

        return view('admin.transcripts.settings', compact('settings'));
    }

    public function updateTranscriptSettings(Request $request)
    {
        $request->validate([
            'transcript_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'transcript_header' => 'nullable|string',
            'transcript_footer' => 'nullable|string',
            'transcript_letter_number' => 'nullable|string',
            'transcript_place' => 'nullable|string',
            'transcript_date_format' => 'nullable|string',
            'transcript_signature_text' => 'nullable|string',
        ]);

        Setting::set('transcript_header', $request->input('transcript_header'));
        Setting::set('transcript_footer', $request->input('transcript_footer'));
        Setting::set('transcript_letter_number', $request->input('transcript_letter_number'));
        Setting::set('transcript_place', $request->input('transcript_place'));
        Setting::set('transcript_date_format', $request->input('transcript_date_format', 'd F Y'));
        Setting::set('transcript_signature_text', $request->input('transcript_signature_text'));

        // Ensure upload directory exists
        if (!File::exists(public_path('uploads'))) {
            File::makeDirectory(public_path('uploads'), 0755, true);
        }

        if ($request->hasFile('transcript_logo')) {
            $old = Setting::get('transcript_logo');
            if ($old && File::exists(public_path($old))) {
                File::delete(public_path($old));
            }
            $file = $request->file('transcript_logo');
            $name = 'transcript_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $name);
            Setting::set('transcript_logo', 'uploads/' . $name);
        }

        return redirect()->route('admin.transcripts.settings')->with('success', 'Pengaturan transkrip berhasil diperbarui.');
    }

    // --- SKL Settings ---
    public function sklSettings()
    {
        $settings = [
            'skl_logo' => Setting::get('skl_logo'),
            'skl_header' => Setting::get('skl_header', "YAYASAN NURUL IHSAN BANJARAN\nSMP NURUL IHSAN\nJl. Raya Banjaran No. 123, Banjaran, Bandung, Jawa Barat\nWebsite: smpnurulihsanbanjaran.sch.id | E-mail: smpnurulihsanbanjaran@gmail.com"),
            'skl_letter_number' => Setting::get('skl_letter_number', '421.3/[NUMBER]/SMP.NI/[YEAR]'),
            'skl_place' => Setting::get('skl_place', 'Banjaran'),
            'skl_date_format' => Setting::get('skl_date_format', 'd F Y'),
            'skl_signature_text' => Setting::get('skl_signature_text', 'Kepala Sekolah,'),
            'skl_opening_text' => Setting::get('skl_opening_text', 'Yang bertanda tangan di bawah ini, Kepala Sekolah [NAMA_SEKOLAH] Kecamatan Banjaran Kabupaten Bandung, menerangkan bahwa:'),
            'skl_body_text' => Setting::get('skl_body_text', 'Berdasarkan Kriteria Kelulusan Peserta Didik yang diatur dalam kurikulum yang berlaku dan Rapat Pleno Dewan Guru [NAMA_SEKOLAH] tentang Kelulusan Siswa Kelas IX Tahun Pelajaran [TAHUN_PELAJARAN] pada tanggal [TANGGAL_PENGUMUMAN], dengan ini menyatakan bahwa siswa tersebut di atas:'),
            'skl_footer_text' => Setting::get('skl_footer_text', '* Surat Keterangan Lulus ini berlaku sementara sampai diterbitkannya Ijazah asli bagi peserta didik yang dinyatakan lulus, guna melengkapi syarat pendaftaran jenjang pendidikan selanjutnya.'),
        ];

        return view('admin.transcripts.settings_skl', compact('settings'));
    }

    public function updateSklSettings(Request $request)
    {
        $request->validate([
            'skl_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'skl_header' => 'nullable|string',
            'skl_letter_number' => 'nullable|string',
            'skl_place' => 'nullable|string',
            'skl_date_format' => 'nullable|string',
            'skl_signature_text' => 'nullable|string',
        ]);

        Setting::set('skl_header', $request->input('skl_header'));
        Setting::set('skl_letter_number', $request->input('skl_letter_number', '421.3/[NUMBER]/SMP.NI/[YEAR]'));
        Setting::set('skl_place', $request->input('skl_place', 'Banjaran'));
        Setting::set('skl_date_format', $request->input('skl_date_format', 'd F Y'));
        Setting::set('skl_signature_text', $request->input('skl_signature_text', 'Kepala Sekolah'));
        
        Setting::set('skl_opening_text', $request->input('skl_opening_text'));
        Setting::set('skl_body_text', $request->input('skl_body_text'));
        Setting::set('skl_footer_text', $request->input('skl_footer_text'));

        // Ensure upload directory exists
        if (!File::exists(public_path('uploads'))) {
            File::makeDirectory(public_path('uploads'), 0755, true);
        }

        if ($request->hasFile('skl_logo')) {
            $old = Setting::get('skl_logo');
            if ($old && File::exists(public_path($old))) {
                File::delete(public_path($old));
            }
            $file = $request->file('skl_logo');
            $name = 'skl_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $name);
            Setting::set('skl_logo', 'uploads/' . $name);
        }

        return redirect()->route('admin.skl.settings')->with('success', 'Pengaturan SKL berhasil diperbarui.');
    }

    // --- Excel Import & Export ---

    public function importStudentsExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        $file = $request->file('file');

        $tmpPath = $file->getPathname();
        if (!file_exists($tmpPath) || !is_readable($tmpPath)) {
            return redirect()->back()->with('error', 'File upload tidak ditemukan atau tidak bisa dibaca.');
        }

        try {
            $spreadsheet = IOFactory::load($tmpPath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (count($rows) < 2) {
            return redirect()->back()->with('error', 'File Excel kosong atau tidak valid.');
        }

        $header = array_map(function($val) {
            return strtolower(trim((string) $val));
        }, $rows[0]);

        $map = [
            'nis' => array_search('nis', $header),
            'nisn' => array_search('nisn', $header),
            'name' => array_search('nama', $header) !== false ? array_search('nama', $header) : array_search('name', $header),
            'birth_place' => array_search('tempat', $header) !== false ? array_search('tempat', $header) : array_search('birth_place', $header),
            'birth_date' => array_search('tanggal_lahir', $header),
            'class' => array_search('kelas', $header) !== false ? array_search('kelas', $header) : array_search('class', $header),
            'jurusan' => array_search('jurusan', $header),
            'status' => array_search('kelulusan', $header) !== false ? array_search('kelulusan', $header) : array_search('status', $header),
            'password' => array_search('password', $header),
            'tahun_lulus' => array_search('tahun_lulus', $header) !== false ? array_search('tahun_lulus', $header) : array_search('tahun_lulus', $header),
        ];

        if ($map['nisn'] === false || $map['name'] === false) {
            return redirect()->back()->with('error', 'Kolom wajib (nisn, nama) tidak ditemukan dalam file Excel.');
        }

        $successCount = 0;
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (count($row) < 2) continue;

            $nisn = trim((string) ($row[$map['nisn']] ?? ''));
            $name = trim((string) ($row[$map['name']] ?? ''));

            if (empty($nisn) || empty($name)) {
                continue;
            }

            $birth_date = null;
            if ($map['birth_date'] !== false && !empty($row[$map['birth_date']])) {
                $birth_date = trim((string) $row[$map['birth_date']]);
            }

            Student::updateOrCreate(
                ['nisn' => $nisn],
                [
                    'nis' => $map['nis'] !== false ? trim((string) ($row[$map['nis']] ?? '')) : null,
                    'name' => $name,
                    'birth_place' => $map['birth_place'] !== false ? trim((string) ($row[$map['birth_place']] ?? '')) : null,
                    'birth_date' => $birth_date,
                    'class' => $map['class'] !== false ? trim((string) ($row[$map['class']] ?? '')) : null,
                    'jurusan' => $map['jurusan'] !== false ? trim((string) ($row[$map['jurusan']] ?? '')) : null,
                    'status' => $map['status'] !== false ? strtoupper(trim((string) ($row[$map['status']] ?? ''))) : 'LULUS',
                    'password' => $map['password'] !== false ? trim((string) ($row[$map['password']] ?? '')) : null,
                    'tahun_lulus' => $map['tahun_lulus'] !== false ? trim((string) ($row[$map['tahun_lulus']] ?? '')) : null,
                ]
            );

            $successCount++;
        }

        return redirect()->route('admin.students')->with('success', "Berhasil mengimpor {$successCount} data siswa.");
    }

    public function downloadStudentTemplate()
    {
        $headers = ['nis', 'nisn', 'nama', 'tempat', 'tanggal_lahir', 'kelas', 'jurusan', 'kelulusan', 'password', 'tahun_lulus'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Siswa');

        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($colLetter . '1', $h);
            $colLetter++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);

        $exampleRow = ['12345', '1234567890', 'Ahmad Fauzi', 'Bandung', '2011-05-12', 'IX-A', 'IPA', 'LULUS', 'siswa123', '2026'];
        $colLetter = 'A';
        foreach ($exampleRow as $val) {
            $sheet->setCellValue($colLetter . '2', $val);
            $colLetter++;
        }

        // Format all data cells as text to prevent Excel auto-formatting dates
        $lastCol = $sheet->getHighestColumn();
        $sheet->getStyle('A2:' . $lastCol . '2')
              ->getNumberFormat()
              ->setFormatCode(NumberFormat::FORMAT_TEXT);
        $sheet->getStyle('A2:' . $lastCol . '1000')
              ->getNumberFormat()
              ->setFormatCode(NumberFormat::FORMAT_TEXT);

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_import_siswa.xlsx"',
        ]);
    }

    public function exportStudentsExcel()
    {
        $students = Student::orderBy('nisn')->get();
        $headers = ['nis', 'nisn', 'nama', 'tempat', 'tanggal_lahir', 'kelas', 'jurusan', 'kelulusan', 'password', 'tahun_lulus'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        $colLetter = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($colLetter . '1', $h);
            $colLetter++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);

        $rowNum = 2;
        foreach ($students as $student) {
            $rowData = [
                $student->nis,
                $student->nisn,
                $student->name,
                $student->birth_place,
                $student->birth_date ? $student->birth_date->format('Y-m-d') : '',
                $student->class,
                $student->jurusan,
                $student->status,
                $student->password,
                $student->tahun_lulus,
            ];
            $colLetter = 'A';
            foreach ($rowData as $val) {
                $sheet->setCellValue($colLetter . $rowNum, $val);
                $colLetter++;
            }
            $rowNum++;
        }

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="data_siswa.xlsx"',
        ]);
    }

    public function importSubjectsCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:txt,csv'
        ]);

        $file = $request->file('csv_file');
        $delimiter = ',';
        $successCount = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $firstLine = fgets($handle);
            rewind($handle);
            
            if (strpos($firstLine, ';') !== false && strpos($firstLine, ',') === false) {
                $delimiter = ';';
            }

            $header = fgetcsv($handle, 0, $delimiter);
            if (!$header) {
                return redirect()->back()->with('error', 'Format CSV tidak valid.');
            }

            $header = array_map(function($val) {
                return strtolower(trim(preg_replace('/[\x00-\x1F\x7F-\x9F\xEF\xBB\xBF]/', '', $val)));
            }, $header);

            $map = [
                'code' => array_search('code', $header) !== false ? array_search('code', $header) : array_search('kode', $header),
                'name' => array_search('name', $header) !== false ? array_search('name', $header) : array_search('nama', $header),
                'category' => array_search('category', $header) !== false ? array_search('category', $header) : array_search('kategori', $header),
            ];

            if ($map['code'] === false || $map['name'] === false) {
                return redirect()->back()->with('error', 'Kolom wajib (code, name) tidak ditemukan dalam CSV.');
            }

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($row) < 2) continue;

                if (count($row) < count($header)) {
                    $row = array_pad($row, count($header), null);
                }

                $code = strtoupper(trim($row[$map['code']]));
                $name = trim($row[$map['name']]);
                $category = $map['category'] !== false ? trim($row[$map['category']]) : 'Kelompok A';

                if (empty($code) || empty($name)) {
                    continue;
                }

                Subject::updateOrCreate(
                    ['code' => $code],
                    [
                        'name' => $name,
                        'category' => $category
                    ]
                );
                $successCount++;
            }
            fclose($handle);
        }

        return redirect()->route('admin.subjects')->with('success', "Berhasil mengimpor {$successCount} mata pelajaran.");
    }

    public function downloadSubjectTemplate()
    {
        $headers = ['code', 'name', 'category'];
        
        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['MAT", "Matematika", "Kelompok A']);
            fputcsv($file, ['SBK", "Seni Budaya dan Keterampilan", "Kelompok B']);
            fclose($file);
        };

        $headers_response = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=template_mapel.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->stream($callback, 200, $headers_response);
    }

    // --- Grade Management (Multi-Semester) ---

    public function grades(Request $request)
    {
        $semester = $request->input('semester', 'Semester 1');
        $validSemesters = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Ujian Sekolah'];
        if (!in_array($semester, $validSemesters)) {
            $semester = 'Semester 1';
        }

        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('exam_number', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
        }

        // Eager load grades with subjects for the selected semester
        $students = $query->with(['grades' => function ($q) use ($semester) {
            $q->where('semester', $semester);
        }])->orderBy('name')->paginate(20);

        $subjects = Subject::orderBy('code')->get();

        return view('admin.grades.index', compact('students', 'subjects', 'semester', 'validSemesters'));
    }

    public function storeGrades(Request $request)
    {
        $semester = $request->input('semester', 'Semester 1');
        $validSemesters = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Ujian Sekolah'];
        if (!in_array($semester, $validSemesters)) {
            return redirect()->back()->with('error', 'Semester tidak valid.');
        }

        $gradesData = $request->input('grades', []);

        foreach ($gradesData as $studentId => $subjectScores) {
            foreach ($subjectScores as $subjectId => $score) {
                if ($score !== null && $score !== '') {
                    Grade::updateOrCreate(
                        [
                            'student_id' => $studentId,
                            'subject_id' => $subjectId,
                            'semester' => $semester
                        ],
                        ['score' => floatval($score)]
                    );
                } else {
                    Grade::where('student_id', $studentId)
                         ->where('subject_id', $subjectId)
                         ->where('semester', $semester)
                         ->delete();
                }
            }
        }

        return redirect()->back()->with('success', 'Nilai berhasil disimpan untuk ' . $semester . '.');
    }

    public function importGradesCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:txt,csv',
            'semester' => 'required'
        ]);

        $semester = $request->input('semester');
        $validSemesters = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Ujian Sekolah'];
        if (!in_array($semester, $validSemesters)) {
            return redirect()->back()->with('error', 'Semester tidak valid.');
        }

        $file = $request->file('csv_file');
        $delimiter = ',';
        $successCount = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $firstLine = fgets($handle);
            rewind($handle);
            
            if (strpos($firstLine, ';') !== false && strpos($firstLine, ',') === false) {
                $delimiter = ';';
            } elseif (strpos($firstLine, ';') !== false && strpos($firstLine, ',') !== false) {
                $semicolons = substr_count($firstLine, ';');
                $commas = substr_count($firstLine, ',');
                if ($semicolons > $commas) {
                    $delimiter = ';';
                }
            }

            $header = fgetcsv($handle, 0, $delimiter);
            
            if (!$header) {
                return redirect()->back()->with('error', 'Format CSV tidak valid.');
            }

            $header = array_map(function($val) {
                return strtolower(trim(preg_replace('/[\x00-\x1F\x7F-\x9F\xEF\xBB\xBF]/', '', $val)));
            }, $header);

            $map = [
                'exam_number' => array_search('no_peserta', $header) !== false ? array_search('no_peserta', $header) : array_search('exam_number', $header),
                'nisn' => array_search('nisn', $header),
            ];

            if ($map['exam_number'] === false && $map['nisn'] === false) {
                return redirect()->back()->with('error', 'Kolom pengidentifikasi (no_peserta atau nisn) tidak ditemukan dalam CSV.');
            }

            $subjectColumns = [];
            $allSubjects = Subject::all()->keyBy(function($item) {
                return strtolower($item->code);
            });

            foreach ($header as $index => $colName) {
                if ($index === $map['exam_number'] || $index === $map['nisn']) {
                    continue;
                }
                
                $cleanColName = strtolower($colName);
                if (isset($allSubjects[$cleanColName])) {
                    $subjectColumns[$index] = $allSubjects[$cleanColName]->id;
                }
            }

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (count($row) < 2) continue;

                if (count($row) < count($header)) {
                    $row = array_pad($row, count($header), null);
                }

                $student = null;
                if ($map['exam_number'] !== false && !empty(trim($row[$map['exam_number']]))) {
                    $student = Student::where('exam_number', trim($row[$map['exam_number']]))->first();
                }
                if (!$student && $map['nisn'] !== false && !empty(trim($row[$map['nisn']]))) {
                    $student = Student::where('nisn', trim($row[$map['nisn']]))->first();
                }

                if (!$student) {
                    continue;
                }

                foreach ($subjectColumns as $index => $subjectId) {
                    $scoreValue = trim($row[$index]);
                    if ($scoreValue !== '' && $scoreValue !== null) {
                        Grade::updateOrCreate(
                            [
                                'student_id' => $student->id,
                                'subject_id' => $subjectId,
                                'semester' => $semester
                            ],
                            ['score' => floatval($scoreValue)]
                        );
                    } else {
                        Grade::where('student_id', $student->id)
                             ->where('subject_id', $subjectId)
                             ->where('semester', $semester)
                             ->delete();
                    }
                }
                $successCount++;
            }
            fclose($handle);
        }

        return redirect()->back()->with('success', "Berhasil mengimpor {$successCount} data nilai untuk {$semester}.");
    }

    public function downloadGradesTemplate(Request $request)
    {
        $semester = $request->input('semester', 'Semester 1');
        $subjects = Subject::orderBy('code')->get();
        $headers = ['no_peserta', 'nisn', 'nama'];
        
        foreach ($subjects as $subject) {
            $headers[] = $subject->code;
        }

        $students = Student::orderBy('name')->get();

        $callback = function() use ($headers, $students, $subjects, $semester) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            foreach ($students as $student) {
                $gradesMap = Grade::where('student_id', $student->id)
                    ->where('semester', $semester)
                    ->pluck('score', 'subject_id');

                $row = [$student->exam_number, $student->nisn, $student->name];
                foreach ($subjects as $subject) {
                    $row[] = $gradesMap->get($subject->id) ?? '';
                }
                fputcsv($file, $row);
            }
            
            if ($students->isEmpty()) {
                $exampleRow = ['02-001-001-1', '1234567890', 'Ahmad Fauzi'];
                foreach ($subjects as $subject) {
                    $exampleRow[] = '85';
                }
                fputcsv($file, $exampleRow);
            }
            
            fclose($file);
        };

        $filename = "template_nilai_" . strtolower(str_replace(' ', '_', $semester)) . ".csv";

        $headers_response = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        return response()->stream($callback, 200, $headers_response);
    }

    public function transcripts(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('exam_number', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
        }

        $students = $query->with('grades')->orderBy('name')->paginate(20);

        return view('admin.transcripts.index', compact('students'));
    }

    public function downloadTranscriptPdf(Student $student)
    {
        $student->load(['grades.subject']);
        $subjects = Subject::orderBy('code')->get();
        
        $announcementDateStr = Setting::get('announcement_date');
        $announcementDate = $announcementDateStr ? Carbon::parse($announcementDateStr) : null;
        
        $settings = [
            'school_name' => Setting::get('school_name', 'SMP Nurul Ihsan Banjaran'),
            'school_address' => Setting::get('school_address', ''),
            'principal_name' => Setting::get('principal_name', ''),
            'principal_nip' => Setting::get('principal_nip', ''),
            'school_logo' => Setting::get('school_logo'),
            'principal_signature' => Setting::get('principal_signature'),
            'transcript_logo' => Setting::get('transcript_logo'),
            'transcript_header' => Setting::get('transcript_header', "LEMBAGA PENDIDIKAN ISLAM \"RIYADHUL JANNAH\"\nSMP NURUL IHSAN\nNSS: 202000012010 | NPSN: 20233628 | Akreditasi: \"B\"\nWebsite: smpnurulihsanbanjaran.sch.id | E-mail: smpnurulihsanbanjaran@gmail.com\nJl. Raya Banjaran No. 123, Banjaran, Bandung, Jawa Barat"),
            'transcript_footer' => Setting::get('transcript_footer', 'Catatan: Nilai akhir merupakan rata-rata dari semester I hingga VI.'),
            'transcript_letter_number' => Setting::get('transcript_letter_number', '421.3/[NUMBER]/SMP.NI/[YEAR]'),
            'transcript_place' => Setting::get('transcript_place', 'Subang'),
            'transcript_date_format' => Setting::get('transcript_date_format', 'd F Y'),
            'transcript_signature_text' => Setting::get('transcript_signature_text', 'Surat transkrip ini merupakan dokumen resmi yang sah.'),
        ];

        // Use physical path for logo and signature (much more stable in DomPDF)
        $logo_path = null;
        $logoSetting = Setting::get('school_logo') ?: Setting::get('transcript_logo');
        if (!empty($logoSetting) && file_exists(public_path($logoSetting))) {
            $logo_path = public_path($logoSetting);
        }

        $signature_path = null;
        if (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) {
            $signature_path = public_path($settings['principal_signature']);
        }

        $students = collect([$student]);

        $pdf = Pdf::loadView('admin.transcripts.pdf', compact('students', 'subjects', 'announcementDate', 'settings', 'logo_path', 'signature_path'));
        
        $filename = "transkrip_" . strtolower(str_replace(' ', '_', $student->name)) . ".pdf";
        return $pdf->download($filename);
    }

    public function previewTranscriptPdf(Student $student)
    {
        $student->load(['grades.subject']);
        $subjects = Subject::orderBy('code')->get();
        
        $announcementDateStr = Setting::get('announcement_date');
        $announcementDate = $announcementDateStr ? Carbon::parse($announcementDateStr) : null;
        
        $settings = [
            'school_name' => Setting::get('school_name', 'SMP Nurul Ihsan Banjaran'),
            'school_address' => Setting::get('school_address', ''),
            'principal_name' => Setting::get('principal_name', ''),
            'principal_nip' => Setting::get('principal_nip', ''),
            'school_logo' => Setting::get('school_logo'),
            'principal_signature' => Setting::get('principal_signature'),
            'transcript_logo' => Setting::get('transcript_logo'),
            'transcript_header' => Setting::get('transcript_header', '<div style="text-align: center; font-family: Arial, sans-serif;"><h4 style="margin: 0; color: #0d9488; font-size: 9pt; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">LEMBAGA PENDIDIKAN ISLAM &ldquo;RIYADHUL JANNAH&rdquo;</h4><h2 style="margin: 5px 0; font-size: 14pt; font-weight: 800; text-transform: uppercase;">SMP NURUL IHSAN</h2><p style="margin: 0; font-size: 8.5pt; color: #4b5563;">NSS: 202000012010 &bull; NPSN: 20233628 &bull; Akreditasi: &ldquo;B&rdquo;</p><p style="margin: 0; font-size: 8pt; color: #6b7280;">Website: smpnurulihsanbanjaran.sch.id &bull; E-mail: smpnurulihsanbanjaran@gmail.com</p><p style="margin: 3px 0 0 0; font-size: 8.5pt; color: #4b5563; font-weight: 500;">Jl. Raya Banjaran No. 123, Banjaran, Bandung, Jawa Barat</p></div>'),
            'transcript_footer' => Setting::get('transcript_footer', 'Catatan: Nilai akhir merupakan rata-rata dari semester I hingga VI.'),
            'transcript_letter_number' => Setting::get('transcript_letter_number', '421.3/[NUMBER]/SMP.NI/[YEAR]'),
            'transcript_place' => Setting::get('transcript_place', 'Subang'),
            'transcript_date_format' => Setting::get('transcript_date_format', 'd F Y'),
            'transcript_signature_text' => Setting::get('transcript_signature_text', 'Surat transkrip ini merupakan dokumen resmi yang sah.'),
        ];

        // Use physical path for logo and signature (much more stable in DomPDF)
        $logo_path = null;
        $logoSetting = Setting::get('school_logo') ?: Setting::get('transcript_logo');
        if (!empty($logoSetting) && file_exists(public_path($logoSetting))) {
            $logo_path = public_path($logoSetting);
        }

        $signature_path = null;
        if (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) {
            $signature_path = public_path($settings['principal_signature']);
        }

        $students = collect([$student]);

        $pdf = Pdf::loadView('admin.transcripts.pdf', compact('students', 'subjects', 'announcementDate', 'settings', 'logo_path', 'signature_path'));
        
        return $pdf->stream();
    }

    public function downloadBulkTranscriptsPdf(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('exam_number', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
        }

        $students = $query->with(['grades.subject'])->orderBy('name')->get();
        $subjects = Subject::orderBy('code')->get();
        
        $announcementDateStr = Setting::get('announcement_date');
        $announcementDate = $announcementDateStr ? Carbon::parse($announcementDateStr) : null;
        
        $settings = [
            'school_name' => Setting::get('school_name', 'SMP Nurul Ihsan Banjaran'),
            'school_address' => Setting::get('school_address', ''),
            'principal_name' => Setting::get('principal_name', ''),
            'principal_nip' => Setting::get('principal_nip', ''),
            'school_logo' => Setting::get('school_logo'),
            'principal_signature' => Setting::get('principal_signature'),
            'transcript_logo' => Setting::get('transcript_logo'),
            'transcript_header' => Setting::get('transcript_header', '<div style="text-align: center; font-family: Arial, sans-serif;"><h4 style="margin: 0; color: #0d9488; font-size: 9pt; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">LEMBAGA PENDIDIKAN ISLAM &ldquo;RIYADHUL JANNAH&rdquo;</h4><h2 style="margin: 5px 0; font-size: 14pt; font-weight: 800; text-transform: uppercase;">SMP NURUL IHSAN</h2><p style="margin: 0; font-size: 8.5pt; color: #4b5563;">NSS: 202000012010 &bull; NPSN: 20233628 &bull; Akreditasi: &ldquo;B&rdquo;</p><p style="margin: 0; font-size: 8pt; color: #6b7280;">Website: smpnurulihsanbanjaran.sch.id &bull; E-mail: smpnurulihsanbanjaran@gmail.com</p><p style="margin: 3px 0 0 0; font-size: 8.5pt; color: #4b5563; font-weight: 500;">Jl. Raya Banjaran No. 123, Banjaran, Bandung, Jawa Barat</p></div>'),
            'transcript_footer' => Setting::get('transcript_footer', 'Catatan: Nilai akhir merupakan rata-rata dari semester I hingga VI.'),
            'transcript_letter_number' => Setting::get('transcript_letter_number', '421.3/[NUMBER]/SMP.NI/[YEAR]'),
            'transcript_place' => Setting::get('transcript_place', 'Subang'),
            'transcript_date_format' => Setting::get('transcript_date_format', 'd F Y'),
            'transcript_signature_text' => Setting::get('transcript_signature_text', 'Surat transkrip ini merupakan dokumen resmi yang sah.'),
        ];

        $logoSetting = Setting::get('school_logo') ?: Setting::get('transcript_logo');
        $logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
        $signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

        $pdf = Pdf::loadView('admin.transcripts.pdf', compact('students', 'subjects', 'announcementDate', 'settings', 'logo_path', 'signature_path'));
        
        $filename = "transkrip_masal_" . date('Ymd_His') . ".pdf";
        return $pdf->download($filename);
    }

    public function downloadSklPdf(Student $student)
    {
        $student->load(['grades.subject']);
        
        $announcementDateStr = Setting::get('announcement_date');
        $announcementDate = $announcementDateStr ? Carbon::parse($announcementDateStr) : null;
        
        $settings = [
            'school_name' => Setting::get('school_name', 'SMP Nurul Ihsan Banjaran'),
            'school_address' => Setting::get('school_address', ''),
            'principal_name' => Setting::get('principal_name', ''),
            'principal_nip' => Setting::get('principal_nip', ''),
            'school_logo' => Setting::get('school_logo'),
            'principal_signature' => Setting::get('principal_signature'),
            'skl_logo' => Setting::get('skl_logo'),
            'skl_letter_number' => Setting::get('skl_letter_number', '421.3/[NUMBER]/SMP.NI/[YEAR]'),
            'skl_place' => Setting::get('skl_place', 'Banjaran'),
            'skl_date_format' => Setting::get('skl_date_format', 'd F Y'),
            'skl_header' => Setting::get('skl_header', ''),
            'skl_signature_text' => Setting::get('skl_signature_text', 'Kepala Sekolah'),
        ];

        // Use physical path for logo and signature
        $logoSetting = $settings['skl_logo'] ?: $settings['school_logo'] ?: Setting::get('transcript_logo');
        $logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
        $signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

        // Parse dynamic letter number
        $number = rand(100, 300);
        $year = $announcementDate ? $announcementDate->format('Y') : date('Y');
        $letterNumber = str_replace(['[NUMBER]', '[YEAR]'], [$number, $year], $settings['skl_letter_number']);

        $pdf = Pdf::loadView('admin.transcripts.skl_pdf', compact('student', 'announcementDate', 'settings', 'logo_path', 'signature_path', 'letterNumber'));
        
        $filename = "skl_" . strtolower(str_replace(' ', '_', $student->name)) . ".pdf";
        return $pdf->download($filename);
    }

    public function previewSklPdf(Student $student)
    {
        $student->load(['grades.subject']);
        
        $announcementDateStr = Setting::get('announcement_date');
        $announcementDate = $announcementDateStr ? Carbon::parse($announcementDateStr) : null;
        
        $settings = [
            'school_name' => Setting::get('school_name', 'SMP Nurul Ihsan Banjaran'),
            'school_address' => Setting::get('school_address', ''),
            'principal_name' => Setting::get('principal_name', ''),
            'principal_nip' => Setting::get('principal_nip', ''),
            'school_logo' => Setting::get('school_logo'),
            'principal_signature' => Setting::get('principal_signature'),
            'skl_logo' => Setting::get('skl_logo'),
            'skl_letter_number' => Setting::get('skl_letter_number', '421.3/[NUMBER]/SMP.NI/[YEAR]'),
            'skl_place' => Setting::get('skl_place', 'Banjaran'),
            'skl_date_format' => Setting::get('skl_date_format', 'd F Y'),
            'skl_header' => Setting::get('skl_header', ''),
            'skl_signature_text' => Setting::get('skl_signature_text', 'Kepala Sekolah'),
        ];

        // Use physical path for logo and signature
        $logoSetting = $settings['skl_logo'] ?: $settings['school_logo'] ?: Setting::get('transcript_logo');
        $logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
        $signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

        // Parse dynamic letter number
        $number = rand(100, 300);
        $year = $announcementDate ? $announcementDate->format('Y') : date('Y');
        $letterNumber = str_replace(['[NUMBER]', '[YEAR]'], [$number, $year], $settings['skl_letter_number']);

        $pdf = Pdf::loadView('admin.transcripts.skl_pdf', compact('student', 'announcementDate', 'settings', 'logo_path', 'signature_path', 'letterNumber'));
        
        return $pdf->stream();
    }

    // --- Database Tools ---

    public function tools()
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $backups = collect(File::files($backupDir))
            ->filter(fn($f) => $f->getExtension() === 'sql')
            ->sortByDesc(fn($f) => $f->getMTime())
            ->map(fn($f) => [
                'filename' => $f->getFilename(),
                'size' => $f->getSize(),
                'date' => Carbon::createFromTimestamp($f->getMTime()),
            ])
            ->values();

        return view('admin.tools', compact('backups'));
    }

    public function backupDatabase()
    {
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = $backupDir . '/' . $filename;

        $mysqldump = 'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe';
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbName = env('DB_DATABASE', 'skl');
        $dbUser = env('DB_USERNAME', 'root');
        $dbPass = env('DB_PASSWORD', '');

        $command = sprintf(
            '"%s" --host=%s --port=%s --user=%s %s %s > "%s" 2>&1',
            $mysqldump,
            $dbHost,
            $dbPort,
            $dbUser,
            $dbPass ? '--password=' . $dbPass : '',
            $dbName,
            $filepath
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($filepath)) {
            return redirect()->route('admin.tools')->with('error', 'Gagal membuat backup database.');
        }

        return redirect()->route('admin.tools')->with('success', 'Backup database berhasil: ' . $filename);
    }

    public function downloadBackup($filename)
    {
        $filepath = storage_path('app/backups/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()->route('admin.tools')->with('error', 'File backup tidak ditemukan.');
        }

        return response()->download($filepath);
    }

    public function restoreDatabase(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|string',
        ]);

        $filename = $request->input('backup_file');
        $filepath = storage_path('app/backups/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()->route('admin.tools')->with('error', 'File backup tidak ditemukan.');
        }

        $mysql = 'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe';
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbPort = env('DB_PORT', '3306');
        $dbName = env('DB_DATABASE', 'skl');
        $dbUser = env('DB_USERNAME', 'root');
        $dbPass = env('DB_PASSWORD', '');

        $command = sprintf(
            '"%s" --host=%s --port=%s --user=%s %s %s < "%s" 2>&1',
            $mysql,
            $dbHost,
            $dbPort,
            $dbUser,
            $dbPass ? '--password=' . $dbPass : '',
            $dbName,
            $filepath
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            return redirect()->route('admin.tools')->with('error', 'Gagal merestore database.');
        }

        return redirect()->route('admin.tools')->with('success', 'Database berhasil direstore dari: ' . $filename);
    }

    public function deleteBackup($filename)
    {
        $filepath = storage_path('app/backups/' . $filename);

        if (!file_exists($filepath)) {
            return redirect()->route('admin.tools')->with('error', 'File backup tidak ditemukan.');
        }

        File::delete($filepath);

        return redirect()->route('admin.tools')->with('success', 'Backup berhasil dihapus: ' . $filename);
    }

    public function cleanData(Request $request)
    {
        $selected = $request->input('tables', []);
        if (empty($selected)) {
            return redirect()->route('admin.tools')->with('error', 'Pilih minimal satu kategori data yang akan dibersihkan.');
        }

        $cleaned = [];
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (in_array('grades', $selected)) {
            DB::table('grades')->truncate();
            $cleaned[] = 'Manajemen Nilai';
        }

        if (in_array('students', $selected)) {
            DB::table('students')->truncate();
            $cleaned[] = 'Data Siswa';
        }

        if (in_array('subjects', $selected)) {
            DB::table('subjects')->truncate();
            $cleaned[] = 'Mata Pelajaran';
        }

        if (in_array('letters', $selected)) {
            DB::table('students')->update(['transcript_grade' => null]);
            $cleaned[] = 'Surat (nilai ijazah)';
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return redirect()->route('admin.tools')->with('success', 'Data berhasil dibersihkan: ' . implode(', ', $cleaned) . '.');
    }
}
