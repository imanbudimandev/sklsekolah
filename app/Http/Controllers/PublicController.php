<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        $announcementDateStr = Setting::get('announcement_date');
        $announcementDate = $announcementDateStr ? Carbon::parse($announcementDateStr) : null;
        $isAnnounced = $announcementDate ? now()->greaterThanOrEqualTo($announcementDate) : true;

        $student = null;
        $searchQuery = $request->input('search');
        $error = null;

        if ($searchQuery) {
            if (!$isAnnounced) {
                $error = 'Pengumuman kelulusan belum dibuka.';
            } else {
                $student = Student::where('nisn', $searchQuery)
                    ->orWhere('nis', $searchQuery)
                    ->with(['grades.subject'])
                    ->first();

                if (!$student) {
                    $error = 'Data siswa tidak ditemukan. Silakan periksa kembali NISN atau NIS Anda.';
                }
            }
        }

        $settings = [
            'school_name' => Setting::get('school_name', 'SMP Nurul Ihsan Banjaran'),
            'school_address' => Setting::get('school_address', ''),
            'principal_name' => Setting::get('principal_name', ''),
            'principal_nip' => Setting::get('principal_nip', ''),
            'school_logo' => Setting::get('school_logo'),
            'principal_signature' => Setting::get('principal_signature'),
        ];

        $subjects = Subject::orderBy('code')->get();

        return view('public.index', compact('isAnnounced', 'announcementDate', 'student', 'error', 'searchQuery', 'settings', 'subjects'));
    }

    public function downloadSklPdf(Student $student)
    {
        $announcementDateStr = Setting::get('announcement_date');
        $announcementDate = $announcementDateStr ? Carbon::parse($announcementDateStr) : null;

        $student->load(['grades.subject']);
        $subjects = Subject::orderBy('order_number')->orderBy('code')->get();

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
            'skl_opening_text' => Setting::get('skl_opening_text', 'Yang bertanda tangan di bawah ini, Kepala Sekolah [NAMA_SEKOLAH] Kecamatan Banjaran Kabupaten Bandung, menerangkan bahwa:'),
            'skl_body_text' => Setting::get('skl_body_text', 'Berdasarkan Kriteria Kelulusan Peserta Didik yang diatur dalam kurikulum yang berlaku dan Rapat Pleno Dewan Guru [NAMA_SEKOLAH] tentang Kelulusan Siswa Kelas IX Tahun Pelajaran [TAHUN_PELAJARAN] pada tanggal [TANGGAL_PENGUMUMAN], dengan ini menyatakan bahwa siswa tersebut di atas:'),
            'skl_footer_text' => Setting::get('skl_footer_text', '* Surat Keterangan Lulus ini berlaku sementara sampai diterbitkannya Ijazah asli bagi peserta didik yang dinyatakan lulus, guna melengkapi syarat pendaftaran jenjang pendidikan selanjutnya.'),
            'skl_after_lulus_text' => Setting::get('skl_after_lulus_text', ''),
            'skl_before_ttd_text' => Setting::get('skl_before_ttd_text', ''),
            'nss' => Setting::get('nss', '202000012010'),
            'npsn' => Setting::get('npsn', '20233628'),
            'accreditation' => Setting::get('accreditation', 'B'),
        ];

        $number = rand(100, 300);
        $year = $announcementDate ? $announcementDate->format('Y') : date('Y');
        $letterNumber = str_replace(['[NUMBER]', '[YEAR]'], [$number, $year], $settings['skl_letter_number']);

        $logoSetting = $settings['skl_logo'] ?: $settings['school_logo'];
        $logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
        $signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

        $pdf = Pdf::loadView('admin.transcripts.skl_pdf', compact('student', 'subjects', 'announcementDate', 'settings', 'logo_path', 'signature_path', 'letterNumber'));

        $filename = "skl_" . strtolower(str_replace(' ', '_', $student->name)) . ".pdf";
        return $pdf->download($filename);
    }

    public function downloadTranscriptPdf(Student $student)
    {
        $announcementDateStr = Setting::get('announcement_date');
        $announcementDate = $announcementDateStr ? Carbon::parse($announcementDateStr) : null;

        $student->load(['grades.subject']);
        $subjects = Subject::orderBy('order_number')->orderBy('code')->get();

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

        $logoSetting = $settings['transcript_logo'] ?: $settings['school_logo'];
        $logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
        $signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

        $students = collect([$student]);

        $pdf = Pdf::loadView('admin.transcripts.pdf', compact('students', 'subjects', 'announcementDate', 'settings', 'logo_path', 'signature_path'));

        $filename = "transkrip_" . strtolower(str_replace(' ', '_', $student->name)) . ".pdf";
        return $pdf->download($filename);
    }
}
