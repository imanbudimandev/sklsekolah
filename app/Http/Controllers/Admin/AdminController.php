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

        $subjects = $query->orderBy('order_number')->orderBy('code')->paginate(20);

        return view('admin.subjects.index', compact('subjects'));
    }

    public function storeSubject(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|unique:subjects,code',
            'name' => 'required',
            'category' => 'nullable',
            'order_number' => 'nullable|integer',
            'jurusan' => 'nullable',
            'tampil_skl' => 'boolean',
            'tampil_transkip' => 'boolean',
        ]);

        $data['tampil_skl'] = $request->boolean('tampil_skl');
        $data['tampil_transkip'] = $request->boolean('tampil_transkip');

        Subject::create($data);

        return redirect()->route('admin.subjects')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'code' => 'required|unique:subjects,code,' . $subject->id,
            'name' => 'required',
            'category' => 'nullable',
            'order_number' => 'nullable|integer',
            'jurusan' => 'nullable',
            'tampil_skl' => 'boolean',
            'tampil_transkip' => 'boolean',
        ]);

        $data['tampil_skl'] = $request->boolean('tampil_skl');
        $data['tampil_transkip'] = $request->boolean('tampil_transkip');

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
            'school_year' => Setting::get('school_year', date('Y') . '/' . (date('Y') + 1)),
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
            'school_year' => 'nullable',
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
        Setting::set('school_year', $request->input('school_year'));
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
            'skl_after_lulus_text' => Setting::get('skl_after_lulus_text', ''),
            'skl_before_ttd_text' => Setting::get('skl_before_ttd_text', ''),
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
            'skl_after_lulus_text' => 'nullable|string',
            'skl_before_ttd_text' => 'nullable|string',
        ]);

        Setting::set('skl_header', $request->input('skl_header'));
        Setting::set('skl_letter_number', $request->input('skl_letter_number', '421.3/[NUMBER]/SMP.NI/[YEAR]'));
        Setting::set('skl_place', $request->input('skl_place', 'Banjaran'));
        Setting::set('skl_date_format', $request->input('skl_date_format', 'd F Y'));
        Setting::set('skl_signature_text', $request->input('skl_signature_text', 'Kepala Sekolah'));
        
        Setting::set('skl_opening_text', $request->input('skl_opening_text'));
        Setting::set('skl_body_text', $request->input('skl_body_text'));
        Setting::set('skl_footer_text', $request->input('skl_footer_text'));

        Setting::set('skl_after_lulus_text', $request->input('skl_after_lulus_text'));
        Setting::set('skl_before_ttd_text', $request->input('skl_before_ttd_text'));

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
            'file_import' => 'required|file|mimes:xlsx,xls'
        ]);

        $file = $request->file('file_import');
        $extension = strtolower($file->getClientOriginalExtension());
        $successCount = 0;

        $tmpPath = $file->getPathname();
        if (!file_exists($tmpPath) || !is_readable($tmpPath)) {
            return redirect()->back()->with('error', 'File upload tidak ditemukan atau tidak bisa dibaca.');
        }

        try {
            $reader = IOFactory::createReader(ucfirst($extension));
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($tmpPath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (empty($rows) || count($rows) < 2) {
            return redirect()->back()->with('error', 'File Excel kosong atau tidak valid.');
        }

        $header = array_map(function($val) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x7F-\x9F\xEF\xBB\xBF]/', '', (string)$val)));
        }, $rows[0]);
        unset($rows[0]);

        $headerMap = [
            'code' => ['kode mapel', 'kode_mapel', 'kode', 'code'],
            'name' => ['nama mapel', 'nama_mapel', 'nama mapel', 'nama', 'mata pelajaran', 'name'],
            'category' => ['kelompok mapel', 'kelompok_mapel', 'kelompok', 'kategori', 'category'],
            'order_number' => ['no urut', 'no_urut', 'nomor urut', 'urut', 'order_number'],
            'jurusan' => ['jurusan'],
            'tampil_skl' => ['tampilkan di skl', 'tampil_skl', 'tampil skl', 'skl'],
            'tampil_transkip' => ['tampil di transkip', 'tampil_transkip', 'tampil transkip', 'transkip'],
        ];

        $map = [];
        foreach ($headerMap as $field => $aliases) {
            $map[$field] = false;
            foreach ($aliases as $alias) {
                $idx = array_search($alias, $header);
                if ($idx !== false) {
                    $map[$field] = $idx;
                    break;
                }
            }
        }

        if ($map['code'] === false || $map['name'] === false) {
            return redirect()->back()->with('error', 'Kolom wajib (Kode Mapel, Nama Mapel) tidak ditemukan dalam file.');
        }

        foreach ($rows as $row) {
            $row = array_pad($row, max(array_filter($map)), null);

            $code = strtoupper(trim((string)($row[$map['code']] ?? '')));
            $name = trim((string)($row[$map['name']] ?? ''));
            if (empty($code) || empty($name)) {
                continue;
            }

            $category = $map['category'] !== false ? trim((string)($row[$map['category']] ?? '')) : 'Kelompok A';
            $order_number = $map['order_number'] !== false ? (int)($row[$map['order_number']] ?? 0) : null;
            $jurusan = $map['jurusan'] !== false ? trim((string)($row[$map['jurusan']] ?? '')) : null;
            $tampil_skl = $map['tampil_skl'] !== false ? in_array(strtolower(trim((string)($row[$map['tampil_skl']] ?? ''))), ['1', 'yes', 'ya', 'true', 'y']) : true;
            $tampil_transkip = $map['tampil_transkip'] !== false ? in_array(strtolower(trim((string)($row[$map['tampil_transkip']] ?? ''))), ['1', 'yes', 'ya', 'true', 'y']) : true;

            Subject::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'category' => $category,
                    'order_number' => $order_number ?: null,
                    'jurusan' => $jurusan ?: null,
                    'tampil_skl' => $tampil_skl,
                    'tampil_transkip' => $tampil_transkip,
                ]
            );
            $successCount++;
        }

        return redirect()->route('admin.subjects')->with('success', "Berhasil mengimpor {$successCount} mata pelajaran.");
    }

    public function downloadSubjectTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['No Urut', 'Kode Mapel', 'Nama Mapel', 'Kelompok Mapel', 'Jurusan', 'Tampilkan di SKL', 'Tampil di Transkip'];
        $sheet->fromArray([$headers], null, 'A1');

        $sheet->setCellValue('A2', 1);
        $sheet->setCellValue('B2', 'MAT');
        $sheet->setCellValue('C2', 'Matematika');
        $sheet->setCellValue('D2', 'Kelompok A');
        $sheet->setCellValue('E2', 'IPA');
        $sheet->setCellValue('F2', 'Ya');
        $sheet->setCellValue('G2', 'Ya');

        $sheet->setCellValue('A3', 2);
        $sheet->setCellValue('B3', 'SBK');
        $sheet->setCellValue('C3', 'Seni Budaya');
        $sheet->setCellValue('D3', 'Kelompok B');
        $sheet->setCellValue('E3', '');
        $sheet->setCellValue('F3', 'Ya');
        $sheet->setCellValue('G3', 'Tidak');

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="template_mapel.xlsx"',
        ]);
    }

    // --- Grade Management (Multi-Semester) ---

    public function grades(Request $request)
    {
        $semester = $request->input('semester', 'Semester 1');
        $validSemesters = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6', 'Nilai Ijazah'];
        if (!in_array($semester, $validSemesters)) {
            $semester = 'Semester 1';
        }

        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
            });
        }

        // Eager load grades with subjects for the selected semester
        $students = $query->with(['grades' => function ($q) use ($semester) {
            $q->where('semester', $semester);
        }])->orderBy('name')->paginate(20);

        $subjects = Subject::orderBy('order_number')->orderBy('code')->get();
        $schoolYear = Setting::get('school_year', date('Y') . '/' . (date('Y') + 1));

        return view('admin.grades.index', compact('students', 'subjects', 'semester', 'validSemesters', 'schoolYear'));
    }

    public function storeGrades(Request $request)
    {
        $semester = $request->input('semester', 'Semester 1');
        $validSemesters = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6', 'Nilai Ijazah'];
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
            'file_import' => 'required|file|mimes:xlsx,xls',
            'semester' => 'required'
        ]);

        $semester = $request->input('semester');
        $validSemesters = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6', 'Nilai Ijazah'];
        if (!in_array($semester, $validSemesters)) {
            return redirect()->back()->with('error', 'Semester tidak valid.');
        }

        $file = $request->file('file_import');
        $extension = strtolower($file->getClientOriginalExtension());
        $successCount = 0;

        $tmpPath = $file->getPathname();
        if (!file_exists($tmpPath) || !is_readable($tmpPath)) {
            return redirect()->back()->with('error', 'File upload tidak ditemukan atau tidak bisa dibaca.');
        }

        try {
            $reader = IOFactory::createReader(ucfirst($extension));
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($tmpPath);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (empty($rows) || count($rows) < 2) {
            return redirect()->back()->with('error', 'File Excel kosong atau tidak valid.');
        }

        // Auto-detect semester from Excel row 3
        if (isset($rows[2][0]) && strpos(strtolower(trim((string)$rows[2][0])), 'semester') !== false) {
            $excelSemesterVal = trim((string)($rows[2][1] ?? ''));
            if (in_array($excelSemesterVal, ['1', '2', '3', '4', '5', '6'])) {
                $semester = 'Semester ' . $excelSemesterVal;
            } elseif ($excelSemesterVal === '0' || strtolower($excelSemesterVal) === 'nilai ijazah') {
                $semester = 'Nilai Ijazah';
            }
        }

        // Auto-detect Graduation Year (Tahun Lulus) from Excel row 4
        $excelTahunLulus = null;
        if (isset($rows[3][0]) && strpos(strtolower(trim((string)$rows[3][0])), 'tahun lulus') !== false) {
            $excelTahunLulus = trim((string)($rows[3][1] ?? ''));
        }

        // Find header row (contains column names like No, NIS, NAMA SISWA)
        $headerRowIdx = null;
        $headerKeywords = ['no', 'nis', 'nama'];
        foreach ($rows as $idx => $row) {
            $firstCell = strtolower(trim((string)($row[0] ?? '')));
            foreach ($headerKeywords as $keyword) {
                if ($firstCell === $keyword || strpos($firstCell, $keyword) !== false) {
                    $headerRowIdx = $idx;
                    break 2;
                }
            }
        }

        if ($headerRowIdx === null) {
            return redirect()->back()->with('error', 'Baris header (No, NIS, NAMA SISWA) tidak ditemukan dalam file.');
        }

        $header = array_map(function($val) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x7F-\x9F\xEF\xBB\xBF]/', '', (string)$val)));
        }, $rows[$headerRowIdx]);

        $dataRows = array_slice($rows, $headerRowIdx + 1);

        $headerMap = [
            'no' => ['no', 'nomor', 'no.'],
            'nis' => ['nis', 'nisn', 'n i s', 'no induk'],
            'nama' => ['nama', 'nama siswa', 'nama_siswa', 'name', 'nama lengkap'],
        ];

        $colMap = [];
        foreach ($headerMap as $field => $aliases) {
            $colMap[$field] = false;
            foreach ($aliases as $alias) {
                $idx = array_search($alias, $header);
                if ($idx !== false) {
                    $colMap[$field] = $idx;
                    break;
                }
            }
        }

        if ($colMap['nis'] === false && $colMap['nama'] === false) {
            return redirect()->back()->with('error', 'Kolom identitas siswa (NIS atau NAMA SISWA) tidak ditemukan.');
        }

        $allSubjects = Subject::all()->keyBy(function($item) {
            return strtolower($item->code);
        });

        // Subject Aliases Mapping
        $subjectAliases = [
            'pai' => ['pai', 'paibp', 'pai bp', 'agama'],
            'ppkn' => ['ppkn', 'pkn', 'kewarganegaraan'],
            'ind' => ['ind', 'bind', 'b. indonesia', 'indonesia'],
            'mtk' => ['mtk', 'mtm', 'matematika', 'mat'],
            'ipa' => ['ipa', 'sains'],
            'ips' => ['ips', 'social'],
            'ing' => ['ing', 'bing', 'b. inggris', 'inggris'],
            'sbd' => ['sbd', 'sbud', 'seni budaya', 'seni'],
            'pjok' => ['pjok', 'penjas', 'olahraga'],
            'sun' => ['sun', 'bsd', 'sunda', 'b. sunda', 'bahasa sunda'],
            'prk' => ['prk', 'kk', 'prakarya', 'keterampilan'],
        ];

        $subjectColumns = [];
        foreach ($header as $index => $colName) {
            if ($index === $colMap['no'] || $index === $colMap['nis'] || $index === $colMap['nama']) {
                continue;
            }
            $cleanColName = strtolower(trim($colName));
            if (empty($cleanColName)) continue;

            $matchedSubject = null;
            // 1. Try exact code match
            if (isset($allSubjects[$cleanColName])) {
                $matchedSubject = $allSubjects[$cleanColName];
            } else {
                // 2. Try alias match
                foreach ($allSubjects as $dbCode => $subjectObj) {
                    $dbCodeLower = strtolower($dbCode);
                    if (isset($subjectAliases[$dbCodeLower]) && in_array($cleanColName, $subjectAliases[$dbCodeLower])) {
                        $matchedSubject = $subjectObj;
                        break;
                    }
                }
            }

            if ($matchedSubject) {
                $subjectColumns[$index] = $matchedSubject->id;
            }
        }

        foreach ($dataRows as $row) {
            $row = array_pad($row, max(array_filter($colMap)), null);

            $nis = $colMap['nis'] !== false ? trim((string)($row[$colMap['nis']] ?? '')) : '';
            $nama = $colMap['nama'] !== false ? trim((string)($row[$colMap['nama']] ?? '')) : '';

            if (empty($nis) && empty($nama)) {
                continue;
            }

            $student = null;
            if (!empty($nis)) {
                $student = Student::where('nisn', $nis)->orWhere('nis', $nis)->first();
            }
            if (!$student && !empty($nama)) {
                $student = Student::where('name', $nama)->first();
            }

            if (!$student) {
                continue;
            }

            // Update student's Graduation Year (Tahun Lulus) if present in Excel
            if ($excelTahunLulus && preg_match('/^\d{4}$/', $excelTahunLulus)) {
                $student->update(['tahun_lulus' => $excelTahunLulus]);
            }

            foreach ($subjectColumns as $index => $subjectId) {
                $scoreValue = trim((string)($row[$index] ?? ''));
                if ($scoreValue !== '' && $scoreValue !== null) {
                    // Replace comma with dot for Indonesian decimal format support
                    $cleanScore = floatval(str_replace(',', '.', $scoreValue));
                    Grade::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subjectId,
                            'semester' => $semester
                        ],
                        ['score' => $cleanScore]
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

        return redirect()->back()->with('success', "Berhasil mengimpor {$successCount} data nilai untuk {$semester}.");
    }

    public function downloadGradesTemplate(Request $request)
    {
        $semester = $request->input('semester', 'Semester 1');
        $schoolYear = Setting::get('school_year', date('Y') . '/' . (date('Y') + 1));
        
        // Clean school year value for B2 (e.g., 2025/2026 -> 20252026)
        $cleanSchoolYear = preg_replace('/\D/', '', $schoolYear);
        if (strlen($cleanSchoolYear) > 8) {
            $cleanSchoolYear = substr($cleanSchoolYear, 0, 8);
        }

        // Semester value for B3: number for numbered semesters, "Nilai Ijazah" for ijazah
        $cleanSemester = preg_match('/\d+/', $semester, $matches) ? $matches[0] : $semester;

        $subjects = Subject::orderBy('order_number')->orderBy('code')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Nilai');

        // Row 1: Title
        $sheet->setCellValue('A1', 'DATA NILAI SISWA');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Row 2: Tahun Pelajaran
        $sheet->setCellValue('A2', 'TAHUN PELAJARAN');
        $sheet->setCellValue('B2', $cleanSchoolYear);
        $sheet->setCellValue('D2', '<< ISI KODE TAHUN PELAJARAN TAHUN AWAL TAHUN AKHIR TANPA STRIP CONTOH (20202021)');
        
        // Row 3: Semester
        $sheet->setCellValue('A3', 'SEMESTER');
        $sheet->setCellValue('B3', $cleanSemester);
        $sheet->setCellValue('D3', '<< ISI SEMESTER DISINI (1/2/3/4/5/6), UNTUK KEPERLUAN SKL SAJA BISA DIISI 0 SAJA');

        // Row 4: Tahun Lulus
        $sheet->setCellValue('A4', 'TAHUN LULUS');
        $sheet->setCellValue('B4', date('Y'));
        $sheet->setCellValue('D4', '<< ISI TAHUN LULUS DISINI');

        // Metadata Labels Styling (Tidy Info-Card Style)
        $metaLabels = ['A2', 'A3', 'A4'];
        foreach ($metaLabels as $cell) {
            $sheet->getStyle($cell)->getFont()->setBold(true)->setSize(10);
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9'); // Premium light slate bg
        }
        
        $sheet->getStyle('B2:B4')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('B2:B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:B4')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CBD5E1'] // Modern slate border
                ]
            ]
        ]);

        // Row 6: Table Headers
        $headers = ['No', 'NIS', 'NAMA SISWA'];
        
        // Map database codes to user's beautiful aliases for header display
        $aliasMap = [
            'pai' => 'PAIBP',
            'ppkn' => 'PPKn',
            'ind' => 'BIND',
            'mtk' => 'MTM',
            'ing' => 'BING',
            'sbd' => 'SBUD',
            'sun' => 'BSD',
            'prk' => 'KK',
        ];

        foreach ($subjects as $subject) {
            $codeLower = strtolower($subject->code);
            $headers[] = isset($aliasMap[$codeLower]) ? $aliasMap[$codeLower] : strtoupper($subject->code);
        }

        $sheet->fromArray([$headers], null, 'A6');
        
        // Style Header Row 6
        $headerColEnd = chr(ord('A') + count($headers) - 1);

        // Merge Title and Metadata Explanations across the header width to prevent column D stretching
        $sheet->mergeCells('A1:' . $headerColEnd . '1');
        $sheet->mergeCells('D2:' . $headerColEnd . '2');
        $sheet->mergeCells('D3:' . $headerColEnd . '3');
        $sheet->mergeCells('D4:' . $headerColEnd . '4');
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C5D9F1'] // Professional light blue accent
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A6:' . $headerColEnd . '6')->applyFromArray($headerStyle);
        $sheet->getRowDimension(6)->setRowHeight(28);

        // Populate Student Data
        $students = Student::orderBy('name')->get();
        $rowNum = 7;

        // Load grades in bulk to avoid N+1 query issue
        $allGrades = Grade::where('semester', $semester)
            ->get()
            ->groupBy('student_id');

        foreach ($students as $index => $student) {
            $studentGrades = $allGrades->get($student->id, collect())->keyBy('subject_id');
            
            // Format NIS and name
            $nisVal = $student->nisn ?: $student->nis;
            
            $rowData = [$index + 1, $nisVal, $student->name];
            foreach ($subjects as $subject) {
                $grade = $studentGrades->get($subject->id);
                // Convert score decimals to commas for Indonesian format
                $score = $grade ? str_replace('.', ',', (string)$grade->score) : '';
                $rowData[] = $score;
            }
            
            // Write student row
            $sheet->fromArray([$rowData], null, "A{$rowNum}");

            // Explicitly format NIS as TEXT to preserve leading zeros
            $sheet->getCell("B{$rowNum}")->setValueExplicit($nisVal, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            
            // Align No and NIS to center/left, name to left
            $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("C{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

            // Center-align all subject grade values
            $sheet->getStyle("D{$rowNum}:" . $headerColEnd . $rowNum)
                ->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Add thin borders for data row
            $sheet->getStyle("A{$rowNum}:" . $headerColEnd . $rowNum)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D9D9D9']
                    ]
                ]
            ]);

            $rowNum++;
        }

        // Color the 'NAMA SISWA' header column (C6) with a soft yellow accent as requested
        $sheet->getStyle('C6')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFFF00'] // Soft Yellow
            ]
        ]);

        // Auto size columns nicely
        foreach (range('A', $headerColEnd) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        $filename = "template_nilai_" . strtolower(str_replace(' ', '_', $semester)) . ".xlsx";

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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
        $subjects = Subject::orderBy('order_number')->orderBy('code')->get();
        
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
        $subjects = Subject::orderBy('order_number')->orderBy('code')->get();
        
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
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
            });
        }

        $students = $query->with(['grades.subject'])->orderBy('name')->get();
        $subjects = Subject::orderBy('order_number')->orderBy('code')->get();
        
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
        $subjects = Subject::with(['grades' => function($q) use ($student) {
            $q->where('student_id', $student->id);
        }])->orderBy('order_number')->orderBy('code')->get();

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
            'nss' => Setting::get('nss', '202000012010'),
            'npsn' => Setting::get('npsn', '20233628'),
            'accreditation' => Setting::get('accreditation', 'B'),
            'skl_after_lulus_text' => Setting::get('skl_after_lulus_text', ''),
            'skl_before_ttd_text' => Setting::get('skl_before_ttd_text', ''),
            'skl_opening_text' => Setting::get('skl_opening_text', 'Yang bertanda tangan di bawah ini, Kepala Sekolah [NAMA_SEKOLAH] Kecamatan Banjaran Kabupaten Bandung, menerangkan bahwa:'),
            'skl_body_text' => Setting::get('skl_body_text', 'Berdasarkan Kriteria Kelulusan Peserta Didik yang diatur dalam kurikulum yang berlaku dan Rapat Pleno Dewan Guru [NAMA_SEKOLAH] tentang Kelulusan Siswa Kelas IX Tahun Pelajaran [TAHUN_PELAJARAN] pada tanggal [TANGGAL_PENGUMUMAN], dengan ini menyatakan bahwa siswa tersebut di atas:'),
            'skl_footer_text' => Setting::get('skl_footer_text', '* Surat Keterangan Lulus ini berlaku sementara sampai diterbitkannya Ijazah asli bagi peserta didik yang dinyatakan lulus, guna melengkapi syarat pendaftaran jenjang pendidikan selanjutnya.'),
        ];

        // Use physical path for logo and signature
        $logoSetting = $settings['skl_logo'] ?: $settings['school_logo'] ?: Setting::get('transcript_logo');
        $logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
        $signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

        // Parse dynamic letter number
        $number = rand(100, 300);
        $year = $announcementDate ? $announcementDate->format('Y') : date('Y');
        $letterNumber = str_replace(['[NUMBER]', '[YEAR]'], [$number, $year], $settings['skl_letter_number']);

        $pdf = Pdf::loadView('admin.transcripts.skl_pdf', compact('student', 'subjects', 'announcementDate', 'settings', 'logo_path', 'signature_path', 'letterNumber'));

        $filename = "skl_" . strtolower(str_replace(' ', '_', $student->name)) . ".pdf";
        return $pdf->download($filename);
    }

    public function previewSklPdf(Student $student)
    {
        $student->load(['grades.subject']);
        $subjects = Subject::with(['grades' => function($q) use ($student) {
            $q->where('student_id', $student->id);
        }])->orderBy('order_number')->orderBy('code')->get();

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
            'nss' => Setting::get('nss', '202000012010'),
            'npsn' => Setting::get('npsn', '20233628'),
            'accreditation' => Setting::get('accreditation', 'B'),
            'skl_after_lulus_text' => Setting::get('skl_after_lulus_text', ''),
            'skl_before_ttd_text' => Setting::get('skl_before_ttd_text', ''),
            'skl_opening_text' => Setting::get('skl_opening_text', 'Yang bertanda tangan di bawah ini, Kepala Sekolah [NAMA_SEKOLAH] Kecamatan Banjaran Kabupaten Bandung, menerangkan bahwa:'),
            'skl_body_text' => Setting::get('skl_body_text', 'Berdasarkan Kriteria Kelulusan Peserta Didik yang diatur dalam kurikulum yang berlaku dan Rapat Pleno Dewan Guru [NAMA_SEKOLAH] tentang Kelulusan Siswa Kelas IX Tahun Pelajaran [TAHUN_PELAJARAN] pada tanggal [TANGGAL_PENGUMUMAN], dengan ini menyatakan bahwa siswa tersebut di atas:'),
            'skl_footer_text' => Setting::get('skl_footer_text', '* Surat Keterangan Lulus ini berlaku sementara sampai diterbitkannya Ijazah asli bagi peserta didik yang dinyatakan lulus, guna melengkapi syarat pendaftaran jenjang pendidikan selanjutnya.'),
        ];

        // Use physical path for logo and signature
        $logoSetting = $settings['skl_logo'] ?: $settings['school_logo'] ?: Setting::get('transcript_logo');
        $logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
        $signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

        // Parse dynamic letter number
        $number = rand(100, 300);
        $year = $announcementDate ? $announcementDate->format('Y') : date('Y');
        $letterNumber = str_replace(['[NUMBER]', '[YEAR]'], [$number, $year], $settings['skl_letter_number']);

        $pdf = Pdf::loadView('admin.transcripts.skl_pdf', compact('student', 'subjects', 'announcementDate', 'settings', 'logo_path', 'signature_path', 'letterNumber'));

        return $pdf->stream();
    }

    public function downloadBulkSklPdf(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('class', 'like', "%{$search}%");
            });
        }

        $students = $query->with(['grades.subject'])->orderBy('name')->get();
        $subjects = Subject::orderBy('order_number')->orderBy('code')->get();

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
            'nss' => Setting::get('nss', '202000012010'),
            'npsn' => Setting::get('npsn', '20233628'),
            'accreditation' => Setting::get('accreditation', 'B'),
            'skl_after_lulus_text' => Setting::get('skl_after_lulus_text', ''),
            'skl_before_ttd_text' => Setting::get('skl_before_ttd_text', ''),
            'skl_opening_text' => Setting::get('skl_opening_text', 'Yang bertanda tangan di bawah ini, Kepala Sekolah [NAMA_SEKOLAH] Kecamatan Banjaran Kabupaten Bandung, menerangkan bahwa:'),
            'skl_body_text' => Setting::get('skl_body_text', 'Berdasarkan Kriteria Kelulusan Peserta Didik yang diatur dalam kurikulum yang berlaku dan Rapat Pleno Dewan Guru [NAMA_SEKOLAH] tentang Kelulusan Siswa Kelas IX Tahun Pelajaran [TAHUN_PELAJARAN] pada tanggal [TANGGAL_PENGUMUMAN], dengan ini menyatakan bahwa siswa tersebut di atas:'),
            'skl_footer_text' => Setting::get('skl_footer_text', '* Surat Keterangan Lulus ini berlaku sementara sampai diterbitkannya Ijazah asli bagi peserta didik yang dinyatakan lulus, guna melengkapi syarat pendaftaran jenjang pendidikan selanjutnya.'),
        ];

        $logoSetting = $settings['skl_logo'] ?: $settings['school_logo'] ?: Setting::get('transcript_logo');
        $logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
        $signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

        $number = rand(100, 300);
        $year = $announcementDate ? $announcementDate->format('Y') : date('Y');
        $letterNumber = str_replace(['[NUMBER]', '[YEAR]'], [$number, $year], $settings['skl_letter_number']);

        $schoolYear = Setting::get('school_year', date('Y') . '/' . (date('Y') + 1));

        $pdf = Pdf::loadView('admin.transcripts.skl_pdf', compact('students', 'subjects', 'announcementDate', 'settings', 'logo_path', 'signature_path', 'letterNumber', 'schoolYear'));

        $filename = "skl_masal_" . date('Ymd_His') . ".pdf";
        return $pdf->download($filename);
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
