@extends('layouts.admin')
@section('title', 'Admin Dashboard - Roket Mini Moto')

@section('content')
@php
    $totalOmzet = \App\Models\SalesReport::where('status','disetujui')->sum('total_amount');
    $totalApproved = \App\Models\SalesReport::where('status','disetujui')->count();
    $totalPending = \App\Models\SalesReport::where('status','diproses')->count();
    $totalRejected = \App\Models\SalesReport::where('status','ditolak')->count();
    $totalStores = \App\Models\Store::where('is_active', true)->count();
    $totalProducts = \App\Models\Product::where('is_active', true)->count();
    $totalEmployees = \App\Models\User::where('role','karyawan')->where('is_active', true)->count();
    
    $today = now();
    $hour = $today->hour;
    if ($hour < 10) $greeting = 'Selamat Pagi';
    elseif ($hour < 15) $greeting = 'Selamat Siang';
    elseif ($hour < 18) $greeting = 'Selamat Sore';
    else $greeting = 'Selamat Malam';

    $lastMonthOmzet = \App\Models\SalesReport::where('status','disetujui')
        ->whereMonth('created_at', now()->subMonth()->month)->sum('total_amount');
    $omzetChange = $lastMonthOmzet > 0 ? round(($totalOmzet - $lastMonthOmzet) / $lastMonthOmzet * 100, 1) : 0;

    $topProducts = \App\Models\SalesReportItem::whereHas('salesReport', fn($q) => $q->where('status','disetujui'))
        ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(subtotal) as total_amount')
        ->groupBy('product_id')->with('product.category')
        ->orderByDesc('total_qty')->take(5)->get();

    $topStores = \App\Models\SalesReport::where('status','disetujui')
        ->selectRaw('store_id, SUM(total_amount) as total_omzet, COUNT(*) as total_transactions')
        ->groupBy('store_id')->with('store')->orderByDesc('total_omzet')->take(5)->get();

    $recentActivities = \App\Models\AuditLog::with('user')->latest()->take(8)->get();
@endphp

{{-- Page Header Premium --}}
<div class="page-header stagger-1">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title" style="font-size:28px;">{{ $greeting }}, {{ explode(' ', trim(auth()->user()->name))[0] }}! <span style="font-size: 28px; display:inline-block; animation: wave 2s infinite transform-origin: 70% 70%;">👋</span></h1>
            <p class="page-subtitle text-muted" style="font-size:14px;">Ringkasan performa bisnis Anda hari ini, <strong class="text-dark">{{ $today->translatedFormat('d F Y') }}</strong>.</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.products.create') }}" class="btn btn-light px-3 py-2" style="font-weight:600; border:1px solid var(--border-light);"><i class="fa-solid fa-box-open text-primary me-2"></i> Tambah Produk</a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-light px-3 py-2" style="font-weight:600; border:1px solid var(--border-light);"><i class="fa-solid fa-user-plus text-primary me-2"></i> Tambah Karyawan</a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-primary px-4 py-2" style="font-weight:600; box-shadow:0 4px 12px rgba(230,57,70,0.25);"><i class="fa-solid fa-file-invoice me-2"></i> Review Laporan</a>
        </div>
    </div>
</div>

{{-- KPI Summary Cards --}}
<div class="row g-4 mb-5 stagger-1">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); background:linear-gradient(135deg, var(--primary) 0%, var(--primary-700) 100%); color:white; cursor:pointer; transition:transform 0.2s;" onclick="window.location.href='{{ route('admin.omzet') }}'" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
            <div class="card-body p-4 position-relative overflow-hidden">
                <i class="fa-solid fa-wallet position-absolute" style="font-size:120px; right:-20px; bottom:-20px; opacity:0.1; transform:rotate(-15deg);"></i>
                <div class="d-flex align-items-center justify-content-between mb-3 position-relative z-index-1">
                    <span class="fw-bold" style="font-size:13px; text-transform:uppercase; letter-spacing:1px; opacity:0.9;">Total Omzet</span>
                    <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-wallet"></i></div>
                </div>
                <h3 class="fw-bold mb-2 position-relative z-index-1" style="font-size:28px;">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
                <div class="d-flex align-items-center gap-2 position-relative z-index-1" style="font-size:13px;">
                    <span class="badge bg-white text-{{ $omzetChange >= 0 ? 'success' : 'danger' }} rounded-pill px-2">
                        <i class="fa-solid fa-{{ $omzetChange >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }} me-1"></i>{{ abs($omzetChange) }}%
                    </span>
                    <span style="opacity:0.8;">dari bulan lalu</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); cursor:pointer; transition:transform 0.2s;" onclick="window.location.href='{{ route('admin.reports.index', ['status' => 'diproses']) }}'" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:1px;">Menunggu Approval</span>
                    <div style="width:36px;height:36px;border-radius:10px;background:var(--warning-50);color:var(--warning);display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-clock-rotate-left"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-2" style="font-size:28px;">{{ $totalPending }}</h3>
                <div class="text-muted" style="font-size:13px;"><i class="fa-solid fa-file-invoice text-warning me-1"></i> Laporan butuh review segera</div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); cursor:pointer; transition:transform 0.2s;" onclick="window.location.href='{{ route('admin.reports.index', ['status' => 'disetujui']) }}'" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:1px;">Penjualan Disetujui</span>
                    <div style="width:36px;height:36px;border-radius:10px;background:var(--success-50);color:var(--success);display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-check-circle"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-2" style="font-size:28px;">{{ $totalApproved }}</h3>
                <div class="text-muted" style="font-size:13px;">
                    <span class="text-danger fw-semibold"><i class="fa-solid fa-xmark-circle me-1"></i>{{ $totalRejected }}</span> laporan telah ditolak
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); cursor:pointer; transition:transform 0.2s;" onclick="window.location.href='{{ route('admin.stores.index') }}'" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="fw-bold text-muted" style="font-size:12px; text-transform:uppercase; letter-spacing:1px;">Toko & Cabang</span>
                    <div style="width:36px;height:36px;border-radius:10px;background:var(--info-50);color:var(--info);display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-store"></i></div>
                </div>
                <h3 class="fw-bold text-dark mb-2" style="font-size:28px;">{{ $totalStores }}</h3>
                <div class="text-muted" style="font-size:13px;"><i class="fa-solid fa-users text-info me-1"></i> <strong>{{ $totalEmployees }}</strong> karyawan aktif bertugas</div>
            </div>
        </div>
    </div>
