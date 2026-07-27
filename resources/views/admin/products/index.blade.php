@extends('layouts.admin')
@section('title', 'Manajemen Produk')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Produk</span>
@endsection

@section('content')
@php
    $totalProducts = \App\Models\Product::count();
    $activeProducts = \App\Models\Product::where('is_active', true)->count();
    $inactiveProducts = \App\Models\Product::where('is_active', false)->count();
    $lowStockProducts = \App\Models\Product::where('stock', '<=', 5)->count();
@endphp

<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-box-open text-primary me-2"></i>Katalog Produk</h1>
            <p class="page-subtitle">Kelola inventaris, harga, dan ketersediaan produk Anda</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary px-4" style="font-weight:600; box-shadow:0 4px 12px rgba(230,57,70,0.25);"><i class="fa-solid fa-plus-circle me-2"></i> Tambah Produk</a>
        </div>
    </div>
</div>

{{-- Summary Statistics --}}
<div class="row g-4 mb-5 stagger-1">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); transition: transform 0.2s;">
            <div class="card-body p-4 d-flex align-items-center">
                <div style="width:56px;height:56px;border-radius:14px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:16px;"><i class="fa-solid fa-boxes-stacked"></i></div>
                <div>
                    <p class="text-muted mb-1" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Total Produk</p>
                    <h3 class="fw-bold mb-0" style="font-size:24px;color:var(--text);">{{ $totalProducts }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); transition: transform 0.2s;">
            <div class="card-body p-4 d-flex align-items-center">
                <div style="width:56px;height:56px;border-radius:14px;background:var(--success-50);color:var(--success-600);display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:16px;"><i class="fa-solid fa-check-circle"></i></div>
                <div>
                    <p class="text-muted mb-1" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Produk Aktif</p>
                    <h3 class="fw-bold mb-0" style="font-size:24px;color:var(--text);">{{ $activeProducts }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); transition: transform 0.2s;">
            <div class="card-body p-4 d-flex align-items-center">
                <div style="width:56px;height:56px;border-radius:14px;background:var(--neutral-100);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:16px;"><i class="fa-solid fa-eye-slash"></i></div>
                <div>
                    <p class="text-muted mb-1" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Nonaktif</p>
                    <h3 class="fw-bold mb-0" style="font-size:24px;color:var(--text);">{{ $inactiveProducts }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); transition: transform 0.2s;">
            <div class="card-body p-4 d-flex align-items-center">
                <div style="width:56px;height:56px;border-radius:14px;background:var(--warning-50);color:var(--warning-700);display:flex;align-items:center;justify-content:center;font-size:24px;margin-right:16px;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div>
                    <p class="text-muted mb-1" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Stok Menipis</p>
                    <h3 class="fw-bold mb-0" style="font-size:24px;color:var(--text);">{{ $lowStockProducts }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

@if($products->isEmpty())
<div class="card empty-state shadow-sm border-0 mb-5" style="border-radius:var(--radius-lg);">
    <div class="card-body text-center p-5 my-4">
        <div class="empty-state-icon mb-4 mx-auto" style="width:96px;height:96px;border-radius:50%;background:var(--neutral-100);color:var(--text-muted);display:flex;align-items:center;justify-content:center;font-size: 3rem;"><i class="fa-solid fa-boxes-stacked"></i></div>
        <h4 class="fw-bold mb-2">Belum ada Produk Terdaftar</h4>
        <p class="text-secondary mb-4">Gudang Anda masih kosong. Mulai tambahkan produk pertama Anda agar kasir dapat memproses transaksi penjualan.</p>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary px-4 py-2" style="font-weight:600;"><i class="fa-solid fa-plus-circle me-2"></i> Tambah Produk Sekarang</a>
    </div>
</div>
@else
<div class="card shadow-sm border-0 stagger-1 mb-5" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable table-hover align-middle" style="margin:0; min-width:1000px;">
                <thead>
                    <tr>
                        <th style="width:80px;">Produk</th>
                        <th style="min-width:240px;">Info Utama</th>
                        <th style="width:150px;">Kategori</th>
                        <th style="width:140px;">Harga Jual</th>
                        <th style="width:140px;">Stok</th>
                        <th style="width:120px;">Visibilitas</th>
                        <th class="text-end" style="width:90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $p)
                    <tr>
                        <td>
                            <div style="width:56px;height:56px;border-radius:10px;background:var(--neutral-100);overflow:hidden;border:1px solid var(--border-light);box-shadow:var(--shadow-sm);">
                                <img src="{{ $p->photo ? asset('storage/'.$p->photo) : asset('assets/images/no-image.png') }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('assets/images/no-image.png') }}';">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark mb-1" style="font-size:15px; line-height:1.2;">
                                <a href="{{ route('admin.products.show', $p->id) }}" class="text-decoration-none text-dark">{{ $p->name }}</a>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="sku-badge">{{ $p->sku }}</span>
                                <span class="text-muted" style="font-size:11px;">#{{ $p->id }}</span>
                            </div>
                        </td>
                        <td>
                            @if($p->category)
                                <a href="{{ route('admin.categories.show', $p->category->id) }}" class="badge bg-light text-dark border text-decoration-none" style="font-weight:600;"><i class="fa-solid fa-tag me-1 text-muted"></i>{{ $p->category->name }}</a>
                            @else
                                <span class="text-muted" style="font-size:13px;font-style:italic;">Tanpa Kategori</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-primary" style="font-size:15px;">Rp {{ number_format($p->price,0,',','.') }}</div>
                            @if($p->cost_price > 0)
                                <div class="text-muted" style="font-size:11px;" title="Harga Modal">Modal: Rp {{ number_format($p->cost_price,0,',','.') }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <div class="d-flex align-items-center gap-1">
                                    <span class="fw-bold {{ $p->stock <= 5 ? 'text-danger' : 'text-dark' }}" style="font-size:15px;">{{ $p->stock }}</span> 
                                    <span style="font-size:12px; color:var(--text-secondary);">{{ $p->unit }}</span>
                                </div>
                                <div class="progress" style="height: 4px; width: 60px; background:var(--neutral-100); border-radius:2px;">
                                    <div class="progress-bar {{ $p->stock <= 5 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ min(100, max(5, $p->stock)) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-2 align-items-start">
                                @if($p->is_active)
                                    <span class="badge badge-success rounded-pill px-2" style="font-weight:600; font-size:11px;"><i class="fa-solid fa-check-circle me-1"></i> Aktif</span>
                                @else
                                    <span class="badge bg-light text-danger border border-danger rounded-pill px-2" style="font-weight:600; font-size:11px;"><i class="fa-solid fa-circle-xmark me-1"></i> Nonaktif</span>
                                @endif
                                
                                @if($p->show_on_landing)
                                    <span class="badge badge-info rounded-pill px-2" style="font-weight:600; font-size:11px;" title="Tampil di Landing Page"><i class="fa-solid fa-globe me-1"></i> Publik</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-3" style="border:1px solid var(--border-light);background:white;width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;" onclick="this.nextElementSibling.classList.toggle('show')"><i class="fa-solid fa-ellipsis-vertical text-secondary"></i></button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-1" style="min-width: 180px;">
                                    <a href="{{ route('admin.products.show', $p->id) }}" class="dropdown-item py-2 fw-semibold text-secondary" style="font-size:14px;"><i class="fa-regular fa-eye me-2 w-15px text-center"></i> Detail Lengkap</a>
                                    <a href="{{ route('admin.products.edit', $p->id) }}" class="dropdown-item py-2 fw-semibold text-secondary" style="font-size:14px;"><i class="fa-regular fa-pen-to-square me-2 w-15px text-center"></i> Edit Produk</a>
                                    <div class="dropdown-divider my-1"></div>
                                    @if($p->is_active)
                                    <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" id="form-del-{{ $p->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="dropdown-item py-2 fw-semibold text-danger" style="font-size:14px;" onclick="confirmDelete({{ $p->id }}, '{{ addslashes($p->name) }}')"><i class="fa-regular fa-circle-xmark me-2 w-15px text-center"></i> Nonaktifkan</button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.products.activate', $p->id) }}" method="POST" id="form-act-{{ $p->id }}">
                                        @csrf
                                        <button type="button" class="dropdown-item py-2 fw-semibold text-success" style="font-size:14px;" onclick="confirmActivate({{ $p->id }}, '{{ addslashes($p->name) }}')"><i class="fa-regular fa-circle-check me-2 w-15px text-center"></i> Aktifkan</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Nonaktifkan Produk?',
        html: `Anda akan menonaktifkan produk <strong>${name}</strong>.<br>Produk tidak akan bisa dipilih kasir.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-power-off me-1"></i> Ya, Nonaktifkan',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((r) => { 
        if(r.isConfirmed) document.getElementById('form-del-'+id).submit(); 
    });
}

function confirmActivate(id, name) {
    Swal.fire({
        title: 'Aktifkan Produk?',
        html: `Produk <strong>${name}</strong> akan diaktifkan dan bisa kembali dijual oleh kasir.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2b9348',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Ya, Aktifkan',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((r) => { 
        if(r.isConfirmed) document.getElementById('form-act-'+id).submit(); 
    });
}
</script>
@endsection