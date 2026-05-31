<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    protected function getStudent()
    {
        $studentId = session('student_id');
        if (!$studentId) {
            abort(redirect()->route('public.index'));
        }
        return Student::with('grades.subject')->findOrFail($studentId);
    }

    protected function getSettings()
    {
        return [
            'school_name' => Setting::get('school_name', 'SMP Nurul Ihsan Banjaran'),
            'school_logo' => Setting::get('school_logo'),
        ];
    }

    public function showLogin()
    {
        if (session()->has('student_id')) {
            return redirect()->route('student.dashboard');
        }

        $announcementDateStr = Setting::get('announcement_date');
        $announcementDate = $announcementDateStr ? Carbon::parse($announcementDateStr) : null;
        $isAnnounced = $announcementDate ? now()->greaterThanOrEqualTo($announcementDate) : true;

        $settings = $this->getSettings();

        if (!$isAnnounced && $announcementDate) {
            // Increment visitor counter on countdown page
            if (!session()->has('public_visited')) {
                session()->put('public_visited', true);
                $visitorCount = (int) Setting::get('visitor_count', 0);
                $visitorCount++;
                Setting::set('visitor_count', $visitorCount);
            } else {
                $visitorCount = (int) Setting::get('visitor_count', 0);
            }

            $student = null;
            $error = null;
            $searchQuery = '';
            $subjects = Subject::orderBy('code')->get();

            return view('public.index', compact('isAnnounced', 'announcementDate', 'student', 'error', 'searchQuery', 'settings', 'subjects', 'visitorCount'));
        }

        return view('student.login', compact('settings'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'nis' => 'required',
        ]);

        $announcementDateStr = Setting::get('announcement_date');
        $announcementDate = $announcementDateStr ? Carbon::parse($announcementDateStr) : null;
        $isAnnounced = $announcementDate ? now()->greaterThanOrEqualTo($announcementDate) : true;

        if (!$isAnnounced && $announcementDate) {
            return back()->withErrors([
                'nis' => 'Pengumuman kelulusan belum dibuka.',
            ])->onlyInput('nis');
        }

        $student = Student::where('nis', $request->nis)
            ->orWhere('nisn', $request->nis)
            ->first();

        if (!$student) {
            return back()->withErrors([
                'nis' => 'NIS/NISN tidak ditemukan.',
            ])->onlyInput('nis');
        }

        $student->update(['last_login_at' => now()]);
        session(['student_id' => $student->id]);
        return redirect()->intended(route('student.dashboard'));
    }

    public function dashboard()
    {
        $studentId = session('student_id');
        if (!$studentId) {
            return redirect()->route('public.index');
        }

        $student = Student::with('grades.subject')->findOrFail($studentId);
        $subjects = Subject::all();
        $settings = $this->getSettings();

        $semesterNames = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6'];
        $semesterData = [];
        foreach ($semesterNames as $sem) {
            $grades = $student->grades->where('semester', $sem);
            if ($grades->isNotEmpty()) {
                $semesterData[] = [
                    'name' => $sem,
                    'avg' => round($grades->avg('score'), 2),
                    'count' => $grades->count(),
                ];
            }
        }

        $ujianSekolah = $student->grades->where('semester', 'Ujian Sekolah');
        $nilaiIjazah = $student->grades->where('semester', 'Nilai Ijazah');

        return view('student.dashboard', compact(
            'student',
            'subjects',
            'settings',
            'semesterData',
            'ujianSekolah',
            'nilaiIjazah'
        ));
    }

    public function profile()
    {
        $student = $this->getStudent();
        $settings = $this->getSettings();
        return view('student.profile', compact('student', 'settings'));
    }

    public function documents()
    {
        $student = $this->getStudent();
        $settings = $this->getSettings();
        return view('student.documents', compact('student', 'settings'));
    }

    public function previewSkl()
    {
        $student = $this->getStudent();
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
            'skl_opening_text' => Setting::get('skl_opening_text', ''),
            'skl_body_text' => Setting::get('skl_body_text', ''),
            'skl_footer_text' => Setting::get('skl_footer_text', ''),
        ];

        $logoSetting = $settings['skl_logo'] ?: $settings['school_logo'];
        $logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
        $signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

        $number = rand(100, 300);
        $year = $announcementDate ? $announcementDate->format('Y') : date('Y');
        $letterNumber = str_replace(['[NUMBER]', '[YEAR]'], [$number, $year], $settings['skl_letter_number']);

        $pdf = Pdf::loadView('admin.transcripts.skl_pdf', compact('student', 'subjects', 'announcementDate', 'settings', 'logo_path', 'signature_path', 'letterNumber'));
        return $pdf->stream();
    }

    public function previewTranscript()
    {
        $student = $this->getStudent();
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
            'transcript_header' => Setting::get('transcript_header', ''),
            'transcript_footer' => Setting::get('transcript_footer', ''),
            'transcript_letter_number' => Setting::get('transcript_letter_number', '421.3/[NUMBER]/SMP.NI/[YEAR]'),
            'transcript_place' => Setting::get('transcript_place', 'Subang'),
            'transcript_date_format' => Setting::get('transcript_date_format', 'd F Y'),
            'transcript_signature_text' => Setting::get('transcript_signature_text', ''),
        ];

        $logoSetting = $settings['transcript_logo'] ?: $settings['school_logo'];
        $logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
        $signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

        $students = collect([$student]);
        $pdf = Pdf::loadView('admin.transcripts.pdf', compact('students', 'subjects', 'announcementDate', 'settings', 'logo_path', 'signature_path'));
        return $pdf->stream();
    }

    public function logout(Request $request)
    {
        session()->forget('student_id');
        return redirect()->route('public.index');
    }
}
