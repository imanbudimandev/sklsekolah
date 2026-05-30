<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin Account
        User::updateOrCreate(
            ['email' => 'iman@appsakola.id'],
            [
                'name' => 'Iman Budiman',
                'password' => Hash::make('Namiiman123'),
            ]
        );

        // 2. Create Default Settings
        $defaultSettings = [
            'school_name' => 'SMP Nurul Ihsan Banjaran',
            'school_address' => 'Jl. Raya Banjaran No. 123, Banjaran, Bandung, Jawa Barat',
            'principal_name' => 'Iman Budiman, S.Pd.',
            'principal_nip' => '197608242005011002',
            'announcement_date' => now()->addDays(2)->format('Y-m-d H:i:s'), // Default: 2 days from now
            'school_logo' => null,
            'principal_signature' => null,
        ];

        foreach ($defaultSettings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // 3. Create Default Subjects
        $subjectsData = [
            ['code' => 'PAI', 'name' => 'Pendidikan Agama dan Budi Pekerti', 'category' => 'Kelompok A'],
            ['code' => 'PPKN', 'name' => 'Pendidikan Pancasila dan Kewarganegaraan', 'category' => 'Kelompok A'],
            ['code' => 'IND', 'name' => 'Bahasa Indonesia', 'category' => 'Kelompok A'],
            ['code' => 'MTK', 'name' => 'Matematika', 'category' => 'Kelompok A'],
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam', 'category' => 'Kelompok A'],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial', 'category' => 'Kelompok A'],
            ['code' => 'ING', 'name' => 'Bahasa Inggris', 'category' => 'Kelompok A'],
            ['code' => 'SBD', 'name' => 'Seni Budaya', 'category' => 'Kelompok B'],
            ['code' => 'PJOK', 'name' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'category' => 'Kelompok B'],
            ['code' => 'PRK', 'name' => 'Prakarya', 'category' => 'Kelompok B'],
            ['code' => 'SUN', 'name' => 'Bahasa Sunda (Muatan Lokal)', 'category' => 'Kelompok B'],
        ];

        $subjects = [];
        foreach ($subjectsData as $data) {
            $subjects[] = Subject::updateOrCreate(['code' => $data['code']], $data);
        }

        // 4. Create Mock Students & Grades
        $studentsData = [
            [
                'exam_number' => '02-001-001-1',
                'nisn' => '1234567890',
                'name' => 'Ahmad Fauzi',
                'birth_place' => 'Bandung',
                'birth_date' => '2011-05-12',
                'class' => 'IX-A',
                'status' => 'LULUS'
            ],
            [
                'exam_number' => '02-001-002-2',
                'nisn' => '0987654321',
                'name' => 'Budi Setiawan',
                'birth_place' => 'Bandung',
                'birth_date' => '2011-08-22',
                'class' => 'IX-B',
                'status' => 'LULUS'
            ],
            [
                'exam_number' => '02-001-003-3',
                'nisn' => '5556667770',
                'name' => 'Citra Lestari',
                'birth_place' => 'Jakarta',
                'birth_date' => '2011-12-05',
                'class' => 'IX-A',
                'status' => 'TIDAK LULUS'
            ]
        ];

        foreach ($studentsData as $studentInfo) {
            $student = Student::updateOrCreate(
                ['exam_number' => $studentInfo['exam_number']],
                $studentInfo
            );

            // Assign grades across multiple semesters
            $semesters = ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Ujian Sekolah'];
            foreach ($subjects as $subject) {
                foreach ($semesters as $semester) {
                    // If student is "TIDAK LULUS", give them low scores in some subjects
                    if ($student->status === 'TIDAK LULUS' && in_array($subject->code, ['MTK', 'IPA'])) {
                        $score = rand(40, 58);
                    } else {
                        $score = rand(75, 96);
                    }

                    Grade::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'semester' => $semester
                        ],
                        ['score' => $score]
                    );
                }
            }
        }
    }
}
