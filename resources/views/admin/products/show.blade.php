@extends('layouts.admin')
@section('title', 'Detail Produk: ' . $product->name)
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.products.index') }}">Produk</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">{{ $product->name }}</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-row align-items-center">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge {{ $product->is_active ? 'badge-success' : 'badge-danger' }} rounded-pill px-2" style="font-weight:600; font-size:11px;">
                    <i class="fa-solid {{ $product->is_active ? 'fa-check-circle' : 'fa-circle-xmark' }} me-1"></i> {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
                @if($product->show_on_landing)
                    <span class="badge badge-info rounded-pill px-2" style="font-weight:600; font-size:11px;"><i class="fa-solid fa-globe me-1"></i> Landing Page</span>
                @endif
            </div>
            <h1 class="page-title">{{ $product->name }}</h1>
            <p class="page-subtitle text-muted" style="font-family:monospace;font-size:13px;">SKU: {{ $product->sku }} &bull; Didaftarkan: {{ $product->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="page-actions d-flex gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-light px-3 py-2" style="font-weight:600;border:1px solid var(--border-light);"><i class="fa-solid fa-arrow-left me-2"></i> Kembali</a>
            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" id="form-del-{{ $product->id }}" class="m-0">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-outline-danger px-3 py-2" style="font-weight:600;" onclick="confirmDelete()"><i class="fa-solid fa-trash-can"></i></button>
            </form>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary px-4 py-2" style="font-weight:600; box-shadow:0 4px 12px rgba(230,57,70,0.25);"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Produk</a>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    {{-- Kolom Kiri: Visual Gambar --}}
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0 h-100" style="border-radius:var(--radius-lg); overflow:hidden;">
            @if($product->photo)
                <div style="width:100%; height:450px; background:var(--neutral-50); position:relative;">
                    <img src="{{ asset('storage/'.$product->photo) }}" style="width:100%; height:100%; object-fit:contain; padding:20px;">
                </div>
            @else
                <div style="width:100%; height:450px; background:var(--neutral-50); display:flex; flex-direction:column; align-items:center; justify-content:center; color:var(--text-muted);">
                    <i class="fa-solid fa-image mb-3" style="font-size:4rem; color:var(--neutral-200);"></i>
                    <h5 class="fw-bold mb-1">Belum Ada Gambar</h5>
                    <p style="font-size:13px;">Produk ini belum memiliki visual.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Kolom Kanan: Detail Informasi --}}
    <div class="col-12 col-lg-7">
        <div class="d-flex flex-column gap-4 h-100">
            
            {{-- Card Harga & Stok Utama --}}
            <div class="card shadow-sm border-0" style="border-radius:var(--radius-lg);">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <p class="text-muted mb-1 fw-bold" style="font-size:12px; text-transform:uppercase; letter-spacing:0.5px;">Harga Jual</p>
                            <h2 class="fw-bold text-primary mb-0" style="font-size:36px;">Rp {{ number_format($product->price,0,',','.') }}</h2>
                            @if($product->cost_price > 0)
                                <div class="text-secondary mt-1" style="font-size:13px;"><i class="fa-solid fa-money-bill-wave me-1"></i> Harga Modal: Rp {{ number_format($product->cost_price,0,',','.') }}</div>
                            @endif
                        </div>
                        <div class="text-end">
                            @if($product->category)
                                <a href="{{ route('admin.categories.show', $product->category->id) }}" class="badge bg-light text-dark border text-decoration-none px-3 py-2" style="font-weight:600; font-size:13px;">
                                    <i class="fa-solid fa-tag text-muted me-2"></i>{{ $product->category->name }}
                                </a>
                            @else
                                <span class="badge bg-light text-muted border px-3 py-2" style="font-weight:600; font-size:13px;">Tanpa Kategori</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-4 rounded-3" style="background:var(--neutral-50); border:1px solid var(--border-light);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold text-dark">Ketersediaan Stok</span>
                            <span class="fw-bold {{ $product->stock <= 5 ? 'text-danger' : 'text-success' }}" style="font-size:18px;">
                                {{ $product->stock }} <span style="font-size:14px; font-weight:normal; color:var(--text-secondary);">{{ $product->unit }}</span>
                            </span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius:4px; background:var(--neutral-200);">
                            <div class="progress-bar {{ $product->stock <= 5 ? 'bg-danger' : 'bg-success' }}" role="progressbar" style="width: {{ min(100, max(5, ($product->stock / max(1, 100)) * 100)) }}%"></div>
                        </div>
                        @if($product->stock <= 5)
                            <div class="text-danger mt-2 fw-semibold" style="font-size:12px;"><i class="fa-solid fa-triangle-exclamation me-1"></i> Stok menipis! Segera lakukan restock.</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Card Deskripsi --}}
            <div class="card shadow-sm border-0 flex-grow-1" style="border-radius:var(--radius-lg);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center" style="font-size:16px;">
                        <i class="fa-solid fa-align-left text-muted me-2"></i> Deskripsi Produk
                    </h5>
                    <div class="text-dark" style="font-size:15px; line-height:1.7; white-space:pre-line;">
                        @if($product->description)
                            {{ $product->description }}
                        @else
                            <span class="text-muted" style="font-style:italic;">Tidak ada deskripsi untuk produk ini.</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@if($product->priceHistories->count() > 0)
{{-- Riwayat Harga --}}
<div class="card shadow-sm border-0 mb-5" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-header bg-white p-4 border-bottom border-light">
        <h5 class="fw-bold mb-0 d-flex align-items-center" style="font-size:16px;">
            <i class="fa-solid fa-clock-rotate-left text-muted me-2"></i> Riwayat Perubahan Harga
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-container">
            <table class="table table-hover align-middle" style="margin:0;">
                <thead style="background: var(--neutral-50);">
                    <tr>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Tanggal & Waktu</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Diubah Oleh</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Harga Lama</th>
                        <th style="padding:16px 24px; border-bottom:1px solid var(--border-light);">Harga Baru</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->priceHistories as $h)
                    <tr>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="text-dark fw-semibold" style="font-size:14px;">{{ $h->created_at->format('d M Y') }}</div>
                            <div class="text-muted" style="font-size:12px;">{{ $h->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:32px;height:32px;border-radius:50%;background:var(--primary-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:bold;">
                                    {{ strtoupper(substr($h->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="fw-semibold text-dark">{{ $h->user->name ?? 'Sistem' }}</span>
                            </div>
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="text-muted text-decoration-line-through">Rp {{ number_format($h->old_price,0,',','.') }}</div>
                        </td>
                        <td style="padding:16px 24px; border-bottom:1px solid var(--neutral-100);">
                            <div class="text-success fw-bold">Rp {{ number_format($h->new_price,0,',','.') }}</div>
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
function confirmDelete() {
    Swal.fire({
        title: 'Hapus / Nonaktifkan Produk?',
        html: `Apakah Anda yakin ingin menonaktifkan <strong>{{ addslashes($product->name) }}</strong>?<br>Tindakan ini akan menyembunyikan produk dari daftar penjualan kasir.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e63946',
        cancelButtonColor: '#9ca3af',
        confirmButtonText: '<i class="fa-solid fa-trash-can me-1"></i> Ya, Lanjutkan',
        cancelButtonText: 'Batal',
        customClass: { popup: 'rounded-4' }
    }).then((r) => { 
        if(r.isConfirmed) document.getElementById('form-del-{{ $product->id }}').submit(); 
    });
}
</script>
@endsection
