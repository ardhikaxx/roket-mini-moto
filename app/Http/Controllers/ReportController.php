<?php
namespace App\Http\Controllers;
use App\Models\{SalesReport, SalesReportItem, SalesReportImage, Product, Store, ReportStatusHistory};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditService;
use App\Services\NotificationService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\{Fill, Border, Alignment, NumberFormat};
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
        $reports = $this->getFilteredReports($request);

        if ($user->isAdmin()) {
            $stores = Store::where('is_active', true)->get();
            return view('admin.reports.index', compact('reports', 'stores'));
        } elseif ($user->isKepalaToko()) {
            $stores = $user->stores()->where('is_active', true)->get();
            return view('admin.reports.index', compact('reports', 'stores'));
        } else {
            $stores = $user->stores;
            $totalApproved = $reports->where('status','disetujui')->sum('total_amount');
            return view('karyawan.reports.index', compact('reports', 'stores', 'totalApproved'));
        }
    }

    public function exportExcel(Request $request) {
        $reports = $this->getFilteredReports($request);
        
        $selectedStore = null;
        if ($request->filled('store_id') && $request->store_id !== 'all') {
            $selectedStore = Store::find($request->store_id);
        }

        $period = $request->input('period', 'all');
        $periodLabel = 'Semua Periode';
        if ($period === 'today') {
            $periodLabel = 'Hari Ini (' . \Carbon\Carbon::today()->format('d/m/Y') . ')';
        } elseif ($period === 'this_week') {
            $periodLabel = 'Minggu Ini (' . \Carbon\Carbon::now()->startOfWeek()->format('d/m/Y') . ' - ' . \Carbon\Carbon::now()->endOfWeek()->format('d/m/Y') . ')';
        } elseif ($period === 'this_month') {
            $periodLabel = 'Bulan Ini (' . \Carbon\Carbon::now()->format('F Y') . ')';
        } elseif ($period === 'custom') {
            $start = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') : 'Awal';
            $end = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') : 'Akhir';
            $periodLabel = "Rentang Tanggal ($start s/d $end)";
        }

        $totalOmzet = $reports->where('status', 'disetujui')->sum('total_amount');
        $totalItems = $reports->sum('total_items');
        $approvedCount = $reports->where('status', 'disetujui')->count();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Penjualan');
        $sheet->setShowGridLines(true);

        // Title Banners
        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'ROKET MINI MOTO BONDOWOSO');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E63946']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(40);

        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', 'LAPORAN PENJUALAN OPERASIONAL & OMZET TOKO');
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(25);

        // Meta Info (Row 4 & 5)
        $sheet->setCellValue('A4', 'Cabang Toko:');
        $sheet->setCellValue('B4', $selectedStore ? $selectedStore->name : 'Semua Toko Cabang');
        $sheet->setCellValue('F4', 'Tanggal Ekspor:');
        $sheet->setCellValue('G4', date('d/m/Y H:i'));

        $sheet->setCellValue('A5', 'Periode Waktu:');
        $sheet->setCellValue('B5', $periodLabel);
        $sheet->setCellValue('F5', 'Diekspor Oleh:');
        $sheet->setCellValue('G5', auth()->user()->name . ' (' . auth()->user()->role . ')');

        $sheet->getStyle('A4:A5')->getFont()->setBold(true);
        $sheet->getStyle('F4:F5')->getFont()->setBold(true);

        // KPI Summary Cards (Row 7)
        $sheet->mergeCells('A7:C7');
        $sheet->setCellValue('A7', 'TOTAL OMZET DISETUJUI: Rp ' . number_format($totalOmzet, 0, ',', '.'));
        $sheet->getStyle('A7:C7')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '15803D']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCFCE7']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '86EFAC']]],
        ]);

        $sheet->mergeCells('D7:F7');
        $sheet->setCellValue('D7', 'TOTAL TRANSAKSI VALID: ' . $approvedCount . ' Transaksi');
        $sheet->getStyle('D7:F7')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '0369A1']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0F2FE']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '7DD3FC']]],
        ]);

        $sheet->mergeCells('G7:I7');
        $sheet->setCellValue('G7', 'TOTAL BARANG TERJUAL: ' . number_format($totalItems, 0, ',', '.') . ' Pcs');
        $sheet->getStyle('G7:I7')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'B45309']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FDE68A']]],
        ]);
        $sheet->getRowDimension(7)->setRowHeight(30);

        // Table Headers (Row 9)
        $tableHeaders = ['No', 'ID Laporan', 'Tanggal Transaksi', 'Cabang Toko', 'Kasir / Petugas', 'Rincian Produk Terjual', 'Total Item', 'Total Omzet (Rp)', 'Status'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

        foreach ($tableHeaders as $index => $headerText) {
            $col = $cols[$index];
            $sheet->setCellValue($col . '9', $headerText);
        }

        $sheet->getStyle('A9:I9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        $sheet->getRowDimension(9)->setRowHeight(30);

        // Data Rows (Row 10+)
        $rowNum = 10;
        foreach ($reports as $index => $r) {
            $itemsSummary = [];
            foreach ($r->items as $item) {
                $itemsSummary[] = $item->quantity . 'x ' . ($item->product_name ?? 'Produk');
            }
            $itemsText = implode(', ', $itemsSummary);

            $sheet->setCellValue('A' . $rowNum, $index + 1);
            $sheet->setCellValue('B' . $rowNum, '#REP-' . str_pad($r->id, 5, '0', STR_PAD_LEFT));
            $sheet->setCellValue('C' . $rowNum, \Carbon\Carbon::parse($r->transaction_date)->format('d/m/Y H:i'));
            $sheet->setCellValue('D' . $rowNum, $r->store->name ?? '-');
            $sheet->setCellValue('E' . $rowNum, $r->user->name ?? '-');
            $sheet->setCellValue('F' . $rowNum, $itemsText ?: '-');
            $sheet->setCellValue('G' . $rowNum, $r->total_items);
            $sheet->setCellValue('H' . $rowNum, $r->total_amount);
            $sheet->setCellValue('I' . $rowNum, strtoupper($r->status));

            // Cell Formatting
            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('H' . $rowNum)->getNumberFormat()->setFormatCode('"Rp "#,##0');
            $sheet->getStyle('I' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Status Colors
            $statusUpper = strtoupper($r->status);
            $statusColor = 'DCFCE7';
            $textColor = '15803D';
            if ($statusUpper === 'DIPROSES') {
                $statusColor = 'FEF9C3';
                $textColor = 'A16207';
            } elseif ($statusUpper === 'DITOLAK') {
                $statusColor = 'FEE2E2';
                $textColor = 'B91C1C';
            }
            $sheet->getStyle('I' . $rowNum)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $textColor]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColor]],
            ]);

            // Alternating Row Background
            if ($index % 2 == 1) {
                $sheet->getStyle("A{$rowNum}:H{$rowNum}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8FAFC');
            }

            // Row Borders
            $sheet->getStyle("A{$rowNum}:I{$rowNum}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');

            $rowNum++;
        }

        // Grand Total Row
        $sheet->mergeCells("A{$rowNum}:F{$rowNum}");
        $sheet->setCellValue("A{$rowNum}", 'TOTAL OMZET VALID (DISETUJUI)');
        $sheet->setCellValue("G{$rowNum}", $totalItems);
        $sheet->setCellValue("H{$rowNum}", $totalOmzet);

        $sheet->getStyle("A{$rowNum}:I{$rowNum}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCFCE7']],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '16A34A']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '16A34A']],
            ],
        ]);
        $sheet->getStyle("A{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("G{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("H{$rowNum}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("H{$rowNum}")->getNumberFormat()->setFormatCode('"Rp "#,##0');
        $sheet->getStyle("H{$rowNum}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('15803D'));

        // Auto-size columns for perfect fit
        foreach ($cols as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Laporan_Penjualan_' . now()->format('Ymd_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportPdf(Request $request) {
        $reports = $this->getFilteredReports($request);
        
        $selectedStore = null;
        if ($request->filled('store_id') && $request->store_id !== 'all') {
            $selectedStore = Store::find($request->store_id);
        }

        $period = $request->input('period', 'all');
        $periodLabel = 'Semua Periode';
        if ($period === 'today') {
            $periodLabel = 'Hari Ini (' . \Carbon\Carbon::today()->format('d/m/Y') . ')';
        } elseif ($period === 'this_week') {
            $periodLabel = 'Minggu Ini (' . \Carbon\Carbon::now()->startOfWeek()->format('d/m/Y') . ' - ' . \Carbon\Carbon::now()->endOfWeek()->format('d/m/Y') . ')';
        } elseif ($period === 'this_month') {
            $periodLabel = 'Bulan Ini (' . \Carbon\Carbon::now()->format('F Y') . ')';
        } elseif ($period === 'custom') {
            $start = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') : 'Awal';
            $end = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') : 'Akhir';
            $periodLabel = "Rentang Tanggal ($start s/d $end)";
        }

        $totalOmzet = $reports->where('status', 'disetujui')->sum('total_amount');
        $totalItems = $reports->sum('total_items');
        $approvedCount = $reports->where('status', 'disetujui')->count();

        $pdf = Pdf::loadView('admin.reports.print_pdf', compact(
            'reports',
            'selectedStore',
            'periodLabel',
            'totalOmzet',
            'totalItems',
            'approvedCount'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Penjualan_' . now()->format('Ymd_His') . '.pdf');
    }

    private function getFilteredReports(Request $request) {
        $user = Auth::user();
        $query = SalesReport::with(['store', 'user', 'images', 'items.product'])->latest('transaction_date');

        if ($user->isKepalaToko()) {
            $query->whereIn('store_id', $user->stores->pluck('id'));
        } elseif ($user->isKaryawan()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('store_id') && $request->store_id !== 'all') {
            // Karyawan: pastikan store_id yang diminta adalah toko yang ditugaskan kepadanya
            if ($user->isKaryawan()) {
                $allowedStoreIds = $user->stores->pluck('id');
                if ($allowedStoreIds->contains($request->store_id)) {
                    $query->where('store_id', $request->store_id);
                }
                // Jika store_id bukan miliknya, abaikan filter (scope user_id sudah berlaku)
            } else {
                $query->where('store_id', $request->store_id);
            }
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $period = $request->input('period', 'all');
        if ($period === 'today') {
            $query->whereDate('transaction_date', \Carbon\Carbon::today());
        } elseif ($period === 'this_week') {
            $query->whereBetween('transaction_date', [
                \Carbon\Carbon::now()->startOfWeek(),
                \Carbon\Carbon::now()->endOfWeek()
            ]);
        } elseif ($period === 'this_month') {
            $query->whereYear('transaction_date', \Carbon\Carbon::now()->year)
                  ->whereMonth('transaction_date', \Carbon\Carbon::now()->month);
        } elseif ($period === 'custom') {
            if ($request->filled('start_date')) {
                $query->whereDate('transaction_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('transaction_date', '<=', $request->end_date);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")->orWhere('username', 'like', "%{$search}%");
                })->orWhereHas('store', function($s) use ($search) {
                    $s->where('name', 'like', "%{$search}%");
                })->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    public function create() {
        $user = Auth::user();
        $stores = $user->stores()->where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        return view('karyawan.reports.create', compact('stores', 'products'));
    }

    public function store(Request $request) {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'notes' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1|max:9999',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'images.*.mimes' => 'File gambar harus berformat JPG, JPEG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran setiap gambar maksimal 5 MB.',
            'images.required' => 'Minimal satu foto bukti laporan harus diunggah.',
        ]);

        $user = Auth::user();
        if (!$user->stores->contains('id', $request->store_id)) {
            return back()->with('error', 'Anda tidak ditugaskan di toko ini.');
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalItems = 0;
            
            $report = SalesReport::create([
                'user_id' => Auth::id(),
                'store_id' => $request->store_id,
                'total_amount' => 0,
                'total_items' => 0,
                'transaction_date' => now(),
                'status' => 'diproses',
                'notes' => $request->notes
            ]);

            foreach ($request->products as $prodData) {
                $product = Product::findOrFail($prodData['id']);
                $subtotal = $product->price * $prodData['qty'];
                $totalAmount += $subtotal;
                $totalItems += $prodData['qty'];

                SalesReportItem::create([
                    'sales_report_id' => $report->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $prodData['qty'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ]);
            }

            $report->update([
                'total_amount' => $totalAmount,
                'total_items' => $totalItems
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = \App\Helpers\FileUploadHelper::upload($image, 'reports');
                    SalesReportImage::create([
                        'sales_report_id' => $report->id,
                        'image_path' => $path
                    ]);
                }
            }

            ReportStatusHistory::create([
                'sales_report_id' => $report->id,
                'user_id' => Auth::id(),
                'from_status' => null,
                'to_status' => 'diproses',
                'notes' => 'Laporan dibuat'
            ]);

            DB::commit();

            AuditService::log('create_report', 'Laporan #'.$report->id.' dibuat oleh '.Auth::user()->name, 'SalesReport', $report->id);
            NotificationService::sendToAllAdmins('report_submitted', 'Laporan Baru', Auth::user()->name.' mengirim laporan penjualan dari '.($report->store->name ?? 'toko tidak diketahui').' sebesar Rp '.number_format($totalAmount,0,',','.'), route('admin.reports.show', $report->id));
            NotificationService::sendToStoreHeads($report->store_id, 'report_submitted', 'Laporan Baru dari Karyawan', 'Karyawan '.Auth::user()->name.' mengirim laporan penjualan.', route('admin.reports.show', $report->id));

            return redirect()->route('karyawan.reports.index')->with('success', 'Laporan penjualan berhasil dikirim dan menunggu persetujuan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim laporan: ' . $e->getMessage());
        }
    }

    public function show(SalesReport $report) {
        $user = Auth::user();
        if ($user->isKepalaToko() && !$user->stores->contains('id', $report->store_id)) {
            abort(403);
        }
        if (!$user->isAdmin() && !$user->isKepalaToko() && $report->user_id !== $user->id) {
            abort(403);
        }
        $report->load(['items.product', 'images', 'user', 'store', 'statusHistories.user']);
        return view('admin.reports.show', compact('report'));
    }

    public function edit(SalesReport $report) {
        $user = Auth::user();
        if ($report->user_id !== $user->id && !$user->isAdmin()) abort(403);
        if ($report->status !== 'ditolak') return back()->with('error', 'Hanya laporan yang ditolak yang dapat diperbaiki.');
        
        $stores = $user->stores()->where('is_active', true)->get();
        $products = Product::where('is_active', true)->get();
        $report->load(['items', 'images']);
        return view('karyawan.reports.edit', compact('report', 'stores', 'products'));
    }

    public function update(Request $request, SalesReport $report) {
        $user = Auth::user();
        if ($report->user_id !== $user->id && !$user->isAdmin()) abort(403);
        if ($report->status !== 'ditolak') return back()->with('error', 'Laporan tidak dapat diperbaiki.');

        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'notes' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1|max:9999',
            'images' => 'nullable|array|max:10',
            'images.*' => 'file|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'images.*.mimes' => 'File gambar harus berformat JPG, JPEG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran setiap gambar maksimal 5 MB.',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalItems = 0;

            // Delete old items
            $report->items()->delete();

            foreach ($request->products as $prodData) {
                $product = Product::findOrFail($prodData['id']);
                $subtotal = $product->price * $prodData['qty'];
                $totalAmount += $subtotal;
                $totalItems += $prodData['qty'];

                SalesReportItem::create([
                    'sales_report_id' => $report->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $prodData['qty'],
                    'price' => $product->price,
                    'subtotal' => $subtotal
                ]);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = \App\Helpers\FileUploadHelper::upload($image, 'reports');
                    SalesReportImage::create([
                        'sales_report_id' => $report->id,
                        'image_path' => $path
                    ]);
                }
            }

            $report->update([
                'store_id' => $request->store_id,
                'total_amount' => $totalAmount,
                'total_items' => $totalItems,
                'notes' => $request->notes,
                'status' => 'diproses',
                'rejection_reason' => null
            ]);

            ReportStatusHistory::create([
                'sales_report_id' => $report->id,
                'user_id' => Auth::id(),
                'from_status' => 'ditolak',
                'to_status' => 'diproses',
                'notes' => 'Laporan diperbaiki dan dikirim ulang'
            ]);

            DB::commit();

            AuditService::log('resubmit_report', 'Laporan #'.$report->id.' diperbaiki dan dikirim ulang.', 'SalesReport', $report->id);
            NotificationService::sendToAllAdmins('report_submitted', 'Laporan Diperbaiki', Auth::user()->name.' telah memperbaiki dan mengirim ulang laporan #'.$report->id, route('admin.reports.show', $report->id));

            return redirect()->route('karyawan.reports.index')->with('success', 'Laporan berhasil diperbaiki dan dikirim ulang.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbaiki laporan: ' . $e->getMessage());
        }
    }

    public function approve(SalesReport $report) {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isKepalaToko()) abort(403);
        if ($report->status !== 'diproses') return back()->with('error', 'Status laporan tidak dapat diubah.');

        try {
            DB::beginTransaction();
            
            // Pengurangan stok produk otomatis saat laporan disetujui
            foreach($report->items as $item) {
                if ($item->product) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }

            $report->update(['status' => 'disetujui']);

            ReportStatusHistory::create([
                'sales_report_id' => $report->id,
                'user_id' => Auth::id(),
                'from_status' => 'diproses',
                'to_status' => 'disetujui',
                'notes' => 'Disetujui oleh ' . $user->name . ' (Stok berkurang otomatis)'
            ]);

            DB::commit();

            AuditService::log('approve_report', 'Laporan #'.$report->id.' disetujui oleh '.$user->name.' (Stok terpotong otomatis)', 'SalesReport', $report->id);
            NotificationService::send($report->user_id, 'report_approved', 'Laporan Disetujui', 'Laporan #'.$report->id.' Anda telah disetujui. Omzet Rp '.number_format($report->total_amount,0,',','.').' telah tercatat dan stok terpotong otomatis.', route('admin.reports.show', $report->id));

            return back()->with('success', 'Laporan berhasil disetujui. Stok produk otomatis berkurang.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui laporan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, SalesReport $report) {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isKepalaToko()) abort(403);

        $request->validate(['rejection_reason' => 'required|string']);

        try {
            DB::beginTransaction();

            $oldStatus = $report->status;

            // Pengembalian stok produk otomatis jika laporan yang sebelumnya disetujui ditolak/dibatalkan
            if ($oldStatus === 'disetujui') {
                foreach($report->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            $report->update([
                'status' => 'ditolak',
                'rejection_reason' => $request->rejection_reason
            ]);

            ReportStatusHistory::create([
                'sales_report_id' => $report->id,
                'user_id' => Auth::id(),
                'from_status' => $oldStatus,
                'to_status' => 'ditolak',
                'notes' => 'Alasan: ' . $request->rejection_reason . ($oldStatus === 'disetujui' ? ' (Stok dikembalikan)' : '')
            ]);

            DB::commit();

            AuditService::log('reject_report', 'Laporan #'.$report->id.' ditolak oleh '.$user->name.'. Alasan: '.$request->rejection_reason, 'SalesReport', $report->id);
            NotificationService::send($report->user_id, 'report_rejected', 'Laporan Ditolak', 'Laporan #'.$report->id.' Anda ditolak. Alasan: '.$request->rejection_reason, route('karyawan.reports.edit', $report->id));

            return back()->with('success', 'Laporan telah ditolak' . ($oldStatus === 'disetujui' ? ' dan stok dikembalikan.' : '.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak laporan: ' . $e->getMessage());
        }
    }

    public function destroy(SalesReport $report) {
        $user = Auth::user();
        if (!$user->isAdmin() && ($user->isKaryawan() && $report->user_id !== $user->id)) {
            abort(403);
        }
        try {
            DB::beginTransaction();

            // Kembalikan stok jika laporan yang disetujui dihapus
            if ($report->status === 'disetujui') {
                foreach ($report->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            foreach ($report->images as $img) {
                \App\Helpers\FileUploadHelper::delete($img->image_path);
                $img->delete();
            }
            $report->items()->delete();
            $report->statusHistories()->delete();
            $report->delete();

            DB::commit();
            AuditService::log('delete_report', 'Laporan #'.$report->id.' dihapus oleh '.$user->name, 'SalesReport', $report->id);
            $redirectRoute = $user->isAdmin() ? 'admin.reports.index' : 'karyawan.reports.index';
            return redirect()->route($redirectRoute)->with('success', 'Laporan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }
}
