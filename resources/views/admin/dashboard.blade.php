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

{{-- Page Header --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 stagger-1">
    <div>
        <h1 class="page-title">{{ $greeting }}, {{ explode(' ', trim(auth()->user()->name))[0] }}! <span style="font-size: 24px;">👋</span></h1>
        <p class="page-subtitle">Berikut adalah ringkasan performa bisnis Anda hari ini, {{ $today->translatedFormat('d F Y') }}.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('admin.products.create') }}" class="btn btn-secondary btn-sm"><i class="fa-solid fa-plus"></i> Produk</a>
        <a href="{{ route('admin.users.create') }}" class="btn btn-secondary btn-sm"><i class="fa-solid fa-plus"></i> Karyawan</a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-primary btn-sm"><i class="fa-solid fa-file-invoice"></i> Review Laporan</a>
    </div>
</div>

{{-- Statistic Cards --}}
<div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-lg-3 stagger-1">
        <div class="stat-card" onclick="window.location.href='{{ route('admin.omzet') }}'">
            <i class="fa-solid fa-wallet stat-card-bg text-primary"></i>
            <div class="stat-card-header">
                <span class="stat-card-title">Total Omzet</span>
                <div class="stat-card-icon icon-primary"><i class="fa-solid fa-wallet"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $totalOmzet }}">0</div>
                <div class="stat-card-trend {{ $omzetChange >= 0 ? 'up' : 'down' }}">
                    <i class="fa-solid fa-{{ $omzetChange >= 0 ? 'arrow-trend-up' : 'arrow-trend-down' }}"></i>
                    <span>{{ abs($omzetChange) }}% dari bulan lalu</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 stagger-2">
        <div class="stat-card" onclick="window.location.href='{{ route('admin.reports.index', ['status' => 'diproses']) }}'">
            <i class="fa-solid fa-clock-rotate-left stat-card-bg text-warning"></i>
            <div class="stat-card-header">
                <span class="stat-card-title">Menunggu Approval</span>
                <div class="stat-card-icon icon-warning"><i class="fa-solid fa-clock-rotate-left"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $totalPending }}">0</div>
                <div class="stat-card-trend neutral">
                    <span>Laporan butuh review</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 stagger-3">
        <div class="stat-card" onclick="window.location.href='{{ route('admin.reports.index', ['status' => 'disetujui']) }}'">
            <i class="fa-solid fa-check-circle stat-card-bg text-success"></i>
            <div class="stat-card-header">
                <span class="stat-card-title">Penjualan Disetujui</span>
                <div class="stat-card-icon icon-success"><i class="fa-solid fa-check-circle"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $totalApproved }}">0</div>
                <div class="stat-card-trend {{ $totalRejected > 0 ? 'down' : 'up' }}">
                    <i class="fa-solid fa-{{ $totalRejected > 0 ? 'xmark' : 'check' }}"></i>
                    <span>{{ $totalRejected }} laporan ditolak</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 stagger-4">
        <div class="stat-card" onclick="window.location.href='{{ route('admin.stores.index') }}'">
            <i class="fa-solid fa-store stat-card-bg text-info"></i>
            <div class="stat-card-header">
                <span class="stat-card-title">Toko Aktif</span>
                <div class="stat-card-icon icon-info"><i class="fa-solid fa-store"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $totalStores }}">0</div>
                <div class="stat-card-trend up">
                    <i class="fa-solid fa-users"></i>
                    <span>{{ $totalEmployees }} Karyawan Aktif</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Main Charts & Store Performace --}}
<div class="row g-4 mb-4 stagger-2">
    <div class="col-12 col-xl-8">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Tren Omzet Penjualan</h3>
                <select id="chartPeriod" class="form-select form-select-sm" style="width:140px; font-weight:600;" onchange="loadChart()">
                    <option value="7">7 Hari Terakhir</option>
                    <option value="30" selected>30 Hari Terakhir</option>
                    <option value="90">3 Bulan</option>
                </select>
            </div>
            <div class="card-body">
                <div id="chartSkeleton" class="skeleton skeleton-card w-100" style="height:280px; display:none;"></div>
                <canvas id="omzetChart" height="280"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Performa Toko</h3>
            </div>
            <div class="card-body">
                @forelse($topStores as $i => $ts)
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div style="width:32px;height:32px;border-radius:50%;background:var(--{{ $i==0 ? 'warning' : 'neutral' }}-100);color:var(--{{ $i==0 ? 'warning' : 'neutral' }}-700);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;flex-shrink:0;">
                        @if($i == 0) <i class="fa-solid fa-trophy"></i> @else {{ $i + 1 }} @endif
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span style="font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $ts->store->name ?? 'Unknown' }}</span>
                            <span style="font-size:13px;font-weight:700;">Rp {{ number_format($ts->total_omzet,0,',','.') }}</span>
                        </div>
                        <div style="font-size:12px;color:var(--text-secondary);margin-bottom:6px;">{{ $ts->total_transactions }} transaksi</div>
                        <div class="progress" style="height:6px;background:var(--neutral-100);border-radius:4px;">
                            <div class="progress-bar" style="background:var(--primary);width:{{ $totalOmzet > 0 ? min(100, round($ts->total_omzet / $totalOmzet * 100)) : 0 }}%;border-radius:4px;"></div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state py-4">
                    <div class="empty-state-icon" style="width:48px;height:48px;font-size:20px;margin-bottom:12px;"><i class="fa-solid fa-store-slash"></i></div>
                    <div class="empty-state-title" style="font-size:16px;">Belum ada data</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- Top Products & Recent Activity --}}
