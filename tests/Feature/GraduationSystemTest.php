<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GraduationSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup base settings
        Setting::set('school_name', 'SMP Test School');
        Setting::set('announcement_date', now()->subHour()->format('Y-m-d H:i:s')); // past, so active
    }

    public function test_public_homepage_renders_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SMP Test School');
    }

    public function test_admin_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSee('Admin Portal');
    }

    public function test_unauthenticated_admin_dashboard_redirects(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_view_dashboard(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    public function test_student_lookup_returns_correct_result(): void
    {
        // Create a subject
        $subject = Subject::create([
            'code' => 'MTK',
            'name' => 'Matematika',
            'category' => 'Kelompok A'
        ]);

        // Create student
        $student = Student::create([
            'exam_number' => '02-001-001-1',
            'nisn' => '1234567890',
            'name' => 'Ahmad Fauzi',
            'birth_place' => 'Bandung',
            'birth_date' => '2011-05-12',
            'class' => 'IX-A',
            'status' => 'LULUS'
        ]);

        // Add score
        Grade::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'score' => 85.00
        ]);

        // Search by Exam Number
        $response = $this->get('/?search=02-001-001-1');
        $response->assertStatus(200);
        $response->assertSee('Ahmad Fauzi');
        $response->assertSee('LULUS');
        $response->assertSee('85.00');

        // Search by NISN
        $response = $this->get('/?search=1234567890');
        $response->assertStatus(200);
        $response->assertSee('Ahmad Fauzi');

        // Search for non-existent student
        $response = $this->get('/?search=invalid_id');
        $response->assertStatus(200);
        $response->assertSee('Data siswa tidak ditemukan');
    }
}
