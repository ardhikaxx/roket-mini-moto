@extends('layouts.admin')
@section('title', 'Tambah Stok')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.stock.index') }}">Manajemen Stok</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Tambah Stok</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Tambah / Sesuaikan Stok</h1>
            <p class="page-subtitle">Tambah stok masuk, keluar, atau penyesuaian manual</p>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius:var(--radius-lg); max-width:700px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.stock.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Produk</label>
                <select name="product_id" class="form-select" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} (Stok: {{ $p->stock }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Tipe</label>
                <select name="type" class="form-select" required>
                    <option value="in" {{ old('type', 'in') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                    <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                    <option value="adjustment" {{ old('type') == 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Jumlah</label>
                <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 1) }}" min="1" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Toko (opsional)</label>
                <select name="store_id" class="form-select">
                    <option value="">-- Tidak spesifik --</option>
                    @foreach($stores as $s)
                        <option value="{{ $s->id }}" {{ old('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold">Catatan</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Alasan penyesuaian stok...">{{ old('notes') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary fw-bold px-4"><i class="fa-solid fa-save me-2"></i> Simpan</button>
                <a href="{{ route('admin.stock.index') }}" class="btn btn-light border fw-bold px-4">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
