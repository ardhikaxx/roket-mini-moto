<?php
namespace App\Http\Controllers;
use App\Models\Store;
use Illuminate\Http\Request;
use App\Services\AuditService;

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
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'operational_hours' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = \App\Helpers\FileUploadHelper::upload($request->file('photo'), 'stores');
        }
        $validated['is_active'] = $request->has('is_active');
        $store = Store::create($validated);
        AuditService::log('create_store', 'Toko ' . $store->name . ' berhasil ditambahkan.', 'Store', $store->id);
        return redirect()->route('admin.stores.index')->with('success', 'Toko berhasil ditambahkan.');
    }

    public function show(Store $store) {
        $store->load(['users' => fn($q) => $q->withCount('salesReports'), 'salesReports' => fn($q) => $q->where('status', 'disetujui')]);
        $totalOmzet = $store->salesReports->sum('total_amount');
        $totalReports = $store->salesReports->count();
        $topProducts = \App\Models\SalesReportItem::whereHas('salesReport', fn($q) => $q->where('store_id', $store->id)->where('status', 'disetujui'))
            ->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->with('product')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
        return view('admin.stores.show', compact('store', 'totalOmzet', 'totalReports', 'topProducts'));
    }

    public function edit(Store $store) {
        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store) {
        $validated = $request->validate([
            'code' => 'required|string|unique:stores,code,'.$store->id,
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'operational_hours' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($store->photo) \App\Helpers\FileUploadHelper::delete($store->photo);
            $validated['photo'] = \App\Helpers\FileUploadHelper::upload($request->file('photo'), 'stores');
        }
        $validated['is_active'] = $request->has('is_active');
        $store->update($validated);
        AuditService::log('update_store', 'Toko ' . $store->name . ' diperbarui.', 'Store', $store->id);
        return redirect()->route('admin.stores.index')->with('success', 'Toko berhasil diupdate.');
    }

    public function destroy(Store $store) {
        $store->update(['is_active' => false]);
        AuditService::log('deactivate_store', 'Toko ' . $store->name . ' dinonaktifkan.', 'Store', $store->id);
        return redirect()->route('admin.stores.index')->with('success', 'Toko dinonaktifkan.');
    }
}
