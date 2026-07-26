@extends('layouts.admin')
@section('title', 'Analitik Omzet Penjualan')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Analitik & Omzet</span>
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

<div class="page-header stagger-1">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-chart-line text-primary me-2"></i>Analitik & Performa Bisnis</h1>
            <p class="page-subtitle text-muted" style="font-size:13px;">Tinjauan menyeluruh terhadap data penjualan, omzet, dan persebaran produk.</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <button class="btn btn-outline-secondary px-3 py-2" style="font-weight:600;border:1px solid var(--border-light);" onclick="window.print()">
                <i class="fa-solid fa-print me-2 text-muted"></i> Cetak Analitik
            </button>
        </div>
    </div>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-4 mb-5 stagger-1">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); background:linear-gradient(135deg, var(--primary) 0%, var(--primary-700) 100%); color:white;">
            <div class="card-body p-4 position-relative overflow-hidden">
                <i class="fa-solid fa-sack-dollar position-absolute" style="font-size:120px; right:-20px; bottom:-20px; opacity:0.1; transform:rotate(-15deg);"></i>
                <div class="d-flex align-items-center justify-content-between mb-3 position-relative z-index-1">
                    <span class="fw-bold" style="font-size:12px; text-transform:uppercase; letter-spacing:1px; opacity:0.9;">Total Omzet Disetujui</span>
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-wallet"></i></div>
                </div>
                <h3 class="fw-bold mb-0 position-relative z-index-1" style="font-size:26px;">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:1px;">Transaksi Sukses</span>
                    <div style="width:36px;height:36px;border-radius:10px;background:var(--success-50);color:var(--success);display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-file-invoice"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-1" style="font-size:26px;">{{ number_format($totalTransactions, 0, ',', '.') }}</h3>
                <div class="text-muted fw-semibold" style="font-size:12px;">Berkas Laporan</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:1px;">Produk Terjual</span>
                    <div style="width:36px;height:36px;border-radius:10px;background:var(--info-50);color:var(--info);display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-box-open"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-1" style="font-size:26px;">{{ number_format($totalItems, 0, ',', '.') }}</h3>
                <div class="text-muted fw-semibold" style="font-size:12px;">Unit Barang</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:1px;">Rata-rata Transaksi</span>
                    <div style="width:36px;height:36px;border-radius:10px;background:var(--warning-50);color:var(--warning);display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-scale-balanced"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-1" style="font-size:26px;">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</h3>
                <div class="text-muted fw-semibold" style="font-size:12px;">Per Laporan</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5 stagger-2">
    {{-- Bar Chart: Omzet per Toko --}}
    <div class="col-12 col-xl-8">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-chart-bar text-primary me-2"></i> Perbandingan Omzet per Cabang
                </h5>
            </div>
            <div class="card-body p-4">
                <div style="position:relative; height:320px; width:100%;">
                    <canvas id="omzetChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Doughnut Chart: Distribusi Kategori --}}
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-chart-pie text-warning me-2"></i> Distribusi Kategori
                </h5>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <div style="position:relative; height:280px; width:100%;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Table: Detail Omzet --}}
