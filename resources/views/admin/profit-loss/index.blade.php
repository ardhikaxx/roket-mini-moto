@extends('layouts.admin')
@section('title', 'Laporan Laba Rugi')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Laporan Laba Rugi</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-coins text-primary me-2"></i>Laporan Laba Rugi</h1>
            <p class="page-subtitle">Analisa laba kotor dari penjualan (harga jual - harga modal)</p>
        </div>
        <div class="page-header-actions d-flex gap-2">
            <a href="{{ route('admin.profit-loss.export-excel', request()->query()) }}" class="btn btn-success fw-bold">
                <i class="fa-solid fa-file-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('admin.profit-loss.export-pdf', request()->query()) }}" class="btn btn-danger fw-bold">
                <i class="fa-solid fa-file-pdf me-1"></i> Export PDF
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg);">
    <div class="card-body p-4 bg-neutral-50">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Toko</label>
                <select name="store_id" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ request('store_id', 'all') == 'all' ? 'selected' : '' }}>Semua Toko</option>
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Bulan</label>
                <select name="month" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Bulan</option>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $m)
                        <option value="{{ $i+1 }}" {{ request('month') == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Tahun</label>
                <select name="year" class="form-select" onchange="this.form.submit()">
                    @for($y = now()->year - 2; $y <= now()->year; $y++)
                        <option value="{{ $y }}" {{ (request('year') == $y || (!request('year') && $y == now()->year)) ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12 col-md-2">
                <a href="{{ route('admin.profit-loss') }}" class="btn btn-light border fw-bold w-100"><i class="fa-solid fa-rotate-right me-1"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4">
                <p class="text-muted mb-1 fw-bold" style="font-size:12px;text-transform:uppercase;">Total Omzet</p>
                <h3 class="fw-bold mb-0" style="color:var(--primary);">Rp {{ number_format($totalRevenue,0,',','.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4">
                <p class="text-muted mb-1 fw-bold" style="font-size:12px;text-transform:uppercase;">Total Modal</p>
                <h3 class="fw-bold mb-0" style="color:#e74c3c;">Rp {{ number_format($totalCost,0,',','.') }}</h3>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);background:linear-gradient(135deg,var(--primary),var(--primary-600));">
            <div class="card-body p-4 text-white">
                <p class="mb-1 fw-bold" style="font-size:12px;text-transform:uppercase;opacity:0.9;">Laba Kotor</p>
                <h3 class="fw-bold mb-0">Rp {{ number_format($totalProfit,0,',','.') }}</h3>
                <small style="opacity:0.9;">Margin {{ $totalProfitPercent }}%</small>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-header bg-white p-0 border-bottom border-light">
        <ul class="nav nav-tabs border-0 mx-3" id="profitTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 fw-bold px-4 py-3 active" id="by-transaction-tab" data-bs-toggle="tab" data-bs-target="#by-transaction" type="button" role="tab">
                    <i class="fa-solid fa-receipt me-1"></i> Per Transaksi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link border-0 fw-bold px-4 py-3" id="by-product-tab" data-bs-toggle="tab" data-bs-target="#by-product" type="button" role="tab">
                    <i class="fa-solid fa-cube me-1"></i> Per Produk
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content">
            {{-- Tab 1: Per Transaksi --}}
            <div class="tab-pane fade show active" id="by-transaction" role="tabpanel">
                <div style="overflow-x: auto;">
                    <table class="table table-hover align-middle m-0 datatable">
                        <thead>
                            <tr>
                                <th style="padding:16px 20px;">#</th>
                                <th style="padding:16px 20px;">Tanggal</th>
                                <th style="padding:16px 20px;">Toko</th>
                                <th style="padding:16px 20px;">Kasir</th>
                                <th style="padding:16px 20px;">Omzet (Rp)</th>
                                <th style="padding:16px 20px;">Modal (Rp)</th>
                                <th style="padding:16px 20px;">Laba (Rp)</th>
                                <th style="padding:16px 20px;">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportProfits as $i => $rp)
                            <tr>
                                <td style="padding:16px 20px;">{{ $i + 1 }}</td>
                                <td style="padding:16px 20px;">{{ $rp['report']->transaction_date->format('d/m/Y') }}</td>
                                <td style="padding:16px 20px;">{{ $rp['report']->store->name ?? '-' }}</td>
                                <td style="padding:16px 20px;">{{ $rp['report']->user->name ?? '-' }}</td>
                                <td class="fw-bold" style="padding:16px 20px;">Rp {{ number_format($rp['revenue'],0,',','.') }}</td>
                                <td style="padding:16px 20px;">Rp {{ number_format($rp['cost'],0,',','.') }}</td>
                                <td style="padding:16px 20px;">
                                    <span class="fw-bold" style="color:{{ $rp['profit'] >= 0 ? 'var(--primary)' : '#e74c3c' }};">
                                        Rp {{ number_format($rp['profit'],0,',','.') }}
                                    </span>
                                </td>
                                <td style="padding:16px 20px;">
                                    <span class="badge rounded-pill px-3 py-1" style="background:{{ $rp['percent'] >= 50 ? 'var(--primary)' : ($rp['percent'] >= 25 ? 'var(--primary-200)' : 'var(--primary-50)') }}; color:{{ $rp['percent'] >= 50 ? '#fff' : ($rp['percent'] >= 25 ? 'var(--primary-700)' : 'var(--primary-700)') }};">
                                        {{ $rp['percent'] }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Belum ada data transaksi disetujui untuk ditampilkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab 2: Per Produk --}}
            <div class="tab-pane fade" id="by-product" role="tabpanel">
                <div style="overflow-x: auto;">
                    <table class="table table-hover align-middle m-0 datatable">
                        <thead>
                            <tr>
                                <th style="padding:16px 20px;">#</th>
                                <th style="padding:16px 20px;">Produk</th>
                                <th style="padding:16px 20px;">SKU</th>
                                <th style="padding:16px 20px;">Terjual</th>
                                <th style="padding:16px 20px;">Omzet (Rp)</th>
                                <th style="padding:16px 20px;">Modal (Rp)</th>
                                <th style="padding:16px 20px;">Laba (Rp)</th>
                                <th style="padding:16px 20px;">Margin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productProfits as $i => $pp)
                            @php
                                $profit = $pp['revenue'] - $pp['cost'];
                                $percent = $pp['revenue'] > 0 ? round(($profit / $pp['revenue']) * 100, 1) : 0;
                            @endphp
                            <tr>
                                <td style="padding:16px 20px;">{{ $i + 1 }}</td>
                                <td style="padding:16px 20px;">{{ $pp['product_name'] }}</td>
                                <td style="padding:16px 20px;"><code>{{ $pp['product']->sku ?? '-' }}</code></td>
                                <td style="padding:16px 20px;">{{ $pp['qty'] }} pcs</td>
                                <td class="fw-bold" style="padding:16px 20px;">Rp {{ number_format($pp['revenue'],0,',','.') }}</td>
                                <td style="padding:16px 20px;">Rp {{ number_format($pp['cost'],0,',','.') }}</td>
                                <td style="padding:16px 20px;">
                                    <span class="fw-bold" style="color:{{ $profit >= 0 ? 'var(--primary)' : '#e74c3c' }};">
                                        Rp {{ number_format($profit,0,',','.') }}
                                    </span>
                                </td>
                                <td style="padding:16px 20px;">
                                    <span class="badge rounded-pill px-3 py-1" style="background:{{ $percent >= 50 ? 'var(--primary)' : ($percent >= 25 ? 'var(--primary-200)' : 'var(--primary-50)') }}; color:{{ $percent >= 50 ? '#fff' : ($percent >= 25 ? 'var(--primary-700)' : 'var(--primary-700)') }};">
                                        {{ $percent }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Belum ada data produk untuk ditampilkan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection