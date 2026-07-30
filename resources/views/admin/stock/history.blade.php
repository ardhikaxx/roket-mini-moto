@extends('layouts.admin')
@section('title', 'Riwayat Stok')
@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Dashboard</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <a href="{{ route('admin.stock.index') }}">Manajemen Stok</a><span class="separator"><i class="fa-solid fa-chevron-right"></i></span>
    <span class="current">Riwayat Stok</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Riwayat Stok</h1>
            <p class="page-subtitle">Histori perubahan stok produk</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.stock.create') }}" class="btn btn-primary fw-bold"><i class="fa-solid fa-plus-circle me-2"></i> Tambah Stok</a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius:var(--radius-lg);">
    <div class="card-body p-4 bg-neutral-50">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Produk</label>
                <select name="product_id" class="form-select">
                    <option value="">-- Semua Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Tipe</label>
                <select name="type" class="form-select">
                    <option value="all" {{ request('type', 'all') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stok Masuk</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stok Keluar</option>
                    <option value="transfer_in" {{ request('type') == 'transfer_in' ? 'selected' : '' }}>Transfer Masuk</option>
                    <option value="transfer_out" {{ request('type') == 'transfer_out' ? 'selected' : '' }}>Transfer Keluar</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Penyesuaian</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Dari Tanggal</label>
                <input type="date" name="date_start" class="form-control" value="{{ request('date_start') }}">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label fw-bold mb-1" style="font-size:12px;">Sampai Tanggal</label>
                <input type="date" name="date_end" class="form-control" value="{{ request('date_end') }}">
            </div>
            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary fw-bold flex-fill"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="{{ route('admin.stock.history') }}" class="btn btn-light border fw-bold"><i class="fa-solid fa-rotate-right"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius:var(--radius-lg); overflow:hidden;">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr>
                        <th style="padding:16px 20px;">Waktu</th>
                        <th style="padding:16px 20px;">Produk</th>
                        <th style="padding:16px 20px;">Tipe</th>
                        <th style="padding:16px 20px;">Jumlah</th>
                        <th style="padding:16px 20px;">Stok Awal</th>
                        <th style="padding:16px 20px;">Stok Akhir</th>
                        <th style="padding:16px 20px;">Oleh</th>
                        <th style="padding:16px 20px;">Toko</th>
                        <th style="padding:16px 20px;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td style="padding:16px 20px;"><span style="font-size:13px;color:var(--text-secondary);">{{ $t->created_at->format('d/m/Y H:i') }}</span></td>
                        <td style="padding:16px 20px;">
                            <span class="fw-bold text-dark" style="font-size:14px;">{{ $t->product->name ?? 'Produk Dihapus' }}</span>
                        </td>
                        <td style="padding:16px 20px;">
                            @php
                                $label = ['in' => 'Stok Masuk', 'out' => 'Stok Keluar', 'transfer_in' => 'Transfer Masuk', 'transfer_out' => 'Transfer Keluar', 'adjustment' => 'Penyesuaian'];
                                $color = ['in' => 'success', 'out' => 'danger', 'transfer_in' => 'info', 'transfer_out' => 'warning', 'adjustment' => 'secondary'];
                            @endphp
                            <span class="badge badge-{{ $color[$t->type] ?? 'secondary' }} rounded-pill px-3 py-1" style="font-size:11px;">{{ $label[$t->type] ?? $t->type }}</span>
                        </td>
                        <td style="padding:16px 20px;"><span class="fw-bold">{{ $t->quantity }}</span></td>
                        <td style="padding:16px 20px;">{{ $t->stock_before }}</td>
                        <td style="padding:16px 20px;">{{ $t->stock_after }}</td>
                        <td style="padding:16px 20px;">{{ $t->user->name ?? '-' }}</td>
                        <td style="padding:16px 20px;">{{ $t->store->name ?? '-' }}</td>
                        <td style="padding:16px 20px;">
                            <span style="font-size:12px;color:var(--text-muted);max-width:180px;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $t->notes }}">{{ $t->notes ?? '-' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">Belum ada transaksi stok.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3 d-flex justify-content-center">{{ $transactions->links() }}</div>
@endsection
