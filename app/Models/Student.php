<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'exam_number',
        'nis',
        'nisn',
        'name',
        'birth_place',
        'birth_date',
        'class',
        'status',
        'photo',
        'transcript_grade',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'grades')
                    ->withPivot('score')
                    ->withTimestamps();
    }

    public function getAverageScoreAttribute()
    {
        $grades = $this->grades;
        if ($grades->isEmpty()) {
            return 0;
        }
        $grouped = $grades->groupBy('subject_id');
        $totalFinalGrades = 0;
        $count = 0;
        foreach ($grouped as $subjectId => $subjectGrades) {
            $semesterGrades = $subjectGrades->filter(function($grade) {
                return in_array($grade->semester, ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5']);
            });
            $ujianSekolahGrade = $subjectGrades->where('semester', 'Ujian Sekolah')->first();
            
            if ($semesterGrades->isNotEmpty() && $ujianSekolahGrade) {
                $avgSemesters = $semesterGrades->avg('score');
                $scoreUjian = $ujianSekolahGrade->score;
                $finalGrade = ($avgSemesters * 0.60) + ($scoreUjian * 0.40);
            } else {
                $finalGrade = $subjectGrades->avg('score');
            }
            $totalFinalGrades += $finalGrade;
            $count++;
        }
        return $count > 0 ? round($totalFinalGrades / $count, 2) : 0;
    }

    public function calculateFinalGradeForSubject($subjectId)
    {
        $grades = $this->grades->where('subject_id', $subjectId);
        if ($grades->isEmpty()) {
            return null;
        }
        
        $semesterGrades = $grades->filter(function($grade) {
            return in_array($grade->semester, ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5']);
        });
        
        $ujianSekolahGrade = $grades->where('semester', 'Ujian Sekolah')->first();
        
        if ($semesterGrades->isNotEmpty() && $ujianSekolahGrade) {
            $avgSemesters = $semesterGrades->avg('score');
            $scoreUjian = $ujianSekolahGrade->score;
            return round(($avgSemesters * 0.60) + ($scoreUjian * 0.40), 2);
        }
        
        return round($grades->avg('score'), 2);
    }

    public function getSemesterAverage($semesterName)
    {
        $grades = $this->grades->where('semester', $semesterName);
        if ($grades->isEmpty()) {
            return null;
        }
        return round($grades->avg('score'), 2);
    }

    /**
     * Calculate Transcript Grade (Nilai Ijazah)
     * Formula: Nilai Akhir = (0.60 × Rata-Rata Rapor) + (0.40 × Nilai Ujian Sekolah)
     * 
     * Rata-Rata Rapor: Average of all subject grades from semesters 1-6
     * Nilai Ujian Sekolah: School exam grade for each subject
     */
    public function calculateTranscriptGrade()
    {
        $grades = $this->grades;
        if ($grades->isEmpty()) {
            return 0;
        }

        $grouped = $grades->groupBy('subject_id');
        $totalTranscriptGrades = 0;
        $count = 0;

        foreach ($grouped as $subjectId => $subjectGrades) {
            // Get all semester grades (1-6)
            $semesterGrades = $subjectGrades->filter(function($grade) {
                return in_array($grade->semester, ['Semester 1', 'Semester 2', 'Semester 3', 'Semester 4', 'Semester 5', 'Semester 6']);
            });

            // Get school exam grade
            $ujianSekolahGrade = $subjectGrades->where('semester', 'Ujian Sekolah')->first();

            // Only calculate if both semester grades and school exam exist
            if ($semesterGrades->isNotEmpty() && $ujianSekolahGrade) {
                $rataRataRapor = $semesterGrades->avg('score');
                $nilaiUjianSekolah = $ujianSekolahGrade->score;
                
                // Apply formula: (0.60 × Rata-Rata Rapor) + (0.40 × Nilai Ujian Sekolah)
                $nilaiIjazah = ($rataRataRapor * 0.60) + ($nilaiUjianSekolah * 0.40);
                $totalTranscriptGrades += $nilaiIjazah;
                $count++;
            }
        }

        // Return overall average with 2 decimal places rounding
        return $count > 0 ? round($totalTranscriptGrades / $count, 2) : 0;
    }
}