<div class="row g-4 stagger-3">
    <div class="col-12 col-xl-5">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Produk Terlaris</h3>
                <a href="{{ route('admin.products.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($topProducts as $tp)
                    <div class="list-group-item d-flex align-items-center gap-3 p-3 border-bottom border-light">
                        <div style="width:56px;height:56px;border-radius:var(--radius-md);background:var(--neutral-100);overflow:hidden;flex-shrink:0;">
                            @if($tp->product && $tp->product->photo)
                                <img src="{{ asset('storage/'.$tp->product->photo) }}" style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-box"></i></div>
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:2px;">{{ $tp->product->name ?? 'Deleted Product' }}</div>
                            <div style="font-size:12px;color:var(--text-secondary);">{{ $tp->product->category->name ?? '-' }}</div>
                            <div class="mt-1">
                                <span class="badge badge-success">{{ $tp->total_qty }} Terjual</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div style="font-size:14px;font-weight:700;">Rp {{ number_format($tp->total_amount,0,',','.') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state py-5">
                        <div class="empty-state-icon"><i class="fa-solid fa-box-open"></i></div>
                        <div class="empty-state-title">Belum ada penjualan</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-7">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Aktivitas Terbaru</h3>
                <a href="{{ route('admin.audit-log') }}" class="btn btn-ghost btn-sm">Lihat Log</a>
            </div>
            <div class="card-body">
                <div style="position:relative; padding-left:24px;">
                    <div style="position:absolute; left:7px; top:0; bottom:0; width:2px; background:var(--neutral-100);"></div>
                    @forelse($recentActivities as $log)
                        @php
                            $color = 'primary'; $icon = 'circle-info';
                            if(in_array($log->action, ['approve_report','login'])) { $color = 'success'; $icon = 'check'; }
                            elseif(in_array($log->action, ['reject_report','logout'])) { $color = 'danger'; $icon = 'xmark'; }
                            elseif(in_array($log->action, ['create_report'])) { $color = 'warning'; $icon = 'file-invoice'; }
                        @endphp
                        <div class="mb-4" style="position:relative;">
                            <div style="position:absolute; left:-24px; top:4px; width:16px; height:16px; border-radius:50%; background:var(--{{$color}}-500); border:3px solid var(--surface); display:flex; align-items:center; justify-content:center; z-index:2; box-shadow:0 0 0 4px var(--{{$color}}-50);">
                                <i class="fa-solid fa-{{$icon}}" style="font-size:6px; color:#fff;"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div style="font-size:14px;font-weight:600;color:var(--text);">
                                    @if($log->user) <span class="text-primary">{{ $log->user->name }}</span> @endif
                                </div>
                                <div style="font-size:12px;color:var(--text-muted); white-space:nowrap;">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                            <div style="font-size:13px;color:var(--text-secondary);">{{ $log->description }}</div>
                        </div>
                    @empty
                        <div class="empty-state py-4">
                            <div class="empty-state-icon" style="width:48px;height:48px;font-size:20px;margin-bottom:12px;"><i class="fa-solid fa-clock"></i></div>
                            <div class="empty-state-title" style="font-size:16px;">Belum ada aktivitas</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
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
            gradient.addColorStop(0, 'rgba(99,102,241,0.25)');
            gradient.addColorStop(1, 'rgba(99,102,241,0)');

            window.omzetChartInstance = new Chart(canvasCtx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Omzet',
                        data: data.values,
                        borderColor: '#6366f1',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#6366f1',
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
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
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
                                callback: (v) => 'Rp ' + (v / 1000000).toFixed(1) + 'M'
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
