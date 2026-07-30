@extends('layouts.admin')
@section('title', 'Manajemen Stok')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Manajemen Stok</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-warehouse text-primary me-2"></i>Manajemen Stok</h1>
            <p class="page-subtitle">Pantau stok produk, stok menipis, dan riwayat perubahan stok</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.stock.history') }}" class="btn btn-light border fw-bold"><i class="fa-solid fa-clock-rotate-left me-2"></i> Riwayat Stok</a>
            <a href="{{ route('admin.stock.transfer') }}" class="btn btn-info text-white fw-bold"><i class="fa-solid fa-arrows-left-right me-2"></i> Transfer Stok</a>
            <a href="{{ route('admin.stock.create') }}" class="btn btn-primary fw-bold"><i class="fa-solid fa-plus-circle me-2"></i> Tambah Stok</a>
        </div>
    </div>
</div>

@if($lowStockProducts->count() > 0)
<div class="alert alert-warning border-0 rounded-3 shadow-sm mb-4" style="border-left:4px solid #f59e0b;">
    <div class="d-flex align-items-center gap-3">
        <div style="width:40px;height:40px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;color:#d97706;font-size:18px;"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div>
            <strong>{{ $lowStockProducts->count() }} Produk dengan Stok Menipis!</strong>
            <p class="mb-0 text-muted" style="font-size:13px;">Segera lakukan pengadaan stok untuk produk-produk berikut.</p>
        </div>
    </div>
</div>
@endif

<div class="card shadow-sm border-0" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-header bg-white p-4 border-bottom border-light d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-box me-2 text-primary"></i> Daftar Stok Produk</h5>
        <span class="badge bg-light text-dark border px-3 py-2">{{ $products->count() }} Produk</span>
    </div>
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <table class="table table-hover align-middle m-0 datatable">
                <thead>
                    <tr>
                        <th style="padding:16px 20px;">Produk</th>
                        <th style="padding:16px 20px;">Kategori</th>
                        <th style="padding:16px 20px;">Stok Saat Ini</th>
                        <th style="padding:16px 20px;">Min. Stok</th>
                        <th style="padding:16px 20px;">Status</th>
                        <th style="padding:16px 20px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                    @php
                        $isLow = $p->min_stock > 0 && $p->stock <= $p->min_stock;
                        $isOut = $p->stock <= 0;
                    @endphp
                    <tr class="{{ $isLow || $isOut ? 'bg-warning-subtle' : '' }}">
                        <td style="padding:16px 20px;">
                            <div class="d-flex align-items-center gap-3">
                                <div style="width:40px;height:40px;border-radius:8px;background:var(--neutral-100);overflow:hidden;flex-shrink:0;">
                                    @if($p->photo)
                                        <img src="{{ asset('storage/'.$p->photo) }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-box"></i></div>
                                    @endif
                                </div>
                                <div>
                                    <div class="fw-bold text-dark" style="font-size:14px;">{{ $p->name }}</div>
                                    <div class="text-muted" style="font-size:11px;">SKU: {{ $p->sku ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:16px 20px;"><span class="badge bg-light text-dark border">{{ $p->category->name ?? '-' }}</span></td>
                        <td style="padding:16px 20px;">
                            <span class="fw-bold {{ $isOut ? 'text-danger' : ($isLow ? 'text-warning' : 'text-success') }}" style="font-size:16px;">{{ $p->stock }}</span>
                            <small class="text-muted"> {{ $p->unit }}</small>
                        </td>
                        <td style="padding:16px 20px;">
                            <form method="POST" action="{{ route('admin.stock.min-stock', $p) }}" class="d-flex align-items-center gap-1" style="max-width:120px;">
                                @csrf
                                <input type="number" name="min_stock" value="{{ $p->min_stock }}" class="form-control form-control-sm" style="width:60px;text-align:center;" min="0">
                                <button type="submit" class="btn btn-sm btn-light border" title="Simpan"><i class="fa-solid fa-check text-success"></i></button>
                            </form>
                        </td>
                        <td style="padding:16px 20px;">
                            @if($isOut)
                                <span class="badge badge-danger rounded-pill px-3 py-1">Stok Habis</span>
                            @elseif($isLow)
                                <span class="badge badge-warning rounded-pill px-3 py-1">Menipis</span>
                            @else
                                <span class="badge badge-success rounded-pill px-3 py-1">Tersedia</span>
                            @endif
                        </td>
                        <td style="padding:16px 20px;">
                            <a href="{{ route('admin.stock.history', ['product_id' => $p->id]) }}" class="btn btn-sm btn-light border" title="Riwayat Stok"><i class="fa-solid fa-clock-rotate-left"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($lowStockProducts->count() > 0)
<div class="card shadow-sm border-0 mt-4" style="border-radius:var(--radius-lg); border-left:4px solid #f59e0b !important;">
    <div class="card-header bg-white p-4 border-bottom border-light">
        <h5 class="fw-bold mb-0 text-warning"><i class="fa-solid fa-bell me-2"></i> Produk Stok Menipis</h5>
    </div>
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <table class="table align-middle m-0">
                <thead>
                    <tr>
                        <th style="padding:12px 20px;font-size:12px;">Produk</th>
                        <th style="padding:12px 20px;font-size:12px;">Stok</th>
                        <th style="padding:12px 20px;font-size:12px;">Min. Stok</th>
                        <th style="padding:12px 20px;font-size:12px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $p)
                    <tr>
                        <td style="padding:12px 20px;" class="fw-bold">{{ $p->name }}</td>
                        <td style="padding:12px 20px;"><span class="fw-bold text-warning">{{ $p->stock }}</span></td>
                        <td style="padding:12px 20px;">{{ $p->min_stock }}</td>
                        <td style="padding:12px 20px;">
                            @if($p->stock <= 0)
                                <span class="badge badge-danger">Habis</span>
                            @else
                                <span class="badge badge-warning">Menipis</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
