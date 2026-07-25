<?php
namespace App\Http\Controllers;
use App\Models\{SalesReport, SalesReportItem, SalesReportImage, Product, Store};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index() {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $reports = SalesReport::with(['store', 'user'])->latest()->get();
            return view('admin.reports.index', compact('reports'));
        } elseif ($user->isKepalaToko()) {
            $reports = SalesReport::whereIn('store_id', $user->stores->pluck('id'))->with(['store', 'user'])->latest()->get();
            return view('admin.reports.index', compact('reports'));
        } else {
            $reports = SalesReport::where('user_id', $user->id)->with('store')->latest()->get();
            return view('karyawan.reports.index', compact('reports'));
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

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalItems = 0;
            
            $report = SalesReport::create([
                'user_id' => Auth::id(),
                'store_id' => $request->store_id,
                'total_amount' => 0,
                'total_items' => 0,
                'transaction_date' => now()->toDateString(),
                'status' => 'diproses',
                'notes' => $request->notes
            ]);

            foreach ($request->products as $prodData) {
                $product = Product::find($prodData['id']);
                $subtotal = $product->price * $prodData['qty'];
                $totalAmount += $subtotal;
                $totalItems += $prodData['qty'];

                SalesReportItem::create([
                    'sales_report_id' => $report->id,
                    'product_id' => $product->id,
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
                    $path = $image->store('reports', 'public');
                    SalesReportImage::create([
                        'sales_report_id' => $report->id,
                        'image_path' => $path
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('karyawan.reports.index')->with('success', 'Laporan penjualan berhasil dikirim dan menunggu persetujuan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim laporan: ' . $e->getMessage());
        }
    }

    public function show(SalesReport $report) {
        $user = Auth::user();
        if ($user->isKepalaToko() && !$user->stores->contains('id', $report->store_id)) {
            abort(403, 'Unauthorized access to this store.');
        }
        if (!$user->isAdmin() && !$user->isKepalaToko() && $report->user_id !== $user->id) {
            abort(403, 'Unauthorized.');
        }
        
        $report->load(['items.product', 'images', 'user', 'store']);
        return view('reports.show', compact('report'));
    }

    public function approve(SalesReport $report) {
        if (!Auth::user()->isAdmin()) abort(403);
        if ($report->status !== 'diproses') return back()->with('error', 'Status laporan tidak dapat diubah.');
        
        // Optionally decrease stock here
        foreach($report->items as $item) {
            $item->product->decrement('stock', $item->quantity);
        }

        $report->update(['status' => 'disetujui']);
        return back()->with('success', 'Laporan berhasil disetujui. Omzet bertambah.');
    }

    public function reject(Request $request, SalesReport $report) {
        if (!Auth::user()->isAdmin()) abort(403);
        if ($report->status !== 'diproses') return back()->with('error', 'Status laporan tidak dapat diubah.');
        
        $request->validate(['rejection_reason' => 'required|string']);
        $report->update([
            'status' => 'ditolak',
            'rejection_reason' => $request->rejection_reason
        ]);
        return back()->with('success', 'Laporan telah ditolak.');
    }
}