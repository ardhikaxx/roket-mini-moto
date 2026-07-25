@extends('layouts.admin')
@section('title', $product->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.products.index') }}">Produk</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $product->name }}</span>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div style="height:240px;background:var(--neutral-100);border-radius:var(--radius-lg) var(--radius-lg) 0 0;overflow:hidden;">
                <img src="{{ $product->photo ? asset('storage/'.$product->photo) : asset('assets/images/default.jpg') }}" style="width:100%;height:100%;object-fit:cover;">
            </div>
            <div class="card-body">
                <span class="badge badge-neutral mb-2">{{ $product->category->name ?? '-' }}</span>
                <h4 class="fw-bold mb-1">{{ $product->name }}</h4>
                <div style="font-size:12px;color:var(--text-muted);margin-bottom:12px;">SKU: {{ $product->sku }}</div>
                <h3 style="color:var(--primary);font-weight:700;">Rp {{ number_format($product->price,0,',','.') }}</h3>
                <div class="d-flex gap-2 mt-3 flex-wrap">
                    <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }}">{{ $product->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    <span class="badge {{ $product->show_on_landing ? 'badge-info' : 'badge-neutral' }}">{{ $product->show_on_landing ? 'Tampil di Landing Page' : 'Tidak ditampilkan' }}</span>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-sm w-100"><i class="fa-regular fa-pen-to-square"></i> Edit Produk</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Informasi Produk</h5>
                <div class="row" style="font-size:14px;">
                    <div class="col-6 mb-3"><span style="color:var(--text-secondary);">Kategori</span><br><span class="fw-semibold">{{ $product->category->name ?? '-' }}</span></div>
                    <div class="col-6 mb-3"><span style="color:var(--text-secondary);">SKU</span><br><span class="fw-semibold">{{ $product->sku }}</span></div>
                    <div class="col-6 mb-3"><span style="color:var(--text-secondary);">Harga Jual</span><br><span class="fw-semibold">Rp {{ number_format($product->price,0,',','.') }}</span></div>
                    <div class="col-6 mb-3"><span style="color:var(--text-secondary);">Harga Modal</span><br><span class="fw-semibold">Rp {{ number_format($product->cost_price ?? 0,0,',','.') }}</span></div>
                    <div class="col-6 mb-3"><span style="color:var(--text-secondary);">Stok</span><br><span class="fw-semibold">{{ $product->stock }} {{ $product->unit }}</span></div>
                    <div class="col-6 mb-3"><span style="color:var(--text-secondary);">Satuan</span><br><span class="fw-semibold">{{ $product->unit }}</span></div>
                    <div class="col-12 mb-3"><span style="color:var(--text-secondary);">Deskripsi</span><br><span>{{ $product->description ?? '-' }}</span></div>
                </div>
            </div>
        </div>

        @if($product->priceHistories->count() > 0)
        <div class="card mb-4">
            <div class="card-header"><h5 class="fw-bold mb-0">Histori Perubahan Harga</h5></div>
            <div class="card-body p-0">
                <div class="table-container">
                    <table class="table" style="margin:0;">
                        <thead><tr><th>Tanggal</th><th>Diubah Oleh</th><th>Harga Lama</th><th>Harga Baru</th></tr></thead>
                        <tbody>
                            @foreach($product->priceHistories as $h)
                            <tr>
                                <td style="font-size:13px;color:var(--text-secondary);">{{ $h->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $h->user->name ?? '-' }}</td>
                                <td style="color:var(--danger);">Rp {{ number_format($h->old_price,0,',','.') }}</td>
                                <td style="color:var(--success);font-weight:600;">Rp {{ number_format($h->new_price,0,',','.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