<div class="card shadow-sm border-0 mb-4 stagger-3" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-header bg-white p-4 border-bottom border-light">
        <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
            <i class="fa-solid fa-table-list text-primary me-2"></i> Detail Kontribusi per Cabang
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-container border-0">
            <table class="table align-middle table-hover" style="margin:0;">
                <thead style="background: var(--neutral-50);">
                    <tr>
                        <th style="padding:16px 24px; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border-light);">Nama Cabang</th>
                        <th class="text-center" style="padding:16px 24px; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border-light);">Volume Transaksi</th>
                        <th class="text-end" style="padding:16px 24px; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border-light);">Total Omzet Disetor</th>
                        <th class="text-end" style="padding:16px 24px; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border-light); width:25%;">Kontribusi (%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($storeOmzet as $so)
                    <tr style="transition:all 0.2s;">
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;border-radius:8px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:14px;"><i class="fa-solid fa-store"></i></div>
                                <span class="fw-bold text-dark" style="font-size:14px;">{{ $so->store->name ?? 'Cabang Tidak Diketahui' }}</span>
                            </div>
                        </td>
                        <td class="text-center" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <span class="badge bg-neutral-100 text-dark px-3 py-2 border" style="font-size:12px; font-weight:700;">{{ $so->count }} Laporan</span>
                        </td>
                        <td class="text-end fw-bold text-primary" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100); font-size:15px;">
                            Rp {{ number_format($so->total,0,',','.') }}
                        </td>
                        <td class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="d-flex align-items-center justify-content-end gap-3">
                                <div style="flex-grow:1; max-width:120px; height:8px; background:var(--neutral-100); border-radius:4px; overflow:hidden;">
                                    <div style="height:100%; border-radius:4px; background:linear-gradient(90deg, var(--primary), var(--primary-400)); width:{{ $totalOmzet > 0 ? round($so->total/$totalOmzet*100) : 0 }}%;"></div>
                                </div>
                                <span class="fw-bold text-dark" style="font-size:13px; min-width:35px;">{{ $totalOmzet > 0 ? round($so->total/$totalOmzet*100, 1) : 0 }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div style="width:64px;height:64px;border-radius:50%;background:var(--neutral-100);color:var(--neutral-400);display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 16px;"><i class="fa-solid fa-chart-simple"></i></div>
                            <p class="text-muted fw-semibold m-0">Belum ada data analitik yang dapat ditampilkan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    .sidebar, .navbar-top, .page-actions, .card-header .btn { display: none !important; }
    .main-content { margin: 0 !important; padding: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; break-inside: avoid; }
    canvas { max-width: 100% !important; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Omzet Bar Chart ---
    const storeLabels = []; 
    const storeData = []; 
    const storeColors = ['#e63946','#f59e0b','#3b82f6','#10b981','#8b5cf6','#ec4899','#14b8a6','#f97316'];
    
    @foreach($storeOmzet as $so)
        storeLabels.push('{{ $so->store->name ?? 'Unknown' }}');
        storeData.push({{ $so->total }});
    @endforeach

    if (document.getElementById('omzetChart')) {
        new Chart(document.getElementById('omzetChart'), {
            type: 'bar',
            data: { 
                labels: storeLabels, 
                datasets: [{ 
                    label: 'Total Omzet Disetor', 
                    data: storeData, 
                    backgroundColor: storeColors.slice(0, storeData.length), 
                    borderRadius: 6,
                    barPercentage: 0.6
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { display: false }, 
                    tooltip: { 
                        backgroundColor: 'rgba(25, 30, 36, 0.95)',
                        titleFont: { family: 'Inter', size: 13, weight: '600' },
                        bodyFont: { family: 'Inter', size: 14, weight: '700' },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: { label: (ctx) => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID') } 
                    } 
                }, 
                scales: { 
                    x: { 
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 12 }, color: '#64748b' }
                    }, 
                    y: { 
                        grid: { color: '#f1f5f9', borderDash: [5, 5] }, 
                        ticks: { 
                            font: { family: 'Inter', size: 12 }, color: '#64748b',
                            callback: (v) => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1) + 'M' : (v/1000).toFixed(0) + 'K') 
                        } 
                    } 
                } 
            }
        });
    }

    // --- Category Doughnut Chart ---
    const catLabels = []; 
    const catData = []; 
    const catColors = ['#e63946','#f59e0b','#3b82f6','#10b981','#8b5cf6','#ec4899','#14b8a6','#f97316','#64748b','#06b6d4'];
    
    @foreach($catSummary as $name => $data)
        catLabels.push('{{ $name }}');
        catData.push({{ $data['amount'] }});
    @endforeach

    if (document.getElementById('categoryChart') && catData.length > 0) {
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: { 
                labels: catLabels, 
                datasets: [{ 
                    data: catData, 
                    backgroundColor: catColors.slice(0, catData.length), 
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                cutout: '65%',
                plugins: { 
                    legend: { 
                        position: 'bottom', 
                        labels: { font: { family: 'Inter', size: 12, weight: '500' }, padding: 15, usePointStyle: true, pointStyle: 'circle' } 
                    },
                    tooltip: {
                        backgroundColor: 'rgba(25, 30, 36, 0.95)',
                        titleFont: { family: 'Inter', size: 13, weight: '600' },
                        bodyFont: { family: 'Inter', size: 14, weight: '700' },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: { label: (ctx) => ' Rp ' + ctx.parsed.toLocaleString('id-ID') }
                    }
                } 
            }
        });
    }
});
</script>
@endsection
