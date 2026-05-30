<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    /**
     * Get transcript grade for a student
     * GET /admin/students/{id}/transcript-grade
     */
    public function getTranscriptGrade(Student $student): JsonResponse
    {
        $transcriptGrade = $student->calculateTranscriptGrade();
        
        return response()->json([
            'success' => true,
            'student_id' => $student->id,
            'student_name' => $student->name,
            'transcript_grade' => $transcriptGrade,
            'message' => 'Nilai ijazah berhasil dihitung'
        ]);
    }

    /**
     * Update transcript grade for a student
     * PUT /admin/students/{id}/transcript-grade
     */
    public function updateTranscriptGrade(Student $student): JsonResponse
    {
        $transcriptGrade = $student->calculateTranscriptGrade();
        $student->update(['transcript_grade' => $transcriptGrade]);
        
        return response()->json([
            'success' => true,
            'student_id' => $student->id,
            'student_name' => $student->name,
            'transcript_grade' => $transcriptGrade,
            'message' => 'Nilai ijazah berhasil diperbarui'
        ]);
    }

    /**
     * Get all students with their transcript grades
     * GET /admin/students/transcript-grades/list
     */
    public function listTranscriptGrades(): JsonResponse
    {
        $students = Student::with('grades')
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'exam_number' => $student->exam_number,
                    'name' => $student->name,
                    'class' => $student->class,
                    'transcript_grade' => $student->calculateTranscriptGrade()
                ];
            });

        return response()->json([
            'success' => true,
            'total' => $students->count(),
            'data' => $students,
            'message' => 'Daftar nilai ijazah berhasil diambil'
        ]);
    }

    /**
     * Update all students' transcript grades
     * POST /admin/students/transcript-grades/update-all
     */
    public function updateAllTranscriptGrades(): JsonResponse
    {
        $updated = 0;
        $failed = 0;

        foreach (Student::all() as $student) {
            try {
                $transcriptGrade = $student->calculateTranscriptGrade();
                $student->update(['transcript_grade' => $transcriptGrade]);
                $updated++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'updated' => $updated,
            'failed' => $failed,
            'message' => "Nilai ijazah berhasil diperbarui untuk {$updated} siswa"
        ]);
    }
}
