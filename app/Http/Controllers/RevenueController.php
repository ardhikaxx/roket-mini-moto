<?php
namespace App\Http\Controllers;
use App\Models\SalesReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    public function index() {
        return view('omzet.index');
    }

    public function chartData(Request $request) {
        $days = $request->get('days', 30);
        $startDate = now()->subDays($days);
        
        $data = SalesReport::where('status', 'disetujui')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

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

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }
}