</div>

{{-- Main Charts & Store Performace --}}
<div class="row g-4 mb-5 stagger-2">
    <div class="col-12 col-xl-8">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-chart-line text-primary me-2"></i> Tren Omzet Penjualan
                </h5>
                <select id="chartPeriod" class="form-select form-select-sm" style="width:140px; font-weight:600; cursor:pointer;" onchange="loadChart()">
                    <option value="7">7 Hari Terakhir</option>
                    <option value="30" selected>30 Hari Terakhir</option>
                    <option value="90">3 Bulan</option>
                </select>
            </div>
            <div class="card-body p-4">
                <div id="chartSkeleton" class="skeleton skeleton-card w-100" style="height:280px; display:none;"></div>
                <canvas id="omzetChart" height="280"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-trophy text-warning me-2"></i> Performa Cabang Terbaik
                </h5>
            </div>
            <div class="card-body p-4">
                @forelse($topStores as $i => $ts)
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:40px;height:40px;border-radius:10px;background:var(--{{ $i==0 ? 'warning' : 'neutral' }}-100);color:var(--{{ $i==0 ? 'warning' : 'neutral' }}-700);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;flex-shrink:0;">
                        @if($i == 0) <i class="fa-solid fa-crown"></i> @else #{{ $i + 1 }} @endif
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-dark fw-bold text-truncate" style="font-size:14px; max-width:150px;">{{ $ts->store->name ?? 'Cabang Tidak Diketahui' }}</span>
                            <span class="text-primary fw-bold" style="font-size:14px;">Rp {{ number_format($ts->total_omzet,0,',','.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted" style="font-size:11px;">{{ $ts->total_transactions }} Laporan Transaksi</span>
                            <span class="text-muted fw-bold" style="font-size:11px;">{{ $totalOmzet > 0 ? round($ts->total_omzet / $totalOmzet * 100) : 0 }}%</span>
                        </div>
                        <div class="progress" style="height:6px;background:var(--neutral-100);border-radius:4px;">
                            <div class="progress-bar bg-primary" style="width:{{ $totalOmzet > 0 ? min(100, round($ts->total_omzet / $totalOmzet * 100)) : 0 }}%;border-radius:4px;"></div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--neutral-100);color:var(--neutral-400);display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 16px;"><i class="fa-solid fa-store-slash"></i></div>
                    <p class="text-muted fw-semibold m-0">Belum ada data penjualan tercatat.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Top Products & Recent Activity --}}
