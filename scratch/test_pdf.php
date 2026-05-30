<?php
// Initialize Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

echo "Loading student...\n";
$student = Student::first();
if (!$student) {
    die("No student found!\n");
}

echo "Generating SKL PDF for " . $student->name . "...\n";
$announcementDateStr = Setting::get('announcement_date');
$announcementDate = $announcementDateStr ? Carbon\Carbon::parse($announcementDateStr) : null;

$settings = [
    'school_name' => Setting::get('school_name', 'SMP Nurul Ihsan Banjaran'),
    'school_address' => Setting::get('school_address', ''),
    'principal_name' => Setting::get('principal_name', ''),
    'principal_nip' => Setting::get('principal_nip', ''),
    'school_logo' => Setting::get('school_logo'),
    'principal_signature' => Setting::get('principal_signature'),
];

$logoSetting = Setting::get('school_logo');
$logo_path = (!empty($logoSetting) && file_exists(public_path($logoSetting))) ? public_path($logoSetting) : null;
$signature_path = (!empty($settings['principal_signature']) && file_exists(public_path($settings['principal_signature']))) ? public_path($settings['principal_signature']) : null;

echo "Logo path: " . ($logo_path ?? 'NULL') . "\n";
echo "Signature path: " . ($signature_path ?? 'NULL') . "\n";

try {
    $pdf = Pdf::loadView('admin.transcripts.skl_pdf', compact('student', 'announcementDate', 'settings', 'logo_path', 'signature_path'));
    echo "Rendering PDF...\n";
    $output = $pdf->output();
    echo "PDF generated successfully! Size: " . strlen($output) . " bytes\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
