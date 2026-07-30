@extends('layouts.admin')
@section('title', 'Admin Dashboard - Roket Mini Moto')

@section('content')
<div class="page-header stagger-1">
    <div class="page-header-row align-items-center">
        <div>
            <h1 class="page-title" style="font-size:28px;"><span id="greetingText">Memuat...</span>, {{ explode(' ', trim(auth()->user()->name))[0] }}! 👋</h1>
            <p class="page-subtitle text-muted" style="font-size:14px;" id="dashboardDate">Memuat data...</p>
        </div>
        <div class="page-actions d-flex gap-2 align-items-center">
            <div class="d-flex align-items-center gap-2 me-2" id="liveIndicator" style="display:none;">
                <span class="live-dot"></span>
                <span class="text-muted" style="font-size:12px;font-weight:500;">Live &bull; <span id="lastUpdate">-</span></span>
            </div>
            <button class="btn btn-sm btn-outline-secondary px-3" style="border-radius:50px;font-weight:600;" onclick="refreshDashboard()">
                <i class="fa-solid fa-rotate me-1"></i> Refresh
            </button>
            <a href="{{ route('admin.products.create') }}" class="btn btn-light px-3 py-2" style="font-weight:600; border:1px solid var(--border-light);">
                <i class="fa-solid fa-box-open text-primary me-1 me-lg-2"></i>
                <span class="d-none d-lg-inline">Tambah Produk</span>
                <span class="d-inline d-lg-none">Produk</span>
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-light px-3 py-2" style="font-weight:600; border:1px solid var(--border-light);">
                <i class="fa-solid fa-user-plus text-primary me-1 me-lg-2"></i>
                <span class="d-none d-lg-inline">Tambah Karyawan</span>
                <span class="d-inline d-lg-none">Karyawan</span>
            </a>
            <a href="{{ route('admin.reports.index') }}" class="btn btn-primary px-3 px-lg-4 py-2" style="font-weight:600;">
                <i class="fa-solid fa-file-invoice me-1 me-lg-2"></i>
                <span class="d-none d-lg-inline">Review Laporan</span>
                <span class="d-inline d-lg-none">Laporan</span>
            </a>
        </div>
    </div>
</div>

<div class="row g-4 mb-5" id="kpiCards"></div>

