@extends('layouts.admin')
@section('title', 'Transfer Stok')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.stock.index') }}">Manajemen Stok</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Transfer Stok</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-arrows-left-right text-primary me-2"></i>Transfer Stok Antar Cabang</h1>
            <p class="page-subtitle">Pindahkan stok produk antar toko cabang</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0" style="border-radius:var(--radius-lg);">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-truck-fast text-primary me-2"></i> Form Transfer</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.stock.transfer.store') }}">
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
                        <label class="form-label fw-bold">Dari Toko</label>
                        <select name="from_store_id" class="form-select" required>
                            <option value="">Pilih Toko Asal</option>
                            @foreach($stores as $s)
                                <option value="{{ $s->id }}" {{ old('from_store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ke Toko</label>
                        <select name="to_store_id" class="form-select" required>
                            <option value="">Pilih Toko Tujuan</option>
                            @foreach($stores as $s)
                                <option value="{{ $s->id }}" {{ old('to_store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Jumlah</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 1) }}" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan (opsional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Alasan transfer...">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary fw-bold w-100 py-2"><i class="fa-solid fa-paper-plane me-2"></i> Proses Transfer</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0" style="border-radius:var(--radius-lg);">
            <div class="card-header bg-white p-4 border-bottom border-light">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Riwayat Transfer</h5>
            </div>
            <div class="card-body p-0">
                <div style="overflow-x: auto;">
                    <table class="table table-hover align-middle m-0">
                        <thead>
                            <tr>
                                <th style="padding:12px 16px;">Waktu</th>
                                <th style="padding:12px 16px;">Produk</th>
                                <th style="padding:12px 16px;">Dari</th>
                                <th style="padding:12px 16px;">Ke</th>
                                <th style="padding:12px 16px;">Jumlah</th>
                                <th style="padding:12px 16px;">Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $t)
                            <tr>
                                <td style="padding:12px 16px;font-size:12px;">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                <td style="padding:12px 16px;" class="fw-bold">{{ $t->product->name ?? '-' }}</td>
                                <td style="padding:12px 16px;">{{ $t->fromStore->name ?? '-' }}</td>
                                <td style="padding:12px 16px;">{{ $t->toStore->name ?? '-' }}</td>
                                <td style="padding:12px 16px;">{{ $t->quantity }}</td>
                                <td style="padding:12px 16px;font-size:12px;">{{ $t->user->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada transfer stok.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="mt-3">{{ $transfers->links() }}</div>
    </div>
</div>
@endsection
