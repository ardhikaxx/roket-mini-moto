<?php
namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\AuditService;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function show(Category $category) {
        $category->load(['products' => fn($q) => $q->where('is_active', true)]);
        return view('admin.categories.show', compact('category'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255|unique:categories,name']);
        $category = Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);
        AuditService::log('create_category', 'Kategori ' . $category->name . ' berhasil ditambahkan.', 'Category', $category->id);
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category) {
        $request->validate(['name' => 'required|string|max:255|unique:categories,name,'.$category->id]);
        $oldName = $category->name;
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);
        AuditService::log('update_category', 'Kategori ' . $oldName . ' diubah menjadi ' . $category->name, 'Category', $category->id);
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diubah.');
    }

    public function destroy(Category $category) {
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Kategori masih memiliki ' . $category->products()->count() . ' produk. Pindahkan produk terlebih dahulu.');
        }
        AuditService::log('delete_category', 'Kategori ' . $category->name . ' dihapus.', 'Category', $category->id);
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
