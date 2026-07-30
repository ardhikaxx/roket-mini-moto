<?php
namespace App\Http\Controllers;
use App\Models\{Product, Store, StockTransaction, StockTransfer};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AuditService;

class StockController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->where('is_active', true)->get();
        $lowStockProducts = $products->filter(function($p) {
            return $p->min_stock > 0 && $p->stock <= $p->min_stock;
        });
        return view('admin.stock.index', compact('products', 'lowStockProducts'));
    }

    public function history(Request $request)
    {
        $query = StockTransaction::with(['product', 'store', 'user'])->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }
        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        $transactions = $query->paginate(50);
        $products = Product::where('is_active', true)->get();

        return view('admin.stock.history', compact('transactions', 'products'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();
        return view('admin.stock.create', compact('products', 'stores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'store_id' => 'nullable|exists:stores,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($request->type === 'out' && $product->stock < $request->quantity) {
            return back()->with('error', "Stok tidak mencukupi. Stok saat ini: {$product->stock}");
        }

        DB::beginTransaction();
        try {
            $stockBefore = $product->stock;
            if ($request->type === 'in') {
                $product->increment('stock', $request->quantity);
            } else {
                $product->decrement('stock', $request->quantity);
            }
            $stockAfter = $product->stock;

            StockTransaction::create([
                'product_id' => $request->product_id,
                'store_id' => $request->store_id,
                'user_id' => Auth::id(),
                'type' => $request->type,
                'quantity' => $request->quantity,
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_type' => 'manual',
                'notes' => $request->notes,
            ]);

            DB::commit();

            AuditService::log('stock_'.$request->type, "Stok {$request->type}: {$product->name} ({$request->quantity} pcs)", 'Product', $product->id);

            return redirect()->route('admin.stock.index')->with('success', 'Stok berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function transferForm()
    {
        $products = Product::where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();
        $transfers = StockTransfer::with(['product', 'fromStore', 'toStore', 'user'])->latest()->paginate(50);
        return view('admin.stock.transfer', compact('products', 'stores', 'transfers'));
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($request->product_id);

        DB::beginTransaction();
        try {
            $transfer = StockTransfer::create([
                'product_id' => $request->product_id,
                'from_store_id' => $request->from_store_id,
                'to_store_id' => $request->to_store_id,
                'user_id' => Auth::id(),
                'quantity' => $request->quantity,
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            StockTransaction::create([
                'product_id' => $request->product_id,
                'store_id' => $request->from_store_id,
                'user_id' => Auth::id(),
                'type' => 'transfer_out',
                'quantity' => $request->quantity,
                'stock_before' => $product->stock,
                'stock_after' => $product->stock,
                'reference_type' => 'transfer',
                'reference_id' => $transfer->id,
                'notes' => 'Transfer ke toko: ' . Store::find($request->to_store_id)->name . ($request->notes ? ' - ' . $request->notes : ''),
            ]);

            StockTransaction::create([
                'product_id' => $request->product_id,
                'store_id' => $request->to_store_id,
                'user_id' => Auth::id(),
                'type' => 'transfer_in',
                'quantity' => $request->quantity,
                'stock_before' => 0,
                'stock_after' => 0,
                'reference_type' => 'transfer',
                'reference_id' => $transfer->id,
                'notes' => 'Transfer dari toko: ' . Store::find($request->from_store_id)->name . ($request->notes ? ' - ' . $request->notes : ''),
            ]);

            DB::commit();

            AuditService::log('stock_transfer', "Transfer stok: {$product->name} ({$request->quantity} pcs) dari {$transfer->fromStore->name} ke {$transfer->toStore->name}", 'StockTransfer', $transfer->id);

            return redirect()->route('admin.stock.transfer')->with('success', 'Transfer stok berhasil.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function updateMinStock(Request $request, Product $product)
    {
        $request->validate(['min_stock' => 'required|integer|min:0']);
        $product->update(['min_stock' => $request->min_stock]);
        return back()->with('success', 'Minimal stok berhasil diperbarui.');
    }
}