<div class="row g-4 mb-5">
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
                <div id="chartSkeleton" class="skeleton w-100" style="height:280px; border-radius:12px;"></div>
                <canvas id="omzetChart" height="280" style="display:none;"></canvas>
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
            <div class="card-body p-4" id="topStoresBody"></div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-12 col-xl-6">
        <div class="card shadow-sm border-0" style="border-radius:var(--radius-lg); overflow:hidden;">
            <div class="card-header bg-white p-4 border-bottom border-light d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
                    <i class="fa-solid fa-box text-success me-2"></i> 5 Produk Paling Laris
                </h5>
                <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-light" style="font-weight:600;">Lihat Katalog</a>
            </div>
            <div class="card-body p-0">
                <div style="overflow-x: auto;">
                    <table class="table table-hover align-middle m-0">
                        <tbody id="topProductsBody"></tbody>
                    </table>
                </div>
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
                <div class="timeline" style="position:relative; padding-left:24px;" id="activitiesBody">
                    <div style="position:absolute; left:7px; top:0; bottom:0; width:2px; background:var(--border-light);"></div>
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
.live-dot {
    display: inline-block;
    width: 8px; height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: livePulse 1.5s ease-in-out infinite;
}
@keyframes livePulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.8); }
}
.skeleton {
    background: linear-gradient(90deg, #f1f5f9 25%, #e8ecf1 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s ease-in-out infinite;
    border-radius: 6px;
}
@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
</style>
@endsection

@push('scripts')
<script>
let omzetChartInstance = null;
let refreshInterval = null;
let isLoading = false;

function getGreeting() {
    const h = new Date().getHours();
    if (h < 10) return 'Selamat Pagi';
    if (h < 15) return 'Selamat Siang';
    if (h < 18) return 'Selamat Sore';
    return 'Selamat Malam';
}

function formatRupiah(num) {
    return 'Rp ' + Number(num).toLocaleString('id-ID');
}

function formatCompact(num) {
    if (num >= 1000000000) return (num / 1000000000).toFixed(1) + 'M';
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'Jt';
    if (num >= 1000) return (num / 1000).toFixed(0) + 'Rb';
    return num.toString();
}

function refreshDashboard() {
    if (isLoading) return;
    isLoading = true;

    const days = document.getElementById('chartPeriod')?.value || 30;
    const url = '{{ route("admin.dashboard.data") }}?days=' + days;

    fetch(url)
        .then(r => r.json())
        .then(data => {
            isLoading = false;
            renderDashboard(data);
        })
        .catch(() => { isLoading = false; });
}

function renderDashboard(data) {
    const kpi = data.kpi;
    if (!kpi) return;

    document.getElementById('greetingText').textContent = getGreeting();
    document.getElementById('dashboardDate').textContent = data.date || '';
    document.getElementById('liveIndicator').style.display = 'flex';
    document.getElementById('lastUpdate').textContent = data.timestamp || '-';

    renderKPICards(kpi);
    renderOmzetChart(data.omzetTrend);
    renderTopStores(data.topStores);
    renderTopProducts(data.topProducts);
    renderActivities(data.activities);
}

function renderKPICards(kpi) {
    const cards = [
        { col: 3, gradient: true, onclick: "window.location.href='{{ route('admin.omzet') }}'", icon: 'fa-wallet', label: 'Total Omzet', value: formatRupiah(kpi.totalOmzet), 
          badge: '<span class="badge bg-white text-' + (kpi.omzetChange >= 0 ? 'success' : 'danger') + ' rounded-pill px-2"><i class="fa-solid fa-' + (kpi.omzetChange >= 0 ? 'arrow-trend-up' : 'arrow-trend-down') + ' me-1"></i>' + Math.abs(kpi.omzetChange) + '%</span> <span style="opacity:0.8;">dari bulan lalu</span>', bgIcon: 'fa-wallet' },
        { col: 3, gradient: false, onclick: "window.location.href='{{ route('admin.reports.index', ['status' => 'diproses']) }}'", icon: 'fa-clock-rotate-left', iconBg: 'var(--warning-50)', iconColor: 'var(--warning)', label: 'Menunggu Approval', value: kpi.totalPending,
          badge: '<span style="font-size:13px;"><i class="fa-solid fa-file-invoice text-warning me-1"></i> ' + kpi.totalApproved + ' disetujui, ' + kpi.totalRejected + ' ditolak</span>', bgIcon: null },
        { col: 3, gradient: false, onclick: "window.location.href='{{ route('admin.reports.index') }}'", icon: 'fa-check-circle', iconBg: 'var(--success-50)', iconColor: 'var(--success)', label: 'Transaksi Sukses', value: kpi.totalApproved,
          badge: '<span style="font-size:13px;"><i class="fa-solid fa-chart-simple text-success me-1"></i> Hari ini: <strong>' + kpi.todayTransactions + '</strong> transaksi</span>', bgIcon: null },
        { col: 3, gradient: false, onclick: "window.location.href='{{ route('admin.stores.index') }}'", icon: 'fa-store', iconBg: 'var(--info-50)', iconColor: 'var(--info)', label: 'Toko & Stok', value: kpi.totalStores + ' Toko',
          badge: '<span style="font-size:13px;"><i class="fa-solid fa-boxes me-1"></i> ' + kpi.totalProducts + ' produk, <strong class="text-' + (kpi.lowStock > 0 ? 'danger' : 'success') + '">' + kpi.lowStock + ' stok menipis</strong></span>', bgIcon: null }
    ];

    document.getElementById('kpiCards').innerHTML = cards.map(c => {
        const isGradient = c.gradient;
        return '<div class="col-12 col-md-6 col-xl-' + c.col + '">' +
            '<div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); cursor:pointer; transition:transform 0.2s;' + (isGradient ? 'background:linear-gradient(135deg, var(--primary) 0%, var(--primary-700) 100%);color:white;' : '') + '" onclick="' + c.onclick + '" onmouseover="this.style.transform=\'translateY(-5px)\'" onmouseout="this.style.transform=\'none\'">' +
            '<div class="card-body p-4 position-relative overflow-hidden">' +
            (c.bgIcon ? '<i class="fa-solid ' + c.bgIcon + ' position-absolute" style="font-size:120px; right:-20px; bottom:-20px; opacity:0.1; transform:rotate(-15deg);"></i>' : '') +
            '<div class="d-flex align-items-center justify-content-between mb-3 position-relative">' +
            '<span class="fw-bold" style="font-size:13px; text-transform:uppercase; letter-spacing:1px;' + (isGradient ? 'opacity:0.9;' : 'color:var(--text-secondary);') + '">' + c.label + '</span>' +
            '<div style="width:36px;height:36px;border-radius:10px;background:' + (isGradient ? 'rgba(255,255,255,0.2)' : c.iconBg) + ';color:' + (isGradient ? 'white' : c.iconColor) + ';display:flex;align-items:center;justify-content:center;"><i class="fa-solid ' + c.icon + '"></i></div></div>' +
            '<h3 class="fw-bold mb-2 position-relative" style="font-size:28px;' + (isGradient ? '' : 'color:var(--text);') + '">' + c.value + '</h3>' +
            '<div class="d-flex align-items-center gap-2 position-relative" style="font-size:13px;' + (isGradient ? '' : 'color:var(--text-secondary);') + '">' + c.badge + '</div></div></div></div>';
    }).join('');
}

function renderOmzetChart(trend) {
    const ctx = document.getElementById('omzetChart');
    const skeleton = document.getElementById('chartSkeleton');
    if (!ctx || !trend || !trend.labels) return;

    skeleton.style.display = 'none';
    ctx.style.display = 'block';

    if (omzetChartInstance) omzetChartInstance.destroy();

    const canvasCtx = ctx.getContext('2d');
    const gradient = canvasCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(230,57,70,0.25)');
    gradient.addColorStop(1, 'rgba(230,57,70,0)');

    omzetChartInstance = new Chart(canvasCtx, {
        type: 'line',
        data: {
            labels: trend.labels,
            datasets: [{
                label: 'Omzet Penjualan',
                data: trend.values,
                borderColor: '#e63946',
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
            responsive: true, maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(25, 30, 36, 0.95)',
                    titleColor: '#fff', bodyColor: '#e2e8f0',
                    padding: 12, cornerRadius: 8,
                    titleFont: { family: 'Inter', size: 13, weight: '600' },
                    bodyFont: { family: 'Inter', size: 14, weight: '700' },
                    displayColors: false,
                    callbacks: { label: (context) => 'Rp ' + context.parsed.y.toLocaleString('id-ID') }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 12 }, color: '#64748b', maxTicksLimit: 8 } },
                y: { grid: { color: '#f1f5f9', borderDash: [5, 5] }, ticks: { font: { family: 'Inter', size: 12 }, color: '#64748b', callback: (v) => 'Rp ' + (v >= 1000000 ? (v / 1000000).toFixed(1) + 'Jt' : (v / 1000).toFixed(0) + 'Rb') } }
            }
        }
    });
}

