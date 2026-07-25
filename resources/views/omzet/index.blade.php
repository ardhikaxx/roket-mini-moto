@extends('layouts.admin')
@section('title', 'Omzet & Analytics')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Omzet & Analytics</span>
@endsection
@section('content')
@php
    $totalOmzet = \App\Models\SalesReport::where('status','disetujui')->sum('total_amount');
    $totalTransactions = \App\Models\SalesReport::where('status','disetujui')->count();
    $totalItems = \App\Models\SalesReport::where('status','disetujui')->sum('total_items');
    $avgTransaction = $totalTransactions > 0 ? $totalOmzet / $totalTransactions : 0;

    $storeOmzet = \App\Models\SalesReport::where('status','disetujui')
        ->selectRaw('store_id, SUM(total_amount) as total, COUNT(*) as count')
        ->groupBy('store_id')->with('store')->orderByDesc('total')->get();

    $catSales = \App\Models\SalesReportItem::whereHas('salesReport', fn($q) => $q->where('status','disetujui'))
        ->whereHas('product.category')
        ->selectRaw('SUM(quantity) as total_qty, SUM(subtotal) as total_amount, product_id')
        ->groupBy('product_id')->with('product.category')->get();

    $catSummary = $catSales->groupBy(fn($i) => $i->product->category->name ?? 'Lainnya')
        ->map(fn($items) => ['qty' => $items->sum('total_qty'), 'amount' => $items->sum('total_amount')]);
@endphp
<div class="page-header"><div class="page-header-row"><div><h1 class="page-title">Omzet & Analytics</h1><p class="page-subtitle">Analisis penjualan bisnis Anda</p></div></div></div>

<div class="row mb-4">
    <div class="col-6 col-md-3"><div class="stat-card" style="padding:14px 18px;cursor:default;"><div class="stat-label">Total Omzet</div><div class="stat-value" style="font-size:22px;">Rp {{ number_format($totalOmzet,0,',','.') }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card" style="padding:14px 18px;cursor:default;"><div class="stat-label">Transaksi</div><div class="stat-value" style="font-size:22px;">{{ $totalTransactions }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card" style="padding:14px 18px;cursor:default;"><div class="stat-label">Produk Terjual</div><div class="stat-value" style="font-size:22px;">{{ $totalItems }}</div></div></div>
    <div class="col-6 col-md-3"><div class="stat-card" style="padding:14px 18px;cursor:default;"><div class="stat-label">Rata-rata Transaksi</div><div class="stat-value" style="font-size:22px;">Rp {{ number_format($avgTransaction,0,',','.') }}</div></div></div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card h-100">
            <div class="card-header"><h5 class="fw-bold mb-0">Omzet per Toko</h5></div>
            <div class="card-body">
                <div style="height:300px;" id="omzetChartContainer">
                    <canvas id="omzetChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header"><h5 class="fw-bold mb-0">Distribusi Kategori</h5></div>
            <div class="card-body">
                <div style="height:300px;"><canvas id="categoryChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="fw-bold mb-0">Detail Omzet per Toko</h5></div>
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table" style="margin:0;">
                <thead><tr><th>Toko</th><th>Transaksi</th><th style="text-align:right;">Total Omzet</th><th style="text-align:right;">Kontribusi</th></tr></thead>
                <tbody>
                    @foreach($storeOmzet as $so)
                    <tr>
                        <td class="fw-semibold">{{ $so->store->name ?? 'Unknown' }}</td>
                        <td>{{ $so->count }}</td>
                        <td style="text-align:right;font-weight:600;">Rp {{ number_format($so->total,0,',','.') }}</td>
                        <td style="text-align:right;">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;">
                                <div style="width:100px;height:6px;background:var(--neutral-100);border-radius:4px;">
                                    <div style="height:100%;border-radius:4px;background:var(--primary);width:{{ $totalOmzet > 0 ? round($so->total/$totalOmzet*100) : 0 }}%;"></div>
                                </div>
                                <span style="font-size:13px;color:var(--text-secondary);">{{ $totalOmzet > 0 ? round($so->total/$totalOmzet*100,1) : 0 }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Omzet bar chart
    const storeLabels = []; const storeData = []; const storeColors = ['#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6'];
    @foreach($storeOmzet as $so)
        storeLabels.push('{{ $so->store->name ?? 'Unknown' }}');
        storeData.push({{ $so->total }});
    @endforeach

    new Chart(document.getElementById('omzetChart'), {
        type: 'bar',
        data: { labels: storeLabels, datasets: [{ label: 'Omzet', data: storeData, backgroundColor: storeColors.slice(0, storeData.length), borderRadius: 6 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') } } }, scales: { x: { grid: { display: false } }, y: { grid: { color: '#f1f5f9' }, ticks: { callback: (v) => 'Rp' + (v/1000000).toFixed(1) + 'jt' } } } }
    });

    // Category donut chart
    const catLabels = []; const catData = []; const catColors = ['#6366f1','#22c55e','#f59e0b','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6','#f97316','#06b6d4'];
    @foreach($catSummary as $name => $data)
        catLabels.push('{{ $name }}');
        catData.push({{ $data['amount'] }});
    @endforeach

    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: { labels: catLabels, datasets: [{ data: catData, backgroundColor: catColors.slice(0, catData.length), borderWidth: 0 }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 12 } } } }
    });
});
</script>
@endsection
