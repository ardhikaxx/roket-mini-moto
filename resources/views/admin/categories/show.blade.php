@extends('layouts.admin')
@section('title', $category->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.categories.index') }}">Kategori</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $category->name }}</span>
@endsection
@section('content')
<div class="page-header"><div class="page-header-row"><div><h1 class="page-title">{{ $category->name }}</h1><p class="page-subtitle">{{ $category->products->count() }} produk dalam kategori ini</p></div></div></div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table datatable" style="margin:0;">
                <thead><tr><th>Foto</th><th>Nama Produk</th><th>SKU</th><th>Harga</th><th>Stok</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($category->products as $p)
                    <tr>
                        <td><div style="width:40px;height:40px;border-radius:var(--radius-sm);background:var(--neutral-100);overflow:hidden;"><img src="{{ $p->photo ? asset('storage/'.$p->photo) : asset('assets/images/default.jpg') }}" style="width:100%;height:100%;object-fit:cover;"></div></td>
                        <td><a href="{{ route('admin.products.show', $p->id) }}" style="color:var(--text);text-decoration:none;font-weight:600;">{{ $p->name }}</a></td>
                        <td style="font-size:13px;color:var(--text-secondary);">{{ $p->sku }}</td>
                        <td class="fw-semibold">Rp {{ number_format($p->price,0,',','.') }}</td>
                        <td>{{ $p->stock }} {{ $p->unit }}</td>
                        <td><span class="badge {{ $p->is_active ? 'badge-success' : 'badge-danger' }}">{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="6"><div class="empty-state"><div class="empty-state-title">Belum ada produk</div></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
