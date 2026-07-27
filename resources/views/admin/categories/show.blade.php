@extends('layouts.admin')
@section('title', 'Detail Kategori: ' . $category->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.categories.index') }}">Kategori</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $category->name }}</span>
@endsection
@section('content')

@php
    $totalProducts = $category->products->count();
    $activeProducts = $category->products->where('is_active', true)->count();
    $inactiveProducts = $totalProducts - $activeProducts;
@endphp

<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-tag text-primary me-2"></i>{{ $category->name }}</h1>
            <p class="page-subtitle">Detail informasi kategori dan daftar produk di dalamnya</p>
        </div>
        <div class="page-actions gap-2 d-flex">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-light" style="font-weight:600;border:1px solid var(--border-light);"><i class="fa-solid fa-arrow-left me-2"></i> Kembali</a>
            <a href="{{ route('admin.products.create') }}?category_id={{ $category->id }}" class="btn btn-primary" style="font-weight:600;box-shadow:0 4px 12px rgba(230,57,70,0.25);"><i class="fa-solid fa-plus-circle me-2"></i> Tambah Produk</a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Card Informasi Kategori --}}
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom border-light">
                    <div style="width:48px;height:48px;border-radius:12px;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:20px;"><i class="fa-solid fa-circle-info"></i></div>
                    <div class="ms-3">
                        <h5 class="fw-bold mb-0" style="font-size:16px;">Informasi Kategori</h5>
                        <div class="text-muted" style="font-size:13px;">Data identitas kategori</div>
                    </div>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    <div>
                        <div class="text-muted mb-1" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Nama Kategori</div>
                        <div class="fw-bold text-dark" style="font-size:15px;">{{ $category->name }}</div>
                    </div>
                    <div>
                        <div class="text-muted mb-1" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Slug / URL URL</div>
                        <div class="text-dark" style="font-size:14px;font-family:monospace;background:var(--neutral-50);padding:4px 8px;border-radius:4px;display:inline-block;border:1px solid var(--border-light);">{{ $category->slug }}</div>
                    </div>
                    <div>
                        <div class="text-muted mb-1" style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Tanggal Dibuat</div>
                        <div class="text-dark" style="font-size:14px;"><i class="fa-regular fa-calendar me-2 text-muted"></i>{{ $category->created_at ? $category->created_at->format('d M Y, H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Card Statistik --}}
    <div class="col-12 col-lg-8">
        <div class="row g-4 h-100">
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
                    <div class="card-body d-flex flex-column justify-content-center p-4 text-center">
                        <div class="mx-auto" style="width:56px;height:56px;border-radius:14px;background:var(--info-50);color:var(--info-700);display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:16px;"><i class="fa-solid fa-box-open"></i></div>
                        <h3 class="fw-bold mb-1" style="font-size:32px;color:var(--text);">{{ $totalProducts }}</h3>
                        <p class="text-muted mb-0" style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Total Produk</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
                    <div class="card-body d-flex flex-column justify-content-center p-4 text-center">
                        <div class="mx-auto" style="width:56px;height:56px;border-radius:14px;background:var(--success-50);color:var(--success-600);display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:16px;"><i class="fa-solid fa-check-circle"></i></div>
                        <h3 class="fw-bold mb-1" style="font-size:32px;color:var(--text);">{{ $activeProducts }}</h3>
                        <p class="text-muted mb-0" style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Produk Aktif</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg);">
                    <div class="card-body d-flex flex-column justify-content-center p-4 text-center">
                        <div class="mx-auto" style="width:56px;height:56px;border-radius:14px;background:var(--neutral-100);color:var(--text-secondary);display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:16px;"><i class="fa-solid fa-eye-slash"></i></div>
                        <h3 class="fw-bold mb-1" style="font-size:32px;color:var(--text);">{{ $inactiveProducts }}</h3>
                        <p class="text-muted mb-0" style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Tidak Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<h4 class="fw-bold mb-3 mt-5" style="font-size:18px;"><i class="fa-solid fa-list me-2 text-primary"></i> Daftar Produk</h4>

@if($category->products->isEmpty())
<div class="card empty-state shadow-sm border-0 mb-5" style="border-radius:var(--radius-lg);">
    <div class="card-body text-center p-5 my-4">
        <div class="empty-state-icon mb-3 mx-auto" style="width:80px;height:80px;border-radius:50%;background:var(--neutral-100);color:var(--text-muted);display:flex;align-items:center;justify-content:center;font-size: 2.5rem;"><i class="fa-solid fa-box-open"></i></div>
        <h4 class="fw-bold mb-2">Belum ada Produk</h4>
        <p class="text-secondary mb-4">Kategori <strong>{{ $category->name }}</strong> masih kosong. Tambahkan produk ke dalam kategori ini.</p>
        <a href="{{ route('admin.products.create') }}?category_id={{ $category->id }}" class="btn btn-primary px-4 py-2" style="font-weight:600;"><i class="fa-solid fa-plus-circle me-2"></i> Tambah Produk</a>
    </div>
</div>
@else
<div class="card shadow-sm border-0 mb-5" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable table-hover" style="margin:0; border-collapse: separate; border-spacing: 0;">
                <thead style="background: var(--neutral-50);">
                    <tr>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light); width:80px;">Foto</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Nama Produk & SKU</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Harga</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Stok</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Status</th>
                        <th class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category->products as $p)
                    <tr class="align-middle" style="transition: all 0.2s;">
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div style="width:48px;height:48px;border-radius:10px;background:var(--neutral-100);overflow:hidden;border:1px solid var(--border-light);box-shadow:var(--shadow-sm);">
                                <img src="{{ $p->photo ? asset('storage/'.$p->photo) : asset('assets/images/no-image.png') }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('assets/images/no-image.png') }}';">
                            </div>
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="fw-bold text-dark" style="font-size:15px;"><a href="{{ route('admin.products.show', $p->id) }}" class="text-decoration-none text-dark">{{ $p->name }}</a></div>
                            <div style="color:var(--text-secondary);font-size:12px;font-family:monospace;margin-top:2px;">{{ $p->sku }}</div>
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="fw-bold" style="color:var(--text);">Rp {{ number_format($p->price,0,',','.') }}</div>
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="d-flex align-items-center gap-1">
                                <span class="fw-bold" style="font-size:15px; color:var(--text);">{{ $p->stock }}</span> 
                                <span style="font-size:12px; color:var(--text-secondary);">{{ $p->unit }}</span>
                            </div>
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            @if($p->is_active)
                                <span class="badge badge-success px-3 py-2 rounded-pill" style="font-weight:600; font-size:12px;"><i class="fa-solid fa-check-circle me-1"></i> Aktif</span>
                            @else
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill" style="font-weight:600; font-size:12px;"><i class="fa-solid fa-eye-slash me-1"></i> Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end" style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.products.show', $p->id) }}" class="btn btn-light btn-sm text-secondary" title="Detail Produk" style="border:1px solid var(--border-light); font-weight:600; background:white;"><i class="fa-solid fa-eye me-1"></i> Detail</a>
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
@endsection
