<?php
namespace App\Http\Controllers;
use App\Models\{SalesTarget, Store, User, SalesReport};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditService;

class SalesTargetController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $targets = SalesTarget::with(['store', 'user'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $stores = Store::where('is_active', true)->get();
        $employees = User::where('role', 'karyawan')->where('is_active', true)->get();

        $achievedData = [];
        foreach ($targets as $target) {
            $achievedData[$target->id] = $target->achieved;
        }

        return view('admin.sales-targets.index', compact('targets', 'stores', 'employees', 'month', 'year', 'achievedData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_id' => 'nullable|exists:stores,id',
            'user_id' => 'nullable|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2099',
            'target_amount' => 'required|numeric|min:0',
        ]);

        SalesTarget::updateOrCreate(
            [
                'store_id' => $request->store_id ?: null,
                'user_id' => $request->user_id ?: null,
                'month' => $request->month,
                'year' => $request->year,
            ],
            ['target_amount' => $request->target_amount]
        );

        AuditService::log('create_target', 'Target penjualan ditetapkan: ' . ($request->store_id ? 'Toko ' . Store::find($request->store_id)->name : '') . ($request->user_id ? ' Karyawan ' . User::find($request->user_id)->name : '') . ' Bulan ' . $request->month . '/' . $request->year);

        return back()->with('success', 'Target penjualan berhasil ditetapkan.');
    }

    public function destroy(SalesTarget $target)
    {
        $target->delete();
        return back()->with('success', 'Target penjualan dihapus.');
    }
}
