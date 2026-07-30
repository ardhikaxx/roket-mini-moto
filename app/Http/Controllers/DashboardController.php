<?php

namespace App\Http\Controllers;

use App\Models\SalesReport;
use App\Models\SalesReportItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Models\AuditLog;
use App\Models\StockTransaction;
use App\Models\UserStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function getData(Request $request)
    {
        $days = $request->get('days', 30);
        $user = auth()->user();

        if ($user->isKaryawan()) {
            return $this->karyawanData();
        }
        if ($user->isKepalaToko()) {
            return $this->kepalaTokoData($request);
        }
        return $this->adminData($days);
    }

    private function adminData($days)
    {
        $totalOmzet = SalesReport::where('status', 'disetujui')->sum('total_amount');
        $totalApproved = SalesReport::where('status', 'disetujui')->count();
        $totalPending = SalesReport::where('status', 'diproses')->count();
        $totalRejected = SalesReport::where('status', 'ditolak')->count();
        $totalStores = Store::where('is_active', true)->count();
        $totalProducts = Product::where('is_active', true)->count();
        $totalEmployees = User::where('role', 'karyawan')->where('is_active', true)->count();

        $todayOmzet = SalesReport::where('status', 'disetujui')
            ->whereDate('created_at', today())->sum('total_amount');

        $lastMonthOmzet = SalesReport::where('status', 'disetujui')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total_amount');
        $omzetChange = $lastMonthOmzet > 0 ? round(($totalOmzet - $lastMonthOmzet) / $lastMonthOmzet * 100, 1) : 0;

        $monthOmzet = SalesReport::where('status', 'disetujui')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        $omzetTarget = $totalOmzet > 0 ? round(($monthOmzet / max($totalOmzet, 1)) * 100) : 0;

        $lowStock = Product::where('is_active', true)
            ->whereColumn('stock', '<=', 'min_stock')
            ->where('min_stock', '>', 0)
            ->count();

        $omzetTrend = $this->getOmzetTrend($days);

        $categoryDist = SalesReportItem::whereHas('salesReport', fn($q) => $q->where('status', 'disetujui'))
            ->whereHas('product.category')
            ->selectRaw('SUM(subtotal) as total, product_id')
            ->groupBy('product_id')->with('product.category')
            ->get()
            ->groupBy(fn($i) => $i->product->category->name ?? 'Lainnya')
            ->map(fn($items) => round($items->sum('total')))
            ->sortDesc()
            ->take(6);

        $topStores = SalesReport::where('status', 'disetujui')
            ->selectRaw('store_id, SUM(total_amount) as total_omzet, COUNT(*) as total_transactions')
            ->groupBy('store_id')->with('store')->orderByDesc('total_omzet')->take(5)->get();

        $topProducts = SalesReportItem::whereHas('salesReport', fn($q) => $q->where('status', 'disetujui'))
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(subtotal) as total_amount')
            ->groupBy('product_id')->with('product.category')
            ->orderByDesc('total_qty')->take(5)->get();

        $recentActivities = AuditLog::with('user')->latest()->take(8)->get();

        $statusCounts = SalesReport::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $todayTransactions = SalesReport::whereDate('created_at', today())->count();

        $stockMutations = StockTransaction::whereDate('created_at', today())->count();

        return response()->json([
            'kpi' => [
                'totalOmzet' => (int) $totalOmzet,
                'todayOmzet' => (int) $todayOmzet,
                'totalApproved' => $totalApproved,
                'totalPending' => $totalPending,
                'totalRejected' => $totalRejected,
                'totalStores' => $totalStores,
                'totalProducts' => $totalProducts,
                'totalEmployees' => $totalEmployees,
                'omzetChange' => $omzetChange,
                'omzetTarget' => $omzetTarget,
                'lowStock' => $lowStock,
                'todayTransactions' => $todayTransactions,
                'stockMutations' => $stockMutations,
                'monthOmzet' => (int) $monthOmzet,
            ],
            'omzetTrend' => $omzetTrend,
            'categoryDist' => $categoryDist,
            'topStores' => $topStores->map(fn($ts) => [
                'name' => $ts->store->name ?? 'Tidak Diketahui',
                'omzet' => (int) $ts->total_omzet,
                'transactions' => $ts->total_transactions,
                'percentage' => $totalOmzet > 0 ? round($ts->total_omzet / $totalOmzet * 100, 1) : 0,
            ]),
            'topProducts' => $topProducts->map(fn($tp) => [
                'id' => $tp->product_id,
                'name' => $tp->product->name ?? 'Produk Dihapus',
                'category' => $tp->product->category->name ?? '-',
                'photo' => $tp->product->photo ? asset('storage/' . $tp->product->photo) : null,
                'qty' => (int) $tp->total_qty,
                'amount' => (int) $tp->total_amount,
            ]),
            'activities' => $recentActivities->map(fn($log) => [
                'user' => $log->user->name ?? 'Sistem',
                'action' => $log->action,
                'description' => $log->description,
                'time' => $log->created_at->diffForHumans(),
                'color' => in_array($log->action, ['approve_report', 'login']) ? 'success'
                    : (in_array($log->action, ['reject_report', 'logout']) ? 'danger'
                    : (in_array($log->action, ['create_report']) ? 'warning'
                    : (in_array($log->action, ['create_product', 'update_product']) ? 'info' : 'primary'))),
            ]),
            'statusCounts' => $statusCounts + ['disetujui' => 0, 'diproses' => 0, 'ditolak' => 0],
            'timestamp' => now()->format('H:i:s'),
            'date' => now()->translatedFormat('l, d F Y'),
        ]);
    }

    private function getOmzetTrend($days)
    {
        $startDate = now()->subDays($days);
        $data = SalesReport::where('status', 'disetujui')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')->orderBy('date')->get();

        $labels = [];
        $values = [];
        $period = now()->subDays($days)->copy();
        $dataMap = $data->keyBy('date');

        while ($period <= now()) {
            $dateStr = $period->format('Y-m-d');
            $labels[] = $period->format('d M');
            $values[] = (int) ($dataMap[$dateStr]->total ?? 0);
            $period->addDay();
        }

        return ['labels' => $labels, 'values' => $values];
    }

    private function karyawanData()
    {
        $user = auth()->user();
        $totalToday = SalesReport::where('user_id', $user->id)->whereDate('created_at', today())->count();
        $totalPending = SalesReport::where('user_id', $user->id)->where('status', 'diproses')->count();
        $totalApproved = SalesReport::where('user_id', $user->id)->where('status', 'disetujui')->count();
        $totalRejected = SalesReport::where('user_id', $user->id)->where('status', 'ditolak')->count();
        $totalOmzet = SalesReport::where('user_id', $user->id)->where('status', 'disetujui')->sum('total_amount');

        $recentReports = SalesReport::where('user_id', $user->id)->with('store')->latest()->take(6)->get()->map(fn($r) => [
            'id' => $r->id,
            'number' => $r->report_number,
            'store' => $r->store->name ?? '-',
            'amount' => (int) $r->total_amount,
            'items' => $r->items()->sum('quantity'),
            'status' => $r->status,
            'rejection_reason' => $r->rejection_reason,
            'time' => $r->created_at->format('d M Y, H:i'),
        ]);

        $todayOmzet = SalesReport::where('user_id', $user->id)->where('status', 'disetujui')
            ->whereDate('created_at', today())->sum('total_amount');

        return response()->json([
            'kpi' => [
                'totalToday' => $totalToday,
                'totalPending' => $totalPending,
                'totalApproved' => $totalApproved,
                'totalRejected' => $totalRejected,
                'totalOmzet' => (int) $totalOmzet,
                'todayOmzet' => (int) $todayOmzet,
            ],
            'recentReports' => $recentReports,
            'rejectedCount' => $totalRejected,
            'timestamp' => now()->format('H:i:s'),
        ]);
    }

    private function kepalaTokoData(Request $request)
    {
        $user = auth()->user();
        $assignedStoreIds = UserStore::where('user_id', $user->id)->pluck('store_id');
        $selectedStoreId = $request->get('store_id', $assignedStoreIds->first());

        if (!$selectedStoreId || !in_array($selectedStoreId, $assignedStoreIds->toArray())) {
            return response()->json(['kpi' => [], 'error' => 'Tidak ada toko ditugaskan']);
        }

        $totalOmzet = SalesReport::where('store_id', $selectedStoreId)->where('status', 'disetujui')->sum('total_amount');
        $todayReports = SalesReport::where('store_id', $selectedStoreId)->whereDate('created_at', today())->count();
        $pendingReports = SalesReport::where('store_id', $selectedStoreId)->where('status', 'diproses')->count();
        $activeEmployees = UserStore::where('store_id', $selectedStoreId)
            ->whereHas('user', fn($q) => $q->where('role', 'karyawan')->where('is_active', true))->count();
        $rejectedReports = SalesReport::where('store_id', $selectedStoreId)->where('status', 'ditolak')->count();

        $statusCounts = SalesReport::where('store_id', $selectedStoreId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')->pluck('count', 'status')->toArray();

        $topProducts = SalesReportItem::whereHas('salesReport', fn($q) => $q->where('store_id', $selectedStoreId)->where('status', 'disetujui'))
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(subtotal) as total_amount')
            ->groupBy('product_id')->with('product.category')
            ->orderByDesc('total_qty')->take(5)->get();

        $topEmployees = SalesReport::where('store_id', $selectedStoreId)->where('status', 'disetujui')
            ->selectRaw('user_id, SUM(total_amount) as total_omzet, COUNT(*) as total_reports')
            ->groupBy('user_id')->with('user')->orderByDesc('total_omzet')->take(5)->get();

        $recentReports = SalesReport::with('user')->where('store_id', $selectedStoreId)->latest()->take(5)->get()->map(fn($r) => [
            'id' => $r->id,
            'number' => $r->report_number,
            'user' => $r->user->name ?? 'Unknown',
            'status' => $r->status,
            'amount' => (int) $r->total_amount,
            'time' => $r->created_at->diffForHumans(),
        ]);

        $totalApproved = $statusCounts['disetujui'] ?? 0;
        $monthOmzet = SalesReport::where('store_id', $selectedStoreId)->where('status', 'disetujui')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
            ->sum('total_amount');

        return response()->json([
            'kpi' => [
                'totalOmzet' => (int) $totalOmzet,
                'monthOmzet' => (int) $monthOmzet,
                'todayReports' => $todayReports,
                'pendingReports' => $pendingReports,
                'rejectedReports' => $rejectedReports,
                'activeEmployees' => $activeEmployees,
                'totalApproved' => $totalApproved,
            ],
            'statusCounts' => $statusCounts + ['disetujui' => 0, 'diproses' => 0, 'ditolak' => 0],
            'topProducts' => $topProducts->map(fn($tp) => [
                'name' => $tp->product->name ?? 'Dihapus',
                'category' => $tp->product->category->name ?? '-',
                'qty' => (int) $tp->total_qty,
                'amount' => (int) $tp->total_amount,
            ]),
            'topEmployees' => $topEmployees->map(fn($te) => [
                'name' => $te->user->name ?? 'Unknown',
                'photo' => $te->user->photo ? asset('storage/' . $te->user->photo) : null,
                'reports' => $te->total_reports,
                'omzet' => (int) $te->total_omzet,
            ]),
            'recentReports' => $recentReports,
            'timestamp' => now()->format('H:i:s'),
        ]);
    }
}
