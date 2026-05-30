<?php
require __DIR__ . '/../vendor/autoload.php';

$dates = [
    '11 Juli 2010',
    '04 Agustus 2011',
    '11 November 2010',
    '13 Juli 2010',
];

echo "Test with Carbon::parseFromLocale:\n";
foreach ($dates as $d) {
    try {
        $parsed = Carbon\Carbon::parseFromLocale($d, 'id');
        echo "  $d -> " . $parsed->format('Y-m-d') . "\n";
    } catch (Exception $e) {
        echo "  $d -> error: " . $e->getMessage() . "\n";
    }
}

echo "\nTest with str_replace:\n";
$months = [
    'Januari' => '01', 'Februari' => '02', 'Maret' => '03', 'April' => '04',
    'Mei' => '05', 'Juni' => '06', 'Juli' => '07', 'Agustus' => '08',
    'September' => '09', 'Oktober' => '10', 'November' => '11', 'Desember' => '12',
];
foreach ($dates as $d) {
    $normalized = str_replace(array_keys($months), array_values($months), $d);
    try {
        $parsed = Carbon\Carbon::createFromFormat('d m Y', $normalized);
        echo "  $d -> $normalized -> " . $parsed->format('Y-m-d') . "\n";
    } catch (Exception $e) {
        echo "  $d -> error: " . $e->getMessage() . "\n";
    }
}
