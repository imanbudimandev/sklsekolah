<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
                    ->orWhere('exam_number', $searchQuery)
                    ->with(['subjects', 'grades'])
                    ->first();

                if (!$student) {
                    $error = 'Data siswa tidak ditemukan. Silakan periksa kembali NISN atau Nomor Peserta Ujian Anda.';
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

        return view('public.index', compact('isAnnounced', 'announcementDate', 'student', 'error', 'searchQuery', 'settings'));
    }
}