<div class="row g-4 mb-5 stagger-3">
    <div class="col-12 col-xl-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-box text-success me-2"></i> 5 Produk Paling Laris
                </h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-light" style="font-weight:600;">Lihat Katalog</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle m-0">
                    <tbody>
                        @forelse($topProducts as $tp)
                        <tr>
                            <td style="padding:16px; border-bottom:1px solid var(--border-light);">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:48px;height:48px;border-radius:10px;background:var(--neutral-100);overflow:hidden;flex-shrink:0;">
                                        @if($tp->product && $tp->product->photo)
                                            <img src="{{ asset('storage/'.$tp->product->photo) }}" style="width:100%;height:100%;object-fit:cover;">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-motorcycle"></i></div>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark text-truncate mb-1" style="font-size:14px; max-width:200px;">{{ $tp->product->name ?? 'Produk Dihapus' }}</div>
                                        <div class="text-muted" style="font-size:12px;">{{ $tp->product->category->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center" style="padding:16px; border-bottom:1px solid var(--border-light);">
                                <span class="badge badge-success rounded-pill px-3 py-1" style="font-weight:600;">{{ $tp->total_qty }} Unit</span>
                            </td>
                            <td class="text-end fw-bold text-dark" style="padding:16px; border-bottom:1px solid var(--border-light); font-size:14px;">
                                Rp {{ number_format($tp->total_amount,0,',','.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <div style="width:64px;height:64px;border-radius:50%;background:var(--neutral-100);color:var(--neutral-400);display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 16px;"><i class="fa-solid fa-box-open"></i></div>
                                <p class="text-muted fw-semibold m-0">Belum ada penjualan produk tercatat.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-xl-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-bolt text-danger me-2"></i> Catatan Log Aktivitas
                </h5>
                <a href="{{ route('admin.audit-log') }}" class="btn btn-sm btn-light" style="font-weight:600;">Lihat Log Lengkap</a>
            </div>
            <div class="card-body p-4 bg-neutral-50">
                <div class="timeline" style="position:relative; padding-left:24px;">
                    <div style="position:absolute; left:7px; top:0; bottom:0; width:2px; background:var(--border-light);"></div>
                    @forelse($recentActivities as $log)
                        @php
                            $color = 'primary'; $icon = 'info';
                            if(in_array($log->action, ['approve_report','login'])) { $color = 'success'; $icon = 'check'; }
                            elseif(in_array($log->action, ['reject_report','logout'])) { $color = 'danger'; $icon = 'xmark'; }
                            elseif(in_array($log->action, ['create_report'])) { $color = 'warning'; $icon = 'file-invoice'; }
                            elseif(in_array($log->action, ['create_product','update_product'])) { $color = 'info'; $icon = 'box'; }
                        @endphp
                        <div class="mb-4 position-relative">
                            <div class="timeline-dot shadow-sm" style="position:absolute; left:-29px; top:4px; width:12px; height:12px; border-radius:50%; background:var(--{{$color}}); border:2px solid white; z-index:2; box-shadow:0 0 0 4px var(--{{$color}}-50) !important;"></div>
                            <div class="bg-white p-3 rounded-3 shadow-sm border border-light">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="fw-bold text-dark" style="font-size:13px;">
                                        @if($log->user) {{ $log->user->name }} @else <span class="text-muted">Sistem</span> @endif
                                    </div>
                                    <div class="text-muted font-monospace" style="font-size:11px;">{{ $log->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="text-secondary" style="font-size:13px; line-height:1.5;">{{ $log->description }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <div style="width:48px;height:48px;border-radius:50%;background:var(--neutral-200);color:var(--neutral-400);display:flex;align-items:center;justify-content:center;font-size:18px;margin:0 auto 12px;"><i class="fa-solid fa-history"></i></div>
                            <p class="text-muted fw-semibold m-0" style="font-size:13px;">Sistem belum mencatat aktivitas apa pun.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes wave {
    0% { transform: rotate(0deg); }
    10% { transform: rotate(14deg); }
    20% { transform: rotate(-8deg); }
    30% { transform: rotate(14deg); }
    40% { transform: rotate(-4deg); }
    50% { transform: rotate(10deg); }
    60% { transform: rotate(0deg); }
    100% { transform: rotate(0deg); }
}
</style>
@endsection

@push('scripts')
<script>
function loadChart() {
    const days = document.getElementById('chartPeriod').value;
    const ctx = document.getElementById('omzetChart');
    const skeleton = document.getElementById('chartSkeleton');
    
    ctx.style.display = 'none';
    skeleton.style.display = 'block';

    fetch(`{{ route('admin.omzet') }}/chart-data?days=${days}`)
        .then(r => r.json())
        .then(data => {
            skeleton.style.display = 'none';
            ctx.style.display = 'block';

            if (window.omzetChartInstance) window.omzetChartInstance.destroy();
            const canvasCtx = ctx.getContext('2d');
            const gradient = canvasCtx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(230,57,70,0.25)'); // Primary color gradient
            gradient.addColorStop(1, 'rgba(230,57,70,0)');

            window.omzetChartInstance = new Chart(canvasCtx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Omzet Penjualan',
                        data: data.values,
                        borderColor: '#e63946', // Primary var(--primary)
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#e63946',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#e63946',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(25, 30, 36, 0.95)',
                            titleColor: '#fff',
                            bodyColor: '#e2e8f0',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: { family: 'Inter', size: 13, weight: '600' },
                            bodyFont: { family: 'Inter', size: 14, weight: '700' },
                            displayColors: false,
                            callbacks: {
                                label: (context) => 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Inter', size: 12 }, color: '#64748b', maxTicksLimit: 8 }
                        },
                        y: {
                            grid: { color: '#f1f5f9', borderDash: [5, 5] },
                            ticks: {
                                font: { family: 'Inter', size: 12 },
                                color: '#64748b',
                                callback: (v) => 'Rp ' + (v >= 1000000 ? (v / 1000000).toFixed(1) + 'M' : (v / 1000).toFixed(0) + 'K')
                            }
                        }
                    }
                }
            });
        });
}
document.addEventListener('DOMContentLoaded', loadChart);
</script>
@endpush
