<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;

try {
    $pdf = Pdf::loadHTML('<h1>Test PDF Generation Roket Mini Moto</h1>')->setPaper('a4', 'landscape');
    $output = $pdf->output();
    echo "PDF_GENERATE_SUCCESS: " . strlen($output) . " bytes generated!\n";
} catch (\Throwable $e) {
    echo "PDF_GENERATE_ERROR: " . $e->getMessage() . "\n";
}