function renderTopStores(stores) {
    const container = document.getElementById('topStoresBody');
    if (!container) return;

    if (!stores || stores.length === 0) {
        container.innerHTML = '<div class="text-center py-5"><div style="width:64px;height:64px;border-radius:50%;background:var(--neutral-100);color:var(--neutral-400);display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 16px;"><i class="fa-solid fa-store-slash"></i></div><p class="text-muted fw-semibold m-0">Belum ada data penjualan tercatat.</p></div>';
        return;
    }

    container.innerHTML = stores.map((s, i) => {
        return '<div class="d-flex align-items-center gap-3 mb-4">' +
            '<div style="width:40px;height:40px;border-radius:10px;background:' + (i === 0 ? 'var(--warning-100)' : 'var(--neutral-100)') + ';color:' + (i === 0 ? 'var(--warning-700)' : 'var(--neutral-700)') + ';display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;flex-shrink:0;">' + (i === 0 ? '<i class="fa-solid fa-crown"></i>' : '#' + (i + 1)) + '</div>' +
            '<div style="flex:1;min-width:0;">' +
            '<div class="d-flex justify-content-between align-items-center mb-1">' +
            '<span class="text-dark fw-bold text-truncate" style="font-size:14px; max-width:150px;">' + s.name + '</span>' +
            '<span class="text-primary fw-bold" style="font-size:14px;">' + formatRupiah(s.omzet) + '</span></div>' +
            '<div class="d-flex justify-content-between align-items-center mb-2">' +
            '<span class="text-muted" style="font-size:11px;">' + s.transactions + ' Laporan</span>' +
            '<span class="text-muted fw-bold" style="font-size:11px;">' + s.percentage + '%</span></div>' +
            '<div class="progress" style="height:6px;background:var(--neutral-100);border-radius:4px;">' +
            '<div class="progress-bar bg-primary" style="width:' + s.percentage + '%;border-radius:4px;"></div></div></div></div>';
    }).join('');
}

