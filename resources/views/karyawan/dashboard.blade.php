@extends('layouts.admin')
@section('title', 'Dashboard Karyawan - Roket Mini Moto')

@section('content')
@php
    $user = auth()->user();
    
    // Get assigned stores for greeting context
    $assignedStores = \App\Models\UserStore::where('user_id', $user->id)->with('store')->get();
    $storeNames = $assignedStores->map(fn($us) => $us->store->name)->join(', ');

    // Stats
    $todayReports = \App\Models\SalesReport::where('user_id', $user->id)->whereDate('created_at', today())->count();
    $pendingReports = \App\Models\SalesReport::where('user_id', $user->id)->where('status','diproses')->count();
    $approvedReports = \App\Models\SalesReport::where('user_id', $user->id)->where('status','disetujui')->count();
    $rejectedReports = \App\Models\SalesReport::where('user_id', $user->id)->where('status','ditolak')->count();
    
    // Recent Reports
    $recentReports = \App\Models\SalesReport::where('user_id', $user->id)->with('store')->latest()->take(6)->get();
    
    $today = now();
    $hour = $today->hour;
    if ($hour < 10) $greeting = 'Selamat Pagi';
    elseif ($hour < 15) $greeting = 'Selamat Siang';
    elseif ($hour < 18) $greeting = 'Selamat Sore';
    else $greeting = 'Selamat Malam';
@endphp

{{-- Page Header & Primary Action --}}
<div class="row align-items-center mb-5 stagger-1">
    <div class="col-12 col-md-8 mb-4 mb-md-0">
        <h1 class="page-title">{{ $greeting }}, {{ explode(' ', trim($user->name))[0] }}!</h1>
        <p class="page-subtitle">
            Hari ini {{ $today->translatedFormat('l, d F Y') }}. Anda ditugaskan di: <strong class="text-primary">{{ $storeNames ?: 'Belum ada toko' }}</strong>
        </p>
    </div>
    <div class="col-12 col-md-4 text-md-end">
        <a href="{{ route('karyawan.reports.create') }}" class="btn btn-primary btn-lg w-100 w-md-auto" style="border-radius:var(--radius-full); font-weight:700; box-shadow:0 8px 20px rgba(79,70,229,0.3);">
            <i class="fa-solid fa-plus-circle me-2 fs-5"></i> Buat Laporan Penjualan
        </a>
    </div>
</div>

@if($rejectedReports > 0)
<div class="alert alert-danger d-flex align-items-center gap-3 border-0 stagger-2 mb-4" style="background:var(--danger-50); border-radius:var(--radius-lg); padding:20px;">
    <div style="width:48px;height:48px;border-radius:50%;background:var(--danger-100);color:var(--danger-600);display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0;">
        <i class="fa-solid fa-triangle-exclamation"></i>
    </div>
    <div style="flex:1;">
        <h5 class="fw-bold text-danger mb-1">Anda memiliki {{ $rejectedReports }} laporan yang ditolak!</h5>
        <p class="mb-0 text-danger" style="font-size:14px; opacity:0.9;">Silakan perbaiki laporan yang ditolak agar penjualan Anda dapat dicatat.</p>
    </div>
    <div>
        <a href="{{ route('karyawan.reports.index', ['status' => 'ditolak']) }}" class="btn btn-danger">Lihat Laporan</a>
    </div>
</div>
@endif

{{-- Progress Cards --}}
<div class="row g-4 mb-4 stagger-2">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-title">Laporan Hari Ini</span>
                <div class="stat-card-icon icon-primary"><i class="fa-solid fa-calendar-day"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $todayReports }}">0</div>
                <div class="stat-card-trend neutral">
                    <span>Dikirim hari ini</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-title">Sedang Diproses</span>
                <div class="stat-card-icon icon-warning"><i class="fa-solid fa-clock-rotate-left"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $pendingReports }}">0</div>
                <div class="stat-card-trend warning">
                    <span>Menunggu review</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-title">Total Disetujui</span>
                <div class="stat-card-icon icon-success"><i class="fa-solid fa-check-circle"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $approvedReports }}">0</div>
                <div class="stat-card-trend success">
                    <span>Laporan berhasil</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="stat-card-title">Total Ditolak</span>
                <div class="stat-card-icon icon-danger"><i class="fa-solid fa-xmark-circle"></i></div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-value" data-count="{{ $rejectedReports }}">0</div>
                <div class="stat-card-trend danger">
                    <span>Perlu diperbaiki</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Reports Timeline --}}
<div class="card stagger-3">
    <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
        <h3 class="card-title">Histori Laporan Terbaru</h3>
        <a href="{{ route('karyawan.reports.index') }}" class="btn btn-ghost btn-sm">Lihat Semua</a>
    </div>
    <div class="card-body">
        @if($recentReports->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>No Laporan / Waktu</th>
                            <th>Toko</th>
                            <th>Total Penjualan</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReports as $report)
                        <tr>
                            <td>
                                <div class="fw-bold text-primary mb-1">#{{ $report->report_number }}</div>
                                <div style="font-size:12px;color:var(--text-secondary);">
                                    {{ $report->created_at->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td>
                                <div style="font-size:14px;font-weight:600;">{{ $report->store->name ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="font-size:14px;font-weight:700;">Rp {{ number_format($report->total_amount, 0, ',', '.') }}</div>
                                <div style="font-size:12px;color:var(--text-secondary);">{{ $report->items()->sum('quantity') }} item produk</div>
                            </td>
                            <td>
                                @if($report->status == 'disetujui')
                                    <span class="badge badge-success"><i class="fa-solid fa-check me-1"></i> Disetujui</span>
                                @elseif($report->status == 'ditolak')
                                    <span class="badge badge-danger"><i class="fa-solid fa-xmark me-1"></i> Ditolak</span>
                                @else
                                    <span class="badge badge-warning"><i class="fa-solid fa-clock-rotate-left me-1"></i> Diproses</span>
                                @endif
                                
                                @if($report->status == 'ditolak' && $report->rejection_reason)
                                    <div class="mt-2 p-2 bg-danger-subtle text-danger rounded" style="font-size:12px; max-width:200px;">
                                        <strong>Alasan:</strong> {{ $report->rejection_reason }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="#" class="btn btn-secondary btn-icon-sm" title="Lihat Detail"><i class="fa-solid fa-eye"></i></a>
                                    @if($report->status == 'ditolak')
                                        <a href="{{ route('karyawan.reports.edit', $report) }}" class="btn btn-danger btn-sm">Perbaiki</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state py-5">
                <div class="empty-state-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                <h3 class="empty-state-title">Belum ada laporan</h3>
                <p class="empty-state-desc">Anda belum pernah membuat laporan penjualan. Klik tombol "Buat Laporan Penjualan" untuk mulai mencatat.</p>
                <a href="{{ route('karyawan.reports.create') }}" class="btn btn-primary mt-3">Buat Laporan Pertama</a>
            </div>
        @endif
    </div>
</div>
@endsection
