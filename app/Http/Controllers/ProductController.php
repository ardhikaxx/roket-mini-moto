<?php
namespace App\Http\Controllers;
use App\Models\{Product, Category, ProductPriceHistory};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\AuditService;

class ProductController extends Controller
{
    public function index() {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create() {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'show_on_landing' => 'boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('products', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['show_on_landing'] = $request->has('show_on_landing');

        $product = Product::create($validated);
        AuditService::log('create_product', 'Produk ' . $product->name . ' berhasil ditambahkan.', 'Product', $product->id);
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function show(Product $product) {
        $product->load(['category', 'priceHistories.user', 'salesReportItems.salesReport']);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product) {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|unique:products,sku,'.$product->id,
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            if ($product->photo) Storage::disk('public')->delete($product->photo);
            $validated['photo'] = $request->file('photo')->store('products', 'public');
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['show_on_landing'] = $request->has('show_on_landing');

        $oldPrice = $product->price;
        $product->update($validated);

        if ($oldPrice != $product->price) {
            ProductPriceHistory::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'old_price' => $oldPrice,
                'new_price' => $product->price,
            ]);
            AuditService::log('change_price', 'Harga produk ' . $product->name . ' berubah: Rp ' . number_format($oldPrice,0,',','.') . ' -> Rp ' . number_format($product->price,0,',','.'), 'Product', $product->id);
        }

        AuditService::log('update_product', 'Produk ' . $product->name . ' diperbarui.', 'Product', $product->id);
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product) {
        $product->update(['is_active' => false, 'show_on_landing' => false]);
        AuditService::log('deactivate_product', 'Produk ' . $product->name . ' dinonaktifkan.', 'Product', $product->id);
        return redirect()->route('admin.products.index')->with('success', 'Produk dinonaktifkan.');
    }

    public function activate(Product $product) {
        $product->update(['is_active' => true]);
        AuditService::log('activate_product', 'Produk ' . $product->name . ' diaktifkan kembali.', 'Product', $product->id);
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diaktifkan.');
    }
}
