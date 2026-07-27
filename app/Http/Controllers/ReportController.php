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
    public function index() {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $reports = SalesReport::with(['store', 'user', 'images'])->latest()->get();
            return view('admin.reports.index', compact('reports'));
        } elseif ($user->isKepalaToko()) {
            $reports = SalesReport::whereIn('store_id', $user->stores->pluck('id'))->with(['store', 'user', 'images'])->latest()->get();
            return view('admin.reports.index', compact('reports'));
        } else {
            $reports = SalesReport::where('user_id', $user->id)->with('store')->latest()->get();
            $totalApproved = $reports->where('status','disetujui')->sum('total_amount');
            return view('karyawan.reports.index', compact('reports', 'totalApproved'));
        }
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
