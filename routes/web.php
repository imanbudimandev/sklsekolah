<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;

// Public Routes
Route::get('/', [StudentController::class, 'showLogin'])->name('public.index');
Route::get('/skl/{student}/pdf', [PublicController::class, 'downloadSklPdf'])->name('public.skl.pdf');
Route::get('/transkrip/{student}/pdf', [PublicController::class, 'downloadTranscriptPdf'])->name('public.transcript.pdf');

// Student Auth Routes
Route::post('/login', [StudentController::class, 'login'])->name('student.login');

// Student Dashboard (Protected by session)
Route::middleware(['web'])->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/profile', [StudentController::class, 'profile'])->name('student.profile');
    Route::get('/documents', [StudentController::class, 'documents'])->name('student.documents');
    Route::get('/documents/skl-preview', [StudentController::class, 'previewSkl'])->name('student.preview.skl');
    Route::get('/documents/transcript-preview', [StudentController::class, 'previewTranscript'])->name('student.preview.transcript');
    Route::post('/logout', [StudentController::class, 'logout'])->name('student.logout');
});

// Admin Auth Routes
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Admin Dashboard Routes (Protected)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Students
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::post('/students', [AdminController::class, 'storeStudent'])->name('students.store');
    Route::put('/students/{student}', [AdminController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{student}', [AdminController::class, 'destroyStudent'])->name('students.destroy');
    Route::post('/students/{student}/photo', [AdminController::class, 'uploadStudentPhoto'])->name('students.photo');
    Route::post('/students/import', [AdminController::class, 'importStudentsExcel'])->name('students.import');
    Route::post('/students/import-photos', [AdminController::class, 'importStudentPhotosZip'])->name('students.import_photos');
    Route::get('/students/export', [AdminController::class, 'exportStudentsExcel'])->name('students.export');
    Route::get('/students/pdf', [AdminController::class, 'exportStudentsPdf'])->name('students.pdf');
    Route::get('/students/template', [AdminController::class, 'downloadStudentTemplate'])->name('students.template');

    // Subjects
    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects');
    Route::post('/subjects', [AdminController::class, 'storeSubject'])->name('subjects.store');
    Route::put('/subjects/{subject}', [AdminController::class, 'updateSubject'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [AdminController::class, 'destroySubject'])->name('subjects.destroy');
    Route::post('/subjects/import', [AdminController::class, 'importSubjectsCsv'])->name('subjects.import');
    Route::get('/subjects/template', [AdminController::class, 'downloadSubjectTemplate'])->name('subjects.template');

    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

    // Grades
    Route::get('/grades', [AdminController::class, 'grades'])->name('grades');
    Route::post('/grades', [AdminController::class, 'storeGrades'])->name('grades.store');
    Route::post('/grades/import', [AdminController::class, 'importGradesCsv'])->name('grades.import');
    Route::get('/grades/template', [AdminController::class, 'downloadGradesTemplate'])->name('grades.template');

    // Transcript Grades (Nilai Ijazah)
    Route::get('/students/{student}/transcript-grade', [AdminStudentController::class, 'getTranscriptGrade'])->name('students.transcript_grade');
    Route::put('/students/{student}/transcript-grade', [AdminStudentController::class, 'updateTranscriptGrade'])->name('students.update_transcript_grade');
    Route::get('/students/transcript-grades/list', [AdminStudentController::class, 'listTranscriptGrades'])->name('students.list_transcript_grades');
    Route::post('/students/transcript-grades/update-all', [AdminStudentController::class, 'updateAllTranscriptGrades'])->name('students.update_all_transcript_grades');

    // Transkrip Settings
    Route::get('/transcripts/settings', [AdminController::class, 'transcriptSettings'])->name('transcripts.settings');
    Route::post('/transcripts/settings', [AdminController::class, 'updateTranscriptSettings'])->name('transcripts.update_settings');

    // SKL Settings
    Route::get('/skl/settings', [AdminController::class, 'sklSettings'])->name('skl.settings');
    Route::post('/skl/settings', [AdminController::class, 'updateSklSettings'])->name('skl.update_settings');

    // Transcripts
    Route::get('/transcripts', [AdminController::class, 'transcripts'])->name('transcripts');
    Route::get('/transcripts/{student}/preview', [AdminController::class, 'previewTranscriptPdf'])->name('transcripts.preview');
    Route::get('/transcripts/{student}/pdf', [AdminController::class, 'downloadTranscriptPdf'])->name('transcripts.pdf');
    Route::get('/transcripts/pdf/bulk', [AdminController::class, 'downloadBulkTranscriptsPdf'])->name('transcripts.bulk_pdf');
    Route::get('/transcripts/{student}/skl/pdf', [AdminController::class, 'downloadSklPdf'])->name('transcripts.skl.pdf');
    Route::get('/transcripts/{student}/skl/preview', [AdminController::class, 'previewSklPdf'])->name('transcripts.skl.preview');
    Route::get('/transcripts/skl/pdf/bulk', [AdminController::class, 'downloadBulkSklPdf'])->name('transcripts.skl.bulk_pdf');

    // Database Tools
    Route::get('/tools', [AdminController::class, 'tools'])->name('tools');
    Route::post('/tools/backup', [AdminController::class, 'backupDatabase'])->name('tools.backup');
    Route::get('/tools/backup/{filename}/download', [AdminController::class, 'downloadBackup'])->name('tools.download');
    Route::post('/tools/restore', [AdminController::class, 'restoreDatabase'])->name('tools.restore');
    Route::delete('/tools/backup/{filename}', [AdminController::class, 'deleteBackup'])->name('tools.delete');
    Route::post('/tools/clean-data', [AdminController::class, 'cleanData'])->name('tools.clean');
});
