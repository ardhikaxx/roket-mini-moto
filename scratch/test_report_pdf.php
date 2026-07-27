<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SalesReport;

try {
    $reports = SalesReport::with(['store','user'])->take(10)->get();
    $totalOmzet = $reports->where('status','disetujui')->sum('total_amount');
    $totalItems = $reports->sum('total_items');
    $approvedCount = $reports->where('status','disetujui')->count();

    $pdf = Pdf::loadView('admin.reports.print_pdf', [
        'reports' => $reports,
        'selectedStore' => null,
        'periodLabel' => 'Semua Periode',
        'totalOmzet' => $totalOmzet,
        'totalItems' => $totalItems,
        'approvedCount' => $approvedCount,
    ])->setPaper('a4', 'landscape');

    $bytes = strlen($pdf->output());
    echo "SALES_REPORT_PDF_GENERATED_SUCCESS: $bytes bytes!\n";
} catch (\Throwable $e) {
    echo "SALES_REPORT_PDF_ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
