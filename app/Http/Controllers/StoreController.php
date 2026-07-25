<?php
namespace App\Http\Controllers;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index() {
        $stores = Store::withCount('users')->latest()->get();
        return view('admin.stores.index', compact('stores'));
    }

    public function create() {
        return view('admin.stores.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'code' => 'required|string|unique:stores,code',
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'required|string',
            'is_active' => 'boolean'
        ]);
        $validated['is_active'] = $request->has('is_active');
        Store::create($validated);
        return redirect()->route('admin.stores.index')->with('success', 'Toko berhasil ditambahkan.');
    }

    public function edit(Store $store) {
        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store) {
        $validated = $request->validate([
            'code' => 'required|string|unique:stores,code,'.$store->id,
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'required|string',
        ]);
        $validated['is_active'] = $request->has('is_active');
        $store->update($validated);
        return redirect()->route('admin.stores.index')->with('success', 'Toko berhasil diupdate.');
    }

    public function destroy(Store $store) {
        $store->update(['is_active' => false]);
        return redirect()->route('admin.stores.index')->with('success', 'Toko dinonaktifkan.');
    }
}