function renderTopProducts(products) {
    const container = document.getElementById('topProductsBody');
    if (!container) return;

    if (!products || products.length === 0) {
        container.innerHTML = '<tr><td colspan="3" class="text-center py-5"><div style="width:64px;height:64px;border-radius:50%;background:var(--neutral-100);color:var(--neutral-400);display:flex;align-items:center;justify-content:center;font-size:24px;margin:0 auto 16px;"><i class="fa-solid fa-box-open"></i></div><p class="text-muted fw-semibold m-0">Belum ada penjualan produk tercatat.</p></td></tr>';
        return;
    }

    container.innerHTML = products.map(p => {
        return '<tr><td style="padding:16px; border-bottom:1px solid var(--border-light);">' +
            '<div class="d-flex align-items-center gap-3">' +
            '<div style="width:48px;height:48px;border-radius:10px;background:var(--neutral-100);overflow:hidden;flex-shrink:0;">' +
            (p.photo ? '<img src="' + p.photo + '" style="width:100%;height:100%;object-fit:cover;">' : '<div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-motorcycle"></i></div>') +
            '</div><div><div class="fw-bold text-dark text-truncate mb-1" style="font-size:14px; max-width:200px;">' + p.name + '</div>' +
            '<div class="text-muted" style="font-size:12px;">' + p.category + '</div></div></div></td>' +
            '<td class="text-center" style="padding:16px; border-bottom:1px solid var(--border-light);">' +
            '<span class="badge badge-success rounded-pill px-3 py-1" style="font-weight:600;">' + p.qty + ' Unit</span></td>' +
            '<td class="text-end fw-bold text-dark" style="padding:16px; border-bottom:1px solid var(--border-light); font-size:14px;">' + formatRupiah(p.amount) + '</td></tr>';
    }).join('');
}

function renderActivities(activities) {
    const container = document.getElementById('activitiesBody');
    if (!container) return;

    const existingLine = container.querySelector('.timeline-line');
    if (!activities || activities.length === 0) {
        container.innerHTML = '<div style="position:absolute; left:7px; top:0; bottom:0; width:2px; background:var(--border-light);"></div>' +
            '<div class="text-center py-4"><div style="width:48px;height:48px;border-radius:50%;background:var(--neutral-200);color:var(--neutral-400);display:flex;align-items:center;justify-content:center;font-size:18px;margin:0 auto 12px;"><i class="fa-solid fa-history"></i></div><p class="text-muted fw-semibold m-0" style="font-size:13px;">Sistem belum mencatat aktivitas apa pun.</p></div>';
        return;
    }

    container.innerHTML = '<div style="position:absolute; left:7px; top:0; bottom:0; width:2px; background:var(--border-light);"></div>' +
        activities.map(a => {
            return '<div class="mb-4 position-relative">' +
                '<div class="timeline-dot shadow-sm" style="position:absolute; left:-29px; top:4px; width:12px; height:12px; border-radius:50%; background:var(--' + a.color + '); border:2px solid white; z-index:2;"></div>' +
                '<div class="bg-white p-3 rounded-3 shadow-sm border border-light">' +
                '<div class="d-flex justify-content-between align-items-start mb-1">' +
                '<div class="fw-bold text-dark" style="font-size:13px;">' + a.user + '</div>' +
                '<div class="text-muted font-monospace" style="font-size:11px;">' + a.time + '</div></div>' +
                '<div class="text-secondary" style="font-size:13px; line-height:1.5;">' + a.description + '</div></div></div>';
        }).join('');
}

function loadChart() {
    refreshDashboard();
}

document.addEventListener('DOMContentLoaded', function() {
    refreshDashboard();
    refreshInterval = setInterval(refreshDashboard, 30000);
});
</script>
@endpush
