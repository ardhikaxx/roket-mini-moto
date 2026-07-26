@extends('layouts.admin')
@section('title', 'Dashboard Kepala Toko - Roket Mini Moto')

@section('content')
@php
    $user = auth()->user();
    
    // Get assigned stores
    $assignedStoreIds = \App\Models\UserStore::where('user_id', $user->id)->pluck('store_id');
    $assignedStores = \App\Models\Store::whereIn('id', $assignedStoreIds)->where('is_active', true)->get();
    
    // Get selected store from request, or default to first assigned store
    $selectedStoreId = request('store_id', $assignedStores->first()?->id);
    $selectedStore = $assignedStores->firstWhere('id', $selectedStoreId);

    if ($selectedStore) {
        $totalOmzet = \App\Models\SalesReport::where('store_id', $selectedStoreId)->where('status','disetujui')->sum('total_amount');
        $todayReports = \App\Models\SalesReport::where('store_id', $selectedStoreId)->whereDate('created_at', today())->count();
        $pendingReports = \App\Models\SalesReport::where('store_id', $selectedStoreId)->where('status','diproses')->count();
        $activeEmployees = \App\Models\UserStore::where('store_id', $selectedStoreId)
            ->whereHas('user', fn($q) => $q->where('role', 'karyawan')->where('is_active', true))
            ->count();
            
        $recentReports = \App\Models\SalesReport::with('user')->where('store_id', $selectedStoreId)->latest()->take(5)->get();
        
        $topProducts = \App\Models\SalesReportItem::whereHas('salesReport', fn($q) => $q->where('store_id', $selectedStoreId)->where('status','disetujui'))
            ->selectRaw('product_id, SUM(quantity) as total_qty, SUM(subtotal) as total_amount')
            ->groupBy('product_id')->with('product.category')
            ->orderByDesc('total_qty')->take(5)->get();

        $topEmployees = \App\Models\SalesReport::where('store_id', $selectedStoreId)->where('status','disetujui')
            ->selectRaw('user_id, SUM(total_amount) as total_omzet, COUNT(*) as total_reports')
            ->groupBy('user_id')->with('user')->orderByDesc('total_omzet')->take(5)->get();
            
        $statusCounts = \App\Models\SalesReport::where('store_id', $selectedStoreId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')->toArray();
            
        $chartData = [
            'disetujui' => $statusCounts['disetujui'] ?? 0,
            'diproses' => $statusCounts['diproses'] ?? 0,
            'ditolak' => $statusCounts['ditolak'] ?? 0,
        ];
    }
@endphp

{{-- Page Header --}}
<div class="page-header stagger-1">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Halo, {{ explode(' ', trim($user->name))[0] }}! <span style="font-size: 24px;">👋</span></h1>
            <p class="page-subtitle">Pantau performa dan operasional toko Anda hari ini.</p>
        </div>
        
        @if($assignedStores->count() > 1)
        <div class="page-actions">
            <form id="storeSelectorForm" action="{{ route('kepalatoko.dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
                <label class="text-sm fw-bold text-muted text-nowrap mb-0"><i class="fa-solid fa-store"></i> Pilih Toko:</label>
                <select name="store_id" class="form-select form-select-sm" style="min-width: 200px; font-weight:600; border-radius:var(--radius-full);" onchange="document.getElementById('storeSelectorForm').submit()">
                    @foreach($assignedStores as $store)
                        <option value="{{ $store->id }}" {{ $selectedStoreId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        @endif
    </div>
</div>

@if(!$selectedStore)
<div class="empty-state card">
    <div class="empty-state-icon text-warning"><i class="fa-solid fa-store-slash"></i></div>
    <h3 class="empty-state-title">Tidak Ada Toko Ditugaskan</h3>
    <p class="empty-state-desc">Anda belum ditugaskan ke toko manapun. Silakan hubungi Administrator sistem.</p>
</div>
@else

{{-- Statistic Cards --}}
<div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-lg-3 stagger-1">
        <div class="stat-card">
            <i class="fa-solid fa-wallet stat-card-bg text-primary"></i>
            <div class="stat-card-header">
                <span class="stat-card-title">Omzet Toko</span>
                <div class="stat-card-icon icon-primary"><i class="fa-solid fa-wallet"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value text-truncate" data-count="{{ $totalOmzet }}">0</div>
                <div class="stat-card-trend up">
                    <span>Semua Penjualan Disetujui</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 stagger-2">
        <div class="stat-card">
            <i class="fa-solid fa-file-invoice stat-card-bg text-info"></i>
            <div class="stat-card-header">
                <span class="stat-card-title">Laporan Hari Ini</span>
                <div class="stat-card-icon icon-info"><i class="fa-solid fa-file-invoice"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $todayReports }}">0</div>
                <div class="stat-card-trend neutral">
                    <span>Laporan masuk hari ini</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 stagger-3">
        <div class="stat-card" onclick="window.location.href='{{ route('admin.reports.index', ['store_id' => $selectedStoreId, 'status' => 'diproses']) }}'">
            <i class="fa-solid fa-clock-rotate-left stat-card-bg text-warning"></i>
            <div class="stat-card-header">
                <span class="stat-card-title">Menunggu Review</span>
                <div class="stat-card-icon icon-warning"><i class="fa-solid fa-clock-rotate-left"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $pendingReports }}">0</div>
                <div class="stat-card-trend {{ $pendingReports > 0 ? 'warning' : 'success' }}">
                    <span>{{ $pendingReports > 0 ? 'Segera review laporan' : 'Semua laporan direview' }}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 stagger-4">
        <div class="stat-card">
            <i class="fa-solid fa-users stat-card-bg text-success"></i>
            <div class="stat-card-header">
                <span class="stat-card-title">Karyawan Aktif</span>
                <div class="stat-card-icon icon-success"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $activeEmployees }}">0</div>
                <div class="stat-card-trend neutral">
                    <span>Di cabang ini</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4 stagger-2">
    {{-- Status Laporan Donut Chart --}}
    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-header border-0 pb-0">
                <h3 class="card-title">Status Laporan Toko</h3>
            </div>
            <div class="card-body d-flex flex-column align-items-center justify-content-center pt-2">
                @if(array_sum($chartData) > 0)
                    <div style="position:relative; width:220px; height:220px;">
                        <canvas id="reportStatusChart"></canvas>
                        <div style="position:absolute; top:0; left:0; right:0; bottom:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none;">
                            <span style="font-size:12px; color:var(--text-secondary); font-weight:600; text-transform:uppercase; letter-spacing:1px;">Total</span>
                            <span style="font-size:28px; font-weight:800; font-family:var(--font-display); color:var(--text); line-height:1;">{{ array_sum($chartData) }}</span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center gap-4 mt-4 w-100">
                        <div class="text-center">
                            <div style="font-size:12px; color:var(--text-secondary); margin-bottom:4px;"><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:var(--success); margin-right:4px;"></span>Disetujui</div>
                            <div style="font-size:15px; font-weight:700;">{{ $chartData['disetujui'] }}</div>
                        </div>
                        <div class="text-center">
                            <div style="font-size:12px; color:var(--text-secondary); margin-bottom:4px;"><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:var(--warning); margin-right:4px;"></span>Diproses</div>
                            <div style="font-size:15px; font-weight:700;">{{ $chartData['diproses'] }}</div>
                        </div>
                        <div class="text-center">
                            <div style="font-size:12px; color:var(--text-secondary); margin-bottom:4px;"><span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:var(--danger); margin-right:4px;"></span>Ditolak</div>
                            <div style="font-size:15px; font-weight:700;">{{ $chartData['ditolak'] }}</div>
                        </div>
                    </div>
                @else
                    <div class="empty-state py-4 w-100 h-100">
                        <div class="empty-state-icon" style="width:48px;height:48px;font-size:20px;margin-bottom:12px;"><i class="fa-solid fa-chart-pie"></i></div>
                        <div class="empty-state-title" style="font-size:16px;">Belum ada data</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Performa Karyawan --}}
    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Top Karyawan</h3>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($topEmployees as $i => $te)
                    <div class="list-group-item d-flex align-items-center gap-3 p-3 border-bottom border-light">
                        <div class="user-avatar" style="width:40px;height:40px;font-size:14px;border:none;">
                            @if($te->user->photo) <img src="{{ asset('storage/'.$te->user->photo) }}">
                            @else {{ strtoupper(substr($te->user->name, 0, 2)) }}
                            @endif
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:14px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $te->user->name }}</div>
                            <div style="font-size:12px;color:var(--text-secondary);">{{ $te->total_reports }} Laporan</div>
                        </div>
                        <div class="text-end">
                            <div style="font-size:14px;font-weight:700;color:var(--primary-700);">Rp {{ number_format($te->total_omzet,0,',','.') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state py-5">
                        <div class="empty-state-icon"><i class="fa-solid fa-users-slash"></i></div>
                        <div class="empty-state-title">Belum ada penjualan</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Activity Feed --}}
    <div class="col-12 col-xl-4">
        <div class="card h-100">
            <div class="card-header">
                <h3 class="card-title">Aktivitas Laporan</h3>
                <a href="{{ route('admin.reports.index', ['store_id' => $selectedStoreId]) }}" class="text-primary text-decoration-none" style="font-size:13px; font-weight:600;">Lihat Semua</a>
            </div>
            <div class="card-body">
                @forelse($recentReports as $report)
                    @php
                        $badgeClass = $report->status == 'disetujui' ? 'success' : ($report->status == 'diproses' ? 'warning' : 'danger');
                    @endphp
                    <div class="d-flex gap-3 mb-3 pb-3 border-bottom border-light">
                        <div class="user-avatar" style="width:36px;height:36px;font-size:12px;border:none;">
                            @if($report->user && $report->user->photo) <img src="{{ asset('storage/'.$report->user->photo) }}">
                            @else {{ strtoupper(substr($report->user->name ?? '?', 0, 2)) }}
                            @endif
                        </div>
                        <div style="flex:1;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span style="font-size:13px;font-weight:600;">{{ $report->user->name ?? 'Unknown' }}</span>
                                <span class="badge badge-{{ $badgeClass }}" style="font-size:10px;padding:2px 6px;">{{ ucfirst($report->status) }}</span>
                            </div>
                            <div style="font-size:12px;color:var(--text-secondary);margin-bottom:4px;">
                                Mengirim laporan <a href="{{ route('admin.reports.show', $report) }}" class="fw-bold text-decoration-none">#{{ $report->report_number }}</a>
                            </div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ $report->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state py-4">
                        <div class="empty-state-icon" style="width:48px;height:48px;font-size:20px;margin-bottom:12px;"><i class="fa-solid fa-file-invoice"></i></div>
                        <div class="empty-state-title" style="font-size:14px;">Belum ada laporan terbaru</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@if($selectedStore && array_sum($chartData) > 0)
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('reportStatusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Disetujui', 'Diproses', 'Ditolak'],
            datasets: [{
                data: [{{ $chartData['disetujui'] }}, {{ $chartData['diproses'] }}, {{ $chartData['ditolak'] }}],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
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
                    displayColors: true,
                    boxPadding: 4
                }
            }
        }
    });
});
</script>
@endpush
@endif
