<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranscriptGradeTest extends TestCase
{
    use RefreshDatabase;
    
    protected Student $student;
    protected Subject $subject1;
    protected Subject $subject2;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat student
        $this->student = Student::factory()->create();

        // Buat subjects
        $this->subject1 = Subject::create(['name' => 'Matematika', 'code' => 'MTK']);
        $this->subject2 = Subject::create(['name' => 'Bahasa Indonesia', 'code' => 'BI']);
    }

    public function test_calculate_transcript_grade_with_complete_data()
    {
        // Setup data untuk Subject 1 (Matematika)
        // Semester 1-6 grades: 85, 86, 87, 88, 89, 90
        // Rata-rata: (85+86+87+88+89+90)/6 = 87.5
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 1', 'score' => 85]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 2', 'score' => 86]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 3', 'score' => 87]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 4', 'score' => 88]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 5', 'score' => 89]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 6', 'score' => 90]);
        // Ujian Sekolah: 92
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Ujian Sekolah', 'score' => 92]);

        // Setup data untuk Subject 2 (Bahasa Indonesia)
        // Semester 1-6 grades: 80, 81, 82, 83, 84, 85
        // Rata-rata: (80+81+82+83+84+85)/6 = 82.5
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject2->id, 'semester' => 'Semester 1', 'score' => 80]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject2->id, 'semester' => 'Semester 2', 'score' => 81]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject2->id, 'semester' => 'Semester 3', 'score' => 82]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject2->id, 'semester' => 'Semester 4', 'score' => 83]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject2->id, 'semester' => 'Semester 5', 'score' => 84]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject2->id, 'semester' => 'Semester 6', 'score' => 85]);
        // Ujian Sekolah: 88
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject2->id, 'semester' => 'Ujian Sekolah', 'score' => 88]);

        // Kalkulasi expected result
        // Matematika: (87.5 * 0.60) + (92 * 0.40) = 52.5 + 36.8 = 89.3
        // Bahasa Indonesia: (82.5 * 0.60) + (88 * 0.40) = 49.5 + 35.2 = 84.7
        // Rata-rata: (89.3 + 84.7) / 2 = 87.0
        
        $transcriptGrade = $this->student->calculateTranscriptGrade();
        
        $this->assertEquals(87.0, $transcriptGrade);
    }

    public function test_calculate_transcript_grade_single_subject()
    {
        // Hanya 1 subject dengan data lengkap
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 1', 'score' => 90]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 2', 'score' => 90]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 3', 'score' => 90]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 4', 'score' => 90]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 5', 'score' => 90]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 6', 'score' => 90]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Ujian Sekolah', 'score' => 90]);

        // Expected: (90 * 0.60) + (90 * 0.40) = 54 + 36 = 90.0
        $transcriptGrade = $this->student->calculateTranscriptGrade();
        
        $this->assertEquals(90.0, $transcriptGrade);
    }

    public function test_calculate_transcript_grade_with_missing_exam_score()
    {
        // Setup data tanpa Ujian Sekolah - seharusnya tidak terhitung
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 1', 'score' => 85]);
        Grade::create(['student_id' => $this->student->id, 'subject_id' => $this->subject1->id, 'semester' => 'Semester 2', 'score' => 85]);

        $transcriptGrade = $this->student->calculateTranscriptGrade();
        
        // Karena tidak ada Ujian Sekolah, return 0
        $this->assertEquals(0, $transcriptGrade);
    }

    public function test_calculate_transcript_grade_empty_grades()
    {
        // Student tanpa grades
        $transcriptGrade = $this->student->calculateTranscriptGrade();
        
        $this->assertEquals(0, $transcriptGrade);
    }
}
