<?php
namespace App\Http\Controllers;
use App\Models\{SalesReport, SalesReportItem, Product, Store};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = SalesReport::with(['items.product', 'store', 'user'])
            ->where('status', 'disetujui');

        if ($user->isKepalaToko()) {
            $query->whereIn('store_id', $user->stores->pluck('id'));
        }

        if ($request->filled('store_id') && $request->store_id !== 'all') {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('transaction_date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('transaction_date', $request->year);
        }

        $reports = $query->get();
        $stores = Store::where('is_active', true)->get();

        $totalRevenue = 0;
        $totalCost = 0;
        $totalProfit = 0;
        $totalProfitPercent = 0;
        $reportProfits = [];

        foreach ($reports as $report) {
            $reportRevenue = $report->total_amount;
            $reportCost = 0;

            foreach ($report->items as $item) {
                if ($item->product && $item->product->cost_price) {
                    $reportCost += $item->product->cost_price * $item->quantity;
                }
            }

            $reportProfit = $reportRevenue - $reportCost;
            $reportPercent = $reportRevenue > 0 ? round(($reportProfit / $reportRevenue) * 100, 1) : 0;

            $totalRevenue += $reportRevenue;
            $totalCost += $reportCost;
            $totalProfit += $reportProfit;

            $reportProfits[] = [
                'report' => $report,
                'revenue' => $reportRevenue,
                'cost' => $reportCost,
                'profit' => $reportProfit,
                'percent' => $reportPercent,
            ];
        }

        $totalProfitPercent = $totalRevenue > 0 ? round(($totalProfit / $totalRevenue) * 100, 1) : 0;

        return view('admin.profit-loss.index', compact(
            'reportProfits', 'stores',
            'totalRevenue', 'totalCost', 'totalProfit', 'totalProfitPercent'
        ));
    }
}
