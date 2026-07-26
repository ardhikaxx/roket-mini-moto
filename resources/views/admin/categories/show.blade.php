@extends('layouts.admin')
@section('title', $category->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.categories.index') }}">Kategori</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $category->name }}</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-tag text-primary me-2"></i>{{ $category->name }}</h1>
            <p class="page-subtitle">{{ $category->products->count() }} produk dalam kategori ini</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
        </div>
    </div>
</div>

@if($category->products->isEmpty())
<div class="card empty-state shadow-sm border-0">
    <div class="card-body text-center p-5">
        <div class="empty-state-icon mb-3" style="font-size: 3rem; color: var(--text-muted);"><i class="fa-solid fa-box-open"></i></div>
        <h4 class="fw-bold">Belum ada Produk</h4>
        <p class="text-secondary">Tidak ada produk yang terdaftar dalam kategori ini.</p>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary mt-2"><i class="fa-solid fa-plus-circle me-1"></i> Tambah Produk</a>
    </div>
</div>
@else
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" style="margin:0;">
                <thead style="background: var(--neutral-50);"><tr><th>Foto</th><th>Nama Produk</th><th>SKU</th><th>Harga</th><th>Stok</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($category->products as $p)
                    <tr class="align-middle">
                        <td>
                            <div style="width:44px;height:44px;border-radius:var(--radius-md);background:var(--neutral-100);overflow:hidden;border:1px solid var(--border-light);">
                                <img src="{{ $p->photo ? asset('storage/'.$p->photo) : asset('assets/images/default.jpg') }}" style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.products.show', $p->id) }}" style="color:var(--text);text-decoration:none;font-weight:600;font-size:14px;" class="text-primary-hover">{{ $p->name }}</a>
                        </td>
                        <td style="font-size:13px;color:var(--text-secondary);font-family:monospace;">{{ $p->sku }}</td>
                        <td class="fw-semibold" style="color:var(--text);">Rp {{ number_format($p->price,0,',','.') }}</td>
                        <td>
                            <span style="font-size:14px; font-weight:600; color:var(--text);">{{ $p->stock }}</span> 
                            <span style="font-size:12px; color:var(--text-secondary);">{{ $p->unit }}</span>
                        </td>
                        <td><span class="badge {{ $p->is_active ? 'badge-success' : 'badge-neutral bg-light text-dark border' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
