<?php
namespace App\Http\Controllers;
use App\Models\{SalesReport, SalesReportItem, Product, Store};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Border, Alignment};

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::where('is_active', true)->get();
        $data = $this->getProfitData($request);

        return view('admin.profit-loss.index', array_merge(
            $data, compact('stores')
        ));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getProfitData($request);

        $pdf = Pdf::loadView('admin.profit-loss.print_pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Laba_Rugi_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getProfitData($request);
        $reportProfits = $data['reportProfits'];
        $productProfits = $data['productProfits'];
        $totalRevenue = $data['totalRevenue'];
        $totalCost = $data['totalCost'];
        $totalProfit = $data['totalProfit'];
        $totalProfitPercent = $data['totalProfitPercent'];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laba Rugi');

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'ROKET MINI MOTO - LAPORAN LABA RUGI');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'E63946']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'Periode: ' . $data['periodLabel']);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['size' => 11, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 4;
        $kpis = [
            ['Total Omzet', 'Rp ' . number_format($totalRevenue, 0, ',', '.'), '1ABC9C'],
            ['Total Modal', 'Rp ' . number_format($totalCost, 0, ',', '.'), 'E74C3C'],
            ['Laba Kotor', 'Rp ' . number_format($totalProfit, 0, ',', '.'), 'E63946'],
            ['Margin', $totalProfitPercent . '%', '8E44AD'],
        ];
        foreach ($kpis as $i => $kpi) {
            $col = chr(66 + $i);
            $sheet->setCellValue($col . $row, $kpi[0]);
            $sheet->setCellValue($col . ($row + 1), $kpi[1]);
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $kpi[2]]],
            ]);
            $sheet->getStyle($col . ($row + 1))->applyFromArray([
                'font' => ['bold' => true, 'size' => 14],
            ]);
        }

        $row = 7;
        $sheet->setCellValue('A' . $row, 'RINCIAN PER TRANSAKSI');
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $row++;

        $headers = ['#', 'Tanggal', 'Toko', 'Kasir', 'Omzet', 'Modal', 'Laba', 'Margin'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . $row, $h);
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $col++;
        }
        $headerRow = $row;

        $row++;
        foreach ($reportProfits as $i => $rp) {
            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $rp['report']->transaction_date->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $rp['report']->store->name ?? '-');
            $sheet->setCellValue('D' . $row, $rp['report']->user->name ?? '-');
            $sheet->setCellValue('E' . $row, $rp['revenue']);
            $sheet->setCellValue('F' . $row, $rp['cost']);
            $sheet->setCellValue('G' . $row, $rp['profit']);
            $sheet->setCellValue('H' . $row, $rp['percent'] . '%');

            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');

            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $row . ':H' . $row)
                    ->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(['rgb' => 'F8FAFC']);
            }
            $row++;
        }

        $row += 2;
        $sheet->setCellValue('A' . $row, 'RINCIAN PER PRODUK');
        $sheet->getStyle('A' . $row)->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
        ]);
        $row++;

        $pHeaders = ['#', 'Produk', 'SKU', 'Terjual', 'Omzet', 'Modal', 'Laba', 'Margin'];
        $col = 'A';
        foreach ($pHeaders as $h) {
            $sheet->setCellValue($col . $row, $h);
            $sheet->getStyle($col . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $col++;
        }

        $row++;
        foreach ($productProfits as $i => $pp) {
            $profit = $pp['revenue'] - $pp['cost'];
            $percent = $pp['revenue'] > 0 ? round(($profit / $pp['revenue']) * 100, 1) : 0;

            $sheet->setCellValue('A' . $row, $i + 1);
            $sheet->setCellValue('B' . $row, $pp['product_name']);
            $sheet->setCellValue('C' . $row, $pp['product']->sku ?? '-');
            $sheet->setCellValue('D' . $row, $pp['qty'] . ' pcs');
            $sheet->setCellValue('E' . $row, $pp['revenue']);
            $sheet->setCellValue('F' . $row, $pp['cost']);
            $sheet->setCellValue('G' . $row, $profit);
            $sheet->setCellValue('H' . $row, $percent . '%');

            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('"Rp "#,##0');

            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $row . ':H' . $row)
                    ->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(['rgb' => 'F8FAFC']);
            }
            $row++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Laporan_Laba_Rugi_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function getProfitData(Request $request)
    {
        $user = Auth::user();
        $query = SalesReport::with(['items.product', 'store', 'user'])
            ->where('status', 'disetujui');

        if ($user->isKepalaToko()) {
            $query->whereIn('store_id', $user->stores->pluck('id'));
        }

        if ($request->filled('store_id') && $request->store_id !== 'all') {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }

        $reports = $query->get();

        $totalRevenue = 0;
        $totalCost = 0;
        $totalProfit = 0;
        $totalProfitPercent = 0;
        $reportProfits = [];
        $productProfits = [];

        foreach ($reports as $report) {
            $reportRevenue = $report->total_amount;
            $reportCost = 0;

            foreach ($report->items as $item) {
                $costPrice = $item->product && $item->product->cost_price ? $item->product->cost_price : 0;
                $itemCost = $costPrice * $item->quantity;
                $reportCost += $itemCost;

                $productId = $item->product_id;
                if (!isset($productProfits[$productId])) {
                    $productProfits[$productId] = [
                        'product' => $item->product,
                        'product_name' => $item->product_name,
                        'qty' => 0,
                        'revenue' => 0,
                        'cost' => 0,
                    ];
                }
                $productProfits[$productId]['qty'] += $item->quantity;
                $productProfits[$productId]['revenue'] += $item->subtotal;
                $productProfits[$productId]['cost'] += $itemCost;
            }

            $reportProfit = $reportRevenue - $reportCost;
            $reportPercent = $reportRevenue > 0 ? round(($reportProfit / $reportRevenue) * 100, 1) : 0;

            $totalRevenue += $reportRevenue;
            $totalCost += $reportCost;
            $totalProfit += $reportProfit;

            $reportProfits[] = [
                'report' => $report,
                'revenue' => $reportRevenue,
                'cost' => $reportCost,
                'profit' => $reportProfit,
                'percent' => $reportPercent,
            ];
        }

        $totalProfitPercent = $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 1) : 0;

        usort($productProfits, fn($a, $b) => ($b['revenue'] - $b['cost']) <=> ($a['revenue'] - $a['cost']));

        $storeLabel = 'Semua Toko';
        if ($request->filled('store_id') && $request->store_id !== 'all') {
            $store = Store::find($request->store_id);
            $storeLabel = $store ? $store->name : 'Semua Toko';
        }
        $monthLabel = $request->filled('month') ? ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][$request->month - 1] : 'Semua Bulan';
        $yearLabel = $request->filled('year') ? $request->year : 'Semua Tahun';
        $periodLabel = "$storeLabel | $monthLabel $yearLabel";

        return compact(
            'reportProfits', 'productProfits',
            'totalRevenue', 'totalCost', 'totalProfit', 'totalProfitPercent',
            'periodLabel'
        );
    }
}
