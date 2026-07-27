<?php
namespace App\Http\Controllers;
use App\Models\{SalesReport, SalesReportItem, SalesReportImage, Product, Store, ReportStatusHistory};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditService;
use App\Services\NotificationService;

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

        $filename = 'Laporan_Penjualan_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($reports, $selectedStore, $periodLabel, $totalOmzet, $totalItems, $approvedCount) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Microsoft Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header Banner
            fputcsv($file, ['ROKET MINI MOTO BONDOWOSO - LAPORAN PENJUALAN OPERASIONAL & OMZET TOKO']);
            fputcsv($file, []);

            // Meta Info
            fputcsv($file, ['Cabang Toko:', $selectedStore ? $selectedStore->name : 'Semua Toko Cabang', '', 'Tanggal Ekspor:', date('d/m/Y H:i')]);
            fputcsv($file, ['Periode Waktu:', $periodLabel, '', 'Diekspor Oleh:', auth()->user()->name . ' (' . auth()->user()->role . ')']);
            fputcsv($file, []);

            // KPI Summary Row
            fputcsv($file, ['--- RINGKASAN UTAMA ---']);
            fputcsv($file, ['TOTAL OMZET DISETUJUI', 'Rp ' . number_format($totalOmzet, 0, ',', '.')]);
            fputcsv($file, ['TOTAL TRANSAKSI VALID', $approvedCount . ' Transaksi']);
            fputcsv($file, ['TOTAL BARANG TERJUAL', number_format($totalItems, 0, ',', '.') . ' Pcs']);
            fputcsv($file, []);

            // Table Headers
            fputcsv($file, [
                'No',
                'ID Laporan',
                'Tanggal Transaksi',
                'Cabang Toko',
                'Kasir / Petugas',
                'Rincian Produk Terjual',
                'Total Item (Pcs)',
                'Total Omzet (Rp)',
                'Status',
                'Catatan'
            ]);

            // Data Rows
            foreach ($reports as $index => $r) {
                $itemsSummary = [];
                foreach ($r->items as $item) {
                    $itemsSummary[] = $item->quantity . 'x ' . ($item->product_name ?? 'Produk');
                }
                $itemsText = implode(' | ', $itemsSummary);

                fputcsv($file, [
                    $index + 1,
                    '#REP-' . str_pad($r->id, 5, '0', STR_PAD_LEFT),
                    \Carbon\Carbon::parse($r->transaction_date)->format('d/m/Y H:i'),
                    $r->store->name ?? '-',
                    $r->user->name ?? '-',
                    $itemsText ?: '-',
                    $r->total_items,
                    'Rp ' . number_format($r->total_amount, 0, ',', '.'),
                    strtoupper($r->status),
                    $r->notes ?? '-'
                ]);
            }

            // Total Summary Row
            fputcsv($file, []);
            fputcsv($file, [
                'GRAND TOTAL OMZET DISETUJUI',
                '',
                '',
                '',
                '',
                '',
                $totalItems . ' Pcs',
                'Rp ' . number_format($totalOmzet, 0, ',', '.'),
                '',
                ''
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

        return view('admin.reports.print_pdf', compact(
            'reports',
            'selectedStore',
            'periodLabel',
            'totalOmzet',
            'totalItems',
            'approvedCount'
        ));
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
            $query->where('store_id', $request->store_id);
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
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|max:2048'
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
            'notes' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.qty' => 'required|integer|min:1',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|max:2048'
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
                'notes' => 'Disetujui oleh ' . $user->name
            ]);

            DB::commit();

            AuditService::log('approve_report', 'Laporan #'.$report->id.' disetujui oleh '.$user->name, 'SalesReport', $report->id);
            NotificationService::send($report->user_id, 'report_approved', 'Laporan Disetujui', 'Laporan #'.$report->id.' Anda telah disetujui. Omzet Rp '.number_format($report->total_amount,0,',','.').' telah tercatat.', route('admin.reports.show', $report->id));

            return back()->with('success', 'Laporan berhasil disetujui. Omzet bertambah.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui laporan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, SalesReport $report) {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isKepalaToko()) abort(403);
        if ($report->status !== 'diproses') return back()->with('error', 'Status laporan tidak dapat diubah.');

        $request->validate(['rejection_reason' => 'required|string']);

        try {
            DB::beginTransaction();

            $report->update([
                'status' => 'ditolak',
                'rejection_reason' => $request->rejection_reason
            ]);

            ReportStatusHistory::create([
                'sales_report_id' => $report->id,
                'user_id' => Auth::id(),
                'from_status' => 'diproses',
                'to_status' => 'ditolak',
                'notes' => 'Alasan: ' . $request->rejection_reason
            ]);

            DB::commit();

            AuditService::log('reject_report', 'Laporan #'.$report->id.' ditolak oleh '.$user->name.'. Alasan: '.$request->rejection_reason, 'SalesReport', $report->id);
            NotificationService::send($report->user_id, 'report_rejected', 'Laporan Ditolak', 'Laporan #'.$report->id.' Anda ditolak. Alasan: '.$request->rejection_reason, route('karyawan.reports.edit', $report->id));

            return back()->with('success', 'Laporan telah ditolak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak laporan: ' . $e->getMessage());
        }
    }

    public function destroy(SalesReport $report) {
        if (!Auth::user()->isAdmin()) abort(403);
        try {
            DB::beginTransaction();
            foreach ($report->images as $img) {
                \App\Helpers\FileUploadHelper::delete($img->image_path);
                $img->delete();
            }
            $report->items()->delete();
            $report->statusHistories()->delete();
            $report->delete();
            DB::commit();
            AuditService::log('delete_report', 'Laporan #'.$report->id.' dihapus oleh '.Auth::user()->name, 'SalesReport', $report->id);
            return redirect()->route('admin.reports.index')->with('success', 'Laporan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus laporan: ' . $e->getMessage());
        }
    }
}
