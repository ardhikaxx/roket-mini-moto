<?php
namespace App\Http\Controllers;

use App\Models\SalesReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Barryvdh\DomPDF\Facade\Pdf;

class RevenueController extends Controller
{
    public function index() {
        return view('omzet.index');
    }

    public function chartData(Request $request) {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        
        $data = SalesReport::where('status', 'disetujui')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $values = [];
        
        $period = now()->subDays($days)->copy();
        $dataMap = $data->keyBy('date');
        
        while ($period <= now()) {
            $dateStr = $period->format('Y-m-d');
            $labels[] = $period->format('d M');
            $values[] = (int) ($dataMap[$dateStr]->total ?? 0);
            $period->addDay();
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    public function exportPdf() {
        $totalOmzet = SalesReport::where('status','disetujui')->sum('total_amount');
        $totalTransactions = SalesReport::where('status','disetujui')->count();
        $totalItems = SalesReport::where('status','disetujui')->sum('total_items');
        $avgTransaction = $totalTransactions > 0 ? $totalOmzet / $totalTransactions : 0;

        $storeOmzet = SalesReport::where('status','disetujui')
            ->selectRaw('store_id, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('store_id')->with('store')->orderByDesc('total')->get();

        $catSales = \App\Models\SalesReportItem::whereHas('salesReport', fn($q) => $q->where('status','disetujui'))
            ->whereHas('product.category')
            ->selectRaw('SUM(quantity) as total_qty, SUM(subtotal) as total_amount, product_id')
            ->groupBy('product_id')->with('product.category')->get();

        $catSummary = $catSales->groupBy(fn($i) => $i->product->category->name ?? 'Lainnya')
            ->map(fn($items) => ['qty' => $items->sum('total_qty'), 'amount' => $items->sum('total_amount')]);

        $pdf = Pdf::loadView('omzet.print_pdf', compact('totalOmzet', 'totalTransactions', 'totalItems', 'avgTransaction', 'storeOmzet', 'catSummary'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Analitik_Omzet_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportExcel() {
        $totalOmzet = SalesReport::where('status','disetujui')->sum('total_amount');
        $totalTransactions = SalesReport::where('status','disetujui')->count();
        $totalItems = SalesReport::where('status','disetujui')->sum('total_items');
        $avgTransaction = $totalTransactions > 0 ? $totalOmzet / $totalTransactions : 0;

        $storeOmzet = SalesReport::where('status','disetujui')
            ->selectRaw('store_id, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('store_id')->with('store')->orderByDesc('total')->get();

        $catSales = \App\Models\SalesReportItem::whereHas('salesReport', fn($q) => $q->where('status','disetujui'))
            ->whereHas('product.category')
            ->selectRaw('SUM(quantity) as total_qty, SUM(subtotal) as total_amount, product_id')
            ->groupBy('product_id')->with('product.category')->get();

        $catSummary = $catSales->groupBy(fn($i) => $i->product->category->name ?? 'Lainnya')
            ->map(fn($items) => ['qty' => $items->sum('total_qty'), 'amount' => $items->sum('total_amount')]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Analitik Omzet');

        // Header Banner
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'ROKET MINI MOTO - LAPORAN ANALITIK & PERFORMA BISNIS');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E63946']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        // Date generated
        $sheet->mergeCells('A2:E2');
        $sheet->setCellValue('A2', 'Tanggal Dibuat: ' . now()->format('d F Y, H:i') . ' WIB');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Ringkasan KPI
        $sheet->setCellValue('A4', 'TOTAL OMZET DISETUJUI');
        $sheet->setCellValue('B4', $totalOmzet);
        $sheet->setCellValue('A5', 'TRANSAKSI SUKSES');
        $sheet->setCellValue('B5', $totalTransactions);
        $sheet->setCellValue('A6', 'PRODUK TERJUAL');
        $sheet->setCellValue('B6', $totalItems);
        $sheet->setCellValue('A7', 'RATA-RATA TRANSAKSI');
        $sheet->setCellValue('B7', $avgTransaction);

        $sheet->getStyle('A4:A7')->getFont()->setBold(true);
        $sheet->getStyle('B4')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle('B5')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('B6')->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('B7')->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle('A4:B7')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CBD5E1');

        // Table 1: Kontribusi per Cabang
        $rowNum = 9;
        $sheet->setCellValue('A' . $rowNum, 'DETAIL KONTRIBUSI OMZET PER CABANG TOKO');
        $sheet->mergeCells("A{$rowNum}:E{$rowNum}");
        $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true)->setSize(11);
        $rowNum++;

        $sheet->setCellValue('A' . $rowNum, 'No');
        $sheet->setCellValue('B' . $rowNum, 'Nama Cabang');
        $sheet->setCellValue('C' . $rowNum, 'Volume Transaksi');
        $sheet->setCellValue('D' . $rowNum, 'Total Omzet Disetor');
        $sheet->setCellValue('E' . $rowNum, 'Kontribusi (%)');

        $sheet->getStyle("A{$rowNum}:E{$rowNum}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $rowNum++;

        foreach ($storeOmzet as $index => $so) {
            $pct = $totalOmzet > 0 ? round(($so->total / $totalOmzet) * 100, 1) : 0;
            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, $so->store->name ?? 'Cabang Tidak Diketahui');
            $sheet->setCellValue('C' . $rowNum, $so->count . ' Laporan');
            $sheet->setCellValue('D' . $rowNum, $so->total);
            $sheet->setCellValue('E' . $rowNum, $pct . '%');

            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('D' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$rowNum}:E{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
            $rowNum++;
        }

        // Table 2: Distribusi Kategori
        $rowNum += 2;
        $sheet->setCellValue('A' . $rowNum, 'DISTRIBUSI OMZET PER KATEGORI PRODUK');
        $sheet->mergeCells("A{$rowNum}:D{$rowNum}");
        $sheet->getStyle('A' . $rowNum)->getFont()->setBold(true)->setSize(11);
        $rowNum++;

        $sheet->setCellValue('A' . $rowNum, 'No');
        $sheet->setCellValue('B' . $rowNum, 'Nama Kategori');
        $sheet->setCellValue('C' . $rowNum, 'Total Qty Terjual');
        $sheet->setCellValue('D' . $rowNum, 'Total Omzet (Rp)');

        $sheet->getStyle("A{$rowNum}:D{$rowNum}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $rowNum++;

        $catIdx = 1;
        foreach ($catSummary as $name => $data) {
            $sheet->setCellValue('A' . $rowNum, $catIdx);
            $sheet->setCellValue('B' . $rowNum, $name);
            $sheet->setCellValue('C' . $rowNum, $data['qty'] . ' Unit');
            $sheet->setCellValue('D' . $rowNum, $data['amount']);

            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('D' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle("A{$rowNum}:D{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
            $rowNum++;
            $catIdx++;
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Analitik_Omzet_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
